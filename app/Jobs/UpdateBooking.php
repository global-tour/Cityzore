<?php

namespace App\Jobs;

use App\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;

class UpdateBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bookings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        /**
         * @param Booking $item
         */
        foreach ($this->bookings as $item) {
            $dateTime = '';
            $dateForSort = Carbon::make($item->dateForSort)->format('Y-m-d');

            if (!is_null($item->hour)) {

                if (count(json_decode($item->hour, 1)) > 1) {
                    $arr = array_column(json_decode($item->hour, 1), 'hour');

                    $minHour = min($arr);

                    $minHour = explode('-', $minHour);

                    $dateTime = Carbon::parse($dateForSort. ' '. str_replace(' ', '', $minHour[0]).':00')->format('Y-m-d H:i:s');

                }else{

                    $arr = json_decode($item->hour, 1)[0]['hour'];

                    $minHour = explode('-' , $arr);

                    $dateTime = Carbon::make($dateForSort. ' ' . str_replace(' ', '', $minHour[0]). ':00')->format('Y-m-d H:i:s');

                }
            }else{

                $dateTime = Carbon::make($item->dateTime)->format('Y-m-d H:i:s');

            }

            $item->dateForSort = $dateTime ?? $dateForSort;

            $item->save();
        }
    }
}
