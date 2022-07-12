<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\BookingContactMailLog;
use App\Booking;

class MailOperations extends Controller
{

    protected $throttleMail;


    public function __construct()
    {
        $this->throttleMail = new ThrottleMail();
    }

    /**
     * @param $data
     * @param $blade
     * @param null $toMail
     */
    public function sendMail($data, $blade, $toMail = null)
    {
        if (!$this->throttleMail->check()) {
            return false;
        }

        $to = array_key_exists('email', $data) ? $data['email'] : $toMail;
        if ($blade == 'mail.booking-successful') {
            $data['hash'] = Crypt::encryptString($data['booking_id']);
        }
        Mail::send($blade, array('data' => $data), function ($message) use ($data, $to, $blade) {
            $message->from('contact@cityzore.com', 'Cityzore');

            if ($blade == 'mail.booking-reminder') {
                $message->attach(public_path() . '/pdf/global_tours_app_find_my_guide_instructions.pdf');
                $booking = Booking::findOrFail($data["booking_id"]);

                $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
                $voucher = 'https://www.cityzore.com/print-pdf-frontend/' . $cryptRelated->encrypt($data["booking_id"]);

                $message->attach($voucher, [
                    'as' => 'Voucher.pdf',
                    'mime' => 'application/pdf',
                ]);

                foreach ($booking->extra_files as $file) {
                    $format = explode(".", $file->image_name);
                    $format = $format[count($format) - 1];
                    $message->attach($file->image_name, [
                        'as' => $file->image_base_name . "." . $format,
                    ]);
                }
            }


            $message->to($to);
            if (array_key_exists('sendToCC', $data) && $data['sendToCC']) {
                $message->cc('contact@parisviptrips.com');
            }
            $message->subject($data['subject']);
        });
    }

    public function sendMailForBookingContacts($data, $blade, $toMail = null)
    {

        if (auth()->guard("admin")->check()) {
            $adminID = auth()->guard("admin")->user()->id;
        } else {
            return false;
        }


        $mail = new BookingContactMailLog();
        $mail->sender_id = $adminID;
        $mail->booking_id = $data["booking_id"];
        $mail->mail_message = $data["mail_message"];
        $mail->mail_title = $data["mail_title"];
        $mail->mail_to = $data["mail_to"];
        $mail->code = $data["mail_code"];



        $booking = Booking::findOrFail($data["booking_id"]);

        $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
        $voucher = 'https://www.cityzore.com/print-pdf-frontend/' . $cryptRelated->encrypt($data["booking_id"]);



        $mail->files = "";
        foreach ($booking->extra_files as $key => $file) {
            $key == 0 ? ($mail->files = $file->image_base_name) : ($mail->files = $mail->files . ', ' . $file->image_base_name);
        }

        if (!$this->throttleMail->check()) {
            $mail->status =  0;
            $mail->logMessage = "Mail Has Been Queued!";
            $mail->save();

            return true;
        }

        Mail::send($blade, array('data' => $data), function ($message) use ($data, $toMail, $blade, $booking, $voucher) {
            $message->from('contact@parisviptrips.com', 'Cityzore');
            $message->to($toMail);
            //$message->cc('contact@parisviptrips.com');
            $message->subject($data['mail_title']);


            $message->attach($voucher, [
                'as' => 'Voucher.pdf',
                'mime' => 'application/pdf',
            ]);

            foreach ($booking->extra_files as $file) {
                $format = explode(".", $file->image_name);
                $format = $format[count($format) - 1];
                $message->attach($file->image_name, [
                    'as' => $file->image_base_name . "." . $format,
                ]);
            }

            $option = $booking->bookingOption;
            $optionFiles = \App\OptionFile::where('option_id', $option->id)->get();
            foreach ($optionFiles as $optionFile) {
                $message->attach($optionFile->file, [
                    'as' => $optionFile->fileName
                ]);
            }

        });



        if (Mail::failures()) {
            $mail->status = 0;
            $mail->logMessage = Mail::failures();
            $mail->save();
            return false;
        } else {
            $mail->status = 1;
            $mail->logMessage = "Mail Has Been  Sent Successfully!";
            $mail->save();
            return true;
        }


    }

}
