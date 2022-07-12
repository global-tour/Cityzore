<?php

namespace App\Imports;

use App\Barcode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BarcodeRodinImport implements ToCollection
{
    public $success_data = ["counter" => 0, "items" => []];
    public $failed_data = ["counter" => 0, "items" => []];
    public $ownerID;
    public function __construct($ownerID){

        $this->ownerID = $ownerID;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($key > 0) {
                try{
                    if(barcode::where("code", trim($row[0]))->count()){
                        $this->failed_data["counter"]++;
                        $this->failed_data["items"][] = trim($row[0]);
                        continue;
                    }

                    $barcode = new Barcode();

                    $endTime = Carbon::now()->addYear()->format('d/m/Y');

                    $barcode->endTime = $endTime;
                    $barcode->reservationNumber = null;
                    $barcode->code = trim($row[0]);
                    $barcode->isUsed = 0;
                    $barcode->isReserved = 0;
                    $barcode->isExpired = 0;
                    $barcode->ownerID = $this->ownerID;
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->recode = null;
                    $barcode->contact = null;
                    $barcode->ticketType = 28;
                    $barcode->description = null;
                    $barcode->searchableTicketType = "Rodin Ticket";

                    if($barcode->save()){
                        $this->success_data["counter"]++;
                        $this->success_data["items"][] = trim($row[0]);
                    }


                }catch(\Exception $e){

                    dd($e->getMessage());

                }













            }
        }

        return response()->json(["success" => $this->success_data, "failed" => $this->failed_data]);



    }
}
