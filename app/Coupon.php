<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    // status descriptions
    // 1: Product - Option
    // 2: Location
    // 3: Attraction
    // 4: For User
    // 5: All
    // 6: Specific User
}
