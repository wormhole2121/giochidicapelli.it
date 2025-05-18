<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bookingId;

    public function __construct($bookingId)
    {
        $this->bookingId = $bookingId;
    }

    public function handle()
    {
        $booking = Booking::with('user')->find($this->bookingId);

        if ($booking && $booking->user) {
            Mail::to($booking->user->email)->send(new AppointmentReminderMail($booking->id));

        }
    }
}


