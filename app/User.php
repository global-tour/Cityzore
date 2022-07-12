<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    use Notifiable;

    protected $guard = 'web';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'countryCode', 'phoneNumber', 'address','avatar', 'isActive'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function commission(){
        return $this->hasMany('App\Commission', 'commissionerID', 'id');
    }
     public function wishlists(){
        return $this->hasMany(Wishlist::class, 'userID');
    }
    public function platform() {
        return $this->hasOne('App\UserPlatform');
    }
}
