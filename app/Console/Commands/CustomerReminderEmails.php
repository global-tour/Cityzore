<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mails;
use App\Booking;
use Carbon\Carbon;
use App\Option;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\CryptRelated;


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

        $now = Carbon::now();
        $allBookings = Booking::where("status", 0)->get();

        foreach ($allBookings as $booking) {
            $bookingDate = Carbon::make($booking->dateForSort);
            if ($bookingDate->diff($now)->days == 1 && ($now->timestamp < $bookingDate->timestamp)) {


                if (Mails::where("bookingID", $booking->id)->count() == 0) {
                    $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();

                    if (!is_null($option->customer_mail_templates) && !empty(json_decode($option->customer_mail_templates, true)["en"])) {
                        $traveler = json_decode($booking->travelers, true)[0];

                        if (strpos($booking->dateTime, "dateTime") === false) {
                            $meetingDateTime = Carbon::parse($booking->dateTime)->format("d/m/Y H:i:s");
                        } else {
                            $meetingDateTime = $booking->date . " " . json_decode($booking->hour, true)[0]["hour"];
                        }

                        $mailTemplate = json_decode($option->customer_mail_templates, true)["en"];
                        $mailTemplate = str_replace("#NAME SURNAME#", $traveler["firstName"] . " " . $traveler['lastName'], $mailTemplate);
                        $mailTemplate = str_replace("#SENDER#", "Paris Business & Travel", $mailTemplate);
                        $mailTemplate = str_replace("#DATE#", $meetingDateTime, $mailTemplate);
                        $mailTemplate = nl2br($mailTemplate);

                        $mail = new Mails();
                        $mail->bookingID = $booking->id;

                        $data = [];
                        $data[] = [
                            'dateForSort' => $booking->dateForSort,
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => !empty(json_decode($booking->hour, true)[0]['hour']) ? json_decode($booking->hour, true)[0]['hour'] : null,
                            'subject' => 'Upcoming Booking Announcements',
                            'name' => $traveler['firstName'],
                            'surname' => $traveler['lastName'],
                            'sendToCC' => false,
                            'template' => $mailTemplate,
                            'booking_id' => $booking->id
                        ];
                        $mail->data = json_encode($data);
                        $mail->to = $traveler['email'];
                        $mail->status = 0;
                        $mail->blade = 'mail.booking-reminder';


                        $mail->save();

                    }


                } // end of if statement

            }

        }


    }
}
