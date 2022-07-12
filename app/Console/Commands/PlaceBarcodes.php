<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Booking;
use Carbon\Carbon;
use App\Barcode;

class PlaceBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'place:barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Place Barcodes Description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bookings = Booking::where(function($q) {
            $q->where('gygBookingReference', '!=', null)->orWhere('isBokun', 1)->orWhere('isViator', 1);
        })->whereDate('dateForSort', '>=', Carbon::today())->get();

        foreach($bookings as $booking) {
            if(count(Barcode::where('bookingID', $booking->id)->get()) <= 0) {
            $dateTime = Carbon::parse($booking->dateTime)->format('d/m/Y H:i');
            $option = $booking->bookingOption;

            if($option && $option->ticket_types) {
                $cancelPolicyTime = $option->cancelPolicyTime;
                $cancelPolicyTimeType = $option->cancelPolicyTimeType;

                $status = false;

                switch ($cancelPolicyTimeType) {
                    case 'd':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInDays(Carbon::now()) < $cancelPolicyTime;
                        break;
                    case 'h':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInHours(Carbon::now()) < $cancelPolicyTime;
                        break;
                    case 'm':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInMinutes(Carbon::now()) < $cancelPolicyTime;
                        break;

                    default:
                        // code...
                        break;
                }

                //dd([$dateTime, $cancelPolicyTime, $cancelPolicyTimeType]);

                if($status) {
                    $totalBarcodeCount = 0;
                    $bookingItems = json_decode($booking->bookingItems, true);
                    $pricing = $option->pricings()->get();

                    if(!$pricing[0]->ignoredCategories) {
                        foreach($bookingItems as $bookingItem) {
                            $totalBarcodeCount += $bookingItem["count"];
                        }
                    } else {
                        foreach($bookingItems as $bookingItem) {
                            $inIgnoredCategory = false;
                            foreach(json_decode($pricing[0]->ignoredCategories) as $ignoredCategory) {
                                if(strtolower($bookingItem["category"]) == strtolower($ignoredCategory)) {
                                    $inIgnoredCategory = true;
                                    break;
                                }
                            }
                            if(!$inIgnoredCategory)
                                $totalBarcodeCount += $bookingItem["count"];
                        }
                    }

                    if($totalBarcodeCount > 0) {
                        $ticketTypes = $option->ticket_types()->get();
                        foreach($ticketTypes as $ticketType) {
                            $barcodes = Barcode::where('isUsed', 0)->where('ticketType', $ticketType->id)->where('isReserved', 0)->where('isExpired', 0)->get();
                            for($i=0; $i<$totalBarcodeCount; $i++) {
                                if(count($barcodes) >= $totalBarcodeCount) {
                                    $barcode = $barcodes[$i];
                                    $barcode->isUsed = 1;
                                    $barcode->bookingID = $booking->id;
                                    $barcode->save();
                                }
                            }
                        }
                    }
                }
            }
            }
        }
    }
}
