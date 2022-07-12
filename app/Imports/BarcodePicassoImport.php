<?php

namespace App\Imports;

use App\Barcode;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class BarcodePicassoImport implements ToCollection
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
            if ($key > 6) {
                try{
                    if(barcode::where("code", trim($row[6]))->count()){
                        $this->failed_data["counter"]++;
                        $this->failed_data["items"][] = trim($row[5]);
                        continue;
                    }
                    if(!($row[0] == null))
                    {
                        $barcode = new Barcode();
                        $endTime = Carbon::now()->addYear()->format('d/m/Y');
                        $barcode->endTime = $endTime; // OK
                        $barcode->reservationNumber = trim($row[0]); // OK
                        $barcode->code = trim($row[13]); // OK
                        $barcode->isUsed = 0; // OK
                        $barcode->isReserved = 0; // OK
                        $barcode->isExpired = 0; // OK
                        $barcode->ownerID = $this->ownerID;
                        $barcode->cartID = null;
                        $barcode->bookingID = null;
                        $barcode->recode = null;
                        $barcode->contact = null;
                        $barcode->ticketType = 29;
                        $barcode->description = null;
                        $barcode->searchableTicketType = "Picasso Museum";

                        if($barcode->save()){
                            $this->success_data["counter"]++;
                            $this->success_data["items"][] = trim($row[5]);
                        }

                    }

                }catch(\Exception $e){

                    dd($e->getMessage());
                }

            }
        }
        return response()->json(["success" => $this->success_data, "failed" => $this->failed_data]);

    }
}
