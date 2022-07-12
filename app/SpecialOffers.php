<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialOffers extends Model
{

    protected $table = 'special_offers';

    public function product()
    {
        return $this->belongsTo('App\Product', 'productID', 'id');
    }

    public function option()
    {
        return $this->belongsTo('App\Option', 'optionID', 'id');
    }

}
