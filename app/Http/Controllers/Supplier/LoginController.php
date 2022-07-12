<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        return view('auth.login', [
            'title' => 'Supplier Login',
            'loginRoute' => 'supplier.login',
            'forgotPasswordRoute' => 'supplier.password.request',
            'guard' => 'supplier'
        ]);
    }

    private function validator(Request $request)
    {
        //validation rules.
        if ($request->guard == 'supplier') {
            $rules = [
                'email'    => 'required|email|exists:suppliers|min:6|max:191',
                'password' => 'required|string|min:4|max:255',
            ];
            //custom validation error messages.
            $messages = [
                'email.exists' => 'These credentials do not match our records.',
            ];
            //validate the request.
        } elseif ($request->guard == 'subUser') {
            $rules = [
                'email'    => 'required|email|exists:sub_users|min:6|max:191',
                'password' => 'required|string|min:4|max:255',
            ];
            //custom validation error messages.
            $messages = [
                'email.exists' => 'These credentials do not match our records.',
            ];
            //validate the request.
        }

        $request->validate($rules,$messages);
    }

    private function loginFailed()
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('error','Login failed, please try again!');
    }

    public function login(Request $request)
    {
        $this->validator($request);
        if (Auth::guard('supplier')->attempt($request->only('email','password'),$request->filled('remember'))
            || Auth::guard('subUser')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::guard('supplier')->user();
            $isActive = $request->guard == 'subUser' ? 1 : $user->isActive;
            if ($isActive == 1) {
                return redirect()
                    ->intended(route('supplierPanel'))
                    ->with('status','You are Logged in as Supplier!');
            } else {
                $this->logout();
                //TODO Add a Alert Page For Passive User
                return view('errors.supplier-error');
            }
        }
        //Authentication failed...
        return $this->loginFailed();
    }

    public function logout()
    {
        Auth::guard('supplier')->logout();
        return redirect()
            ->route('supplier.login')
            ->with('status','Supplier has been logged out!');
    }

}
