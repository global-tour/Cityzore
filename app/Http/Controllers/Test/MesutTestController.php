<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MesutTestController extends Controller
{
    public function index()
    {
        return view('frontend.test');
    }
}
