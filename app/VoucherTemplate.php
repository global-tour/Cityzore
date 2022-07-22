<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherTemplate extends Model
{
    protected $fillable = ['name', 'template', 'status', 'image'];
    protected $casts = [
        'template' => 'array',
    ];

}
