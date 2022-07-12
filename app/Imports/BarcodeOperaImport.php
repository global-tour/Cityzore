<?php

namespace App\Imports;

use App\Barcode;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;


class BarcodeOperaImport implements ToCollection
{
    public $success_data = ["counter" => 0, "items" => []];
    public $failed_data = ["counter" => 0, "items" => []];
    public $ownerID;

    public function __construct($ownerID)
    {

        $this->ownerID = $ownerID;
    }

    public function collection(Collection $rows)
    {

        foreach ($rows as $key => $row) {
            if ($key > 6) {

                //$row = explode(";", $row);

//                dd($row);


                try {

                    if (barcode::where("code", trim($row[5]))->count()) {
                        $this->failed_data["counter"]++;
                        $this->failed_data["items"][] = trim($row[5]);
                        continue;
                    }

                    $barcode = new Barcode();


                    $endTime = Carbon::now()->addYear()->format('d/m/Y');

                    $barcode->endTime = $endTime; // OK
                    $barcode->reservationNumber = trim($row[0]); // OK
                    $barcode->code = trim($row[5]); // OK
                    $barcode->isUsed = 0; // OK
                    $barcode->isReserved = 0; // OK
                    $barcode->isExpired = 0; // OK
                    $barcode->ownerID = $this->ownerID;
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->recode = trim($row[1]);
                    $barcode->contact = $row[2];
                    $barcode->ticketType = 7;
                    $barcode->description = null;
                    $barcode->searchableTicketType = "Opera National";

                    if ($barcode->save()) {
                        $this->success_data["counter"]++;
                        $this->success_data["items"][] = trim($row[5]);
                    }


                } catch (\Exception $e) {

                    dd($e->getMessage());

                }


            }
        }

        return response()->json(["success" => $this->success_data, "failed" => $this->failed_data]);


    }
}
