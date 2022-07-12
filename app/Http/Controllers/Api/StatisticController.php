<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Str;
use Carbon\Carbon;
use App\Booking;
use App\Meeting;
use App\Option;
use App\Pricing;
use DB;




class StatisticController extends Controller
{




  public function SupplierStc(Request $request){





     $target_bookings = collect();


     $target_bookings = Booking::with(["check" => function($q) use($request){
      $q->where('checkinable_id', $request->user_id);




       }])->whereHas("bookingOption", function($optQuery) use($request){
       $optQuery->where('rCodeID', $request->user_id);
      })->orWhere(function($q) use($request){

       $q->where("companyID", $request->user_id);
       $q->where("status", 0);
       $q->where("dateForSort",">=", Carbon::parse(trim($request->startDate)));
       $q->where("dateForSort","<", Carbon::parse(trim($request->endDate)));







     })->orderby('dateForSort', 'desc')->get()->groupBy(function($d) {
       return Carbon::parse($d->dateForSort)->format('Y-m');
   });







     $target_bookings = $target_bookings->map(function ($model){
    return $model->map(function($model2){

      $pricing_id = Option::where('referenceCode', $model2->optionRefCode)->first()->pricings ?? null;
      $pricing = Pricing::findOrFail($pricing_id);
      $ignoredCategories = $pricing->ignoredCategories;
      $ignoredCategories = empty($ignoredCategories) ? [] : $ignoredCategories;
      $ignoredCategories = is_array($ignoredCategories) ? $ignoredCategories : json_decode($ignoredCategories, true);

        return [

       "id" => $model2->id,
       "status" => $model2->status,
       "productRefCode" => $model2->productRefCode,
       "optionRefCode" => $model2->optionRefCode,
       "reservationRefCode" => $model2->reservationRefCode,
       "bookingRefCode" => $model2->bookingRefCode,
       "gygBookingReference" => $model2->gygBookingReference,
       "bookingItems" => json_decode($model2->bookingItems),
       "dateTime" => $model2->dateTime,
       "dateForSort" => $model2->dateForSort,
       //"hour" => json_decode($model2->hour),
       //"travelers" => json_decode($model2->travelers),
       "fullName" => $model2->fullName,
       "totalPrice" => $model2->totalPrice,
       "isBokun" => $model2->isBokun,
       "isViator" => $model2->isViator,
       "ignoredCategories" => array_map('strtoupper', $ignoredCategories),
       "check" => $model2->check

        ];

    });
});



   return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $target_bookings]);
  }





  public function OthersStc(Request $request){


     $meetings_contains_user = Meeting::whereJsonContains('guides', [(string)$request->user_id])->get();
     //return response()->json($meetings_contains_user);


     $target_bookings = collect();
     if($meetings_contains_user->count()){

     $target_bookings = Booking::with(["check" => function($q) use($request){
      $q->where('checkinable_id', $request->user_id);




     }])->where(function($q) use($meetings_contains_user){
      $timer = 0;


       foreach ($meetings_contains_user as $meet) {
        $queryStr  =   $timer === 0 ? 'where' :'orWhere';
       $q->$queryStr(function($subq) use($meet){



        $subq->where('dateForSort', $meet->date);
        $subq->where('optionRefCode', $meet->option);
        $subq->where('dateTime', 'LIKE', '%'.$meet->time.'%');



       });
       $timer++;
     }




     })->where('status', 0)->whereDate("dateForSort",">=", Carbon::parse(trim($request->startDate)))->whereDate("dateForSort","<", Carbon::parse(trim($request->endDate)))->orderby('dateForSort', 'desc')->get()->groupBy(function($d) {
     return Carbon::parse($d->dateForSort)->format('Y-m');
 });



}



     $target_bookings = $target_bookings->map(function ($model){
    return $model->map(function($model2){

      $pricing_id = Option::where('referenceCode', $model2->optionRefCode)->first()->pricings ?? null;
      $pricing = Pricing::findOrFail($pricing_id);
      $ignoredCategories = $pricing->ignoredCategories;
      $ignoredCategories = empty($ignoredCategories) ? [] : $ignoredCategories;
      $ignoredCategories = is_array($ignoredCategories) ? $ignoredCategories : json_decode($ignoredCategories, true);

        return [

       "id" => $model2->id,
       "status" => $model2->status,
       "productRefCode" => $model2->productRefCode,
       "optionRefCode" => $model2->optionRefCode,
       "reservationRefCode" => $model2->reservationRefCode,
       "bookingRefCode" => $model2->bookingRefCode,
       "gygBookingReference" => $model2->gygBookingReference,
       "bookingItems" => json_decode($model2->bookingItems),
       "dateTime" => $model2->dateTime,
       "dateForSort" => $model2->dateForSort,
       //"hour" => json_decode($model2->hour),
       //"travelers" => json_decode($model2->travelers),
       "fullName" => $model2->fullName,
       "totalPrice" => $model2->totalPrice,
       "isBokun" => $model2->isBokun,
       "isViator" => $model2->isViator,
       "ignoredCategories" => array_map('strtoupper', $ignoredCategories),
       "check" => $model2->check

        ];

    });
});



   return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $target_bookings]);
  }




 public function GuideStc(Request $request){
  $target_response = [];
  $controlArray = [];
  $meetings_contains_user = Meeting::with(['shifts' => function($q){


  }])->whereHas("shifts", function($r) use($request){
  $r->where("guide_id", $request->user_id);
  })

  ->whereJsonContains('guides', [(string)$request->user_id])->whereNotNull('clock_in')->whereNotNull('clock_out')->whereDate("date",">=", Carbon::parse(trim($request->startDate)))->whereDate("date","<", Carbon::parse(trim($request->endDate)))->orderBy('date', 'asc')->get()->groupBy([function($d) {
     return Carbon::parse($d->date)->format('Y-m');
 }]);


  $totalClockSecondAll = 0;
  $totalShiftSecondAll = 0;
  $generalMeetingData = [];
  $alldatesTotal = [];
  $alldatesTotalByMonth = [];


//return response()->json($meetings_contains_user);
foreach ($meetings_contains_user as $date => $meetings) {
  $alldatesTotalByMonth[$date] = 0;
  $totalClockSecond = 0;
  $clockArrData = [];

  foreach ($meetings as $meeting) {
    $alldatesTotal[$meeting->date] = 0;
  }

  foreach ($meetings as $meeting) {


    if(!empty($meeting->clock_in) && !empty($meeting->clock_out)){
      $thisClockSecondDiff = $meeting->clock_out->timestamp - $meeting->clock_in->timestamp;
      $totalClockSecond += $meeting->clock_out->timestamp - $meeting->clock_in->timestamp;


    }else{
      $thisClockSecondDiff = $meeting->clock_out->timestamp - $meeting->clock_in->timestamp;
      $totalClockSecond += 0;
    }


    $dateTime = $meeting->date." ".$meeting->time;
     $totalShiftSecond = 0;
     if(!in_array($dateTime, $controlArray)){

      $shiftArrData = [];


      foreach($meeting->shifts()->where("guide_id",$request->user_id)->get() as $shift){
        if(!empty($shift->time_in) && !empty($shift->time_out)){
          //$totalShiftSecond += $shift->time_out->timestamp - $shift->time_in->timestamp;
          $totalShiftSecond += $shift->time_out->diffInSeconds($shift->time_in);

          array_push($shiftArrData, [
            "shift_id" => $shift->id,
            "time_in" => $shift->time_in->format("Y-m-d H:i:s"),
            "time_out" => $shift->time_out->format("Y-m-d H:i:s"),
            //"shifted_diff" => $shift->time_out->timestamp - $shift->time_in->timestamp,
            "shifted_diff" =>  $shift->time_out->diffInSeconds($shift->time_in)
          ]);
        }


      }
      $alldatesTotalByMonth[$date] += $totalShiftSecond;
      $alldatesTotal[$meeting->date] += $totalShiftSecond;
      $totalShiftSecondAll += $totalShiftSecond;



        array_push($clockArrData, [
            "meeting_id" => $meeting->id,
            "guides" => json_decode($meeting->guides),
            "clock_in" => !empty($meeting->clock_in) ? $meeting->clock_in->format("Y-m-d H:i:s") : 0,
            "clock_out" => !empty($meeting->clock_out) ? $meeting->clock_out->format("Y-m-d H:i:s") : 0,
            "scheduled_diff" => $thisClockSecondDiff,
            "totalshift_of_meeting" => $totalShiftSecond,
            "shifts" => ["data" => $shiftArrData]
          ]);



      array_push($controlArray, $dateTime);
     }

  }

  $totalClockSecondAll += $totalClockSecond;
  $generalMeetingData[$date] = ["data" => $clockArrData, "scheduled_total" => $totalClockSecond, "shifted_total" => $alldatesTotalByMonth[$date]];



}


$alldatesTotal = array_map(function($data){
return \gmdate("H:i", $data);

}, $alldatesTotal);


$totalMinute = ((ceil($totalShiftSecondAll/60))%60);
$totalMinute = ($totalMinute < 10) ? "0".$totalMinute : $totalMinute;

$generalMeetingData["scheduled_total"] = $totalClockSecondAll;
$generalMeetingData["shifted_total"] = $totalShiftSecondAll;
$generalMeetingData["shifted_total_hour"] = (int)($totalShiftSecondAll/3600).":".$totalMinute;
$generalMeetingData["alldatesTotal"] = $alldatesTotal;

//return response()->json($generalMeetingData);





/*  $target_response = $meetings_contains_user->map(function($model){






   $processed = $model->map(function($model2){







    $processedShift = $model2->shifts->map(function($shiftModel){

      if(!empty($shiftModel->time_out) && !empty($shiftModel->time_in)){
      $shifted_diff = $shiftModel->time_out->timestamp - $shiftModel->time_in->timestamp;


             return[
               "time_in" =>!empty($shiftModel->time_out)? $shiftModel->time_in->format('Y-m-d H:i:s') : 0,
               "time_out" => !empty($shiftModel->time_out) ? $shiftModel->time_out->format('Y-m-d H:i:s') : 0,
               "shifted_diff" =>$shifted_diff
             ];
           }else{
           return [
               "time_in" =>0,
               "time_out" =>0,
               "shifted_diff" =>0
             ];
           }





        });



     return [
        "clock_in" => $model2->clock_in->format('Y-m-d H:i:s'),
        "clock_out" => $model2->clock_out->format('Y-m-d H:i:s'),
        "scheduled_diff" => $model2->clock_out->timestamp - $model2->clock_in->timestamp,
        "totalshift_of_meeting" => $processedShift->sum("shifted_diff"),
        "shifts" => [
          "data" => $processedShift
        ]


     ];






   });





   return [
    "data" => $processed,
    "scheduled_total" => $processed->sum("scheduled_diff"),
    "shifted_total" => $processed->sum("totalshift_of_meeting"),

   ];







  });*/


  return response()->json(["status" => "success", "statusCode" => 200, "data" => ($generalMeetingData) ]);


 }



}
