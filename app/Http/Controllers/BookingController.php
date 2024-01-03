<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Imposta la lingua di Carbon in italiano
        Carbon::setLocale('it');

        // Recupera la data selezionata dalla richiesta
        $selectedDate = $request->input('date');


        if (Auth::check() && Auth::user()->is_admin) {
            // L'amministratore può vedere tutte le prenotazioni
            $bookings = Booking::where('date', $selectedDate)->get();
        } else {
            // Gli utenti normali vedono solo le loro prenotazioni
            $bookings = Booking::where('user_id', Auth::id())->where('date', $selectedDate)->get();
        }



        // Recupera tutte le prenotazioni per la data selezionata
        $bookings = Booking::where('date', $selectedDate)->get();

        // Recupera tutte le date già prenotate
        $bookedDates = Booking::where('date', '>=', now())
            ->where('end_time', '>=', now())
            ->pluck('date')
            ->unique();

        // Recupera tutte le date disponibili
        $availableDates = [];
        $startDate = Carbon::createFromFormat('Y-m-d', now()->format('Y-m-d'));
        $endDate = Carbon::createFromFormat('Y-m-d', now()->addDays(14)->format('Y-m-d'));

        while ($startDate <= $endDate) {
            $date = $startDate->format('Y-m-d');
            $availableDates[] = $date;
            $startDate->addDay();
        }

        // Definisci gli orari di lavoro
        $dayOfWeek = Carbon::parse($selectedDate)->dayOfWeek;
        $timeslots = [];

        if ($dayOfWeek == 4) { // Giovedì
            $timeslots = range(14 * 60, 21.25 * 60 - 25, 25);
        } elseif (in_array($dayOfWeek, [2, 3, 5, 6])) { // Martedì, Mercoledì, Venerdì, Sabato
            // Modifica qui per Venerdì e Sabato
            if (in_array($dayOfWeek, [5, 6])) { // Venerdì, Sabato
                $morning = range(8 * 60, 12.25 * 60 - 25, 25); // Inizia alle 08:00
            } else {
                $morning = range(8.5 * 60, 12.25 * 60 - 25, 25); // Inizia alle 08:30
            }
            // Modifica al range del pomeriggio per includere un orario in più
            $afternoon = range(14 * 60, 19.4 * 60, 25);
            $timeslots = array_merge($morning, $afternoon);
        }

        // Converti i minuti in orari
        $availableTimes = collect($timeslots)->map(function ($minutes) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            return sprintf('%02d:%02d', $hours, $mins);
        });
        // Recupera tutti gli orari prenotati per la data selezionata
        $bookedHours = Booking::where('date', $selectedDate)->pluck('start_time')->all();
        // Calcola gli orari disponibili escludendo quelli prenotati
        $availableHours = array_diff($availableTimes->toArray(), $bookedHours);

        // Verifica se l'utente autenticato ha già prenotato per la data selezionata
        $userBookings = [];
        if (Auth::check()) {
            $userBookings = Booking::where('user_id', Auth::id())
                ->where('date', $selectedDate)
                ->get();
        }

        $isDateBooked = $bookings->where('date', $selectedDate)->count() > 0;

        return view('calendario', compact('selectedDate', 'availableDates', 'bookings', 'isDateBooked', 'userBookings', 'availableHours'));
    }


    public function prenota(Request $request)
    {
        // Validazione dei dati inviati dal modulo
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            // 'end_time' => 'required|date_format:H:i|after:start_time',
            'phone' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'haircut_types' => 'required|array',
            'haircut_types.*' => 'in:Taglio,Taglio con modellatura barba,Taglio Razor fade(Sfumatura),Taglio shampoo e modellatura,Taglio Children,Modellatura barba',

        ]);


        // Per gli amministratori, salta il controllo della prenotazione singola per giorno
        if (!Auth::user()->is_admin) {
            // Verifica se l'utente ha già una prenotazione per la data selezionata
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
        $validDayOfWeeks = [2, 3, 4, 5, 6]; // Dal martedì al sabato

        $validTimeRanges = [
            'morning' => ['08:30', '12:00'],
            'afternoon' => ['14:00', '19:15'],
            'thursday' => ['14:00', '21:00']
        ];

        $isThursday = $dayOfWeek == 4;

        // Se è giovedì, controlla solo l'intervallo del giovedì
        if ($isThursday) {
            $validStart = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['thursday'][0]);
            $validEnd = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['thursday'][1]);
            if (!$startTime->between($validStart, $validEnd)) {
                return redirect()->route('calendario')->with('error', 'Gli orari di prenotazione validi il giovedì sono dalle 14:00 alle 21:00.');
            }
        } else {
            $validDayOfWeeks = [2, 3, 4, 5, 6]; // Dal martedì al sabato

            $validStartMorning = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['morning'][0]);
            $validEndMorning = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['morning'][1]);

            $validStartAfternoon = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['afternoon'][0]);
            $validEndAfternoon = Carbon::createFromFormat('Y-m-d H:i', $validatedData['date'] . ' ' . $validTimeRanges['afternoon'][1]);

            if (
                !in_array($dayOfWeek, $validDayOfWeeks) ||
                (!$startTime->between($validStartMorning, $validEndMorning) &&
                    !$startTime->between($validStartAfternoon, $validEndAfternoon))
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

        // Aggiungi queste righe per settare il campo haircut_types
        $haircutTypes = $request->input('haircut_types');
        $booking->haircut_types = json_encode($haircutTypes);


        if ($booking->save()) {
            return redirect()->route('calendario')->with('success', 'Prenotazione effettuata con successo!');
        } else {
            return redirect()->route('calendario')->with('error', 'Errore durante il salvataggio della prenotazione.');
        }
    }


    public function leMiePrenotazioni()
    {
        // Imposta la lingua locale su italiano
        Carbon::setLocale('it');
        // Recupera le prenotazioni dell'utente autenticato
        $userBookings = Booking::where('user_id', Auth::id())->get();
        return view('le-mie-prenotazioni', compact('userBookings'));
    }

    public function elimina($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return redirect()->route('le-mie-prenotazioni')->with('error', 'Appuntamento non trovato.');
        }

        // Verifica se l'utente autenticato è autorizzato a eliminare l'appuntamento
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