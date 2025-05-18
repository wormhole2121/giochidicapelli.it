<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentReminder;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('it');

        $selectedDate = $request->input('date');
        $alwaysSelectableDates = [
            '2024-12-23',
            '2024-12-29',
            '2024-12-30'
        ];

        if (Auth::check() && Auth::user()->is_admin) {
            $bookings = Booking::where('date', $selectedDate)->orderBy('start_time', 'asc')->get();
        } else {
            $bookings = Booking::where('user_id', Auth::id())->where('date', $selectedDate)->get();
        }

        $bookedHours = Booking::where('date', $selectedDate)
            ->pluck('start_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            });

        $bookedDates = Booking::where('date', '>=', now())
            ->where('end_time', '>=', now())
            ->pluck('date')
            ->unique();

        $fullyBookedDates = collect();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths(6)->endOfMonth();
        $availableDates = [];

        while ($startDate <= $endDate) {
            $date = $startDate->format('Y-m-d');
            $dayOfWeek = $startDate->dayOfWeek;

            $timeslots = [];

            if (in_array($date, $alwaysSelectableDates)) {
                $morning = range(8.5 * 60, 12.25 * 60 - 25, 25); // 08:30 - 12:00
                $afternoon = range(14 * 60, 19.4 * 60, 25);      // 14:00 - 19:15
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($dayOfWeek == 4) { // Giovedì
                $timeslots = range(14 * 60, 21.25 * 60 - 25, 25);
            } elseif (in_array($dayOfWeek, [2, 3])) { // Martedì, Mercoledì
                $morning = range(8.5 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 19.4 * 60, 25);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($dayOfWeek == 5) { // Venerdì
                $morning = range(8 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 19.4 * 60, 25);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($dayOfWeek == 6) { // Sabato
                $morning = range(8 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 18.6 * 60, 25); // Fino alle 18:35
                $timeslots = array_merge($morning, $afternoon);
            }

            $bookedSlotsCount = Booking::where('date', $date)->count();

            if ($bookedSlotsCount >= count($timeslots)) {
                $fullyBookedDates->push($date);
            }

            if (!$bookedDates->contains($date)) {
                $availableDates[] = $date;
            }

            $startDate->addDay();
        }

        $availableTimes = [];
        if ($selectedDate) {
            $formattedDate = Carbon::parse($selectedDate)->format('Y-m-d');
            $selectedDayOfWeek = Carbon::parse($selectedDate)->dayOfWeek;

            if (in_array($formattedDate, $alwaysSelectableDates)) {
                $morning = range(8.5 * 60, 12.25 * 60 - 25, 25); // 08:30 - 12:00
                $afternoon = range(14 * 60, 19.4 * 60, 25);      // 14:00 - 19:15
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($selectedDayOfWeek == 4) {
                $timeslots = range(14 * 60, 21.25 * 60 - 25, 25);
            } elseif (in_array($selectedDayOfWeek, [2, 3])) {
                $morning = range(8.5 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 19.4 * 60, 25);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($selectedDayOfWeek == 5) {
                $morning = range(8 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 19.4 * 60, 25);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($selectedDayOfWeek == 6) {
                $morning = range(8 * 60, 12.25 * 60 - 25, 25);
                $afternoon = range(14 * 60, 18.6 * 60, 25); // Fino alle 18:35
                $timeslots = array_merge($morning, $afternoon);
            }

            $availableTimes = collect($timeslots)->map(function ($minutes) use ($bookedHours) {
                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                return sprintf('%02d:%02d', $hours, $mins);
            })->reject(function ($time) use ($bookedHours) {
                return in_array($time, $bookedHours->toArray());
            })->values()->toArray();
        }

        $userBookings = [];
        if (Auth::check()) {
            $userBookings = Booking::where('user_id', Auth::id())
                ->where('date', $selectedDate)
                ->get();
        }

        $isDateBooked = in_array($selectedDate, $bookedDates->toArray());
        $isFullyBooked = in_array($selectedDate, $fullyBookedDates->toArray());

        return view('calendario', compact('selectedDate', 'availableDates', 'bookings', 'isDateBooked', 'userBookings', 'availableTimes', 'fullyBookedDates', 'isFullyBooked'));
    }

    public function prenota(Request $request)
    {
        // Validazione dei dati inviati dal modulo
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'phone' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'haircut_types' => 'required|array',
            'haircut_types.*' => 'in:Taglio,Taglio con modellatura barba,Taglio Razor fade(Sfumatura),Taglio Children,Modellatura barba',
        ]);

        // Definisci le alwaysSelectableDates
        $alwaysSelectableDates = [
            '2024-12-23',
            '2024-12-29',
            '2024-12-30'
        ];

        // Per gli amministratori, salta il controllo della prenotazione singola per giorno
        if (!Auth::user()->is_admin) {
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('date', $validatedData['date'])
                ->first();

            if ($existingBooking) {
                return redirect()->route('calendario')->with('error', 'Hai già una prenotazione per questa data.');
            }
        }

        // Verifica sovrapposizioni con altre prenotazioni
        $startTime = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validatedData['start_time']);
        $endTime = $startTime->copy()->addMinutes(20);

        $overlappingBooking = Booking::where('date', $validatedData['date'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })->first();

        if ($overlappingBooking) {
            return redirect()->route('calendario')->with('error', 'L\'orario selezionato è già prenotato.');
        }

        // Calcola l'orario di inizio e fine della nuova prenotazione
        $dayOfWeek = $startTime->dayOfWeek;
        $validTimeRanges = [
            'morning' => ['08:30', '12:00'],
            'afternoon' => ['14:00', '19:15'],
            'thursday' => ['14:00', '21:00']
        ];

        $isThursday = $dayOfWeek == 4;
        $formattedDate = $startTime->format('Y-m-d');

        if (in_array($formattedDate, $alwaysSelectableDates)) {
            // Validazione degli orari per le alwaysSelectableDates (come mercoledì)
            $validStartMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['morning'][0]);
            $validEndMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['morning'][1]);
            $validStartAfternoon = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['afternoon'][0]);
            $validEndAfternoon = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['afternoon'][1]);

            if (
                !$startTime->between($validStartMorning, $validEndMorning) &&
                !$startTime->between($validStartAfternoon, $validEndAfternoon)
            ) {
                return redirect()->route('calendario')->with('error', 'Gli orari di prenotazione validi sono dalle 08:30 alle 12:00 e dalle 14:00 alle 19:15.');
            }
        } elseif ($isThursday) {
            // Validazione per il giovedì
            $validStart = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['thursday'][0]);
            $validEnd = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['thursday'][1]);
            if (!$startTime->between($validStart, $validEnd)) {
                return redirect()->route('calendario')->with('error', 'Gli orari di prenotazione validi il giovedì sono dalle 14:00 alle 21:00.');
            }
        } elseif ($dayOfWeek == 5 || $dayOfWeek == 6) { // Venerdì e Sabato
            $validStartMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' 08:00');
            $validEndMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' 12:00');
            $validStartAfternoon = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' 14:00');
            $validEndAfternoon = ($dayOfWeek == 6) 
                ? Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' 18:35') // Sabato termina alle 18:35
                : Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' 19:15'); // Venerdì termina alle 19:15

            if (
                !$startTime->between($validStartMorning, $validEndMorning) &&
                !$startTime->between($validStartAfternoon, $validEndAfternoon)
            ) {
                return redirect()->route('calendario')->with('error', 'Gli orari di prenotazione validi sono dalle 08:00 alle 12:00 e dalle 14:00 alle ' . $validEndAfternoon->format('H:i') . ' per venerdì e sabato.');
            }
        } else {
            // Validazione per gli altri giorni
            $validStartMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['morning'][0]);
            $validEndMorning = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['morning'][1]);
            $validStartAfternoon = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['afternoon'][0]);
            $validEndAfternoon = Carbon::createFromFormat('Y-m-d H:i', $formattedDate . ' ' . $validTimeRanges['afternoon'][1]);

            if (
                !$startTime->between($validStartMorning, $validEndMorning) &&
                !$startTime->between($validStartAfternoon, $validEndAfternoon)
            ) {
                return redirect()->route('calendario')->with('error', 'Gli orari di prenotazione validi sono dalle 08:30 alle 12:00 e dalle 14:00 alle 19:15.');
            }
        }

        // Crea la prenotazione
        $booking = new Booking([
            'user_id' => Auth::id(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $validatedData['date'],
            'phone' => $validatedData['phone'],
            'name' => $validatedData['name'],
            'is_visible' => true,
        ]);

        $haircutTypes = $request->input('haircut_types');
        $booking->haircut_types = json_encode($haircutTypes);

        if ($booking->save()) {
            $reminderTime = $startTime->copy()->subHours(4);
            SendAppointmentReminder::dispatch($booking->id)->delay($reminderTime);
            Mail::to(Auth::user()->email)->queue(new BookingConfirmationMail($booking->id));
            return redirect()->route('calendario')->with('success', 'Prenotazione effettuata con successo!');
        }


        return redirect()->route('calendario')->with('error', 'Errore durante il salvataggio della prenotazione.');
    }

    public function leMiePrenotazioni()
    {
        Carbon::setLocale('it');
        $userBookings = Booking::where('user_id', Auth::id())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('le-mie-prenotazioni', compact('userBookings'));
    }

    public function elimina($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return redirect()->route('le-mie-prenotazioni')->with('error', 'Appuntamento non trovato.');
        }

        if (Auth::check() && $booking->user_id == Auth::id()) {
            $booking->delete();
            return redirect()->route('le-mie-prenotazioni')->with('delete_success', 'Appuntamento eliminato con successo.');
        }

        if (Auth::check() && (Auth::user()->is_admin || $booking->user_id == Auth::id())) {
            $booking->delete();
            return redirect()->route('le-mie-prenotazioni')->with('success', 'Appuntamento eliminato con successo.');
        }

        return redirect()->route('le-mie-prenotazioni')->with('error', 'Non hai l\'autorizzazione per eliminare questo appuntamento.');
    }
}
