<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';

    // description of statuses
    // 0: added to cart
    // 1: removed from cart (not bought)
    // 2: removed from cart (bought)
    // 3: removed from cart (bought and made ammendment)
    // 4: time expired
    // 5: booking cancelled
    // 6: shared with customer

}
