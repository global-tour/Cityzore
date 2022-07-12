<?php

namespace App\Http\Controllers\Supplier;

use App\Country;
use App\Events\StatusLiked;
use App\LicenseFile;
use App\Mails;
use App\Option;
use App\PaymentDetail;
use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Helpers\CommonFunctions;
use ReCaptcha\ReCaptcha;


class SupplierController extends Controller
{

    public $commonFunctions;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $countries = Country::all();

        if (auth()->guard('admin')->check()) {
            return view('panel.suppliers.index', ['countries' => $countries]);
        } elseif (auth()->guard('supplier')->check()) {
            $suppliers = Supplier::where('createdBy', auth()->user()->id)->get();
            return view('panel.suppliers.restaurants', ['suppliers' => $suppliers, 'countries' => $countries]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $country = Country::has('cities')->get();
        return view('panel.suppliers.create', ['country'=> $country]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function becomeASupplier()
    {
        $suppliers = Supplier::all();
        $country = Country::all();
        return view('frontend.become-a-supplier', ['suppliers' => $suppliers, 'country' => $country]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        //Validate name, email and password fields
        $this->validate($request, [
            'companyName'=>'required|max:120',
            'email'=>'required|email|unique:suppliers',
            'password'=>'required|min:6|confirmed',
            'companyShortCode' => 'min:3|max:5',
            'contactName' => 'regex:/(^([a-zA-Z\s]+)$)/',
            'contactSurname' => 'regex:/(^([a-zA-Z\s]+)$)/',
            'g-recaptcha-response' => 'required'
        ], [
            'regex' => 'This field format is invalid'
        ]);

        $supplier = new Supplier();
        $supplier->companyName = $request->get('companyName');
        $supplier->companyShortCode = $request->get('companyShortCode');
        $supplier->contactName = $request->get('contactName');
        $supplier->contactSurname = $request->get('contactSurname');
        $supplier->email = $request->get('email');
        $supplier->country = $request->get('location');
        $supplier->city = $request->get('cities');
        if (strpos($request->get('countryCode'), '+') !== false) {
            $supplier->countryCode = $request->get('countryCode');
        } else {
            $supplier->countryCode = '+'.$request->get('countryCode');
        }
        $supplier->phoneNumber = $request->get('phoneNumber');
        $supplier->website = $request->get('website');
        $supplier->password = Hash::make($request->get('password'));
        if ($request->has('comission')) {
            $supplier->comission = $request->get('comission');

            if($request->has('commissioner_commission')){

               $supplier->commissioner_commission = ($request->commissioner_commission > $request->comission) ? $request->comission : $request->commissioner_commission;

            }

        }
        $supplier->mailHash = md5($request->get('email'));
        if (auth()->guard('admin')->check()) {
            $supplier->isRestaurant = !is_null($request->get('isRestaurant')) ? $request->get('isRestaurant') : 0;
        } elseif (auth()->guard('supplier')->check()) {
            $supplier->isRestaurant = 1;
        }

         if($request->get('isAsMobileUser')){
           $supplier->roles = '["Supplier","Supplier_Mobile"]';
         }else{
            $supplier->roles = '["Supplier"]';
         }

         $supplier->permissions = $request->permissions ? json_encode($request->permissions) : "[]";

        if ($supplier->save()) {
            event(new StatusLiked($supplier->companyName.' has been registered as standard user !', $supplier, 'SUPPLIER_REGISTER'));
            $config = new Config();
            $config->userID = $supplier->id;
            $config->currencyID = 2; // Euro as default
            $config->languageID = 1; // English as default
            $config->save();
        }

        if (Auth::guard('admin')->check() || Auth::guard('supplier')->check()) {
            return redirect('/supplier')->with(['success' => 'supplier created successfully!']);
        } else {
            return view('frontend.become-a-supplier-status');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (auth()->guard('admin')->check() || (auth()->guard('supplier')->check() && auth()->guard('supplier')->user()->id == $id)) {
            $country = Country::has('cities')->get();
            $supplier = Supplier::findOrFail($id);
            $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'supplier')->first();

            return view('panel.suppliers.edit', ['supplier' => $supplier, 'country' => $country, 'payment' => $payment]);
        }

        abort(404);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {



     if (!(auth()->guard('admin')->check() || (auth()->guard('supplier')->check() && auth()->guard('supplier')->user()->id == $id)))
        return redirect()->back()->with(['error' => 'You cant change another suppliers information']);

        $supplier = Supplier::findOrFail($id);

        if ($request->get('email') != $supplier->email) {
            $this->validate($request, [
                'email' => 'required|email|unique:suppliers',
            ]);
            $supplier->mailHash = md5($request->get('email'));
        }

        $supplier->companyName = $request->get('companyName');
        $supplier->companyShortCode = $request->get('companyShortCode');
        $supplier->contactName = $request->get('contactName');
        $supplier->contactSurname = $request->get('contactSurname');
        $supplier->email = $request->get('email');
        if (strpos($request->get('countryCode'), '+') !== false) {
            $supplier->countryCode = $request->get('countryCode');
        } else {
            $supplier->countryCode = '+'.$request->get('countryCode');
        }
        $supplier->phoneNumber = $request->get('phoneNumber');
        $supplier->website = $request->get('website');
        $supplier->country = $request->get('location');
        $supplier->city = $request->get('cities');
        if (!is_null($request->get('password')) && !is_null($request->get('password_confirmation'))) {
            $supplier->password = Hash::make($request->get('password'));
        }
        if (!is_null($request->get('comission'))) {
            $supplier->comission = str_replace(',', '.', $request->get('comission'));

              if($request->has('commissioner_commission')){

               $supplier->commissioner_commission = ($request->commissioner_commission > $request->comission) ? $request->comission : $request->commissioner_commission;
               $supplier->commissioner_commission = str_replace(',', '.', $supplier->commissioner_commission);

               if(empty($supplier->commissioner_commission)){
                $supplier->commissioner_commission = null;
               }

            }
        }
        $supplier->isRestaurant = !is_null($request->get('isRestaurant')) ? $request->get('isRestaurant') : 0;
        if($request->get('isAsMobileUser')){
            $roles = json_decode($supplier->roles, true);
            if(!in_array('Supplier_Mobile', $roles)){
                array_push($roles, 'Supplier_Mobile');
            }

        }else{

             $roles = json_decode($supplier->roles, true);
            if(in_array('Supplier_Mobile', $roles)){
               if (($key = array_search('Supplier_Mobile', $roles)) !== false) {
                    unset($roles[$key]);
                }
            }

        }

        $supplier->permissions = $request->permissions ? json_encode($request->permissions) : "[]";
        $supplier->roles = json_encode($roles);
        $supplier->save();

        return redirect('/supplier/'.$id.'/edit')->with(['success' => 'changes has been done successfully!']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePaymentDetails(Request $request, $id)
    {

     if (!(auth()->guard('admin')->check() || auth()->user()->id == $id))
        return redirect()->back()->with(['error' => 'You cannot update another users payment details!']);


        $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'supplier')->first();
        if (is_null($payment)) {
            $payment = new PaymentDetail();
        }

        $payment->companyID = auth()->user()->id;
        $payment->companyType = 'supplier';
        $payment->bankName = $request->bankName;
        $payment->bankBranch = $request->bankBranch;
        $payment->city = $request->city;
        $payment->district = $request->district;
        $payment->postalCode = $request->postalCode;
        $payment->swift = $request->swift;
        $payment->iban = $request->iban;
        $payment->save();

        return redirect('/supplier/'.$id.'/edit');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationEmail(Request $request)
    {
        $supplierID = $request->supplierID;

        $supplier = Supplier::findOrFail($supplierID);
        $supplier->verificationCode = $this->commonFunctions->generateRandomString(8);
        if ($supplier->save()) {
            $mail = new Mails();
            $mail->to = $supplier->email;
            $mail->data = json_encode([
                [
                    'subject' => 'Cityzore - Verification Code!',
                    'verificationCode' => $supplier->verificationCode,
                    'sendToCC' => false,
                ]
            ]);
            $mail->blade = 'mail.verification-code-for-payment-details';
            $mail->status = 0;
            $mail->save();

            return response()->json(['success']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitVerificationEmail(Request $request)
    {
        $supplierID = $request->supplierID;

        $supplier = Supplier::findOrFail($supplierID);
        if ($request->verificationCode == $supplier->verificationCode) {
            return response()->json(['success' => __('verificationEmailSuccess')]);
        }

        return response()->json(['error' => __('verificationEmailError')]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        if(!(auth()->guard('admin')->check() || auth()->user()->id == $id))
         return redirect()->back()->with(['error' => 'You cannot delete this supplier']);
        //Find a user with a given id and delete
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        $config = Config::where('userID', $supplier->id)->first();
        $config->delete();
        return redirect('/supplier');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        $supplier->isActive = $request->isActive;
        $supplier->save();

        return response()->json(['success'=>__('changeStatusSuccess')]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getOptions(Request $request)
    {
        $userID = auth()->guard('admin')->check() ? -1 : auth()->user()->id;
        if (Auth::guard('admin')->check()) {
            $options = Option::all();
        } elseif (Auth::guard('supplier')->check()) {
            $options = Option::where('supplierID', '=', Auth::guard('supplier')->user()->id)->get();
        }

        return ['options' => $options, 'userID' => $userID];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOption(Request $request)
    {
        $optionID = $request->optionID;
        $supplierID = $request->supplierID;
        $option = Option::findOrFail($optionID);
        $rCode = explode(',', $option->rCodeID);

        if (in_array($supplierID, $rCode)) {

            unset($rCode[array_search($supplierID, $rCode)]);

            if (!$rCode) {

                $rCode = null;

            }else{

                $rCode = implode(',', array_values($rCode));

            }

            $option->rCodeID = $rCode;
        }

        if ($option->save()) {
            return response()->json(['success' => __("optionRemoveFromSupplierSuccess")]);
        }

        return response()->json(['error' => __('optionRemoveFromSupplierError')]);
    }

    /**
     * @param Request $request
     */
    public function selectOptionsForRCode(Request $request)
    {
        $options = $request->options;
        $suppilerID = $request->supplierID;

        for ($i=0;$i<count($options);$i++) {

            $option = Option::findOrFail($options[$i]);

            if (! is_null($option->rCodeID)) {

                $rCode = explode(',', $option->rCodeID);

                if (! in_array($suppilerID, $rCode)) {

                    array_push($rCode, $suppilerID);

                    $rCodeID = ltrim(implode(',' , $rCode), ',');

                }else{

                    $rCodeID = $option->rCodeID;

                }

            }else{

                $rCodeID = $suppilerID;

            }

            $option->rCodeID = $rCodeID;

            $option->save();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details(Request $request, $id)
    {
       if(!(auth()->guard('admin')->check() || auth()->user()->id == $id))
        return redirect()->back()->with(['error' => 'You cannot view that users details']);

        $supplier = Supplier::findOrFail($id);
        $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'supplier')->first();

        return view('panel.suppliers.details', ['supplier' => $supplier, 'payment' => $payment]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeLicenseFiles(Request $request, $id)
    {
        $files = $request->file('fileName');
        $title = $request->get('title');

        if (!empty($files[0])) {
            foreach ($files as $ind => $file) {
                if($file->getMimeType() != "application/pdf"){
                return redirect()->back()->with(['error' => 'You can upload only pdf files!']);
                }
                $license = new LicenseFile();
                $license->companyID = $id;
                $license->companyType = 'Supplier';
                $license->title = $title[$ind];
                $license->fileName = Str::slug(Supplier::findOrFail($id)->companyName,'.').'-'.Str::slug($license->title.'.'.$file->getClientOriginalExtension(), '.');
                if ($license->save()) {
                    $file->move(public_path('storage/license-files/'), Str::slug(Supplier::findOrFail($id)->companyName,'.').'-'.Str::slug($license->title.'.'.$file->getClientOriginalExtension(), '.'));
                }
            }
        }

        return redirect('/supplier/'.Auth::guard('supplier')->user()->id.'/details');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function deleteLicense(Request $request)
    {
        $license = LicenseFile::findOrFail($request->id);
        if ($license->delete()) {
            Storage::delete('license-files/'.$license->fileName);
            return [
                'success' => __('deleteLicenseFileSuccess')
            ];
        } else {
            return [
                'error' => __('deleteLicenseFileError')
            ];
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detailsForAdmin(Request $request, $id)
    {
        if(!auth()->guard('admin')->check())
         return redirect()->back();

        $license = LicenseFile::where('companyID', $id)->where('companyType', 'commissioner')->first();
        $supplier = Supplier::findOrFail($id);
        $payment = PaymentDetail::where('companyID', $id)->where('companyType', 'supplier')->first();

        return view('panel.suppliers.details-for-admin', ['license' => $license, 'supplier' => $supplier, 'payment' => $payment]);
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
            $mail->to = Supplier::findOrFail($license->companyID)->email;
            $mail->status = 0;
            if ($license->confirm == 1) {
                array_push($data, ['subject' => __('changeLicenseStatusSuccess'), 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
                $mail->blade = 'mail.confirm-license';
            } else {
                array_push($data, ['subject' => __('changeLicenseStatusError'), 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
                $mail->blade = 'mail.declined-license';
            }
            $mail->data = json_encode($data);

            $mail->save();
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
        array_push($data, ['subject' => __("editSuggestForYourLicense"), 'fileTitle' => $license->title, 'fileName' => $license->fileName]);
        $mail = new Mails();
        $mail->to = Supplier::findOrFail($companyID)->email;
        $mail->data = json_encode($data);
        $mail->blade = 'mail.edit-suggest-licenses';
        $mail->status = 0;
        $mail->save();
    }

}
