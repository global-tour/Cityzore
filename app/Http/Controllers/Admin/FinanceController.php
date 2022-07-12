<?php

namespace App\Http\Controllers\Admin;

use App\Platform;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Booking;
use App\Exports\FinancesExport;
use App\Exports\FinancesExportForCommissioner;
use App\Admin;
use Maatwebsite\Excel\Facades\Excel;
use App\Billing;
use Storage;

class FinanceController extends Controller
{

    /**
     * Exports earnings to excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(Request $request)
    {
        if (!is_null($request->commissioner) && $request->commissioner != 0) {

            return Excel::download(new FinancesExportForCommissioner($request), 'finances.xlsx');
        }

        return Excel::download(new FinancesExport($request), 'finances.xlsx');
    }

    /**
     * Finance page only for supplier.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finance()
    {
        $supplier = Supplier::where('id', auth()->guard('supplier')->user()->id)->first();

        $arr = [];
        $from = $supplier->created_at->format('Y-m-d');
        $to = date('Y-m-d');
        $yearsRange = array_reverse(range(gmdate('Y', strtotime($from)), gmdate('Y', strtotime($to))));
        $bookingsArr = $this->fillBookings($supplier, 0, $arr, 'supplier');
        $bookingsArr = array_reverse($bookingsArr);
        $commissionRate = $supplier->comission;

        $monthNames = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        return view('panel.finance.index', ['financeMonthly' => $bookingsArr, 'years' => $yearsRange, 'monthNames' => $monthNames, 'supplier' => $supplier, 'model' => 'supplier', 'commissionRate' => $commissionRate]);
    }

    /**
     * Finance page only for admin.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function financeAdmin()
    {
        $suppliers = Supplier::whereNotNull('comission')->where('comission', '>', 0)->get();
        $commissioners = User::whereNotNull('commission')->where('commission', '>', 0)->get();
        $platforms = Platform::where('status', 1)->get();

        $areThereBookings = true;
        if (auth()->guard('supplier')->check()) {
            $areThereBookings = Booking::where('companyID', auth()->guard('supplier')->user()->id)->count() > 0 ? true : false;
        } else if (auth()->guard('subUser')->check()) {
            $supplier = Supplier::findOrFail(auth()->guard('subUser')->user()->supervisor);
            $areThereBookings = Booking::where('companyID', $supplier->id)->count() > 0 ? true : false;
        }
        return view('panel.finance.index-admin',
            [
                'suppliers' => $suppliers,
                'commissioners' => $commissioners,
                'areThereBookings' => $areThereBookings,
                'platforms' => $platforms
            ]
        );
    }


    public function financeBills(Request $request)
    {

        return view('panel.finance.bills');


    }

    /**
     * Gets bookings month by month cumulatively
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBookings(Request $request)
    {

        $model = 'supplier';
        $commissionRate = 0;
        if ($request->has('companySelect')) {
            if ($request->companySelect != '-1') {
                $supplier = Supplier::where('id', $request->companySelect)->first();
                $commissionRate = $supplier->comission;
            } else {
                $supplier = Admin::findOrFail(1);
                $model = 'admin';
            }
        } elseif ($request->has('platforms')) {
            $supplier = Platform::findOrFail($request->platforms);
            $model = 'platform';
        } else {
            if ($request->has('commissioner')) {
                $supplier = User::where('id', $request->commissioner)->first();
                $model = 'commissioner';
            } else {
                if (auth()->guard('supplier')->check()) {
                    $supplier = auth()->guard('supplier')->user();
                } else if (auth()->guard('subUser')->check()) {
                    $supplier = Supplier::findOrFail(auth()->guard('subUser')->user()->supervisor);
                }
                $model = 'supplier';
            }
            $commissionRate = $supplier->commission;
        }


        $createdAt = $supplier->created_at;

        $arr = [];
        $from = $createdAt->format('Y-m-d');
        $to = date('Y-m-d');
        $yearsRange = array_reverse(range(gmdate('Y', strtotime($from)), gmdate('Y', strtotime($to))));
        $bookingsArr = $this->fillBookings($supplier, 0, $arr, $model);
        $bookingsArr = array_reverse($bookingsArr);
        $monthNames = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        return view('panel.finance.index', [
            'financeMonthly' => $bookingsArr,
            'years' => $yearsRange,
            'monthNames' => $monthNames,
            'supplier' => $supplier,
            'model' => $model,
            'commissionRate' => $commissionRate
        ]);
    }

    /**ß
     * Fills bookings month by month cumulatively
     *
     * @param $supplier
     * @param $ind
     * @param $arr
     * @param $model
     * @return mixed
     */
    public function fillBookings($supplier, $ind, $arr, $model)
    {
        $companyID = -1;
        if (in_array($model, ['supplier', 'platform', 'commissioner'])) {
            $companyID = $supplier->id;
        }

        $registerDate = $supplier->created_at->addMonths($ind);
        $registerDateMonth = $registerDate->format('m');
        $registerDateYear = $registerDate->format('Y');
        $today = date('Y-m-d');

        if ($registerDate <= $today) {
            $financeBookings = Booking::where('gygBookingReference', null);
            if ($model != 'commissioner') {

                $financeBookings = $model == 'platform'
                    ? $financeBookings->where('platformID', $supplier->id)
                    : $financeBookings->where('companyID', $companyID);

            } else {
                $financeBookings = $financeBookings->where(function ($q) use ($companyID) {
                    $q->where('userID', $companyID)->orWhere('affiliateID', $companyID);
                });
            }
            $financeBookings = $financeBookings->where('status', 0)
                ->whereMonth('dateForSort', $registerDateMonth)->whereYear('dateForSort', $registerDateYear)->get();
            array_push($arr, ['year' => $registerDateYear, 'month' => $registerDateMonth, 'bookings' => $financeBookings]);

            $ind += 1;

            return $this->fillBookings($supplier, $ind, $arr, $model);
        }
        return $arr;
    }

    public function downloadBillImage(Request $request, $id)
    {

        $bill = Billing::findOrFail($id);
        /*return Storage::disk('s3')->download('billing-files',  $bill->name);*/

        $extension = explode(".", $bill->name);
        $extension = end($extension);
        $extension = "." . $extension;

        $s3Client = Storage::cloud()->getAdapter()->getClient();

        $stream = $s3Client->getObject([
            'Bucket' => 'cityzore',
            'Key' => "billing-files/" . $bill->name
        ]);

        return response($stream['Body'], 200)->withHeaders([
            'Content-Type' => $stream['ContentType'],
            'Content-Length' => $stream['ContentLength'],
            'Content-Disposition' => 'attachment; filename=" ' . $bill->billingable->name . ' ' . $bill->billingable->surname . ' ' . $bill->created_at->format('d/m/Y H:i') . '"' . $extension
        ]);

    }


    public function ajax(Request $request)
    {
        switch ($request->action) {
            case 'get_billing_images':
                $bills = Billing::whereDate('created_at', $request->date)->orderBy('created_at', 'asc')->get();

                $view = view("panel.finance.bill_images", compact('bills'))->render();

                return response()->json(['view' => $view]);

                break;


            default:
                # code...
                break;
        }
    }

}
