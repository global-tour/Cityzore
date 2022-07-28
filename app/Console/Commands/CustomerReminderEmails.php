<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mails;
use App\Booking;
use Carbon\Carbon;
use App\Option;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\CryptRelated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CustomerReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for reminder booking for customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            Booking::with(['mails', 'bookingOption'])
                ->withCount(['mails' => function ($q) {
                    $q->where('blade', 'mail.booking-reminder');
                }])
                ->where('status', 0)
                ->where(DB::raw('DATE_FORMAT(dateForSort, "%Y-%m-%d")'), now()->addDay()->format('Y-m-d'))
                ->get()
                ->each(function ($item) {
                    $traveler = json_decode($item->travelers, 1)[0];
                    $meetingDateTime = $item->bookingDateTime['org'];

                    if (!is_null($item->bookingOption->customer_mail_templates) &&
                        !empty(json_decode($item->bookingOption->customer_mail_templates, true)["en"])) {

                        if (!$item->mails_count) {

                            $mailTemplate = json_decode($item->bookingOption->customer_mail_templates, true)["en"];
                            $mailTemplate = str_replace("#NAME SURNAME#", $traveler["firstName"] . " " . $traveler['lastName'], $mailTemplate);
                            $mailTemplate = str_replace("#SENDER#", "Paris Business & Travel", $mailTemplate);
                            $mailTemplate = str_replace("#DATE#", $meetingDateTime, $mailTemplate);
                            $mailTemplate = nl2br($mailTemplate);

                            $mails = new Mails();
                            $mails->bookingID = $item->id;
                            $mails->to = $traveler['email'];
                            $mails->status = 0;
                            $mails->blade = 'mail.booking-reminder';


                            $data[] = [
                                'dateForSort' => $item->dateForSort,
                                'options' => $item->bookingOption->title,
                                'date' => $item->date,
                                'hour' => $item->bookingDateTime['time'],
                                'subject' => 'Upcoming Booking Announcements',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'sendToCC' => false,
                                'template' => $mailTemplate,
                                'booking_id' => $item->id
                            ];

                            $mails->data = json_encode($data);

                            $mails->save();

                        }

                    }

                });

        } catch (\Exception $exception) {

            Log::error('Reminder Mail Error: '. $exception->getMessage());

        }
    }
}
