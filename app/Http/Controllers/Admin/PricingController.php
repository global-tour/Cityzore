<?php

namespace App\Http\Controllers\Admin;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Pricing;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Adminlog;

class PricingController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('panel.pricings.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $comission = 0;
        $userType = 'admin';
        if (auth()->guard('supplier')->check()) {
            $supplier = Supplier::where('id', auth()->guard('supplier')->user()->id)->first();
            $comission = $supplier->comission;
            $userType = 'supplier';
        }

        return view('panel.pricings.create',
            [
                'comission' => $comission,
                'userType' => $userType
            ]
        );
    }

    /**
     * @param $pricingTitle
     * @return bool
     */
    public function pricingTitleValidation($pricingTitle)
    {
        $sameName = Pricing::where('title', $pricingTitle)->get();
        if (count($sameName) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'adultPrice.*' => ['required', 'numeric', 'min:0'],
            'youthPrice.*' => ['required', 'numeric', 'min:0'],
            'childPrice.*' => ['required', 'numeric', 'min:0'],
            'infantPrice.*' => ['required', 'numeric', 'min:0'],
        ]);

        $supplierID = -1;
        if (auth()->guard('supplier')->check()) {
            $supplierID = auth()->user()->id;
        }

        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Pricing';
        $adminLog->url = $request->url();
        $adminLog->action = 'Saved Pricing';

        $pricing = new Pricing();
        $pricing->type = 'Per Person';
        $pricing->title = $request->title;
        $pricing->minPerson = json_encode($request->minPerson);
        $pricing->maxPerson = json_encode($request->maxPerson);
        $pricing->adultMin = $request->adultMin;
        $pricing->adultMax = $request->adultMax;
        $pricing->adultPrice = json_encode($request->adultPrice);
        $pricing->adultPriceCom = json_encode($request->adultPriceCom);
        $pricing->youthMin = $request->youthMin;
        $pricing->youthMax = $request->youthMax;
        if (!is_null($request->youthPrice)) {
            $pricing->youthPrice = json_encode($request->youthPrice);
        }
        if (!is_null($request->youthPriceCom)) {
            $pricing->youthPriceCom = json_encode($request->youthPriceCom);
        }
        $pricing->childMin = $request->childMin;
        $pricing->childMax = $request->childMax;
        if (!is_null($request->childPrice)) {
            $pricing->childPrice = json_encode($request->childPrice);
        }
        if (!is_null($request->childPriceCom)) {
            $pricing->childPriceCom = json_encode($request->childPriceCom);
        }
        $pricing->infantMin = $request->infantMin;
        $pricing->infantMax = $request->infantMax;
        if (!is_null($request->infantPrice)) {
            $pricing->infantPrice = json_encode($request->infantPrice);
        }
        if (!is_null($request->infantPriceCom)) {
            $pricing->infantPriceCom = json_encode($request->infantPriceCom);
        }
        $pricing->euCitizenMin = $request->euCitizenMin;
        $pricing->euCitizenMax = $request->euCitizenMax;
        if (!is_null($request->euCitizenPrice)) {
            $pricing->euCitizenPrice = json_encode($request->euCitizenPrice);
        }
        if (!is_null($request->euCitizenPriceCom)) {
            $pricing->euCitizenPriceCom = json_encode($request->euCitizenPriceCom);
        }
        $pricing->supplierID = $supplierID;
        if (!is_null($request->ignoredCategories)) {
            $pricing->ignoredCategories = json_encode($request->ignoredCategories);
        }
        $pricing->save();
        if (auth()->guard('supplier')->check()) {
            $pricing->supplier()->attach($supplierID);
        }

        if ($pricing->save()) {
            $adminLog->details = auth()->user()->name . ' clicked to Save Price Button and created a new pricing with id ' . $pricing->id;
            $adminLog->tableName = 'pricings';
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return response()->json(['success' => 'Pricing is successfully saved!', 'pricing' => $pricing]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'adultPrice.*' => ['required', 'numeric', 'min:0'],
            'youthPrice.*' => ['required', 'numeric', 'min:0'],
            'childPrice.*' => ['required', 'numeric', 'min:0'],
            'infantPrice.*' => ['required', 'numeric', 'min:0'],
        ]);

        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Price';
        $adminLog->url = $request->url();
        $adminLog->action = 'Updated Price';

        $pricing = Pricing::findOrFail($request->pricingID);
 
        if(auth()->guard('supplier')->check()){
            if($pricing->supplierID != auth()->guard('supplier')->user()->id)
                return redirect()->back()->with(['error' => 'You cannot update this price']);
        }

        $pricing->title = $request->title;
        $pricing->minPerson = json_encode($request->minPerson);
        $pricing->maxPerson = json_encode($request->maxPerson);
        $pricing->adultMin = $request->adultMin;
        $pricing->adultMax = $request->adultMax;
        $pricing->adultPrice = json_encode($request->adultPrice);
        $pricing->adultPriceCom = json_encode($request->adultPriceCom);
        $pricing->youthMin = $request->youthMin;
        $pricing->youthMax = $request->youthMax;
        $pricing->youthPrice = !is_null($request->youthPrice) ? json_encode($request->youthPrice) : null;
        $pricing->youthPriceCom = !is_null($request->youtPriceCom) ? json_encode($request->youthPriceCom) : null;
        $pricing->childMin = $request->childMin;
        $pricing->childMax = $request->childMax;
        $pricing->childPrice = !is_null($request->childPrice) ? json_encode($request->childPrice) : null;
        $pricing->childPriceCom = !is_null($request->childPriceCom) ? json_encode($request->childPriceCom) : null;
        $pricing->infantMin = $request->infantMin;
        $pricing->infantMax = $request->infantMax;
        $pricing->infantPrice = !is_null($request->infantPrice) ? json_encode($request->infantPrice) : null;
        $pricing->infantPriceCom = !is_null($request->infantPriceCom) ? json_encode($request->infantPriceCom) : null;
        $pricing->euCitizenMin = $request->euCitizenMin;
        $pricing->euCitizenMax = $request->euCitizenMax;
        $pricing->euCitizenPrice = !is_null($request->euCitizenPrice) ? json_encode($request->euCitizenPrice) : null;
        $pricing->euCitizenPriceCom = !is_null($request->euCitizenPriceCom) ? json_encode($request->euCitizenPriceCom) : null;
        $pricing->ignoredCategories = !is_null($request->ignoredCategories) ? json_encode($request->ignoredCategories) : null;
        $pricing->save();

        if ($pricing->save()) {
            $adminLog->details = auth()->user()->name . ' clicked to Update Button and updated pricing with id ' . $pricing->id;
            $adminLog->tableName = 'pricings';
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return response()->json(['success' => 'Pricing is successfully updated!', 'pricing' => $pricing]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $pricing = Pricing::findOrFail($id);
        $comission = 0;
        $userType = 'admin';
        if (auth()->guard('supplier')->check()) {

             if($pricing->supplierID != Auth::guard('supplier')->user()->id)
                return redirect()->back()->with(['error' => 'You cannot view this user information']);

            $supplier = Supplier::where('id', auth()->guard('supplier')->user()->id)->first();
            $comission = $supplier->comission;
            $userType = 'supplier';
        }
        $ignoredCategories = json_decode($pricing->ignoredCategories, true);
        $minPerson = json_decode($pricing->minPerson, true);
        $maxPerson = json_decode($pricing->maxPerson, true);
        $adultPrice = json_decode($pricing->adultPrice, true);
        $adultPriceCom = json_decode($pricing->adultPriceCom, true);
        $youthPrice = json_decode($pricing->youthPrice, true);
        $youthPriceCom = json_decode($pricing->youthPriceCom, true);
        $childPrice = json_decode($pricing->childPrice, true);
        $childPriceCom = json_decode($pricing->childPriceCom, true);
        $infantPrice = json_decode($pricing->infantPrice, true);
        $infantPriceCom = json_decode($pricing->infantPriceCom, true);
        $euCitizenPrice = json_decode($pricing->euCitizenPrice, true);
        $euCitizenPriceCom = json_decode($pricing->euCitizenPriceCom, true);
        $tierIterator = count($adultPrice);
        $availableCategories = [];
        if (is_null($youthPrice)) {
            array_push($availableCategories, 'youth');
        }
        if (is_null($childPrice)) {
            array_push($availableCategories, 'child');
        }
        if (is_null($infantPrice)) {
            array_push($availableCategories, 'infant');
        }
        if (is_null($euCitizenPrice)) {
            array_push($availableCategories, 'euCitizen');
        }

        return view('panel.pricings.edit',
            [
                'pricing' => $pricing,
                'comission' => $comission,
                'userType' => $userType,
                'tierIterator' => $tierIterator,
                'ignoredCategories' => $ignoredCategories,
                'minPerson' => $minPerson,
                'maxPerson' => $maxPerson,
                'adultPrice' => $adultPrice,
                'adultPriceCom' => $adultPriceCom,
                'youthPrice' => $youthPrice,
                'youthPriceCom' => $youthPriceCom,
                'childPrice' => $childPrice,
                'childPriceCom' => $childPriceCom,
                'infantPrice' => $infantPrice,
                'infantPriceCom' => $infantPriceCom,
                'euCitizenPrice' => $euCitizenPrice,
                'euCitizenPriceCom' => $euCitizenPriceCom,
                'availableCategories' => $availableCategories
            ]
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Pricing';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com'). '/pricing/'.$id.'/delete';
        $adminLog->action = 'Deleted Pricing';
        $adminLog->details = auth()->user()->name. ' clicked to Delete Icon Button and deleted pricing with id ' . $id;
        $adminLog->tableName = 'pricings';
        $pricing = Pricing::findOrFail($id);
        $options = $pricing->options()->get();
        if ($pricing->delete()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            foreach ($options as $opt) {
                if (count($opt->pricings()->get()) <= 0) {
                    $opt->isPublished = 0;
                    $opt->save();
                }
            }
        }
        return redirect('/pricings');
    }

}
