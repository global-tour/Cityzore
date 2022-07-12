<?php

namespace App\Imports;

use App\Barcode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BarcodePompidouImport implements ToCollection
{
    public $success_data = ["counter" => 0, "items" => []];
    public $failed_data = ["counter" => 0, "items" => []];
    protected $userID;

    public function __construct($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {

            if ($key > 6) {

                try {
                    if (Barcode::where("code", trim($row[13]))->count()) {
                        $this->failed_data["counter"]++;
                        $this->failed_data["items"][] = trim($row[13]);
                        continue;
                    }
                    $endTime = Carbon::now()->addYear()->format('d/m/Y');

                    $barcode = new Barcode();

                    $barcode->endTime = $endTime; // OK
                    $barcode->reservationNumber = null; // OK
                    $barcode->code = trim($row[13]); // OK
                    $barcode->isUsed = 0; // OK
                    $barcode->isReserved = 0; // OK
                    $barcode->isExpired = 0; // OK
                    $barcode->ownerID = $this->userID;
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->recode = null;
                    $barcode->contact = $row[7];
                    $barcode->ticketType = 27;
                    $barcode->description = null;
                    $barcode->searchableTicketType ='Pompidou';

                    if ($barcode->save()) {
                        $this->success_data["counter"]++;
                        $this->success_data["items"][] = trim($row[13]);
                    }


                } catch (\Exception $e) {

                    dd($e->getMessage());

                }

            }
        }

        return response()->json(["success" => $this->success_data, "failed" => $this->failed_data]);


    }

}
