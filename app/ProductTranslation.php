<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{

    protected $table = 'product_translations';

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'productID');
    }

}
