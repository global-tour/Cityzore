<?php

namespace App\Http\Controllers\Admin;

use App\BookingContactMailLog;
use App\Http\Controllers\Helpers\MailOperations;
use App\Mails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{
    public function isRead($code)
    {
        $clearCode = htmlspecialchars($code);
        if (BookingContactMailLog::where('code', $clearCode)->exists()) {
            BookingContactMailLog::where('code', $clearCode)->increment('read_count', 1);
            echo 'başarılı';
        }
        if (Mails::where('code', $clearCode)->exists()) {
            Mails::where('code', $clearCode)->increment('read_count', 1);
            echo 'başarılı';
        }
        echo 'running';
    }

    public function contact404(Request $request)
    {
        $validatorMessages = array(
            'mail.required' => 'Email field is required',
            'mail.email' => 'Email field must be in email format',
            'mail.max' => 'Email field can be maximum 100 characters',
            'message.required' => 'Message field is required',
            'message.max' => 'Message field can be maximum 300 characters',
            'g-recaptcha-response' => ['required']
        );

        $validator = Validator::make($request->all(), [
            'mail' => 'required|email|max:100',
            'message' => 'required|max:300',
        ], $validatorMessages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $data['mail']=htmlspecialchars($request->mail);
        $data['message']=htmlspecialchars($request->message);
        $data['subject']='Error Page Guest Contact Mail';


        $to = 'contact@parisviptrips.com';
        Mail::send('mail.error', array('data' => $data), function ($message) use ($data, $to) {
            $message->from('contact@cityzore.com', 'Cityzore');
            $message->to($to);
            $message->subject($data['subject']);
        });

        return back()->with(['success'=>'Your message has been sent successfully! Feedback will be made as soon as possible.']);
    }

    public function error404()
    {
        return view('errors.404');
    }
}
