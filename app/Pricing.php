<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $table = 'pricings';

    protected $fillable = [
        'formType', 'type', 'title', 'adultMin', 'adultMax', 'adultPrice', 'youthMin', 'youthMax', 'youthPrice',
        'childMin', 'childMax', 'childPrice', 'infantMin', 'infantMax', 'infantPrice', 'supplierID', 'adultPriceCom',
        'youthPriceCom', 'childPriceCom', 'infantPriceCom', 'groupPriceData', 'euCitizenMin', 'euCitizenMax', 'euCitizenPrice',
        'euCitizenPriceCom'
    ];


    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_pricing');
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class);
    }

}
