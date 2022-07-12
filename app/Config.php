<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{

    protected $table = 'configs';

    public function currencyName()
    {
        return $this->belongsTo('App\Currency', 'currencyID');
    }

    public function calculateCurrency($bookingPrice, $intendedValue, $bookingCurrencyID=2)
    {
        $bookingCurrency = Currency::findOrFail($bookingCurrencyID);
        $bookingCurrencyValue = $bookingCurrency->value;
        $val = $bookingPrice * $bookingCurrencyValue / $intendedValue;
        $val =  number_format((float)$val, 2, '.', '');
        return $val;
    }
}
