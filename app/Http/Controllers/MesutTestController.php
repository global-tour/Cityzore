<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Jobs\UpdateBooking;

class MesutTestController extends Controller
{
    public function index()
    {
        Booking::chunk(10000, function ($items){
            dispatch(new UpdateBooking($items));
        });

        echo "Finish";
    }
}
