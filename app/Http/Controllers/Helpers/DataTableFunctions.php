<?php

namespace App\Http\Controllers\Helpers;

use App\Admin;
use App\Barcode;
use App\Config;
use App\Invoice;
use App\Meeting;
use App\Option;
use App\Platform;
use App\Pricing;
use App\Supplier;
use App\Attraction;
use App\Product;
use App\Booking;
use App\Rcode;
use App\Adminlog;
use App\Apilog;
use App\BookingLog;
use App\Meetinglog;
use App\CustomerLoginLog;
use App\Comment;
use App\Language;
use App\AttractionTranslation;
use App\Av;
use App\TicketType;
use App\User;
use App\ErrorLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Helpers\CryptRelated;
use App\Http\Controllers\Helpers\AccessRelated;
use App\ExternalPayment;
use App\Cart;
use App\Mails;


class DataTableFunctions extends Controller
{

    public $cryptRelated;

    public function __construct()
    {
        $this->cryptRelated = new CryptRelated();
        $this->accessRelated = new AccessRelated();
    }

    /**
     * Main function for datatables
     *
     * @param Request $request
     * @return false|string
     */
    public function getRowsForDataTable(Request $request)
    {
        $supplier = null;
//        dd($request->all());
        $companyName = 'Paris Business and Travel';
        if (auth()->guard('supplier')->check() || $request->has('supplier') && $request->supplier != '-1') {
            $supId = auth()->guard('supplier')->check() ? auth()->guard('supplier')->user()->id : $request->supplier;
            $supplier = Supplier::findOrFail($supId);
            $companyName = $supplier->companyName;
        }

        $modelsArr = [
            'customerlog' => new CustomerLoginLog(),
            'meetinglog' => new Meetinglog(), 'attraction' => new Attraction(), 'product' => new Product(), 'booking' => new Booking(),
            'adminlog' => new Adminlog(), 'apilog' => new Apilog(), 'supplier' => new Supplier(),
            'option' => new Option(), 'availability' => new Av(), 'pricing' => new Pricing(),
            'bookingLog' => new BookingLog(), 'comment' => new Comment(), 'barcode' => new Barcode(),
            'errorLog' => new ErrorLog(), 'productPCT' => new Product(), 'productPCTcom' => new Product(),
            'productCTP' => new Product(), 'on-goings' => new Cart(),
            'attractionpct' => new Attraction(), 'mail' => new Mails(),
            'attractionpctcom' => new Attraction(),
            'attractionctp' => new Attraction()
        ];
        $model = $modelsArr[$request->model];

        // Read value
        $draw = $request->draw;
        $row = $request->start;
        $searchValue = $request->search['value']; // Search value

        $availabilitySelect = $request->availabilitySelect;

        // Total number of records without filtering
        $totalRecords = $model::count();
        $rowperpage = $request->length == '-1' ? $totalRecords : $request->length; // Rows display per page

        // Total number of record with filtering

        switch ($request->model) {
            case 'attraction':
                $returnData = $this->getDataForAttraction($model, $searchValue, $row, $rowperpage, $request);
                break;

            case 'attractionpct':

                $returnData = $this->getDataForAttractionPCT($model, $searchValue, $row, $rowperpage, $request);
                break;

            case 'attractionpctcom':
                $returnData = $this->getDataForAttractionPCTcom($model, $searchValue, $row, $rowperpage, $request);
                break;

            case 'attractionctp':
                $returnData = $this->getDataForAttractionCTP($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'product':
                $returnData = $this->getDataForProduct($model, $searchValue, $row, $rowperpage, $request, $companyName);
                break;
            case 'productPCT':
                $returnData = $this->getDataForProductPCT($model, $searchValue, $row, $rowperpage, $request, $companyName);
                break;
            case 'productPCTcom':
                $returnData = $this->getDataForProductPCTcom($model, $searchValue, $row, $rowperpage, $request, $companyName);
                break;
            case 'productCTP':
                $returnData = $this->getDataForProductCTP($model, $searchValue, $row, $rowperpage, $request, $companyName);
                break;
            case 'booking':
                if ($this->accessRelated->hasAccess('Supplier Access Checkins')) {
                    $returnData = $this->getDataForBookingWithAccess($model, $searchValue, $row, $rowperpage, $request);
                } else {
                    $returnData = $this->getDataForBooking($model, $searchValue, $row, $rowperpage, $request);
                }

                break;
            case 'adminlog':
                $returnData = $this->getDataForAdminlog($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'apilog':
                $returnData = $this->getDataForApilog($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'supplier':
                $returnData = $this->getDataForSupplier($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'option':
                $returnData = $this->getDataForOption($model, $searchValue, $row, $rowperpage, $request, $availabilitySelect);
                break;
            case 'availability':
                $returnData = $this->getDataForAvailability($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'pricing':
                $returnData = $this->getDataForPricing($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'bookingLog':
                $returnData = $this->getDataForBookingLog($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'comment':
                $returnData = $this->getDataForComment($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'barcode':
                $returnData = $this->getDataForBarcode($model, $searchValue, $row, $rowperpage);
                break;
            case 'errorLog':
                $returnData = $this->getDataForErrorLog($model, $searchValue, $row, $rowperpage);
                break;
            case 'meetinglog':
                $returnData = $this->getDataForMeetingLog($model, $searchValue, $row, $rowperpage);
                break;
            case 'customerlog':
                $returnData = $this->getDataForCustomerLog($model, $searchValue, $row, $rowperpage);
                break;
            case 'on-goings':
                $returnData = $this->getDataForOnGoings($model, $searchValue, $row, $rowperpage, $request);
                break;
            case 'mail':
                $returnData = $this->getDataForMails($model, $searchValue, $row, $rowperpage, $request);
                break;
        }

        $data = $returnData['data'];
        $totalRecordwithFilter = $returnData['totalRecordwithFilter'];
        $pageID = array_key_exists('pageID', $returnData) ? $returnData['pageID'] : 1;

        // Response
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecordwithFilter == $totalRecords ? $totalRecords : $totalRecordwithFilter,
            "aaData" => $data,
            "pageID" => $pageID,
        ];

        return json_encode($response);
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForPricing($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('?', explode('=', $url)[1])[0] : 1;
        $optionID = count(explode('=', $url)) > 2 ? explode('=', $url)[2] : null;

        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        if (auth()->guard('subUser')->check()) {
            $ownerID = Supplier::where('id', auth()->guard('subUser')->user()->supervisor)->first()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('supplierID', $ownerID)->where(function ($query) use ($searchValue) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('type', 'like', '%' . $searchValue . '%');
        });

        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records

        if (!is_null($optionID)) {
            $records = Option::findOrFail($optionID)->pricings()->get();
        } else {
            // Fetch records
            $records = $model::where('supplierID', $ownerID)->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('type', 'like', '%' . $searchValue . '%');
            });

            $records = $records->skip($row)->take($rowperpage)->get();
        }

        foreach ($records as $rowIndex => $row) {
            $titleColumn = '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '"><span class="list-enq-name">' . $row->title . '</span></a>';

            if ($row->supplierID == -1) {
                $companyName = 'Paris Business and Travel';
            } else {
                $companyName = Supplier::findOrFail($row->supplierID)->first()->companyName;
            }

            $actionsColumn = '<div style="height: 30px;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $actionsColumn .= '<form method="POST" action="' . url('/' . $request->model . '/' . $row->id . '/delete') . '">';
            $actionsColumn .= '<input type="hidden" name="_token" value="' . csrf_token() . '" >';
            $actionsColumn .= '<div style="height: 30px;margin-left: -6px;"><button type="submit" onclick="return confirm(\'Are you sure?\')"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></button></div>';
            $actionsColumn .= '</form>';

            $data[] = [
                "type" => $row->type,
                "name" => $titleColumn,
                "actions" => $actionsColumn,
                "companyName" => $companyName,
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'optionID' => $optionID];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForAvailability($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];
        $expiredAvailabilities = $request->expiredAvailabilities;

        $userID = auth()->guard('supplier')->check() ? auth()->user()->id : -1;

        if (auth()->guard('subUser')->check()) {
            $userID = Supplier::where('id', auth()->guard('subUser')->user()->supervisor)->first()->id;
        }

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;


        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('supplierID', $userID)->where(function ($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('availabilityType', 'like', '%' . $searchValue . '%');
        });

        if ($expiredAvailabilities == '1') {
            $totalRecordwithFilter = $totalRecordwithFilter->has('avdates')->get()->filter(function ($q) {
                $avdate = $q->avdates()->orderBy("valid_to", "desc")->first();
                if (Carbon::now()->timestamp > Carbon::parse($avdate->valid_to)->timestamp)
                    return true;
            });
        }

        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records

        if (auth()->guard('admin')->check()) {
            $records = $model::where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('availabilityType', 'like', '%' . $searchValue . '%');
            });
        } else {
            $records = $model::where('supplierID', $userID)->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('availabilityType', 'like', '%' . $searchValue . '%');
            });
        }

        if ($expiredAvailabilities == '1') {
            $records = $records->has('avdates')->get()->filter(function ($q) {
                $avdate = $q->avdates()->orderBy("valid_to", "desc")->first();
                if (Carbon::now()->timestamp > Carbon::parse($avdate->valid_to)->timestamp)
                    return true;
            });
            $records = $records->slice($row, $rowperpage);
        } else
            $records = $records->skip($row)->take($rowperpage)->get();

        foreach ($records as $rowIndex => $row) {

            $avdate = $row->avdates()->orderBy("valid_to", "desc")->first();

            if (!empty($avdate)) {
                if (Carbon::now()->timestamp > Carbon::parse($avdate->valid_to)->timestamp) {
                    $valid_message = "<label style='font-size:14px;' class='label label-danger'>expired on --> " . $avdate->valid_to . "</label>";
                } else {
                    $valid_message = "<label style='font-size:14px;' class='label label-success'>valid until--> " . $avdate->valid_to . "</label>";
                }
            } else {
                $valid_message = "<label style='font-size:14px;' class='label label-success'>valid until--> No Date Range </label>";
            }


            $titleColumn = '<a href="' . url('/av/' . $row->id . '/edit') . '"><span class="list-enq-name">' . $row->name . '</span></a>';

            if ($row->supplierID == -1) {
                $companyName = 'PBT';
            } else {
                $companyName = Supplier::findOrFail($row->supplierID)->companyName;
            }

            $actionsColumn = '<div style="height: 30px;"><a href="' . url('/av/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $actionsColumn .= '<form method="POST" action="' . url('/' . $request->model . '/' . $row->id . '/delete') . '">';
            $actionsColumn .= '<input type="hidden" name="_token" value="' . csrf_token() . '" >';
            $actionsColumn .= '<div style="height: 30px;margin-left: -6px;"><button type="submit" onclick="return confirm(\'Are you sure?\')"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></button></div>';
            $actionsColumn .= '</form>';

            $connectedOptionsColumn = '';
            $connectedOptions = Av::findOrFail($row->id)->options()->get();
            foreach ($connectedOptions as $connectedOption) {
                $connectedToApiClass = $connectedOption->connectedToApi == 1 ? 'label-warning' : 'label-info';
                $connectedOptionsColumn .= "<a class='popup' href='" . url('option/' . $connectedOption->id . '/edit') . "'>";
                $connectedOptionsColumn .= "<label for='myPopup" . $connectedOption->id . "' style='cursor:pointer;margin-right:2px;font-size: 11px;width:100%' class='col-md-2 label " . $connectedToApiClass . "'>$connectedOption->referenceCode<span class='popuptext' id='myPopup" . $connectedOption->id . "'>$connectedOption->title</span></label></a>";
            }
            $data[] = [
                "type" => $row->type,
                "name" => $titleColumn,
                "actions" => $actionsColumn,
                "companyName" => $companyName,
                "connectedOptions" => $connectedOptionsColumn,
                "valid" => $valid_message
            ];

        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForOption($model, $searchValue, $row, $rowperpage, $request, $availabilitySelect)
    {
        $data = [];

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('?', explode('=', $url)[1])[0] : 1;
        $productID = count(explode('=', $url)) > 2 ? explode('=', $url)[2] : null;

        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        if (auth()->guard('subUser')->check()) {
            $ownerID = Supplier::where('id', auth()->guard('subUser')->user()->supervisor)->first()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('supplierID', $ownerID)->where(function ($query) use ($searchValue) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('referenceCode', 'like', '%' . $searchValue . '%');
        });

        $totalRecordwithFilter = $totalRecordwithFilter->count();


        if (!is_null($productID)) {
            $records = Product::findOrFail($productID)->options()->get();
        } else {
            // Fetch records
            $records = $model::where('supplierID', $ownerID)->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%');
            })->get();

            if ($availabilitySelect == "VALID") {
                $records = $records->filter(function ($model) {
                    if ($model->avs()->count()) {
                        $lastAvDate = $model->avs()->first()->avdates()->orderBy("id", "desc")->first();
                        if (Carbon::now()->timestamp <= Carbon::parse($lastAvDate->valid_to)->timestamp) {
                            return true;
                        }
                        return false;
                    } else {
                        return true;
                    }
                });
            }

            if ($availabilitySelect == "EXPIRED") {
                $records = $records->filter(function ($model) {
                    if ($model->avs()->count()) {
                        $lastAvDate = $model->avs()->first()->avdates()->orderBy("id", "desc")->first();
                        if (Carbon::now()->timestamp > Carbon::parse($lastAvDate->valid_to)->timestamp) {
                            return true;
                        }
                        return false;
                    } else {
                        return true;
                    }
                });
            }

            $totalRecordwithFilter = $records->count();
            $records = $records->slice($row, $rowperpage);
        }

        foreach ($records as $rowIndex => $row) {
            $option = $model::findOrFail($row->id);
            $availabilities = $option->avs;
            $availabilityColumn = '';
            $supplierColumn = '';
            foreach ($availabilities as $availability) {
                $avdate = $availability->avdates()->orderBy("valid_to", "desc")->first();
                if (Carbon::now()->timestamp > Carbon::parse($avdate->valid_to)->timestamp) {
                    $availabilityColumn .= '<p><a style="border-bottom: 1px solid #ddd;padding: 1px" href="' . url('av/' . $availability->id . '/edit') . '">' . $availability->name . ' <span style="color:#E74C3C;">--> Expired on(' . $avdate->valid_to . ')</span></a></p>';
                } else {
                    $availabilityColumn .= '<p class="text-success"><a style="border-bottom: 1px solid #ddd;padding: 1px" href="' . url('av/' . $availability->id . '/edit') . '">' . $availability->name . ' <span style="color:#1E8449;">--> Valid Until(' . $avdate->valid_to . ')</span></a></p>';
                }

            }

            // Supplier
            if (auth()->guard('admin')->check()) {
                if ($row->rCodeID) {
                    $rCode = explode(',', $row->rCodeID);
                    $supplierColumn .= '<p><a  data-toggle="modal" data-target="#opt-supp-modal" data-opt-id="'. $row->id .'"  style="border-bottom: 1px solid #ddd;padding: 1px;cursor:pointer">Supplier ('. count($rCode) .')</a></p>';
                }else{
                    $supplierColumn .= '--';
                }
            }

            $checkedPublished = $row->isPublished ? 'checked' : '';
            $actionsColumn = '<div style="height: 30px;float:left;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '?page=' . (((int)$request->start / (int)$request->length) + 1) . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            if (auth()->guard('admin')->check())
                $actionsColumn .= '<div style="height: 30px;float: left;"><a class="copyOption" href="' . url('/' . $request->model . '/' . $row->id . '/copy') . '"><i class="icon-cz-copy"></i></a></div>';
            $actionsColumn .= '<form method="POST" action="' . url('/' . $request->model . '/' . $row->id . '/delete') . '">';
            $actionsColumn .= '<input type="hidden" name="_token" value="' . csrf_token() . '" >';
            $actionsColumn .= '<div style="height: 30px;margin-left: -6px;"><button type="submit" onclick="return confirm(\'Are you sure?\')"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></button></div>';
            $actionsColumn .= '</form>';
            if (count($model::findOrFail($row->id)->pricings()->get()) > 0) {
                $pricingColumn = '<a href="' . url('/pricings?page=' . $pageID . '?optionId=') . $row->id . '">Show price</a>';
            } else {
                $pricingColumn = '<label style="font-size: 10px;" class="label label-danger">Please add pricing !</label>';
            }

            if (auth()->guard('admin')->check()) {
                $publishedColumn = '<div>
                                        <input data-id="' . $row->id . '" class="toggle-class4" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '>
                                    </div>';
            } else {
                if ($row->isPublished == 1) {
                    $publishedColumn = '<p><span class="db-done"> Published </span></p>';
                } else {
                    $publishedColumn = '<p><span class="db-not-done"> Not Published </span></p>';
                }
            }

            $commissionColumn = '<input type="text" readonly="" value="' . $option->comission . '" class="commissionInput"><button data-option-id="' . $row->id . '" class="btn btn-primary saveCommissionInput" style="display: none;">Save</button>';
            $apiColumn = '';
            if (auth()->guard('admin')->check() && in_array(auth()->user()->id, [2, 60, 16])) {
                if ($option->connectedToApi == 0) {
                    $apiColumn = '<div class="text-center spanDiv" style="margin-bottom: 10px;"><span class="label label-info">Not Connected to API</span></div>
                              <div class="text-center">
                              <button style="height: 0!important;" class="btn btn-primary btn apiButton disconnectedToApi" data-option-id="' . $row->id . '">
                              <i style="font-size:.8rem!important;background: #28a745!important;" class="icon-cz-connection"></i>
                              </button>
                              </div>';
                } else {
                    $apiColumn = '<div class="text-center spanDiv" style="margin-bottom: 10px;">
                              <span class="label label-info">Connected to API</span>
                              </div>
                              <div class="text-center">
                              <button style="height: 0!important;" class="btn btn-primary btn apiButton  connectedToApi " data-option-id="' . $row->id . '">
                              <i style="font-size: .8rem!important; background: #
                              dc3545!important;" class="icon-cz-connection"></i>
                              </button>
                              </div>';
                }


            }

            $connectedProductsColumn = '';
            if (count($option->products()->get()) > 0) {
                foreach ($option->products()->get() as $product) {
                    $connectedProductsColumn .= "<a class='popup' href='" . url('product/' . $product->id . '/edit') . "'>";
                    $connectedProductsColumn .= "<label for='myPopup" . $product->id . "' style='width:100%;cursor:pointer;margin-right:2px;font-size: 11px' class='col-md-2 label label-info'>$product->referenceCode<span class='popuptext' id='myPopup" . $product->id . "'>$product->title</span></label></a>";
                }
            }

            $data[] = [
                "referenceCode" => $row->referenceCode,
                "title" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit?page=' . (((int)$request->start / (int)$request->length) + 1)) . '"><span class="list-enq-name">' . $row->title . '</span></a>',
                'category' => $row->category,
                'pricing' => $pricingColumn,
                'commission' => $row->comission,
                'availability' => $availabilityColumn,
                'published' => $publishedColumn,
                'comission' => $commissionColumn,
                'supplier' => $supplierColumn,
                "actions" => $actionsColumn,
                "api" => $apiColumn,
                "connectedProducts" => $connectedProductsColumn,
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'productID' => $productID];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForAttraction($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model::where('name', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $statusColumn = '';

            if ($row->isActive == 1) {
                $statusColumn .= '<button data-is-active="1" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Passive</button>';
            } else {
                $languages = Language::where('code', '!=', 'en')->pluck('id')->toArray();
                $isAllTranslationsAreMade = AttractionTranslation::where('attractionID', $row->id)->whereIn('languageID', $languages)->count();
                if (count($languages) != $isAllTranslationsAreMade) {
                    $statusColumn .= '<span style="padding: 10px; background-color: #cd3539; color: #ffffff;">You must translate this attraction for all languages available to set this attraction active</span>';
                } else {
                    $statusColumn .= '<button data-is-active="0" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Active</button>';
                }
            }

            $data[] = [
                "name" => $row->name,
                "status" => $statusColumn,
                "actions" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a>',
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForAttractionPCT($model, $searchValue, $row, $rowperpage, $request)
    {
        $model = $model::on('mysql2');
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model->where('name', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $statusColumn = '';

            if ($row->isActive == 1) {
                $statusColumn .= '<button data-is-active="1" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Passive</button>';
            } else {
                $languages = Language::where('code', '!=', 'en')->pluck('id')->toArray();
                $isAllTranslationsAreMade = AttractionTranslation::on('mysql2')->where('attractionID', $row->id)->whereIn('languageID', $languages)->count();
                if (count($languages) != $isAllTranslationsAreMade) {
                    $statusColumn .= '<span style="padding: 10px; background-color: #cd3539; color: #ffffff;">You must translate this attraction for all languages available to set this attraction active</span>';
                } else {
                    $statusColumn .= '<button data-is-active="0" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Active</button>';
                }
            }

            $data[] = [
                "name" => $row->name,
                "status" => $statusColumn,
                "actions" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a>',
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForAttractionPCTcom($model, $searchValue, $row, $rowperpage, $request)
    {
        $model = $model::on('mysql3');
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model->where('name', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $statusColumn = '';

            if ($row->isActive == 1) {
                $statusColumn .= '<button data-is-active="1" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Passive</button>';
            } else {
                $languages = Language::where('code', '!=', 'en')->pluck('id')->toArray();
                $isAllTranslationsAreMade = AttractionTranslation::on('mysql3')->where('attractionID', $row->id)->whereIn('languageID', $languages)->count();
                if (count($languages) != $isAllTranslationsAreMade) {
                    $statusColumn .= '<span style="padding: 10px; background-color: #cd3539; color: #ffffff;">You must translate this attraction for all languages available to set this attraction active</span>';
                } else {
                    $statusColumn .= '<button data-is-active="0" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Active</button>';
                }
            }

            $data[] = [
                "name" => $row->name,
                "status" => $statusColumn,
                "actions" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a>',
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForAttractionCTP($model, $searchValue, $row, $rowperpage, $request)
    {
        $model = $model::on('mysql4');
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model->where('name', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $statusColumn = '';

            if ($row->isActive == 1) {
                $statusColumn .= '<button data-is-active="1" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Passive</button>';
            } else {
                $languages = Language::where('code', '!=', 'en')->pluck('id')->toArray();
                $isAllTranslationsAreMade = AttractionTranslation::on('mysql4')->where('attractionID', $row->id)->whereIn('languageID', $languages)->count();
                if (count($languages) != $isAllTranslationsAreMade) {
                    $statusColumn .= '<span style="padding: 10px; background-color: #cd3539; color: #ffffff;">You must translate this attraction for all languages available to set this attraction active</span>';
                } else {
                    $statusColumn .= '<button data-is-active="0" data-attraction-id="' . $row->id . '" id="setStatusButton" class="btn btn-primary">Set Active</button>';
                }
            }

            $data[] = [
                "name" => $row->name,
                "status" => $statusColumn,
                "actions" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '" style="float:left"><i class="icon-cz-edit"></i></a>',
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @param $companyName
     * @return array
     */
    public function getDataForProduct($model, $searchValue, $row, $rowperpage, $request, $companyName)
    {
        $data = [];

        $country = $request->country;
        $city = $request->city;
        $attraction = $request->attraction;
        $category = $request->category;
        $supplier = $request->supplier;
        $published = $request->published;
        $notPublished = $request->notPublished;
        $orderBy = $request->orderBy;
        $pendingApproval = $request->pendingApproval;
        $specialOffer = $request->specialOffer;

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;

        $ownerID = -1;
        if (auth()->guard('admin')->check()) {
            if ($supplier != $ownerID) {
                $ownerID = $supplier;
            }
        }
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        if (auth()->guard('subUser')->check()) {
            $ownerID = Supplier::where('id', auth()->guard('subUser')->user()->supervisor)->first()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = Product::with('copies')->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            })->where('copyOf', '=', null);

        if ($country != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('country', $country);
        }

        if ($city != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('city', $city);
        }
        if ($attraction != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('category', 'like', '%' . $category . '%');
        }


        $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) use ($published, $notPublished, $pendingApproval) {

            $q->where(function ($sub) use ($published, $notPublished, $pendingApproval) {
                if ($published == '1') {
                    $sub->where('isPublished', 1);
                }
            });

            $q->orWhere(function ($sub) use ($published, $notPublished, $pendingApproval) {

                if ($notPublished == '1') {
                    $sub->where('isPublished', 0);
                }

            });

            $q->orWhere(function ($sub) use ($published, $notPublished, $pendingApproval) {

                if ($pendingApproval == '1') {
                    $sub->where('isDraft', 1);
                }

            });


        });

        if ($specialOffer == '1') {
            $totalRecordwithFilter = $totalRecordwithFilter->has("specialOffers");
        }

        /*   if ($published == '0' && $notPublished == '1') {
               $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 0);
           }
           if ($published == '1' && $notPublished == '0') {
               $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 1);
           }
           if($pendingApproval == '0') {
               $totalRecordwithFilter = $totalRecordwithFilter->doesntHave('copies');
           }*/


        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = Product::with('copies')->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            })->where('copyOf', '=', null);
        if ($country != '') {
            $records = $records->where('country', $country);
        }
        if ($city != '') {
            $records = $records->where('city', $city);
        }
        if ($attraction != '') {
            $records = $records->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $records = $records->where('category', 'like', '%' . $category . '%');
        }


        $records = $records->where(function ($q) use ($published, $notPublished, $pendingApproval) {

            $q->where(function ($sub) use ($published, $notPublished, $pendingApproval) {
                if ($published == '1') {
                    $sub->where('isPublished', 1);
                }
            });

            $q->orWhere(function ($sub) use ($published, $notPublished, $pendingApproval) {

                if ($notPublished == '1') {
                    $sub->where('isPublished', 0);
                }

            });

            $q->orWhere(function ($sub) use ($published, $notPublished, $pendingApproval) {

                if ($pendingApproval == '1') {
                    $sub->where('isDraft', 1);
                }

            });


        });

        if ($specialOffer == '1') {
            $records = $records->has("specialOffers");
        }

        /*  if ($published == '0' && $notPublished == '1') {
              $records = $records->where('isPublished', 0);
          }
          if ($published == '1' && $notPublished == '0') {
              $records = $records->where('isPublished', 1);
          }

          if($pendingApproval == '0') {
              $records = $records->doesntHave('copies');
          }*/


        if ($orderBy == 'newest') {
            $records = $records->orderBy('updated_at', 'desc');
        }
        if ($orderBy == 'oldest') {
            $records = $records->orderBy('updated_at', 'asc');
        }
        if ($orderBy == 'titleAsc') {
            $records = $records->orderBy('title', 'ASC');
        }
        if ($orderBy == 'titleDesc') {
            $records = $records->orderBy('title', 'DESC');
        }
        if ($orderBy == 'idAsc') {
            $records = $records->orderBy('id', 'ASC');
        }
        if ($orderBy == 'idDesc') {
            $records = $records->orderBy('id', 'DESC');
        }

        $records = $records->skip($row)->take($rowperpage)->get();

        foreach ($records as $rowIndex => $row) {
            $coverWrapper = Storage::disk('s3')->url('product-images/default_product.jpg');
            if (!is_null($row->productCover)) {
                $coverSrc = $row->productCover->src;
                $coverWrapper = Storage::disk('s3')->url('product-images-xs/' . $coverSrc);
            }
            $imageColumn = '<img style="border-radius:5px" src="' . $coverWrapper . '" width="64" height="64">';

            $optionsIsPublished = [];
            foreach ($row->options()->get() as $o) {
                array_push($optionsIsPublished, $o->isPublished);
            }
            $optionsColumn = 'Option(s)(0)';
            if ($row->options()->count() > 0) {
                if (!is_null($row->options()->get())) {
                    $countOfOptions = $row->options()->count();
                }
                $optionsColumn = '<a href="' . url('/option/?page=1?productId=' . $row->id) . '">Option(s) (' . $countOfOptions . ')</a>';
            }
            $checkedDraft = $row->isDraft ? 'checked' : '';
            $checkedPublished = $row->isPublished ? 'checked' : '';
            $actionsColumn = '<div style="float: left;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '?page=' . (((int)$request->start / (int)$request->length) + 1) . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $actionsColumn .= '<form method="POST" action="' . url('/' . $request->model . '/' . $row->id . '/delete') . '">';
            $actionsColumn .= '<input type="hidden" name="_token" value="' . csrf_token() . '" >';
            $actionsColumn .= '<div style="float: left;"><button type="submit" onclick="return confirm(\'Are you sure?\')" style="padding: 0px;"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></button></div>';
            $actionsColumn .= '</form>';
            if ($row->isDraft != 1) {
                $actionsColumn .= '<div style="height: 30px;float: left;"><a target="_blank" href="' . env('APP_URL', 'https://cityzore.com') . '/' . $row->url . '"><i style="font-size: 11px;padding: 4px 10px 4px 4px; cursor: pointer;" class="icon-cz-preview"></i></a></div>';
                $actionsColumn .= '<div><a data-product-id="' . $row->id . '" style="background: none;float: left;border: none" data-toggle="modal" href="#orderModal" class="orderModal"><i style="cursor: pointer;" class="icon-cz-picture"></i></a></div>';
            } else {
                if (!in_array(1, $optionsIsPublished) || is_null($row->coverPhoto)) {
                    $actionsColumn .= '<div style="height: 30px;float: left;"><a class="disabledPreview"><i style="background-color:#0e76a8;font-size: 11px;padding: 4px 10px 4px 4px;cursor: pointer;" class="icon-cz-preview"></i></a></div>';
                } else {
                    $actionsColumn .= '<div style="height: 30px;float: left;"><a target="_blank" href="' . env('APP_ADMIN', 'https://admin.cityzore.com') . '/' . $row->url . '"><i style="background-color:#0e76a8;font-size: 11px;padding: 4px 10px 4px 4px;" class="icon-cz-preview"></i></a></div>';
                }
            }
            if (auth()->guard('admin')->check()) {
                if (is_null($row->city) || is_null($row->title)) {
                    $confirmedColumn = '<div data-platform="main" class="disabledDraft"><input disabled data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '></div>';
                } else {
                    $confirmedColumn = '<input data-platform="main" data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '>';
                }
                if (!in_array(1, $optionsIsPublished)) {
                    $publishedColumn = '<div data-platform="main" class="disabledPublish"><input disabled data-id="' . $row->id . '" class="toggle-class2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '></div>';
                } else {
                    $publishedColumn = '<input data-platform="main" data-id="' . $row->id . '" class="toggle-class2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '>';
                }
                $actionsColumn .= '<div style="height: 30px;float: left;"><a class="copyProduct" href="' . url('/' . $request->model . '/' . $row->id . '/copy') . '"><i class="icon-cz-copy"></i></a></div>';
                $metaTagTitleValue = $row->metaTag()->first() ? $row->metaTag()->first()->title : '';
                $metaTagDescriptionValue = $row->metaTag()->first() ? $row->metaTag()->first()->description : '';
                $metaTagKeywordsValue = $row->metaTag()->first() ? $row->metaTag()->first()->keywords : '';
                $actionsColumn .= '<div style="height: 30px; float:left;"><a class="seoButtonForProduct" style="cursor:pointer;letter-spacing:2px;border-radius:3px;padding: 4px 4px;color:white;background-color: grey">SEO</a></div>';
                $actionsColumn .= '<a class="metaTagCloseButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 2px;color:white;background-color: #dd2c00">CLOSE</a>';
                $actionsColumn .= '<a data-id="' . $row->id . '" class="metaTagSaveButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: #0e76a8">SAVE</a>';
                $actionsColumn .= '<div style="display: none" class="metaTagDiv">';
                $actionsColumn .= '<input value="' . $metaTagTitleValue . '" class="metaTagTitle" type="text" placeholder="title">';
                $actionsColumn .= '<input value="' . $metaTagDescriptionValue . '" class="metaTagDescription" type="text" placeholder="description">';
                $actionsColumn .= '<input value="' . $metaTagKeywordsValue . '" class="metaTagKeywords" type="text" placeholder="keywords">';
                $actionsColumn .= '</div>';
            } else {
                $confirmedColumn = '<input data-platform="main" data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '>';
                if ($row->isPublished == 1) {
                    $publishedColumn = '<p><span class="db-done">Published</span></p>';
                } else {
                    $publishedColumn = '<p><span class="db-not-done"> Not Published </span></p>';
                }
            }
            if (Product::where('copyOf', $row->id)->count() > 0 && $row->isDraft == false) {
                $referenceCode = $row->referenceCode;
                $referenceCode .= '<p style="font-size: 16px!important;"><label class="label label-warning">Waiting for Confirmation</label></p>';
                $referenceCode .= '<p style="font-size: 16px!important;"><label class="label label-warning">Please click edit button to confirm</label></p>';
            } else {
                $referenceCode = $row->referenceCode;
            }

            $hasCopy = Product::where('copyOf', $row->id)->first();

            $data[] = [
                "index" => $row->id,
                "image" => $imageColumn,
                "referenceCode" => $referenceCode,
                "companyName" => $companyName,
                "title" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit?page=' . (((int)$request->start / (int)$request->length) + 1)) . '"><span class="list-enq-name">' . ($hasCopy ? $hasCopy->title : $row->title) . '</span></a>',
                'category' => $row->category,
                'options' => $optionsColumn,
                'confirmed' => $confirmedColumn,
                'published' => $publishedColumn,
                "actions" => $actionsColumn,
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'row' => $row];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @param $companyName
     * @return array
     */
    public function getDataForProductPCT($model, $searchValue, $row, $rowperpage, $request, $companyName)
    {
        $data = [];

        $model = $model::on('mysql2');

        $attraction = $request->attraction;
        $category = $request->category;
        $supplier = $request->supplier;
        $published = $request->published;
        $notPublished = $request->notPublished;
        $orderBy = $request->orderBy;

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;

        $ownerID = -1;
        if (auth()->guard('admin')->check()) {
            if ($supplier != $ownerID) {
                $ownerID = $supplier;
            }
        }
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            });
        if ($attraction != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 1);
        }
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            })->where('copyOf', '=', null);
        if ($attraction != '') {
            $records = $records->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $records = $records->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $records = $records->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $records = $records->where('isPublished', 1);
        }

        if ($orderBy == 'newest') {
            $records = $records->orderBy('updated_at', 'desc');
        }
        if ($orderBy == 'oldest') {
            $records = $records->orderBy('updated_at', 'asc');
        }
        if ($orderBy == 'titleAsc') {
            $records = $records->orderBy('title', 'ASC');
        }
        if ($orderBy == 'titleDesc') {
            $records = $records->orderBy('title', 'DESC');
        }

        $records = $records->skip($row)->take($rowperpage)->get();

        foreach ($records as $rowIndex => $row) {
            $checkedDraft = $row->isDraft ? 'checked' : '';
            $checkedPublished = $row->isPublished ? 'checked' : '';
            $coverWrapper = Storage::disk('s3')->url('product-images/default_product.jpg');
            if (!is_null($row->productCover)) {
                $coverSrc = $row->productCover->src;
                $coverWrapper = Storage::disk('s3')->url('product-images-xs/' . $coverSrc);
            }
            $imageColumn = '<img style="border-radius:5px" src="' . $coverWrapper . '" width="64" height="64">';


            $actionsColumn = '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '?page=' . (((int)$request->start / (int)$request->length) + 1) . ' " style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $metaTagTitleValue = $row->metaTag()->first() ? $row->metaTag()->first()->title : '';
            $metaTagDescriptionValue = $row->metaTag()->first() ? $row->metaTag()->first()->description : '';
            $metaTagKeywordsValue = $row->metaTag()->first() ? $row->metaTag()->first()->keywords : '';
            $actionsColumn .= '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a class="seoButtonForProduct" style="cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: grey">SEO</a></div>';
            $actionsColumn .= '<a class="metaTagCloseButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 2px;color:white;background-color: #dd2c00">CLOSE</a>';
            $actionsColumn .= '<a data-id="' . $row->id . '" class="metaTagSaveButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: #0e76a8">SAVE</a>';
            $actionsColumn .= '<div style="display: none" class="metaTagDiv">';
            $actionsColumn .= '<input value="' . $metaTagTitleValue . '" class="metaTagTitle" type="text" placeholder="title">';
            $actionsColumn .= '<input value="' . $metaTagDescriptionValue . '" class="metaTagDescription" type="text" placeholder="description">';
            $actionsColumn .= '<input value="' . $metaTagKeywordsValue . '" class="metaTagKeywords" type="text" placeholder="keywords">';
            $actionsColumn .= '</div>';

            $confirmedColumn = '<input data-platform="pct" data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '>';

            $publishedColumn = '<input data-platform="pct" data-id="' . $row->id . '" class="toggle-class2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '>';


            if (count(Product::where('copyOf', '=', (int)$row->id)->get()) > 0) {
                $referenceCode = $row->referenceCode . '<p style="font-size: 16px!important;"><label class="label label-warning">Waiting for Confirmation</label></p>';
            } else {
                $referenceCode = $row->referenceCode;
            }

            $data[] = [
                "index" => $row->id,
                "image" => $imageColumn,
                "referenceCode" => $referenceCode,
                "companyName" => $companyName,
                "title" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '"><span class="list-enq-name">' . $row->title . '</span></a>',
                'confirmed' => $confirmedColumn,
                'published' => $publishedColumn,
                "actions" => $actionsColumn,

            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'row' => $row];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @param $companyName
     * @return array
     */
    public function getDataForProductPCTcom($model, $searchValue, $row, $rowperpage, $request, $companyName)
    {
        $data = [];

        $model = $model::on('mysql3');

        $attraction = $request->attraction;
        $category = $request->category;
        $supplier = $request->supplier;
        $published = $request->published;
        $notPublished = $request->notPublished;
        $orderBy = $request->orderBy;

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;

        $ownerID = -1;
        if (auth()->guard('admin')->check()) {
            if ($supplier != $ownerID) {
                $ownerID = $supplier;
            }
        }
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            });
        if ($attraction != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 1);
        }
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            })->where('copyOf', '=', null);
        if ($attraction != '') {
            $records = $records->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $records = $records->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $records = $records->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $records = $records->where('isPublished', 1);
        }

        if ($orderBy == 'newest') {
            $records = $records->orderBy('updated_at', 'desc');
        }
        if ($orderBy == 'oldest') {
            $records = $records->orderBy('updated_at', 'asc');
        }
        if ($orderBy == 'titleAsc') {
            $records = $records->orderBy('title', 'ASC');
        }
        if ($orderBy == 'titleDesc') {
            $records = $records->orderBy('title', 'DESC');
        }

        $records = $records->skip($row)->take($rowperpage)->get();

        foreach ($records as $rowIndex => $row) {
            $checkedDraft = $row->isDraft ? 'checked' : '';
            $checkedPublished = $row->isPublished ? 'checked' : '';
            $coverWrapper = Storage::disk('s3')->url('product-images/default_product.jpg');
            if (!is_null($row->productCover)) {
                $coverSrc = $row->productCover->src;
                $coverWrapper = Storage::disk('s3')->url('product-images-xs/' . $coverSrc);
            }
            $imageColumn = '<img style="border-radius:5px" src="' . $coverWrapper . '" width="64" height="64">';


            $actionsColumn = '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '?page=' . (((int)$request->start / (int)$request->length) + 1) . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $metaTagTitleValue = $row->metaTag()->first() ? $row->metaTag()->first()->title : '';
            $metaTagDescriptionValue = $row->metaTag()->first() ? $row->metaTag()->first()->description : '';
            $metaTagKeywordsValue = $row->metaTag()->first() ? $row->metaTag()->first()->keywords : '';
            $actionsColumn .= '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a class="seoButtonForProduct" style="cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: grey">SEO</a></div>';
            $actionsColumn .= '<a class="metaTagCloseButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 2px;color:white;background-color: #dd2c00">CLOSE</a>';
            $actionsColumn .= '<a data-id="' . $row->id . '" class="metaTagSaveButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: #0e76a8">SAVE</a>';
            $actionsColumn .= '<div style="display: none" class="metaTagDiv">';
            $actionsColumn .= '<input value="' . $metaTagTitleValue . '" class="metaTagTitle" type="text" placeholder="title">';
            $actionsColumn .= '<input value="' . $metaTagDescriptionValue . '" class="metaTagDescription" type="text" placeholder="description">';
            $actionsColumn .= '<input value="' . $metaTagKeywordsValue . '" class="metaTagKeywords" type="text" placeholder="keywords">';
            $actionsColumn .= '</div>';


            $confirmedColumn = '<input data-platform="pctcom" data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '>';

            $publishedColumn = '<input data-platform="pctcom" data-id="' . $row->id . '" class="toggle-class2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '>';

            if (count(Product::where('copyOf', '=', (int)$row->id)->get()) > 0) {
                $referenceCode = $row->referenceCode . '<p style="font-size: 16px!important;"><label class="label label-warning">Waiting for Confirmation</label></p>';
            } else {
                $referenceCode = $row->referenceCode;
            }

            $data[] = [
                "index" => $row->id,
                "image" => $imageColumn,
                "referenceCode" => $referenceCode,
                "companyName" => $companyName,
                "title" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '"><span class="list-enq-name">' . $row->title . '</span></a>',
                'confirmed' => $confirmedColumn,
                'published' => $publishedColumn,
                "actions" => $actionsColumn,
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'row' => $row];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @param $companyName
     * @return array
     */
    public function getDataForProductCTP($model, $searchValue, $row, $rowperpage, $request, $companyName)
    {
        $data = [];

        $model = $model::on('mysql4');

        $attraction = $request->attraction;
        $category = $request->category;
        $supplier = $request->supplier;
        $published = $request->published;
        $notPublished = $request->notPublished;
        $orderBy = $request->orderBy;

        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;

        $ownerID = -1;
        if (auth()->guard('admin')->check()) {
            if ($supplier != $ownerID) {
                $ownerID = $supplier;
            }
        }
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            });
        if ($attraction != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('isPublished', 1);
        }
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                    ->orWhere('id', $searchValue);
            })->where('copyOf', '=', null);
        if ($attraction != '') {
            $records = $records->where('attractions', 'like', '%' . $attraction . '%');
        }
        if ($category != '') {
            $records = $records->where('category', 'like', '%' . $category . '%');
        }
        if ($published == '0' && $notPublished == '1') {
            $records = $records->where('isPublished', 0);
        }
        if ($published == '1' && $notPublished == '0') {
            $records = $records->where('isPublished', 1);
        }

        if ($orderBy == 'newest') {
            $records = $records->orderBy('updated_at', 'desc');
        }
        if ($orderBy == 'oldest') {
            $records = $records->orderBy('updated_at', 'asc');
        }
        if ($orderBy == 'titleAsc') {
            $records = $records->orderBy('title', 'ASC');
        }
        if ($orderBy == 'titleDesc') {
            $records = $records->orderBy('title', 'DESC');
        }

        $records = $records->skip($row)->take($rowperpage)->get();

        foreach ($records as $rowIndex => $row) {
            $checkedDraft = $row->isDraft ? 'checked' : '';
            $checkedPublished = $row->isPublished ? 'checked' : '';
            $coverWrapper = Storage::disk('s3')->url('product-images/default_product.jpg');
            if (!is_null($row->productCover)) {
                $coverSrc = $row->productCover->src;
                $coverWrapper = Storage::disk('s3')->url('product-images-xs/' . $coverSrc);
            }
            $imageColumn = '<img style="border-radius:5px" src="' . $coverWrapper . '" width="64" height="64">';


            $actionsColumn = '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '?page=' . (((int)$request->start / (int)$request->length) + 1) . '" style="float:left"><i class="icon-cz-edit"></i></a></div>';
            $metaTagTitleValue = $row->metaTag()->first() ? $row->metaTag()->first()->title : '';
            $metaTagDescriptionValue = $row->metaTag()->first() ? $row->metaTag()->first()->description : '';
            $metaTagKeywordsValue = $row->metaTag()->first() ? $row->metaTag()->first()->keywords : '';
            $actionsColumn .= '<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="height: 30px;"><a class="seoButtonForProduct" style="cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: grey">SEO</a></div>';
            $actionsColumn .= '<a class="metaTagCloseButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 2px;color:white;background-color: #dd2c00">CLOSE</a>';
            $actionsColumn .= '<a data-id="' . $row->id . '" class="metaTagSaveButton" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: #0e76a8">SAVE</a>';
            $actionsColumn .= '<div style="display: none" class="metaTagDiv">';
            $actionsColumn .= '<input value="' . $metaTagTitleValue . '" class="metaTagTitle" type="text" placeholder="title">';
            $actionsColumn .= '<input value="' . $metaTagDescriptionValue . '" class="metaTagDescription" type="text" placeholder="description">';
            $actionsColumn .= '<input value="' . $metaTagKeywordsValue . '" class="metaTagKeywords" type="text" placeholder="keywords">';
            $actionsColumn .= '</div>';

            $confirmedColumn = '<input data-platform="ctp" data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="success" data-toggle="toggle" data-on="Draft" data-off="Confirmed" ' . $checkedDraft . '>';

            $publishedColumn = '<input data-platform="ctp" data-id="' . $row->id . '" class="toggle-class2" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Published" data-off="Not Published" ' . $checkedPublished . '>';

            if (count(Product::where('copyOf', '=', (int)$row->id)->get()) > 0) {
                $referenceCode = $row->referenceCode . '<p style="font-size: 16px!important;"><label class="label label-warning">Waiting for Confirmation</label></p>';
            } else {
                $referenceCode = $row->referenceCode;
            }

            $data[] = [
                "index" => $row->id,
                "image" => $imageColumn,
                "referenceCode" => $referenceCode,
                "companyName" => $companyName,
                "title" => '<a href="' . url('/' . $request->model . '/' . $row->id . '/edit') . '"><span class="list-enq-name">' . $row->title . '</span></a>',
                'confirmed' => $confirmedColumn,
                'published' => $publishedColumn,
                "actions" => $actionsColumn,
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data, 'pageID' => $pageID, 'row' => $row];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForBooking($model, $searchValue, $row, $rowperpage, $request)
    {
        $from = '';
        $to = '';
        $cFrom = '';
        $cTo = '';
        $isFilter = true;
        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;
        }
        if ($request->has('cFrom') && $request->has('cTo')) {
            $cFrom = $request->cFrom;
            $cTo = $request->cTo;
        }
        if ($request->has('isFilter') && $request->isFilter == "true") $isFilter = true;
        $payment_supplier = "";
        $payment_affiliate = "";
        $commissioner = 0;
        $platforms = $request->platforms != null ? array_map('intval', $request->platforms) : null;
        $approvedBookings = '1';
        $pendingBookings = '1';
        $cancelledBookings = '1';
        $paymentMethod = '';
        $selectedOption = '';
        $selectedRestaurant = '';
        $isImported = '';
        if ($request->has('paymentMethod')) {
            $paymentMethod = $request->paymentMethod;
        }
        if ($request->has('approvedBookings')) {
            $approvedBookings = $request->approvedBookings;
        }
        if ($request->has('pendingBookings')) {
            $pendingBookings = $request->pendingBookings;
        }
        if ($request->has('cancelledBookings')) {
            $cancelledBookings = $request->cancelledBookings;
        }
        if ($request->has('payment_supplier')) {
            $payment_supplier = $request->payment_supplier;
        }
        if ($request->has('payment_affiliate')) {
            $payment_affiliate = $request->payment_affiliate;
        }
        if ($request->has('selectedOption')) {
            $selectedOption = $request->selectedOption;
        }
        if ($request->has('selectedRestaurant') && $request->selectedRestaurant != null) {
            $selectedRestaurant = $request->selectedRestaurant;
        }

        if ($request->has('commissioner')) {
            $commissioner = intval($request->commissioner);
        }

        if ($request->has('withImported') && $request->withImported) {
            $isImported = $request->withImported == 1 ? intval($request->withImported) : 0;
        }

        $data = [];
        // Fetch records
        $invoices = Invoice::with('bookings')->get();
        $product = Product::with('options')->get();
        $options = Option::with('bookings')->get();

        // Total number of record with filtering for admin
        $totalRecordwithFilter = $model::where('status', '!=', 1);
        if (auth()->guard('supplier')->check()) {


            $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) {
                $q->where('companyID', auth()->guard('supplier')->user()->id);
                $q->orWhere(function ($q2) {
                    $q2->whereHas("bookingOption", function ($sub) {
                        $sub->where("rCodeID", auth()->guard('supplier')->user()->id);
                    });
                });
            });


        } else {

            if ($payment_supplier != '') {


                $totalRecordwithFilter = $totalRecordwithFilter->where('companyID', $payment_supplier);


            }

        }


        if ($payment_affiliate != '') {


            $totalRecordwithFilter = $totalRecordwithFilter->where('affiliateID', $payment_affiliate);


        }

        if ($selectedOption != '' || $selectedRestaurant != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) use ($selectedOption, $selectedRestaurant) {
                if ($selectedOption != '')
                    $q->whereIn('optionRefCode', $selectedOption);
                if ($selectedRestaurant != '') {
                    if ($selectedOption == '') {
                        $q->whereHas('bookingOption', function ($q) use ($selectedRestaurant) {
                            $q->whereIn('id', $selectedRestaurant);
                        });
                    } else {
                        $q->orWhereHas('bookingOption', function ($q) use ($selectedRestaurant) {
                            $q->whereIn('id', $selectedRestaurant);
                        });
                    }
                }
            });
        }

        if ($searchValue != null) {
            switch ($searchValue) {
                case str_contains($searchValue, '-searchBooking-'):
                    $searchLast = explode('-searchBooking-', $searchValue)[0];
                    if (str_contains($searchLast, '-')) $searchLast = str_replace('-', '', $searchLast);
                    if (substr($searchLast, 0, 2) == "BR") $searchLast = substr($searchLast, 0, 2) . '-' . substr($searchLast, 2);
                    $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($searchLast) {
                        $query->where('gygBookingReference', 'like', '%' . $searchLast . '%')
                            ->orWhere('bookingRefCode', 'like', '%' . $searchLast . '%')
                            ->orWhere('specialRefCode', 'like', '%' . $searchLast . '%');
                    });
                    break;
                case str_contains($searchValue, '-searchInvoice-'):
                    $searchLast = explode('-searchInvoice-', $searchValue)[0];
                    $invodID = Invoice::select(['bookingID'])->where('referenceCode', 'like', '%' . $searchLast . '%')->get()->pluck('bookingID')->toArray();
                    $totalRecordwithFilter = $totalRecordwithFilter->whereIn('id', $invodID);
                    break;
                case str_contains($searchValue, '-searchTraveler-'):
                    $searchLast = explode('-searchTraveler-', $searchValue)[0];
                    $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($searchLast) {
                        $query->whereRaw('travelers->"$[*].email" like ?', '%' . $searchLast . '%')
                            ->orWhere('fullName', 'like', '%' . $searchLast . '%');
                    });
                    break;
            }
        }

        if (!empty($paymentMethod)) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereHas('invoc', function ($query) use ($paymentMethod) {
                $query->where('paymentMethod', $paymentMethod);
            });
        }

        if ($from != '' && $to != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereBetween(DB::raw('DATE(dateForSort)'), [date($from), date($to)]);
        }
        if ($cFrom != '' && $cTo != '') {
            $cFrom = $cFrom . ' 00:00:00';
            $cTo = $cTo . ' 23:59:59';
            $totalRecordwithFilter = $totalRecordwithFilter->whereBetween(DB::raw('DATE(created_at)'), [date($cFrom), date($cTo)]);
        }

        if ($approvedBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('status', '!=', 0);
        }
        if ($pendingBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereNotIn('status', [4, 5]);
        }
        if ($cancelledBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereNotIn('status', [2, 3]);
        }
        if ($platforms != null) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereIn('platformID', $platforms);
            // if(in_array(5, $platforms)) {
            //     $totalRecordwithFilter = $totalRecordwithFilter->where('isViator', 1);
            // }
        }
        if ($commissioner != 0) {
            $totalRecordwithFilter = $totalRecordwithFilter->where('userID', $commissioner);
        }

        if ($isImported) {
            $totalRecordwithFilter = $totalRecordwithFilter->where('is_imported', $isImported);
        }

        $totalRecordwithFilter = $totalRecordwithFilter->count();


        $records = $model::where('status', '!=', 1);

        if ($isImported) {
            $records = $records->where('is_imported', $isImported);
        }

        if (auth()->guard('supplier')->check()) {
            $records = $records->where(function ($q) {
                $q->where('companyID', auth()->guard('supplier')->user()->id);
                $q->orWhere(function ($q2) {
                    $q2->whereHas("bookingOption", function ($sub) {
                        $sub->where("rCodeID", auth()->guard('supplier')->user()->id);
                    });
                });
            });
        } else {

            if ($payment_supplier != '') {


                $records = $records->where('companyID', $payment_supplier);


            }


        }


        if ($payment_affiliate != '') {


            $records = $records->where('affiliateID', $payment_affiliate);


        }

        if ($selectedOption != '' || $selectedRestaurant != '') {
            $records = $records->where(function ($q) use ($selectedOption, $selectedRestaurant) {
                if ($selectedOption != '')
                    $q->whereIn('optionRefCode', $selectedOption);
                if ($selectedRestaurant != '') {
                    if ($selectedOption == '') {
                        $q->whereHas('bookingOption', function ($q) use ($selectedRestaurant) {
                            $q->whereIn('id', $selectedRestaurant);
                        });
                    } else {
                        $q->orWhereHas('bookingOption', function ($q) use ($selectedRestaurant) {
                            $q->whereIn('id', $selectedRestaurant);
                        });
                    }
                }
            });
        }

        if ($searchValue != null) {
            switch ($searchValue) {
                case str_contains($searchValue, '-searchBooking-'):
                    $searchLast = explode('-searchBooking-', $searchValue)[0];
                    if (str_contains($searchLast, '-')) $searchLast = str_replace('-', '', $searchLast);
                    if (substr($searchLast, 0, 2) == "BR") $searchLast = substr($searchLast, 0, 2) . '-' . substr($searchLast, 2);
                    $records = $records->where(function ($query) use ($searchLast) {
                        $query->where('gygBookingReference', 'like', '%' . $searchLast . '%')
                            ->orWhere('bookingRefCode', 'like', '%' . $searchLast . '%')
                            ->orWhere('specialRefCode', 'like', '%' . $searchLast . '%');
                    });
                    break;
                case str_contains($searchValue, '-searchInvoice-'):
                    $searchLast = explode('-searchInvoice-', $searchValue)[0];
                    $invodID = Invoice::select(['bookingID'])->where('referenceCode', 'like', '%' . $searchLast . '%')->get()->pluck('bookingID')->toArray();
                    $records = $records->whereIn('id', $invodID);
                    break;
                case str_contains($searchValue, '-searchTraveler-'):
                    $searchLast = explode('-searchTraveler-', $searchValue)[0];
                    $records = $records->where(function ($query) use ($searchLast) {
                        $query->whereRaw('travelers->"$[*].email" like ?', '%' . $searchLast . '%')
                            ->orWhere('fullName', 'like', '%' . $searchLast . '%');
                    });
                    break;
            }
        }

        if (!empty($paymentMethod)) {
            $records = $records->whereHas('invoc', function ($query) use ($paymentMethod) {
                $query->where('paymentMethod', $paymentMethod);
            });
        }

        if ($from != '' && $to != '') {
            $records = $records->whereBetween(DB::raw('DATE(dateForSort)'), [date($from), date($to)]);
        }
        if ($cFrom != '' && $cTo != '') {
            $cFrom = $cFrom . ' 00:00:00';
            $cTo = $cTo . ' 23:59:59';
            $records = $records->whereBetween(DB::raw('DATE(created_at)'), [date($cFrom), date($cTo)]);
        }

        if ($approvedBookings == '0') {
            $records = $records->where('status', '!=', 0);
        }
        if ($pendingBookings == '0') {
            $records = $records->whereNotIn('status', [4, 5]);
        }
        if ($cancelledBookings == '0') {
            $records = $records->whereNotIn('status', [2, 3]);
        }
        if ($platforms != null) {
            $records = $records->whereIn('platformID', $platforms);
            // if(in_array(5, $platforms)) {
            //     $records = $records->where('isViator', 1);
            // }
        }
        if ($commissioner != 0) {
            $records = $records->where('userID', $commissioner);
        }

        if ($request->has('order')) {
            $records = $records->orderBy('dateForSort', $request->input('order.0.dir'))->skip($row)->take($rowperpage)->get();
        } else {
            $records = $records->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();
        }
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $config = Config::where('userID', $ownerID)->first();
        foreach ($records as $row) {
            $platform = Platform::where('id', $row->platformID)->first();
            $dateColumn = '<div class="date" style="background-color: #253d5214; text-align: center; width: 95px;">';
            $monthContainer = '';
            if (($row->gygBookingReference != null)) {
                $monthContainer .= '<p class="monthContainer" style="background-color: orange; color: white;">' . date('F', strtotime($row->dateTime)) . '</p>';
            } else if ($row->platformID != 0) {
                $monthContainer .= '<p class="monthContainer" style="background-color: ' . $platform->colorBg . ' !important;color: ' . $platform->color . '">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 0) {
                $monthContainer .= '<p style="background-color: #1d57c7!important;" class="active2 monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 2) {
                $monthContainer .= '<p class="canceled monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 4) {
                $monthContainer .= '<p class="month pending monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->status == 2 || $row->status == 3) {
                $monthContainer .= '<p class="canceled monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            } else if ($row->status == 0) {
                $monthContainer .= '<p class="active2 monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            } else if ($row->status == 4 || $row->status == 5) {
                $monthContainer .= '<p class="month pending monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            }

            $dateColumn .= $monthContainer;
            if (!($row->gygBookingReference == null)) {
                $explodedHour = explode('T', $row->dateTime)[1];
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime($row->dateTime)) . '</strong><br>' . date('D', strtotime($row->dateTime)) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime($row->dateTime)) . '</strong></p>';
                $dateColumn .= '<p>Time</p>';
                $dateColumn .= '<p><strong>';
                if (explode('+', $explodedHour)[0] == '00:00:00') {
                    $dateColumn .= 'Operating Hours';
                } else {
                    $dateColumn .= explode('+', $explodedHour)[0];
                }
                $dateColumn .= '</strong></p>';

                if (auth()->guard("admin")->check()) {
                    if ($row->invoice_numbers->count()) {
                        $label_class = "label-success";
                    } else {
                        $label_class = "label-danger";
                    }


                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


            } else if ($row->isBokun == 1 || $row->isViator == 1) {
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime($row->dateForSort)) . '</strong><br>' . date('D', strtotime($row->dateForSort)) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime($row->dateForSort)) . '</strong></p>';
                $dateColumn .= '<p>Time</p>';
                if (json_decode($row->hour, true)[0]['hour'] == "00:00") {
                    $dateColumn .= 'Operating Hours';
                } else {
                    $dateColumn .= json_decode($row->hour, true)[0]['hour'];
                }
                $dateColumn .= '<p><strong>';
                $dateColumn .= '</strong></p>';


                if (auth()->guard("admin")->check()) {

                    if ($row->invoice_numbers->count()) {
                        $label_class = "label-success";
                    } else {
                        $label_class = "label-danger";
                    }


                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


            } else {
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</strong><br>' . date('D', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</strong></p>';
                $dateColumn .= '<p>Time</p>';
                if (!is_null($row->hour)) {
                    foreach (json_decode($row->hour, true) as $dateTime) {
                        $dateColumn .= '<p><strong>' . $dateTime['hour'] . '</strong></p>';
                    }
                }
                if ($row->invoice_numbers->count()) {
                    $label_class = "label-success";
                } else {
                    $label_class = "label-danger";
                }

                if (auth()->guard("admin")->check()) {
                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


            }
            $dateColumn .= '</div>';


            if ($row->isBokun != 1 && $row->isViator != 1) {


                $tourColumn = '';
                if ($row->gygBookingReference == null) {
                    if ($product->where('referenceCode', '=', explode('-', $row->reservationRefCode)[0])->first()) {
                        $tourColumn .= '<strong>' . $product->where('referenceCode', '=', explode('-', $row->reservationRefCode)[0])->first()->title . '</strong>';
                    } else {
                        $tourColumn .= '';
                    }
                } else {
                    $tourColumn .= '<strong>GYG Product</strong>';
                }
                $tourColumn .= '<p><strong>Option:</strong>';
                foreach ($options as $option) {
                    if ($option->referenceCode == $row->optionRefCode) {
                        $tourColumn .= $option->title;
                        $availability = $option->avs()->first();
                    }
                }
                $tourColumn .= '</p>';
            } else {
                $tourColumn = '';

                if ($product->where('referenceCode', '=', $row->productRefCode)->first()) {
                    $tourColumn .= '<strong>' . $product->where('referenceCode', '=', $row->productRefCode)->first()->title . '</strong>';

                    $tourColumn .= '<p><strong>Option:</strong>';
                    $tourColumn .= $product->where('referenceCode', '=', $row->productRefCode)->first()->options()->where('referenceCode', $row->optionRefCode)->first()->title;


                } else if (!empty($row->optionRefCode) && Option::where('referenceCode', $row->optionRefCode)->count()) {
                    $tourColumn .= '<p><strong>Option:</strong>';
                    $tourColumn .= Option::where('referenceCode', $row->optionRefCode)->first()->title;
                } else {
                    $tourColumn .= '';
                }

            }


            $travelers = json_decode($row->travelers, true)[0];
            $firstName = '';
            $lastName = '';
            if (array_key_exists('firstName', $travelers)) {
                $firstName = $travelers['firstName'];
            }
            if (array_key_exists('lastName', $travelers)) {
                $lastName = $travelers['lastName'];
            }
            $tourColumn .= '<p><strong>Lead Traveler:</strong>' . $firstName . ' ' . $lastName . '</p>';
            if (array_key_exists('phoneNumber', json_decode($row->travelers, true)[0])) {
                $tourColumn .= '<p><strong>Phone Number:</strong><a href="tel:' . json_decode($row->travelers, true)[0]['phoneNumber'] . '"> ' . json_decode($row->travelers, true)[0]['phoneNumber'] . '</a></p>';
            }
            if (array_key_exists('email', json_decode($row->travelers, true)[0])) {
                $tourColumn .= '<p><strong>E-mail Address:</strong><a href="mailto:' . json_decode($row->travelers, true)[0]['email'] . '"> ' . json_decode($row->travelers, true)[0]['email'] . '</a></p>';
            }
            if (!($row->travelerHotel == null)) {
                $tourColumn .= '<p><strong>Hotel Address:</strong> ' . $row->travelerHotel . '</p>';
            }

            $bookingRefColumn = '';
            $bookingLang = '';
            $bookingRefCode = '';

            if ($row->gygBookingReference == null) {
                if (count(explode("-", $row->bookingRefCode)) > 3) {
                    $bookingRefColumn .= '<p><strong>' . explode("-", $row->bookingRefCode)[3] . '</strong></p>';
                    $bookingRefCode = explode("-", $row->bookingRefCode)[3];
                } else {
                    $bookingRefColumn .= '<p><strong>' . $row->bookingRefCode . '</strong></p>';
                    $bookingRefCode = $row->bookingRefCode;
                }
            } else {
                $bookingRefColumn .= '<p><strong>' . $row->gygBookingReference . '</strong></p>';
                $bookingRefCode = $row->gygBookingReference;

                $bkn_arr = explode('-', $row->bookingRefCode);
                $bkn_count = count($bkn_arr);
                $bookingRefColumn .= '<p><strong>Bkn Ref Code:</strong> <br> ' . $bkn_arr[$bkn_count - 1] . '</p>';

                if ($row->language != null) {
                    $bookingLang = '<p><strong>Lang:</strong> ' . ucwords($row->language) . '</p>';
                }
            }
            $bookingRefColumn .= '<p><strong>Booked On:</strong> <br> ' . date('d-m-Y H:i', strtotime($row->created_at)) . '</p>';
            $bookingRefColumn .= '<p><strong>Participants:</strong>';
            foreach (json_decode($row->bookingItems, true) as $participants) {
                $bookingRefColumn .= $participants['category'] . ': ' . $participants['count'];
            }
            $bookingRefColumn .= '</p>';
            $bookingRefColumn .= '<p><strong>Price:</strong> <i class="' . $config->currencyName->iconClass . '"></i> ' . $config->calculateCurrency($row->totalPrice, $config->currencyName->value, $row->currencyID) . '</p>';
            $bookingRefColumn .= $bookingLang;

            $statusColumn = '';
            $statusColumn .= '<div class="tri-state-toggle">';

            if ($row->isBokun == 1 || $row->isViator == 1) {
                if ($row->status == 0) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="active2 tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                } else if ($row->status == 3 || $row->status == 2) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="canceled tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                }
            } else {
                if ($row->status == 0) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="active2 tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="4" class="tri-state-toggle-button toggle-button2">';
                    $statusColumn .= 'Pending';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                } else if ($row->status == 4 || $row->status == 5) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="4" class="pending tri-state-toggle-button toggle-button2">';
                    $statusColumn .= 'Pending';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                } else if ($row->status == 3 || $row->status == 2) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="canceled tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                }
            }

            $statusColumn .= '</div>';

            $rCodeColumn = '<div class="col-md-12">';
            if ($row->status != 0) {
                $rCodeColumn .= '<div class="col-md-8">';
                if (!is_null($row->rCodeID)) {
                    $rCode = Rcode::where('id', $row->rCodeID)->first()->rCode;
                    $rCodeColumn .= '<input type="text" readonly value="' . $rCode . '" class="rCodeInput" />';
                } else {
                    $rCodeColumn .= '<input type="text" readonly value="" class="rCodeInput" />';
                }
                if ($row->restaurant) {
                    $rCodeColumn .= '<br><span><strong>Restaurant:</strong> ' . $row->restaurant->companyName . '</span>';
                }
                $rCodeColumn .= '</div>';
                $rCodeColumn .= '<div class="col-md-4">';
                $rCodeColumn .= '<button data-booking-status="' . $row->status . '" data-booking-id="' . $row->id . '" class="btn btn-primary saveRCodeInput" style="display: none;">Save</button>';
                $rCodeColumn .= '</div>';
            } elseif ($row->status == 0 && !is_null($row->rCodeID)) {
                $rCode = Rcode::where('id', $row->rCodeID)->first()->rCode;
                $rCodeColumn .= '<span>' . $rCode . '</span>';
                if ($row->restaurant) {
                    $rCodeColumn .= '<br><span><strong>Restaurant:</strong> ' . $row->restaurant->companyName . '</span>';
                }
            } else {
                $rCodeColumn .= '<p>This booking doesn\'t have a restaurant option</p>';
            }
            $rCodeColumn .= '</div>';

            $salesColumn = '<p>';
            $salesColumn .= '<strong>Invoice ID:</strong> <br>';
            if ($row->gygBookingReference == null && !is_null($invoices->where('bookingID', '=', $row->id)->first())) {
                $refCode = $invoices->where('bookingID', '=', $row->id)->first()->referenceCode;
                $salesColumn .= $refCode;
            }
            $salesColumn .= '</p>';
            $salesColumn .= '<p>';
            $salesColumn .= '<strong>Payment Method:</strong> <br>';
            $salesColumn .= '<span style="text-transform: capitalize">';
            if (($row->gygBookingReference == null && $row->isBokun == 0 && $row->isViator == 0) && !is_null($invoices->where('bookingID', '=', $row->id)->first())) {
                $userID = (int)$row->userID;
                $user = User::where('id', $userID)->first();
                $paymentMethod = $invoices->where('bookingID', '=', $row->id)->first()->paymentMethod;
                $salesColumn .= $paymentMethod;
                $salesColumn .= "<br><span style='font-weight:bold;'>" . $platform->name . "</span>";
            } elseif ($row->gygBookingReference == null && ($row->isBokun == 1 || $row->isViator == 1)) {
                $salesColumn .= $platform->name;
            } else {
                $salesColumn .= 'GETYOURGUIDE';
            }


            if ($row->affiliater) {
                $salesColumn .= '<br><br>Affiliate By <br><strong>' . $row->affiliater->email . '</strong> <br>';
            }


            $salesColumn .= '</span>';

            if ($row->coupon) {

                if (!empty(json_decode($row->coupon, true)[0]["coupon"]["couponCode"])) {
                    $couponCode = json_decode($row->coupon, true)[0]["coupon"]["couponCode"];
                    $salesColumn .= '<br><span style="color: #449d44;">Coupon: ' . $couponCode . '</span>';
                } else {
                    $couponCode = json_decode($row->coupon, true)["coupon"]["couponCode"];
                    $salesColumn .= '<br><span style="color: #449d44;">Coupon: ' . $couponCode . '</span>';
                }


            }

            if ($row->user) {
                if ($row->user->companyName)
                    $salesColumn .= '<br><br><span><strong>Company Name:</strong> ' . $row->user->companyName . '</span>';
                else
                    $salesColumn .= '<br><br><span><strong>Email:</strong> ' . $row->user->email . '</span>';
            }

            $externalPayment = ExternalPayment::where('bookingRefCode', $bookingRefCode)->first();
            if ($externalPayment) {
                if ($externalPayment->is_paid == 0)
                    $salesColumn .= '<br><br><span><strong>External Payment:</strong> <span class="db-not-done">Not Paid</span></span>';
                elseif ($externalPayment->is_paid == 1)
                    $salesColumn .= '<br><br><span><strong>External Payment:</strong> <span class="db-done">Paid</span></span>';
            }

            $contactMailLog = \App\BookingContactMailLog::where('booking_id', $row->id)->orderBy('id', 'desc')->first();
            if ($contactMailLog) {
                if (json_decode($contactMailLog->check_information, true)["status"])
                    $salesColumn .= '<br><br><span><strong>Mail Check: </strong> <span class="db-done">Checked</span></span>';
                else
                    $salesColumn .= '<br><br><span><strong>Mail Check: </strong> <span class="db-not-done">Unchecked</span></span>';
            }

            $salesColumn .= '</p>';

            if ($row->status == 2 ||$row->status == 3) {
                $moreColumn = '<button  type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button>';
                //$moreColumn = '<button disabled="disabled" type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button>'
                if ($row->gygBookingReference == null){
                    $moreColumn .= '<button disabled="disabled" type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Invoice</button></a>';
                }
                $moreColumn .= '<button type="submit" disabled="disabled" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Add Comment</button></a>';
                //$moreColumn .= '<button type="submit" disabled="disabled" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Special Ref. Code</button></a>';
                $moreColumn .= '<a href="'.url('/booking/specialRefCode/'.$row->id).'" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Special Ref. Code</button></a>';
                if (!is_null($row->specialRefCode)) {
                    $moreColumn .= '<span style="background-color: #449d44; color: #ffffff;">Special Ref. Code: ' . $row->specialRefCode . '</span>';
                }
                if($row->extra_files()->count() > 0){
                    $b_class = "light_green";
                }else{
                    $b_class = "light_red";
                }
                $moreColumn .= '<a href="#"><button data-toggle="modal" data-target="#file-import-modal" type="button" class="btn btn-xs btn-primary fire-booking-file-import-button '.$b_class.'" data-id="'.$row->id.'" style="width: 58px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Import</button>
                            <button data-toggle="modal" data-target="#customer-contact-modal" type="button" data-id="'.$row->id.'"  class="fire-booking-customer-contact-button btn btn-xs btn-primary btn-block light_red" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">CONTACT</button></a>';
            } else {
                $moreColumn = '<a href="' . url('/print-pdf/' . $this->cryptRelated->encrypt($row->id)) . '" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button></a>';
                if ($row->gygBookingReference == null) {
                    $moreColumn .= '<a href="' . url('/print-invoice/' . $this->cryptRelated->encrypt($row->id)) . '" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Invoice</button></a>';
                }
                    $moreColumn .= '<a href="'.url('/booking/addComment/'.$row->id).'"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Add Comment</button></a>';


                $moreColumn .= '<a href="'.url('/booking/specialRefCode/'.$row->id).'" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Special Ref. Code</button></a>';
                if (!is_null($row->specialRefCode)) {
                    $moreColumn .= '<span style="background-color: #449d44; color: #ffffff;">Special Ref. Code: ' . $row->specialRefCode . '</span>';
                }



                if(auth()->guard('admin')->check()){

                    if ($row->extra_files()->count() > 0) {
                        $b_class = "light_green";
                    } else {
                        $b_class = "light_red";
                    }

                    if ($row->contacts()->count() > 0) {
                        $c_class = "light_green";
                    } else {
                        $c_class = "light_red";
                    }


                    $moreColumn .= '<a href="#"><button data-toggle="modal" data-target="#file-import-modal" type="button" class="btn btn-xs btn-primary fire-booking-file-import-button ' . $b_class . '" data-id="' . $row->id . '" style="width: 58px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Import</button>

                    <button data-toggle="modal" data-target="#customer-contact-modal" type="button" class="btn btn-xs btn-primary fire-booking-customer-contact-button ' . $c_class . '" data-id="' . $row->id . '" style="width: 58px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Contact</button>

                    </a>';

                    $check = $row->check()->orderBy('id', 'DESC')->get();
                    if (count($check) > 0) {
                        $check = $check->where('status', 1)->first();
                        if ($check)
                            $moreColumn .= '<div class="btn btn-xs" style="width: 120px; padding: 0px;margin-bottom: 3px; color: white; border-color: #8852E4; font-size: 10px; font-weight: bold; background: #00C851 !important;">Checked In</div>';
                        else
                            $moreColumn .= '<div class="btn btn-xs" style="width: 120px; padding: 0px;margin-bottom: 3px; color: white; border-color: #8852E4; font-size: 10px; font-weight: bold; background: #ff4444 !important;">Not Checked In</div>';
                    }
                }

                if (auth()->guard('admin')->check())
                    $moreColumn .= '<div><a href="' . url('/booking/' . $row->id . '/edit') . '" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true" style="background-color: #54bb49; color: #fff; padding: 5px; border-radius: 2px; font-size: 11px; text-align: center;"></i></a></div>';
            }

            $data[] = [
                "date" => $dateColumn,
                "tour" => $tourColumn,
                "bookingRef" => $bookingRefColumn,
                "status" => $statusColumn,
                "rCode" => $rCodeColumn,
                "salesInformations" => $salesColumn,
                "more" => $moreColumn,
                "invoice_status" => $row->invoice_check
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForBookingWithAccess($model, $searchValue, $row, $rowperpage, $request)
    {
        $from = '';
        $to = '';
        $cFrom = '';
        $cTo = '';
        $isFilter = true;
        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;
        }
        if ($request->has('cFrom') && $request->has('cTo')) {
            $cFrom = $request->cFrom;
            $cTo = $request->cTo;
        }
        if ($request->has('isFilter') && $request->isFilter == "true") $isFilter = true;

        $approvedBookings = '1';
        $pendingBookings = '1';
        $cancelledBookings = '1';
        $selectedOption = '';

        if ($request->has('approvedBookings')) {
            $approvedBookings = $request->approvedBookings;
        }
        if ($request->has('pendingBookings')) {
            $pendingBookings = $request->pendingBookings;
        }
        if ($request->has('cancelledBookings')) {
            $cancelledBookings = $request->cancelledBookings;
        }
        if ($request->has('selectedOption')) {
            $selectedOption = $request->selectedOption;
        }

        $data = [];
        // Fetch records
        $invoices = Invoice::with('bookings')->get();
        $product = Product::with('options')->get();
        $options = Option::with('bookings')->get();

        // Total number of record with filtering for admin
        $totalRecordwithFilter = $model::where('status', '!=', 1);
        if (auth()->guard('supplier')->check()) {
            $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) {
                $q->where('companyID', auth()->guard('supplier')->user()->id);
                $q->orWhere(function ($q2) {
                    $q2->whereHas("bookingOption", function ($sub) {
                        $sub->where("rCodeID", auth()->guard('supplier')->user()->id);
                    });
                });
            });
        }
        if ($selectedOption != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereIn('optionRefCode', $selectedOption);

        }

        if ($searchValue != null) {
            switch ($searchValue) {
                case str_contains($searchValue, '-searchBooking-'):
                    $searchLast = explode('-searchBooking-', $searchValue)[0];
                    if (str_contains($searchLast, '-')) $searchLast = str_replace('-', '', $searchLast);
                    if (substr($searchLast, 0, 2) == "BR") $searchLast = substr($searchLast, 0, 2) . '-' . substr($searchLast, 2);
                    $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($searchLast) {
                        $query->where('gygBookingReference', 'like', '%' . $searchLast . '%')
                            ->orWhere('bookingRefCode', 'like', '%' . $searchLast . '%');
                    });
                    break;
                case str_contains($searchValue, '-searchInvoice-'):
                    $searchLast = explode('-searchInvoice-', $searchValue)[0];
                    $invodID = Invoice::select(['bookingID'])->where('referenceCode', 'like', '%' . $searchLast . '%')->get()->pluck('bookingID')->toArray();
                    $totalRecordwithFilter = $totalRecordwithFilter->whereIn('id', $invodID);
                    break;
                case str_contains($searchValue, '-searchTraveler-'):
                    $searchLast = explode('-searchTraveler-', $searchValue)[0];
                    $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($searchLast) {
                        $query->whereRaw('travelers->"$[*].email" like ?', '%' . $searchLast . '%')
                            ->orWhere('fullName', 'like', '%' . $searchLast . '%');
                    });
                    break;
            }
        }
        if ($from != '' && $to != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereBetween(DB::raw('DATE(dateForSort)'), [date($from), date($to)]);
        }
        if ($cFrom != '' && $cTo != '') {
            $cFrom = $cFrom . ' 00:00:00';
            $cTo = $cTo . ' 23:59:59';
            $totalRecordwithFilter = $totalRecordwithFilter->whereBetween(DB::raw('DATE(created_at)'), [date($cFrom), date($cTo)]);
        }


        if ($approvedBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('status', '!=', 0);
        }
        if ($pendingBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereNotIn('status', [4, 5]);
        }
        if ($cancelledBookings == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereNotIn('status', [2, 3]);
        }

        $totalRecordwithFilter = $totalRecordwithFilter->count();

        $records = $model::where('status', '!=', 1);


        if (auth()->guard('supplier')->check()) {
            $records = $records->where(function ($q) {
                $q->where('companyID', auth()->guard('supplier')->user()->id);
                $q->orWhere(function ($q2) {
                    $q2->whereHas("bookingOption", function ($sub) {
                        $sub->where("rCodeID", auth()->guard('supplier')->user()->id);
                    });
                });
            });
        }
        if ($selectedOption != '') {
            $records = $records->whereIn('optionRefCode', $selectedOption);

        }
        if ($searchValue != null) {
            switch ($searchValue) {
                case str_contains($searchValue, '-searchBooking-'):
                    $searchLast = explode('-searchBooking-', $searchValue)[0];
                    if (str_contains($searchLast, '-')) $searchLast = str_replace('-', '', $searchLast);
                    if (substr($searchLast, 0, 2) == "BR") $searchLast = substr($searchLast, 0, 2) . '-' . substr($searchLast, 2);
                    $records = $records->where(function ($query) use ($searchLast) {
                        $query->where('gygBookingReference', 'like', '%' . $searchLast . '%')
                            ->orWhere('bookingRefCode', 'like', '%' . $searchLast . '%');
                    });
                    break;
                case str_contains($searchValue, '-searchInvoice-'):
                    $searchLast = explode('-searchInvoice-', $searchValue)[0];
                    $invodID = Invoice::select(['bookingID'])->where('referenceCode', 'like', '%' . $searchLast . '%')->get()->pluck('bookingID')->toArray();
                    $records = $records->whereIn('id', $invodID);
                    break;
                case str_contains($searchValue, '-searchTraveler-'):
                    $searchLast = explode('-searchTraveler-', $searchValue)[0];
                    $records = $records->where(function ($query) use ($searchLast) {
                        $query->whereRaw('travelers->"$[*].email" like ?', '%' . $searchLast . '%')
                            ->orWhere('fullName', 'like', '%' . $searchLast . '%');
                    });
                    break;
            }
        }


        if ($from != '' && $to != '') {
            $records = $records->whereBetween(DB::raw('DATE(dateForSort)'), [date($from), date($to)]);
        }
        if ($cFrom != '' && $cTo != '') {
            $cFrom = $cFrom . ' 00:00:00';
            $cTo = $cTo . ' 23:59:59';
            $records = $records->whereBetween(DB::raw('DATE(created_at)'), [date($cFrom), date($cTo)]);
        }

        if ($approvedBookings == '0') {
            $records = $records->where('status', '!=', 0);
        }
        if ($pendingBookings == '0') {
            $records = $records->whereNotIn('status', [4, 5]);
        }
        if ($cancelledBookings == '0') {
            $records = $records->whereNotIn('status', [2, 3]);
        }
        $records = $records->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();


        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $config = Config::where('userID', $ownerID)->first();

        foreach ($records as $row) {

            $dateColumn = '<div class="date" style="background-color: #253d5214; text-align: center; width: 95px;">';
            $monthContainer = '';
            if (!($row->gygBookingReference == null)) {
                $monthContainer .= '<p class="monthContainer" style="background-color: orange; color: white;">' . date('F', strtotime($row->dateTime)) . '</p>';
            } else if ($row->fromWebsite == "Musement") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #fc6c4f !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "Viator.com") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #e65f84 !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "Headout") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #ad081b !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "Isango") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #aa9a76 !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "Holibob") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #aa9a76 !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "Railbookers") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #aa9a76 !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->fromWebsite == "RaynaTours") {
                $monthContainer .= '<p class="monthContainer" style="background-color: #aa9a76 !important;">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 0) {
                $monthContainer .= '<p style="background-color: #1d57c7!important;" class="active2 monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 2) {
                $monthContainer .= '<p class="canceled monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if (($row->isBokun == 1 || $row->isViator == 1) && $row->status == 4) {
                $monthContainer .= '<p class="month pending monthContainer">' . date('F', strtotime($row->dateForSort)) . '</p>';
            } else if ($row->status == 2 || $row->status == 3) {
                $monthContainer .= '<p class="canceled monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            } else if ($row->status == 0) {
                $monthContainer .= '<p class="active2 monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            } else if ($row->status == 4 || $row->status == 5) {
                $monthContainer .= '<p class="month pending monthContainer">' . date('F', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
            }
            $dateColumn .= $monthContainer;
            if (!($row->gygBookingReference == null)) {
                $explodedHour = explode('T', $row->dateTime)[1];
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime($row->dateTime)) . '</strong><br>' . date('D', strtotime($row->dateTime)) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime($row->dateTime)) . '</strong></p>';
                $dateColumn .= '<div class="row">';
                $dateColumn .= '<div class="col-md-12 col-sm-12" style="text-align: center">';
                $dateColumn .= '<p>Time</p>';
                $dateColumn .= '<p><strong>';
                if (explode('+', $explodedHour)[0] == '00:00:00') {
                    $dateColumn .= 'Operating Hours';
                } else {
                    $dateColumn .= explode('+', $explodedHour)[0];
                }
                $dateColumn .= '</strong></p>';

                if (auth()->guard("admin")->check()) {
                    if ($row->invoice_numbers->count()) {
                        $label_class = "label-success";
                    } else {
                        $label_class = "label-danger";
                    }


                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


                $dateColumn .= '</div>';
                $dateColumn .= '</div>';
            } else if ($row->isBokun == 1 || $row->isViator == 1) {
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime($row->dateForSort)) . '</strong><br>' . date('D', strtotime($row->dateForSort)) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime($row->dateForSort)) . '</strong></p>';
                $dateColumn .= '<div class="row">';
                $dateColumn .= '<div class="col-md-12 col-sm-12" style="text-align: center">';
                $dateColumn .= '<p>Time</p>';
                if (json_decode($row->hour, true)[0]['hour'] == "00:00") {
                    $dateColumn .= 'Operating Hours';
                } else {
                    $dateColumn .= json_decode($row->hour, true)[0]['hour'];
                }
                $dateColumn .= '<p><strong>';
                $dateColumn .= '</strong></p>';


                if (auth()->guard("admin")->check()) {

                    if ($row->invoice_numbers->count()) {
                        $label_class = "label-success";
                    } else {
                        $label_class = "label-danger";
                    }


                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


                $dateColumn .= '</div>';
                $dateColumn .= '</div>';
            } else {
                $dateColumn .= '<p class="day"><strong style="color: #f23434; font-size:25px;">' . date('d', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</strong><br>' . date('D', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</p>';
                $dateColumn .= '<p class="years" style="color: black"><strong>' . date('Y', strtotime(json_decode($row->dateTime, true)[0]['dateTime'])) . '</strong></p>';
                $dateColumn .= '<div class="row">';
                $dateColumn .= '<div class="col-md-12 col-sm-12" style="text-align: center">';
                $dateColumn .= '<p>Time</p>';
                foreach (json_decode($row->hour, true) as $dateTime) {
                    $dateColumn .= '<p><strong>' . $dateTime['hour'] . '</strong></p>';
                }
                if ($row->invoice_numbers->count()) {
                    $label_class = "label-success";
                } else {
                    $label_class = "label-danger";
                }

                if (auth()->guard("admin")->check()) {
                    $dateColumn .= "<label data-toggle='modal' data-target='#file-invoice-modal' class='label " . $label_class . " invoice-check' data-id='" . $row->id . "' style='margin-bottom:10px;'>Invoice (" . $row->invoice_numbers->count() . ")</label>";
                }


                $dateColumn .= '</div>';
                $dateColumn .= '</div>';
            }
            $dateColumn .= '</div>';


            if ($row->isBokun != 1 && $row->isViator != 1) {


                $tourColumn = '';
                if ($row->gygBookingReference == null) {
                    if ($product->where('referenceCode', '=', explode('-', $row->reservationRefCode)[0])->first()) {
                        $tourColumn .= '<strong>' . $product->where('referenceCode', '=', explode('-', $row->reservationRefCode)[0])->first()->title . '</strong>';
                    } else {
                        $tourColumn .= '';
                    }
                } else {
                    $tourColumn .= '<strong>GYG Product</strong>';
                }
                $tourColumn .= '<p><strong>Option:</strong>';
                foreach ($options as $option) {
                    if ($option->referenceCode == $row->optionRefCode) {
                        $tourColumn .= $option->title;
                        $availability = $option->avs()->first();
                    }
                }
                $tourColumn .= '</p>';
            } else {
                $tourColumn = '';

                if ($product->where('referenceCode', '=', $row->productRefCode)->first()) {
                    $tourColumn .= '<strong>' . $product->where('referenceCode', '=', $row->productRefCode)->first()->title . '</strong>';

                    $tourColumn .= '<p><strong>Option:</strong>';
                    $tourColumn .= $product->where('referenceCode', '=', $row->productRefCode)->first()->options()->first()->title;


                } else if (!empty($row->optionRefCode) && Option::where('referenceCode', $row->optionRefCode)->count()) {
                    $tourColumn .= '<p><strong>Option:</strong>';
                    $tourColumn .= Option::where('referenceCode', $row->optionRefCode)->first()->title;
                } else {
                    $tourColumn .= '';
                }

            }


            $travelers = json_decode($row->travelers, true)[0];
            $firstName = '';
            $lastName = '';
            if (array_key_exists('firstName', $travelers)) {
                $firstName = $travelers['firstName'];
            }
            if (array_key_exists('lastName', $travelers)) {
                $lastName = $travelers['lastName'];
            }
            $tourColumn .= '<p><strong>Lead Traveler:</strong>' . $firstName . ' ' . $lastName . '</p>';
            if (array_key_exists('phoneNumber', json_decode($row->travelers, true)[0])) {
                $tourColumn .= '<p><strong>Phone Number:</strong><a href="tel:' . json_decode($row->travelers, true)[0]['phoneNumber'] . '"> ' . json_decode($row->travelers, true)[0]['phoneNumber'] . '</a></p>';
            }
            if (array_key_exists('email', json_decode($row->travelers, true)[0])) {
                $tourColumn .= '<p><strong>E-mail Address:</strong><a href="mailto:' . json_decode($row->travelers, true)[0]['email'] . '"> ' . json_decode($row->travelers, true)[0]['email'] . '</a></p>';
            }
            if (!($row->travelerHotel == null)) {
                $tourColumn .= '<p><strong>Hotel Address:</strong> ' . $row->travelerHotel . '</p>';
            }

            $bookingRefColumn = '';
            $bookingLang = '';

            if ($row->gygBookingReference == null) {
                if (count(explode("-", $row->bookingRefCode)) > 3) {
                    $bookingRefColumn .= '<p><strong data-target-booking-id-for-voucher="' . $row->id . '">' . $this->hiddenWithAsterix($row, explode("-", $row->bookingRefCode)[3]) . '</strong></p>';
                } else {
                    $bookingRefColumn .= '<p><strong data-target-booking-id-for-voucher="' . $row->id . '">' . $this->hiddenWithAsterix($row, $row->bookingRefCode) . '</strong></p>';
                }
            } else {
                $bookingRefColumn .= '<p><strong data-target-booking-id-for-voucher="' . $row->id . '">' . $this->hiddenWithAsterix($row, $row->gygBookingReference) . '</strong></p>';
                if ($row->language != null) {
                    $bookingLang = '<p><strong>Lang:</strong> ' . ucwords($row->language) . '</p>';
                }
            }
            $bookingRefColumn .= '<p><strong>Booked On:</strong> <br> ' . date('d-m-Y H:i', strtotime($row->created_at)) . '</p>';
            $bookingRefColumn .= '<p><strong>Participants:</strong>';
            foreach (json_decode($row->bookingItems, true) as $participants) {
                $bookingRefColumn .= $participants['category'] . ': ' . $participants['count'];
            }
            $bookingRefColumn .= '</p>';
            $bookingRefColumn .= '<p><strong>Price:</strong> <i class="' . $config->currencyName->iconClass . '"></i> ' . $config->calculateCurrency($row->totalPrice, $config->currencyName->value, $row->currencyID) . '</p>';
            $bookingRefColumn .= $bookingLang;


            $statusColumn = '';
            $statusColumn .= '<div class="tri-state-toggle">';

            if ($row->isBokun == 1 || $row->isViator == 1) {
                if ($row->status == 0) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="active2 tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                } else if ($row->status == 3 || $row->status == 2) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="canceled tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                }
            } else {
                if ($row->status == 0) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="active2 tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="4" class="tri-state-toggle-button toggle-button2">';
                    $statusColumn .= 'Pending';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                } else if ($row->status == 4 || $row->status == 5) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="0" class="tri-state-toggle-button toggle-button1">';
                    $statusColumn .= 'Approved';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="4" class="pending tri-state-toggle-button toggle-button2">';
                    $statusColumn .= 'Pending';
                    $statusColumn .= '</div>';
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                } else if ($row->status == 3 || $row->status == 2) {
                    $statusColumn .= '<div data-booking-id="' . $row->id . '" data-content="3" class="canceled tri-state-toggle-button toggle-button3">';
                    $statusColumn .= 'Cancelled';
                    $statusColumn .= '</div>';
                }
            }

            $statusColumn .= '</div>';

            $rCodeColumn = '<div class="col-md-12">';
            if ($row->status != 0) {
                $rCodeColumn .= '<div class="col-md-8">';
                if (!is_null($row->rCodeID)) {
                    $rCode = Rcode::where('id', $row->rCodeID)->first()->rCode;
                    $rCodeColumn .= '<input type="text" readonly value="' . $rCode . '" class="rCodeInput" />';
                } else {
                    $rCodeColumn .= '<input type="text" readonly value="" class="rCodeInput" />';
                }
                if ($row->restaurant) {
                    $rCodeColumn .= '<br><span><strong>Restaurant:</strong> ' . $row->restaurant->companyName . '</span>';
                }
                $rCodeColumn .= '</div>';
                $rCodeColumn .= '<div class="col-md-4">';
                $rCodeColumn .= '<button data-booking-status="' . $row->status . '" data-booking-id="' . $row->id . '" class="btn btn-primary saveRCodeInput" style="display: none;">Save</button>';
                $rCodeColumn .= '</div>';
            } elseif ($row->status == 0 && !is_null($row->rCodeID)) {
                $rCode = Rcode::where('id', $row->rCodeID)->first()->rCode;
                $rCodeColumn .= '<span>' . $rCode . '</span>';
                if ($row->restaurant) {
                    $rCodeColumn .= '<br><span><strong>Restaurant:</strong> ' . $row->restaurant->companyName . '</span>';
                }
            } else {
                $rCodeColumn .= '<p>This booking doesn\'t have a restaurant option</p>';
            }
            $rCodeColumn .= '</div>';

            $salesColumn = '<p>';
            $salesColumn .= '<strong>Invoice ID:</strong> <br>';
            if ($row->gygBookingReference == null && !is_null($invoices->where('bookingID', '=', $row->id)->first())) {
                $refCode = $invoices->where('bookingID', '=', $row->id)->first()->referenceCode;
                $salesColumn .= $refCode;
            }
            $salesColumn .= '</p>';
            $salesColumn .= '<p>';
            $salesColumn .= '<strong>Payment Method:</strong> <br>';
            $salesColumn .= '<span style="text-transform: capitalize">';
            if ($row->gygBookingReference == null && !is_null($invoices->where('bookingID', '=', $row->id)->first())) {
                $userID = (int)$row->userID;
                $user = User::where('id', $userID)->first();
                $paymentMethod = $invoices->where('bookingID', '=', $row->id)->first()->paymentMethod;
                $salesColumn .= $paymentMethod;
            } elseif ($row->gygBookingReference == null && ($row->isBokun == 1 || $row->isViator == 1)) {
                $salesColumn .= $row->fromWebsite;
            } else {
                $salesColumn .= 'GETYOURGUIDE';
            }


            if ($row->affiliater) {
                $salesColumn .= '<br><br>Affiliate By <br><strong>' . $row->affiliater->email . '</strong> <br>';
            }


            $salesColumn .= '</span>';

            if ($row->coupon) {

                if (!empty(json_decode($row->coupon, true)[0]["coupon"]["couponCode"])) {
                    $couponCode = json_decode($row->coupon, true)[0]["coupon"]["couponCode"];
                    $salesColumn .= '<br><span style="color: #449d44;">Coupon: ' . $couponCode . '</span>';
                } else {
                    $couponCode = json_decode($row->coupon, true)["coupon"]["couponCode"];
                    $salesColumn .= '<br><span style="color: #449d44;">Coupon: ' . $couponCode . '</span>';
                }


            }


            $salesColumn .= '</p>';

            if ($row->status == 2 || $row->status == 3) {
                $moreColumn = '<button disabled="disabled" type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button>';
                if ($row->gygBookingReference == null) {
                    $moreColumn .= '<button disabled="disabled" type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Invoice</button></a>';
                }
                $moreColumn .= '<button type="submit" disabled="disabled" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Add Comment</button></a>';
                $moreColumn .= '<button type="submit" disabled="disabled" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Special Ref. Code</button></a>';

                $moreColumn .= '<button data-toggle="modal" data-target="#customer-contact-modal" type="button" data-id="' . $row->id . '"  class="fire-booking-customer-contact-button btn btn-xs btn-primary btn-block light_red" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">CONTACT</button></a>';
            } else {
                if ($row->check()->count()) {
                    $moreColumn = '<a href="' . url('/print-pdf/' . $this->cryptRelated->encrypt($row->id)) . '" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button></a>';
                } else {
                    $moreColumn = '<a href="#" data-target-booking-id-for-voucher="' . $row->id . '"><button disabled="disabled" type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button></a>';
                }


                if ($row->gygBookingReference == null) {
                    $moreColumn .= '<a href="' . url('/print-invoice/' . $this->cryptRelated->encrypt($row->id)) . '" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Invoice</button></a>';
                }
                $moreColumn .= '<a href="' . url('/booking/addComment/' . $row->id) . '"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Add Comment</button></a>';
                $moreColumn .= '<a href="' . url('/booking/specialRefCode/' . $row->id) . '" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Special Ref. Code</button></a>';
                if (!is_null($row->specialRefCode)) {
                    $moreColumn .= '<span style="background-color: #449d44; color: #ffffff;">Special Ref. Code: ' . $row->specialRefCode . '</span>';
                }

                if (auth()->guard('admin')->check()) {

                    if ($row->extra_files()->count() > 0) {
                        $b_class = "light_green";
                    } else {
                        $b_class = "light_red";
                    }

                    if ($row->contacts()->count() > 0) {
                        $c_class = "light_green";
                    } else {
                        $c_class = "light_red";
                    }


                    $moreColumn .= '<a href="#"><button data-toggle="modal" data-target="#file-import-modal" type="button" class="btn btn-xs btn-primary fire-booking-file-import-button ' . $b_class . '" data-id="' . $row->id . '" style="width: 58px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Import</button>

                    <button data-toggle="modal" data-target="#customer-contact-modal" type="button" class="btn btn-xs btn-primary fire-booking-customer-contact-button ' . $c_class . '" data-id="' . $row->id . '" style="width: 58px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Contact</button>

                    </a>';
                }

                if ($row->check()->count()) {

                    $moreColumn .= '<button data-toggle="modal" data-target="#access-checkins-modal" data-id="' . $row->id . '" type="submit" class="btn btn-xs btn-primary btn-block access-checkins-button light_green" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Checkins</button>';

                } else {

                    $moreColumn .= '<button data-toggle="modal" data-target="#access-checkins-modal" data-id="' . $row->id . '" type="submit" class="btn btn-xs btn-primary btn-block access-checkins-button light_red" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; border-color: #8852E4; font-size: 10px; font-weight: bold;">Checkins</button>';

                }


                // $moreColumn .= '<a href="' . url('/booking/' . $row->id . '/edit') . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            }

            $data[] = [
                "date" => $dateColumn,
                "tour" => $tourColumn,
                "bookingRef" => $bookingRefColumn,
                "status" => $statusColumn,
                "rCode" => $rCodeColumn,
                "salesInformations" => $salesColumn,
                "more" => $moreColumn,
                "invoice_status" => $row->invoice_check
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForAdminlog($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::whereHas('admin', function ($q) use ($searchValue) {
            $q->where('name', 'like', '%' . $searchValue . '%');
        })
            ->orWhere('productRefCode', 'like', '%' . $searchValue . '%')
            ->orWhere('optionRefCode', 'like', '%' . $searchValue . '%')
            ->orWhere('action', 'like', '%' . $searchValue . '%')
            ->orWhere('details', 'like', '%' . $searchValue . '%')
            ->orWhere('created_at', 'like', '%' . $searchValue . '%')
            ->count();

        // Fetch records
        $records = $model->whereHas('admin', function ($q) use ($searchValue) {
            $q->where('name', 'like', '%' . $searchValue . '%');
        })
            ->orWhere('productRefCode', 'like', '%' . $searchValue . '%')
            ->orWhere('optionRefCode', 'like', '%' . $searchValue . '%')
            ->orWhere('action', 'like', '%' . $searchValue . '%')
            ->orWhere('details', 'like', '%' . $searchValue . '%')
            ->orWhere('created_at', 'like', '%' . $searchValue . '%')
            ->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $name = '';
            if (!is_null($row->admin)) {
                $name = $row->admin->name;
            } else {
                $name = $row->supplier->contactName;
            }
            $data[] = [
                "name" => $name,
                "productRefCode" => $row->productRefCode,
                "optionRefCode" => $row->optionRefCode,
                "page" => $row->page,
                "action" => $row->action,
                "details" => $row->details,
                "date" => $row->created_at->format('d/m/Y H:i:s'),
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForApilog($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];

        $getAvailabilities = $request->getAvailabilities;
        $reserve = $request->reserve;
        $cancelReservation = $request->cancelReservation;
        $book = $request->book;
        $cancelBooking = $request->cancelBooking;
        $notifyPush = $request->notifyPush;

        // Total number of record with filtering
        $totalRecordwithFilter = $model::where(function ($query) use ($searchValue) {
            $query->where('request', 'like', '%' . $searchValue . '%')
                ->orWhere('query', 'like', '%' . $searchValue . '%')
                ->orWhere('server', 'like', '%' . $searchValue . '%')
                ->orWhere('headers', 'like', '%' . $searchValue . '%')
                ->orWhere('path', 'like', '%' . $searchValue . '%')
                ->orWhere('fullPath', 'like', '%' . $searchValue . '%')
                ->orWhere('fromDateTime', 'like', '%' . $searchValue . '%')
                ->orWhere('toDateTime', 'like', '%' . $searchValue . '%')
                ->orWhere('productId', 'like', '%' . $searchValue . '%')
                ->orWhere('created_at', 'like', '%' . $searchValue . '%');
        });
        $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($getAvailabilities, $reserve, $cancelReservation, $book, $cancelBooking, $notifyPush) {
            if ($getAvailabilities == '1') {
                $query->where('fullPath', 'like', '%get-availabilities%');
            }
            if ($reserve == '1') {
                $query->orWhere('fullPath', 'like', '%reserve%');
            }
            if ($cancelReservation == '1') {
                $query->orWhere('fullPath', 'like', '%cancel-reservation%');
            }
            if ($book == '1') {
                $query->orWhere('fullPath', 'like', '%book');
            }
            if ($cancelBooking == '1') {
                $query->orWhere('fullPath', 'like', '%cancel-booking%');
            }
            if ($notifyPush == '1') {
                $query->orWhere('fullPath', 'like', '%notify-availability-update%');
            }
        });
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model::where(function ($query) use ($searchValue) {
            $query->where('request', 'like', '%' . $searchValue . '%')
                ->orWhere('query', 'like', '%' . $searchValue . '%')
                ->orWhere('server', 'like', '%' . $searchValue . '%')
                ->orWhere('headers', 'like', '%' . $searchValue . '%')
                ->orWhere('path', 'like', '%' . $searchValue . '%')
                ->orWhere('fullPath', 'like', '%' . $searchValue . '%')
                ->orWhere('fromDateTime', 'like', '%' . $searchValue . '%')
                ->orWhere('toDateTime', 'like', '%' . $searchValue . '%')
                ->orWhere('productId', 'like', '%' . $searchValue . '%')
                ->orWhere('created_at', 'like', '%' . $searchValue . '%');
        });
        $records = $records->where(function ($query) use ($getAvailabilities, $reserve, $cancelReservation, $book, $cancelBooking, $notifyPush) {
            if ($getAvailabilities == '1') {
                $query->where('fullPath', 'like', '%get-availabilities%');
            }
            if ($reserve == '1') {
                $query->orWhere('fullPath', 'like', '%reserve%');
            }
            if ($cancelReservation == '1') {
                $query->orWhere('fullPath', 'like', '%cancel-reservation%');
            }
            if ($book == '1') {
                $query->orWhere('fullPath', 'like', '%book');
            }
            if ($cancelBooking == '1') {
                $query->orWhere('fullPath', 'like', '%cancel-booking%');
            }
            if ($notifyPush == '1') {
                $query->orWhere('fullPath', 'like', '%notify-availability-update%');
            }
        });
        $records = $records->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $fullPath = $row->fullPath;
            $requestType = '';
            $query = $row->query;
            $queryDecoded = json_decode($row->query, true);
            $requestStr = $row->request;
            $requestDecoded = json_decode($row->request, true);
            if (strpos($fullPath, 'get-availabilities') != false) {
                $requestType = 'Get Availabilities';
                $query = 'Option Ref. Code: ' . $queryDecoded['productId'] . '<br> From DateTime: ' . $queryDecoded['fromDateTime'] . '<br> To DateTime: ' . $queryDecoded['toDateTime'];
                $requestStr = 'Option Ref. Code: ' . $requestDecoded['productId'] . '<br> From DateTime: ' . $requestDecoded['fromDateTime'] . '<br> To DateTime: ' . $requestDecoded['toDateTime'];
            }
            if (strpos($fullPath, 'notify-availability-update') != false) {
                $requestType = 'Notify Availability Update';
                $query = 'Option Ref. Code: ' . $queryDecoded['data']['productId'] . '<br> From DateTime: ' . $queryDecoded['data']['fromDateTime'] . '<br> To DateTime: ' . $queryDecoded['data']['toDateTime'];
                $requestStr = 'Message: ' . $requestDecoded['data']['message'];
            }
            if (strpos($fullPath, 'reserve') != false) {
                $requestType = 'Reserve';
                $query = 'Query is empty by default';
                $requestData = $requestDecoded['data'];
                $requestStr = 'Option Ref. Code: ' . $requestData['productId'] . '<br> ';
                $requestStr .= 'DateTime: ' . $requestData['dateTime'] . '<br> ';
                $requestStr .= 'Booking Items: ' . '<br> ';
                foreach ($requestData['bookingItems'] as $item) {
                    $requestStr .= $item['count'] . ' ' . $item['category'] . '<br> ';
                }
                $requestStr .= 'GYG Booking Reference: ' . $requestData['gygBookingReference'] . '<br> ';
                $requestStr .= 'GYG Activity Reference: ' . $requestData['gygActivityReference'] . '<br> ';
            }
            if (strpos($fullPath, 'cancel-reservation') != false) {
                $requestType = 'Cancel Reservation';
                $query = 'Query is empty by default';
                $requestStr = 'Reservation Reference: ' . $requestDecoded['data']['reservationReference'] . '<br> GYG Booking Reference: ' . $requestDecoded['data']['gygBookingReference'] . '<br> GYG Activity Reference: ' . $requestDecoded['data']['gygActivityReference'];
            }
            if (strpos($fullPath, 'book') != false) {
                $requestType = 'Book';
                $query = 'Query is empty by default';
                $requestData = $requestDecoded['data'];
                $requestStr = 'Option Ref. Code: ' . $requestData['productId'] . '<br> ';
                $requestStr .= 'DateTime: ' . $requestData['dateTime'] . '<br> ';
                $requestStr .= 'Reservation Reference: ' . $requestData['reservationReference'] . '<br> ';
                $requestStr .= 'GYG Booking Reference: ' . $requestData['gygBookingReference'] . '<br> ';
                $requestStr .= 'Booking Items:' . '<br> ';
                foreach ($requestData['bookingItems'] as $item) {
                    $requestStr .= $item['count'] . ' ' . $item['category'] . '<br>';
                }
                $requestStr .= 'Travelers: ' . '<br> ';
                foreach ($requestData['travelers'] as $traveler) {
                    $requestStr .= 'First Name: ' . $traveler['firstName'] . '<br>';
                    $requestStr .= 'Last Name: ' . $traveler['lastName'] . '<br>';
                    $requestStr .= 'E-Mail: ' . $traveler['email'] . '<br>';
                    $requestStr .= 'Phone Number: ' . $traveler['phoneNumber'] . '<br>';
                }
                $requestStr .= 'Traveler Hotel: ' . $requestData['travelerHotel'] . '<br>';
                $requestStr .= 'Comment: ' . $requestData['comment'] . '<br>';
                $requestStr .= 'GYG Activity Reference: ' . $requestData['gygActivityReference'];
            }
            if (strpos($fullPath, 'cancel-booking') != false) {
                $requestType = 'Cancel Booking';
                $query = 'Query is empty by default';
                $requestData = $requestDecoded['data'];
                $requestStr .= 'Option Ref. Code: ' . $requestData['productId'] . '<br> ';
                $requestStr .= 'Booking Reference: ' . $requestData['bookingReference'] . '<br> ';
                $requestStr .= 'GYG Booking Reference: ' . $requestData['gygBookingReference'] . '<br> ';
                $requestStr .= 'GYG Activity Reference: ' . $requestData['gygActivityReference'] . '<br> ';
            }
            $data[] = [
                "requestType" => $requestType,
                "query" => $query,
                "request" => $requestStr,
                "optionRefCode" => $row->productId,
                "requestTime" => $row->created_at->format('d/m/Y H:i:s'),
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForSupplier($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];

        $country = $request->country;

        // Total number of record with filtering
        $totalRecordwithFilter = $model::where(function ($query) use ($searchValue) {
            $query->where('companyName', 'like', '%' . $searchValue . '%')
                ->orWhere('contactName', 'like', '%' . $searchValue . '%')
                ->orWhere('contactSurname', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%')
                ->orWhere('city', 'like', '%' . $searchValue . '%');
        });
        $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($country) {
            if ($country != '') {
                $query->where('country', $country);
            }
        });
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model::where(function ($query) use ($searchValue) {
            $query->where('companyName', 'like', '%' . $searchValue . '%')
                ->orWhere('contactName', 'like', '%' . $searchValue . '%')
                ->orWhere('contactSurname', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%')
                ->orWhere('city', 'like', '%' . $searchValue . '%');
        });
        $records = $records->where(function ($query) use ($country) {
            if ($country != '') {
                $query->where('country', $country);
            }
        });
        $records = $records->orderBy('id', 'asc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $companyName = $row->companyName;
            $contact = '<span class="list-enq-name">' . $row->contactName . ' ' . $row->contactSurname . '</span>';
            $email = $row->email;
            $supCountry = is_null($row->country) ? '' : $row->countryName->countries_name;
            $city = $row->city;
            $isRestaurant = '';
            if (auth()->guard('admin')->check()) {
                $isRestaurant .= '<p>';
                if ($row->isRestaurant == 1) {
                    $isRestaurant .= '<span class="db-done">Yes</span>';
                    $isRestaurant .= '<p><a class="modalOpen" data-supplier-id="' . $row->id . '" href="#ex1" rel="modal:open">Choose Option</a></p>';
                } else {
                    $isRestaurant .= '<span class="db-not-done">No</span>';
                }
                $isRestaurant .= '</p>';
            } else {
                $isRestaurant .= '<p>';
                $isRestaurant .= '<a class="modalOpen" data-supplier-id="' . $row->id . '" href="#ex1" rel="modal:open">';
                $isRestaurant .= 'Open Modal';
                $isRestaurant .= '</a>';
                $isRestaurant .= '</p>';
            }
            $status = '';
            if (auth()->guard('admin')->check()) {
                $status = '<input data-id="' . $row->id . '" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="Passive"';
                if ($row->isActive == 1) {
                    $status .= ' checked ';
                }
                $status .= '>';
            }
            $edit = '';
            $licenses = '';
            $delete = '';
            if (auth()->guard('admin')->check()) {
                $edit = '<a href="/supplier/' . $row->id . '/edit"><i class="icon-cz-edit" aria-hidden="true"></i></a>';
                $licenses = '<a href="/supplier/' . $row->id . '/details"><i class="icon-cz-copy"></i></a>';
//                $delete = '<a href="/supplier/'.$row->id.'/destroy"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></a>';
            }
            $data[] = [
                "companyName" => $companyName,
                "contact" => $contact,
                "email" => $email,
                "country" => $supCountry,
                "city" => $city,
                "isRestaurant" => $isRestaurant,
                "status" => $status,
                "edit" => $edit,
                "licenses" => $licenses,
                "delete" => $delete,
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForBookingLog($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('code', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model::where('code', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $data[] = [
                "processID" => $row->processID,
                "userID" => $row->userID,
                "optionTitle" => $row->option->title,
                "cartID" => $row->cartID,
                "code" => $row->code,
                "paymentDate" => $row->created_at,
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForMeetingLog($model, $searchValue, $row, $rowperpage)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('action', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model::where('action', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $effId = json_decode($row->affected_id);
            $adminName = '';
            if ($effId[0] != null && Admin::where('id', intval($effId[0]))->exists()) {
                $admins = Admin::where('id', intval($effId[0]))->first();
                $adminName = $admins->name . ' ' . $admins->surname;
            }
            $meeting = Meeting::where('id', intval($row->meeting_id))->first();
            $data[] = [
                "processID" => $row->id,
                "meeting-date" => $meeting->date,
                "meeting-time" => json_decode($meeting->operating_hours)[0]->hour,
                "meeting-option" => $meeting->option,
                "logger_id" => $row->logger_id,
                "logger_email" => $row->logger_email,
                "affected_name" => $adminName,
                //"affected_email" => $row->affected_email,
                "action" => $row->action,
                "date" => $row->created_at->format('d-m-Y H:i'),
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }


    public function getDataForCustomerLog($model, $searchValue, $row, $rowperpage)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::where(function ($query) use ($searchValue) {
            $query->where('id', 'like', '%' . $searchValue . '%')
                ->orWhere('booking_id', 'like', '%' . $searchValue . '%')
                ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                ->orWhere('customerEmail', 'like', '%' . $searchValue . '%')
                ->orWhere('customerName', 'like', '%' . $searchValue . '%')
                ->orWhere('action', 'like', '%' . $searchValue . '%')
                ->orWhereHas('booking', function ($subQ4Booking) use ($searchValue) {
                    $subQ4Booking->whereHas('bookingOption', function ($subQ4Option) use ($searchValue) {
                        $subQ4Option->where('title', 'like', '%' . $searchValue . '%');
                    });
                });
        })->get()->groupBy('customerEmail')->count();

        // Fetch records
        $records = $model::where(function ($query) use ($searchValue) {
            $query->where('id', 'like', '%' . $searchValue . '%')
                ->orWhere('booking_id', 'like', '%' . $searchValue . '%')
                ->orWhere('referenceCode', 'like', '%' . $searchValue . '%')
                ->orWhere('customerEmail', 'like', '%' . $searchValue . '%')
                ->orWhere('customerName', 'like', '%' . $searchValue . '%')
                ->orWhere('action', 'like', '%' . $searchValue . '%')
                ->orWhereHas('booking', function ($subQ4Booking) use ($searchValue) {
                    $subQ4Booking->whereHas('bookingOption', function ($subQ4Option) use ($searchValue) {
                        $subQ4Option->where('title', 'like', '%' . $searchValue . '%');
                    });
                });
        })->orderBy('id', 'desc')->groupBy('customerEmail')->select('*', DB::raw('count(*) as total'))->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $data[] = [
                "processID" => $row->id,
                "bookingID" => $row->booking_id,
                "referenceCode" => $row->referenceCode,
                "customerEmail" => $row->customerEmail,
                "customerName" => $row->customerName,
                "option" => Booking::find($row->booking_id)->bookingOption->title,
                "action" => $row->action,
                "date" => $row->created_at->format('d-m-Y H:i'),
                "total" => '<div style="display: flex; justify-content: space-evenly"><span class="db-done">' . $row->total . '</span><i style="background-color: #5bc0de; cursor: pointer;" class="fetchCustomerLogs icon-cz-rocket" data-email=' . $row->customerEmail . ' data-toggle="modal" data-target="#customerLogsModal"></i></div>'

            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @param $request
     * @return array
     */
    public function getDataForComment($model, $searchValue, $row, $rowperpage, $request)
    {
        $model = $model->with('product');
        $country = $request->country;
        $attraction = $request->attraction;
        $one = $request->one;
        $two = $request->two;
        $three = $request->three;
        $four = $request->four;
        $five = $request->five;
        $confirmed = $request->confirmed;
        $notConfirmed = $request->notConfirmed;

        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        if ($request->has('supplierId')) {
            $ownerID = $request->supplierId;
        }

        if ($request->has('one')) {
            $one = $request->one;
        }
        if ($request->has('two')) {
            $two = $request->two;
        }
        if ($request->has('three')) {
            $three = $request->three;
        }
        if ($request->has('four')) {
            $four = $request->four;
        }
        if ($request->has('five')) {
            $five = $request->five;
        }
        if ($request->has('confirmed')) {
            $confirmed = $request->confirmed;
        }
        if ($request->has('notConfirmed')) {
            $notConfirmed = $request->notConfirmed;
        }

        $data = [];

        // Total number of record with filtering for admin
        $totalRecordwithFilter = $model->where(function ($query) use ($searchValue) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('description', 'like', '%' . $searchValue . '%')
                ->orWhere('username', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%');
        });

        if ($ownerID) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereHas('product', function ($q) use ($ownerID) {
                $q->where('supplierID', $ownerID);
            });

        }

        if (auth()->guard('supplier')->check()) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereHas('product', function ($q) use ($ownerID) {
                $q->where('supplierID', $ownerID);
            });
        }


        if (!is_null($country)) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereHas('product', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if (!is_null($attraction)) {
            $totalRecordwithFilter = $totalRecordwithFilter->whereHas('product', function ($q) use ($attraction) {
                $q->where('attractions', 'like', '%"' . $attraction . '"%');
            });
        }
        if ($one == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('rate', '!=', 1);
        }
        if ($two == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('rate', '!=', 2);
        }
        if ($three == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('rate', '!=', 3);
        }
        if ($four == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('rate', '!=', 4);
        }
        if ($five == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('rate', '!=', 5);
        }
        if ($confirmed == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('status', '!=', 1);
        }
        if ($notConfirmed == '0') {
            $totalRecordwithFilter = $totalRecordwithFilter->where('status', '!=', 0);
        }
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        $records = $model->where(function ($query) use ($searchValue) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('description', 'like', '%' . $searchValue . '%')
                ->orWhere('username', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%');
        });

        if (auth()->guard('supplier')->check()) {
            $records = $records->whereHas('product', function ($q) use ($ownerID) {
                $q->where('supplierID', $ownerID);
            });
        }

        if (!is_null($country)) {
            $records = $records->whereHas('product', function ($q) use ($country) {
                $q->where('country', $country);
            });
        }
        if (!is_null($attraction)) {
            $records = $records->whereHas('product', function ($q) use ($attraction) {
                $q->where('attractions', 'like', '%"' . $attraction . '"%');
            });
        }
        if ($one == '0') {
            $records = $records->where('rate', '!=', 1);
        }
        if ($two == '0') {
            $records = $records->where('rate', '!=', 2);
        }
        if ($three == '0') {
            $records = $records->where('rate', '!=', 3);
        }
        if ($four == '0') {
            $records = $records->where('rate', '!=', 4);
        }
        if ($five == '0') {
            $records = $records->where('rate', '!=', 5);
        }
        if ($confirmed == '0') {
            $records = $records->where('status', '!=', 1);
        }
        if ($notConfirmed == '0') {
            $records = $records->where('status', '!=', 0);
        }
        $records = $records->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $statusChecked = $row->status == 1 ? 'checked' : '';
            if ($ownerID == -1) {
                $statusColumn = '<input data-id="' . $row->id . '" class="toggle-class5" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Confirmed" data-off="Not Confirmed" ' . $statusChecked . '>';
            } else {
                $statusColumn = $row->status == 1 ? '<span style="padding: 20px; color: #ffffff; background-color: #0e76a8;">Confirmed</span>' : '<span style="padding: 20px; color: #ffffff; background-color: #ac2925;">Not Confirmed</span>';
            }
            $actionColumn = '<a href="/comments/' . $row->id . '/delete?type=cz"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></a>';

            try {
                $data[] = [
                    "productRefCode" => $row->product->referenceCode,
                    "productName" => $row->product->title,
                    "userName" => $row->username,
                    "title" => $row->title,
                    "description" => $row->description,
                    "rate" => $row->rate,
                    "created_at" => date('d/m/Y', strtotime($row->created_at)),
                    "status" => $statusColumn,
                    "action" => $actionColumn,
                ];
            } catch (\Exception $exception) {

            }
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @return array
     */
    public function getDataForBarcode($model, $searchValue, $row, $rowperpage)
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }

        $data = [];

        // Total number of record with filtering for admin
        $totalRecordwithFilter = $model->where('ownerID', $ownerID)->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%')
                ->orWhere('description', 'like', '%' . $searchValue . '%')
                ->orWhere('endTime', 'like', '%' . $searchValue . '%')
                ->orWhere('searchableTicketType', 'like', '%' . $searchValue . '%');
        });

        $records = $model->where('ownerID', $ownerID)->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%')
                ->orWhere('description', 'like', '%' . $searchValue . '%')
                ->orWhere('endTime', 'like', '%' . $searchValue . '%')
                ->orWhere('searchableTicketType', 'like', '%' . $searchValue . '%');
        });
        $records = $records->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        $totalRecordwithFilter = $totalRecordwithFilter->count();

        foreach ($records as $row) {
            $statusChecked = $row->isUsed == 1 ? 'checked' : '';
            $downloadMultipleButton = '';
            if ($row->ticketTypeName->name == 'Cruise Ticket' || $row->ticketTypeName->name == 'Versailles Ticket') {
                $url = url('single-pdf/' . $row->id);
                if ($row->isUsed == 0 && $row->isReserved == 0) {
                    $actionButton = '<button class="createTicket btn btn-primary" type="submit" style="background-color: forestgreen">Create Ticket</button>';
                } else {
                    $actionButton = '<button class="createTicket btn btn-primary" type="submit">Download Again</button>';
                    //$desc = decode($row->description);
                    if (Barcode::whereNotNull('description')->whereNotNull('booking_date')->where('description', $row->description)->where('booking_date', $row->booking_date)->count() > 1) {
                        $formUrl = url('/multiple-tickets-on-index');
                        $downloadMultipleButton = '<form action=' . $formUrl . ' enctype="multipart/form-data" method="POST" id="multiple-ticket-form" style="display: inline-block">';
                        $downloadMultipleButton .= '<input type="hidden" name="_token" value="' . csrf_token() . '" >';
                        $downloadMultipleButton .= '<input type="hidden" name="barcodeDescription" value="' . $row->description . '">';
                        $downloadMultipleButton .= '<input type="hidden" name="bookingDate" value="' . $row->booking_date . '"  >';
                        $downloadMultipleButton .= '<button class="btn btn-primary" style="margin-left: 10px; background-color:#2d4373;" type="submit">Download Multiple</button>';
                        $downloadMultipleButton .= '</form>';
                    }
                }
            } else {
                $url = '';
                $actionButton = '';
            }


            $barcodeTypeColumn = TicketType::findOrFail($row->ticketType)->name;
            $actionColumn = '<a href="' . $url . '">' . $actionButton . '</a>';
            if ($downloadMultipleButton != '')
                $actionColumn .= $downloadMultipleButton;

            if ($row->isUsed == 0) {
                $actionColumn .= '<button style="margin-left: 10px;" class="btn btn-danger active" onclick="removeBarcode($(this), ' . $row->id . ')">Remove</button>';
            }

            if ($ownerID == -1) {
                $statusColumn = '<input data-id="' . $row->id . '" class="toggle-class-isUsed" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Used" data-off="Not Used" ' . $statusChecked . '>';
            } else {
                $statusColumn = '<input disabled data-id="' . $row->id . '" class="toggle-class-isUsed" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Used" data-off="Not Used" ' . $statusChecked . '>';
            }

            if ($row->isExpired) {
                $statusColumn = '<input disabled data-id="' . $row->id . '" class="toggle-class-isUsed" disabled type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Used" data-off="Expired" ' . $statusChecked . '>';
            }

            if (!empty($row->booking)) {
                $booking = $row->booking;
                $bookingRefCode = $booking->bookingRefCode;
                $bookingRefCodeArray = explode("-", $bookingRefCode);
                $bknCode = \end($bookingRefCodeArray);

            }

            if (!empty($bknCode)) {
                $desc = $bknCode;
            } elseif (!is_null($row->description)) {
                $desc = $row->description;
            } else {
                $desc = "No Description";
            }

            if (!is_null($row->log)) {
                $log = json_decode($row->log, true);
                $oldBooking = Booking::where('id', $log[0]['oldBookingID'])->first();
                if ($oldBooking) {
                    $log[0]['bknCode'] = explode('-', $oldBooking->bookingRefCode)[count(explode('-', $oldBooking->bookingRefCode)) - 1];
                } else {
                    $log[0]['bknCode'] = '-';
                }

                $log[0]['cancelReason'] = str_replace(' ', '&nbsp', $log[0]['cancelReason']);
                $log = json_encode($log);
            }

            $data[] = [
                'isExpired' => $row->isExpired,
                'barcode' => $row->code,
                'barcodeType' => $barcodeTypeColumn,
                'status' => $statusColumn,
                'description' => $desc,
                'endtime' => $row->endTime,
                'usedDate' => $row->isUsed == 1 ? $row->updated_at->format('d/m/Y H:i') : 'Not Used',
                'actions' => $actionColumn,
                'info' => !is_null($row->log) ? "<i data-toggle='modal' data-target='#logModal' style='color:#ff0000; font-size:17px; cursor:pointer;' class='log-info icon-cz-bell' data-log=" . $log . "></i>" : ""
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param $model
     * @param $searchValue
     * @param $row
     * @param $rowperpage
     * @return array
     */
    public function getDataForErrorLog($model, $searchValue, $row, $rowperpage)
    {
        $data = [];
        // Total number of record with filtering
        $totalRecordwithFilter = $model::where('fullUrl', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $model::where('fullUrl', 'like', '%' . $searchValue . '%')->orderBy('id', 'desc')->skip($row)->take($rowperpage)->get();

        foreach ($records as $row) {
            $data[] = [
                'url' => $row->fullUrl,
                'code' => $row->code,
                'file' => $row->file,
                'line' => $row->line,
                'message' => $row->message,
                'dateTime' => date('d-m-Y H:i', strtotime($row->created_at)),
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function pageIDForDataTable(Request $request)
    {
        $pageID = is_null($request->pageID) ? '1' : $request->pageID;
        return $pageID;
    }


    protected function hiddenWithAsterix($book, $data)
    {
        if ($book->check()->count()) {
            return $data;
        }
        return substr($data, 0, -3) . '***';

    }

    public function getDataForOnGoings($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];

        $product = $request->product;
        $option = $request->option;

        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('status', 0)->orderBy('id', 'DESC');
        $totalRecordwithFilter = $totalRecordwithFilter->where(function ($query) use ($option) {
            if (is_array($option) && count($option) > 0) {
                $query->whereIn('optionID', $option);
            }
        });
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model->where('status', 0)->orderBy('id', 'DESC');
        $records = $records->where(function ($query) use ($option) {
            if (is_array($option) && count($option) > 0) {
                $query->whereIn('optionID', $option);
            }
        });
        $records = $records->orderBy('id', 'asc')->skip($row)->take($rowperpage)->get();

        $apiRelated = new \App\Http\Controllers\Helpers\ApiRelated();

        foreach ($records as $row) {
            $bookingItems = $apiRelated->getCategoryAndCountInfo($row->bookingItems);
            $createdAt = Carbon::parse($row->created_at)->format('d/m/Y H:i');

            $productTitle = \App\Product::where('id', $row->productID)->first();
            $productTitle = $productTitle ? ($productTitle->title ?? '-') : '-';

            $optionTitle = \App\Option::where('id', $row->optionID)->first();
            $optionTitle = $optionTitle ? ($optionTitle->title ?? '-') : '-';

            $totalPrice = $row->totalPrice ?? '-';
            $from = $row->isGYG != 1 ? ($row->isBokun != 1 ? ($row->isViator != 1 ? 'Cityzore' : 'Viator') : 'Bokun') : 'GYG';
            if ($row->dateTime) {
                if ($this->isJson($row->dateTime)) {
                    $dateTime = Carbon::parse(json_decode($row->dateTime, true)["dateTime"])->format('d/m/Y H:i');
                } else {
                    $dateTime = Carbon::parse($row->dateTime)->format('d/m/Y H:i');
                }
            } else {
                $dateTime = $row->date;
                $hours = json_decode($row->hour, true);
                foreach ($hours as $hour) {
                    $dateTime .= " " . $hour["hour"];
                }
            }

            $data[] = [
                "bookingItems" => $bookingItems,
                "createdAt" => $createdAt,
                "productTitle" => $productTitle,
                "optionTitle" => $optionTitle,
                "totalPrice" => $totalPrice . " ???",
                "from" => $from,
                "dateTime" => $dateTime,
            ];
        }
        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    public function getDataForMails($model, $searchValue, $row, $rowperpage, $request)
    {
        $data = [];
        $typeSelect = $request->get('typeSelect');
        $from = '';
        $to = '';

        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;
        }

        // Total number of record with filtering
        $totalRecordwithFilter = $model->where('to', '!=', 'contact@parisviptrips.com')->where(function ($query) use ($searchValue) {
            $query->where('id', 'like', '%' . $searchValue . '%')
                ->orWhere('to', 'like', '%' . $searchValue . '%')
                ->orWhere('data', 'like', '%' . $searchValue . '%')
                ->orWhere('blade', 'like', '%' . $searchValue . '%');
        });
        if ($typeSelect != '' || $typeSelect != null) {
            if ($typeSelect == 'mail.booking-successful')
                $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) use ($typeSelect) {
                    $q->where('blade', $typeSelect)->orWhere('blade', 'mail.api-booking-successful');
                });
            elseif ($typeSelect == 'mail.booking-cancel')
                $totalRecordwithFilter = $totalRecordwithFilter->where(function ($q) use ($typeSelect) {
                    $q->where('blade', $typeSelect)->orWhere('blade', 'mail.api-booking-cancel');
                });
            else
                $totalRecordwithFilter = $totalRecordwithFilter->where('blade', $typeSelect);
        }
        if ($from != '' && $to != '') {
            $totalRecordwithFilter = $totalRecordwithFilter->whereBetween(DB::raw('DATE(updated_at)'), [date($from), date($to)]);
        }
        $totalRecordwithFilter = $totalRecordwithFilter->count();

        // Fetch records
        $records = $model->where('to', '!=', 'contact@parisviptrips.com')->where(function ($query) use ($searchValue) {
            $query->where('id', 'like', '%' . $searchValue . '%')
                ->orWhere('to', 'like', '%' . $searchValue . '%')
                ->orWhere('data', 'like', '%' . $searchValue . '%')
                ->orWhere('blade', 'like', '%' . $searchValue . '%');
        })->orderBy('id', 'DESC');
        if ($typeSelect != '' || $typeSelect != null) {
            if ($typeSelect == 'mail.booking-successful')
                $records = $records->where(function ($q) use ($typeSelect) {
                    $q->where('blade', $typeSelect)->orWhere('blade', 'mail.api-booking-successful');
                });
            elseif ($typeSelect == 'mail.booking-cancel')
                $records = $records->where(function ($q) use ($typeSelect) {
                    $q->where('blade', $typeSelect)->orWhere('blade', 'mail.api-booking-cancel');
                });
            else
                $records = $records->where('blade', $typeSelect);
        }
        if ($from != '' && $to != '') {
            $records = $records->whereBetween(DB::raw('DATE(updated_at)'), [date($from), date($to)]);
        }
        $records = $records->skip($row)->take($rowperpage)->get();
        foreach ($records as $row) {
            $data[] = [
                "index" => $row->id,
                "to" => $row->to,
                "data" => json_decode($row->data, true)[0]["subject"] ?? '-',
                "type" => explode('.', $row->blade)[1],
                "date" => Carbon::parse($row->updated_at)->format('d/m/Y H:i'),
                "status" => $row->status == 0 ? '<span class="db-not-done">Not Sent</span>' : '<span class="db-done">Sent</span>'
            ];
        }

        return ['totalRecordwithFilter' => $totalRecordwithFilter, 'data' => $data];
    }

    public function isJson($str)
    {
        $json = json_decode($str);
        return $json && $str != $json;
    }

}
