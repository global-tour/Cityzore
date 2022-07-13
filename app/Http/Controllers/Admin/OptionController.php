<?php

namespace App\Http\Controllers\Admin;

use App\Av;
use App\Avdate;
use App\Bigbus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\BigBusRelated;
use App\Http\Controllers\Helpers\ComissionCalculateHelper;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Option;
use App\Pricing;
use App\Product;
use App\Adminlog;
use App\Supplier;
use App\TicketType;
use App\TootbusConnection;
use App\TootbusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\Http\Controllers\Helpers\ApiRelated;
use Carbon\Carbon;
use App\OptionTranslation;
use App\OptionTType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\OptionFile;

class OptionController extends Controller
{

    public $commonFunctions;
    public $apiRelated;
    protected $bigbusRelated;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
        $this->apiRelated = new ApiRelated();
        $this->bigbusRelated = new BigBusRelated();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('panel.options.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $pricings = Pricing::where('supplierID', -1)->get();
        $availabilities = Av::where('supplierID', -1)->get();
        if (auth()->guard('supplier')->check()) {
            $supplierId = auth()->user()->id;
            $pricings = Pricing::where('supplierID', $supplierId)->get();
            $availabilities = Av::where('supplierID', $supplierId)->get();
        }
        $ticketTypes = TicketType::all();

        return view('panel.options.create',
            [
                'pricings' => $pricings,
                'availabilities' => $availabilities,
                'ticketTypes' => $ticketTypes
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Option';
        $adminLog->url = $request->url();
        $adminLog->action = 'Saved Option';

        $sameName = Option::where('title', $request->title)->get();
        if (count($sameName) > 0) {
            return response()->json(['errors' => 'There is an option using this name. Please change name of option', 'option' => []]);
        }
        $comission = 0;
        //Auth Supplier ID Check
        if (Auth::guard('supplier')->check()) {
            $supplier = Auth::guard('supplier')->user();
            $supplier_id = $supplier->id;
            $comission = $supplier->comission;
        }

        //If Admin => -1
        if (Auth::guard('admin')->check()) {
            $supplier_id = -1;
        }

        $meetingComment = $request->meetingComment;

        $refCodeGenerator = new RefCodeGenerator();
        $referenceCode = $refCodeGenerator->refCodeGeneratorForOption();

        if ($request->ajax()) {
            $opt = new Option();
            $opt->referenceCode = $referenceCode;
            $opt->minPerson = $request->minPerson;
            $opt->maxPerson = is_null($request->maxPerson) ? '' : $request->maxPerson;
            $title = str_replace("  ", " ", $request->title);
            $opt->guideInformation = $request->guideInformation;
            $opt->isSkipTheLine = $request->isSkipTheLine;
            $opt->isFreeCancellation = $request->isFreeCancellation;
            $opt->customer_mail_templates = $request->customerMailTemplates;
            $opt->title = $title;
            $opt->description = $request->description;
            $opt->meetingComment = $meetingComment;
            $opt->meetingPoint = $request->meetingPoint;
            $opt->meetingPointLat = $request->meetingPointLat;
            $opt->meetingPointLong = $request->meetingPointLong;
            $opt->meetingPointDesc = $request->meetingPointDesc;
            $opt->addresses = $request->addresses;
            $opt->cutOfTime = $request->cutOfTime;
            $opt->cutOfTimeDate = $request->cutOfTimeDate;
            $opt->tourDuration = $request->tourDuration;
            $opt->tourDurationDate = $request->tourDurationDate;
            $opt->guideTime = $request->guideTime;
            $opt->guideTimeType = $request->guideTimeType;

            $opt->cancelPolicyTime = $request->cancelPolicyTime;
            $opt->cancelPolicyTimeType = $request->cancelPolicyTimeType;

            $opt->pricings = $request->pricings;
            $opt->comission = $comission;
            $opt->supplierID = $supplier_id;
            $opt->isMixed = $request->isMixed;
            $opt->isPublished = 1;
            if (!is_null($request->contactInformationFieldsArray)) {
                $contactInformationFieldsFinal = $this->createContactInformationFields($request->contactInformationFieldsArray);
                $opt->contactInformationFields = json_encode($contactInformationFieldsFinal);
            }
            $opt->contactForAllTravelers = (int)$request->contactForAllTravelers;
            $opt->mobileBarcode = $request->mobileBarcode;

            $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $opt->knowBeforeYouGo = $knowBeforeYouGo;

            $includes = json_decode($request->included, true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $opt->included = $includes;

            $notIncluded = json_decode($request->notIncluded, true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $opt->notIncluded = $notIncluded;

            if ($opt->save()) {
                $opt->pricings()->attach($request->pricings);
                $opt->avs()->attach($request->availabilities);

                /*
                $ticketTypes = $request->ticketTypes ?? [];
                foreach($ticketTypes as $ticketType) {
                    OptionTType::create(['option_id' => $opt->id, 'ticket_type_id' => $ticketType]);
                }
                */

                $ticketType = $request->ticketTypes;
                if ($ticketType) {
                    OptionTType::create(['option_id' => $opt->id, 'ticket_type_id' => $ticketType]);
                }

                $adminLog->details = auth()->user()->name . ' clicked to Save Option Button and created a new option with id ' . $opt->id;
                $adminLog->optionRefCode = $referenceCode;
                $adminLog->tableName = 'options';
                $adminLog->result = 'successful';

                $avs = $opt->avs;
                $blockoutHours = $request->get('blockoutHours') ?? [];
                foreach ($avs as $av) {
                    $avBlockoutHours = json_decode($av->blockoutHours, true);
                    if (count($avBlockoutHours) == 0) {
                        array_push($avBlockoutHours, [$opt->referenceCode => $blockoutHours]);
                    } else {
                        $keyExists = false;
                        foreach ($avBlockoutHours as $key => $avBlockoutHour) {
                            if (count($blockoutHours) == 0) {
                                if (array_key_exists($opt->referenceCode, $avBlockoutHour))
                                    unset($avBlockoutHours[$key]);
                            } else {
                                if (array_key_exists($opt->referenceCode, $avBlockoutHour)) {
                                    $avBlockoutHours[$key][$opt->referenceCode] = $blockoutHours;
                                    $keyExists = true;
                                }
                            }
                        }
                        if (!$keyExists && count($blockoutHours) > 0) {
                            array_push($avBlockoutHours, [$opt->referenceCode => $blockoutHours]);
                        }
                    }

                    $av->blockoutHours = json_encode($avBlockoutHours);
                    $av->save();
                }
            } else {
                $adminLog->result = 'failed';
            }
            $adminLog->save();

            if (Auth::guard('supplier')->check()) {
                $opt->supplier()->attach($supplier_id);
            }
            return response()->json($opt, 200);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionDetach(Request $request, $id)
    {
        $product = Product::findOrFail($request->productId);
        $product->options()->detach($request->optId);
        $productOpts = json_decode($product->options, true);
        if (!is_null($productOpts)) {
            $key = array_search($request->optId, $productOpts);
            if ($key != false) {
                unset($productOpts[$key]);
            }
        }

        if (count($product->options()->get()) <= 0) {
            $product->isDraft = 1;
            $product->isPublished = 0;
        }

        $product->options = json_encode($productOpts);
        $product->save();
        return response()->json(['success' => 'Option is removed successfully']);
    }

    public function optionSupp(Request $request)
    {
        $option = Option::findOrFail($request->optionId);

        $supplier = Supplier::whereIn('id', explode(',', $option->rCodeID))->get();

        return response()->json([
            'status' => true,
            'data' => $supplier
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionStore(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Option';
        $adminLog->url = $request->url();
        $adminLog->action = 'Saved Option';

        $sameName = Option::where('title', $request->title)->get();
        if (count($sameName) > 0) {
            return response()->json(['errors' => 'There is an option using this name. Please change name of option', 'option' => []]);
        }
        $comission = 0;
        //Auth Supplier ID Check
        if (Auth::guard('supplier')->check()) {
            $supplier = Auth::guard('supplier')->user();
            $supplier_id = $supplier->id;
            $comission = $supplier->comission;
        }

        //If Admin => -1
        if (Auth::guard('admin')->check()) {
            $supplier_id = -1;
        }

        $refCodeGenerator = new RefCodeGenerator();
        $referenceCode = $refCodeGenerator->refCodeGeneratorForOption();

        $contactInformationFieldsFinal = $this->createContactInformationFields($request->contactInformationFieldsArray);

        if ($request->ajax()) {
            $option = new Option();
            $option->referenceCode = $referenceCode;
            $title = str_replace("  ", " ", $request->title);
            $option->title = $title;
            $option->guideInformation = $request->guideInformation;
            $option->isSkipTheLine = $request->isSkipTheLine;
            $option->customer_mail_templates = $request->customerMailTemplates;
            $option->customer_whatsapp_templates = $request->customerWhatsAppTemplates;
            $option->isFreeCancellation = $request->isFreeCancellation;
            $option->description = $request->description;
            $option->minPerson = $request->minPerson;
            $option->maxPerson = is_null($request->maxPerson) ? '' : $request->maxPerson;
            $option->meetingPoint = $request->meetingPoint;
            $option->meetingComment = $request->meetingComment;
            $option->meetingPointLat = $request->meetingPointLat;
            $option->meetingPointLong = $request->meetingPointLong;
            $option->meetingPointDesc = $request->meetingPointDesc;
            $option->addresses = $request->addresses;
            $option->cutOfTime = $request->cutOfTime;
            $option->cutOfTimeDate = $request->cutOfTimeDate;
            $option->tourDuration = $request->tourDuration;
            $option->tourDurationDate = $request->tourDurationDate;
            $option->guideTime = $request->guideTime;
            $option->guideTimeType = $request->guideTimeType;

            $option->cancelPolicyTime = $request->cancelPolicyTime;
            $option->cancelPolicyTimeType = $request->cancelPolicyTimeType;


            $option->pricings = $request->pricings;
            $option->comission = $comission;
            $option->supplierID = $supplier_id;
            $option->isMixed = $request->isMixed;
            if (!is_null($request->contactInformationFieldsArray)) {
                $contactInformationFieldsFinal = $this->createContactInformationFields($request->contactInformationFieldsArray);
                $option->contactInformationFields = json_encode($contactInformationFieldsFinal);
            }
            $option->contactForAllTravelers = $request->contactForAllTravelers;
            $option->mobileBarcode = $request->mobileBarcode;

            $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $option->knowBeforeYouGo = $knowBeforeYouGo;

            $includes = json_decode($request->included, true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $option->included = $includes;

            $notIncluded = json_decode($request->notIncluded, true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $option->notIncluded = $notIncluded;

            if ($option->save()) {
                $option->pricings()->attach($request->pricings);
                $option->avs()->attach($request->availabilities);

                /*
                $ticketTypes = $request->ticketTypes ?? [];
                foreach($ticketTypes as $ticketType) {
                    OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
                }
                */

                $ticketType = $request->ticketTypes;
                if ($ticketType) {
                    OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
                }

                $adminLog->details = auth()->user()->name . ' clicked to Save Option Button and created a new option with id ' . $option->id;
                $adminLog->optionRefCode = $referenceCode;
                $adminLog->tableName = 'options';
                $adminLog->result = 'successful';

                $avs = $option->avs;
                $blockoutHours = $request->get('blockoutHours') ?? [];
                foreach ($avs as $av) {
                    $avBlockoutHours = json_decode($av->blockoutHours, true);
                    if (count($avBlockoutHours) == 0) {
                        array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                    } else {
                        $keyExists = false;
                        foreach ($avBlockoutHours as $key => $avBlockoutHour) {
                            if (count($blockoutHours) == 0) {
                                if (array_key_exists($option->referenceCode, $avBlockoutHour))
                                    unset($avBlockoutHours[$key]);
                            } else {
                                if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                                    $avBlockoutHours[$key][$option->referenceCode] = $blockoutHours;
                                    $keyExists = true;
                                }
                            }
                        }
                        if (!$keyExists && count($blockoutHours) > 0) {
                            array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                        }
                    }

                    $av->blockoutHours = json_encode($avBlockoutHours);
                    $av->save();
                }
            } else {
                $adminLog->result = 'failed';
            }
            $adminLog->save();

            if (Auth::guard('supplier')->check()) {
                $option->supplier()->attach($supplier_id);
            }
            return response()->json($option, 200);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (request()->page) {
            $pageID = request()->page;
        } else {
            $pageID = 1;
        }
        $option = Option::with('bigBus')->findOrFail($id);

        if (auth()->guard('supplier')->check()) {

            if ($option->supplierID != Auth::guard('supplier')->user()->id)
                return redirect()->back()->with(['error' => 'You cannot view this user information']);

            $supplier = Supplier::where('id', auth()->guard('supplier')->user()->id)->first();
            $pricings = $supplier->pricing;
            $availabilities = $supplier->av;
        }

        if (auth()->guard('admin')->check()) {
            $supplierID = $option->supplierID;
            $pricings = Pricing::where('supplierID', $supplierID)->get();
            $availabilities = Av::where('supplierID', $supplierID)->get();
        }

        $option_availabilities = $option->avs()->get();
        if (is_null($option)) {
            abort(404);
        }

        $ticketTypes = TicketType::all();
        $optionTicketTypes = $option->ticket_types()->pluck('ticket_type_id')->toArray();

        $blockoutHours = [];
        $avBlockoutHours = count($option_availabilities) ? json_decode($option_availabilities[0]->blockoutHours, true) : false;

        if ($avBlockoutHours) {
            foreach ($avBlockoutHours as $avBlockoutHour) {
                if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                    $blockoutHours = $avBlockoutHour[$option->referenceCode];
                }
            }
        }

        return view('panel.options.edit',
            [
                'option' => $option,
                'pricings' => $pricings,
                'availabilities' => $availabilities,
                'option_availabilities' => $option_availabilities,
                'pageID' => $pageID,
                'ticketTypes' => $ticketTypes,
                'optionTicketTypes' => $optionTicketTypes,
                'blockoutHours' => $blockoutHours,
                'optionFiles' => OptionFile::where('option_id', $option->id)->get()
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionUpdate(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Option';
        $adminLog->url = $request->url();
        $adminLog->action = 'Updated Option';


        $comission = 0;
        //Auth Supplier ID Check
        if (Auth::guard('supplier')->check()) {
            $supplier = Auth::guard('supplier')->user();
            $supplier_id = $supplier->id;
            $comission = $supplier->comission;
        }

        //If Admin => -1
        if (Auth::guard('admin')->check()) {
            $supplier_id = -1;
        }

        if ($request->ajax()) {
            $option = Option::findOrFail($request->optionId);
            $sameName = Option::where('title', $request->title)->get();
            if (count($sameName) > 0 && count($sameName) != 1) {
                return response()->json(['errors' => 'There is an option using this name. Please change name of option', 'option' => []]);
            } else if (count($sameName) == 1) {
                $firstHasSameName = $sameName->first();
                if ($firstHasSameName->id != $option->id) {
                    return response()->json(['errors' => 'There is an option using this name. Please change name of option', 'option' => []]);
                }
            }

            if (!is_null($request->contactInformationFieldsArray)) {
                $contactInformationFieldsFinal = $this->createContactInformationFields($request->contactInformationFieldsArray);
                $option->contactInformationFields = json_encode($contactInformationFieldsFinal);
            } else {
                $option->contactInformationFields = null;
            }

            $title = str_replace("  ", " ", $request->title);
            $option->title = $title;
            $option->guideInformation = $request->guideInformation;
            $option->isSkipTheLine = $request->isSkipTheLine;
            $option->customer_mail_templates = $request->customerMailTemplates;
            $option->customer_whatsapp_templates = $request->customerWhatsAppTemplates;
            $option->isFreeCancellation = $request->isFreeCancellation;
            $option->description = $request->description;
            $option->minPerson = $request->minPerson;
            $option->maxPerson = is_null($request->maxPerson) ? '' : $request->maxPerson;
            $option->meetingPoint = $request->meetingPoint;
            $option->meetingComment = $request->meetingComment;
            $option->meetingPointLat = $request->meetingPointLat;
            $option->meetingPointLong = $request->meetingPointLong;
            $option->meetingPointDesc = $request->meetingPointDesc;
            $option->addresses = $request->addresses;
            $option->cutOfTime = $request->cutOfTime;
            $option->cutOfTimeDate = $request->cutOfTimeDate;
            $option->tourDuration = $request->tourDuration;
            $option->tourDurationDate = $request->tourDurationDate;
            $option->guideTime = $request->guideTime;
            $option->guideTimeType = $request->guideTimeType;
            $option->cancelPolicyTime = $request->cancelPolicyTime;
            $option->cancelPolicyTimeType = $request->cancelPolicyTimeType;
            $option->comission = $comission;
            $option->supplierID = $supplier_id;
            $option->isMixed = $request->isMixed;
            $option->contactForAllTravelers = (int)$request->contactForAllTravelers;
            if ($option->pricings != $request->pricings) {
                $option->pricings()->detach($option->pricings);
                $option->pricings()->attach($request->pricings);
            }
            $option->avs()->detach();
            $option->avs()->attach($request->availabilities);
            $option->pricings = $request->pricings;
            $option->mobileBarcode = $request->mobileBarcode;

            $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $option->knowBeforeYouGo = $knowBeforeYouGo;

            $includes = json_decode($request->included, true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $option->included = $includes;

            $notIncluded = json_decode($request->notIncluded, true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $option->notIncluded = $notIncluded;

            if ($option->save()) {
                /*
                $ticketTypes = $request->ticketTypes ?? [];
                $optionTicketTypes = $option->ticket_types()->pluck('ticket_type_id')->toArray();
                foreach($ticketTypes as $ticketType) {
                    if(!in_array($ticketType, $optionTicketTypes))
                        OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
                }
                foreach($optionTicketTypes as $optionTicketType) {
                    if(!in_array($optionTicketType, $ticketTypes))
                        OptionTType::where(['option_id' => $option->id, 'ticket_type_id' => $optionTicketType])->delete();
                }
                */
                $ticketType = $request->ticketTypes;
                if ($ticketType) {
                    $optionsInTable = $option->ticket_types()->pluck('option_id')->toArray();
                    if (in_array($option->id, $optionsInTable)) {
                        OptionTType::where('option_id', $option->id)->update(['ticket_type_id' => $ticketType]);
                    } else {
                        OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
                    }
                } else {
                    OptionTType::where('option_id', $option->id)->delete();
                }

                $adminLog->details = auth()->user()->name . ' clicked to Update Button and updated option with id ' . $option->id;
                $adminLog->optionRefCode = $option->referenceCode;
                $adminLog->tableName = 'options';
                $adminLog->result = 'successful';

                $avs = $option->avs;
                $blockoutHours = $request->get('blockoutHours') ?? [];
                foreach ($avs as $av) {
                    $avBlockoutHours = json_decode($av->blockoutHours, true);
                    if (count($avBlockoutHours) == 0) {
                        array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                    } else {
                        $keyExists = false;
                        foreach ($avBlockoutHours as $key => $avBlockoutHour) {
                            if (count($blockoutHours) == 0) {
                                if (array_key_exists($option->referenceCode, $avBlockoutHour))
                                    unset($avBlockoutHours[$key]);
                            } else {
                                if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                                    $avBlockoutHours[$key][$option->referenceCode] = $blockoutHours;
                                    $keyExists = true;
                                }
                            }
                        }
                        if (!$keyExists && count($blockoutHours) > 0) {
                            array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                        }
                    }

                    $av->blockoutHours = json_encode($avBlockoutHours);
                    $av->save();
                }
            } else {
                $adminLog->result = 'failed';
            }
            $adminLog->save();

            if (count($option->avs()->get()) > 0 && count($option->pricings()->get()) > 0) {
                $option->isPublished = 1;
                $option->save();
            } else {
                $option->isPublished = 0;
                $option->save();
            }

            if (Auth::guard('supplier')->check()) {
                if ($option->supplierID != Auth::guard('supplier')->user()->id)
                    return redirect()->back()->with(['error' => 'You cannot update this Option']);

                $option->supplier()->detach();
                $option->supplier()->attach($supplier_id);

            }
            return response()->json($option, 200);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Option';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com') . '/option/' . $id . '/delete';
        $adminLog->action = 'Deleted Option';
        $adminLog->details = auth()->user()->name . ' clicked to Delete Icon Button and deleted option with id ' . $id;
        $adminLog->tableName = 'options';
        $option = Option::findOrFail($id);
        $products = $option->products()->get();
        if ($option->delete()) {
            $option->avs()->detach();
            $option->products()->detach();
            $option->pricings()->detach();

            $adminLog->result = 'successful';
            $adminLog->save();
            foreach ($products as $p) {
                if (count($p->options()->get()) <= 0) {
                    $p->isPublished = 0;
                    $p->isDraft = 1;
                    $p->supplierPublished = 0;
                    $p->save();
                }
            }
        }

        return redirect('/option');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $previewOption = Option::findOrFail($request->optionId);
        $previewAvailabilities = $previewOption->avs()->get();
        $availabilitiesExtras = [];
        foreach ($previewAvailabilities as $pAv) {
            $avdate_min = $pAv->avdates()->min('valid_from');
            $avdate_max = $pAv->avdates()->max('valid_to');
            $isLimitless = $pAv->isLimitless == 1;
            array_push($availabilitiesExtras, ['id' => $pAv->id, 'min' => $avdate_min, 'max' => $avdate_max, 'isLimitless' => $isLimitless]);
        }
        $previewPricing = $previewOption->pricings()->first(); // Option has only one pricing
        return response()->json(
            [
                'success' => true,
                'previewOption' => $previewOption,
                'previewAvailabilities' => $previewAvailabilities,
                'previewPricing' => $previewPricing,
                'availabilitiesExtras' => $availabilitiesExtras
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveOptionCommission(Request $request)
    {
        $option = Option::findOrFail($request->optionId);
        $commission = $request->commission;
        $option->comission = $commission;
        $option->save();

        return ['optionId' => $option->id, 'comFromRequest' => $commission, 'comFromDB' => $option->comission];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOptionPublishedStatus(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Option';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com') . '/option/changeOptionPublishedStatus';
        $adminLog->tableName = 'options';

        $option = Option::findOrFail($request->id);
        if ($request->isPublished == 1) {
            $adminLog->action = 'Published Option';
            $adminLog->details = auth()->user()->name . ' clicked to Published/Not Published Toggle and Published Option with id ' . $request->id;
            $adminLog->optionRefCode = $option->referenceCode;
        } else {
            $adminLog->action = 'Not Published Option';
            $adminLog->details = auth()->user()->name . ' clicked to Published/Not Published Toggle and Not Published Option with id ' . $request->id;
            $adminLog->optionRefCode = $option->referenceCode;
        }
        $option->isPublished = $request->isPublished;
        if ($option->save()) {
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();
        $product = $option->products()->get();
        $optionIsPublished = [];
        foreach ($product as $p) {
            foreach ($p->options()->get() as $o) {
                array_push($optionIsPublished, $o->isPublished);
            }
            if (!in_array(1, $optionIsPublished)) {
                $p->isDraft = 1;
                $p->isPublished = 0;
                $p->save();
            }
        }

        return response()->json(['success' => 'Status change successfully.']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setApiConnection(Request $request)
    {
        $option = Option::findOrFail($request->optionID);
        $oldValue = $option->connectedToApi;
        $option->connectedToApi = $oldValue == 0 ? 1 : 0;
        if ($option->save()) {
            $str = $option->connectedToApi == 0 ? 'disconnected' : 'connected';
            return response()->json(['success' => 'Option is marked as ' . $str . ' successfully!', 'value' => $option->connectedToApi]);
        }
        return response()->json(['error' => 'A problem is occured!']);
    }

    /**
     * Handles an ajax request for getting option part
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOptionPart(Request $request)
    {
        $part = $request->part;

        if (in_array($part, [1, 2, 3, 6])) {
            if ($part == 1) {
                $option = Option::select('title', 'description', 'included', 'notIncluded', 'knowBeforeYouGo');
            }

            if ($part == 2) {
                $option = Option::select('minPerson', 'maxPerson', 'guideInformation', 'isSkipTheLine', 'isFreeCancellation', 'mobileBarcode');
            }

            if ($part == 3) {
                $option = Option::select('cutOfTime', 'cutOfTimeDate', 'tourDuration', 'tourDurationDate', 'guideTime', 'guideTimeType', 'cancelPolicyTime', 'cancelPolicyTimeType');
            }

            if ($part == 6) {
                $option = Option::select('meetingPoint', 'meetingPointLat', 'meetingPointLong', 'meetingPointDesc', 'meetingComment');
            }

            $option = $option->where('id', $request->optionID)->first();
            return response()->json(['success' => 'Successful', 'option' => $option]);
        }

        if ($part == 4) {
            $option = Option::findOrFail($request->optionID);
            $pricing = $option->pricings()->first();
            $commission = 0;
            $userType = 'admin';
            if (auth()->guard('supplier')->check()) {
                $supplier = Supplier::where('id', Auth::guard('supplier')->user()->id)->first();
                $commission = $supplier->comission;
                $userType = 'supplier';
            }

            $ticketTypes = TicketType::all();
            $optionTicketTypes = $option->ticket_types()->pluck('ticket_type_id')->toArray();

            $option_availabilities = $option->avs;
            $blockoutHours = [];
            $avBlockoutHours = json_decode($option_availabilities[0]->blockoutHours, true);
            foreach ($avBlockoutHours as $avBlockoutHour) {
                if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                    $blockoutHours = $avBlockoutHour[$option->referenceCode];
                }
            }

            return response()->json(['success' => 'Successful', 'pricing' => $pricing, 'commission' => $commission, 'userType' => $userType, 'ticketTypes' => $ticketTypes, 'optionTicketTypes' => $optionTicketTypes, 'blockoutHours' => $blockoutHours]);
        }

        if ($part == 5) {
            $option = Option::findOrFail($request->optionID);
            $availability = $option->avs()->first();
            $ticketTypes = TicketType::all();
            $isLimitless = $availability->isLimitless;
            $avdates = $availability->avdates();
            $minDate = $avdates->min('valid_from');
            $maxDate = $avdates->max('valid_to');
            $availabilityTicketType = $availability->ticketType()->first();

            return response()->json(
                [
                    'success' => 'Successful',
                    'availability' => $availability,
                    'ticketTypes' => $ticketTypes,
                    'isLimitless' => $isLimitless,
                    'minDate' => $minDate,
                    'maxDate' => $maxDate,
                    'availabilityTicketType' => $availabilityTicketType
                ]
            );
        }
        if ($part == 7) {
            $option = Option::findOrFail($request->optionID);

            return response()->json([
                'option' => $option,
            ]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveOptionPart(Request $request)
    {
        $values = $request->values;
        $optionID = $request->optionID;
        $optionStep = $request->optionStep;
        $option = Option::findOrFail($optionID);
        if ($optionStep == 1) {
            $title = str_replace("  ", " ", $values['title']);


            if (Option::where("title", "=", $title)->where("id", "<>", $option->id)->count()) {
                return response()->json(["error" => "Option name cannot be the same as an existing option name!"]);
            }


            $option->title = $title;
            $option->description = $values['description'];

            $knowBeforeYouGo = json_decode($values['knowBeforeYouGo'], true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $option->knowBeforeYouGo = $knowBeforeYouGo;

            $includes = json_decode($values['included'], true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $option->included = $includes;

            $notIncluded = json_decode($values['notIncluded'], true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $option->notIncluded = $notIncluded;

            $option->save();
        }
        if ($optionStep == 2) {
            $option->minPerson = $values['minPerson'];
            $option->maxPerson = $values['maxPerson'];
            $option->guideInformation = $values['guideInformation'];
            $option->isSkipTheLine = $values['isSkipTheLine'];
            $option->isFreeCancellation = $values['isFreeCancellation'];
            $option->mobileBarcode = $values['mobileBarcode'];
            $option->save();
        }
        if ($optionStep == 3) {
            $option->cutOfTime = $values['cutOfTime'];
            $option->cutOfTimeDate = $values['cutOfTimeDate'];
            $option->tourDuration = $values['tourDuration'];
            $option->tourDurationDate = $values['tourDurationDate'];
            $option->guideTime = $values['guideTime'];
            $option->guideTimeType = $values['guideTimeType'];
            $option->cancelPolicyTime = $values['cancelPolicyTime'];
            $option->cancelPolicyTimeType = $values['cancelPolicyTimeType'];
            $option->save();
        }
        if ($optionStep == 4) {
            /*
            $ticketTypes = $values['ticketTypes'] ?? [];
            $optionTicketTypes = $option->ticket_types()->pluck('ticket_type_id')->toArray();
            foreach($ticketTypes as $ticketType) {
                if(!in_array($ticketType, $optionTicketTypes))
                    OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
            }
            foreach($optionTicketTypes as $optionTicketType) {
                if(!in_array($optionTicketType, $ticketTypes))
                    OptionTType::where(['option_id' => $option->id, 'ticket_type_id' => $optionTicketType])->delete();
            }
            */

            $ticketType = $values['ticketTypes'];
            if ($ticketType) {
                $optionsInTable = $option->ticket_types()->pluck('option_id')->toArray();
                if (in_array($option->id, $optionsInTable)) {
                    OptionTType::where('option_id', $option->id)->update(['ticket_type_id' => $ticketType]);
                } else {
                    OptionTType::create(['option_id' => $option->id, 'ticket_type_id' => $ticketType]);
                }
            } else {
                OptionTType::where('option_id', $option->id)->delete();
            }

            $avs = $option->avs;
            $blockoutHours = $values['blockoutHours'] ?? [];
            foreach ($avs as $av) {
                $avBlockoutHours = json_decode($av->blockoutHours, true);
                if (count($avBlockoutHours) == 0) {
                    array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                } else {
                    $keyExists = false;
                    foreach ($avBlockoutHours as $key => $avBlockoutHour) {
                        if (count($blockoutHours) == 0) {
                            if (array_key_exists($option->referenceCode, $avBlockoutHour))
                                unset($avBlockoutHours[$key]);
                        } else {
                            if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                                $avBlockoutHours[$key][$option->referenceCode] = $blockoutHours;
                                $keyExists = true;
                            }
                        }
                    }
                    if (!$keyExists && count($blockoutHours) > 0) {
                        array_push($avBlockoutHours, [$option->referenceCode => $blockoutHours]);
                    }
                }

                $av->blockoutHours = json_encode($avBlockoutHours);
                $av->save();
            }
        }
        if ($optionStep == 6) {
            if (array_key_exists('meetingPoint', $values)) {
                $option->meetingPoint = $values['meetingPoint'];
                $option->meetingPointLat = $values['meetingPointLat'];
                $option->meetingPointLong = $values['meetingPointLong'];
                if ($values['meetingComment'] != '') {

                    $optionCommends = json_decode($values['meetingComment'], true);

                    $optionCommends = collect($optionCommends);
                    $optionCommends = $optionCommends->map(function ($row) {
                        return $row['value'];
                    })->toArray();
                    $optionCommends = implode('|', $optionCommends);
                    $option->meetingComment = $optionCommends;
                }
                $option->meetingPointDesc = null;
            } else {
                $option->meetingPoint = null;
                $option->meetingPointLat = null;
                $option->meetingPointLong = null;
                $option->meetingComment = null;
                $option->meetingPointDesc = $values['meetingPointDesc'];
            }
            $option->save();
        }

        if ($optionStep == 7) {
            if (array_key_exists('contactInformationFieldsArray', $request->values)) {
                $contactInformationFieldsFinal = $this->createContactInformationFields($request->values['contactInformationFieldsArray']);
                $option->contactInformationFields = json_encode($contactInformationFieldsFinal);
            } else {
                $option->contactInformationFields = NULL;
            }
            $option->contactForAllTravelers = (int)$request->values['contactForAllTravelers'];

            $option->addresses = $request->addresses;
            $option->customer_mail_templates = $request->editable_customer_mail_templates;

            $option->save();
        }
        return $values;
    }

    /**
     * @param $contactInformationFieldsArray
     * @return array
     */
    public function createContactInformationFields($contactInformationFieldsArray)
    {
        $contactInformationFieldsFinal = [];
        if ($contactInformationFieldsArray != null) {
            for ($i = 0; $i < count($contactInformationFieldsArray); $i++) {
                $name = $contactInformationFieldsArray[$i]['title'];
                $splitTitle = preg_split('\' +\'', $name);
                if (count($splitTitle) == 1) {
                    $nameFinal = $splitTitle[0];
                } elseif (count($splitTitle) > 1) {
                    for ($j = 1; $j < count($splitTitle); $j++) {
                        $titleUcFirst = implode((array)ucfirst($splitTitle[$j - 1])) . implode(array(ucfirst($splitTitle[$j])));
                        if (count($splitTitle) == 2) {
                            $nameFinal = lcfirst($titleUcFirst);
                        } elseif (count($splitTitle) > 2) {
                            $nameFinal = lcfirst($splitTitle[0] . $titleUcFirst);
                        }
                    }
                }
                array_push($contactInformationFieldsFinal, [
                    "title" => $contactInformationFieldsArray[$i]['title'],
                    "name" => $nameFinal,
                    "isRequired" => $contactInformationFieldsArray[$i]['isRequired'],
                ]);
            }
        }

        return $contactInformationFieldsFinal;
    }

    // tootbus actions
    public function ajax(Request $request)
    {
        switch ($request->action) {
            case 'set_tootbus_connection':

                //return response()->json($this->ifExistsTootbusProduct($request->tootbus_product_id, $request->tootbus_option_id));
                $tootBusResponse = $this->ifExistsTootbusProduct($request->tootbus_product_id, $request->tootbus_option_id);

                if ($tootBusResponse["status"] !== true) {

                    return response()->json(["status" => 0, "message" => $tootBusResponse["message"]]);
                }


                $option = Option::findOrFail($request->option_id);
                if ($option->tootbus()->count()) {
                    $tootLogAction = "update";

                    $tootbus = TootbusConnection::findOrFail($request->tootbus_id);
                    $tootbus->option_id = $option->id;
                    $tootbus->tootbus_product_id = trim($request->tootbus_product_id);
                    $tootbus->tootbus_option_id = trim($request->tootbus_option_id);
                    $tootbus->body = $tootBusResponse["message"];
                    $tootbus->status = 1;


                } else {
                    $tootLogAction = "create";
                    $tootbus = new TootbusConnection();
                    $tootbus->option_id = $option->id;
                    $tootbus->tootbus_product_id = $request->tootbus_product_id;
                    $tootbus->tootbus_option_id = $request->tootbus_option_id;
                    $tootbus->body = $tootBusResponse["message"];
                    $tootbus->status = 1;

                }

                if ($tootbus->save()) {

                    $tootbusLog = new TootbusLog();
                    $tootbusLog->user_id = auth()->user()->id;
                    $tootbusLog->option_id = $option->id;
                    $tootbusLog->action = $tootLogAction;
                    $tootbusLog->tootbus_product_id = $tootbus->tootbus_product_id;
                    $tootbusLog->tootbus_option_id = $tootbus->tootbus_option_id;
                    $tootbusLog->tootbus_body = $tootbus->body;


                    $tootbusResponseForSetAvailabilityData = $this->setTootbusResponseDataToAvailability($option);
                    if ($tootbusResponseForSetAvailabilityData["status"] === false) {
                        $tootbusLog->message = "Tootbus Product and option ID connected Successfully but get an error while sync availability data " . $tootbusResponseForSetAvailabilityData["message"];
                        $tootbusLog->save();
                        return response()->json(["status" => 0, "message" => $tootbusResponseForSetAvailabilityData["message"], "tootbus_id" => $tootbus->id, "tootbus_body" => $tootbus->body]);
                    }

                    $tootbusLog->message = "Tootbus Product and option ID  and Availability data connected Successfully";
                    $tootbusLog->save();


                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $nahid_json = $jsonq->json($tootbus->body);

                    $query_option_id = !empty($tootbus->tootbus_option_id) ? $tootbus->tootbus_option_id : "DEFAULT";
                    $target_option = $nahid_json->from("options")->where("id", "=", $query_option_id)->first();
                    $html = [];
                    foreach ($target_option["units"] as $unit) {
                        $html[] = $unit["id"];
                    }


                    return response()->json(["status" => 1, "message" => "Api Connection Applied Successfully", "tootbus_id" => $tootbus->id, "tootbus_body" => $tootbus->body, "units" => $html]);
                }
                return response()->json(["status" => 0, "message" => "An Error Occurred!"]);


                break;

            case 'delete_tootbus_connection':

                $tootbus = TootbusConnection::findOrFail($request->data_id);

                $tootbusLog = new TootbusLog();
                $tootbusLog->user_id = auth()->user()->id;
                $tootbusLog->option_id = $tootbus->option_id;
                $tootbusLog->action = "delete";
                $tootbusLog->tootbus_product_id = $tootbus->tootbus_product_id;
                $tootbusLog->tootbus_option_id = $tootbus->tootbus_option_id;
                $tootbusLog->tootbus_body = $tootbus->body;


                $result = TootbusConnection::destroy($request->data_id);
                if ($result) {

                    $tootbusLog->message = "Tootus Api Connection Deleted Successfully";
                    $tootbusLog->save();

                    return response()->json(["status" => 1, "message" => "Api Connection Removed Successfully"]);
                }

                $tootbusLog->message = "An error occured when trying to delete tootbus api connection";
                $tootbusLog->save();
                return response()->json(["status" => 0, "message" => "An Error Occurred!"]);

                break;

            default:
                // code...
                break;
        }
    }

    private function ifExistsTootbusProduct($productID, $optionID)
    {
        $tootbusRelated = new TootbusRelated();
        if (empty(trim($optionID))) {
            $optionID = "DEFAULT";
        }
        return $tootbusRelated->checkProduct($productID, $optionID);
    }


    private function setTootbusResponseDataToAvailability($option)
    {


        if ($option->tootbus()->count()) {
            $diff_day = 180;
            $startDate = Carbon::now()->format("Y-m-d");
            $tootbusRelated = new TootbusRelated();
            $tootbusAvailabilityResponse = $tootbusRelated->checkAvailability($option->tootbus->tootbus_product_id, $option->tootbus->tootbus_option_id, $startDate, $diff_day);
            if ($tootbusAvailabilityResponse["status"] === false) {
                return ["status" => false, "message" => $tootbusAvailabilityResponse["message"]];
            }
            $decoded_json_tootbas_body = json_decode($option->tootbus->body, true);
            $jsonq = $this->apiRelated->prepareJsonQ();


            if ($option->avs()->count()) {

                $_av = $option->avs()->first();


            } else {
                $_av = new Av();
            }

            $hourly = [];
            $daily = [];
            $disabledDays = [];

            $_av->availabilityType = ($decoded_json_tootbas_body["availabilityType"]) === "START_TIME" ? "Starting Time" : "Operating Hours";
            $_av->name = $decoded_json_tootbas_body["internalName"];
            $_av->supplierID = -1;
            $_av->avTicketType = ($decoded_json_tootbas_body["availabilityType"]) === "START_TIME" ? 1 : 2;
            $_av->ticketReferenceCode = null;
            $_av->dateRange = "[]";
            $_av->barcode = "[]";
            $_av->isLimitless = 0;
            $_av->disabledWeekDays = null;
            $_av->disabledMonths = null;
            $_av->disabledYears = null;


            if ($tootbusAvailabilityResponse && $tootbusAvailabilityResponse["status"] === true) {

                foreach (json_decode($tootbusAvailabilityResponse["message"], true) as $toot) {

                    if ($decoded_json_tootbas_body["availabilityType"] === "START_TIME") {  // if has starting time
                        $nahid_hourly = $jsonq->json($_av->hourly !== "[]" && !empty($_av->hourly) ? $_av->hourly : "[{}]");
                        $day = Carbon::parse($toot["id"])->format("d/m/Y");
                        $hour = Carbon::parse($toot["id"])->format("H:i");
                        $result = $nahid_hourly->where('day', '=', $day)->where('hour', '=', $hour)->get();


                        $hourly[] = [
                            "id" => $toot["id"],
                            "day" => $day,
                            "hour" => $hour,
                            "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                            "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                            "isActive" => $toot["available"] === true ? 1 : 0
                        ];


                    } else { // if has operating hours
                        $nahid_daily = $jsonq->json($_av->daily !== "[]" && !empty($_av->daily) ? $_av->daily : "[{}]");

                        if (count($toot["openingHours"]) == 0) {
                            $toot["openingHours"][] = ["from" => "00:00", "to" => "23:59"];
                        }

                        foreach ($toot["openingHours"] as $h) {

                            $day = Carbon::parse($toot["id"])->format("d/m/Y");
                            $result = $nahid_daily->where('day', '=', $day)->where('hourFrom', '=', $h["from"])->where("hourTo", $h["to"])->get();


                            $daily[] = [
                                "id" => $toot["id"],
                                "day" => $day,
                                "hourFrom" => $h["from"],
                                "hourTo" => $h["to"],
                                "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                                "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                                "isActive" => $toot["available"] === true ? 1 : 0
                            ];


                        }


                    }


                }

                $_av->hourly = json_encode($hourly);
                $_av->daily = json_encode($daily);
                $_av->disabledDays = json_encode($disabledDays);
                $_av->save();


                if ($_av->avdates()->count() == 0) {
                    $avdate = new Avdate();
                } else {
                    $avdate = $_av->avdates()->first();
                }

                $avdate->valid_from_to = Carbon::now()->format('d/m/Y') . " - " . Carbon::now()->addDays($diff_day)->format('d/m/Y');
                $avdate->valid_from = Carbon::now()->format('Y-m-d');
                $avdate->valid_to = Carbon::now()->addDays($diff_day)->format('Y-m-d');
                $avdate->save();


                if ($_av->avdates()->count() == 0) {
                    $_av->avdates()->sync($avdate->id);
                }
                if ($_av->supplier()->count() == 0) {
                    $_av->supplier()->sync($_av->supplierID);
                }
                if ($_av->ticketType()->count() == 0) {
                    $_av->ticketType()->sync(24);
                }


                return ["status" => true, "message" => "Availability Data Set SuccessFully!"];


            } else {
                return ["status" => false, "message" => "An Error Occurred!"];
            }


        }


    }

    public function getOptionIncFore(Request $request)
    {
        try {
            $option = Product::where('id', $request->get('productID'))->first();
            $optionTranslation = ProductTranslation::where('productID', $option->id)->where('languageID', $request->get('langID'))
                ->where(function ($query) {
                    $query->where('title', '!=', null)
                        ->where('description', '!=', null)
                        ->where('meetingComment', '!=', null)
                        ->where('included', '!=', null)
                        ->where('notIncluded', '!=', null)
                        ->where('knowBeforeYouGo', '!=', null);
                })->first();

            $data = array(
                'withoutTranslation' => array(
                    'included' => $option->included,
                    'notIncluded' => $option->notIncluded,
                    'knowBeforeYouGo' => $option->knowBeforeYouGo
                ),
                'withTranslation' => array(
                    'included' => $optionTranslation ? $optionTranslation->included : null,
                    'notIncluded' => $optionTranslation ? $optionTranslation->notIncluded : null,
                    'knowBeforeYouGo' => $optionTranslation ? $optionTranslation->knowBeforeYouGo : null,
                )
            );

            return ['incFore' => $data];
        } catch (Exception $e) {

        }
    }

    public function copyOption(Request $request, $id)
    {
        $option = Option::findOrFail($id);
        $newOption = new Option();

        $refCodeGenerator = new RefCodeGenerator();
        $referenceCode = $refCodeGenerator->refCodeGeneratorForOption();

        $ignoredColumns = ['id', 'referenceCode', 'title', 'created_at', 'updated_at'];
        $optionColumns = Schema::getColumnListing('options');
        foreach ($optionColumns as $optionColumn) {
            if (!in_array($optionColumn, $ignoredColumns))
                $newOption->$optionColumn = $option->$optionColumn;
        }

        $newOption->referenceCode = $referenceCode;
        $newOption->title = $option->title . ' (copy)';
        $newOption->save();

        return redirect('/option?page=1');
    }

    public function saveOptionFiles(Request $request)
    {
        $base_url_for_guide_image = "https://cityzore.s3.eu-central-1.amazonaws.com/option-files/";
        $optionFiles = [];

        if ($request->hasFile('file')) {
            $files = $request->file;
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $fileName = str_replace(' ', '-', $fileName);
                $fileName = preg_replace('/[^A-Za-z0-9_.\-]/', '', $fileName);
                $fileBaseName = $fileName;
                $fileName = str_random(32) . "_" . $fileName;

                $s3 = Storage::disk('s3');
                $filePath = '/option-files/' . $fileName;
                $stream = fopen($file->getRealPath(), 'r+');
                $s3->put($filePath, $stream);

                $option_file = new OptionFile();
                $option_file->option_id = $request->option_id;
                $option_file->file = $base_url_for_guide_image . $fileName;
                $option_file->fileName = $fileBaseName;
                $option_file->save();

                array_push($optionFiles, ['id' => $option_file->id, 'fileName' => $option_file->fileName]);
            }
        }

        return response()->json(["status" => "success", "message" => "Selected Files Uploaded Successfully", 'optionFiles' => $optionFiles]);
    }

    public function deleteOptionFile(Request $request)
    {
        $file = OptionFile::findOrFail($request->optionFileID);

        if ($file->delete()) {
            Storage::disk('s3')->delete('option-files/' . $file->fileName);
            return response()->json(['status' => true, 'success' => 'File Deleted Successfully']);
        }
        return response()->json(['status' => false, 'error' => 'An Error Occurred']);
    }

    public function getBigbusProducts(Request $request)
    {
        try {

            $products = collect($this->bigbusRelated->setClient()->getProducts());

        }catch (\Exception $exception){

            return response()->json([
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ]);

        }

        return response()->json([
            'status' => true,
            'data' => $products['data']
        ]);
    }

    public function apiConnect(Request $request)
    {
        if ($request->ajax()) {

            if (!$request->has('productId')){

                return response()->json([
                    'status' => false,
                    'errorMessage' => 'Select a product!'
                ], 400);

            }

            $products = $this->bigbusRelated->setClient()->getProducts();

            foreach ($products['data'] as $product) {

                if ($product['id'] == $request->productId) {

                    try {

                        $check = Bigbus::where('option_id', $request->option_id)->where('product_id', $product['id'])->first();

                        if ($check)
                            throw new \Exception('This option has already connect');

                        foreach ($product['options'] as $option) {

                            if ($option['id'] == 'DEFAULT') {

                                foreach ($option['units'] as $unit) {
                                    $unitItems[$unit['type']] = $unit;
                                }

                            }

                        }

                        $bigbus = Bigbus::create([
                            'option_id' => $request->option_id,
                            'product_id' => $product['id'],
                            'body' => json_encode($product),
                            'units' => json_encode($unitItems)
                        ]);
                        Option::find($request->option_id)->update(["bigBusID" => $bigbus->id]);

                        return response()->json([
                            'status' => true,
                            'message' => 'Connection successfully',
                            'data' => $bigbus
                        ]);

                    } catch (\Exception $exception) {

                        return response()->json([
                            'status' => false,
                            'errorMessage' => 'An error has occurred: ' . $exception->getMessage()
                        ], 400);

                    }

                }

            }


        }

        abort(404);
    }

    public function apiDisconnect(Request $request)
    {
        try {
            $bigbus = Bigbus::findOrFail($request->id);
            $bigbus->option()->update(["bigBusID" => NULL]);
            $bigbus->delete();
            

            return response()->json([
                'status' => true,
                'message' => 'Disconnection successfully'
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'status' => false,
                'errorMessage' => 'An error has occurred: '. $exception->getMessage()
            ], 400);

        }
    }

}
