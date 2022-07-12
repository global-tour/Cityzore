<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CityImage extends Model
{

    protected $table = 'city_images';

    public function countryName()
    {
        return $this->belongsTo('App\Country', 'countryID', 'id');
    }

}
