<?php

namespace App\Http\Controllers\User;

use App\Country;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\User;
use Auth;

//Enables us to output flash messaging
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

use DB;

class UserController extends Controller {

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::where('commission', null)->where('isActive', 0)->orderBy('id', 'DESC')->get();
        return view('panel.users.index')->with('users', $users);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = User::all();
        $country = Country::all();
        $platforms = \App\Platform::where('status', 1)->get();
        return view('panel.users.create', ['user' => $user, 'country' => $country, 'platforms' => $platforms]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|max:120',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|confirmed',
        ]);

        if (request()->hasFile('avatar')) {
            request()->validate([
               'avatar' => 'required|image',
            ]);
        }

        DB::transaction(function () use($request) {
            $user = new User();
            $user->name = $request->get('name');
            $user->surname = $request->get('surname');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->countryCode = $request->get('countryCode');
            $user->phoneNumber = $request->get('phoneNumber');
            $user->address = $request->get('address');
            $user->isActive = $request->get('isCommissioner') ? $request->get('isCommissioner') : 0;
            if ($request->get('isCommissioner') == 1) {

                $user->commission = $request->get('commission');
                $user->commissionType = $request->get('commissionType');
            }
            $user->affiliate_unique = $request->affiliate_unique;

            $file = $request->file('avatar');
            if ($file != '') {
                $file->move(public_path().'/uploads/avatars/',$request->get('email').time().'.png');
                $user->avatar = 'uploads/avatars/'.$request->get('email').time().'.png';
            }
            $user->save();

            if ($request->get('isCommissioner') == 1) {
                if($request->get('platform') != 'noPlatform') {
                    \App\UserPlatform::create(['user_id' => $user->id, 'platform_id' => $request->get('platform')]);
                }
            }
        });

        return redirect('/user');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::with('platform')->findOrFail($id); //Get user with specified id

        $country  = Country::all();
        $platforms = \App\Platform::where('status', 1)->get();
        return view('panel.users.edit', [ 'user' => $user , 'country' => $country, 'platforms' => $platforms]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $users = User::all();
        return view('frontend.profile-details', [ 'users' => $users ]);
    }

    /**
     * @param $lang
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editFrontend($lang, $id)
    {
        // starting security control
        if(Auth::user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot view the information of another user!']);
        //ending security control


        $user = User::findOrFail($id); //Get user with specified id
        $country  = Country::all();
        return view('frontend.edit-profile', [ 'user' => $user, 'country' => $country]);
    }

    /**
     * @param $lang
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateFrontend($lang, Request $request, $id)
    {
        // starting security control
        if(Auth::user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot update the information of another user!']);
        //ending security control

        $user = User::findOrFail($id);
        if (!is_null($user->commission)) {
            $user->companyName = $request->companyName;
        }
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->countryCode = $request->countryCode;
        $user->phoneNumber = $request->phoneNumber;
        if (!is_null($request->get('password')) && !is_null($request->get('password_confirmation'))) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        return redirect($langCodeForUrl.'/profile-details');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name'=>'required|max:255',
            'surname'=>'required|max:255',
            'address' => 'max:255',
            'email'=>'required|email|unique:users,email,'.$id,
        ]);

        $file = $request->file('avatar');
        if ($file != '') {
            $file->move(public_path().'/uploads/avatars/',$user->email.time().'.png');
            $user->avatar = 'uploads/avatars/'.$user->email.time().'.png';
        }

        if (!is_null($user->commission)) {
            $user->companyName = $request->get('companyName');
        }
        $user->affiliate_unique = $request->affiliate_unique;
        $user->name = $request->get('name');
        $user->surname = $request->get('surname');
        $user->email = $request->get('email');
        if (!is_null($request->get('password')) && !is_null($request->get('password_confirmation'))) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->countryCode = $request->get('countryCode');
        $user->phoneNumber = $request->get('phoneNumber');
        $user->address = $request->get('address');
        //$user->isActive = $request->get('isCommissioner') == NULL ? 0 : 1;
        if ($request->get('isCommissioner') == 0) {
            $user->commission = NULL;
            $user->commissionType = NULL;
            \App\UserPlatform::where('user_id', $user->id)->delete();
        } else {
            $user->commission = $request->get('commission');
            $user->commissionType = $request->get('commissionType');
            $platform = \App\UserPlatform::where('user_id', $user->id)->first();
            if($request->get('platform') != 'noPlatform') {
                if($platform) {
                    $platform->platform_id = $request->get('platform');
                    $platform->save();
                } else {
                    \App\UserPlatform::create(['user_id' => $user->id, 'platform_id' => $request->get('platform')]);
                }
            }
        }
        $user->save();

        if (!is_null($user->commission)) {
            return redirect('commissioners')->with(['success' => 'changes has been done successfully!']);
        }
        return redirect('user')->with(['success' => 'changes has been done successfully!']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        //Find a user with a given id and delete
        $user = User::findOrFail($id);
        if ($user->avatar == "uploads/avatars/default_user_avatar.png") {
            $user->delete();
            return redirect('/user');
        } else {
            File::delete( public_path('/' . $user->avatar));
            $user->delete();
        }

        return redirect('user');
    }

}
