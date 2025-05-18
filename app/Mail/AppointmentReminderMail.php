<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct($bookingId)
    {
        $this->booking = Booking::find($bookingId);
    }

    public function build()
    {
        return $this->subject('Promemoria del tuo appuntamento')
                    ->from('prenotazioni@giochidicapelli.it', 'Giochi di Capelli')
                    ->view('emails.appointment-reminder')
                    ->with([
                        'booking' => $this->booking,
                    ]);
    }
}