<?php

namespace App\Exports;

use App\Cart;
use App\Config;
use App\Supplier;
use App\User;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use App\Booking;
use Maatwebsite\Excel\Concerns\FromArray;

class FinancesExportForCommissioner extends DefaultValueBinder implements WithHeadings, WithMapping, FromArray, ShouldAutoSize, WithEvents
{

    public $financesRequest;

    public function __construct($financesRequest)
    {
        $this->financesRequest = $financesRequest;
    }

    protected function findCategory($items, $name){
     foreach ($items as $item) {
        if($item["category"] == $name)
         return $item["count"];
     }

   return 0;

    }

    public function map($finances): array
    {
        $excelArr = array();

        $excelArr[0] = $finances[0];
        $excelArr[1] = $finances[1];
        $excelArr[2] = $finances[2];
        $excelArr[3] = $finances[3];
        $excelArr[4] = $finances[4];
        $excelArr[5] = $finances[5];
        $excelArr[6] = $finances[6];
        $excelArr[7] = $finances[7];
        $excelArr[8] = $finances[8];
        $excelArr[9] = $finances[9];
        $excelArr[10] = $finances[10];
        $excelArr[11] = $finances[11];
        $excelArr[12] = $finances[12];
        $excelArr[13] = $finances[13];
        $excelArr[14] = $finances[14];
        $excelArr[15] = $finances[15];
        $excelArr[16] = $finances[16];
        $excelArr[17] = $finances[17];
        $excelArr[18] = $finances[18];

        return $excelArr;
    }

    public function array() : array {
        $queryModel = new Booking();
        $financeMonth = $this->financesRequest->financeMonth;
        $financeYear = $this->financesRequest->financeYear;
        $companyID = $this->financesRequest->companyID;
        $commissioner = $this->financesRequest->commissioner;

        $config = Config::where('userID', -1)->first();

        $financeBookings = null;
        if (!is_null($companyID) && $companyID != 0) {
            $financeBookings = $queryModel::where('gygBookingReference', null)->where('companyID', $companyID)->where('status', 0)
                ->whereMonth('dateForSort', $financeMonth)->whereYear('dateForSort', $financeYear)->get();
            $commissionRate = 0;
            if ($companyID != '-1') {
                $commissionRate = Supplier::where('id', $companyID)->first()->comission;
            }
        }

        if (!is_null($commissioner) && $commissioner != 0) {
            $financeBookings = $queryModel::where('gygBookingReference', null)->where(function($q) use($commissioner){
                $q->where('userID', $commissioner);
                $q->orWhere('affiliateID', $commissioner);
            })->where('status', 0)
                ->whereMonth('dateForSort', $financeMonth)->whereYear('dateForSort', $financeYear)->get();
            $commissionRate = User::where('id', $commissioner)->first()->commission;
        }

        $excelArr = array();
        $totalRate = 0;
        $comTotal  = 0;
        $creditTotal = 0;
        foreach ($financeBookings as $financeBooking) {
            $dateForSort =  \Carbon\Carbon::parse($financeBooking->dateForSort)->format('d/m/Y');
            $travelers = json_decode($financeBooking->travelers, true);
            $bookingItems = json_decode($financeBooking->bookingItems, true);

            $paymentMethod = \App\Invoice::where('bookingID', $financeBooking->id)->first()->paymentMethod ?? '';



            $fbTotalPrice = $financeBooking->totalPrice;
            $currencyValue = $config->currencyName->value;
            if (!is_null($companyID)) {
                $totalRate = $totalRate + $fbTotalPrice - ($fbTotalPrice * ($commissionRate) / 100);
            }
            if (!is_null($commissioner)) {
                $totalRate = $totalRate + $fbTotalPrice * ($commissionRate) / 100;
            }

            $bookingRefCode = '';
            $explodedBookingRefCodes = explode('-', $financeBooking->bookingRefCode);
            $items = [];
            foreach ($explodedBookingRefCodes as $refCode) {
                $items['ref'] = $refCode;
                if (strpos($refCode, "BK")  !== false) {
                    $bookingRefCode = $refCode;
                }
            }

            $bookingDate = $financeBooking->created_at->format('d/m/Y');
            $bookingProductRefCode = explode('-', $financeBooking->reservationRefCode)[0];
            $bookingOptionRefCode = $financeBooking->optionRefCode;
            //$bookingRetailRateValue = $config->calculateCurrency($fbTotalPrice, $currencyValue);
            $bookingRetailRateValue = $fbTotalPrice;
            $bookingRetailRate = $config->currencyName->currency . ' ' . $bookingRetailRateValue;
            if ($companyID == -1 || !is_null(Supplier::where('id', $companyID)->first())) {
                $bookingNetRateValue = $config->calculateCurrency($fbTotalPrice - ($fbTotalPrice * ($commissionRate) / 100), $currencyValue);
            } else if (!is_null($commissioner)) {

                $cart = Cart::where('referenceCode', $financeBooking->reservationRefCode)->first();
                if (!empty($cart->tempCommission) && $cart->tempCommission > 0) {
                    $bookingNetRateValue = $cart->tempCommission;

                    if ($paymentMethod == "COMMISSION") {
                        $cmt = 1;
                        $crt = 0;
                        $diffCom = $cart->totalPrice - $cart->tempCommission;
                        $diffCredit = $cart->tempCommission;
                        $comTotal += $diffCom;
                    }else{

                        $cmt = 0;
                        $crt = 1;
                        $diffCom = $cart->totalPrice - $cart->tempCommission;
                        $diffCredit = $cart->tempCommission;
                        $creditTotal += $diffCredit;

                    }

                }
                else {
                    $bookingNetRateValue = $cart->totalCommission;

                      if ($paymentMethod == "COMMISSION") {
                        $cmt = 1;
                        $crt = 0;
                        $diffCom = $cart->totalPrice - $cart->totalCommission;
                        $diffCredit = $cart->totalCommission;
                        $comTotal += $diffCom;
                    }else{

                        $cmt = 0;
                        $crt = 1;
                         $diffCom = $cart->totalPrice - $cart->totalCommission;
                        $diffCredit = $cart->totalCommission;
                        $creditTotal += $diffCredit;

                    }


                    //$bookingNetRateValue = $config->calculateCurrency($fbTotalPrice * ($commissionRate) / 100, $currencyValue);
                }
            }

            // $bookingNetRate = $config->currencyName->currency . ' ' . $bookingNetRateValue;

            array_push($excelArr, [
                $bookingRefCode,
                $dateForSort,
                $bookingDate,
                $bookingProductRefCode,
                $bookingOptionRefCode,

                $travelers[0]["firstName"],
                $travelers[0]["lastName"],
                $travelers[0]["phoneNumber"],
                $travelers[0]["email"],

                $this->findCategory($bookingItems, "ADULT"),
                $this->findCategory($bookingItems, "EU_CITIZEN"),
                $this->findCategory($bookingItems, "YOUTH"),
                $this->findCategory($bookingItems, "CHILD"),
                $this->findCategory($bookingItems, "INFANT"),
                $paymentMethod,




                $bookingRetailRate,
                $paymentMethod == "COMMISSION" ? $config->currencyName->currency.' '.$diffCom : '',
                $paymentMethod != "COMMISSION" ? $config->currencyName->currency.' '.$diffCredit : '',
                ''
            ]);
        }

         array_push($excelArr, [
                '',
                '',
                '',
                '',
                '',

                '',
                '',
                '',
                '',

                '',
                '',
                '',
                '',
                '',
                '',



                '',
                 '',
                '',
                $config->currencyName->currency.' '. ($creditTotal-$comTotal)
            ]);


             if($companyID != 0){


                        $extraPayment = \App\ExternalPayment::where('is_paid', 1)->where('createdBy', $companyID)
                                    ->whereDate('updated_at','<=', \Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d H:i:s'))
                                    ->get();

                                     $currency = \App\Currency::where('isActive', true)->get();
                                     $euro = $currency->where('currency', 'EUR')->first();



                                      foreach($extraPayment as $payment){
                                        $oran = $euro->value / $currency->where('currency', $payment->currency_code)->first()->value;
                                        $price = $payment->price * $oran;
                                        $totalRatee = $price > 0 ? $price : 0;
                                        $netRatee = $price > 0 ? $price : 0;
                                        $totalRatee = number_format($totalRatee,2);
                                        $netRatee = number_format($netRatee,2);


                                        array_push($excelArr, [
                                            $payment->referenceCode,
                                            '---', // arrival date
                                            $payment->created_at->format('d/m/Y'),
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',
                                            '---',



                                            $config->currencyName->currency.' '.$totalRatee,
                                            $config->currencyName->currency.' '.$netRatee,
                                            $config->currencyName->currency.' '.$netRatee,
                                            ''
                                        ]

                                        );
                                    }
              }


        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        return $excelArr;

    }

    public function headings(): array
    {
        return [
            'Reference No.',
            'Arrival Date.',
            'Sale Date',
            'Product Ref.',
            'Option Ref. Code',

            'Travelers First Name',
            'Travelers Last Name',
            'Travelers Phone Number',
            'Travelers Email',
            'Adult',
            'EU Citizen',
            'Youth',
            'Child',
            'Infant',
            'Payment Method',

            'Retail Rate',
            'Com Rate',
            'Credit Rate',
            'Total Diff Rate',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $event->sheet->styleCells(
                    'A1:S1',
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

}
