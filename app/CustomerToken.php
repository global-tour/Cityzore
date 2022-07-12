<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class CustomerToken extends Model
{

   protected $dates = ['created_at', 'updated_at', 'until_validdate'];

 

    protected $fillable = [
        'token_name', 'token', 'until_validdate'
    ];

   

   
}

