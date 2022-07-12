<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Avdate extends Model
{

    protected $table = 'avdates';

    public function av()
    {
        return $this->belongsToMany(Av::class, 'av_avdate');
    }

}
