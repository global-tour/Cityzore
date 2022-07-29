<?php

namespace App\Helpers\Frontend\Option;

use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
use Nahid\JsonQ\Jsonq;

class CheckAllAvailabilities
{
    public $option;
    public $commonFunctions;
    public $apiRelated;
    public $timeRelatedFunctions;

    public function __construct($option = null)
    {

        $this->apiRelated = new ApiRelated();
        $this->commonFunctions = new CommonFunctions();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->option = $option;
    }

    public function check()
    {
        $data = ['data' => ['availabilities' => []]];
        $allAvs = $this->option->avs()->get();
        $currentTime = Carbon::now()->format('d/m/Y H:i');
        //$targetTime = Carbon::now()->addYears(2)->format('d/m/Y H:i');
        $fromDate = explode(' ', $currentTime)[0];
        $fromTime = explode(' ', $currentTime)[1];
        //$toDate = explode(' ', $targetTime)[0];
        //$toTime = explode(' ', $targetTime)[1];
        $allMaxValidDates = [];
        foreach ($allAvs as $availability) {
            $avdates = $availability->avdates()->get();
            $maxValidTo = $avdates->max('valid_to');
            $allMaxValidDates[] = $maxValidTo;
        }
        foreach ($allAvs as $availability) {
            $avdates = $availability->avdates()->get();
            $maxValidTo = min($allMaxValidDates);
            $toDate = Carbon::createFromFormat('Y-m-d', $maxValidTo)->format('d/m/Y');
            $toTime = '23:59';



            $disabledDates = $this->getDisabledDates($availability);
            $blockoutHours = $this->getBlockOutHours($availability);
            $jsonq = $this->apiRelated->prepareJsonQ();
            if (in_array($availability->avTicketType, [1, 2])) {
                $column = $availability->avTicketType == 1 ? $availability->hourly : $availability->daily;
                $res = $jsonq->json($column);
                $result = $res->where('day', 'dateGte', $fromDate)
                    ->where('day', 'dateLte', $toDate)
                    ->where('isActive', '=', 1)
                    ->get();

                if (count($result) == 0) {
                    continue;
                }
                foreach ($result as $r) {
                    if (!in_array($r['day'], $disabledDates)) {
                        if ($availability->avTicketType == 1) {
                            if (!($r['day'] == $fromDate && $r['hour'] < $fromTime) && !($r['day'] == $toDate && $r['hour'] > $toTime)) {
                                $isInBlockArr = false;
                                foreach ($blockoutHours as $blockoutHour) {
                                    if (isset($blockoutHour['hours'])) {
                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])) && in_array($r['hour'], $blockoutHour['hours']));
                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])) && in_array($r['hour'], $blockoutHour['hours']));
                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])));
                                    } elseif (isset($blockoutHour['days'])) {
                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days']));
                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])));
                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days']));
                                    } elseif (isset($blockoutHour['months'])) {
                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])));
                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])) && in_array(explode('/', $r['day'])[1], $blockoutHour['months']));
                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])) && in_array(explode('/', $r['day'])[1], $blockoutHour['months']));
                                    }


                                    if (!$a || !$b || !$c) {
                                        $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day'], $r['hour']);
                                        $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                                        $validAvailabilities['vacancies'] = $ticket;
                                    } else {
                                        $isInBlockArr = true;
                                        break;
                                    }
                                }
                                if (count($blockoutHours) > 0) {
                                    if (!$isInBlockArr)
                                        array_push($data['data']['availabilities'], $validAvailabilities);
                                } else {
                                    $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day'], $r['hour']);
                                    $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                                    $validAvailabilities['vacancies'] = $ticket;
                                    array_push($data['data']['availabilities'], $validAvailabilities);
                                }
                            }
                        } else if ($availability->avTicketType == 2) {
                            if (!($r['day'] == $fromDate && $r['hourFrom'] < $fromTime) && !($r['day'] == $toDate && $r['hourFrom'] > $toTime)) {
                                $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day']);
                                $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                                $validAvailabilities['vacancies'] = $ticket;
                                $validAvailabilities['openingTimes'] = array();
                                array_push($validAvailabilities['openingTimes'], ['fromTime' => $r['hourFrom'], 'toTime' => $r['hourTo']]);
                                array_push($data['data']['availabilities'], $validAvailabilities);
                            }
                        }
                    }
                }
                $res->reset();


            } else if (in_array($availability->avTicketType, [3,4])) {
                $dateRange = ($availability->avTicketType === 3) ? $availability->dateRange: $availability->barcode;
                $res = $jsonq->json($dateRange);
                $result = $res->where('dayFrom', 'dateBetween', [$fromDate, $toDate])
                    ->orWhere('dayTo', 'dateBetween', [$fromDate, $toDate])
                    ->orWhere('dayFrom', 'dateLte', $fromDate)
                    ->orWhere('dayTo', 'dateGte', $toDate)
                    ->get();
                $res->reset();
                if (count($result) == 0) {
                    continue;
                } else {
                    foreach ($result as $i => $r) {
                        if($availability->isLimitless != 1 && intval($r['ticket']) === 0){
                            continue;
                        }
                        $fromDateFormatted = DateTime::createFromFormat('d/m/Y', $fromDate);
                        $dayFrom = DateTime::createFromFormat('d/m/Y', $r['dayFrom']);
                        if ($dayFrom < $fromDateFormatted) {
                            $dayFrom = $fromDateFormatted;
                        }
                        $toDateFormatted = DateTime::createFromFormat('d/m/Y', $toDate);
                        $dayTo = DateTime::createFromFormat('d/m/Y', $r['dayTo']);
                        if ($dayTo > $toDateFormatted) {
                            $dayTo = $toDateFormatted;
                        }
                        $interval = new DateInterval('P1D');
                        $dayTo->add($interval);
                        $period = new DatePeriod($dayFrom, $interval, $dayTo);

                        foreach ($period as $dt) {
                            $dtFormatted = $dt->format('d/m/Y');
                            if (!in_array($dtFormatted, $disabledDates)) {
                                if ($availability->availabilityType == 'Starting Time') {
                                    $requestedCol = $availability->hourly;
                                    $queryStrHour = 'hour';
                                } else {
                                    $requestedCol = $availability->daily;
                                    $queryStrTo = 'hourTo';
                                    $queryStrFrom = 'hourFrom';
                                }

                                //$daily = $availability->daily;
                                $res2 = $jsonq->json($requestedCol);
                                $result2 = $res2->where('day', '=', $dtFormatted)
                                    ->where('isActive', '=', 1)
                                    ->get();
                                if (count($result2) != 0) {
                                    foreach ($result2 as $r2) {
                                        if ($availability->availabilityType == 'Starting Time') {
                                            if (!($r2['day'] == $fromDate && $r2['hour'] < $fromTime) && !($r2['day'] == $toDate && $r2['hour'] > $toTime)) {
                                                $isInBlockArr = false;
                                                foreach ($blockoutHours as $blockoutHour) {
                                                    if (isset($blockoutHour['hours'])) {
                                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r2['day'])[1], $blockoutHour['months'])) && in_array($r2['hour'], $blockoutHour['hours']));
                                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r2['day'])->format('l'), $blockoutHour['days'])) && in_array($r2['hour'], $blockoutHour['hours']));
                                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r2['hour'], $blockoutHour['hours'])));
                                                    } elseif (isset($blockoutHour['days'])) {
                                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r2['day'])[1], $blockoutHour['months'])) && in_array(Carbon::createFromFormat('d/m/Y', $r2['day'])->format('l'), $blockoutHour['days']));
                                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r2['day'])->format('l'), $blockoutHour['days'])));
                                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r2['hour'], $blockoutHour['hours'])) && in_array(Carbon::createFromFormat('d/m/Y', $r2['day'])->format('l'), $blockoutHour['days']));
                                                    } elseif (isset($blockoutHour['months'])) {
                                                        $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r2['day'])[1], $blockoutHour['months'])));
                                                        $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r2['day'])->format('l'), $blockoutHour['days'])) && in_array(explode('/', $r2['day'])[1], $blockoutHour['months']));
                                                        $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r2['hour'], $blockoutHour['hours'])) && in_array(explode('/', $r2['day'])[1], $blockoutHour['months']));
                                                    }


                                                    if (!$a || !$b || !$c) {
                                                        $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r2['day'], $r2['hour']);
                                                        $ticket = $availability->isLimitless == 1 ? 999999 : intval($r2['ticket']);
                                                        $validAvailabilities['vacancies'] = $ticket;
                                                    } else {
                                                        $isInBlockArr = true;
                                                        break;
                                                    }
                                                }
                                                if (count($blockoutHours) > 0) {
                                                    if (!$isInBlockArr)
                                                        array_push($data['data']['availabilities'], $validAvailabilities);
                                                } else {
                                                    $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r2['day'], $r2['hour']);
                                                    $ticket = $availability->isLimitless == 1 ? 999999 : intval($r2['ticket']);
                                                    $validAvailabilities['vacancies'] = $ticket;
                                                    array_push($data['data']['availabilities'], $validAvailabilities);
                                                }
                                            }

                                        }else{
                                            if (!($r2['day'] == $fromDate && $r2['hourFrom'] < $fromTime) && !($r2['day'] == $toDate && $r2['hourFrom'] > $toTime)) {
                                                $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r2['day']);
                                                $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']); // getting ticket count from dateRange column
                                                $validAvailabilities['vacancies'] = $ticket;
                                                $validAvailabilities['openingTimes'] = array();
                                                array_push($validAvailabilities['openingTimes'], ['fromTime' => $r2['hourFrom'], 'toTime' => $r2['hourTo']]);
                                                array_push($data['data']['availabilities'], $validAvailabilities);
                                            }
                                        }

                                    }
                                }
                                $res2->reset();
                            }
                        }
                    }
                }

            }else{

            }

        }
        $data = $this->filter($data); //dd($this->option->avs()->pluck('id'));
        $data = $this->commonFunctions->flatten($data); //dd($this->option->referenceCode,$this->option->avs()->first()->id, $data);
        if(count($data)) return true;
        return false;
    }

    public function load($option)
    {
        $this->option = $option;
        return $this;
    }

    public function getDisabledDates($availability)
    {
        $disabledDates = [];
        $avdates = $availability->avdates()->get();


        array_push($disabledDates, json_decode($availability->disabledDays, true));


        $disabledDates = $this->commonFunctions->flatten($disabledDates);


        $disabledYears = json_decode($availability->disabledYears, true);
        if (!is_null($disabledYears)) {
            foreach ($disabledYears as $dy) {
                $begin = DateTime::createFromFormat('Y-m-d', $dy . '-01-01');
                $begin = $begin->format('d/m/Y');
                $begin = DateTime::createFromFormat('d/m/Y', $begin);
                $end = DateTime::createFromFormat('Y-m-d', $dy . '-12-31');
                $end = $end->format('d/m/Y');
                $end = DateTime::createFromFormat('d/m/Y', $end);
                $interval = new DateInterval('P1D');
                $end->add($interval);
                $period = new DatePeriod($begin, $interval, $end);
                foreach ($period as $dt) {
                    $formattedDt = $dt->format('d/m/Y');
                    array_push($disabledDates, $formattedDt);
                }
            }
        }



        $disabledMonths = json_decode($availability->disabledMonths, true);
        if (!is_null($disabledMonths)) {
            $minYear = strtok($avdates->min('valid_from'), '-');
            $maxYear = strtok($avdates->max('valid_to'), '-');
            $allYears = range($minYear, $maxYear);

            foreach ($allYears as $aY) {
                foreach ($disabledMonths as $dM) {
                    $begin = DateTime::createFromFormat('Y-m-d', $aY . '-' . $dM . '-01');
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $aY . '-' . $dM . '-01')->modify('last day of this month');
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        array_push($disabledDates, $formattedDt);
                    }
                }
            }
        }



        $disabledWeekDays = json_decode($availability->disabledWeekDays, true);
        if (!is_null($disabledWeekDays)) {
            $startDate = new DateTime($avdates->min('valid_from'));
            $endDate = new DateTime($avdates->max('valid_to'));
            foreach ($disabledWeekDays as $dwd) {
                while ($startDate <= $endDate) {
                    if (strtolower($startDate->format('l')) == $dwd) {
                        array_push($disabledDates, $startDate->format('d/m/Y'));
                    }
                    $startDate->modify('+1 day');
                }
                $startDate = new DateTime($avdates->min('valid_from'));
            }
        }


        $disabledDates = $this->commonFunctions->flatten($disabledDates);
        $disabledDates = array_values(array_unique($disabledDates));

        return $disabledDates;
    }

    public function getBlockOutHours($availability)
    {
        $blockoutHours = [];
        $avBlockoutHours = json_decode($availability->blockoutHours, true);
        foreach ($avBlockoutHours as $avBlockoutHour) {
            if (array_key_exists($this->option->referenceCode, $avBlockoutHour)) {
                $blockoutHours = $avBlockoutHour[$this->option->referenceCode];
            }
        }
        return $blockoutHours;
    }

    public function filter($data){
        $arr = [];

        foreach ($data['data']['availabilities'] as $d) {
            if(intval($d['vacancies']) > 0){
                $arr[] = $d['dateTime'];
            }

        }
        return $arr;
    }
}
