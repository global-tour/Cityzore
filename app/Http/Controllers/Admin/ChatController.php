<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function getChatPage(){
    	
    	return view('chat.app.pages.staff.index');
    }
}
