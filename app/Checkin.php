<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Checkin extends Model
{

 protected $fillable = ['booking_id','status', 'email', 'readerName', 'role', 'person', 'ticket', 'status'];


    public function checkinable()
    {
        return $this->morphTo();
    }

    public function book(){
    	return $this->belongsTo(Booking::class, 'booking_id');
    }

     public function getPersonAttribute($value)
    {
        return json_decode($value, true);
    }
}

