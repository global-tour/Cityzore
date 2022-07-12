<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adminlog extends Model
{

    protected $table = 'adminlogs';

    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo('App\Admin', 'userID', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'userID', 'id');
    }

}
