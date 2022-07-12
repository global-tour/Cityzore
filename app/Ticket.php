<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'referenceCode', 'type'
    ];

    public function availabilities()
    {
        return $this->belongsToMany(Availability::class, 'availability_ticket');
    }

}
