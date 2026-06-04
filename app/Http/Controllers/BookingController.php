<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentReminder;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\UnavailableDate;
use App\Models\AvailableDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private function extendedWorkSlots(): array
    {
        $morning = range(510, 690, 30);
        $afternoon = range(840, 1230, 30);

        return array_merge($morning, $afternoon);
    }

    private function normalTuesdayWednesdaySlots(): array
    {
        $morning = range(510, 690, 30);
        $afternoon = range(840, 1140, 30);

        return array_merge($morning, $afternoon);
    }

    private function thursdayWorkSlots(): array
    {
        return range(840, 1230, 30);
    }

    private function fridayWorkSlots(): array
    {
        $morning = range(480, 690, 30);
        $afternoon = range(840, 1140, 30);

        return array_merge($morning, $afternoon);
    }

    private function saturdayWorkSlots(): array
    {
        $morning = range(480, 690, 30);
        $afternoon = range(840, 1110, 30);

        return array_merge($morning, $afternoon);
    }

    private function getTimeslotsForDate(string $date): array
    {
        $carbonDate = Carbon::parse($date);
        $formattedDate = $carbonDate->format('Y-m-d');
        $dayOfWeek = $carbonDate->dayOfWeek;

        if (UnavailableDate::where('date', $formattedDate)->exists()) {
            return [];
        }

        $specialDate = AvailableDate::where('date', $formattedDate)->first();

        if ($specialDate) {
            if ($specialDate->schedule_type === 'normal') {
                return $this->extendedWorkSlots();
            }

            if ($specialDate->schedule_type === 'thursday') {
                return $this->thursdayWorkSlots();
            }
        }

        if (in_array($dayOfWeek, [0, 1])) {
            return [];
        }

        if (in_array($dayOfWeek, [2, 3])) {
            return $this->normalTuesdayWednesdaySlots();
        }

        if ($dayOfWeek == 4) {
            return $this->thursdayWorkSlots();
        }

        if ($dayOfWeek == 5) {
            return $this->fridayWorkSlots();
        }

        if ($dayOfWeek == 6) {
            return $this->saturdayWorkSlots();
        }

        return [];
    }

    public function index(Request $request)
    {
        Carbon::setLocale('it');

        $selectedDate = $request->input('date');

        if (Auth::check() && Auth::user()->is_admin) {
            $bookings = Booking::where('date', $selectedDate)
                ->orderBy('start_time', 'asc')
                ->get();
        } else {
            $bookings = Booking::where('user_id', Auth::id())
                ->where('date', $selectedDate)
                ->get();
        }

        $bookedHours = Booking::where('date', $selectedDate)
            ->pluck('start_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            });

        $fullyBookedDates = collect();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths(6)->endOfMonth();

        $availableDates = [];

        $dbBlocked = UnavailableDate::pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $scheduleOverrides = AvailableDate::all()
            ->mapWithKeys(function ($item) {
                return [
                    Carbon::parse($item->date)->format('Y-m-d') => $item->schedule_type,
                ];
            })
            ->toArray();

        while ($startDate <= $endDate) {
            $date = $startDate->format('Y-m-d');
            $timeslots = $this->getTimeslotsForDate($date);

            if (count($timeslots) > 0) {
                $availableDates[] = $date;
            }

            $bookedSlotsCount = Booking::where('date', $date)->count();

            if (count($timeslots) > 0 && $bookedSlotsCount >= count($timeslots)) {
                $fullyBookedDates->push($date);
            }

            $startDate->addDay();
        }

        $availableTimes = [];

        if ($selectedDate) {
            $formattedDate = Carbon::parse($selectedDate)->format('Y-m-d');
            $timeslots = $this->getTimeslotsForDate($formattedDate);

            $availableTimes = collect($timeslots)->map(function ($minutes) {
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

        $isFullyBooked = in_array($selectedDate, $fullyBookedDates->toArray());

        $today = now()->startOfDay();
        $closedSundayMonday = [];

        for ($i = 0; $i < 180; $i++) {
            $d = $today->copy()->addDays($i);
            $formatted = $d->format('Y-m-d');

            if (in_array($d->dayOfWeek, [0, 1]) && !array_key_exists($formatted, $scheduleOverrides)) {
                $closedSundayMonday[] = $formatted;
            }
        }

        $unavailableDates = array_values(array_unique(array_merge($dbBlocked, $closedSundayMonday)));

        $isDateBooked = false;

        return view('calendario', compact(
            'selectedDate',
            'availableDates',
            'bookings',
            'isDateBooked',
            'userBookings',
            'availableTimes',
            'fullyBookedDates',
            'isFullyBooked',
            'unavailableDates',
            'scheduleOverrides'
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

        $date = Carbon::parse($validatedData['date'])->format('Y-m-d');

        $timeslots = $this->getTimeslotsForDate($date);

        if (count($timeslots) === 0) {
            return redirect()->route('calendario')->with('error', 'Questa data non è prenotabile.');
        }

        $validTimes = collect($timeslots)->map(function ($minutes) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;

            return sprintf('%02d:%02d', $hours, $mins);
        })->toArray();

        if (!in_array($validatedData['start_time'], $validTimes)) {
            return redirect()->route('calendario')->with('error', 'Orario non valido per questa data.');
        }

        if (!Auth::user()->is_admin) {
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('date', $date)
                ->first();

            if ($existingBooking) {
                return redirect()->route('calendario')->with('error', 'Hai già una prenotazione per questa data.');
            }
        }

        $startTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validatedData['start_time']);
        $endTime = $startTime->copy()->addMinutes(30);

        $overlappingBooking = Booking::where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime->copy()->subSecond()])
                    ->orWhereBetween('end_time', [$startTime->copy()->addSecond(), $endTime]);
            })
            ->first();

        if ($overlappingBooking) {
            return redirect()->route('calendario')->with('error', 'L\'orario selezionato è già prenotato.');
        }

        $booking = new Booking([
            'user_id' => Auth::id(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $date,
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

        if (Auth::check() && ($booking->user_id == Auth::id() || Auth::user()->is_admin)) {
            $booking->delete();

            return redirect()->route('le-mie-prenotazioni')->with('delete_success', 'Appuntamento eliminato con successo.');
        }

        return redirect()->route('le-mie-prenotazioni')->with('error', 'Non hai l\'autorizzazione per eliminare questo appuntamento.');
    }

    public function toggleDate(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        $validatedData = $request->validate([
            'date' => 'required|date',
            'action' => 'required|in:default,closed,normal,thursday',
        ]);

        $date = Carbon::parse($validatedData['date'])->format('Y-m-d');
        $action = $validatedData['action'];

        if ($action === 'closed') {
            AvailableDate::where('date', $date)->delete();

            UnavailableDate::updateOrCreate([
                'date' => $date,
            ]);

            return response()->json([
                'status' => 'closed',
                'date' => $date,
            ]);
        }

        if ($action === 'normal' || $action === 'thursday') {
            UnavailableDate::where('date', $date)->delete();

            AvailableDate::updateOrCreate(
                ['date' => $date],
                ['schedule_type' => $action]
            );

            return response()->json([
                'status' => 'special',
                'date' => $date,
                'schedule_type' => $action,
            ]);
        }

        if ($action === 'default') {
            UnavailableDate::where('date', $date)->delete();
            AvailableDate::where('date', $date)->delete();

            return response()->json([
                'status' => 'default',
                'date' => $date,
            ]);
        }

        return response()->json(['error' => 'Azione non valida'], 400);
    }
}