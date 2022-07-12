<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Shift extends Model
{
protected $dates = [
"created_at",
"updated_at",
"time_in",
"time_out"
];

  public function user(){
  	return $this->belongsTo(Admin::class, 'guide_id');
  }

   public function meeting(){
  	return $this->belongsTo(Meeting::class, 'meeting_id');
  }

  public function logs(){
  	return $this->hasMany(Shiftlog::class, 'shift_id');
  }
 
}

