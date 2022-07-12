<?php

namespace App\Http\Controllers\User;

use App\Country;
use App\Events\StatusLiked;
use App\LicenseFile;
use App\Mails;
use App\Option;
use App\User;
use App\Product;
use App\Commission;
use App\PaymentDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Helpers\MailOperations;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReCaptcha\ReCaptcha;


class CommissionerController extends Controller
{

    public $mailOperations;

    public function __construct()
    {
        $this->mailOperations = new MailOperations();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function register()
    {
        $translationArray = json_encode([
            "passwordCharacterLength" => __('passwordCharacterLength'),
            "passwordConfirmationEquality"=>__('passwordConfirmationEquality')
        ]);
        $country = Country::all();
        return view('frontend.become-a-commissioner', [
            'country' => $country,
            'translationArray' => $translationArray
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function commissioners()
    {
        $users = User::where('commission', '!=', null)->get();
        return view('panel.commissioners.index', ['users' => $users]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        return redirect()->back()->with(["error" => "invalid Email Address!"]);
       }

        $this->validate($request, [
            'email'=>'required|email|unique:users',
            'contactName' => 'regex:/(^([a-zA-Z\s]+)$)/',
            'contactSurname' => 'regex:/(^([a-zA-Z\s]+)$)/',
            'g-recaptcha-response' => 'required'
        ], [
            'regex' => 'This field format is invalid'
        ]);

        $user = new User();
        $user->companyName = $request->companyName;
        $user->name = $request->contactName;
        $user->surname = $request->contactSurname;
        $user->email = $request->email;
        $user->countryCode = $request->countryCode;
        $user->phoneNumber = $request->phoneNumber;
        $user->address = $request->address;
        $user->commission = 0.00;
        $user->isActive = 0;
        $user->affiliate_unique = hash('sha256', Str::random(60));
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            event(new StatusLiked($user->companyName.' has been registered as commissioner !', $user, 'COMPANY_REGISTER'));

            // Mail for commissioner
            $mail = new Mails();
            $data = [];
            array_push($data, [
             'name' => $request->companyName,
             'subject' => 'Registration is Pending',
             'sendToCC' => false
            ]);
            $mail->data = json_encode($data);
            $mail->to = $request->email;
            $mail->blade = 'mail.commissioner-pending';
            $mail->save();

            // Mail for us -> contact@parisviptrips.com
            $mail = new Mails();
            $data = [];
            array_push($data, [
                'companyName' => $user->companyName,
                'countryCode' => $user->countryCode,
                'phoneNumber' => $user->phoneNumber,
                'subject' => 'Agency Registration is Pending!',
                'companyEmail' => $user->email,
                'sendToCC' => true
            ]);
            $mail->data = json_encode($data);
            $mail->to = 'contact@parisviptrips.com';
            $mail->blade = 'mail.commissioner-pending-for-us';
            $mail->save();
        }

        return view('frontend.commissioner-status');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function commissionerDetails($id)
    {
        $user = User::findOrFail($id);
        $commissions = Commission::where('commissionerID', '=', $id)->get();
        $options = [];
        foreach ($commissions as $commission) {
            $option = Option::where('id', '=', $commission->optionID)->get();
            array_push($options, $option);
        }
        return view('panel.commissioners.details', ['user' => $user, 'commissions' => $commissions, 'options' => $options]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCommission(Request $request)
    {
        $commissioner = User::findOrFail($request->commissionerId);
        $commission = $request->commission;
        $commissioner->commission = $commission;
        if ($commissioner->save()) {
            return response()->json(['success' => 'Commission is successfully saved!']);
        }

        return response()->json(['error' => 'Something went wrong while saving commission!']);
    }

    public function saveCommissionType(Request $request)
    {
        $commissioner = User::findOrFail($request->commissionerId);
        $commissionType = $request->commissionType;
        $commissioner->commissionType = $commissionType;
        if ($commissioner->save()) {
            return response()->json(['success' => 'Commission Type is successfully saved!']);
        }

        return response()->json(['error' => 'Something went wrong while saving commission!']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(Request $request)
    {
        $commissioner = User::findOrFail($request->id);
        $commissioner->isActive = $request->isActive;
        if ($request->isActive == 1) {
            $mail = new Mails();
            // Mail for agency
            $data = [];
            array_push($data, [
                'name' => $commissioner->name,
                'subject' => 'Registration is Active',
                'sendToCC' => false
            ]);
            $mail->data = json_encode($data);
            $mail->to = $commissioner->email;
            $mail->blade = 'mail.commissioner-active';
            $response = ['success' => 'Commissioner is active now!'];
        } else {
            $mail = new Mails();

            // Mail for commissioner
            $data = [];
            array_push($data, [
                'name' => $commissioner->name,
                'subject' => 'Registration is Pending',
                'sendToCC' => false
            ]);
            $mail->data = json_encode($data);
            $mail->to = $commissioner->email;
            $mail->blade = 'mail.commissioner-pending';

            // Mail for us -> contact@parisviptrips.com
            $mail2 = new Mails();
            $data2 = [];
            array_push($data2, [
                'companyName' => $commissioner->companyName,
                'countryCode' => $commissioner->countryCode,
                'phoneNumber' => $commissioner->phoneNumber,
                'companyEmail' => $commissioner->email,
                'subject' => 'Agency Registration is Pending!',
                'sendToCC' => true
            ]);
            $mail2->data = json_encode($data2);
            $mail2->to = 'contact@parisviptrips.com';
            $mail2->blade = 'mail.commissioner-pending-for-us';
            $mail2->save();
            $response = ['error' => 'Commissioner is not active now!'];
        }
        $commissioner->save();

        $mail->save();

        return response()->json($response);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCommissions($id)
    {
        $commissioner = User::findOrFail($id);
        $products = Product::where('supplierID', -1)->where('isDraft', 0)->where('isPublished', 1)->get();
        return view('panel.commissioners.edit', ['commissioner' => $commissioner, 'products' => $products]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionSelect(Request $request)
    {
        $commission = Commission::where('optionID', $request->optionSelect)->where('commissionerID', $request->commissionerID);
        if ($commission->count() == 1) {
            return response()->json(['commission' => $commission->first()]);
        }
        return response()->json(['commission' => null]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOptionCommission(Request $request)
    {
        $commission = Commission::where('commissionerID', $request->commissionerID)->where('optionID', $request->optionID)->first();
        if ($commission) {
            $commission->commission = $request->commission;
        } else {
            $commission = new Commission();
            $commission->commissionerID = $request->commissionerID;
            $commission->optionID = $request->optionID;
            $commission->commission = $request->commission;
        }
        $commission->save();
        return response()->json(['success' => 'Commission is saved successfully!']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function turnToStandardUser($id)
    {
        $user = User::findOrFail($id);
        $user->isActive = 0;
        $user->commission = NULL;
        $user->save();

        return redirect('/commissioners');
    }

    /**
     * @param $lang
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentDetails($lang, $id)
    {
      //starting security
       if (auth()->user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot view the information of another comissioner!']);
      //ending security



        $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'commissioner')->first();
        return view('frontend.payment-details',
            [
                'payment' => $payment
            ]
        );
    }

    /**
     * @param $lang
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function storePaymentDetails($lang, Request $request, $id)
    {
      //starting security
       if (auth()->user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot Update or Store the information of another comissioner!']);
      //ending security

        $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'commissioner')->first();
        if (is_null($payment)) {
            $payment = new PaymentDetail();
        }

        $payment->companyID = auth()->user()->id;
        $payment->companyType = 'commissioner';
        $payment->bankName = $request->bankName;
        $payment->bankBranch = $request->bankBranch;
        $payment->city = $request->city;
        $payment->district = $request->district;
        $payment->postalCode = $request->postalCode;
        $payment->swift = $request->swift;
        $payment->iban = $request->iban;
        $payment->save();

        return view('frontend.payment-details',
            [
                'payment' => $payment
            ]
        );
    }

    /**
     * @param $lang
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function licenseFiles($lang, $id)
    {
        //starting security
       if (auth()->user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot view the license file of another user!']);
      //ending security

        $licenses = LicenseFile::where('companyID', $id)->where('companyType', 'commissioner')->get();

        return view('frontend.license-files',
            [
                'licenses' => $licenses
            ]
        );
    }

    /**
     * @param $lang
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeLicenseFiles($lang, Request $request, $id)
    {

       //starting security
       if (auth()->user()->id !== (int)$id)
         return redirect()->back()->with(['error' => 'You cannot store the license file for another user!']);
      //ending security

        $files = $request->file('fileName');
        $title = $request->get('title');

        if (!empty($files[0])) {
            foreach ($files as $ind => $file) {
                if($file->getMimeType() != "application/pdf"){
                return redirect()->back()->with(['error' => 'You can upload only pdf files!']);
                }

                $license = new LicenseFile();
                $license->companyID = $id;
                $license->companyType = 'Commissioner';
                $license->title = $title[$ind];
                $license->fileName = Str::slug(User::findOrFail($id)->name,'.').'-'.Str::slug($license->title.'.'.$file->getClientOriginalExtension(), '.');
                if ($license->save()) {
                    $file->move(public_path('storage/license-files/'), Str::slug(User::findOrFail($id)->name,'.').'-'.Str::slug($license->title.'.'.$file->getClientOriginalExtension(), '.'));
                }
            }
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        //return redirect($langCodeForUrl.'/license-files/'.$id.'/details');
         return redirect()->back()->with(['success' => 'Files uploaded Successfully!']);
    }

    /**
     * @param Request $request
     */
    public function deleteLicenses(Request $request)
    {
        $license = LicenseFile::findOrFail($request->id);
        if ($license->delete()) {
            Storage::delete('license-files/'.$license->fileName);
        }
    }

    /**
     * @param Request $request
     */
    public function editSuggestFile(Request $request)
    {
        $license = LicenseFile::findOrFail($request->id);
        $companyID = $license->companyID;
        $data = [];
        array_push($data, ['subject' => 'Edit Suggest For Your Licenses', 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
        $mail = new Mails();
        $mail->to = User::findOrFail($companyID)->email;
        $mail->data = json_encode($data);
        $mail->blade = 'mail.edit-suggest-licenses';
        $mail->status = 0;
        $mail->save();
    }

    /**
     * @param Request $request
     */
    public function changeLicenseStatus(Request $request)
    {
        $license = LicenseFile::findOrFail($request->id);
        $license->confirm = $request->value;
        if ($license->save()) {
            $data = [];

            $mail = new Mails();
            $mail->to = User::findOrFail($license->companyID)->email;
            $mail->status = 0;
            if ($license->confirm == 1) {
                array_push($data, ['subject' => 'Your License Has Been Confirmed !', 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
                $mail->blade = 'mail.confirm-license';
            } else {
                array_push($data, ['subject' => 'Your License Has Been Declined !', 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
                $mail->blade = 'mail.declined-license';
            }
            $mail->data = json_encode($data);

            $mail->save();
        }
    }

}
