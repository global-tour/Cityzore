<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherTemplate extends Model
{
    protected $fillable = ['name', 'template', 'default', 'status', 'image'];
    protected $casts = [
        'template' => 'array',
    ];

}
