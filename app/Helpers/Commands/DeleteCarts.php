<?php

namespace App\Helpers\Commands;

use App\Barcode;
use App\Cart;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Option;
use Carbon\Carbon;

class DeleteCarts
{
    public $apiRelated;
    public $timeRelatedFunctions;

    public function __construct()
    {
        $this->apiRelated = new ApiRelated();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
    }

    public function run($manuel=false)
    {
        $carts = Cart::whereIn('status', [0, 6])->orderBy('id', 'ASC')->get();
        $c= new CartController();
        if($manuel === false){
            $c->sendMailToUserWhenCart30MinutesHavePassed();
        }
        foreach ($carts as $cart) {
            if ($cart) {
                $updated_at =  strtotime($cart->updated_at);
                $now = strtotime('now');
                if ($updated_at+(75*60) <= $now) {
                    $cart->status = 4;
                    if ($cart->save()) {
                        Barcode::where('isReserved', 1)
                            ->where('cartID', $cart->id)->take($cart->ticketCount)
                            ->update(['cartID' => null, 'isReserved' => 0]);
                    }

                    $option = Option::where('id', '=', $cart->optionID)->first();
                    $pricing = $option->pricings()->first();
                    $bookingItems = json_decode($cart->bookingItems, true);
                    $temporaryIgnoredCategoriesArray = json_decode($pricing->ignoredCategories, true);
                    $ignoredCategoriesArray = [];
                    $newTicketCount = $cart->ticketCount;
                    if (!is_null($temporaryIgnoredCategoriesArray)) {
                        foreach ($temporaryIgnoredCategoriesArray as $ignoredCategory) {
                            if ($ignoredCategory == 'euCitizen')
                                array_push($ignoredCategoriesArray, 'EU_CITIZEN');
                            else
                                array_push($ignoredCategoriesArray, strtoupper($ignoredCategory));
                        }
                        foreach ($ignoredCategoriesArray as $ignoredCategory) {
                            foreach ($bookingItems as $bookingItem) {
                                if ($bookingItem['category'] == $ignoredCategory) {
                                    $newTicketCount -=  $bookingItem['count'];
                                }
                            }
                        }
                    }
                    $availability = $option->avs()->get();
                    foreach ($availability as $av) {
                        if ($av->isLimitless == 0) {
                            $ticketHourlyDatabase = json_decode($av->hourly, true);
                            $ticketDailyDatabase = json_decode($av->daily, true);
                            $ticketDateRangeDatabase = json_decode($av->dateRange, true);
                            $ticketBarcodeDatabase = json_decode($av->barcode, true);
                            $selectedDate = $cart->date;
                            if($cart->isGYG === 1){
                                $selectedDate = Carbon::parse($cart->dateTime)->format('d/m/Y');
                            }

                            $jsonq = $this->apiRelated->prepareJsonQ();
                            $selectedHour = $cart->hour ? json_decode($cart->hour,  true) : [];
                            if ($av->avTicketType == 1 && count($ticketHourlyDatabase) > 0) {
                                foreach ($selectedHour as $hour) {
                                    $res = $jsonq->json($av->hourly);
                                    $result = $res->where('day', '=', $selectedDate)
                                        ->where('hour', '=', $hour['hour'])
                                        ->get();
                                    if (count($result) == 1) {
                                        $key = key($result);
                                        $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] + $newTicketCount;
                                        $ticketState = $ticketHourlyDatabase[$key]['ticket'];
                                        $av->hourly = json_encode($ticketHourlyDatabase);
                                        $av->save();
                                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketHourlyDatabase[$key]['day'], $ticketHourlyDatabase[$key]['hour'], 'Europe/Paris');
                                        if ($ticketState < 5 && $isDateTimeValid) {
                                            $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketHourlyDatabase[$key]['day'], $ticketHourlyDatabase[$key]['hour'], 'Europe/Paris');
                                            $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                            foreach ($optionRefCodes as $orc) {
                                                $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                            }
                                        }
                                    }
                                    $res->reset();
                                }
                            } else if ($av->avTicketType == 2 && count($ticketDailyDatabase) > 0) {
                                $res = $jsonq->json($av->daily);
                                $result = $res->where('day', '=', $selectedDate)->get();
                                if (count($result) == 1) {
                                    $key = key($result);
                                    $ticketDailyDatabase[$key]['ticket'] = $ticketDailyDatabase[$key]['ticket'] + $newTicketCount;
                                    $ticketState = $ticketDailyDatabase[$key]['ticket'];
                                    $av->daily = json_encode($ticketDailyDatabase);
                                    $av->save();
                                    $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketDailyDatabase[$key]['day'], '00:00', 'Europe/Paris');
                                    if ($ticketState < 5 && $isDateTimeValid) {
                                        $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketDailyDatabase[$key]['day'], '00:00', 'Europe/Paris');
                                        $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                        foreach ($optionRefCodes as $orc) {
                                            $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                        }
                                    }
                                }
                                $res->reset();
                            } else if ($av->avTicketType == 3 && count($ticketDateRangeDatabase) > 0) {
                                $res = $jsonq->json($av->dateRange);
                                $result = $res->where('dayFrom', 'dateLte', $selectedDate)
                                    ->where('dayTo', 'dateGte', $selectedDate)
                                    ->get();
                                if (count($result) == 1) {
                                    $key = key($result);
                                    $ticketDateRangeDatabase[$key]['ticket'] = $ticketDateRangeDatabase[$key]['ticket'] + $newTicketCount;
                                    $ticketState = $ticketDateRangeDatabase[$key]['ticket'];
                                    $av->dateRange = json_encode($ticketDateRangeDatabase);
                                    $av->save();
                                    $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($selectedDate, '00:00', 'Europe/Paris');
                                    if ($ticketState < 5 && $isDateTimeValid) {
                                        $jsonq2 = $this->apiRelated->prepareJsonQ();
                                        $res2 = $jsonq2->json($av->daily);
                                        $result2 = $res2->where('day', '=', $selectedDate)
                                            ->where('isActive', '=', 1)
                                            ->get();
                                        if (count($result2) == 1) {
                                            $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($selectedDate, '00:00', 'Europe/Paris');
                                            $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                            foreach ($optionRefCodes as $orc) {
                                                $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                            }
                                        }
                                    }
                                }
                                $res->reset();
                            } else if ($av->avTicketType == 4 && count($ticketBarcodeDatabase) > 0) {
                                $res = $jsonq->json($av->barcode);
                                $result = $res->where('dayFrom', 'dateLte', $selectedDate)->where('dayTo', 'dateGte', $selectedDate)->get();
                                if ($res->count() > 0) {
                                    $key = key($result);
                                    $ticketBarcodeDatabase[$key]['ticket'] = json_encode($ticketBarcodeDatabase[$key]['ticket'] + $newTicketCount);
                                    $av->barcode = json_encode($ticketBarcodeDatabase);
                                }
                                $av->save();
                                $res->reset();
                                $ticketType = $av->ticketType()->first();
                                if (!is_null($ticketType)) {
                                    $avsUsingThisTT = $ticketType->av()->where('supplierID', $av->supplierID)->where('id', '!=', $av->id)->get();
                                    if (count($avsUsingThisTT) > 0) {
                                        foreach ($avsUsingThisTT as $avUsingThisTT) {
                                            $barcodeDecodedOfThisTicket = json_decode($avUsingThisTT->barcode, true);
                                            if (count($barcodeDecodedOfThisTicket) > 0) {
                                                $barcodeDecodedOfThisTicket[0]['ticket'] += $newTicketCount;
                                                $avUsingThisTT->barcode = json_encode($barcodeDecodedOfThisTicket);
                                                $avUsingThisTT->save();
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
    }
}
