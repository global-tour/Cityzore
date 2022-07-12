<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTag extends Model
{

    protected $table = 'meta_tags';

    public function product()
    {
        return $this->belongsToMany(Product::class, 'metatag_product');
    }

    public function blogPost()
    {
        return $this->belongsToMany(BlogPost::class, 'blogpost_metatag', 'metatag_id', 'blogpost_id');
    }

}
