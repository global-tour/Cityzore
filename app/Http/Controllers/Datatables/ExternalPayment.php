<?php

namespace App\Http\Controllers\Datatables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\ExternalPayment as PaymentModel;

class ExternalPayment
{

    protected $columns = [
        'id',
        'payment_link',
        'referenceCode',
        'bookingRefCode',
        'email',
        'message',
        'price',
        'paid',
        'created_at',
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
         * Search
         *
         */
        $search = $request->input('search.value');

        $query = PaymentModel::skip($start)->take($limit)->orderBy($column, $dir);
        $queryC = PaymentModel::query();


        /*
         * Search
         *
         */
        $query->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('email', 'like', '%' . $search . '%')
                    ->orWhere('referenceCode', 'like', '%' . $search . '%')
                    ->orWhere('invoiceID', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%');
            });
        });

        /*
         * Search For count
         */
        $queryC->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('email', 'like', '%' . $search . '%')
                    ->orWhere('referenceCode', 'like', '%' . $search . '%')
                    ->orWhere('invoiceID', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%');
            });
        });

        $recordsFiltered = $queryC->count();
        $data['data'] = $query->get()->map(function ($item) {

            return [
                $this->columns[0] => $item->id,
                $this->columns[1] => $item->payment_link,
                $this->columns[2] => $item->referenceCode,
                $this->columns[3] => $item->bookingRefCode ?? '',
                $this->columns[4] => $item->email ?? '',
                $this->columns[5] => $item->message ?? '',
                $this->columns[6] => $item->currency_code . ' ' . $item->price,
                $this->columns[7] => $item->is_paid,
                $this->columns[8] => Carbon::make($item->created_at)->format('Y-m-d H:i:s'),
                $this->columns[9] => '',
            ];
        });


        $data['recordsFiltered'] = $recordsFiltered;
        $data['recordsTotal'] = $recordsFiltered;
        $data['lastInput'] = $request->input();

        return response()->json($data);
    }
}
