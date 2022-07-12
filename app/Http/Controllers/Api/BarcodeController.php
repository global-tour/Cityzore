<?php

namespace App\Http\Controllers\Api;

use App\Barcode;
use App\Http\Controllers\Controller;
use App\TicketType;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BarcodeController extends Controller
{

    public function index()
    {
        try {

            $ticketTypes = TicketType::select('id', 'name')->get();

        } catch (\Exception $exception) {
            return response()->json([
                'status'    => false,
                'message'   => 'An error occurred: ' . $exception->getMessage(),
                'data'      => []
            ], 400);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Data fetched!',
            'data'      => $ticketTypes
        ]);
    }

    public function readBarcodes(Request $request)
    {

        try {

            $validation = Validator::make($request->all(), [
                'barcodes' => 'required|array',
                'ticketType' => 'required',
                'barcodes.*.expirationDate' => 'nullable|date_format:d/m/Y',
            ], [
                'required'  => 'this field is required!',
                'array'     => 'this field must be array',
                'date_format' => 'format must be d/m/Y'
            ]);

            if ($validation->fails()) {
                throw new \Exception($validation->errors(), 400);
            }

            $ownerID = -1;

            $data = [];

            foreach ($request->barcodes as $barcodeR) {
                $barcodeOnDB = Barcode::where('code', '=', $barcodeR['barcode'])->where('isExpired', 0)->first();

                if ($barcodeOnDB) {
                    $data['oldBarcodes'][] = $barcodeOnDB;
                }else{
                    $barcode = new Barcode();
                    $barcode->ticketType = $request->ticketType;
                    $ticketType = TicketType::where('id', $barcode->ticketType)->first();
                    $barcode->code = $barcodeR['barcode'];

                    if (isset($barcodeR['reservationNumber'])) {
                        $barcode->reservationNumber = $barcodeR['reservationNumber'];
                    }

                    if (isset($barcodeR['expirationDate'])) {
                        $barcode->endTime = $barcodeR['expirationDate'];
                    } else {
                        $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                    }

                    $barcode->isUsed = 0;
                    $barcode->isExpired = 0;
                    $barcode->isReserved = 0;
                    $barcode->ownerID = -1;
                    $barcode->recode = isset($barcodeR['recode']) ? $barcodeR['recode'] : NULL;
                    $barcode->contact = isset($barcodeR['contact']) ? $barcodeR['contact'] : NULL;
                    $barcode->searchableTicketType = $ticketType->name;
                    $barcode->save();

                    $data['newBarcodes'][] = $barcode;
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
                        $barcodeFromDB['ticket'] += count($request->barcodes);
                        $av->barcode = json_encode([$barcodeFromDB]);
                        $av->save();
                    }
                }
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => false,
                'message' => 'An error occured! '. $exception->getMessage(),
                'data' => []
            ]);

        }

        return response()->json([
            'status'    => true,
            'message'   => isset($data['newBarcodes']) ? 'Barcodes added' : 'Barcodes Fetched',
            'data'      => $data
        ]);


    }

}
