<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Meeting extends Model
{

 protected $dates = [
 "created_at",
 "updated_at",
 "clock_in",
 "clock_out"
 ];

 public function shifts(){
 	return $this->hasMany(Shift::class, 'meeting_id');
 }

  public function opt(){
 	return $this->belongsTo(Option::class, 'option', 'referenceCode');
 }

  public function startEndTimes(){
    return $this->hasMany(MeetingStartEndTime::class, 'meeting_id');
 }
}

