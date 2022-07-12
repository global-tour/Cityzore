<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmail;
use App\Option;
use App\Platform;
use App\Product;
use Carbon\Carbon;
use Faker\Provider\Address;
use Faker\Provider\Text;
use Faker\Provider\UserAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkMailController extends Controller
{

    public $columns = [
        'check',
        'dateForSort',
        'optionRefCode',
        'bookingRefCode',
        'platformID',
        'status',
        'created_at',
        'actions'
    ];

    public function index()
    {
        return view('panel.bulk-mail.index');
    }

    public function getBookingForMail(Request $request)
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
        $travelledDate = $request->input('columns.1.search.value');
        $tour = $request->input('columns.2.search.value');
        $bookingRef = $request->input('columns.3.search.value');
        $platform = $request->input('columns.4.search.value');
        $status = $request->input('columns.5.search.value');
        $bookedDate = $request->input('columns.6.search.value');

        /*
         * Search
         *
         */
        $search = $request->input('search.value');

        /*
         * Query Start
         *
         */
        $query = Booking::skip($start)->take($limit)->orderBy($column, $dir);
        $queryC = Booking::query();

        /*
         * Query creating
         */
        if ($tour) {
            $query->whereIn('optionRefCode', explode(',', $tour));
            $queryC->whereIn('optionRefCode', explode(',', $tour));
        }

        if ($bookingRef) {
            $query->where('bookingRefCode', 'like', '%' . $bookingRef . '%');
            $queryC->where('bookingRefCode', 'like', '%' . $bookingRef . '%');
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

        if ($platform) {
            $query->whereIn('platformID', explode(',', $platform));
            $queryC->whereIn('platformID', explode(',', $platform));
        }

        if ($status) {
            $query->where('status', $status);
            $queryC->where('status', $status);
        }

        /*
         * Search
         *
         */
        $query->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('productRefCode', 'like', '%' . $search . '%')
                    ->orWhere('optionRefCode', 'like', '%' . $search . '%')
                    ->orWhere('reservationRefCode', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%')
                    ->orWhere('gygBookingReference', 'like', '%' . $search . '%')
                    ->orWhere('travelers', 'like', '%' . $search . '%')
                    ->orWhere('fullName', 'like', '%' . $search . '%')
                    ->orWhereHas(
                        'bookingOption', function ($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%');
                    });
            });
        });

        /*
         * Search For count
         */
        $queryC->where(function ($q) use ($search) {
            $q->when(!is_null($search), function ($qq) use ($search) {
                $qq->where('productRefCode', 'like', '%' . $search . '%')
                    ->orWhere('optionRefCode', 'like', '%' . $search . '%')
                    ->orWhere('reservationRefCode', 'like', '%' . $search . '%')
                    ->orWhere('bookingRefCode', 'like', '%' . $search . '%')
                    ->orWhere('gygBookingReference', 'like', '%' . $search . '%')
                    ->orWhere('travelers', 'like', '%' . $search . '%')
                    ->orWhere('fullName', 'like', '%' . $search . '%')
                    ->orWhereHas(
                        'bookingOption', function ($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%');
                    });
            });
        });

        $recordsFiltered = $queryC->count();
        $data['data'] = $query->with(['bookingOption', 'platform'])->get()->map(function ($item) {
            return [
                'check' => $item->id,
                'dateForSort' => $item->dateTime ? Carbon::make(is_array(json_decode($item->dateTime)) ? json_decode($item->dateTime, true)[0]['dateTime'] : $item->dateTime)->format('D, d-F-Y') : '',
                'option' => $item->bookingOption->title ?? '',
                'test2' => $item->bookingRefCode ?? '',
                'created_at' => Carbon::make($item->created_at)->format('D, d-F-Y'),
                'platform' => $item->platform->name ?? '',
                'status' => $item->status ?? '',
                'actions' => json_decode($item->travelers, 1)[0]['email'] ?? ''
            ];
        });

        $data['recordsFiltered'] = $recordsFiltered;
        $data['recordsTotal'] = $recordsFiltered;


        return response()->json($data);
    }

    public function getPlatforms(Request $request)
    {
        $data = [];

        $query = Platform::select('id', 'name');

        foreach (explode(' ', $request->q) as $word) {
            $query->where('name', 'like', '%' . $word . '%');
        }

        $data['items'] = $query->where('status', 1)
            ->get()->take(7);

        return response()->json($data);
    }

    public function getPlatformsById($ids)
    {
        $platforms = Platform::select('id', 'name')->whereIn('id', explode(',', $ids))->get();

        return response()->json($platforms);
    }

    public function getProducts(Request $request)
    {
        $data = [];

        $query = Product::with(['prodOpt']);

        foreach (explode(' ', $request->q) as $word) {
            $query->where('title', 'like', '%' . $word . '%');
        }

        $data['items'] = $query->orWhere('referenceCode', 'like', '%' . $request->q . '%')
            ->orWhere('id', $request->q)
            ->get()
            ->take(7)->map(function ($item) {
                $data['id'] = $item->referenceCode;
                $data['text'] = "#{$item->id} {$item->title}";
                foreach ($item->prodOpt as $key => $option) {
                    $data['children'][$key]['id'] = $option->referenceCode;
                    $data['children'][$key]['text'] = $option->title;
                }
                return $data;
            })->toArray();

        return response()->json($data);
    }

    public function getOptions(Request $request)
    {
        $data = [];

        $query = Option::select('referenceCode', 'title');

        foreach (explode(' ', $request->q) as $word) {
            $query->where('title', 'like', '%' . $word . '%');
        }

        $data['items'] = $query->orWhere('referenceCode', 'like', '%' . $request->q . '%')
            ->get()->take(7);

        return response()->json($data);
    }

    public function getOptionsByRef($ref)
    {
        $options = Option::select('referenceCode', 'title')->whereIn('referenceCode', explode(',', $ref))->get();

        return response()->json($options);
    }

    public function sendMails(Request $request)
    {
        $bookings = Booking::whereIn('id', explode(',', $request->selected))->get();

        $details = [];
        $details['emails'] = [];
        $details['content'] = $request->input('content');
        $details['subject'] = $request->input('subject');

        foreach ($bookings as $item) {
            if ($item->travelers) {

                $email = json_decode($item->travelers, 1)[0]['email'];

                if (!in_array($email, $details['emails'])) {
                    $details['emails'][] = $email;
                }

            }
        }

        try {
            dispatch(new SendBulkEmail($details));
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

        return response()->json('Mails queued');
    }

}
