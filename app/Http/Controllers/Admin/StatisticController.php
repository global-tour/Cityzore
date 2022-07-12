<?php

namespace App\Http\Controllers\Admin;

use App\Barcode;
use App\Booking;
use App\Checkin;
use App\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        $bookings = Booking::whereDate('created_at', '>=', Carbon::today()->subDays(30))->get();
        $hourStats = $bookings->groupBy(function($booking) {
            return Carbon::parse($booking->created_at)->format('H');
        });
        $languageStats = $bookings->groupBy(function($booking) {
            return strtoupper($booking->language);
        });
        $deviceTypeStats = $bookings->groupBy(function($booking) {
            return strtoupper($booking->deviceType);
        });

        $statsArr = [
            "hour" => [
                "categories" => [],
                "data" => []
            ],
            "language" => [
                "categories" => [],
                "data" => []
            ],
            "deviceType" => [
                "categories" => [],
                "data" => []
            ]
        ];

        $hourStats = $hourStats->toArray();
        ksort($hourStats);
        foreach($hourStats as $key => $booking) {
            array_push($statsArr["hour"]["categories"], $key . '-' . Carbon::createFromFormat('H', $key)->addHour(1)->format('H'));
            array_push($statsArr["hour"]["data"], count($booking));
        }
        $languageStats = $languageStats->toArray();
        ksort($languageStats);
        foreach($languageStats as $key => $booking) {
            if(strlen($key) > 0) {
                array_push($statsArr["language"]["categories"], $key);
                array_push($statsArr["language"]["data"], count($booking));
            }
        }
        $deviceTypeStats = $deviceTypeStats->toArray();
        ksort($deviceTypeStats);
        foreach($deviceTypeStats as $key => $booking) {
            if(strlen($key) > 0) {
                array_push($statsArr["deviceType"]["categories"], $key);
                array_push($statsArr["deviceType"]["data"], count($booking));
            }
        }

        return view('panel.statistics.booking-statistic', compact('statsArr'));
    }

    public function barcodeAnalysis()
    {
        $start_date_raw = '2021-09-12';
        $end_date_raw = '2021-10-11';

        $start_date_int = strtotime($start_date_raw);
        $end_date_int = strtotime($end_date_raw);
        $barcodeArray = array();
        while ($start_date_int <= $end_date_int) {
            $temp = array();
            $barcode_date_array = array();
            $temp['barcode_date'] = date('Y-m-d', $start_date_int);
            $after_date_int = $start_date_int + (60 * 60 * 24) - 1;
            $barcode_raw = Barcode::whereBetween('updated_at', [date('Y-m-d H:i:s', $start_date_int), date('Y-m-d H:i:s', $after_date_int)])->get('booking_date')->toArray();

            foreach ($barcode_raw as $b) {
                if ($b['booking_date'] != null)
                    array_push($barcode_date_array, $b['booking_date']);
            }
            $temp['booking_date'] = array_count_values($barcode_date_array);
            $temp['total'] = array_sum(array_values($temp['booking_date']));
            array_push($barcodeArray, $temp);

            $start_date_int += 60 * 60 * 24;
        }

        return view('panel.statistics.barcode-analysis', ['barcodeArray' => $barcodeArray]);

    }

    public function barcodeAnalysisUpdate(Request $request)
    {
        $start_date_raw = $request->startDate;
        $end_date_raw = $request->finishDate;

        $start_date_int = strtotime($start_date_raw);
        $end_date_int = strtotime($end_date_raw);
        $barcodeArray = array();
        while ($start_date_int <= $end_date_int) {
            $temp = array();
            $barcode_date_array = array();
            $temp['barcode_date'] = date('Y-m-d', $start_date_int);
            $after_date_int = $start_date_int + (60 * 60 * 24) - 1;
            $barcode_raw = Barcode::whereBetween('updated_at', [date('Y-m-d H:i:s', $start_date_int), date('Y-m-d H:i:s', $after_date_int)])->get('booking_date')->toArray();

            foreach ($barcode_raw as $b) {
                if ($b['booking_date'] != null)
                    array_push($barcode_date_array, $b['booking_date']);
            }
            $temp['booking_date'] = array_count_values($barcode_date_array);
            $temp['total'] = array_sum(array_values($temp['booking_date']));
            array_push($barcodeArray, $temp);

            $start_date_int += 60 * 60 * 24;
        }

        $resultHtml = '';
        foreach ($barcodeArray as $b) {
            $resultHtml .= '<tr>';
            $resultHtml .= '<td><span class="txt-dark weight-500">';
            $resultHtml .= $b['barcode_date'];
            $resultHtml .= '</span></td>';
            $resultHtml .= '<td>';
            foreach ($b['booking_date'] as $key => $value) {
                $resultHtml .= '<span class="label label-primary">';
                $resultHtml .= $key;
                $resultHtml .= ' <span class="label label-danger">';
                $resultHtml .= $value;
                $resultHtml .= '</span></span>';
            }
            $resultHtml .= ' </td><td class="text-center w-75"><span class="label label-warning ">';
            $resultHtml .= $b['total'];
            $resultHtml .= '</span></td></tr>';


        }
        return response()->json([
            'barcode' => $resultHtml
        ]);

    }

    public function uploadStatistic(Request $request, $which)
    {
        $start_date = $request->startDate;
        $end_date = $request->finishDate;
        $booking_raw = Booking::whereBetween('dateForSort', [$start_date, $end_date])->select('dateForSort', 'created_at', 'id', 'optionRefCode', 'status','bookingItems')->get();
        switch ($which) {
            case 'chart':
                $start_date_time = strtotime($start_date);
                $end_date_time = strtotime($end_date);

                $order_diff_avg = array();
                $order_diff_noShow = array();
                $order_diff_date = array();
                $bookings_size = 0;

                while ($start_date_time <= $end_date_time) {
                    $bookings = $booking_raw->filter(function ($item) use ($start_date_time) {
                        return $item->dateForSort == date('Y-m-d', $start_date_time);
                    });

                    $bookings_size = sizeof($bookings);
                    $noShow = 0;
                    $total = 0;
                    $average = 0;
                    $date = 'XX-XX';
                    if ($bookings_size > 0) {
                        foreach ($bookings as $booking) {
                            $date = date('d-M', $start_date_time);
                            $diff_date = date_diff(date_create($booking->dateForSort), date_create($booking->created_at), false);
                            $total += $diff_date->days;
                            $checkins = Checkin::where('booking_id', intval($booking->id))->orderByDesc('id')->select('person', 'ticket')->first();

                            if ($checkins != null) {
                                $ordered_ticket_person = 0;
                                foreach ($checkins->person as $p) {
                                    $ordered_ticket_person += $p;
                                }
                                $noShow += $checkins->ticket - $ordered_ticket_person;
                            }
                        }
                        $average = $total / $bookings_size;
                    }


                    array_push($order_diff_avg, floor($average));
                    array_push($order_diff_noShow, $noShow);
                    array_push($order_diff_date, $date);
                    $start_date_time += 60 * 60 * 24;
                }

                return response()->json([
                    'order_diff_avg' => $order_diff_avg,
                    'order_diff_noShow' => $order_diff_noShow,
                    'order_diff_date' => $order_diff_date,
                ]);

            case 'chart-opt':
                $option_arr = array();
                $result_arr = array();
                $options = $booking_raw->filter(function ($item) {
                    return $item->status == 0;
                });

                foreach ($options as $option) {
                    array_push($option_arr, $option->optionRefCode);
                }

                $result = array_count_values($option_arr);
                foreach ($result as $key => $value) {
                    $opt_name = Option::where('referenceCode', $key)->value('title');

                    if ($opt_name != null) {
                        $temp['label']='';
                        $temp['value'] = $value;
                        $temp['adult'] =0;
                        $temp['child'] =0;
                        $temp['infant'] =0;
                        $temp['eu_citizen'] =0;
                        $temp['youth'] =0;
                        $families = $booking_raw->filter(function ($item) use($key) {
                            return $item->optionRefCode == $key;
                        });
                        foreach($families as $family){
                            $items=json_decode($family->bookingItems);
                            foreach ($items as $item){
                                switch ($item->category){
                                    case "ADULT":
                                        $temp['adult']+=intval($item->count);
                                        break;
                                    case "CHILD":
                                        $temp['child']+=intval($item->count);
                                        break;
                                    case "INFANT":
                                        $temp['infant']+=intval($item->count);
                                        break;
                                    case "EU_CITIZEN":
                                        $temp['eu_citizen']+=intval($item->count);
                                        break;
                                    case "YOUTH":
                                        $temp['youth']+=intval($item->count);
                                        break;

                                }
                            }
                        }
                        if($temp['adult']>0)    $temp['label'].='(A):'.$temp['adult'].'-';
                        if($temp['child']>0)    $temp['label'].='(C):'.$temp['child'].'-';
                        if($temp['infant']>0)   $temp['label'].='(I):'.$temp['infant'].'-';
                        if($temp['eu_citizen']>0)$temp['label'].='(E):'.$temp['eu_citizen'].'-';
                        if($temp['youth']>0)    $temp['label'].='(Y):'.$temp['youth'].'-';
                        $temp['label'] .= $opt_name;
                        array_push($result_arr, $temp);
                    }
                }
                $optVal = array_column($result_arr, 'value');
                array_multisort($optVal, SORT_DESC, $result_arr);
                $result_label_arr = array_column($result_arr, 'label');
                $result_value_arr = array_column($result_arr, 'value');




                return response()->json([
                    'opt_label' => $result_label_arr,
                    'opt_value' => $result_value_arr,
                ]);
            case 'chart-opt-cancel':
                $option_arr_c = array();
                $result_arr_c = array();
                $options_c = $booking_raw->filter(function ($item) {
                    return $item->status == 2 || $item->status == 3;
                });
                foreach ($options_c as $option) {
                    array_push($option_arr_c, $option->optionRefCode);
                }

                $result_c = array_count_values($option_arr_c);
                foreach ($result_c as $key => $value) {
                    $opt_name = Option::where('referenceCode', $key)->value('title');
                    if ($opt_name != null) {
                        $temp['label']='';
                        $temp['value'] = $value;
                        $temp['adult'] =0;
                        $temp['child'] =0;
                        $temp['infant'] =0;
                        $temp['eu_citizen'] =0;
                        $temp['youth'] =0;
                        $families = $booking_raw->filter(function ($item) use($key) {
                            return $item->optionRefCode == $key;
                        });
                        foreach($families as $family){
                            $items=json_decode($family->bookingItems);
                            foreach ($items as $item){
                                switch ($item->category){
                                    case "ADULT":
                                        $temp['adult']+=intval($item->count);
                                        break;
                                    case "CHILD":
                                        $temp['child']+=intval($item->count);
                                        break;
                                    case "INFANT":
                                        $temp['infant']+=intval($item->count);
                                        break;
                                    case "EU_CITIZEN":
                                        $temp['eu_citizen']+=intval($item->count);
                                        break;
                                    case "YOUTH":
                                        $temp['youth']+=intval($item->count);
                                        break;

                                }
                            }
                        }
                        if($temp['adult']>0)    $temp['label'].='(A):'.$temp['adult'].'-';
                        if($temp['child']>0)    $temp['label'].='(C):'.$temp['child'].'-';
                        if($temp['infant']>0)   $temp['label'].='(I):'.$temp['infant'].'-';
                        if($temp['eu_citizen']>0)$temp['label'].='(E):'.$temp['eu_citizen'].'-';
                        if($temp['youth']>0)    $temp['label'].='(Y):'.$temp['youth'].'-';
                        $temp['label'] .= $opt_name;
                        array_push($result_arr_c, $temp);
                    }
                }
                $optValCancel = array_column($result_arr_c, 'value');
                array_multisort($optValCancel, SORT_DESC, $result_arr_c);
                $result_label_arr_cancel = array_column($result_arr_c, 'label');
                $result_value_arr_cancel = array_column($result_arr_c, 'value');

                return response()->json([
                    'opt_label_c' => $result_label_arr_cancel,
                    'opt_value_c' => $result_value_arr_cancel,
                ]);
        }


    }

    public function readyStatistic(Request $request)
    {
        $start_date = $request->startDate;
        $end_date = $request->finishDate;

        $start_date_time = strtotime($start_date);
        $end_date_time = strtotime($end_date);

        $order_diff_avg = array();
        $order_diff_noShow = array();
        $order_diff_date = array();
        $bookings_size = 0;
        $booking_raw = Booking::whereBetween('dateForSort', [$start_date, $end_date])->select('dateForSort', 'created_at', 'id', 'optionRefCode', 'status','bookingItems')->get();

        while ($start_date_time <= $end_date_time) {
            $bookings = $booking_raw->filter(function ($item) use ($start_date_time) {
                return $item->dateForSort == date('Y-m-d', $start_date_time);
            });

            $bookings_size = sizeof($bookings);
            $noShow = 0;
            $total = 0;
            $average = 0;
            $date = 'XX-XX';
            if ($bookings_size > 0) {
                foreach ($bookings as $booking) {
                    $date = date('d-M', $start_date_time);
                    $diff_date = date_diff(date_create($booking->dateForSort), date_create($booking->created_at), false);
                    $total += $diff_date->days;
                    $checkins = Checkin::where('booking_id', intval($booking->id))->orderByDesc('id')->select('person', 'ticket')->first();

                    if ($checkins != null) {
                        $ordered_ticket_person = 0;
                        foreach ($checkins->person as $p) {
                            $ordered_ticket_person += $p;
                        }
                        $noShow += $checkins->ticket - $ordered_ticket_person;
                    }
                }
                $average = $total / $bookings_size;
            }

            array_push($order_diff_avg, floor($average));
            array_push($order_diff_noShow, $noShow);
            array_push($order_diff_date, $date);
            $start_date_time += 60 * 60 * 24;
        }

        //----Option Statistic----
        $option_arr = array();
        $result_arr = array();
        $options = $booking_raw->filter(function ($item) {
            return $item->status == 0;
        });

        foreach ($options as $option) {
            array_push($option_arr, $option->optionRefCode);
        }

        $result = array_count_values($option_arr);
        foreach ($result as $key => $value) {
            $opt_name = Option::where('referenceCode', $key)->value('title');

            if ($opt_name != null) {
                $temp['label']='';
                $temp['value'] = $value;
                $temp['adult'] =0;
                $temp['child'] =0;
                $temp['infant'] =0;
                $temp['eu_citizen'] =0;
                $temp['youth'] =0;
                $families = $booking_raw->filter(function ($item) use($key) {
                    return $item->optionRefCode == $key;
                });
                foreach($families as $family){
                    $items=json_decode($family->bookingItems);
                    foreach ($items as $item){
                        switch ($item->category){
                            case "ADULT":
                                $temp['adult']+=intval($item->count);
                                break;
                            case "CHILD":
                                $temp['child']+=intval($item->count);
                                break;
                            case "INFANT":
                                $temp['infant']+=intval($item->count);
                                break;
                            case "EU_CITIZEN":
                                $temp['eu_citizen']+=intval($item->count);
                                break;
                            case "YOUTH":
                                $temp['youth']+=intval($item->count);
                                break;

                        }
                    }
                }
                if($temp['adult']>0)    $temp['label'].='(A):'.$temp['adult'].'-';
                if($temp['child']>0)    $temp['label'].='(C):'.$temp['child'].'-';
                if($temp['infant']>0)   $temp['label'].='(I):'.$temp['infant'].'-';
                if($temp['eu_citizen']>0)$temp['label'].='(E):'.$temp['eu_citizen'].'-';
                if($temp['youth']>0)    $temp['label'].='(Y):'.$temp['youth'].'-';
                $temp['label'] .= $opt_name;
                array_push($result_arr, $temp);
            }
        }
        $optVal = array_column($result_arr, 'value');
        array_multisort($optVal, SORT_DESC, $result_arr);
        $result_label_arr = array_column($result_arr, 'label');
        $result_value_arr = array_column($result_arr, 'value');

        //----Option Cancelled Statistic----
        $option_arr_c = array();
        $result_arr_c = array();
        $options_c = $booking_raw->filter(function ($item) {
            return $item->status == 2 || $item->status == 3;
        });
        foreach ($options_c as $option) {
            array_push($option_arr_c, $option->optionRefCode);
        }

        $result_c = array_count_values($option_arr_c);
        foreach ($result_c as $key => $value) {
            $opt_name = Option::where('referenceCode', $key)->value('title');
            if ($opt_name != null) {
                $temp['label']='';
                $temp['value'] = $value;
                $temp['adult'] =0;
                $temp['child'] =0;
                $temp['infant'] =0;
                $temp['eu_citizen'] =0;
                $temp['youth'] =0;
                $families = $booking_raw->filter(function ($item) use($key) {
                    return $item->optionRefCode == $key;
                });
                foreach($families as $family){
                    $items=json_decode($family->bookingItems);
                    foreach ($items as $item){
                        switch ($item->category){
                            case "ADULT":
                                $temp['adult']+=intval($item->count);
                                break;
                            case "CHILD":
                                $temp['child']+=intval($item->count);
                                break;
                            case "INFANT":
                                $temp['infant']+=intval($item->count);
                                break;
                            case "EU_CITIZEN":
                                $temp['eu_citizen']+=intval($item->count);
                                break;
                            case "YOUTH":
                                $temp['youth']+=intval($item->count);
                                break;

                        }
                    }
                }
                if($temp['adult']>0)    $temp['label'].='(A):'.$temp['adult'].'-';
                if($temp['child']>0)    $temp['label'].='(C):'.$temp['child'].'-';
                if($temp['infant']>0)   $temp['label'].='(I):'.$temp['infant'].'-';
                if($temp['eu_citizen']>0)$temp['label'].='(E):'.$temp['eu_citizen'].'-';
                if($temp['youth']>0)    $temp['label'].='(Y):'.$temp['youth'].'-';
                $temp['label'] .= $opt_name;
                array_push($result_arr_c, $temp);
            }
        }
        $optValCancel = array_column($result_arr_c, 'value');
        array_multisort($optValCancel, SORT_DESC, $result_arr_c);
        $result_label_arr_cancel = array_column($result_arr_c, 'label');
        $result_value_arr_cancel = array_column($result_arr_c, 'value');

        return response()->json([
            'order_diff_avg' => $order_diff_avg,
            'order_diff_noShow' => $order_diff_noShow,
            'order_diff_date' => $order_diff_date,
            'opt_label' => $result_label_arr,
            'opt_value' => $result_value_arr,
            'opt_label_c' => $result_label_arr_cancel,
            'opt_value_c' => $result_value_arr_cancel,
        ]);

    }


}
