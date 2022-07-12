<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingLog extends Model
{

    protected $table = 'booking_logs';

    public function option()
    {
        return $this->belongsTo('App\Option', 'optionID', 'id');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y H:i:s');
    }

}
