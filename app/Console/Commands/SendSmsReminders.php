<?php

namespace App\Console\Commands;
use App\Models\Booking;
use Carbon\Carbon;
use App\Jobs\SendReminderSms;
use Illuminate\Console\Command;

class SendSmsReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sms-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders for bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $inFourHours = $now->copy()->addHour(299);

        $bookings = Booking::where('start_time', '>=', $now)
                            ->where('start_time', '<=', $inFourHours)
                            ->get();

        foreach ($bookings as $booking) {
            dispatch(new SendReminderSms($booking));
        }
    }
}
