<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{

    protected $table = 'wishlists';

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID', 'id');
    }

}
