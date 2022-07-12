<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TootbusConnection extends Model
{
    
    public function option(){
    	return $this->belongTo(Option::class);
    }
}
