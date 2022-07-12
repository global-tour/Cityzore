<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class GuideImage extends Model
{

   protected $dates = ['created_at', 'updated_at'];

 

    protected $fillable = [
       'src', 'guide_imageable_id'
    ];

    public function guide_imageable()
    {
        return $this->morphTo();
    }

   
}

