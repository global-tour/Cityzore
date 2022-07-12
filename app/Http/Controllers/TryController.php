<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TryController extends Controller
{
    public function index(){

        $this->addPlatform();
        //$this->addNullPlatform();



    }
    public function addPlatform(){
        $booking=new Booking();
        $bookArr=$booking->whereNotNull('fromWebsite')->get();
        $status=Array();
        $statusFalse=Array();
        foreach ($bookArr as $arr){
            switch ($arr->fromWebsite){
                case "https://cityzore.com":
                    $arr->platformID=2;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    break;
                case "https://pariscitytours.fr":
                    $arr->platformID=4;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Musement":
                    $arr->platformID=6;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Viator.com":
                    $arr->platformID=5;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Paris Business and Travel":
                    $arr->platformID=12;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Getyourguide":
                    $arr->platformID=1;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Bokun":
                    $arr->platformID=3;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Headout":
                    $arr->platformID=7;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Railbookers":
                    $arr->platformID=10;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;
                case "Expedia local expert":
                    $arr->platformID=13;
                    $stat=$arr->save();
                    array_push($status,$stat);
                    if(!$stat) array_push($statusFalse,$arr->id);
                    break;

            }
        }
        dd($statusFalse,$status);
    }
    public function addNullPlatform(){
        $booking=new Booking();
        $bookArr=$booking->whereNull('fromWebsite')->get();
        $status=Array();
        $statusFalse=Array();
        foreach ($bookArr as $arr){
            if ($arr->gygBookingReference!=null){
                $arr->platformID=1;
                $stat=$arr->save();
                array_push($status,$stat);
            } else if($arr->isBokun==1){
                $arr->platformID=3;
                $stat=$arr->save();
                array_push($status,$stat);
            } else if($arr->isBokun==0 && $arr->productRefCode!=null){
                $arr->platformID=2;
                $stat=$arr->save();
                array_push($status,$stat);
            }
        }
        $isNull=$booking->whereNull('fromWebsite')->get();
        dd($statusFalse,$status,$bookArr->count(),$isNull);
    }
}
