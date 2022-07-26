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

    public function cart()
    {
        return $this->hasOne(Cart::class, 'id', 'cartID');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y H:i:s');
    }

    public function getRecordFieldsAttribute(): array
    {
        $bookingRecord = BookingRecord::where('client_id', $this->attributes['userID'])
            ->orWhere('request->clientid', 'like', '%' . $this->attributes['userID'] . '%')
            ->orWhere('request->oid', 'like', '%' . $this->attributes['processID'] . '%')
            ->first();


        return [
            'fullName'  => $bookingRecord ? $bookingRecord->name . ' ' . $bookingRecord->surname : '',
            'phone'     => $bookingRecord ? $bookingRecord->country_code . $bookingRecord->phone_number : '',
            'email'     => $bookingRecord ? $bookingRecord->email : '',
            'from'      => $bookingRecord ? $bookingRecord->platform : ''
        ];
    }

}
