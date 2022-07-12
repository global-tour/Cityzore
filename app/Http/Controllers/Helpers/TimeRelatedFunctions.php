<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Product;

class TimeRelatedFunctions extends Controller
{

    /**
     * Converting date format from Y-m-d to d/m/Y for given array of dates
     *
     * @param $arr
     * @return array
     */
    public function convertDateFormat($arr)
    {
        $newArr = [];
        foreach ($arr as $a) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $a);
            $newDate = $dateObj->format('d/m/Y');
            array_push($newArr, $newDate);
        }
        return $newArr;
    }

    /**
     * Converts only one day from d/m/Y to Y-m-d ($fromDateTime) and adds one day to it ($toDateTime)
     * returns array => $fromDateTime, $toDateTime
     * 01/11/2019 => $fromDateTime: 2019-11-01T00:00:00+01:00
     * 02/11/2019 => $toDateTime  : 2019-11-02T00:00:00+01:00
     *
     * @param $day
     * @return array
     */
    public function convertDmyToYmd($day)
    {
        $day = $day . 'T00:00:00';
        $fromDateTime = Carbon::createFromFormat('d/m/Y\TH:i:s', $day, 'Europe/Paris');
        $fromDateTimeStr = $fromDateTime->toIso8601String();
        $toDateTime = $fromDateTime->addDay();
        $toDateTimeStr = $toDateTime->toIso8601String();
        return [
            'fromDateTime' => $fromDateTimeStr,
            'toDateTime' => $toDateTimeStr
        ];
    }

    /**
     * Converts only one day from d/m/Y to Y-m-d with starting time.
     * 01/11/2019 && 10:00 => $fromDateTime : 2019-11-01T10:00:00+01:00
     *
     * @param $day
     * @param $hour
     * @param $timeZone
     * @return string
     */
    public function convertDmyToYmdWithHour($day, $hour, $timeZone = 'Europe/Paris')
    {
        if (gettype($hour) == 'array') {
            $hour = $hour['hour'];
        }
        $day = str_replace('.', '/', $day);
        $day = $day.'T'.$hour.':00';
        
        $fromDateTime = Carbon::createFromFormat('d/m/Y\TH:i:s', $day, $timeZone);
        $fromDateTimeStr = $fromDateTime->toIso8601String();
        return $fromDateTimeStr;
    }

    /**
     * Checks if date value is not much older than 1 week
     *
     * @param $date
     * @param string $timeZone
     * @return bool
     */
    public function isDateValid($date, $timeZone = 'Europe/Paris')
    {
        $now = Carbon::now();
        $oneWeekAfterNow = $now->addWeeks(1);
        $dateCarbon = Carbon::createFromFormat('d/m/Y', $date, $timeZone);

        return $oneWeekAfterNow->greaterThanOrEqualTo($dateCarbon);
    }

    /**
     * Checks if dateTime value is not much older than 1 week
     *
     * @param $date
     * @param $time
     * @param string $timeZone
     * @return bool
     */
    public function isDateTimeValid($date, $time, $timeZone = 'Europe/Paris')
    {
        $now = Carbon::now();
        $oneWeekAfterNow = $now->addWeeks(1);
        $dateTime = $date . 'T' . $time . ':00';
        $dateTimeCarbon = Carbon::createFromFormat('d/m/Y\TH:i:s', $dateTime, $timeZone);

        return $oneWeekAfterNow->greaterThanOrEqualTo($dateTimeCarbon);
    }

    /**
     * Adds x weeks to today
     * used for notify push related code
     *
     * @param int $weeks
     * @return string
     */
    public function addXWeeksToToday($weeks=1)
    {
        $now = Carbon::now();
        $newNow = $now->addWeeks($weeks);
        $newNowStr = $newNow->toIso8601String();
        return $newNowStr;
    }

    /**
     * Adds x minutes to today.
     * used for reservationExpiration field on /reserve request
     *
     * @param int $minutes
     * @return string
     */
    public function addXMinutesToToday($minutes=70)
    {
        $now = Carbon::now();
        $newNow = $now->addMinutes($minutes);
        $newNowStr = $newNow->toIso8601String();
        return $newNowStr;
    }

    /**
     * Arranges datetime for the response of get availabilities request
     *
     * @param $day
     * @param string $hour
     * @return Carbon|string
     */
    public function arrangeDateTimeForGetAvailabilities($day, $hour='00:00')
    {
        $day = $day . 'T' . $hour;
        $datetime = Carbon::createFromFormat('d/m/Y\TH:i', $day, 'Europe/Paris');
        $datetime = $datetime->toIso8601String();

        return $datetime;
    }

    /**
     * Converts only one day from d/m/Y to Y-m-d and returns $dateTimeStr
     *
     * @param $day
     * @param string $type
     * @return string
     */
    public function convertDmyToYmdForOneDay($day, $type = 'from')
    {
        $timeStr = $type == 'from' ? 'T00:00:00' : 'T23:59:59';
        $day = $day . $timeStr;
        $dateTime = Carbon::createFromFormat('d/m/Y\TH:i:s', $day, 'Europe/Paris');
        $dateTimeStr = $dateTime->toIso8601String();
        return $dateTimeStr;
    }

    /**
     * Converts Y-m-d to Y-m-d\TH:i:s
     *
     * @param $day
     * @param string $type
     * @return string
     */
    public function convertYmdToYmdHisForOneDay($day, $type = 'from')
    {
        $timeStr = $type == 'from' ? 'T00:00:00' : 'T23:59:59';
        $day = $day . $timeStr;
        $dateTime = Carbon::createFromFormat('Y-m-d\TH:i:s', $day, 'Europe/Paris');
        $dateTimeStr = $dateTime->toIso8601String();
        return $dateTimeStr;
    }

    /**
     * Converts Y-m-dTH:i:s+01:00 to d/m/Y H:i format
     *
     * @param $dateTime
     * @return string
     */
    public function convertYmdHisPlusTimezoneToDmyHi($dateTime, $option=null, $booking=null)
    {
        $withoutTimeZone = explode('+', $dateTime)[0];
        $date = Carbon::createFromFormat('Y-m-d\TH:i:s', $withoutTimeZone, 'Europe/Paris');
        $asExpected = $date->format('d/m/Y H:i');
        $asExpectedExplodedOne = explode(' ', $asExpected)[1];
        $asExpectedExplodedZero = explode(' ', $asExpected)[0];
        if ($asExpectedExplodedOne == '00:00') {

            $opt_hour = json_encode([["hour" => "There is No Registered Operating Hour!"]]);

                 if($booking && $booking->hour){
                  $opt_hour = $booking->hour;

                 }else{

                    if($option) {
                        $av = $option->avs()->first();

                        foreach(json_decode($av->daily, true) as $day){
                        if($day["day"] === Carbon::parse($booking->dateForSort)->format('d/m/Y')){
                         
                         $opt_hour = json_encode([["hour" => $day["hourFrom"]."-".$day["hourTo"]]]);

                          break;
                        }
                        }
                    }

                 }

                  

 
                
                  

                  


            $asExpected = $asExpectedExplodedZero ." - ". json_decode($opt_hour, true)[0]["hour"];

            return $asExpected;
        }else{
            return $asExpected;
        }
        
    }

    /**
     * @param $option
     * @param $hour
     * @param $selectedDate
     * @param $productID
     * @return array
     */
    public function cutOfTimeCalculator($option, $hour, $selectedDate, $productID)
    {
        $product = Product::findOrFail($productID);
        $cutOfTimeDate = $option->cutOfTimeDate;
        $cutOfTime = $option->cutOfTime;
        $time = strtotime($this->convertDmyToYmdWithHour($selectedDate, $hour, $product->countryName->timezone));
        if ($cutOfTimeDate == 'd') {
            $cutOfTimeAtomic = 24 * 60 * 60 * $cutOfTime;
        } elseif ($cutOfTimeDate == 'h') {
            $cutOfTimeAtomic = $cutOfTime * 60 * 60;
        } else {
            $cutOfTimeAtomic = $cutOfTime * 60;
        }

        return ['cutOfTimeAtomic' => $cutOfTimeAtomic, 'time' => $time];
    }

    /**
     * @param $created_at
     * @return float|int|string
     */
    public static function calculateElapsedTimeOver($created_at)
    {
        $date = strtotime('now');
        $created_at = strtotime($created_at);
        $elapsedTime = ($date - $created_at)/(24*60*60);
        if ($elapsedTime < 1) {
            $elapsedTime = $elapsedTime*24;
            if ($elapsedTime < 1) {
                $elapsedTime = (int)($elapsedTime * 60);
                $elapsedTime = $elapsedTime.' minutes ago';
            } else if ($elapsedTime > 1) {
                $elapsedTime = (int)$elapsedTime.' hours ago';
            } else {
                $elapsedTime = 'a few seconds ago';
            }
        } else if ($elapsedTime > 1 && $elapsedTime < 30) {
            $elapsedTime = (int)$elapsedTime.' days ago';
        } else if ($elapsedTime == 30) {
            $elapsedTime = 'a month ago';
        } else if ($elapsedTime > 30 && $elapsedTime < 30 * 12) {
            $elapsedTime = $elapsedTime/30;
            $elapsedTime = (int)$elapsedTime.' months ago';
        } elseif ($elapsedTime >= 30 * 12) {
            $elapsedTime = $elapsedTime / (12 * 30);
            $elapsedTime = (int)$elapsedTime.' years ago';
        }

        return $elapsedTime;
    }

    /**
     * returns all dates between to dates
     *
     * @param $fromdate
     * @param $todate
     * @return DatePeriod
     * @throws \Exception
     */
    function returnDates($fromdate, $todate)
    {
        $fromdate = DateTime::createFromFormat('d/m/Y', $fromdate);
        $todate = DateTime::createFromFormat('d/m/Y', $todate);
        return new DatePeriod(
            $fromdate,
            new DateInterval('P1D'),
            $todate->modify('+1 day')
        );
    }

}
