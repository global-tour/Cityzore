<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\TicketType;
use App\Voucher;
use App\Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\RefCodeGenerator;

class VoucherController extends Controller
{
    public $refCodeGenerator;

       public function __construct()
    {
        $this->refCodeGenerator = new RefCodeGenerator();
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::guard('supplier')->check()) {
            $vouchers = Voucher::where('companyID', '=', Auth::guard('supplier')->user()->id)->get();
        }

        if (Auth::guard('admin')->check()) {
            $vouchers = Voucher::where('companyID', '=', '-1')->get();
        }
        return view('panel.voucher.index', ['vouchers' => $vouchers]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('supplier')->check()) {
            $products = Product::where('supplierID', '=', Auth::guard('supplier')->user()->id)->where('isDraft', '=', 0)->get();
        }

        if (Auth::guard('admin')->check()) {
            $products = Product::where('isDraft', '=', 0)->get();
        }
        $options = Option::all();
        $ticketTypes = TicketType::all();
        return view('panel.voucher.create', ['products' => $products, 'options' => $options, 'ticketTypes' => $ticketTypes]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $voucher = new Voucher();
        $dateTimeArr = [];
        $dateTime = [
            'date' => $request->bookingDate,
            'time' => $request->bookingTime
        ];
        array_push($dateTimeArr, $dateTime);

        $participantsArr = [];
        $participants = [
          'adult' => $request->adultCount,
          'youth' => $request->youthCount,
          'child' => $request->childCount,
          'infant' => $request->infantCount,
          'euCitizen' => $request->euCitizenCount,
        ];
        array_push($participantsArr, $participants);

        $voucher->dateTime = json_encode($dateTime);
        $voucher->participants = json_encode($participantsArr);
        $voucher->traveler = $request->travelerName;
        $voucher->bookingRefCode = $request->bookingRefCode;
        $voucher->productID = $request->product;
        $voucher->optionID = $request->option;
        $voucher->ticketTypes = json_encode($request->get('ticketTypes'));
        if (Auth::guard('admin')->check()) {
            $voucher->companyID = -1;
        } else {
            $voucher->companyID = auth()->user()->id;
        }

        if ($request->bookingRefCode == null) {
        
            $voucher->bookingRefCode = $this->refCodeGenerator->refCodeGeneratorForBooking(null);
        }

        $voucher->save();

        return redirect('/vouchers');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getOptions(Request $request)
    {
        $product = Product::findOrFail($request->productId);
        $options = $product->options()->get();
        return ['options' => $options];
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);

       if(auth()->guard('supplier')->check()){
        if($voucher->companyID != auth()->guard('supplier')->user()->id)
            return redirect()->back()->with(['error' => 'You cannot view and edit this voucher']);
       }

        $products = Product::all();
        return view('panel.voucher.edit',
            [
                'voucher'=>$voucher,
                'products' => $products
            ]
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

       if(auth()->guard('supplier')->check()){
        if($voucher->companyID != auth()->guard('supplier')->user()->id)
            return redirect()->back()->with(['error' => 'You cannot update this voucher']);
       }

        $dateTimeArr = [];
        $dateTime = [
            'date' => $request->bookingDate,
            'time' => $request->bookingTime
        ];
        array_push($dateTimeArr, $dateTime);

        $participantsArr = [];
        $participants = [
            'adult' => $request->adultCount,
            'youth' => $request->youthCount,
            'child' => $request->childCount,
            'infant' => $request->infantCount,
            'euCitizen' => $request->euCitizenCount,
        ];
        array_push($participantsArr, $participants);

        $voucher->productID = $request->product;
        $voucher->optionID = $request->option;
        $voucher->traveler = $request->travelerName;
        $voucher->bookingRefCode = $request->bookingRefCode;
        $voucher->participants = json_encode($participantsArr);
        $voucher->dateTime = json_encode($dateTime, true);

        $voucher->save();
        return redirect('/vouchers');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return redirect('/vouchers');
    }

}
