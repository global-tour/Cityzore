<?php


namespace App\Http\Controllers\Helpers;



use App\BokunLog;
use App\Cart;
use App\Option;
use Illuminate\Http\Request;
use Nahid\JsonQ\Jsonq;

class BokunRelated
{

    public $errorMessages;

    public function __construct()
    {
        $this->errorMessages = [
            'AUTHORIZATION_FAILURE' => 'The provided credentials are not valid.',
            'VALIDATION_FAILURE' => 'The request object contains inconsistent or invalid data or is missing data.',
            'INVALID_PRODUCT' => 'The specified product does not exist or is broken for another reason (excluding availability issues).',
            'INTERNAL_SYSTEM_FAILURE' => 'An error occurred that is unexpected.',
            'API_HOST_FAILURE' => 'The CZ_API_HOST does not match with that on our system.',
            'API_CREDENTIALS_FAILURE' => 'The CZ_API_CREDENTIALS pair does not match with that on our system.',
            'NO_AVAILABILITY' => 'The reservation or booking call cannot be fullfilled because there is insufficient availability.',
            'INVALID_RESERVATION' => 'The specified reservation does not exist or is not in a valid state for the requested operation.',
            'INVALID_BOOKING' => 'The specified booking does not exist or is not in a valid state.',
            'INVALID_CANCELLATION' => 'The specified booking could not cancellation.',
            'INVALID_TICKET_CATEGORY' => 'The reservation or booking call specified a ticket category that is not configured for the requested product.',
            'PRODUCTS_NOT_FOUND' => 'There\'s no product that match with your search criterias',
            'INVALID_CURRENCY' => 'The currency does not exist on our system.',
            'INVOICE_NOT_CREATED' => 'The invoice for specified booking could not create.',
            'CART_FAILURE' => 'This cart has been booked, poured or expired.',
            'NO_PRICING' => 'There\'s no pricing which connected to this option.'
        ];
    }

    /**
     * @param $request
     * @return bool
     */
    public function apiAuthorization($request)
    {
        $authorization = $request->header('authorization');
        $authorization = explode("Basic ", $authorization);
        $usernamePassword = array_key_exists(1, $authorization) ? base64_decode($authorization[1]) : '';
        return $usernamePassword == env('BOKUN_CREDENTIALS', 'cityzorecom:7TX<T/$(2uM7m~xF');
    }

    /**
     * DMY to D-M-Y
     *
     * @param $dateRange
     * @return array
     */
    public function bokunDateToCityZoreDMY($dateRange)
    {
        $dateFrom = $dateRange['from'];
        $dateTo = $dateRange['to'];
        if ($dateFrom['day'] < 10 && $dateFrom['month'] > 10) {
            $dateFromDMY = '0'.$dateFrom['day'] . '-' .$dateFrom['month'] . '-' .$dateFrom['year'];
        } else if ($dateFrom['month'] < 10 &&$dateFrom['day'] > 10) {
            $dateFromDMY =$dateFrom['day'] . '-' . '0'.$dateFrom['month'] . '-' .$dateFrom['year'];
        } else if ($dateFrom['day'] < 10 &&$dateFrom['month'] < 10) {
            $dateFromDMY = '0'.$dateFrom['day'] . '-' .'0'.$dateFrom['month'] . '-' .$dateFrom['year'];
        } else {
            $dateFromDMY =$dateFrom['day'] . '-' .$dateFrom['month'] . '-' .$dateFrom['year'];
        }
        if ($dateTo['day'] < 10 && $dateTo['month'] > 10) {
            $dateToDMY = '0'.$dateTo['day'] . '-' .$dateTo['month'] . '-' .$dateTo['year'];
        } else if ($dateTo['month'] < 10 &&$dateTo['day'] > 10) {
            $dateToDMY =$dateTo['day'] . '-' . '0'.$dateTo['month'] . '-' .$dateTo['year'];
        } else if ($dateFrom['day'] < 10 &&$dateTo['month'] < 10) {
            $dateToDMY = '0'.$dateTo['day'] . '-' .'0'.$dateTo['month'] . '-' .$dateTo['year'];
        } else {
            $dateToDMY =$dateTo['day'] . '-' .$dateTo['month'] . '-' .$dateTo['year'];
        }

        return ['from' => $dateFromDMY, 'to' => $dateToDMY];
    }

    /**
     * @param $optionReferenceCode
     * @return array
     */
    public function getPricingCategories($optionReferenceCode)
    {
        $option = Option::where('referenceCode', '=', $optionReferenceCode)->first();
        $pricing = $option->pricings()->first();
        $pricingCategories = [];
        if (!is_null($pricing->adultMin)) {
            array_push($pricingCategories, [
                'id' => 'ADULT',
                'label' => 'ADULT',
                'minAge' => $pricing->adultMin,
                'maxAge' => $pricing->adultMax,
            ]);
        }
        if (!is_null($pricing->youthMin)) {
            array_push($pricingCategories, [
                'id' => 'YOUTH',
                'label' => 'YOUTH',
                'minAge' => $pricing->youthMin,
                'maxAge' => $pricing->youthMax,
            ]);
        }
        if (!is_null($pricing->childMin)) {
            array_push($pricingCategories, [
                'id' => 'CHILD',
                'label' => 'CHILD',
                'minAge' => $pricing->childMin,
                'maxAge' => $pricing->childMax,
            ]);
        }
        if (!is_null($pricing->infantMin)) {
            array_push($pricingCategories, [
                'id' => 'INFANT',
                'label' => 'INFANT',
                'minAge' => $pricing->infantMin,
                'maxAge' => $pricing->infantMax,
            ]);
        }
        if (!is_null($pricing->euCitizenMin)) {
            array_push($pricingCategories, [
                'id' => 'EUCITIZEN',
                'label' => 'EUCITIZEN',
                'minAge' => $pricing->euCitizenMin,
                'maxAge' => $pricing->euCitizenMax,
            ]);
        }

        return $pricingCategories;
    }

    /**
     * @param $optionReferenceCode
     * @return array
     */
    public function getPricingCategoriesWithPrice($optionReferenceCode)
    {
        $option = Option::where('referenceCode', '=', $optionReferenceCode)->first();
        $pricing = $option->pricings()->first();
        $pricingCategories = [];
        if (!is_null($pricing->adultMin)) {
            array_push($pricingCategories, [
                'pricingCategoryId' => 'ADULT',
                'price' => [
                    'currency' => 'EUR',
                    'amount' => 0
                ],
            ]);
        }
        if (!is_null($pricing->youthMin)) {
            array_push($pricingCategories, [
                    'pricingCategoryId' => 'YOUTH',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => 0
                    ],
            ]);
        }
        if (!is_null($pricing->childMin)) {
            array_push($pricingCategories, [
                    'pricingCategoryId' => 'CHILD',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => 0
                    ],
            ]);
        }
        if (!is_null($pricing->infantMin)) {
            array_push($pricingCategories, [
                    'pricingCategoryId' => 'INFANT',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => 0
                    ],
            ]);
        }
        if (!is_null($pricing->euCitizenMin)) {
            array_push($pricingCategories, [
                    'pricingCategoryId' => 'EUCITIZEN',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => 0
                    ],
            ]);
        }

        return $pricingCategories;
    }

    /**
     * @param $currency
     * @return int
     */
    public function getCurrencyID($currency)
    {
        switch ($currency) {
            case 'EUR' :
                $currencyID = 2;
                break;
            case 'USD' :
                $currencyID = 1;
                break;
            case 'GBP' :
                $currencyID = 3;
                break;
            case 'TRY' :
                $currencyID = 4;
                break;
            default:
                $currencyID = 2;
        }

        return $currencyID;
    }

    /**
     * @param $optionID
     * @param $ticketCount
     * @param $cart
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function ticketCountDecrement($optionID, $ticketCount, $cart)
    {
        $jsonq = new Jsonq();
        $option = Option::findOrFail($optionID);
        $availability = $option->avs()->first();

        if (!$availability) {
            return $this->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }

        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);

        if (count($ticketHourlyDatabase) > 0) {
            $res = $jsonq->json($availability->hourly);
            $result = $res->where('day', '=', $cart->date)->where('hour', '=', json_decode($cart->hour, true)[0]['hour'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] - $ticketCount;
                $availability->hourly = json_encode($ticketHourlyDatabase);
                if ($ticketHourlyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $cart->save();
                    $res->reset();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDailyDatabase) > 0) {
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $cart->date)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['ticket'] -= $ticketCount;
                $availability->daily = json_encode($ticketDailyDatabase);
                if ($ticketDailyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $cart->save();
                    $res->reset();
                    return ['result'=>'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDateRangeDatabase) > 0) {
            $selectedDate = $cart->date;
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['ticket'] -= $ticketCount;
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
                if ($ticketDateRangeDatabase[$key]['ticket'] >= 0) {
                    $cart->save();
                    $availability->save();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }
    }

    public function soldCountIncrement($optionID, $ticketCount, $cart) {
        $jsonq = new Jsonq();
        $option = Option::findOrFail($optionID);
        $availability = $option->avs()->first();

        if (!$availability) {
            return $this->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }

        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);

        if (count($ticketHourlyDatabase) > 0) {
            $res = $jsonq->json($availability->hourly);
            $result = $res->where('day', '=', $cart->date)->where('hour', '=', json_decode($cart->hour, true)[0]['hour'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['sold'] = $ticketHourlyDatabase[$key]['sold'] + $ticketCount;
                $availability->hourly = json_encode($ticketHourlyDatabase);
                if ($ticketHourlyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $cart->save();
                    $res->reset();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDailyDatabase) > 0) {
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $cart->date)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['sold'] += $ticketCount;
                $availability->daily = json_encode($ticketDailyDatabase);
                if ($ticketDailyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $cart->save();
                    $res->reset();
                    return ['result'=>'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDateRangeDatabase) > 0) {
            $selectedDate = $cart->date;
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['sold'] += $ticketCount;
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
                if ($ticketDateRangeDatabase[$key]['ticket'] >= 0) {
                    $cart->save();
                    $availability->save();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }
    }

    /**
     * @param $booking
     * @return array
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function ticketCountIncrement($booking)
    {
        $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $cart = Cart::where('referenceCode', '=', $booking->reservationRefCode)->first();
        $availability = $option->avs()->first();
        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);
        $selectedDate = $booking->date;
        $jsonq = new Jsonq();
        $selectedHour = json_decode($booking->hour, true)[0]['hour'];
        if (count($ticketHourlyDatabase) > 0) {
            $ticketCount = $cart->ticketCount;
            $res = $jsonq->json($availability->hourly);
            $res->where('day', '=', $selectedDate)->where('hour', '=', $selectedHour)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['ticket'] = json_encode($ticketHourlyDatabase[$key]['ticket'] + $ticketCount);
                $ticketHourlyDatabase[$key]['sold'] = json_encode($ticketHourlyDatabase[$key]['sold'] - $ticketCount);
                $availability->hourly = json_encode($ticketHourlyDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
        } else if (count($ticketDailyDatabase) > 0) {
            $ticketCount = $cart->ticketCount;
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $selectedDate)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['ticket'] = json_encode($ticketDailyDatabase[$key]['ticket'] + $ticketCount);
                $ticketDailyDatabase[$key]['sold'] = json_encode($ticketDailyDatabase[$key]['sold'] - $ticketCount);
                $availability->daily = json_encode($ticketDailyDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
        } else if (count($ticketDateRangeDatabase) > 0) {
            $ticketCount = $cart->ticketCount;
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['ticket'] = json_encode($ticketDateRangeDatabase[$key]['ticket'] + $ticketCount);
                $ticketDateRangeDatabase[$key]['sold'] = json_encode($ticketDateRangeDatabase[$key]['sold'] - $ticketCount);
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
        }

        return ['result' => $this->throwErrors('INTERNAL_SYSTEM_FAILURE')];
    }

    /**
     * @param $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkParameters($parameters)
    {
        foreach ($parameters as $parameter) {
            if ($parameter['name'] == 'CZ_API_HOST') {
                if ($parameter['value'] != 'cityturs.com') {
                    return $this->throwErrors('API_HOST_FAILURE');
                }
            } elseif ($parameter['name'] == 'CZ_API_USERNAME') {
                if ($parameter['value'] != 'cityzorecom') {
                    return $this->throwErrors('API_CREDENTIALS_FAILURE');
                }
            } elseif ($parameter['name'] == 'CZ_API_PASSWORD') {
                if ($parameter['value'] != '7TX<T/$(2uM7m~xF') {
                    return $this->throwErrors('API_CREDENTIALS_FAILURE');
                }
            }
        }
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    function unique_multidimensional_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    /**
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function throwErrors($type)
    {
        $errorArray = [
            'errorCode'=> $type,
            'errorMessage' => $this->errorMessages[$type]
        ];
        return response()->json($errorArray);
    }

    /**
     * @param $reservationData
     * @param $bokunLog
     * @param $type
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function arrayIntToStringForDateOrHour($reservationData, $bokunLog, $type)
    {
        if ($type == 'DATE') {
            if ($reservationData['date']['day'] < 10 && $reservationData['date']['month'] > 10) {
                $date = '0'.$reservationData['date']['day'] . '-' . $reservationData['date']['month'] . '-' . $reservationData['date']['year'];
            } else if ($reservationData['date']['month'] < 10 && $reservationData['date']['day'] > 10) {
                $date = $reservationData['date']['day'] . '-' . '0'.$reservationData['date']['month'] . '-' . $reservationData['date']['year'];
            } else if ($reservationData['date']['day'] < 10 && $reservationData['date']['month'] < 10) {
                $date = '0'.$reservationData['date']['day'] . '-' .'0'.$reservationData['date']['month'] . '-' . $reservationData['date']['year'];
            } else {
                $date = $reservationData['date']['day'] . '-' . $reservationData['date']['month'] . '-' . $reservationData['date']['year'];
            }
            return $date;
        }
        if ($type == 'HOUR') {
            if (array_key_exists('time', $reservationData) && !is_null($reservationData['time'])) {
                if ($reservationData['time']['hour'] < 10 && $reservationData['time']['minute'] >= 10) {
                    $hour = '0'.$reservationData['time']['hour'] . ':' . $reservationData['time']['minute'];
                } else if ($reservationData['time']['hour'] >= 10 && $reservationData['time']['minute'] < 10) {
                    if ($reservationData['time']['minute'] == 0) {
                        $hour = $reservationData['time']['hour'] . ':' . '00';
                    } else {
                        $hour = $reservationData['time']['hour'] . ':' . '0'.$reservationData['time']['minute'];
                    }
                } else if ($reservationData['time']['hour'] < 10 && $reservationData['time']['minute'] < 10) {
                    if ($reservationData['time']['minute'] == 0) {
                        $hour = '0'.$reservationData['time']['hour'] . ':' . '00';
                    } else {
                        $hour = '0'.$reservationData['time']['hour'] . ':' . '0'.$reservationData['time']['minute'];
                    }
                } else {
                    $hour = $reservationData['time']['hour'] . ':' . $reservationData['time']['minute'];
                }
            }
            return $hour;
        } else {
            $bokunLog->error = $this->throwErrors('NO_AVAILABILITY');
            $bokunLog->save();
            return $this->throwErrors('NO_AVAILABILITY');
        }
    }

    /**
     * @param $hour
     * @return array
     */
    public function stringToIntHourOrMinute($hour)
    {
            $hourPart = (int)(explode(':',$hour)[0]);
            $minutePart = (int)(explode(':', $hour)[1]);

            return [
                'hour' => $hourPart,
                'minute' => $minutePart
            ];
    }
}
