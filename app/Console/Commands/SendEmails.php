<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\ThrottleMail;
use Illuminate\Console\Command;
use App\Mails;
use App\Http\Controllers\Helpers\MailOperations;
use Illuminate\Support\Facades\Log;


class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:emails';

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
        $mailOperations = new MailOperations();
        $mails = Mails::where('status', 0)->take(4)->get();
        foreach ($mails as $mail) {
            $data = json_decode($mail->data, true);
            $data = $data[0];
            try {
                if (is_null($mail->to)) {
                    $mail->status = 2;
                } else {
                    $mailOperations->sendMail($data, $mail->blade, $mail->to);
                    $mail->status = 1;
                }
            } catch (\Exception $exception) {
                $mail->status = 2;
                Log::error('Mail Hatası: ' . $exception->getMessage());
            }
            $mail->save();
        }
    }
}
