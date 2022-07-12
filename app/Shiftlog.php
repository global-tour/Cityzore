<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Shiftlog extends Model
{

 public function logger(){
 	return $this->belongsTo(Admin::class, 'logger_id');
 }
}

