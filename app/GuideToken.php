<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class GuideToken extends Model
{

   protected $dates = ['created_at', 'updated_at', 'until_validdate'];

 

    protected $fillable = [
        'token_name', 'token', 'until_validdate', 'guide_tokenable_id'
    ];

   


     public function guide_tokenable()
    {
        return $this->morphTo();
    }

   
}

