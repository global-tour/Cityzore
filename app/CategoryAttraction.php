<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryAttraction extends Model
{
    protected $table='category_attractions';
    protected $fillable = ["category_id","attraction_id"];
    public $timestamps = false;

}
