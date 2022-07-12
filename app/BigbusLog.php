<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BigbusLog extends Model
{

    protected $table = 'bigbus_logs';

    protected $fillable = [
        'booking_id',
        'status',
        'response',
    ];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'id', 'booking_id');
    }

}
