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

    public function getStatusColorAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                $status = '#facbb1c7';
                break;
            case 1:
                $status = '#daf1dbc7';
                break;
            case 2:
                $status = '#ffcacac7';
                break;
            default:
                $status = '#fafafa';
                break;
        }

        return $status;
    }
}
