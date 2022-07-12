<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CustomerLoginLog;
use DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
     public function getMobileCustomerLogs()
    {















           $allMonths = CustomerLoginLog::select('booking_id', DB::raw('count(DISTINCT booking_id) as `data`'), DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') month_date"))

    ->orderBy("created_at", "desc")
    ->distinct('booking_id')
    ->groupby('year','month')
    ->get();
        return view('panel.customerlogs.index', compact("allMonths"));
    }

    public function ajax(Request $request){

        switch ($request->action) {
            case 'get_data_by_month':

             $date = $request->date;
             $date = Carbon::createFromFormat("Y-m-d", $date);
             $first_of_month = $date->copy()->firstOfMonth();
             $last_of_month = $date->copy()->lastOfMonth();
             $responseArray = [];


               //DB::enableQueryLog();




             $groupByData = CustomerLoginLog::select("booking_id","id","created_at")->whereBetween(DB::raw('DATE(created_at)'), [date($first_of_month), date($last_of_month)])
                         ->orderBy("created_at")
                        ->get()
                        ->groupBy(function ($val) {
                            return Carbon::parse($val->created_at)->format('Y-m-d');
                        });

                        //dd(DB::getQueryLog());


              $ignoredBookingID = [];
              foreach($groupByData as $date => $elements){
                $tempArr = [];
                foreach($elements as $elem){
                  if(!in_array($elem->booking_id, $ignoredBookingID)) {
                      $tempArr[] = $elem->booking_id;
                      array_push($ignoredBookingID, $elem->booking_id);
                  }
                }
               $tempArr =  array_unique($tempArr);
                $responseArray[$date] = count($tempArr);

             }


            return response()->json(["status" => "success", "data" => $responseArray]);


                break;

            default:
                // code...
                break;
        }



    }

    public function fetchCustomerLogs(Request $request) {
         $customerEmail = $request->customerEmail;
         $customerLogs = CustomerLoginLog::where('customerEmail', $customerEmail)->orderBy('id', 'DESC')->get();
         foreach ($customerLogs as $customerLog) {
             $booking = \App\Booking::find($customerLog->booking_id);
             $optionTitle = $booking->bookingOption->title;
             $customerLog->option = $optionTitle;
         }
        return response()->json(["status" => "success", "data" => $customerLogs]);
    }



}
