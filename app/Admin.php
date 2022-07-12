<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{

    protected $guard = 'admin';

    use Notifiable;

    protected $fillable = [
        'name', 'surname', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'adminFullName'
    ];

     public function images(){
        return $this->morphMany(GuideImage::class, 'guide_imageable');
    }

      public function tokens(){
    	return $this->morphMany(GuideToken::class, 'guide_tokenable');
    }

      public function shifts(){
        return $this->hasMany(Shift::class, 'user_id');
    }

     public function billings(){
        return $this->morphMany(Billing::class, 'billingable');
    }

    public function checkins(){
        return $this->morphMany(Checkin::class, 'checkinable');
    }

    public function chats(){
        return $this->hasOne(ChatAdmin::class, 'user_id');
    }

      public function offday(){
        return $this->hasMany(OfDateForGuide::class, 'guide_id');
    }

    public function getAdminFullNameAttribute()
    {
        return $this->attributes['name'] . ' ' . $this->attributes['surname'] ;
    }
}

