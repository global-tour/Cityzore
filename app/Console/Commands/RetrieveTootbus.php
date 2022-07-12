<?php

namespace App\Console\Commands;
use App\Option;
use Illuminate\Console\Command;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\Http\Controllers\Helpers\ApiRelated;
use Carbon\Carbon;


class RetrieveTootbus extends Command
{
    public $apiRelated;
    public $tootbusRelated;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve:tootbus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve daily tootbus Availability data for connected options';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
       $this->apiRelated = new ApiRelated();
       $this->tootbusRelated = new TootbusRelated();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
     $options = Option::has("tootbus")->get();
     if($options->count()){

        foreach($options as $option){
        $this->setTootbusResponseDataToAvailability($option);
     }

     }
     



    }









       private function setTootbusResponseDataToAvailability($option){



  

 
    if($option->tootbus()->count()){
               $diff_day =  180;
               $startDate = Carbon::now()->format("Y-m-d");
               $tootbusAvailabilityResponse = $this->tootbusRelated->checkAvailability($option->tootbus->tootbus_product_id, $option->tootbus->tootbus_option_id, $startDate, $diff_day);
               if($tootbusAvailabilityResponse["status"] === false){
                return ["status" => false, "message" => $tootbusAvailabilityResponse["message"]];
               }
                $decoded_json_tootbas_body = json_decode($option->tootbus->body, true);
                $jsonq = $this->apiRelated->prepareJsonQ();
               
               
                

        if($option->avs()->count()){

            $_av = $option->avs()->first();


         

        }else{
            $_av = new Av();
        }

        $hourly = [];
        $daily = [];
        $disabledDays = [];

        $_av->availabilityType = ($decoded_json_tootbas_body["availabilityType"]) === "START_TIME" ? "Starting Time" : "Operating Hours";
        $_av->name = $decoded_json_tootbas_body["internalName"];
        $_av->supplierID = -1;
        $_av->avTicketType = ($decoded_json_tootbas_body["availabilityType"]) === "START_TIME" ? 1 : 2;
        $_av->ticketReferenceCode = null;
        $_av->dateRange = "[]";
        $_av->barcode = "[]";
        $_av->isLimitless = 0;
        $_av->disabledWeekDays = null;
        $_av->disabledMonths = null;
        $_av->disabledYears = null;
        
   
  
       
      
       if($tootbusAvailabilityResponse && $tootbusAvailabilityResponse["status"] === true){

       foreach(json_decode($tootbusAvailabilityResponse["message"], true) as $toot){

            if($decoded_json_tootbas_body["availabilityType"] === "START_TIME"){  // if has starting time 
                $nahid_hourly = $jsonq->json($_av->hourly !== "[]" && !empty($_av->hourly) ? $_av->hourly : "[{}]");
                $day = Carbon::parse($toot["id"])->format("d/m/Y");
                $hour = Carbon::parse($toot["id"])->format("H:i");
                $result = $nahid_hourly->where('day', '=', $day)->where('hour', '=', $hour)->get();


                    $hourly[] = [
                            "id" => $toot["id"],
                           "day" => $day,
                           "hour" => $hour,
                           "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                           "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                           "isActive" => $toot["available"] === true ? 1 : 0
                          ];
           




          

            }else{ // if has operating hours
           $nahid_daily = $jsonq->json($_av->daily !== "[]" && !empty($_av->daily) ? $_av->daily : "[{}]");

        if(count($toot["openingHours"]) == 0){
            $toot["openingHours"][] = ["from" => "00:00", "to" => "23:59"];
        }
       
           foreach($toot["openingHours"] as $h){

                $day = Carbon::parse($toot["id"])->format("d/m/Y");
                $result = $nahid_daily->where('day', '=', $day)->where('hourFrom', '=', $h["from"])->where("hourTo", $h["to"])->get();
               


                $daily[] = [
                            "id" => $toot["id"],
                            "day" => $day,
                           "hourFrom" => $h["from"],
                           "hourTo" => $h["to"],
                           "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                           "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                           "isActive" => $toot["available"] === true ? 1 : 0
                          ];



           }



     



            




            }


        }

            $_av->hourly = json_encode($hourly);
            $_av->daily = json_encode($daily);
            $_av->disabledDays = json_encode($disabledDays);
            $_av->save();



         if($_av->avdates()->count() == 0){
            $avdate = new Avdate();
         }else{
            $avdate = $_av->avdates()->first();
         }
            
            $avdate->valid_from_to = Carbon::now()->format('d/m/Y')." - ".Carbon::now()->addDays($diff_day)->format('d/m/Y');
            $avdate->valid_from = Carbon::now()->format('Y-m-d');
            $avdate->valid_to = Carbon::now()->addDays($diff_day)->format('Y-m-d');
            $avdate->save();
           


           if($_av->avdates()->count() == 0){
            $_av->avdates()->sync($avdate->id);
           }
           if($_av->supplier()->count() == 0){
           $_av->supplier()->sync($_av->supplierID);
           }
           if($_av->ticketType()->count() == 0){
           $_av->ticketType()->sync(24);
           }
           
            
            
            
      return ["status" => true, "message" => "Availability Data Set SuccessFully!"];
      


       }else{
        return ["status" => false, "message" => "An Error Occurred!"];
       }

      
    }
  




    }
}
