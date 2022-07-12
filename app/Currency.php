<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cart;

class Currency extends Model
{
    protected $table = 'currencies';


    public static function calculateCurrencyForVisitorForEveryItem($amount, $oldCurrencyID=2){

          $currencyID = session()->get('currencyCode');
        $oldCurrency = Currency::findOrFail($oldCurrencyID);
        $oldCurrencyValue = $oldCurrency->value;
        if (!is_null($currencyID)) {
            $currencyDesired = Currency::findOrFail($currencyID);
        } else {
            $currencyDesired = Currency::findOrFail(2);
        }
        $amountDesired = $amount * $oldCurrencyValue / $currencyDesired->value;
        return $amountDesired;


    }


    public static function calculateCurrencyForVisitor($amount, $oldCurrencyID=2)
    {
       
    

        $currencyID = session()->get('currencyCode');
        $oldCurrency = Currency::findOrFail($oldCurrencyID);
        $oldCurrencyValue = $oldCurrency->value;
        if (!is_null($currencyID)) {
            $currencyDesired = Currency::findOrFail($currencyID);
        } else {
            $currencyDesired = Currency::findOrFail(2);
        }
        if (is_null(session()->get('currencyIcon'))) {
            session()->put('currencyIcon', 'icon-cz-eur');
        }
        $amountDesired = $amount * $oldCurrencyValue / $currencyDesired->value;
        $amountDesired =  number_format((float)$amountDesired, 2, '.', '');
        return $amountDesired;
    }



    public static function calculateCurrencyForVisitorFromCart($refCode, $oldCurrencyID=2)
    {
       
       
        $amount = Cart::where('referenceCode', $refCode)->first()->totalPrice ?? 0;
        $tempAmount = Cart::where('referenceCode', $refCode)->first()->tempTotalPrice;


         $amount = !is_null($tempAmount) ? $tempAmount : $amount;

        $currencyID = session()->get('currencyCode');
        $oldCurrency = Currency::findOrFail($oldCurrencyID);
        $oldCurrencyValue = $oldCurrency->value;
        if (!is_null($currencyID)) {
            $currencyDesired = Currency::findOrFail($currencyID);
        } else {
            $currencyDesired = Currency::findOrFail(2);
        }
        if (is_null(session()->get('currencyIcon'))) {
            session()->put('currencyIcon', 'icon-cz-eur');
        }
        $amountDesired = $amount * $oldCurrencyValue / $currencyDesired->value;
        $amountDesired =  number_format((float)$amountDesired, 2, '.', '');
        return $amountDesired;

    }


        public static function calculateAffiliateCommission($totalPrice, $optionID = null)
    {
       
       
        
                 if(auth()->guard('web')->user()->whereHas('commission',function($q) use($optionID){
                         $q->where('optionID', $optionID);

                        })->exists()){



                           $commission = auth()->guard('web')->user()->commission()->where('optionID', $optionID)->first()->commission ?? 0;

                           
                         }else{
                            $commission = auth()->guard('web')->user()->commission ?? 0;

                         }
                         
                   return number_format((float)$totalPrice*($commission/100), 2, '.', '');
    }



    

}
