<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerLoginLog extends Model
{
    public function booking() {
        return $this->belongsTo('App\Booking');
    }
}
