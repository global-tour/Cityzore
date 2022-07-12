<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helpers\ViatorRelated;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ViatorController extends Controller
{
    public $viatorRelated;
    public function __construct()
    {
        $this->viatorRelated = new ViatorRelated();
    }

    public function getAvailabilities(Request $request) {
        $responseType = 'AvailabilityResponse';
        /* Mandatory Parameters Control */
        $mandatoryParameters = [
            'requestType',
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'Timestamp', 'StartDate', 'SupplierProductCode']
        ];

        if(!$this->viatorRelated->checkMandatoryParameters($mandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* Semi Mandatory Parameters Control */
        $semiMandatoryParameters = [
            'data' => [
                'Parameter' => ['Name', 'Value'],
                'TourOptions' => [
                    'Option' => ['Name', 'Value']
                ],
                'AvailabilityHold' => ['Expiry']
            ]
        ];
        if(!$this->viatorRelated->checkSemiMandatoryParameters($semiMandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* ApiKey parameter control */
        if(!$this->viatorRelated->apiAuthorization($request->data["ApiKey"], $request->data['SupplierId'], $request->data['ResellerId']))
            return $this->viatorRelated->throwErrors('TGDS0002', $responseType);

        /* Action Control */
        $getAvailabilityResponse = $this->viatorRelated->getAvailability($request);
        if(isset($getAvailabilityResponse['status']) && $getAvailabilityResponse['status'] == 'error')
            return $this->viatorRelated->throwErrors($getAvailabilityResponse['errorkey'], $responseType);

        $data = [];
        $constantParameters = [
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'ExternalReference', 'SupplierProductCode']
        ];
        $changeableParameters = [
            'responseType' => $responseType,
            'data' => [
                'Timestamp' => Carbon::now(),
                'RequestStatus' => ['Status' => 'SUCCESS'],
                'TourAvailability' => $getAvailabilityResponse
            ]
        ];

        $data = $this->viatorRelated->putConstantParameters($constantParameters, $data, $request);
        $data = $this->viatorRelated->putChangeableParameters($changeableParameters, $data);

        return response()->json($data);
    }

    public function getTourList(Request $request) {
        $responseType = 'TourListResponse';
        /* Mandatory Parameters Control */
        $mandatoryParameters = [
            'requestType',
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'Timestamp']
        ];
        if(!$this->viatorRelated->checkMandatoryParameters($mandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* Semi Mandatory Parameters Control */
        $semiMandatoryParameters = [
            'data' => [
                'Parameter' => ['Name', 'Value']
            ]
        ];
        if(!$this->viatorRelated->checkSemiMandatoryParameters($semiMandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* ApiKey parameter control */
        if(!$this->viatorRelated->apiAuthorization($request->data["ApiKey"], $request->data['SupplierId'], $request->data['ResellerId']))
            return $this->viatorRelated->throwErrors('TGDS0002', $responseType);

        /* Action Control */
        $getTourListResponse = $this->viatorRelated->getTourList($request);
        if(isset($getTourListResponse['status']) && $getTourListResponse['status'] == 'error')
            return $this->viatorRelated->throwErrors($getTourListResponse['errorkey'], $responseType);

        $data = [];
        $constantParameters = [
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'ExternalReference']
        ];
        $changeableParameters = [
            'responseType' => $responseType,
            'data' => [
                'Timestamp' => Carbon::now(),
                'RequestStatus' => ['Status' => 'SUCCESS'],
                'Tour' => $getTourListResponse
            ]
        ];

        $data = $this->viatorRelated->putConstantParameters($constantParameters, $data, $request);
        $data = $this->viatorRelated->putChangeableParameters($changeableParameters, $data);

        return response()->json($data);
    }

    public function book(Request $request) {
        $responseType = 'BookingResponse';
        /* Mandatory Parameters Control */
        $mandatoryParameters = [
            'requestType',
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'Timestamp', 'BookingReference', 'TravelDate', 'SupplierProductCode',
                'Traveller' => ['TravellerIdentifier', 'GivenName', 'Surname', 'AgeBand']
            ]
        ];

        if(!$this->viatorRelated->checkMandatoryParameters($mandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* Semi Mandatory Parameters Control */
        $semiMandatoryParameters = [
        ];

        if(!$this->viatorRelated->checkSemiMandatoryParameters($semiMandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* ApiKey parameter control */
        if(!$this->viatorRelated->apiAuthorization($request->data["ApiKey"], $request->data['SupplierId'], $request->data['ResellerId']))
            return $this->viatorRelated->throwErrors('TGDS0002', $responseType);

        /* Action Control */
        $bookResponse = $this->viatorRelated->book($request);
        if(isset($bookResponse['status']) && $bookResponse['status'] == 'error')
            return $this->viatorRelated->throwErrors($bookResponse['errorkey'], $responseType);

        $data = [];
        $constantParameters = [
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'ExternalReference']
        ];
        $changeableParameters = [
            'responseType' => $responseType,
            'data' => [
                'Timestamp' => Carbon::now(),
                'RequestStatus' => ['Status' => 'SUCCESS'],
                'Traveller' => $bookResponse['Traveller'],
                'TransactionStatus' => ['Status' => 'CONFIRMED'],
                'SupplierConfirmationNumber' => $bookResponse['SupplierConfirmationNumber']
            ]
        ];

        $data = $this->viatorRelated->putConstantParameters($constantParameters, $data, $request);
        $data = $this->viatorRelated->putChangeableParameters($changeableParameters, $data);

        return response()->json($data);
    }

    public function cancelBooking(Request $request) {
        $responseType = 'BookingCancellationResponse';
        /* Mandatory Parameters Control */
        $mandatoryParameters = [
            'requestType',
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'Timestamp', 'BookingReference', 'SupplierConfirmationNumber', 'CancelDate', 'Author', 'Reason']
        ];
        if(!$this->viatorRelated->checkMandatoryParameters($mandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* Semi Mandatory Parameters Control */
        $semiMandatoryParameters = [
            'data' => [
                'Parameter' => ['Name', 'Value']
            ]
        ];
        if(!$this->viatorRelated->checkSemiMandatoryParameters($semiMandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* ApiKey parameter control */
        if(!$this->viatorRelated->apiAuthorization($request->data["ApiKey"], $request->data['SupplierId'], $request->data['ResellerId']))
            return $this->viatorRelated->throwErrors('TGDS0002', $responseType);

        /* Action Control */
        $cancelBookingResponse = $this->viatorRelated->cancelBooking($request);
        if(isset($cancelBookingResponse['status']) && $cancelBookingResponse['status'] == 'error')
            return $this->viatorRelated->throwErrors($cancelBookingResponse['errorkey'], $responseType);

        $data = [];
        $constantParameters = [
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'ExternalReference', 'BookingReference', 'SupplierConfirmationNumber']
        ];
        $changeableParameters = [
            'responseType' => $responseType,
            'data' => [
                'Timestamp' => Carbon::now(),
                'RequestStatus' => ['Status' => 'SUCCESS'],
                'TransactionStatus' => ['Status' => 'CONFIRMED'],
            ]
        ];

        $data = $this->viatorRelated->putConstantParameters($constantParameters, $data, $request);
        $data = $this->viatorRelated->putChangeableParameters($changeableParameters, $data);

        return response()->json($data);
    }

    public function amendBooking(Request $request) {
        $responseType = 'BookingAmendmentResponse';
        /* Mandatory Parameters Control */
        $mandatoryParameters = [
            'requestType',
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'Timestamp', 'BookingReference', 'TravelDate', 'SupplierProductCode',
                'Traveller' => ['TravellerIdentifier', 'GivenName', 'Surname', 'AgeBand']
            ]
        ];

        if(!$this->viatorRelated->checkMandatoryParameters($mandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* Semi Mandatory Parameters Control */
        $semiMandatoryParameters = [
            'data' => [
                'TourOptions' => [
                    'Option' => ['Name', 'Value']
                ],
                'ContactDetail' => ['ContactType', 'ContactName', 'ContactValue'],
                'RequiredInfo' => [
                    'Question' => ['QuestionText']
                ]
            ]
        ];
        if(!$this->viatorRelated->checkSemiMandatoryParameters($semiMandatoryParameters, $request))
            return $this->viatorRelated->throwErrors('TGDS0022', $responseType);

        /* ApiKey parameter control */
        if(!$this->viatorRelated->apiAuthorization($request->data["ApiKey"], $request->data['SupplierId'], $request->data['ResellerId']))
            return $this->viatorRelated->throwErrors('TGDS0002', $responseType);

        /* Action Control */
        $amendBookingResponse = $this->viatorRelated->amendBooking($request);
        if(isset($amendBookingResponse['status']) && $amendBookingResponse['status'] == 'error')
            return $this->viatorRelated->throwErrors($amendBookingResponse['errorkey'], $responseType);

        $data = [];
        $constantParameters = [
            'data' => ['ApiKey', 'ResellerId', 'SupplierId', 'ExternalReference']
        ];
        $changeableParameters = [
            'responseType' => $responseType,
            'data' => [
                'Timestamp' => Carbon::now(),
                'RequestStatus' => ['Status' => 'SUCCESS'],
                'Traveller' => $amendBookingResponse['Traveller'],
                'TransactionStatus' => ['Status' => 'CONFIRMED'],
                'SupplierConfirmationNumber' => $amendBookingResponse['SupplierConfirmationNumber']
            ]
        ];

        $data = $this->viatorRelated->putConstantParameters($constantParameters, $data, $request);
        $data = $this->viatorRelated->putChangeableParameters($changeableParameters, $data);

        return response()->json($data);
    }
}
