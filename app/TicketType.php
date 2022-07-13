<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $table = 'ticket_types';

    protected $fillable = [
        'type',
        'format',
        'name',
        'bladeName',
        'multipleTicket',
        'usableAsTicket',
        'warnTicket'
    ];

    public function av()
    {
        return $this->belongsToMany(Av::class, 'av_tickettype');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_ttype');
    }


    public function getGeneratorAttribute()
    {
        $this->attributes['type'] == 'BARCODE';

    }

    public function barcodes(){
         return $this->hasMany(Barcode::class, 'ticketType', 'id');
    }

}
