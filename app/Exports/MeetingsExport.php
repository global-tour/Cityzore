<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MeetingsExport implements FromView, ShouldAutoSize
{

	public function __construct($responseArray, $currentDate, $currentTime, $supplierID){
     $this->responseArray = $responseArray;
     $this->currentDate = $currentDate;
     $this->currentTime = $currentTime;
     $this->supplierID = $supplierID;

	}


    public function view(): View
    {
        return view('panel.bookings.meetings.excel.meetings', [
            'responseArray' => $this->responseArray,  
            'currentDate' => $this->currentDate,  
            'currentTime' => $this->currentTime,  
            'supplierID' => $this->supplierID,  
        ]);
    }
}