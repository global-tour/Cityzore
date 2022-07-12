<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingRecord extends Model
{
    protected $table = 'booking_records';
    protected $guarded = [];
    
    public $timestamps = false;
}
