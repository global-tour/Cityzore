<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Supplier;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ProductsExport implements FromCollection, WithHeadings
{
    public $productsRequest;

    public function __construct($productsRequest) {
        $this->productsRequest = $productsRequest;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $queryModel = new Product();
        $country = $this->productsRequest->country;
        $city = $this->productsRequest->city;
        $attraction = $this->productsRequest->attraction;
        $category = $this->productsRequest->category;
        $supplier = $this->productsRequest->supplier;
        $published = $this->productsRequest->published;
        $notPublished = $this->productsRequest->notPublished;
        $pendingApproval = $this->productsRequest->pendingApproval;
        $orderBy = $this->productsRequest->orderBy;
        $specialOffer = $this->productsRequest->specialOffer;

        $attractionModel = new \App\Attraction();

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

        $queryModel = Product::with('copies')->where('supplierID', $ownerID)->where('isSpecial', '!=', 1)->where('copyOf', '=', null);

        if ($country != '' && $country != "null" && $country != null) {
            $queryModel = $queryModel->where('country', $country);
        }
        if ($city != '' && $city != "null" && $city != null) {
            $queryModel = $queryModel->where('city', $city);
        }
        if ($attraction != '' && $attraction != "null" && $attraction != null) {
            $queryModel = $queryModel->where('attractions', 'like', '%'.$attraction.'%');
        }
        if ($category != '' && $category != "null" && $category != null) {
            $queryModel = $queryModel->where('category', $category);
        }

        $queryModel = $queryModel->where(function($q) use($published, $notPublished, $pendingApproval){
          $q->where(function($sub) use($published, $notPublished, $pendingApproval){
              if ($published == '1') {
                $sub->where('isPublished', 1);
            }
          });
          $q->orWhere(function($sub) use($published, $notPublished, $pendingApproval){

             if ($notPublished == '1') {
                $sub->where('isPublished', 0);
            }

          });
          $q->orWhere(function($sub) use($published, $notPublished, $pendingApproval){

             if ($pendingApproval == '1') {
                $sub->where('isDraft', 1);
            }

          });

        });

        if($specialOffer == '1') {
            $queryModel = $queryModel->has("specialOffers");
        }
        if ($orderBy == 'newest') {
            $queryModel = $queryModel->orderBy('updated_at', 'desc');
        }
        if ($orderBy == 'oldest') {
            $queryModel = $queryModel->orderBy('updated_at', 'asc');
        }
        if ($orderBy == 'titleAsc') {
            $queryModel = $queryModel->orderBy('title', 'ASC');
        }
        if ($orderBy == 'titleDesc') {
            $queryModel = $queryModel->orderBy('title', 'DESC');
        }
        if ($orderBy == 'idAsc') {
            $queryModel = $queryModel->orderBy('id', 'ASC');
        }
        if ($orderBy == 'idDesc') {
            $queryModel = $queryModel->orderBy('id', 'DESC');
        }

        $products = $queryModel->get();
        $excelArr = [];

        foreach($products as $product) {
            $options = $product->options()->get();
            foreach($options as $option) {
                $availabilities = $option->avs;
                $pricings = $option->pricings()->get();
                foreach($availabilities as $availability) {
                    $avdates = $availability->avdates()->get();
                    $rangeFrom = "";
                    $rangeTo = "";
                    foreach($avdates as $avdate) {
                        if($rangeFrom == "")
                            $rangeFrom = Carbon::createFromFormat('Y-m-d', $avdate->valid_from);
                        if($rangeTo == "")
                            $rangeTo = Carbon::createFromFormat('Y-m-d', $avdate->valid_to);

                        if(Carbon::createFromFormat('Y-m-d', $avdate->valid_from) < $rangeFrom)
                            $rangeFrom = Carbon::createFromFormat('Y-m-d', $avdate->valid_from);
                        if(Carbon::createFromFormat('Y-m-d', $avdate->valid_to) > $rangeTo)
                            $rangeTo = Carbon::createFromFormat('Y-m-d', $avdate->valid_to);
                    }

                    $productExcelArr = [];

                    $productExcelArr[0] = $product->referenceCode ? $product->referenceCode : "-";
                    $productExcelArr[1] = $product->title ? $product->title : "-";
                    $productExcelArr[2] = $product->country ? json_decode($product->countryName)->countries_name : "-";
                    $productExcelArr[3] = $product->city ? $product->city : "-";
                    $productExcelArr[4] = $product->attractions ? $attractionModel->getAttractionNames($product->attractions) : "-";
                    $productExcelArr[5] = $product->highlights ? $product->highlights : "-";
                    $productExcelArr[6] = $product->included ? $product->included : "-";
                    $productExcelArr[7] = $product->notIncluded ? $product->notIncluded : "-";
                    $productExcelArr[8] = $product->knowBeforeYouGo ? $product->knowBeforeYouGo : "-";
                    $productExcelArr[9] = $product->category ? $product->category : "-";
                     
                    $productExcelArr[10] = $option->referenceCode ? $option->referenceCode : "-";
                    $productExcelArr[11] = $option->title ? $option->title : "-"; 
                    $productExcelArr[12] = $option->minPerson ? $option->minPerson : "-";
                    $productExcelArr[13] = $option->maxPerson ? $option->maxPerson : "-";

                    $cutOfTimeDate = "";
                    if($option->cutOfTimeDate == 'm') $cutOfTimeDate = " Minute(s)"; 
                    elseif($option->cutOfTimeDate == 'h') $cutOfTimeDate = " Hour(s)"; 
                    elseif($option->cutOfTimeDate == 'd') $cutOfTimeDate = " Day(s)";   
                    $productExcelArr[14] = $option->cutOfTime ? ($option->cutOfTime . $cutOfTimeDate) : "-";

                    $tourDurationDate = "";
                    if($option->tourDurationDate == 'm') $tourDurationDate = " Minute(s)"; 
                    elseif($option->tourDurationDate == 'h') $tourDurationDate = " Hour(s)"; 
                    elseif($option->tourDurationDate == 'd') $tourDurationDate = " Day(s)";   
                    $productExcelArr[15] = $option->tourDuration ? ($option->tourDuration . $tourDurationDate) : "-";

                    $cancelPolicyTimeType = "";
                    if($option->cancelPolicyTimeType == 'm') $cancelPolicyTimeType = " Minute(s)"; 
                    elseif($option->cancelPolicyTimeType == 'h') $cancelPolicyTimeType = " Hour(s)"; 
                    elseif($option->cancelPolicyTimeType == 'd') $cancelPolicyTimeType = " Day(s)";   
                    $productExcelArr[16] = $option->cancelPolicyTime ? ($option->cancelPolicyTime . $cancelPolicyTimeType) : "-";

                    $productExcelArr[17] = $option->meetingPoint ? $option->meetingPoint : "-";

                    $meetingPointAddresses = $option->addresses ? json_decode($option->addresses, true) : null;
                    $decodedAddresses = "";
                    if($meetingPointAddresses) {
                        foreach($meetingPointAddresses as $meetingPointAddress) {
                            $decodedAddresses = $decodedAddresses . $meetingPointAddress["address"] . " | ";
                        }
                    }
                    $productExcelArr[18] = $meetingPointAddresses ? $decodedAddresses : "-";

                    $contactInformationFields = $option->contactInformationFields ? json_decode($option->contactInformationFields) : null;
                    $decodedFields = "";
                    if($contactInformationFields) {
                        foreach($contactInformationFields as $contactInformationField) {
                            $decodedFields = $decodedFields . $contactInformationField->title . " | ";
                        }
                    }
                    $productExcelArr[19] = $contactInformationFields ? $decodedFields : "-";
                    
                    $productExcelArr[20] = $availability->name ? $availability->name : "-";
                    $productExcelArr[21] = ($rangeFrom != "" ? $rangeFrom->format('Y/m/d') : "/") . " - " . ($rangeTo != "" ? $rangeTo->format('Y/m/d') : "/");
                    $productExcelArr[22] = ($availability->isLimitless ? "Limitless, " : "") . ($availability->availabilityType ? $availability->availabilityType : "-"); 
                    $productExcelArr[23] = ($pricings[0]->adultMin >= 0 ? $pricings[0]->adultMin : "x") . " - " . ($pricings[0]->adultMax ? $pricings[0]->adultMax : "x");
                    $productExcelArr[24] = $pricings[0]->adultPrice ? json_decode($pricings[0]->adultPrice, true)[0] . " €" : "-";
                    $productExcelArr[25] = ($pricings[0]->euCitizenMin >= 0 ? $pricings[0]->euCitizenMin : "x") . " - " . ($pricings[0]->euCitizenMax ? $pricings[0]->euCitizenMax : "x");
                    $productExcelArr[26] = $pricings[0]->euCitizenPrice ? json_decode($pricings[0]->euCitizenPrice, true)[0] . " €" : "-";
                    $productExcelArr[27] = ($pricings[0]->youthMin >= 0 ? $pricings[0]->youthMin : "x") . " - " . ($pricings[0]->youthMax ? $pricings[0]->youthMax : "x");
                    $productExcelArr[28] = $pricings[0]->youthPrice ? json_decode($pricings[0]->youthPrice, true)[0] . " €" : "-";
                    $productExcelArr[29] = ($pricings[0]->childMin >= 0 ? $pricings[0]->childMin : "x") . " - " . ($pricings[0]->childMax ? $pricings[0]->childMax : "x");
                    $productExcelArr[30] = $pricings[0]->childPrice ? json_decode($pricings[0]->childPrice, true)[0] . " €" : "-";
                    $productExcelArr[31] = ($pricings[0]->infantMin >= 0 ? $pricings[0]->infantMin : "x") . " - " . ($pricings[0]->infantMax ? $pricings[0]->infantMax : "x");
                    $productExcelArr[32] = $pricings[0]->infantPrice ? json_decode($pricings[0]->infantPrice, true)[0] . " €" : "-";

                    $productExcelArr[33] = $product->fullDesc ? html_entity_decode(strip_tags($product->fullDesc)) : "-";
                    $productExcelArr[34] = $product->shortDesc ? $product->shortDesc : "-";
                    
                    array_push($excelArr, $productExcelArr);
                }

            }
        }

        return collect($excelArr);
    }

    public function headings(): array
    {
        return [
            'Product Ref Code',
            'Product Title',
            'Country',
            'City',
            'Attraction',
            'Highlights',
            'Included',
            'Not Included',
            'Know Before You Go',
            'Category',
            'Option Ref Code',
            'Option Title',
            'Min Person',
            'Max Person',
            'Cut Off Time',
            'Tour Duration',
            'Cancel Policy Time',
            'Meeting Point',
            'Meeting Point Addresses',
            'Contact Information Fields',
            'Availability Name',
            'Availability Date Range',
            'Availability Type',
            'Adult Age Range',
            'Adult',
            'Eu Citizen Age Range',
            'Eu Citizen',
            'Youth Age Range',
            'Youth',
            'Child Age Range',
            'Child',
            'Infant Age Range',
            'Infant',
            'Product Full Description',
            'Product Short Description'
        ];
    }

}
