<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blockout extends Model
{

    protected $table = 'blockouts';

    public function availabilities()
    {
        return $this->belongsToMany(Availability::class, 'blockout_availability');
    }

}
