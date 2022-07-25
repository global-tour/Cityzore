<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\MailOperations;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\ExternalPayment;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Mails;
use App\Config;
use Illuminate\Support\Facades\Auth;
use App\Booking;

class PaymentController extends Controller
{

    public $commonFunctions;
    public $refCodeGenerator;
    public $mailOperations;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->mailOperations = new MailOperations();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function externalPayment()
    {
        $userID = -1;
        if (auth()->guard('supplier')->check()) {
            $userID = auth()->user()->id;
            $paymentLinks = ExternalPayment::where('createdBy', '=', $userID)->orderBy('created_at', 'desc')->get()->paginate(15);
        }
        if (Auth::guard('admin')->check()) {
            $paymentLinks = ExternalPayment::orderBy('created_at', 'desc')->get()->paginate(15);
        }
        $config = Config::where('userID', $userID)->first();

        $bookingsToBeSelected = [];
        $now = Carbon::now();
        $bookings = Booking::where('status', '=', 0)->whereMonth('created_at', '=', $now->month)->get(['bookingRefCode', 'gygBookingReference', 'isBokun', 'isViator']);
        foreach($bookings as $booking) {
            if($booking->gygBookingReference)
                array_push($bookingsToBeSelected, $booking->gygBookingReference);
            elseif($booking->isBokun == 1 || $booking->isViator == 1)
                array_push($bookingsToBeSelected, $booking->bookingRefCode);
            else {
                $bookingRefCode = explode('-', $booking->bookingRefCode);
                array_push($bookingsToBeSelected, $bookingRefCode[count($bookingRefCode)-1]);
            }
        }

        return view('panel.external-payment.index',
            [
                'paymentLinks' => $paymentLinks,
                'config' => $config,
                'bookingsToBeSelected' => $bookingsToBeSelected,
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function externalPaymentCreate()
    {
        $bookingsToBeSelected = [];
        $bookings = Booking::whereDate('dateForSort', '>=', \Carbon\Carbon::today())->get(['bookingRefCode', 'gygBookingReference', 'isBokun', 'isViator']);
        foreach($bookings as $booking) {
            if($booking->gygBookingReference)
                array_push($bookingsToBeSelected, $booking->gygBookingReference);
            elseif($booking->isBokun == 1 || $booking->isViator == 1)
                array_push($bookingsToBeSelected, $booking->bookingRefCode);
            else {
                $bookingRefCode = explode('-', $booking->bookingRefCode);
                array_push($bookingsToBeSelected, $bookingRefCode[count($bookingRefCode)-1]);
            }
        }

        return view('panel.external-payment.create', compact('bookingsToBeSelected'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePaymentLink(Request $request)
    {
        $externalPayment = new ExternalPayment();
        $externalPayment->is_paid = 0;
        $externalPayment->email = $request->email;
        $externalPayment->price = $request->price;
        $externalPayment->message = $request->message;
        $randomString = $this->commonFunctions->generateRandomString('24');
        $paymentLink = env('APP_URL', 'https://cityzore.com'). '/payment-link/' . $randomString;
        $externalPayment->payment_link = $paymentLink;
        $externalPayment->referenceCode = $this->refCodeGenerator->refCodeGeneratorForExtPayment();
        $externalPayment->currency = (int)$request->currency;
        $createdBy = -1;
        if (auth()->guard('supplier')->check()) {
            $createdBy = auth()->user()->id;
        }
        $externalPayment->createdBy = $createdBy;
        $externalPayment->creatorEmail = auth()->user()->email;
        if($request->bookingRefCode)
            $externalPayment->bookingRefCode = $request->bookingRefCode;
        if ($externalPayment->save()) {

            $data = [];
            array_push($data,
                [
                    'subject' => 'Cityzore - Your Payment Link!',
                    'message' => $request->message,
                    'link' => $paymentLink,
                    'sendToCC' => false,
                ]
            );
            $mail = new Mails();
            $mail->to = $request->email;
            $mail->data = json_encode($data);
            $mail->blade = 'mail.external-payment';
            $mail->save();
        }
        return response()->json(['success' => 'Payment Link is created successfully', 'payment_link' => $paymentLink]);
    }

    /**
     * @param $lang
     * @param $link
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function externalPaymentDetails($lang, $link)
    {
        $payment = ExternalPayment::where('payment_link', 'like', '%' . $link . '%')->first();
        // Payment related parameters
        $clientId = env('PAYMENT_CLIENT_ID', '190217122');
        $amount = $payment->price;
        $okUrl = env('EXT_PAYMENT_OK_URL', 'https://www.cityzore.com/external-payment-successful');
        $failUrl = env('EXT_PAYMENT_FAIL_URL', 'https://www.cityzore.com/external-payment-failed');
        $oid = $this->refCodeGenerator->invoiceGenerator();
        $rnd = microtime();
        $taksit = '';
        $islemtipi = env('PAYMENT_TYPE', 'Auth');
        $storekey = env('PAYMENT_STOREKEY', 'NamyeluS3+-*/');
        $currency = $payment->currency;

        $hashstr = $clientId . $oid . $amount . $okUrl . $failUrl . $islemtipi . $taksit . $rnd . $storekey;
        $hash = base64_encode(pack('H*',sha1($hashstr)));
        return view('frontend.external-payment-details',
            [
                'payment' => $payment,
                'clientId' => $clientId,
                'amount' => $amount,
                'okUrl' => $okUrl,
                'failUrl' => $failUrl,
                'oid' => $oid,
                'rnd' => $rnd,
                'taksit' => $taksit,
                'islemtipi' => $islemtipi,
                'storekey' => $storekey,
                'hashstr' => $hashstr,
                'hash' => $hash,
                'currency' => $currency,
            ]
        );
    }

    /**
     * Resends external payment email whether it's not sent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendEmail(Request $request)
    {
        $paymentLink = ExternalPayment::findOrFail($request->id);
        $this->mailOperations->sendMail(
            [
                'email' => $paymentLink->email,
                'subject' => 'Cityzore - Your Payment Link!',
                'message' => $paymentLink->message,
                'link' => $paymentLink->payment_link,
                'env_mail_username' => env('MAIL_USERNAME', 'contact@cityzore.com'),
                'sendToCC' => false,
            ],
            'mail.external-payment'
        );

        return response()->json(['success' => 'Payment Link E-Mail is successfully sent!']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentLogs()
    {
        return view('panel.paymentlogs.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function externalPaymentInvoice ($id)
    {
        $externalPayment = ExternalPayment::findOrFail($id);
        $data = [
            'externalPayment' => $externalPayment,
        ];
        $pdf = PDF::loadView('pdfs.externalPaymentInvoice', $data);
        return $pdf->stream($externalPayment->referenceCode.'.pdf');
    }

    public function editExternalPayment($id)
    {
        $externalPayment = ExternalPayment::findOrFail($id);

        return view('panel.external-payment.edit', compact( 'externalPayment'));
    }

    public function externalBookingRefCode($id = null, Request $request)
    {

        if ($id) {

            $externalPayment = ExternalPayment::find($id);

            if ($externalPayment->bookingRefCode){
                return response()->json([
                    'item' => $externalPayment->bookingRefCode
                ]);
            }

        }

        $booking['items'] = Booking::where('status', '=', 0)
            ->whereMonth('created_at', '=', now())
            ->where('bookingRefCode', 'like', '%'. $request->q .'%')
            ->orWhere('gygBookingReference', 'like', '%'. $request->q .'%')
            ->get()
            ->take(10)
            ->each(function ($item){
                if ($item->gygBookingReference) {
                    $item->mutatorRefCode =  $item->gygBookingReference;
                }elseif ($item->isViator || $item->isBokun ){
                    $item->mutatorRefCode =  $item->bookingRefCode;
                }else{
                    $bookingRefCode = explode('-', $item->bookingRefCode);
                    $item->mutatorRefCode =  array_last($bookingRefCode);
                }
            });

        return response()->json($booking);
    }

    public function updateExternalPayment($id, Request $request)
    {
        try {
            $externalPayment = ExternalPayment::findOrFail($id);
            $externalPayment->bookingRefCode = $request->bookingRefCode;
            $externalPayment->message = $request->message;
            $externalPayment->price = $request->price;
            $externalPayment->save();

            return redirect(url('/external-payment'));

        } catch (\Exception $exception) {

            return redirect()->back();

        }
    }

    public function externalPaymentEdit(Request $request) {
        try {
            $externalPayment = ExternalPayment::findOrFail($request->get('externalPaymentID'));
            $externalPayment->bookingRefCode = $request->get('bookingRefCodeNewVal');
            $externalPayment->message = $request->get('messageNewVal');
            if($externalPayment->save()) {
                return ["success" => "success"];
            }
        } catch (Exception $e) {
            return ["error" => "An Error Occurred!"];
        }
    }

}
