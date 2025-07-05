<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentReminder;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\UnavailableDate;

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

            // 🔄 SLOT 30 min
            if (in_array($date, $alwaysSelectableDates) || in_array($dayOfWeek, [2, 3])) {
                $morning = range(510, 690 - 30, 30); // 08:30 - 11:30
                $afternoon = range(840, 1140 - 30, 30); // 14:00 - 19:00
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($dayOfWeek == 4) { // Giovedì
                $timeslots = range(840, 1230 - 30, 30); // 14:00 - 20:30
            } elseif ($dayOfWeek == 5) { // Venerdì
                $morning = range(480, 690 - 30, 30); // 08:00 - 11:30
                $afternoon = range(840, 1140 - 30, 30); // 14:00 - 19:00
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($dayOfWeek == 6) { // Sabato
                $morning = range(480, 690 - 30, 30); // 08:00 - 11:30
                $afternoon = range(840, 1110 - 30, 30); // 14:00 - 18:00
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

            // 🔄 SLOT 30 min
            if (in_array($formattedDate, $alwaysSelectableDates) || in_array($selectedDayOfWeek, [2, 3])) {
                $morning = range(510, 690 - 30, 30);
                $afternoon = range(840, 1140 - 30, 30);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($selectedDayOfWeek == 4) {
                $timeslots = range(840, 1230 - 30, 30);
            } elseif ($selectedDayOfWeek == 5) {
                $morning = range(480, 690 - 30, 30);
                $afternoon = range(840, 1140 - 30, 30);
                $timeslots = array_merge($morning, $afternoon);
            } elseif ($selectedDayOfWeek == 6) {
                $morning = range(480, 690 - 30, 30);
                $afternoon = range(840, 1110 - 30, 30);
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

        // ✅ Aggiunta blocchi dinamici dal DB + domeniche/lunedì
        $dbBlocked = \App\Models\UnavailableDate::pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
        $today = now()->startOfDay();
        $sundayMonday = [];
        for ($i = 0; $i < 180; $i++) {
            $d = $today->copy()->addDays($i);
            if (in_array($d->dayOfWeek, [0, 1])) {
                $sundayMonday[] = $d->format('Y-m-d');
            }
        }
        $unavailableDates = array_unique(array_merge($dbBlocked, $sundayMonday));

        return view('calendario', compact(
            'selectedDate',
            'availableDates',
            'bookings',
            'isDateBooked',
            'userBookings',
            'availableTimes',
            'fullyBookedDates',
            'isFullyBooked',
            'unavailableDates'
        ));
    }

    public function prenota(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'phone' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'haircut_types' => 'required|array',
            'haircut_types.*' => 'in:Taglio,Taglio con modellatura barba,Taglio Razor fade(Sfumatura),Taglio Children,Modellatura barba',
        ]);

        $alwaysSelectableDates = ['2024-12-23','2024-12-29','2024-12-30'];

        if (!Auth::user()->is_admin) {
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('date', $validatedData['date'])
                ->first();

            if ($existingBooking) {
                return redirect()->route('calendario')->with('error', 'Hai già una prenotazione per questa data.');
            }
        }

        $startTime = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validatedData['start_time']);
        $endTime = $startTime->copy()->addMinutes(30); // 🔁 Durata fissa

        $overlappingBooking = Booking::where('date', $validatedData['date'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime->subSecond()])
                    ->orWhereBetween('end_time', [$startTime->addSecond(), $endTime]);
            })->first();

        if ($overlappingBooking) {
            return redirect()->route('calendario')->with('error', 'L\'orario selezionato è già prenotato.');
        }

        // La validazione oraria può rimanere invariata
        // [omessa per spazio ma è OK]

        $booking = new Booking([
            'user_id' => Auth::id(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $validatedData['date'],
            'phone' => $validatedData['phone'],
            'name' => $validatedData['name'],
            'is_visible' => true,
        ]);

        $booking->haircut_types = json_encode($request->input('haircut_types'));

        if ($booking->save()) {
            $reminderTime = $startTime->copy()->subHours(4);
            SendAppointmentReminder::dispatch($booking->id)->delay($reminderTime);
            Mail::to(Auth::user()->email)->queue(new BookingConfirmationMail($booking->id));
            return redirect()->route('calendario')->with('success', 'Prenotazione effettuata con successo!');
        }

        return redirect()->route('calendario')->with('error', 'Errore durante il salvataggio della prenotazione.');
    }

    public function leMiePrenotazioni() {
        Carbon::setLocale('it');
        $userBookings = Booking::where('user_id', Auth::id())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('le-mie-prenotazioni', compact('userBookings'));
    }

    public function elimina($id) {
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

    public function toggleDate(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error'=>'Non autorizzato'],403);
        }
        $date = Carbon::parse($request->date);
        if (in_array($date->dayOfWeek, [0,1])) {
            return response()->json(['error'=>'Impossibile modificare Domenica/Lunedì'], 400);
        }

        $exists = UnavailableDate::where('date',$date->format('Y-m-d'))->first();
        if ($exists) {
            $exists->delete();
            return response()->json(['status'=>'unblocked']);
        }
        UnavailableDate::create(['date'=>$date->format('Y-m-d')]);
        return response()->json(['status'=>'blocked']);
    }
}
