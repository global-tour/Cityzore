<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\BookingController;
use App\Booking;
use App\Meeting;
use App\Option;
use App\GuideImage;
use App\Barcode;
use App\BookingImage;
use App\Product;
use App\CustomerToken;
use App\Supplier;
use App\Admin;
use App\CustomerLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Str;
use App\Http\Controllers\Helpers\CryptRelated;

class CustomerController extends Controller
{


   public function getCustomerLogin(Request $request){







    $checkBooking = Booking::where('status', 2)->orWhere('status', 3)->get()->filter(function($model) use($request){

        //$parts = explode('-', $model->bookingRefCode);
        //$lastOne = $parts[count($parts)-1];

        $bookingRefCode = $model->bookingRefCode;

        if((($model->gygBookingReference == $request->refCode) || ($model->bookingRefCode == $request->refCode) || preg_match("/".$request->refCode."$/",$bookingRefCode))){

            if((strpos($request->refCode, "BKN") === false && strpos($request->refCode, "BR-") === false && strpos($request->refCode, "PAR-") === false && strpos($request->refCode, "BOKUN") === false &&  strpos($request->refCode, "BOKUN") === false)){
                return false;
            }
         return true;
        }
    });

    if($checkBooking->count()){
        return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'this booking has been canceled before']], 400);
    }





    $targetBooking = Booking::where('status', 0)->get()->filter(function($model) use($request){

    	//$parts = explode('-', $model->bookingRefCode);
    	//$lastOne = $parts[count($parts)-1];

    	$bookingRefCode = $model->bookingRefCode;

    	if((($model->gygBookingReference == $request->refCode) || ($model->bookingRefCode == $request->refCode) || preg_match("/".$request->refCode."$/",$bookingRefCode))){
             if((strpos($request->refCode, "BKN") === false && strpos($request->refCode, "BR-") === false && strpos($request->refCode, "PAR-") === false && strpos($request->refCode, "BOKUN") === false && strpos($request->refCode, "GYG") === false)){
                return false;
            }
         return true;
    	}
    });

    if($targetBooking->count() == 0)
    	return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'there is no registered booking!']], 400);






         $result = CustomerToken::create([
            "token_name" => 'customer_token',
            "token" => $token = hash('sha256', Str::random(60)),
            "until_validdate" => Carbon::now()->addYear()
           ]);

          if(!$result){
           return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'An Error Occurred trying to create token!']], 400);
          }







    $targetMeeting = collect();
    foreach ($targetBooking as $book) {


      $productTitle = null;

      if($book->gygBookingReference === null &&  $book->is_tootbus == 0 && $book->isBokun == 0 && $book->isViator == 0){
       if(Product::where("referenceCode", $book->productRefCode)->count()){
          $product = Product::where("referenceCode", $book->productRefCode)->first();
          $productTitle = $product->title;
       }
      }
      $firstName = json_decode($book->travelers, true)[0]["firstName"];
      $lastName = json_decode($book->travelers, true)[0]["lastName"];
      $customer_email = json_decode($book->travelers, true)[0]["email"];
      $bookingItems = json_decode($book->bookingItems);
      $booking_id = $book->id;
      $gygBookingReference = $book->gygBookingReference;
      $bookingRefCode = $book->bookingRefCode;
      $isBokun = $book->isBokun;
      $isViator = $book->isViator;


      $companyID = $book->companyID;

    	$times = $this->parseTime($book->dateTime);


    	$targetMeeting = Meeting::where('date', Carbon::make($book->dateForSort)->format('Y-m-d'))->where('option', $book->optionRefCode)->where(function($q) use($times){
    	   $timer = 0;
          $queryStr = $timer === 0 ? 'where' : 'orWhere';

           foreach ($times as $time) {
           	$q->$queryStr('time', $time);
           }

    	})->get();

    }

     $targetMeeting = $targetMeeting->map(function($model) use ($firstName, $lastName, $booking_id, $bookingItems, $productTitle, $token, $companyID, $gygBookingReference, $bookingRefCode, $isBokun, $isViator, $customer_email){
     $optionName = Option::where('referenceCode', $model->option)->firstOrFail();
     $guide_images = GuideImage::whereIn("guide_imageable_id", json_decode($model->guides, true))->pluck('src', 'guide_imageable_id')->toArray();


    if($companyID == -1){
        $supplierInformations = [];
        $supplierInformations["companyName"] = "Global Tours and Tickets";
        $supplierInformations["email"] = "contact@parisciptrips.com";
        $supplierInformations["phoneNumber"] = "+33184208801";

    }else{
        $supplier = Supplier::findOrFail($companyID);
        $supplierInformations = [];
        $supplierInformations["companyName"] = $supplier->companyName;
        $supplierInformations["email"] = $supplier->email;
        $supplierInformations["phoneNumber"] = $supplier->countryCode.$supplier->phoneNumber;

    }
    $logoURL = $this->getLogo($gygBookingReference, $bookingRefCode, $isBokun, $isViator);






     return [
     "token" => $token,
    "hasGuide" => true,
    "id" => $model->id,
    "date" => $model->date,
    "time" => $model->time,
    "operatingHours" => json_decode($model->operating_hours),
    "option" => $model->option ?? '',
    "productTitle" => $productTitle,
    "optionName" =>  $optionName->title ?? '',
    "mobileBarcode" => $optionName->mobileBarcode,
    //"meetingPointLat" =>  $optionName->meetingPointLat ?? '',
    //"meetingPointLong" =>  $optionName->meetingPointLong ?? '',
    //"meetingPoint" =>  $optionName->meetingPoint ?? '',
    //"addresses" => json_decode($optionName->addresses),
    //"guides" => json_decode($model->guides),
    //"guideImages" => $guide_images,
    "bookingItems" => $bookingItems,
    "bookings" => json_decode($model->bookings),
    "clock_in" => $model->clock_in->format('Y-m-d H:i:s'),
    "clock_out" => $model->clock_out->format('Y-m-d H:i:s'),
    "diff" => $model->diff,
    "name" => ucwords($firstName),
    "surname" => ucwords($lastName),
    "customerEmail" => $customer_email,
    "bookingID" => $booking_id,
    "role" => "Customer",
    "supplier" => $supplierInformations,
    "logoURL" => $logoURL,
    "created_at" => $model->created_at->format('Y-m-d H:i:s'),
    "updated_at" => $model->updated_at->format('Y-m-d H:i:s')

     ];

    });

    if($targetMeeting->count()){
      return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $targetMeeting]);
    }

    else{



      foreach ($targetBooking as $book) {
         $productTitle = null;

      if($book->gygBookingReference === null &&  $book->is_tootbus == 0 && $book->isBokun == 0 && $book->isViator == 0){
       if(Product::where("referenceCode", $book->productRefCode)->count()){
          $product = Product::where("referenceCode", $book->productRefCode)->first();
          $productTitle = $product->title;
       }
      }

        $bookingItems = json_decode($book->bookingItems);
        $option = Option::where('referenceCode', $book->optionRefCode)->first();

         $firstName = json_decode($book->travelers, true)[0]["firstName"];
         $lastName = json_decode($book->travelers, true)[0]["lastName"];
         $customer_email = json_decode($book->travelers, true)[0]["email"];
         $companyID = $book->companyID;
         $gygBookingReference = $book->gygBookingReference;
          $bookingRefCode = $book->bookingRefCode;
          $isBokun = $book->isBokun;
          $isViator = $book->isViator;

             if($companyID == -1){
        $supplierInformations = [];
        $supplierInformations["companyName"] = "Global Tours and Tickets";
        $supplierInformations["email"] = "contact@parisciptrips.com";
        $supplierInformations["phoneNumber"] = "+33184208801";

        }else{
            $supplier = Supplier::findOrFail($companyID);
            $supplierInformations = [];
            $supplierInformations["companyName"] = $supplier->companyName;
            $supplierInformations["email"] = $supplier->email;
            $supplierInformations["phoneNumber"] = $supplier->countryCode.$supplier->phoneNumber;

        }
         $logoURL = $this->getLogo($gygBookingReference, $bookingRefCode, $isBokun, $isViator);

         if(strpos($book->dateTime, "dateTime") !== false){

            $dateTime = json_decode($book->dateTime, true)[0]["dateTime"];
            $date = Carbon::parse($dateTime)->format('Y-m-d');
            $hour = json_decode($book->hour, true)[0]["hour"];



         }else{

            $dateTime = $book->dateTime;
            $date = Carbon::parse($dateTime)->format('Y-m-d');
            $hour = Carbon::parse($dateTime)->format('H:i');

            if(empty($hour)){
                $hour = Carbon::parse($dateTime)->format('H:i');
            }else{
                $hour = $hour;
            }

         }

        $response = [
            "token" => $token,
            "hasGuide" => false,
            "date" => $date,
            "time" => $hour,
            //"operatingHours" => json_decode($model->operating_hours),

            "bookingItems" => $bookingItems,
            "option" => $option->referenceCode ?? '',
            "productTitle" => $productTitle,
            "optionName" =>  $option->title ?? '',
            "mobileBarcode" => $option->mobileBarcode,
            "name" => ucwords($firstName),
            "surname" => ucwords($lastName),
            "customerEmail" => $customer_email,
            "bookingID" => $book->id,
            "role" => "Customer",
            "supplier" => $supplierInformations,
            "logoURL" => $logoURL,
            "created_at" => $book->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $book->updated_at->format('Y-m-d H:i:s')





        ];

        break;

      }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => [$response]]);
    }




   }

   protected function getLogo($gygBookingReference, $bookingRefCode, $isBokun, $isViator){
     $logoURL = '';

     if($isBokun == 1){
        $logoURL = asset('logos/bokun.png');
     }
     elseif($isViator == 1) {
        $logoURL = asset('logos/viator.png');
     }
     elseif(!empty($gygBookingReference)){
        $logoURL = asset('logos/getyourguide.png');
     }
     elseif (preg_match("/BR-/",$bookingRefCode)) {
         $logoURL = asset('logos/viator.png');
     }else{
        $logoURL = asset('logos/cityzore.png');
     }

     return $logoURL;
   }


   public function getGuideInformationForCustomer(Request $request){
    if(!Meeting::where('id', $request->meeting_id)->count()){
        return response()->json(['status' => 'error', 'statusCode' => 200, 'data' => []]);
    }
    $meeting = Meeting::find($request->meeting_id);
    if(empty($meeting->guides)){
        $meeting->guides = "[]";
    }

    $guides = json_decode($meeting->guides, true);
    $guide_images = GuideImage::whereIn("guide_imageable_id", json_decode($meeting->guides, true))->pluck('src', 'guide_imageable_id')->toArray();

    $guides = Admin::whereIn("id",$guides)->whereJsonDoesntContain("roles", "Others")->pluck("id")->toArray();
    $guidesData = Admin::whereIn("id",$guides)->whereJsonDoesntContain("roles", "Others")->get()->map(function($obj){
        return [
            "id" => $obj->id,
            "name" => $obj->name,
            "surname" => $obj->surname
        ];
    });

    $response = [
    "guides" => $guides,
    "guideImages" => $guide_images,
    "guidesData" => $guidesData
    ];

    return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);


   }


   public function getMeetingPointsForCustomer(Request $request){

    if(!Option::where('referenceCode', $request->optionRefCode)->count()){
       return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'Undefined OptionReferenceCode!']], 400);
    }

    $option = Option::where('referenceCode', $request->optionRefCode)->first();

    $response = [
    "meetingPointLat" =>  $option->meetingPointLat ?? '',
    "meetingPointLong" =>  $option->meetingPointLong ?? '',
    "meetingPoint" =>  $option->meetingPoint ?? '',
    "addresses" => json_decode($option->addresses),
    ];

    return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);



   }




   public function getCancelPolicyByOption(Request $request){


       $option = Option::where('referenceCode', $request->optionRefCode)->first();

       if(!$option){
        return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'Undefined Option!']], 400);
       }



       $response = [
        "cancelPolicyTime" => $option->isFreeCancellation == 0 ? -1 : $option->cancelPolicyTime,
        "cancelPolicyTimeType" => $option->cancelPolicyTimeType
       ];

       return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);


   }



   public function getBarcodeForCustomer(Request $request){

       $cryptRelated = new CryptRelated();
       $booking_id = $request->bookingID;



         $barcode = [];
     $bookingExtraFiles = [];

     if(Barcode::where('bookingID', $booking_id)->count()){

       $barcode = Barcode::where('bookingID', $booking_id)->where('ticketType',4)->first();
     }

     if(BookingImage::where("booking_id", $booking_id)->count()){
        $bookingExtraFiles = BookingImage::where("booking_id", $booking_id)->get();
     }




          $response =  [
        "barcode" => $barcode,
        "bookingExtraFiles" => $bookingExtraFiles,
        "voucher" => "https://www.cityzore.com/print-pdf-frontend/".$cryptRelated->encrypt($booking_id)

         ];



         return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);




   }





   public function cancelBookingByCustomer(Request $request){

    try {

   $request->status = 3;
    $bookingController = new BookingController();
    $response = $bookingController->changeStatus($request);

    return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);

    } catch (\Exception $e) {
      return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => $e->getMessage()]], 400);

    }






   }


   public function setCustomerLog(Request $request){





      try {

      $customerLog = new CustomerLoginLog();
      $customerLog->booking_id  = $request->bookingID;
      $customerLog->referenceCode = $request->referenceCode;
      $customerLog->customerEmail = $request->customerEmail;
      $customerLog->customerName = $request->customerName;
      $customerLog->action = $request->action;



    if($customerLog->save()){

     return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => 'Customer Log Saved Successfully!']);

    }
    return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => "An Error Occurred!"]], 400);

    } catch (\Exception $e) {
      return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => $e->getMessage()]], 400);

    }







   }



   protected function parseTime($dateTime){

   	$dates = [];

      if(strpos($dateTime, 'dateTime') !== false && strlen($dateTime) > 1){

            foreach (json_decode($dateTime, true) as $dates) {
             $arr[] = explode('+', explode('T', $dates["dateTime"])[1])[0];


            }


             }else{

               if(strlen($dateTime) > 1)
               $arr[] = explode('+', explode('T', $dateTime)[1])[0];


             }

              $arr = array_unique($arr);
              return $arr;

   }




}
