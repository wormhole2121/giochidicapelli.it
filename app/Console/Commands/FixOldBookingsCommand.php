<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class FixOldBookingsCommand extends Command
{
    protected $signature = 'fix:old-bookings';
    protected $description = 'Allinea tutte le prenotazioni esistenti agli slot da 30 minuti';

    public function handle()
    {
        $bookings = Booking::all();
        $fixed = 0;

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->start_time);
            $end = Carbon::parse($booking->end_time);

            $duration = $start->diffInMinutes($end);
            $startMinutes = $start->hour * 60 + $start->minute;

            if ($duration != 30 || $startMinutes % 30 != 0) {
                $roundedMinutes = floor($startMinutes / 30) * 30;
                $newStart = Carbon::parse($start->format('Y-m-d'))->addMinutes($roundedMinutes);
                $newEnd = $newStart->copy()->addMinutes(30);

                $booking->start_time = $newStart;
                $booking->end_time = $newEnd;
                $booking->save();

                $this->info("Fix booking ID {$booking->id}: {$start->format('H:i')} → {$newStart->format('H:i')}");
                $fixed++;
            }
        }

        $this->info("✔️ Prenotazioni sistemate: $fixed");
    }
}
