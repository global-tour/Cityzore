<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Shift;
use App\Meeting;
use App\Option;
use App\Config;
use Carbon\Carbon;

class GuideManagementController extends Controller
{
    

  public function setShiftInputOutput(Request $request){
    $mt = Meeting::find($request->meeting_id);
    $meetings = Meeting::where("date", $mt->date)->where("time", $mt->time)->whereJsonContains("guides", (string)$request->guide_id)->get();


try{



  foreach ($meetings as $meeting) {
  



    if(Shift::where('meeting_id', $meeting->id)->where('guide_id', $request->guide_id)->exists() && $request->status == "output"){
     $shift = Shift::where('meeting_id', $meeting->id)->where('guide_id', $request->guide_id)->orderBy('id', 'desc')->first();
    }else{
     $shift = new Shift();
    }


    $shift->meeting_id = $meeting->id;
    $shift->guide_id = $request->guide_id;


  if($request->status == "input"){
   $shift->meeting_point = $request->meeting_point;
   $shift->time_in = Carbon::createFromTimestamp($request->time_in, "Europe/Paris")->format('Y-m-d H:i:s');
   $shift->latitude_in = $request->latitude_in;
   $shift->longitude_in = $request->longitude_in;
   $shift->message_in = $request->message_in ?? '';
  }else{

   $shift->time_out = Carbon::createFromTimestamp($request->time_out, "Europe/Paris")->format('Y-m-d H:i:s');
   $shift->latitude_out = $request->latitude_out;
   $shift->longitude_out = $request->longitude_out;
   $shift->message_out = $request->message_out ?? '';
  }




    $shift->status = $request->status;
    $shift->optRefCode = $meeting->option;
    $shift->meeting_id = $meeting->id;
    $shift->save();
    

    
}

}catch(\Exception $e){
  return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => $e->getMessage()]], 400);
}
   

    return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['message' => 'Record Has Been Set Successfully!']]);
    //return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'An Error Occured!']], 400);
    


  }



  public function getShifts(Request $request){

    $config = Config::where('userID', -1)->first();
    $meetings = Meeting::whereJsonContains("guides", (string)$request->guide_id)->orderBy('clock_in', 'desc')->get();
    $responseData = [];

   

    foreach ($meetings as $meeting) {
   /* 	 if($meeting->id == 239){
         $europeTimestamp = (int)$request->time;
         $europeTimestamp = Carbon::createFromTimestamp(time())->timezone("Europe/Paris")->format("Y-m-d H:i:s");
         $europeTimestamp = Carbon::parse($europeTimestamp)->timestamp;

           
        return response()->json($europeTimestamp ."   -  ". $meeting->clock_out->timestamp);
        return response()->json(($europeTimestamp >= $meeting->clock_in->timestamp) && ($europeTimestamp <= $meeting->clock_out->timestamp));
       }*/
    	
        $option = Option::where('referenceCode', $meeting->option)->first();

        $timezone = "Europe/Paris";
        if($option->products()->count()){
        $timezone = $option->products()->first()->countryName->timezone;
        }

        
       
         $europeTimestamp = (int)$request->time;
         $europeTimestamp = Carbon::createFromTimestamp($europeTimestamp)->timezone("Europe/Paris")->format("Y-m-d H:i:s");
         $europeTimestamp = Carbon::parse($europeTimestamp)->timestamp;
        

    		if($europeTimestamp >= $meeting->clock_in->timestamp && $europeTimestamp <= $meeting->clock_out->timestamp){
           
    			$option = Option::where('referenceCode', $meeting->option)->first();

             
             $responseData[$meeting->time] = [
               
               "meetingID" => $meeting->id,
               "meetingDate" => $meeting->date,
               "meetingPoint" => $option->meetingPoint,             
               "meetingPoint" => $option->meetingPoint,
               "meetingLatitude" => $option->meetingPointLat,             
               "meetingLongitude" => $option->meetingPointLong,             
               "optionName" => $option->title,             
               "optRefCode" => $option->referenceCode,             
               "shiftStartClock" => $meeting->clock_in->format('d-m-Y H:i'),             
               "shiftEndClock" => $meeting->clock_out->format('d-m-Y H:i'),
               "shiftDistance" => ((int)$config->meeting_distance) / 1000,           
               "timezone" => $timezone          
             ];



    		}
   
        


    	
    }

    return response()->json(["status" => "success", "statusCode" => 200, "data" => $responseData]);
    

  }






 



}
