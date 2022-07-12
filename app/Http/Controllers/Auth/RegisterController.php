<?php

namespace App\Http\Controllers\Auth;

use App\Events\StatusLiked;
use App\Mails;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\CommonFunctions;
use ReCaptcha\ReCaptcha;
use App\Country;
use App\Coupon;
use App\Config;


class RegisterController extends Controller
{

    use RegistersUsers;

    public $mailOperations;
    public $commonFunctions;
    protected $redirectTo = '/successful-register';

    public function __construct()
    {
        $this->middleware('guest');
        $this->mailOperations = new MailOperations();
        $this->commonFunctions = new CommonFunctions();
    }

    public function showRegistrationForm()
    {
        $countries = Country::all();
        return view('auth.register',
            [
                'countries' => $countries
            ]
        );
    }

    protected function validator(array $data)
    {
        /* Validation about user table */
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z\s]+)$)/'],
            'surname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required']
        ], [
            'regex' => 'This :attribute format is invalid'
        ]);
    }

    protected function create(array $data)
    {

        $user = new User();
        $data['ip'] = request()->ip();
        $user->name = $data['name'];
        $user->surname = $data['surname'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->countryCode = $data['countryCode'];
        $user->phoneNumber = $data['phoneNumber'];
        $user->address = $data['address'];
        $user->isActive = 1;

        if ($user->save()) {
            $verificationLink = env('APP_URL', 'https://cityzore.com').'/verification/'.$user->id;
            $mail = new Mails();
            $mailData = [];

            array_push($mailData, [
                'subject' => 'E Mail Verification Needed!',
                'firstName' => $data['name'],
                'lastName' => $data['surname'],
                'verificationLink' => $verificationLink,
                'sendToCC' => false,
                'ip' => $data['ip']
            ]);

            $mail->to = $data['email'];
            $mail->data = json_encode($mailData);
            $mail->blade = 'mail.user-verification';
            $mail->save();
            return $user;
        } else {
            return 'Looks like something went wrong. Please try again later';
        }
    }

    public function userVerification($id)
    {
        $user = User::where('id', $id)->first();
        $user->isActive = 1;
        if ($user->save()) {
            $coupon = new Coupon();
            $coupon->type = 6;
            $coupon->couponCode = $this->commonFunctions->generateRandomString(8);
            $coupon->lastSelect = $user->id;
            $coupon->countOfUsing = 0;
            $coupon->maxUsability = 1;
            $coupon->startingDate = date("Y-m-d");
            $coupon->endingDate = date("Y-m-d", strtotime("+1 week"));
            $config = Config::where('userID', -1)->first();
            $coupon->discountType = $config->couponDiscountType;
            $coupon->discount = $config->couponDiscountAmount;
            $coupon->isUsed = 0;
            if ($coupon->save()) {
                $this->mailOperations->sendMail(
                    [
                        'subject' => 'Welcome to Cityzore!',
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'email' => $user->email,
                        'couponCode' => $coupon->couponCode,
                        'couponDiscountType' => $coupon->discountType,
                        'couponDiscount' => $coupon->discount,
                        'sendToCC' => false
                    ],
                    'mail.welcome');
            }
        }

        return view('frontend.successful-verification', ['user' => $user]);
    }

    public function successfulRegister()
    {
        if (Auth::guard()->check()) {
            return redirect('/');
        } else {
            return view('frontend.successful-register');
        }
    }

}

