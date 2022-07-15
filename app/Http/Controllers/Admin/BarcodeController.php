<?php

namespace App\Http\Controllers\Admin;

use App\Barcode;
use App\Imports\BarcodeGrevinImport;
use App\Imports\BarcodeOrsayImport;
use App\Imports\BarcodeOrsayOrOrangerieImport;
use App\Imports\BarcodePompidouImport;
use App\Jobs\BarcodeImportJob;
use App\Jobs\PdfImportJob;
use App\Ticket;
use App\TicketType;
use Barryvdh\DomPDF\Facade as PDF2;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Imports\BarcodeImport;
use App\Imports\BarcodeOperaImport;
use App\Imports\BarcodePicassoImport;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Config;
use Spatie\PdfToText\Pdf;
use Carbon\Carbon;
use App\Imports\BarcodeRodinImport;
use App\Imports\BarcodeMontparnasseImport;
use Symfony\Component\Console\Style\OutputStyle;

class BarcodeController extends Controller
{

    public $timeRelatedFunctions;
    public $apiRelated;

    public function __construct()
    {
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->apiRelated = new ApiRelated();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }

        $ticketTypes = TicketType::withCount(['barcodes' => function ($q) use ($ownerID){
            $q->where('ownerID', $ownerID);
        }, 'barcodes as is_used' => function ($q) use ($ownerID){
            $q->where('isUsed', 1)->where('ownerID', $ownerID);
        }])->get();

        return view('panel.barcodes.index', compact('ticketTypes'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $ticketType = TicketType::all();
        return view('panel.barcodes.create', ['ticketType' => $ticketType]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $barcodes = $request->barcodes;
        $reservationNumbers = [];
        $expirationDates = [];
        if (!is_null($request->reservationNumbers)) {
            $reservationNumbers = $request->reservationNumbers;
        }
        if (!is_null($request->expirationDates)) {
            $expirationDates = $request->expirationDates;
        }

        $recodes = !is_null($request->recodes) ? $request->recodes : NULL;
        $contacts = !is_null($request->contacts) ? $request->contacts : NULL;
        $oldBarcodes = [];
        for ($i = 0; $i < count($barcodes); $i++) {
            $barcodeOnDB = Barcode::where('code', '=', $request->barcodes[$i])->where('isExpired', 0)->first();
            if ($barcodeOnDB) {
                array_push($oldBarcodes, $barcodeOnDB->code);
            } else {
                $barcode = new Barcode();
                $barcode->ticketType = $request->ticketType;
                $ticketType = TicketType::where('id', $barcode->ticketType)->first();
                $barcode->code = $barcodes[$i];
                if (count($reservationNumbers) > 0) {
                    $barcode->reservationNumber = $reservationNumbers[$i];
                }
                if (count($expirationDates) > 0) {
                    $barcode->endTime = $expirationDates[$i];
                } else {
                    $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                }

                $barcode->isUsed = 0;
                $barcode->isExpired = 0;
                $barcode->isReserved = 0;
                $barcode->ownerID = $ownerID;
                $barcode->recode = !is_null($recodes) ? $recodes[$i] : NULL;
                $barcode->contact = !is_null($contacts) ? $contacts[$i] : NULL;
                $barcode->searchableTicketType = $ticketType->name;
                $barcode->save();
            }
        }

        // Adding new tickets if barcodes of selected ticket type is usable as ticket count
        $ticketType = TicketType::findOrFail($request->ticketType);
        $avsUsingThisTT = $ticketType->av()->where('supplierID', $ownerID)->get();
        if (count($avsUsingThisTT) > 0) {
            if ($ticketType->usableAsTicket == 1) {
                foreach ($avsUsingThisTT as $av) {
                    $barcodeFromDB = json_decode($av->barcode, true)[0]; // Allways one object will be in the daterange array
                    $barcodeFromDB['dayTo'] = date('d/m/Y', strtotime('+1 years'));
                    $barcodeFromDB['ticket'] += count($barcodes);
                    $av->barcode = json_encode([$barcodeFromDB]);
                    $av->save();
                }
            }
        }

        return ['oldBarcodes' => $oldBarcodes];
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Picqer\Barcode\Exceptions\BarcodeException
     */
    public function createTicket(Request $request, $id)
    {
        $barcode = Barcode::findOrFail($id);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeCode = $barcode->code;
        $reservationNumber = $barcode->reservationNumber;
        $endTime = $barcode->endTime;
        $barcode->isUsed = 1;
        $barcode->save();
        $data = [
            'barcode' => $barcode,
            'generator' => $generator,
            'barcodeCode' => $barcodeCode,
            'reservationNumber' => $reservationNumber,
            'endTime' => $endTime,
        ];
        $pdf = PDF2::loadView('pdfs.single-tickets', $data);
        return $pdf->download($barcode->code . '.pdf');
    }

    public function multipleTicket()
    {
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        } elseif (auth()->guard('admin')->check()) {
            $ownerID = -1;
        }

        $ticketTypes = TicketType::withCount(['barcodes' => function ($q) use ($ownerID){
            $q->where('ownerID', $ownerID);
        }, 'barcodes as is_used' => function ($q) use ($ownerID){
            $q->where('isUsed', 1)->where('ownerID', $ownerID);
        }])
            ->where('multipleTicket', 1)->get();

        return view('panel.barcodes.multiple-tickets', compact('ticketTypes'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createMultipleTicket(Request $request)
    {
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        } elseif (auth()->guard('admin')->check()) {
            $ownerID = -1;
        }
        $ticketType = TicketType::where('multipleTicket', 1)->get();
        $barcodeInfos = [];
        foreach ($ticketType as $ticketTyp) {
            $barcodes = Barcode::where('ownerID', $ownerID)->where('ticketType', $ticketTyp->id)->where('isExpired', 0)->get();
            $total = $barcodes->where('ownerID', $ownerID)->count();
            $used = $barcodes->where('ownerID', $ownerID)->where('isUsed', 1)->count();
            array_push($barcodeInfos, ['ticketTypeName' => $ticketTyp->name, 'used' => $used, 'total' => $total]);
        }
        $barcodeAll = Barcode::where('ownerID', $ownerID)->where('ticketType', '=', $request->ticketType)->get();
        for ($i = 0; $i < $request->barcodeCount; $i++) {
            $barcode = Barcode::where('ownerID', $ownerID)->where('ticketType', '=', $request->ticketType)->where('isUsed', '=', 0)->where('isExpired', 0)->where('isReserved', 0)->first();
            $barcode->isUsed = 1;
            $barcode->save();
        }
        return view('panel.barcodes.multiple-tickets', ['barcodeCount' => count($barcodeAll), 'ticketType' => $ticketType, 'barcodeInfos' => $barcodeInfos]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function changeIsUsedStatus(Request $request)
    {
        $id = $request->id;
        $barcode = Barcode::findOrFail($id);
        $isUsed = $request->isUsed;

        if ($barcode->isUsed == 1) {
            $logs = json_decode($barcode->log, true) ?? [];
            array_push($logs, [
                "oldBookingID" => '-',
                "cancelReason" => str_replace(' ', '&nbsp', 'Not Used Operation'),
                "cancelBy" => str_replace(' ', '&nbsp', 'description: ' . $barcode->description . ' | bookingID: ' . $barcode->bookingID . ' | bookingDate: ' . $barcode->booking_date . ' | usedDate: ' . Carbon::parse($barcode->updated_at)->format('d/m/Y H:i')),
                "cancelDate" => '-'
            ]);
            $barcode->log = json_encode($logs);
        }

        $barcode->isUsed = $isUsed;
        $barcode->description = null;
        $barcode->bookingID = null;
        $barcode->booking_date = null;
        $barcode->cartID = null;
        $barcode->save();
        return $barcode;
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', 'exists:barcodes']
        ]);

        //Barcode::destroy($request->id);

        return response()->json(['status' => 'ok']);
    }


    public function importExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }
        $imported = new BarcodeImport($userID);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importOperaExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }
        $imported = new BarcodeOperaImport($userID);

        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importPicassoExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }
        $imported = new BarcodePicassoImport($userID);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importGrevinExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }
        $imported = new BarcodeGrevinImport($userID);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }


    public function importPdfForTriomphe(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }

        //$text = (new Pdf("C:/xampp/poppler/bin/pdftotext")) for locale
        $text = (new Pdf())
            ->setPdf($request->file)
            ->text();

        $success_data = ["counter" => 0, "items" => []];
        $failed_data = ["counter" => 0, "items" => []];

        $triompheNumbers = [];
        $purchaseDates = [];
        $reservationNumbers = [];

        if (preg_match_all("/N°\s(.*)\s/", $text, $numbers)) {
            $triompheNumbers = $numbers[1];


        } else {
            $triompheNumbers = [];

        }


        if (preg_match_all("/Date of purchase\: (.*)\s/", $text, $purchaseDates)) {
            $purchaseDates = $purchaseDates[1];


        } else {

            $purchaseDates = [];
        }

        if (preg_match_all("/P0(.*) /", $text, $reservationNumbers)) {

            $reservationNumbers = $reservationNumbers[0];

        } else {
            $reservationNumbers = [];

        }


        //Barcode::where('ticketType', 6)->delete();

        if (count($triompheNumbers) == 0) {
            return redirect()->back()->with(["error" => "Cant find any Arc de Triomphe Barcode in this file"]);
        }

        for ($i = 0; $i < count($triompheNumbers); $i++) {
            if (Barcode::where('code', $triompheNumbers[$i])->count()) {
                $failed_data["counter"]++;
                $failed_data["items"][] = $triompheNumbers[$i];

            } else {

                $barcode = new Barcode();
                $barcode->endTime = 0.001977261492832427;
                $barcode->reservationNumber = trim($reservationNumbers[$i]);
                $barcode->code = $triompheNumbers[$i];
                $barcode->isUsed = 0;
                $barcode->isReserved = 0;
                $barcode->isExpired = 0;
                $barcode->ownerID = $userID;
                $barcode->cartID = null;
                $barcode->bookingID = null;
                $barcode->recode = null;
                $barcode->contact = null;
                $barcode->ticketType = 6;
                $barcode->description = null;
                $barcode->searchableTicketType = "Arc de Triopmhe";

                if ($barcode->save()) {

                    $success_data["counter"]++;
                    $success_data["items"][] = $triompheNumbers[$i];

                }

            }

        }


        return redirect()->back()->with(["success" => "Arc De Triomphe PDF File Imported Successfully", "success_data" => $success_data, "failed_data" => $failed_data]);
    }

    public function importPdfForSainte(Request $request)
    {

        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }

        //$text = (new Pdf("C:/xampp/poppler/bin/pdftotext")); for local
        $text = (new Pdf())
            ->setPdf($request->file)
            ->text();

        $success_data = ["counter" => 0, "items" => []];
        $failed_data = ["counter" => 0, "items" => []];

        $sainteNumbers = [];
        $purchaseDates = [];
        $reservationNumbers = [];

        if (preg_match_all("/N°\s(.*)\s/", $text, $numbers)) {
            $sainteNumbers = $numbers[1];
        } else {
            $sainteNumbers = [];
        }
        if (preg_match_all("/Date of purchase\: (.*)\s/", $text, $purchaseDates)) {
            $purchaseDates = $purchaseDates[1];

        } else {

            $purchaseDates = [];
        }

        if (preg_match_all("/P0(.*) /", $text, $reservationNumbers)) {

            $reservationNumbers = $reservationNumbers[0];

        } else {
            $reservationNumbers = [];

        }


        //Barcode::where('ticketType', 6)->delete();

        if (count($sainteNumbers) == 0) {
            return redirect()->back()->with(["error" => "Cant find any Arc de Triomphe Barcode in this file"]);
        }

        for ($i = 0; $i < count($sainteNumbers); $i++) {
            if (Barcode::where('code', $sainteNumbers[$i])->count()) {
                $failed_data["counter"]++;
                $failed_data["items"][] = $sainteNumbers[$i];

            } else {
                //dd($formattedDate);
                $barcode = new Barcode();
                $barcode->endTime = 0.001977261492832427;
                $barcode->reservationNumber = trim($reservationNumbers[$i]);
                $barcode->code = $sainteNumbers[$i];
                $barcode->isUsed = 0;
                $barcode->isReserved = 0;
                $barcode->isExpired = 0;
                $barcode->ownerID = $userID;
                $barcode->cartID = null;
                $barcode->bookingID = null;
                $barcode->recode = null;
                $barcode->contact = null;
                $barcode->ticketType = 20;
                $barcode->description = null;
                $barcode->searchableTicketType = "Sainte-Chapelle";

                if ($barcode->save()) {

                    $success_data["counter"]++;
                    $success_data["items"][] = $sainteNumbers[$i];

                }

            }

        }


        return redirect()->back()->with(["success" => "Sainte Shapelle PDF File Imported Successfully", "success_data" => $success_data, "failed_data" => $failed_data]);
    }

    public function importRodinExcel(Request $request)
    {

        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }

        $imported = new BarcodeRodinImport($userID);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importMontparnasseAdultExcel(Request $request)
    {

        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }

        $imported = new BarcodeMontparnasseImport($userID, 0);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importMontparnasseInfantExcel(Request $request)
    {

        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }

        $imported = new BarcodeMontparnasseImport($userID, 1);
        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importOrsayOrOrangerieExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }


        $imported = new BarcodeOrsayOrOrangerieImport($userID, $request->barcodeType);

        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    public function importPompidouExcel(Request $request)
    {
        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }


        $imported = new BarcodePompidouImport($userID);

        $data = Excel::import($imported, $request->file);

        return redirect()->back()->with(["success" => "Excel File Imported Successfully", "success_data" => $imported->success_data, "failed_data" => $imported->failed_data]);
    }

    /**
     * Barcode import from excell with Queue
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importExcelAjax(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'ticketTypeSelect' => 'required',
            'file' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'All inputs required!'
            ], 400);
        }

        if (auth()->guard("admin")->check()) {
            $userID = -1;
        } else {
            $userID = auth()->user()->id;
        }


        try {

            $ticket_type = TicketType::findOrFail($request->ticketTypeSelect);

            if($request->file('file')->getClientOriginalExtension() == 'pdf') {

                $text = (new Pdf())
                    ->setPdf($request->file('file'))
                    ->text();

                if (is_null($text)) {
                    return \response()->json([
                        'message' => 'This file is not readable'
                    ], 400);
                }

                $this->pdfImport($request->file('file'), $userID, $ticket_type);


            }else{

                (new BarcodeImport($userID, $ticket_type))->import($request->file);

            }


            return response()->json([
                'message' => 'Barcodes will added in a few minutes: '
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'message' => 'An error has occurred: '. $exception->getMessage()
            ], 400);

        }


    }

    /**
     *
     * For local new Pdf('/opt/homebrew/bin/pdftotext')
     * @param $file
     * @param $userID
     * @param $ticket_type
     * @return void
     * @throws \Exception
     */
    public function pdfImport($file, $userID, $ticket_type)
    {


        try {
            $text = (new Pdf())
                ->setPdf($file)
                ->text();

            $codes = [];

            $dates = [];

            $reservationNumbers = [];



            if (preg_match_all("/N°\s(.*)\s/", $text, $codes)) {
                $codes = $codes[1];
            }

            if (preg_match_all("/Date of purchase\: (.*)\s/", $text, $dates)) {
                $dates = $dates[1];
            }

            if (preg_match_all("/P0(.*) /", $text, $reservationNumbers)) {
                $reservationNumbers = $reservationNumbers[0];
            }

            if (count($codes)) {
                foreach ($codes as $code) {

                    if (Barcode::where('code', $code)->first())
                        continue;

                    $barcode = new Barcode();
                    $barcode->endTime = 0.001977261492832427;
                    $barcode->reservationNumber = trim($code);
                    $barcode->code = $code;
                    $barcode->isUsed = 0;
                    $barcode->isReserved = 0;
                    $barcode->isExpired = 0;
                    $barcode->ownerID = $userID;
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->recode = null;
                    $barcode->contact = null;
                    $barcode->ticketType = $ticket_type->id;
                    $barcode->description = null;
                    $barcode->searchableTicketType = $ticket_type->name;
                    $barcode->save();
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), 400);
        }
    }

}
