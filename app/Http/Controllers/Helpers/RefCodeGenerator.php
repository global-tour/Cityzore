<?php

namespace App\Http\Controllers\Helpers;

use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Cart;
use App\Invoice;

class RefCodeGenerator extends Controller
{

    /**
     * @return string
     */
    public function refCodeGenerator()
    {
        if (Auth::guard('supplier')->check()) {
            return Auth::guard('supplier')->user()->companyShortCode.rand(0, 99999999);
        } else {
            return "PBT".rand(0, 99999999);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function refCodeGeneratorForTicket(Request $request)
    {
        if (Auth::guard('supplier')->check()) {
            $companyShortCode = Auth::guard('supplier')->user()->companyShortCode;
        } else {
            $companyShortCode = "PBT";
        }
        $referenceCode = 'TKT-'.$companyShortCode. + rand(0, 99999999);
        return $referenceCode;
    }

    /**
     * @return string
     */
    public function refCodeGeneratorForOption()
    {
        $optionReferenceCode = 'OPT' . rand(0, 99999999);
        return $optionReferenceCode;
    }

    /**
     * @return string
     */
    public function refCodeGeneratorForCart()
    {
        $referenceCode = '-CRT' . rand(0, 99999999);
        $isThereAny = Cart::where('referenceCode', $referenceCode)->count();
        if ($isThereAny > 0) {
            return $this->refCodeGeneratorForCart();
        }
        return $referenceCode;
    }

    /**
     * @param $resRef
     * @return string
     */
    public function refCodeGeneratorForBooking($resRef)
    {
        $bkn = 'BKN' . rand(10000000, 99999999);
        $check = Booking::where('bookingRefCode', 'like', '%'. $bkn .'%')->first();

        if ($check)
            return $this->refCodeGeneratorForBooking($resRef);

        return $resRef.'-'. $bkn;
    }

    /**
     * @param $bookingRef
     * @return string
     */
    public function refCodeGeneratorForPhysicalTicket($bookingRef)
    {
        return $bookingRef. '-TKN' . rand(0, 99999999);
    }

    /**
     * @return string
     */
    public function invoiceGenerator()
    {
       $referenceCode = 'INV-' . rand(0, 99999999);
         $isThereAny = Invoice::where('referenceCode', $referenceCode)->count();
        if ($isThereAny > 0) {
            return $this->invoiceGenerator();
        }
        return $referenceCode;
    }

    /**
     * @return string
     */
    public function refCodeGeneratorForExtPayment()
    {
        return 'EXT-' . rand(0, 99999999);
    }

    /**
     * @return string
     */
    public function refCodeGeneratorForBokunBooking()
    {
        return $bokunRefCode =  'BOKUN' . rand(0, 9999999);
    }

}
