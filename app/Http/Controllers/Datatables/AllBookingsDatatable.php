<?php

namespace App\Http\Controllers\Datatables;

use App\Booking;
use App\Http\Controllers\Helpers\CryptRelated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function GuzzleHttp\Psr7\str;

class AllBookingsDatatable
{
    protected $columns = [
        'dateForSort',
        'optionRefCode',
        'created_at',
        'status',
        'platformID',
        'bookingInformation',
        'invoiceID',
        'actions'
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
        $column = $this->columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        /*
         * Inputs
         *
         */
        $travelledDate = $request->input('columns.0.search.value');
        $tour = $request->input('columns.1.search.value');
        $bookedDate = $request->input('columns.2.search.value');
        $status = $request->input('columns.3.search.value');
        $platform = $request->input('columns.4.search.value');
        $bookingInformation = $request->input('columns.5.search.value');
        $invoice = $request->input('columns.6.search.value');

        /*
         * Search
         *
         */
        $search = $request->input('search.value');

        /*
         * Query Start
         *
         */
        if (!$limit) {
            $query = Booking::orderBy($column, $dir);
        }else{
            $query = Booking::skip($start)->take($limit)->orderBy($column, $dir);
        }

        $queryC = Booking::query();

        $query->where('status', '!=', 1);
        $queryC->where('status', '!=', 1);

        /*
         * Supplier Check
         **/
        if (auth()->guard('supplier')->check()) {
            $query->where(function ($q) {
                $q->where('companyID', auth()->guard('supplier')->user()->id);
                $q->orWhere(function ($q2) {
                    $q2->whereHas("bookingOption", function ($sub) {
                        $sub->where("rCodeID", auth()->guard('supplier')->user()->id);
                    });
                });
            });
        }

        /*
         * Query creating
         */
        if ($tour) {
            $query->whereIn('optionRefCode', explode(',', $tour));
            $queryC->whereIn('optionRefCode', explode(',', $tour));
        }

        if ($platform) {
            $query->where('platformID', $platform);
            $queryC->where('platformID', $platform);
        }

        if ($bookedDate) {
            $fullDate = explode('-', str_replace(' ', '', $bookedDate));
            $sDate = Carbon::make($fullDate[0])->format('Y-m-d 00:00:00');
            $eDate = Carbon::make($fullDate[1])->format('Y-m-d 23:59:59');

            $query->whereBetween('created_at', [$sDate, $eDate]);
            $queryC->whereBetween('created_at', [$sDate, $eDate]);

        }

        if ($travelledDate) {
            $fullDate = explode('-', str_replace(' ', '', $travelledDate));
            $sDate = Carbon::make($fullDate[0])->format('Y-m-d 00:00:00');
            $eDate = Carbon::make($fullDate[1])->format('Y-m-d 23:59:59');

            $query->whereBetween('dateForSort', [$sDate, $eDate]);
            $queryC->whereBetween('dateForSort', [$sDate, $eDate]);
        }

        if ($invoice) {

            $arr = array_filter(explode('|', $invoice));

            if (isset($arr[1])) {

                $query->whereHas('invoc', function($q) use ($arr){
                    $q->where('paymentMethod', $arr[1]);
                    $q->where('referenceCode', 'like', '%' . $arr[0] . '%');
                });

                $queryC->whereHas('invoc', function($q) use ($arr){
                    $q->where('paymentMethod', $arr[1]);
                    $q->where('referenceCode', 'like', '%' . $arr[0] . '%');
                });

            }else{

                $query->whereHas('invoc', function($q) use ($arr){
                    $q->where('referenceCode', 'like', '%' . $arr[0] . '%');
                    $q->orWhere('paymentMethod', $arr[0]);
                });

                $queryC->whereHas('invoc', function($q) use ($arr){
                    $q->where('referenceCode', 'like', '%' . $arr[0] . '%');
                    $q->orWhere('paymentMethod', $arr[0]);
                });

            }

        }

        if ($bookingInformation) {


            if (in_array(1, explode(',', $bookingInformation))) {
                $query->whereHas('extra_files');
                $queryC->whereHas('extra_files');
            }

            if (in_array(2, explode(',', $bookingInformation))) {
                $query->whereDoesntHave('extra_files');
                $queryC->whereDoesntHave('extra_files');
            }

            if (in_array(3, explode(',', $bookingInformation))) {
                $query->whereHas('contactBooking', function ($q){
                    $q->where("status", '1');
                });
                $queryC->whereHas('contactBooking', function ($q){
                    $q->where("status", '1');
                });
            }

            if (in_array(4, explode(',', $bookingInformation))) {
                $query->whereHas('contactBooking', function ($q){
                    $q->where("status", '0');
                });
                $queryC->whereHas('contactBooking', function ($q){
                    $q->where("status", '0');
                });
            }

            if (in_array(5, explode(',', $bookingInformation))) {
                $query->whereHas('contactBooking', function ($q){
                    $q->where("check_information->status", 'like', "%true%");
                });
                $queryC->whereHas('contactBooking', function ($q){
                    $q->where("check_information->status", 'like', "%true%");
                });
            }

            if (in_array(6, explode(',', $bookingInformation))) {
                $query->whereHas('contactBooking', function ($q){
                    $q->where("check_information->status", 'like', "%false%");
                });
                $queryC->whereHas('contactBooking', function ($q){
                    $q->where("check_information->status", 'like', "%false%");
                });
            }

            if (in_array(7, explode(',', $bookingInformation))) {
                $query->whereNotNull('specialRefCode');
                $queryC->whereNotNull('specialRefCode');
            }

            if (in_array(8, explode(',', $bookingInformation))) {
                $query->whereNull('specialRefCode');
                $queryC->whereNull('specialRefCode');
            }
        }

        if ($status) {

            $statuses = explode(',', $status);

            foreach ($statuses as $k => $stat){
                if (!$k) {

                    if ($stat == 1) {
                        $query->where('status', 0);
                        $queryC->where('status', 0);
                    }else{
                        $query->whereIn('status', explode('-', $stat));
                        $queryC->whereIn('status', explode('-', $stat));
                    }
                }else{

                    if ($stat == 1) {
                        $query->orWhere('status', 0);
                        $queryC->orWhere('status', 0);
                    }else{
                        $query->orWhereIn('status', explode('-', $stat));
                        $queryC->orWhereIn('status', explode('-', $stat));
                    }
                }

            }

        }

        /*
         * Search
         *
         */
        $query->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('productRefCode', 'like', '%' . $search . '%')
                    ->orWhere('reservationRefCode', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%')
                    ->orWhere('gygBookingReference', 'like', '%' . $search . '%')
                    ->orWhere('fullName', 'like', '%' . $search . '%')
                    ->orWhere('travelers', 'like', '%' . $search . '%');
            });
        });

        /*
         * Search For count
         */
        $queryC->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('productRefCode', 'like', '%' . $search . '%')
                    ->orWhere('reservationRefCode', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%')
                    ->orWhere('gygBookingReference', 'like', '%' . $search . '%')
                    ->orWhere('fullName', 'like', '%' . $search . '%')
                    ->orWhere('travelers', 'like', '%' . $search . '%');
            });
        });

        $recordsFiltered = $queryC->count();
        $data['data'] = $query->with(['bookingProduct', 'bookingOption', 'platform', 'invoc'])->get()->map(function ($item) {


            if (!is_null($item->bookingItems)) {

                foreach (json_decode($item->bookingItems, 1) as $cat){

                    if (isset($counter[strtolower($cat['category'])])) {
                        $counter[strtolower($cat['category'])] += $cat['count'];
                    }else{
                        $counter[strtolower($cat['category'])] = $cat['count'];
                    }
                }

            }

            return [
                $this->columns[0] => $item->bookingDateTime,
                $this->columns[1] => $item->tourDetails,
                $this->columns[2] => $item->bookingDetails,
                $this->columns[3] => $item->bookingStatus ?? '',
                $this->columns[4] => $item->platform->name ?? '',
                $this->columns[5] => $item->bookingInformation,
                $this->columns[6] => $item->salesInformation,
                $this->columns[7] => '',
                'info'            => [
                    'id'          => $item->id,
                    'voucher'     => url('/print-pdf/' . (new CryptRelated())->encrypt($item->id)),
                    'invoice'     => url('/print-invoice/' . (new CryptRelated())->encrypt($item->id))
                ]
            ];
        });


        $data['recordsFiltered'] = $recordsFiltered;
        $data['recordsTotal'] = $recordsFiltered;
        $data['lastInput'] = $request->input();

        return response()->json($data);
    }
}
