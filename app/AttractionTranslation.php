<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttractionTranslation extends Model
{

    protected $table = 'attraction_translations';

    public function attraction()
    {
        return $this->hasOne(Attraction::class, 'id', 'attractionID');
    }
}
