<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class DeletePastBookings extends Command
{
    protected $signature = 'bookings:delete-past';
    protected $description = 'Elimina prenotazioni con data passata';

    public function handle()
    {
        $today = Carbon::today();

        // Elimina le prenotazioni con una data precedente a oggi
        $deletedBookings = Booking::where('date', '<', $today)->delete();

        $this->info("Eliminate $deletedBookings prenotazioni con data passata.");
    }
}

