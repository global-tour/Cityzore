<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\City;

class Country extends Model
{

    protected $table = 'countries';

    public function cities()
    {
        return $this->hasMany(City::class, 'countryID', 'id');
    }

}
