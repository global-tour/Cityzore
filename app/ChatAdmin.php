<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatAdmin extends Model
{
    protected $fillable = ["user_id", "chat_password", "status", "response_data"];


}
