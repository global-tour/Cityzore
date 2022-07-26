<?php

namespace App\Http\Controllers\Datatables;

use App\Booking;
use App\BookingLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentLogsTable
{

    protected $columns = [
        'processID',
        'userID',
        'option_title',
        'code',
        'created_at',
        'childRow'
    ];

    public function getRows(Request $request)
    {
        $data = [];

        /*
         * Pagination
         *
         */
        $start = $request->input('start');
        $limit = $request->input('length');

        /*
         * Orderable
         *
         */
        $column = $this->columns[$request->input('order.0.column') - 1];
        $dir = $request->input('order.0.dir');

        /*
         * Search
         *
         */
        $search = $request->input('search.value');

        $query = BookingLog::skip($start)->take($limit)->orderBy($column, $dir);
        $queryC = BookingLog::query();


        /*
         * Search
         *
         */
        $query->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->orWhere('processID', 'like', '%' . $search . '%')
                    ->orWhere('userID', 'like', '%' . $search . '%')
                    ->orWhere('optionID', 'like', '%' . $search . '%')
                    ->orWhereHas('option', function ($qqq) use ($search) {
                        foreach (explode(' ', $search) as $k => $val) {
                            $qqq->where('title', 'like', '%' . $val .'%');
                        }
                    });
            });
        });

        /*
         * Search For count
         */
        $queryC->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->orWhere('processID', 'like', '%' . $search . '%')
                    ->orWhere('userID', 'like', '%' . $search . '%')
                    ->orWhere('optionID', 'like', '%' . $search . '%')
                    ->orWhereHas('option', function ($qqq) use ($search) {
                        foreach (explode(' ', $search) as $k => $val) {
                                $qqq->where('title', 'like', '%' . $val .'%');
                        }
                    });
            });
        });

        $recordsFiltered = $queryC->count();

        $data['data'] = $query->with(['option', 'cart'])->get()->map(function ($item){

            $childRow = $item->record_fields;

            if ($item->cart) {
                $cat = '';
                foreach (json_decode($item->cart->bookingItems, 1) as $category){
                    $cat .= "{$category['count']}x {$category['category']} ~ ";
                }

                $childRow['items'] = rtrim($cat, '~ ');
                $childRow['totalPrice'] = $item->cart->totalPrice;
                $childRow['travelledDate'] = !is_null($item->cart->date)
                    ? $item->cart->date . ' ~ ' . $item->cart->hour
                    : $item->cart->dateTime;
            }

            return [
                $this->columns[0] => $item->processID,
                $this->columns[1] => $item->userID,
                $this->columns[2] => $item->option ? $item->option->title : '',
                $this->columns[3] => $item->code ?? '',
                $this->columns[4] => $item->created_at,
                $this->columns[5] => $childRow,
            ];
        });

         $data['recordsFiltered'] = $recordsFiltered;
         $data['recordsTotal'] = $recordsFiltered;
         $data['lastInput'] = $request->input();

        return response()->json($data);
    }
}
