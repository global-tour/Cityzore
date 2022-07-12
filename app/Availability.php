<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{

    protected $table = 'availabilitys'; // Not availabilities

    protected $fillable = [
        'supplierID', 'name', 'type'
    ];

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_availability');
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function avdates()
    {
        return $this->belongsToMany(Avdate::class, 'availability_avdate');
    }

    public function blockouts()
    {
        return $this->belongsToMany(Blockout::class, 'blockout_availability');
    }

    public function ticket()
    {
        return $this->belongsToMany(Ticket::class, 'availability_ticket');
    }

    public function ticketType()
    {
        return $this->belongsToMany(TicketType::class, 'availability_tickettype');
    }

}
