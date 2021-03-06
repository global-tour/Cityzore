<?php

namespace App\Http\Controllers\Pdfs;

use App\Barcode;
use App\Booking;
use App\Cart;
use App\Currency;
use App\ExternalPayment;
use App\Invoice;
use App\Option;
use App\Platform;
use App\Product;
use App\ProductGallery;
use App\Rcode;
use App\Supplier;
use App\TicketType;
use App\User;
use App\Voucher;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Http\Controllers\Helpers\CryptRelated;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image as Image;



class PdfController extends Controller
{

    public $cryptRelated;

    public function __construct()
    {
        $this->cryptRelated = new CryptRelated();
    }

    /**
     * @param $lang
     * @param $id
     * @return mixed
     * @throws \Picqer\Barcode\Exceptions\BarcodeException
     */
    public function voucher($lang, $id)
    {
        return $this->voucherBackend($id);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Picqer\Barcode\Exceptions\BarcodeException
     */
    public function voucherBackend($id)
    {
        $id = $this->cryptRelated->decrypt($id);
        $booking = Booking::findOrFail($id);
        //starting security
        /*if(!(auth()->guard('admin')->check()) && (int)$booking->companyID !== auth()->user()->id)
           return redirect()->back()->with(['error' => 'You cant print another users voucher']);*/
        //ending security
        $products = Product::all();
        $url = 'cityzore';
        $rCode = null;
        if (!($booking->rCodeID == null)) {
            $rCode = Rcode::select('rCode')->where('id', $booking->rCodeID)->first();
        }
        $bigBus = $this->barcode('3', $booking->id);
        $cruiseBarcode = $this->barcode('4', $booking->id);
        $versaillesBarcode = $this->barcode('5', $booking->id);
        $sainteChapelleBarcode = $this->barcode('20', $booking->id);
        $sainteChapelleConciergerieBarcode = $this->barcode('19', $booking->id);
        $conciergerieBarcode = $this->barcode('17', $booking->id);
        $arcdeTriomphe = $this->barcode('6', $booking->id);
        $grevin = $this->barcode('25', $booking->id);
        $picasso = $this->barcode('29', $booking->id);
        $rodin = $this->barcode('28', $booking->id);
        $pompidou = $this->barcode('27', $booking->id);
        $montparnasse = $this->barcode('30', $booking->id);
        $orsay = $this->barcode('32', $booking->id);
        $orangerie = $this->barcode('33', $booking->id);
        $museedelarme = $this->barcode('34', $booking->id);
        $pantheonBarcode = $this->barcode('18', $booking->id);
        $basiliqueBarcode = $this->barcode('16', $booking->id);
        $operaBarcode = $this->barcode('7', $booking->id);
        $options = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $pricing = $options->pricings()->first();
        $barcodes = Barcode::where('bookingId', $booking->id)->get();
        $ticketTypeArr = [];
        foreach ($barcodes as $barcode) {
            if ($barcode && $barcode->ticketType != 31) {
                array_push($ticketTypeArr, $barcode->ticketType);
            }
        }
        $ticketTypeArr = $this->flatten(array_unique($ticketTypeArr));


        $generator = new BarcodeGeneratorPNG();
        $travelerName = json_decode($booking->travelers, true)[0]['firstName'];
        $travelerLastname = json_decode($booking->travelers, true)[0]['lastName'];
        $participantCountArr = [];
        $participantSum = 0;
        $museedelarmePricing = [];
        foreach (json_decode($booking->bookingItems, true) as $bookingItem) {
            switch ($bookingItem['category']) {
                case 'EU_CITIZEN':
                    $title = 'euCitizen';
                    break;
                default:
                    $title = strtolower($bookingItem['category']);
                    break;
            }

            if (!is_null($museedelarme)) {
                $column = $title.'Price';
                $museedelarmePricing[$title] = json_decode($pricing->$column)[0];
            }

            if (!is_null($cruiseBarcode) || !is_null($versaillesBarcode) || !is_null($sainteChapelleBarcode) || !is_null($sainteChapelleConciergerieBarcode) || !is_null($conciergerieBarcode) || !is_null($arcdeTriomphe) || !is_null($grevin) || !is_null($picasso) || !is_null($operaBarcode) || !is_null($pantheonBarcode || !is_null($basiliqueBarcode) || !is_null($rodin) || !is_null($montparnasse) || !is_null($orsay) || !is_null($orangerie)|| !is_null($museedelarme))) {

                if (!is_null($pricing->ignoredCategories)) {
                    if (!in_array($title, json_decode($pricing->ignoredCategories, true))) {
                        array_push($participantCountArr, $bookingItem['count']);
                        $participantSum = array_sum($participantCountArr);
                    }
                } else {
                    array_push($participantCountArr, $bookingItem['count']);
                    $participantSum = array_sum($participantCountArr);
                }
            } else {
                array_push($participantCountArr, $bookingItem['count']);
                $participantSum = array_sum($participantCountArr);
            }
        }

        if ($booking->gygBookingReference == null && $booking->isBokun == 0 && $booking->isViator == 0) {
            $bknNumber = explode("-", $booking->bookingRefCode)[3];
            $productRefCode = explode('-', $booking->bookingRefCode)[0];
            foreach ($products as $product) {
                if ($product->referenceCode == $productRefCode) {
                    $productImage = ProductGallery::where('id', '=', $product->coverPhoto)->pluck('src');
                    $productImage = $this->reducedImage($productImage);
                    if($product->id === 128){
                        $product->title = count(explode("with", $product->title)) ? explode("with", $product->title)[0] : $product->title;
                        $options->title = count(explode("with", $options->title)) ? explode("with", $options->title)[0] : $options->title;
                    }
                    $data = [
                        'bkn' => $bknNumber,
                        'travelerName' => $travelerName,
                        'travelerLastname' => $travelerLastname,
                        'product' => $product,
                        'options' => $options,
                        'booking' => $booking,
                        'productImage' => $productImage,
                        'ticketTypeArr' => $ticketTypeArr,
                        'participantSum' => $participantSum,
                        'cruiseBarcode' => $cruiseBarcode,
                        'operaBarcode' => $operaBarcode,
                        'versaillesBarcode' => $versaillesBarcode,
                        'sainteChapelleBarcode' => $sainteChapelleBarcode,
                        'sainteChapelleConciergerieBarcode' => $sainteChapelleConciergerieBarcode,
                        'conciergerieBarcode' => $conciergerieBarcode,
                        'arcdeTriomphe' => $arcdeTriomphe,
                        'grevin' => $grevin,
                        'picasso' => $picasso,
                        'rodin' => $rodin,
                        'montparnasse' => $montparnasse,
                        'orsay' => $orsay,
                        'pompidou' => $pompidou,
                        'orangerie' => $orangerie,
                        'museedelarme' => $museedelarme,
                        'pantheonBarcode' => $pantheonBarcode,
                        'basiliqueBarcode' => $basiliqueBarcode,
                        'bigBus' => $bigBus,
                        'generator' => $generator,
                        'url' => $url,
                        'rCode' => $rCode,
                        'museedelarmePricing' => $museedelarmePricing
                    ];
                }
            }
        } else {
            if ($booking->isBokun == 1 || $booking->isViator == 1) {
                $bknNumber = $booking->bookingRefCode;
            } else {
                $bknNumber = explode("-", $booking->bookingRefCode)[2];
            }
            $data = [
                'bkn' => $bknNumber,
                'travelerName' => $travelerName,
                'travelerLastname' => $travelerLastname,
                'options' => $options,
                'booking' => $booking,
                'ticketTypeArr' => $ticketTypeArr,
                'participantSum' => $participantSum,
                'operaBarcode' => $operaBarcode,
                'cruiseBarcode' => $cruiseBarcode,
                'versaillesBarcode' => $versaillesBarcode,
                'arcdeTriomphe' => $arcdeTriomphe,
                'grevin' => $grevin,
                'picasso' => $picasso,
                'rodin' => $rodin,
                'montparnasse' => $montparnasse,
                'orsay' => $orsay,
                'orangerie' => $orangerie,
                'museedelarme' => $museedelarme,
                'bigBus' => $bigBus,
                'generator' => $generator,
                'url' => $url,
                'rCode' => $rCode,
                'pompidou' => $pompidou,
                'museedelarmePricing' => $museedelarmePricing,
                'productImage' => asset('reducedimages/eiffel-tower-5ab.jpg'),
            ];
        }
        $pdf = PDF::loadView('pdfs.voucher', $data);
        return $pdf->stream($travelerName . $travelerLastname . $bknNumber . '.pdf');
    }

    /**
     * @param $ticketType
     * @param $bookingID
     * @return mixed
     */
    public function barcode($ticketType, $bookingID)
    {
        if ($ticketType == 30) {
            $barcode = Barcode::where(function ($q) {
                $q->where('ticketType', '=', 30)->orWhere('ticketType', '=', 31);
            })->where('bookingID', '=', $bookingID)->get();
        } else {
            $barcode = Barcode::where('ticketType', '=', $ticketType)->where('bookingID', '=', $bookingID)->get();
        }
        return $barcode;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function sendBookingID(Request $request)
    {
        $id = $request->id;
        return ['id' => $id];
    }

    /**
     * @param $lang
     * @param $token
     * @return mixed
     * @throws \Picqer\Barcode\Exceptions\BarcodeException
     */
    public function printVoucherByToken($lang, $token)
    {
        $decrypted = Crypt::decryptString($token);
        $booking = Booking::where('id', $decrypted)->first();
        $enc = $this->cryptRelated->encrypt($booking->id);
        return $this->voucher($lang, $enc);
    }

    /**
     * @param $lang
     * @param $id
     * @return mixed
     */
    public function invoice($lang, $id)
    {
        return $this->invoiceBackend($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function invoiceBackend($id)
    {
        $id = $this->cryptRelated->decrypt($id);
        $booking = Booking::findOrFail($id);
        $url = 'cityzore';
        $currencySymbols = ['1' => '&#x24;', '2' => '&euro;', '3' => '&#163;', '4' => '&#8378;'];
        $currencySymbol = $currencySymbols[$booking->currencyID];
        $products = Product::all();
        $options = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $invoice = Invoice::where('id', '=', $booking->invoiceID)->first();
        if ($booking->gygBookingReference == null) {
            $productRefCode = explode('-', $booking->bookingRefCode)[0];
            $data = [
                'booking' => $booking,
                'invoice' => $invoice,
                'products' => $products,
                'options' => $options,
                'productRefCode' => $productRefCode,
                'currencySymbol' => $currencySymbol,
                'url' => $url
            ];
        } else {
            $data = [
                'booking' => $booking,
                'invoice' => $invoice,
                'products' => $products,
                'options' => $options,
                'currencySymbol' => $currencySymbol,
                'url' => $url
            ];
        }
        $pdf = PDF::loadView('pdfs.invoice', $data);
        return $pdf->stream($invoice->referenceCode . '.pdf');
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Picqer\Barcode\Exceptions\BarcodeException
     */
    public function multipleTickets(Request $request)
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        $barcodes = Barcode::where('ticketType', '=', $request->ticketTypeSelect)
            ->where('isUsed', '=', '0')
            ->where('isExpired', '=', 0)
            ->orderBy('id', 'asc')
            ->where('ownerID', $ownerID)->get()->take($request->barcodeCount)->each(function($item) use ($request){
                $item->isUsed = 1;
                $item->description = $request->barcodeDescription;
                $item->booking_date = $request->bookingDate;
                $item->save();
            });

        if (!$barcodes) {
            return redirect()->back()->with('status', 'Barcode not found');
        }

        $pdf = PDF::loadView('pdfs.multiple-tickets', ['cruiseBarcodeArray' => $barcodes]);

        return $pdf->download($barcodes[0]['searchableTicketType'] . '.pdf');
    }

    public function multipleTicketsOnIndex(Request $request)
    {
        $barcodeDescription = $request->get('barcodeDescription');
        $bookingDate = $request->get('bookingDate');
        $generator = new BarcodeGeneratorPNG();

        $barcodes = Barcode::where('description', $barcodeDescription)->where('booking_date', $bookingDate)->get();
        $barcodeArray = [];
        foreach ($barcodes as $barcode) {
            $endTimeString = explode('/', ($barcode->endTime))[2] . '-' . explode('/', ($barcode->endTime))[1] . '-' . explode('/', ($barcode->endTime))[0];
            $barcode->endTime = $endTimeString;
            array_push($barcodeArray, $barcode);
        }
        usort($barcodeArray, function ($a, $b) {
            return $a['endTime'] <=> $b['endTime'];
        });

        for ($i = 0; $i < count($barcodeArray); $i++) {
            $barcodeArray[$i]['endTime'] = explode('-', $barcodeArray[$i]['endTime'])[2] . '/' . explode('-', $barcodeArray[$i]['endTime'])[1] . '/' . explode('-', $barcodeArray[$i]['endTime'])[0];
        }

        $data = array('barcodeCount' => count($barcodeArray), 'ticketTypeSelect' => $barcodeArray[0]->ticketType);
        $data2 = [
            'generator' => $generator,
            'cruiseBarcodeArray' => $barcodeArray,
        ];
        $pdf = PDF::loadView('pdfs.multiple-tickets', $data, $data2);
        return $pdf->download($barcodeArray[0]->searchableTicketType . '.pdf');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsableBarcodeCount(Request $request)
    {

        $ticketTypeName = TicketType::findOrFail($request->ticketType)->name;
        $ticketType = TicketType::where('name', $ticketTypeName)->get();
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }

        $usableBarcodeCount = 0;
        if (count($ticketType) > 0) {
            $usableBarcodeCount = Barcode::where('isUsed', 0)->where('isReserved', 0)->where('ownerID', $ownerID)->where('ticketType', $request->ticketType)->count();
        }

        return response()->json(['usableBarcodeCount' => $usableBarcodeCount, 'ticketType' => $ticketType]);
    }

    /**
     * @param $lang
     * @param $id
     * @param $totalCommission
     * @return mixed
     */
    public function commissionerInvoice($lang, $id, $totalCommission)
    {

        $booking = Booking::findOrFail($id);
        $products = Product::all();
        $currencySymbols = ['1' => '&#x24;', '2' => '&euro;', '3' => '&#163;', '4' => '&#8378;'];
        $currencySymbol = $currencySymbols[$booking->currencyID];
        $cartCommission = Cart::where('referenceCode', '=', $booking->reservationRefCode)->first()->totalCommission;
        $options = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $invoice = Invoice::where('id', '=', $booking->invoiceID)->first();
        if ($booking->gygBookingReference == null) {
            $productRefCode = explode('-', $booking->bookingRefCode)[0];
            $data = [
                'booking' => $booking,
                'invoice' => $invoice,
                'products' => $products,
                'options' => $options,
                'productRefCode' => $productRefCode,
                'totalCommission' => $totalCommission,
                'cartCommission' => $cartCommission,
                'currencySymbol' => $currencySymbol
            ];
        } else {
            $data = [
                'booking' => $booking,
                'invoice' => $invoice,
                'products' => $products,
                'options' => $options,
                'currencySymbol' => $currencySymbol
            ];
        }
        $pdf = PDF::loadView('pdfs.invoice', $data);
        return $pdf->stream($invoice->referenceCode . '.pdf');
    }


    public function commissionerInvoiceByMonth($dateParam)
    {
        $first_of_month = Carbon::parse($dateParam . "-01");
        $end_of_month = $first_of_month->copy()->endOfMonth();


        $bookings = Booking::where('status', 0)->where(function ($q) {
            $q->where('userID', auth()->guard('web')->user()->id);
            $q->orWhere('affiliateID', auth()->guard('web')->user()->id);

        })->where('dateForSort', '>=', $first_of_month)->where('dateForSort', '<=', $end_of_month)->get();


        //dd($bookings);
        $data = [];
        foreach ($bookings as $booking) {


            $products = Product::all();
            $currencySymbols = ['1' => '&#x24;', '2' => '&euro;', '3' => '&#163;', '4' => '&#8378;'];
            $currencySymbol = $currencySymbols[$booking->currencyID];
            $cartCommission = Cart::where('referenceCode', '=', $booking->reservationRefCode)->first()->totalCommission;
            $options = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
            $invoice = Invoice::where('id', '=', $booking->invoiceID)->first();
            if ($booking->gygBookingReference == null) {
                $productRefCode = explode('-', $booking->bookingRefCode)[0];
                $data[] = [
                    'booking' => $booking,
                    'invoice' => $invoice,
                    'products' => $products,
                    'options' => $options,
                    'productRefCode' => $productRefCode,
                    //'totalCommission' => $totalCommission,
                    'cartCommission' => $cartCommission,
                    'currencySymbol' => $currencySymbol
                ];
            } else {
                $data[] = [
                    'booking' => $booking,
                    'invoice' => $invoice,
                    'products' => $products,
                    'options' => $options,
                    'currencySymbol' => $currencySymbol
                ];
            }


        }


        $pdf = PDF::loadView('pdfs.invoice_by_month', ['datas' => $data]);
        return $pdf->stream(time() . '.pdf');

    }

    /**
     * @param $month
     * @param $year
     * @param $totalRate
     * @param $companyID
     * @param null $commissionerRequest
     * @return mixed
     */
    public function financeInvoice($month, $year, $totalRate, $companyID, $isPlatform, $commissionerRequest = null )
    {
        $extraPayment = [];
        if ($commissionerRequest == null || $commissionerRequest == 0) {
            if ($isPlatform) {
                $company = Platform::find($companyID)->name;
                $bookings = Booking::where('gygBookingReference', '=', null)->where('platformID', $companyID)->get();
                $extraPayment = ExternalPayment::where('is_paid', 1)->where('createdBy', $companyID)
                    ->whereYear('updated_at', '=', $year)->whereMonth('updated_at', '=', $month)
                    ->get();
                $commissionRate = 0;
            } else {
                if ($companyID == '-1') {
                    $company = 'Paris Business and Travel';
                    $bookings = Booking::where('gygBookingReference', '=', null)->where('status', '=', 0)->get();
                    $extraPayment = ExternalPayment::where('is_paid', 1)->where('createdBy', $companyID)
                        ->whereYear('updated_at', '=', $year)->whereMonth('updated_at', '=', $month)
                        ->get();
                    $commissionRate = 0;
                }
                if (!($companyID == '-1') && !($companyID == null)) {

                    $company = Supplier::where('id', '=', $companyID)->first()->companyName;
                    $bookings = Booking::where('gygBookingReference', '=', null)->where('isBokun', '=', '0')->where('isViator', 0)->where('companyID', '=', $companyID)->where('status', '=', 0)->get();
                    $extraPayment = ExternalPayment::where('is_paid', 1)->where('createdBy', $companyID)
                        ->whereYear('updated_at', $year)->whereMonth('updated_at', $month)
                        ->get();
                    $commissionRate = \App\Supplier::where('id', '=', $companyID)->first()->comission;
                }
            }
        }
        if ($companyID == 0) {
            $company = User::where('id', '=', $commissionerRequest)->first()->name;
            $bookings = Booking::where('gygBookingReference', '=', null)->where(function ($q) use ($commissionerRequest) {

                $q->where('userID', '=', $commissionerRequest);
                $q->orWhere('affiliateID', '=', $commissionerRequest);
            })->where('status', '=', 0)->get();
            $commissionRate = \App\User::where('id', '=', $commissionerRequest)->first()->commission;
        }

        $financeBookings = [];
        foreach ($bookings as $booking) {
            $bookingDate = explode('/', $booking->date);
            $bookingMonth = $bookingDate[1];
            $bookingYear = $bookingDate[2];
            if (($bookingMonth == $month) && ($bookingYear == $year)) {
                $cart = Cart::where('referenceCode', $booking->reservationRefCode)->first();
                $booking->cart = $cart;
                $arr = ['booking' => $booking];
                array_push($financeBookings, $arr);
            }
        }

        $currency = Currency::where('isActive', true)->get();

        $data = [
            'month' => $month,
            'year' => $year,
            'totalRate' => $totalRate,
            'companyID' => $companyID,
            'company' => $company,
            'bookings' => $bookings,
            'extraPayment' => $extraPayment,
            'financeBookings' => $financeBookings,
            'commissionRate' => $commissionRate,
            'currency' => $currency,
            'commissionerRequest' => $commissionerRequest
        ];
        $pdf = PDF::loadView('pdfs.finance-voucher', $data);
        return $pdf->stream($month . $year . '.pdf');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function panelVoucher($id)
    {
        $voucher = Voucher::findOrFail($id);
        $dateV = json_decode($voucher->dateTime, true)['date'];
        $dateV = DateTime::createFromFormat('d/m/Y', $dateV);
        $dateV = $dateV->format('D, d-F-y');
        $product = Product::where('id', '=', $voucher->productID)->first();
        $productImage = ProductGallery::where('id', '=', $product->coverPhoto)->first();
        $options = Option::where('id', '=', $voucher->optionID)->first();
        $data = [
            'voucher' => $voucher,
            'dateV' => $dateV,
            'product' => $product,
            'productImage' => $productImage,
            'options' => $options,
        ];
        $pdf = PDF::loadView('pdfs.panel-voucher', $data);
        return $pdf->stream($voucher->bookingRefCode . '.pdf');
    }


    private function flatten(array $array)

    {
        $return = [];
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    private function reducedImage($images){
        if(!empty($images)){
            if(file_exists(public_path('reducedimages/'.$images[0]))){
               return asset('reducedimages/'.$images[0]);
            }
            $img = Image::make(Storage::disk('s3')->url('product-images/' . $images[0]));
            $img->resize(350, 350);
            $fileName = $images[0];
            $img->save(public_path('reducedimages/'.$fileName));
            $url = asset('reducedimages/'.$fileName);
            return $url;
        }
        return asset('reducedimages/eiffel-tower-5ab.jpg');
    }


}
