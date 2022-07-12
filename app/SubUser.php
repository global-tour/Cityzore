<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SubUser extends Authenticatable
{

    protected $guard = 'subUser';
    protected $table = 'sub_users';

    use Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];
}
