<?php

namespace App\Http\Controllers\Admin;

use App\Barcode;
use App\TicketType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\ApiRelated;
use Illuminate\Support\Facades\Validator;

class TicketTypeController extends Controller
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
        $ticketTypes = TicketType::all();
        return view('panel.ticket-types.index', ['ticketTypes' => $ticketTypes]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('panel.ticket-types.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'format' => 'required_if:type,==,BARCODE'
        ], [
            'required' => 'This field is required.',
            'required_if' => 'This field is required.'
        ]);

        $request->merge(['bladeName' => $request->name]);

        if ($request->type == 'QRCODE') {
            $request->merge(['format' => 'png']);
        }


        if (TicketType::create($request->all())) {
            return redirect('/ticket-type');
        }

        return redirect()->back()->with('error', 'An error has occurred');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $ticketType = TicketType::findOrFail($id);
        return view('panel.ticket-types.edit', ['ticketType' => $ticketType]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'format' => 'required_if:type,==,BARCODE'
        ], [
            'required' => 'This field is required.',
            'required_if' => 'This field is required.'
        ]);

        $request->merge(['bladeName' => $request->name]);

        if ($request->type == 'QRCODE') {
            $request->merge(['format' => 'png']);
        }

        if (intval($request->warnTicket)<1) return back()->with('warnTicket','Minimum Ticket Number Error / You can not enter less than 1');

        if (TicketType::find($id)->update($request->all())) {
            return redirect('/ticket-type');
        }

        return redirect()->back()->with('status', 'An error has occurred');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUsableAsTicket(Request $request)
    {
        $gygMessages = [];
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $ticketType = TicketType::findOrFail($request->ticketTypeID);
        $oldValue = $ticketType->usableAsTicket;
        $ticketType->usableAsTicket = $oldValue == 0 ? 1 : 0;
        $avsUsingThisTT = $ticketType->av()->where('supplierID', $ownerID)->get();
        if (count($avsUsingThisTT) > 0) {
            if ($ticketType->usableAsTicket == 1) {
                $usableBarcodeCount = Barcode::where('ticketType', $ticketType->id)->where('isUsed', 0)
                    ->where('isReserved', 0)->where('isExpired', 0)->where('ownerID', $ownerID)->count();
                foreach ($avsUsingThisTT as $av) {
                    $dayFrom = $av->avdates()->min('valid_from');
                    $dayTo = $av->avdates()->max('valid_to');
                    $dayToYear = explode('-', $dayTo)[0];
                    $dayToYearNew = $dayToYear + 1;
                    $dayTo = $dayToYearNew.'-'.explode('-', $dayTo)[1].'-'.explode('-', $dayTo)[2];
                    $dates = $this->timeRelatedFunctions->convertDateFormat([$dayFrom, $dayTo]);
                    $av->avTicketType = 4;
                    $av->dateRange = json_encode([]);
                    $barcodeColumnArr = [
                        [
                            'dayFrom' => $dates[0],
                            'dayTo' => $dates[1],
                            'ticket' => $usableBarcodeCount,
                            'sold' => 0,
                        ]
                    ];
                    $av->barcode = json_encode($barcodeColumnArr);
                    $av->isLimitless = 0;
                    $av->save();
                }
            } else {
                foreach ($avsUsingThisTT as $av) {
                    $av->barcode = json_encode([]);
                    $av->avTicketType = 1;
                    $av->save();
                }
            }
        }

        if ($ticketType->save()) {
            $str = $ticketType->usableAsTicket == 0 ? 'not usable' : 'usable';

            return response()->json(['success' => 'Ticket Type is marked as '. $str .' successfully!', 'value' => $ticketType->usableAsTicket, 'gygMessages' => $gygMessages]);
        }
        return response()->json(['error' => 'A problem is occured!']);
    }

}
