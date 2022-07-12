<?php

namespace App\Console\Commands;

use App\Av;
use App\Booking;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Option;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSolds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:solds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates sold tickets daily';

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
        $summitAvailability = Av::where('id', 1)->first()->hourly;
        $options = Option::get();
        $arr = [];
        //Find options tied to id #1
        foreach($options as $option) {
            foreach($option->avs()->get() as $av)
            if($av->id == 1) {
                array_push($arr, $option->referenceCode);
            }
        }
        $now = Carbon::now();
        $bookings= Booking::whereIn('optionRefCode', $arr)->where('status', 0)->where('dateForSort', '>=', $now)->get();
        $arr = [];
        foreach ($bookings as $booking) {
            if(!($booking->productRefCode == null) && $booking->isBokun == 0 && $booking->isViator == 0) {
                array_push($arr, ['id' => $booking->id, 'dateTime' => explode('+', json_decode($booking->dateTime)[0]->dateTime)[0], 'bookingItems' => json_decode($booking->bookingItems)]);
            }
            else {
                array_push($arr, ['id' => $booking->id, 'dateTime' => explode('+', $booking->dateTime)[0], 'bookingItems' => json_decode($booking->bookingItems)]);
            }
        }
        //dd($arr);
        foreach ($arr as $key => $item) {
            $arr2[$item['dateTime']][$key] = $item; //gathers the matches
        }
        $i = 0;
        //dd($arr2);
        foreach($arr2 as $arr) {

            $itemSum = 0;
            foreach($arr as $a) {
                foreach($a['bookingItems'] as $items) {
                    $itemSum = $itemSum + $items->count;
                }
            }
            $dateArray = [];
            $day = date("d/m/Y", strtotime(array_reverse($arr)[0]['dateTime']));
            $time = date("H:i", strtotime(explode("+", array_reverse($arr)[0]['dateTime'])[0]));
            array_push($dateArray, ['day' =>$day, 'hour' => $time, 'count' => $itemSum]);
            $hourlyDecoded = json_decode($summitAvailability, true);
            $apiRelated = new ApiRelated();
            $jsonq = $apiRelated->prepareJsonQ();
            $res = $jsonq->json($summitAvailability);
            $result = $res->where('day', '=', $day)->where('hour', '=', $time)->get();
            if (count($result) == 1) {
                $key = key($result);
                $hourlyDecoded[$key]['sold'] = $itemSum;
                $summitAvailability = json_encode($hourlyDecoded);
                //dd($summitAvailability);

                Av::where('id',1)->update(['hourly' => $summitAvailability]);
            }
            $i++;
        }
    }
}
