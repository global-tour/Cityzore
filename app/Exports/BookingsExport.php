<?php

namespace App\Exports;

use App\Platform;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use App\Booking;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Invoice;

class BookingsExport extends DefaultValueBinder implements WithCustomValueBinder, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithEvents
{

    public $bookingsRequest;
    public $timeRelatedFunctions;

    public function __construct($bookingsRequest)
    {
        $this->bookingsRequest = $bookingsRequest;
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $event->sheet->styleCells(
                    'A1:AC1',
                    [
                        //border Style
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'EB2B02'],
                            ],

                        ],

                        //font style
                        'font' => [
                            'name'      =>  'Calibri',
                            'size'      =>  15,
                            'bold'      =>  true,
                            'color' => ['argb' => 'EB2B02'],
                        ],

                        //background style
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'dff0d8',
                            ]
                        ],
                    ]
                );
            },
        ];
    }
    public function headings(): array
    {
        return [
            'Product Index',
            'Product Title',
            'Option Ref. Code',
            'Option Title',
            'Status',
            'Reservation Ref. Code',
            'Booking Ref. Code',
            'GYG Ref. Code',
            'Adult',
            'Eu Citizen',
            'Youth',
            'Child',
            'Infant',
            'Total Price',
            'Currency',
            'Comment',
            'Booking Date and Time',
            'Language',
            'Traveler Hotel',
            'Traveler E-Mail',
            'Traveler First Name',
            'Traveler Last Name',
            'Traveler Phone Number',
            'Full Name',
            'Special Ref. Code',
            'Admin Comment',
            'Big Bus Ref. Code',
            'Platforms',
            //'Commissioner Name',
            'Mail Check',
            'Booked Date'
        ];
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
    public function collection() {
        $queryModel = new Booking();

        $from = '';
        $to = '';

        $cFrom = '';
        $cTo = '';

        if ($this->bookingsRequest->has('from') && $this->bookingsRequest->has('to')) {
            $from = $this->bookingsRequest->from;
            $to = $this->bookingsRequest->to;
        }
        if ($this->bookingsRequest->has('cFrom') && $this->bookingsRequest->has('cTo')) {
            $cFrom = $this->bookingsRequest->cFrom;
            $cTo = $this->bookingsRequest->cTo;
        }

        $payment_supplier = "";
        $payment_affiliate = "";
        $platforms=$this->bookingsRequest->get('platforms')!= null ? array_map('intval',explode(',',$this->bookingsRequest->get('platforms'))):null;
        $commissioner=intval($this->bookingsRequest->get('commissionID'));
        $approvedBookings = '1';
        $pendingBookings = '1';
        $cancelledBookings = '1';
        $paymentMethod = '';
        $selectedOption = '';

        $bookingNumber = "";
        $invoiceID = "";
        $travelerName = "";
        if ($this->bookingsRequest->has('paymentMethod')){
            $paymentMethod = $this->bookingsRequest->paymentMethod;
        }
        if ($this->bookingsRequest->has('approvedBookings')) {
            $approvedBookings = $this->bookingsRequest->approvedBookings;
        }
        if ($this->bookingsRequest->has('pendingBookings')) {
            $pendingBookings = $this->bookingsRequest->pendingBookings;
        }
        if ($this->bookingsRequest->has('cancelledBookings')) {
            $cancelledBookings = $this->bookingsRequest->cancelledBookings;
        }
        if ($this->bookingsRequest->has('payment_supplier')) {
            $payment_supplier = $this->bookingsRequest->payment_supplier;
        }
        if ($this->bookingsRequest->has('payment_affiliate')) {
            $payment_affiliate = $this->bookingsRequest->payment_affiliate;
        }
        if ($this->bookingsRequest->has('selectedOption') && $this->bookingsRequest->selectedOption != null) {
            $selectedOption = $this->bookingsRequest->selectedOption;
            $selectedOption = explode(',', $selectedOption);
        }
        if ($this->bookingsRequest->has('bookingNumber')) {
            $bookingNumber = $this->bookingsRequest->bookingNumber;
        }
        if ($this->bookingsRequest->has('invoiceID')) {
            $invoiceID = $this->bookingsRequest->invoiceID;
        }
        if ($this->bookingsRequest->has('travelerName')) {
            $travelerName = $this->bookingsRequest->travelerName;
        }

        $queryModel = $queryModel::where('status', '!=', 1);

        if ($payment_supplier != '') {
            $queryModel = $queryModel->where('companyID', $payment_supplier)->orWhereHas('bookingOption', function($subQuery) use($payment_supplier) {
                $subQuery->where('rCodeID', $payment_supplier);
            });
        }
        if ($payment_affiliate != '') {
            $queryModel = $queryModel->where('affiliateID', $payment_affiliate);
        }
        if($selectedOption != '') {
            $queryModel = $queryModel->whereIn('optionRefCode', $selectedOption);
        }
        if (!empty($paymentMethod)) {
            $queryModel = $queryModel->whereHas('invoc', function ($query) use($paymentMethod) {
                $query->where('paymentMethod', $paymentMethod);
            });
        }
        if ($from != '' && $to != '') {
            $queryModel = $queryModel->whereBetween(DB::raw('DATE(dateForSort)'), [date($from), date($to)]);
        }
        if ($cFrom != '' && $cTo != '') {
            $cFrom=$cFrom.' 00:00:00';
            $cTo=$cTo.' 23:59:59';
            $queryModel = $queryModel->whereBetween(DB::raw('DATE(created_at)'), [date($cFrom), date($cTo)]);
        }
        if ($platforms != null) {
            $queryModel = $queryModel->whereIn('platformID',$platforms);
        }
        if ($commissioner != 0) {
            $queryModel = $queryModel->where('userID',$commissioner);
        }
        if ($approvedBookings == '0') {
            $queryModel = $queryModel->where('status', '!=', 0);
        }
        if ($pendingBookings == '0') {
            $queryModel = $queryModel->whereNotIn('status', [4, 5]);
        }
        if ($cancelledBookings == '0') {
            $queryModel = $queryModel->whereNotIn('status', [2, 3]);
        }
        if ($bookingNumber != '') {
            $searchLast = $bookingNumber;
            if(str_contains($searchLast,'-')) $searchLast=str_replace('-','',$searchLast);
            if(substr($searchLast,0,2)=="BR") $searchLast=substr($searchLast,0,2).'-'.substr($searchLast,2);
            $queryModel = $queryModel->where(function ($query) use ($searchLast) {
                $query->where('gygBookingReference', 'like', '%' . $searchLast . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $searchLast . '%');
            });
        }
        if ($invoiceID != '') {
            $searchLast = $invoiceID;
            $invodID = Invoice::select(['bookingID'])->where('referenceCode', 'like', '%' . $searchLast . '%')->get()->pluck('bookingID')->toArray();
            $queryModel = $queryModel->whereIn('id', $invodID);
        }
        if ($travelerName != '') {
            $searchLast = $travelerName;
            $queryModel = $queryModel->where(function ($query) use ($searchLast) {
                $query->where('travelers', 'like', '%' . $searchLast . '%')
                    ->orWhere('fullName', 'like', '%' . $searchLast . '%');
            });
        }

        $queryModel = $queryModel->get();
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        return $queryModel;

    }
    public function map($bookings): array
    {

        $excelArr = [];
        $excelArr[0] = $bookings->bookingProduct ? $bookings->bookingProduct->id : "-";
        $excelArr[1] = $bookings->bookingProduct ? $bookings->bookingProduct->title : "-";
        $excelArr[2] = $bookings->optionRefCode;
        if (!is_null($bookings->bookingOption)) {
            $excelArr[3] = $bookings->bookingOption->title;
        } else {
            $excelArr[3] = '-';
        }
        $statusStr = '';
        if ($bookings->status == 0) {
            $statusStr .= 'Approved';
        }
        if (in_array($bookings->status, [4, 5])) {
            $statusStr .= 'Pending';
        }
        if (in_array($bookings->status, [2, 3])) {
            $statusStr .= 'Cancelled';
        }
        $excelArr[4] = $statusStr;
        $excelArr[5] = $bookings->reservationRefCode;
        $excelArr[6] = $bookings->bookingRefCode;
        $excelArr[7] = $bookings->gygBookingReference;
        $bookingItems = json_decode($bookings->bookingItems, true);
        $bookingItemsArr = [0, 0, 0, 0, 0];
        foreach ($bookingItems as $bi) {
            if($bi['category'] == 'ADULT')
                $bookingItemsArr[0] = $bi['count'];
            elseif($bi['category'] == 'EU_CITIZEN')
                $bookingItemsArr[1] = $bi['count'];
            elseif($bi['category'] == 'YOUTH')
                $bookingItemsArr[2] = $bi['count'];
            elseif($bi['category'] == 'CHILD')
                $bookingItemsArr[3] = $bi['count'];
            elseif($bi['category'] == 'INFANT')
                $bookingItemsArr[4] = $bi['count'];
        }
        $excelArr[8] = $bookingItemsArr[0] > 0 ? $bookingItemsArr[0] : "-";
        $excelArr[9] = $bookingItemsArr[1] > 0 ? $bookingItemsArr[1] : "-";
        $excelArr[10] = $bookingItemsArr[2] > 0 ? $bookingItemsArr[2] : "-";
        $excelArr[11] = $bookingItemsArr[3] > 0 ? $bookingItemsArr[3] : "-";
        $excelArr[12] = $bookingItemsArr[4] > 0 ? $bookingItemsArr[4] : "-";
        $excelArr[13] = $bookings->totalPrice;
        $excelArr[14] = $bookings->bookingCurrency->currency;
        $excelArr[15] = is_null($bookings->comment) ? '' : $bookings->comment;
        $dateTimeStr = '';
        if (is_null($bookings->gygBookingReference)) {
            $dateTimes = json_decode($bookings->dateTime, true);
            if (gettype($dateTimes) == 'string') {
                $dateTimeStr .= $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($bookings->dateTime);
            } else {
                foreach ((array) $dateTimes as $dt) {
                    $dateTimeStr .= $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($dt['dateTime']) . ' ';
                }
            }
        } else {
            $dateTimeStr = $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($bookings->dateTime, $bookings->bookingOption, $bookings);
        }

        $excelArr[16] = $dateTimeStr;
        $excelArr[17] = $bookings->language;
        $excelArr[18] = $bookings->travelerHotel;
        $tEmailStr = '';
        $tFirstNameStr = '';
        $tLastNameStr = '';
        $tPhoneNumberStr = '';
        $travelers = json_decode($bookings->travelers, true)[0];
        foreach ($travelers as $k => $t) {
            if ($k == 'email') {
                $tEmailStr .= $t;
            }
            if ($k == 'firstName') {
                $tFirstNameStr .= $t;
            }
            if ($k == 'lastName') {
                $tLastNameStr .= $t;
            }
            if ($k == 'phoneNumber') {
                $tPhoneNumberStr .= $t;
            }
        }

        $excelArr[19] = $tEmailStr;
        $excelArr[20] = $tFirstNameStr;
        $excelArr[21] = $tLastNameStr;
        $excelArr[22] = $tPhoneNumberStr;
        $excelArr[23] = $bookings->fullName;
        $excelArr[24] = $bookings->specialRefCode;
        $excelArr[25] = $bookings->adminComment;
        $excelArr[26] = $bookings->bigBusRefCode;
        $excelArr[27] = Platform::where('id',$bookings->platformID)->value('name');

        $contactMailLog = \App\BookingContactMailLog::where('booking_id', $bookings->id)->orderBy('id', 'desc')->first();
        if($contactMailLog) {
            $checkInformation = json_decode($contactMailLog->check_information, true);
            $excelArr[28] = $checkInformation["status"] ? ("Checked on " . $checkInformation["check_date"] . " by " . $checkInformation["checker"]) : "Unchecked";
        } else {
            $excelArr[28] = "-";
        }
        $excelArr[29] = Carbon::make($bookings->created_at)->format('d-m-Y');

        return $excelArr;
    }
}
