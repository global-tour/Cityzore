<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\MailOperations;
use Illuminate\Console\Command;
use App\Booking;
use Carbon\Carbon;

class BookingCounterReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking-counter:reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $mailOperations;
    public function __construct()
    {
        $this->mailOperations = new MailOperations();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bookingsLimit = 150;

        $bookings = Booking::whereNotIn('status', [2, 3])->whereDate('dateForSort', '>=', Carbon::today())->whereDate('dateForSort', '<=', Carbon::today()->addDays(30))->get()->groupBy(function($booking) {
            return Carbon::createFromFormat('Y-m-d', $booking->dateForSort)->format('Y-m-d');
        });
        $bookingsToBeReminded = [];

        foreach($bookings as $key => $booking) {
            if(count($booking) >= $bookingsLimit) {
                $bookingsToBeReminded[$key] = count($booking);
            }
        }

        if(count($bookingsToBeReminded) > 0) {
            $this->mailOperations->sendMail(array(
                'subject' => 'Booking Counter Reminder',
                'data' => $bookingsToBeReminded
            ), 'mail.booking-counter-reminder', 'contact@parisviptrips.com');
        }
    }
}
