<?php

namespace App\Jobs;

use Twilio\Rest\Client; // Import the Twilio\Rest\Client class
use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\Log; // Import the Log class

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The booking instance.
     *
     * @var Booking
     */
    protected $booking;

    /**
     * Create a new job instance.
     *
     * @param Booking $booking
     */
    // Costruttore per passare l'oggetto Booking
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Crea il client Twilio
        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            // Invia l'SMS
            $client->messages->create(
                $this->booking->phone,
                [
                    'from' => env('TWILIO_FROM'),
                    // 'body' => 'Promemoria: Il tuo appuntamento è alle ' . $this->booking->start_time->format('d/m/Y H:i')
                    'body' => 'Promemoria: Il tuo appuntamento è alle '
                ]
            );

            // Log dell'SMS inviato
            Log::info('SMS inviato a ' . $this->booking->phone);

        } catch (Exception $e) {
            // Log in caso di errore
            Log::error('Errore nell\'invio SMS: ' . $e->getMessage());
        }
    }
}