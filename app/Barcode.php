<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Barcode extends Model
{
    protected $fillable = ["bookingID",  "ownerID", "cartID", "isUsed", "isReserved", "log", "ticketType"];

    public function ticketTypeName()
    {
        return $this->belongsTo(TicketType::class, 'ticketType', 'id');
    }

       public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingID', 'id');
    }

    public function getBarcodeGeneratorAttribute()
    {
        $generator = new BarcodeGeneratorPNG();

        return base64_encode($generator->getBarcode($this->attributes['code'], $generator::TYPE_CODE_128));
    }

}
