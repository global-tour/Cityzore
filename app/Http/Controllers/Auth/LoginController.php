<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\SubUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\Admin;
use App\Supplier;
use App\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Helpers\MailOperations;
use Illuminate\Support\Facades\Hash;

use Laravel\Socialite\Facades\Socialite as Socialite;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $redirectTo = '/';
    public $mailOperations;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->mailOperations = new MailOperations();
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('status','User has been logged out!');
    }

    public function showLoginForm()
    {
        return view('auth.login',[
            'title' => 'Login',
            'loginRoute' => 'login',
            'forgotPasswordRoute' => 'password.request',
            'guard' => 'web'
        ]);
    }

    public function redirectToProviderFacebook()
    {
        //return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallbackFacebook()
    {
        //$user = Socialite::driver('facebook')->user();
    }

    public function redirectToProviderGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallbackGoogle(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
            $langCodeForUrl = $langCode == 'en' ? '' : $langCode;
            return redirect($langCodeForUrl.'/login');
        }

        $existingUser = User::where('email', $user->email)->first();
        if ($existingUser) {
            auth()->login($existingUser, true);
        } else {
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->google_id = $user->id;
            $newUser->save();
            auth()->login($newUser, true);
        }
        return redirect()->to('/');
    }

    public function resetPassword()
    {
        return view('auth.email');
    }

    public function sendResetPasswordEmail(Request $request)
    {
        $requestURL = $request->url();
        $model = new User();
        $baseURL = env('APP_URL', 'https://cityzore.com');
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        if (strpos($requestURL, 'admin') != false) {
            $model = new Admin();
            $baseURL = env('APP_ADMIN', 'https://admin.cityzore.com');
            $langCode = '';
        } else if (strpos($requestURL, 'supplier') != false) {
            $model = new Supplier();
            $baseURL = env('APP_SUPPLIER', 'https://supplier.cityzore.com');
            $langCode = '';
        }
        $email = $model::where('email', $request->email)->first();
        if (!is_null($email)) {
            $passwordReset = PasswordReset::where('email', $request->email)->first();
            if ($passwordReset) {
                $passwordReset->delete();
            }
            $passwordReset = new PasswordReset();
            $passwordReset->email = $request->email;
            $passwordReset->token = md5($request->email.microtime());
            if ($passwordReset->save()) {
                if ($langCode == 'en') {
                    $url = $baseURL.'/reset-password-link/'.$passwordReset->token;
                } else {
                    $url = $baseURL.'/'.$langCode.'/reset-password-link/'.$passwordReset->token;
                }
                $data = [
                    'env_mail_username' => env('MAIL_USERNAME'),
                    'subject' => __('sendResetPasswordEmailSubject'),
                    'email' => $request->email,
                    'url' => $url,
                    'sendToCC' => false
                ];
                $this->mailOperations->sendMail($data, 'mail.password');
                return response()->json(['success' => __('passwordResetSuccess')]);
            }
        }
        return response()->json(['error' => __('passwordResetFailed')]);
    }

    public function resetPasswordLink($lang, $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->where('isUsed', 0)->first();
        if ($passwordReset) {
            $translationArray = json_encode([
                "passwordCharacterLength" => __('passwordCharacterLength'),
                "passwordConfirmationEquality"=>__('passwordConfirmationEquality')
            ]);
            return view('auth.reset', ['email' => $passwordReset->email, 'token' => $token, 'translationArray' => $translationArray]);
        }
        abort(404);
    }

    public function resetPasswordLinkForPanel($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->where('isUsed', 0)->first();
        if ($passwordReset) {
            $translationArray = json_encode([
                "passwordCharacterLength" => __('passwordCharacterLength'),
                "passwordConfirmationEquality"=>__('passwordConfirmationEquality')
            ]);
            return view('auth.reset', ['email' => $passwordReset->email, 'token' => $token, 'translationArray' => $translationArray]);
        }
        abort(404);
    }

    public function sendNewPassword(Request $request)
    {
        $requestURL = $request->url();
        $model = new User();
        if (strpos($requestURL, 'admin') != false) {
            $model = new Admin();
        } elseif (strpos($requestURL, 'supplier') != false) {
            $model = new Supplier();
        }
        $user = $model::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $passwordReset = PasswordReset::where('token', $request->token)->first();
            $passwordReset->isUsed = 1;
            $passwordReset->save();
            if ($user->save()) {
                return response()->json(['success' => __('sendNewPasswordSuccess')]);
            }
        }
        $translationArray = json_encode([
            "passwordCharacterLength" => __('passwordCharacterLength'),
            "passwordConfirmationEquality"=>__('passwordConfirmationEquality')
        ]);
        return response()->json(['error' => __('sendNewPasswordError'), 'translationArray' => $translationArray]);
    }

    public function validateLogin(Request $request)
    {
        $guard = $request->guard;
        switch ($guard) {
            case 'admin':
                $model = new Admin();
                break;
            case 'supplier':
                $model = new Supplier();
                break;
            case 'web':
                $model = new User();
                break;
            case 'subUser' :
                $model = new SubUser();
                break;
            default:
                $model = new User();
        }
        $email = $request->email;
        $password = $request->password;
        $validUser = $model::where('email', $email)->first();
        if ($validUser) {
            if (Hash::check($password, $validUser->password)) {
                if ($guard == 'web') {
                    if ($validUser->isActive == 1) {
                        return response()->json(['success' => __('userCredentialsRight')]);
                    } else {
                        return response()->json(['error' => __('userActivateMessage')]);
                    }
                } else if ($guard == 'admin') {
                    return response()->json(['success' => __('userCredentialsRight')]);
                } else if ($guard == 'supplier' || $guard == 'subUser') {
                    return response()->json(['success' => __('userCredentialsRight')]);
                }
            }
        }
        return response()->json(['error' => __('userCredentialsWrong').'<a style="color: #a94442;" href="/password/reset"><strong>'.__('forgotPassword').'</strong></a>']);
    }

}
