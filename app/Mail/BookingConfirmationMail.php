<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($bookingId)
    {
        $this->booking = Booking::find($bookingId);
    }

    public function build()
    {
        return $this->subject('Conferma della tua prenotazione')
                    ->from('prenotazioni@giochidicapelli.it', 'Giochi di Capelli')
                    ->view('emails.booking-confirmation')
                    ->with([
                        'booking' => $this->booking,
                    ]);
    }
}
