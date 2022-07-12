<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfDateForGuide extends Model
{
    protected $fillable = ["guide_id", "date", "status"];
}
