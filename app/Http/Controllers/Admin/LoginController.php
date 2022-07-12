<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login', [
            'title' => 'Admin Login',
            'loginRoute' => 'admin.login',
            'forgotPasswordRoute' => 'admin.password.request',
            'guard' => 'admin'
        ]);
    }

    /**
     * @param Request $request
     */
    private function validator(Request $request)
    {
        //validation rules.
        $rules = [
            'email' => 'required|email|exists:admins|min:5|max:191',
            'password' => 'required|string|min:4|max:255',
        ];
        //custom validation error messages.
        $messages = [
            'email.exists' => 'These credentials do not match our records.',
        ];
        //validate the request.
        $request->validate($rules, $messages);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    private function loginFailed()
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Login failed, please try again!');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validator($request);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()
                ->intended(route('adminPanel'))
                ->with('status', 'You are Logged in as Admin!');
        }
        //Authentication failed...
        return $this->loginFailed();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()
            ->route('admin.login')
            ->with('status', 'Admin has been logged out!');
    }

    public function mailTest()
    {
        if (!request()->has('mailTo')) {
            abort(404);
        }
        try {
            $detail = [
                'content' => 'sşdflkasşldfka',
                'subject' => 'aslkdmflakmsdf'
            ];

            \Illuminate\Support\Facades\Mail::to(request()->get('mailTo'))->send(new \App\Mail\BulkMail($detail));

            dd('Mail Sended..');

        } catch (\Swift_TransportException $exception) {

            return response()->json($exception->getMessage());

        }
    }

}
