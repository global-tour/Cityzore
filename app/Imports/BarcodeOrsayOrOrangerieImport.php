<?php

namespace App\Imports;

use App\Barcode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarcodeOrsayOrOrangerieImport implements ToCollection
{
    public $success_data = ["counter" => 0, "items" => []];
    public $failed_data = ["counter" => 0, "items" => []];
    protected $userID;
    protected $barcodeType;

    public function __construct($userID, $barcodeType)
    {
        $this->userID = $userID;
        $this->barcodeType = $barcodeType;
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {

            if ($key > 0) {

                try {
                    if (Barcode::where("code", trim($row[0]))->count()) {
                        $this->failed_data["counter"]++;
                        $this->failed_data["items"][] = trim($row[0]);
                        continue;
                    }

                    $barcode = new Barcode();


                    $endTime = $row[1];

                    $barcode->endTime = $endTime; // OK
                    $barcode->reservationNumber = null; // OK
                    $barcode->code = trim($row[0]); // OK
                    $barcode->isUsed = 0; // OK
                    $barcode->isReserved = 0; // OK
                    $barcode->isExpired = 0; // OK
                    $barcode->ownerID = $this->userID;
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->recode = null;
                    $barcode->contact = null;
                    $barcode->ticketType = $this->barcodeType;
                    $barcode->description = null;
                    $barcode->searchableTicketType = $this->barcodeType == 32 ? "Orsay" : 'Orangerie';

                    if ($barcode->save()) {
                        $this->success_data["counter"]++;
                        $this->success_data["items"][] = trim($row[0]);
                    }


                } catch (\Exception $e) {

                    dd($e->getMessage());

                }

            }
        }

        return response()->json(["success" => $this->success_data, "failed" => $this->failed_data]);


    }

}
