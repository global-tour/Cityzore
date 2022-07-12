<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Av extends Model
{

    protected $table = 'avs';

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_av');
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class, 'av_supplier');
    }

    public function avdates()
    {
        return $this->belongsToMany(Avdate::class, 'av_avdate');
    }

    public function ticketType()
    {
        return $this->belongsToMany(TicketType::class, 'av_tickettype');
    }

}
