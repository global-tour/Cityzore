<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bigbus extends Model
{

    protected $table = 'bigbus_connection';

    protected $fillable = [
        'option_id',
        'product_id',
        'body',
        'units'
    ];

    public function option()
    {
        return $this->hasOne(Option::class, 'id', 'option_id');
    }

}
