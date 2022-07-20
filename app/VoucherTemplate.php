<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherTemplate extends Model
{
    protected $fillable = ['name', 'template', 'status', 'image'];
    protected $casts = [
        'template' => 'array',
    ];

//    public function getTemplateAttribute($value)
//    {
//        return json_decode($value, true);
//    }
}
