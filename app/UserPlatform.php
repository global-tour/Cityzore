<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'user_platform';
}
