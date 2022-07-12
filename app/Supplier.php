<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Supplier extends Authenticatable
{
    protected $guard = 'supplier';

    use Notifiable;

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }

    public function pricing()
    {
        return $this->belongsToMany(Pricing::class);
    }

    public function option()
    {
        return $this->belongsToMany(Option::class);
    }

    public function av()
    {
        return $this->belongsToMany(Av::class);
    }

    public function productGallery()
    {
        return $this->belongsToMany(ProductGallery::class);
    }

    public function countryName()
    {
        return $this->belongsTo('App\Country', 'country', 'id');
    }

     public function billings(){
        return $this->morphMany(Billing::class, 'billingable');
    }
     public function tokens(){
        return $this->morphMany(GuideToken::class, 'guide_tokenable');
    }

    public function images(){
        return $this->morphMany(GuideImage::class, 'guide_imageable');
    }

     public function checkins(){
        return $this->morphMany(Checkin::class, 'checkinable');
    }

}
