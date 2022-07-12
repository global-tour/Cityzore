<?php

namespace App\Console\Commands;

use App\Booking;
use App\BookingContactMailLog;
use App\Http\Controllers\Helpers\ThrottleMail;
use Illuminate\Console\Command;
use App\Mails;
use App\Http\Controllers\Helpers\MailOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class BookingInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:booking-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for sending mails';

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
        $throttleMail = new ThrottleMail();
        if (!$throttleMail->check()) {
            Log::error('Rate Limit executed successfully');
            return false;
        }

        $mails = BookingContactMailLog::where('status', 0)->take(4)->get();

        foreach ($mails as $mail) {

            try {
                if (is_null($mail->mail_to)) {
                    $mail->status = 2;
                    $mail->save();
                    continue;
                }

                $blade = 'mail.booking_information_for_customer';
                $data = $mail->toArray();
                $data['mail_code'] = $mail['code'];
                $toMail = $mail['mail_to'];
                $booking = Booking::with('extra_files')->findOrFail($mail['booking_id']);
                $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
                $voucher = 'https://www.cityzore.com/print-pdf-frontend/' . $cryptRelated->encrypt($mail['booking_id']);

                Mail::send($blade, array('data' => $data), function ($message) use ($data, $toMail, $booking, $voucher) {
                    $message->from('contact@parisviptrips.com', 'Cityzore');
                    $message->to($toMail);
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

                $mail->logMessage = 'Mail Has Been  Sent Successfully!';
                $mail->status = 1;
                $mail->save();
                $this->output->success('Başarılı');

            } catch (\Exception $exception) {
                $mail->status = 2;
                $mail->save();
                $this->output->error($exception->getMessage());
                Log::error('Mail Hatası: ' . $exception->getMessage());
            }
        }
    }
}
