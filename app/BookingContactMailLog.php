<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingContactMailLog extends Model
{
    public function sender(){
        return $this->belongsTo('App\Admin', 'sender_id', 'id');
    }

    public function booking(){
        return $this->belongsTo('App\Booking', 'booking_id', 'id');
    }
}
