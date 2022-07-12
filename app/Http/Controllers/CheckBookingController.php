<?php

namespace App\Http\Controllers;

use App\Adminlog;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\CryptRelated;
use App\Mails;
use App\Supplier;
use Illuminate\Http\Request;
use App\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\CheckBookingConfirmation;
use App\Barcode;

class CheckBookingController extends Controller
{
    public function checkBookingIndex()
    {
        session()->put('bookingRefCode', '');

        return view('frontend.check-booking');
    }

    public function checkBooking(Request $request)
    {
        try {
            $bookingRefCode = $request->bookingRefCode;
            $confirmationCode = $request->confirmationCode;
            if ($request->conCodeStatus == "conCodeMatters")
                $checkBookingConfirmation = CheckBookingConfirmation::where('confirmationCode', $confirmationCode)->where('bookingReferenceCode', $bookingRefCode)->where('status', false)->first();
            elseif ($request->conCodeStatus == "conCodeNotMatter")
                $checkBookingConfirmation = true;

            if ($checkBookingConfirmation) {
                if ($request->conCodeStatus == "conCodeMatters") {
                    $checkBookingConfirmation->status = true;
                    $checkBookingConfirmation->save();

                    session()->put('bookingRefCode', $bookingRefCode);
                }

                $booking = Booking::with(['extra_files'])->where('bookingRefCode', $bookingRefCode)->orWhere('gygBookingReference', $bookingRefCode)->first(); // check if bokun or gyg
                if (is_null($booking)) {
                    $bookings = Booking::with(['extra_files'])->where('bookingRefCode', 'like', '%' . $bookingRefCode . '%')->get();
                    foreach ($bookings as $book) {
                        $bknRefCode = explode("-", $book->bookingRefCode);
                        $bknRefCode = $bknRefCode[count($bknRefCode) - 1];
                        if ($bknRefCode == $bookingRefCode) {
                            $booking = $book;
                            break;
                        }
                    }
                }

                if ($booking) {
                    $bookingOption = $booking->bookingOption;

                    $title = $bookingOption->translations ? $bookingOption->translations->title : $bookingOption->title;

                    if (is_null($booking->gygBookingReference))
                        $datetime = $booking->date . " " . json_decode($booking->hour, true)[0]['hour'];
                    else
                        $datetime = Carbon::parse($booking->dateTime)->format('d/m/Y H:i');

                    $apiRelated = new \App\Http\Controllers\Helpers\ApiRelated();
                    $bookingItems = $apiRelated->getCategoryAndCountInfoWithLang($booking->bookingItems);
                    $totalPrice = $booking->totalPrice;

                    $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
                    $langCodeForUrl = $langCode == 'en' ? '' : $langCode;
                    $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();

                    $cancellability = false;
                    $cancelPolicyTime = $bookingOption->cancelPolicyTime;
                    if ($cancelPolicyTime != 0) {
                        if (Carbon::createFromFormat('d/m/Y H:i', $datetime)->timestamp > Carbon::now()->timestamp) {
                            $cancelPolicyTimeType = $bookingOption->cancelPolicyTimeType;
                            if ($cancelPolicyTimeType == 'd') {
                                if (Carbon::createFromFormat('d/m/Y H:i', $datetime)->diffInDays(Carbon::now()) > $cancelPolicyTime)
                                    $cancellability = true;
                            } elseif ($cancelPolicyTimeType == 'h') {
                                if (Carbon::createFromFormat('d/m/Y H:i', $datetime)->diffInHours(Carbon::now()) > $cancelPolicyTime)
                                    $cancellability = true;
                            } elseif ($cancelPolicyTimeType == 'm') {
                                if (Carbon::createFromFormat('d/m/Y H:i', $datetime)->diffInMinutes(Carbon::now()) > $cancelPolicyTime)
                                    $cancellability = true;
                            }
                        }
                    }

                    $invoiceUrl = "#";
                    $voucherUrl = "#";
                    if (!($booking->status == 2 || $booking->status == 3)) {
                        $invoiceUrl = url($langCodeForUrl . '/print-invoice-frontend/' . $cryptRelated->encrypt($booking->id));
                        if (!$cancellability)
                            $voucherUrl = url($langCodeForUrl . '/print-pdf-frontend/' . $cryptRelated->encrypt($booking->id));
                    }

                    $extraFiles = [];

                    foreach ($booking->extra_files as $item) {
                        $extraFiles[] = [
                            'base' => $item->image_name,
                            'name' => $item->image_base_name
                        ];
                    }

                    $data = array(
                        //'status' => ($booking->status == 2 || $booking->status == 3) ? false : true,
                        'title' => $title,
                        'datetime' => $datetime,
                        'bookingItems' => $bookingItems,
                        'totalPrice' => $totalPrice,
                        'invoiceUrl' => $invoiceUrl,
                        //'cancellability' => $cancellability,
                        'voucherUrl' => $voucherUrl,
                        'extraFiles' => $extraFiles
                    );


                    return response()->json(['booking' => $data], 200);
                } else
                    return response()->json(['error' => __('bookingNotFoundRelatedWithTheBookingReferenceCode')], 400);
            } else {
                session()->put('bookingRefCode', '');
                return response()->json(['error' => __('invalidConfirmationCode')], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelBookingByUser(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => __('somethingWentWrong'),
            ], 422);
        }

        $cyrptRelated = new CryptRelated();
        $apiRelated = new ApiRelated();

        $decrypt = $cyrptRelated->decrypt($request->id);

        $booking = Booking::with('bookingOption')->find($decrypt);

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => __('bookingNotFound'),
            ], 404);
        }


        if ($booking->status == 2 || $booking->status == 3)
            return response()->json([
                'error' => __('bookingIsAlreadyCancelled')
            ], 400);


        $booking->status = 2;
        $booking->save();

        Barcode::where('bookingID', $booking->id)->update([
            'cartID' => null,
            'bookingId' => null,
            'isUsed' => 0,
            'isReserved' => 0,
            'log' => json_encode([
                'oldBookingID' => $booking->id,
                'cancelReason' => '-',
                'cancelBy' => 'Customer',
                'cancelDate' => Carbon::now()->format('d/m/Y-H:i')
            ])
        ]);

        Adminlog::create([
            'userID' => Auth::user()->id,
            'page' => 'my-profile',
            'url' => $request->fullUrl(),
            'action' => 'Canceled Booking',
            'details' => Auth::user()->name . ' ' . $booking->id . ' id of Booking Status Changed',
            'productRefCode' => $booking->productRefCode,
            'optionRefCode' => $booking->optionRefCode,
            'tableName' => 'Bookings',
            'columnName' => 'bookings.status',
        ]);

        $BKNCode = ($booking->isBokun == 1 || $booking->isViator == 1) ? $booking->bookingRefCode : $apiRelated->explodeBookingRefCode($booking->bookingRefCode)['bkn'];
        $date = date('D, d-F-Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']));

        $subjectForCustomer = 'Booking is canceled!';
        $subjectForCompany = json_decode($booking->travelers, 1)[0]['firstName'] . ' ' . json_decode($booking->travelers, 1)[0]['lastName'] . "'s booking is canceled! $BKNCode";
        $bladeForCustomer = 'mail.booking-cancel';
        $bladeForCompany = 'mail.booking-cancel-for-creator';

        Mails::insert([
            [
                'to' => json_decode($booking->travelers, 1)[0]['email'],
                'data' => json_encode([
                    'sendToCC' => false,
                    'BKNCode' => $BKNCode,
                    'options' => $booking->bookingOption->title,
                    'date' => $date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'subject' => $subjectForCustomer,
                    'name' => json_decode($booking->travelers, 1)[0]['firstName'],
                    'surname' => json_decode($booking->travelers, 1)[0]['lastName']
                ]),
                'blade' => $bladeForCustomer
            ],
            [
                'to' => $booking->companyID == -1 ? 'contact@parisviptrips.com' : Supplier::findOrFail($booking->companyID)->first()->email,
                'data' => json_encode([
                    'sendToCC' => $booking->companyID == -1 ? true : false,
                    'categoryAndCountInfo' => $apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'options' => $booking->bookingOption->title,
                    'date' => $date,
                    'subject' => $subjectForCompany,
                    'name' => json_decode($booking->travelers, 1)[0]['firstName'],
                    'surname' => json_decode($booking->travelers, 1)[0]['lastName']
                ]),
                'blade' => $bladeForCompany
            ]
        ]);



        return response()->json([
            'status' => true,
            'message' => __('theBookingHasBeenCancelled')
        ]);


    }

    public function cancelBooking(Request $request)
    {
        try {
            $bookingRefCode = session()->get('bookingRefCode');
            if ($bookingRefCode == '' || $bookingRefCode == null)
                return response()->json(['error' => __('somethingWentWrong')], 400);

            $booking = Booking::where('bookingRefCode', $bookingRefCode)->orWhere('gygBookingReference', $bookingRefCode)->first(); // check if bokun or gyg
            if (is_null($booking)) {
                $bookings = Booking::where('bookingRefCode', 'like', '%' . $bookingRefCode . '%')->get();
                foreach ($bookings as $book) {
                    $bknRefCode = explode("-", $book->bookingRefCode);
                    $bknRefCode = $bknRefCode[count($bknRefCode) - 1];
                    if ($bknRefCode == $bookingRefCode) {
                        $booking = $book;
                        break;
                    }
                }
            }

            if ($booking) {
                if ($booking->status == 2 || $booking->status == 3)
                    return response()->json(['error' => __('bookingIsAlreadyCancelled')], 400);

                $booking->status = 2;
                $booking->save();

                $barcode = Barcode::where('bookingID', $booking->id)->first();
                if ($barcode) {
                    $barcode->cartID = null;
                    $barcode->bookingID = null;
                    $barcode->isUsed = 0;
                    $barcode->isReserved = 0;

                    $logs = json_decode($barcode->log, true) ?? [];
                    array_push($logs, [
                        "oldBookingID" => $booking->id,
                        "cancelReason" => "-",
                        "cancelBy" => "Customer",
                        "cancelDate" => Carbon::now()->format('d/m/Y-H:i')
                    ]);
                    $barcode->log = json_encode($logs);

                    $barcode->save();
                }

                session()->put('bookingRefCode', '');

                return response()->json(['message' => __('theBookingHasBeenCancelled')], 200);
            } else
                return response()->json(['error' => __('bookingNotFound')], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sendConfirmationCode(Request $request)
    {
        try {
            $apiRelated = new ApiRelated();
            $bookingRefCode = $request->bookingRefCode;

            $booking = Booking::where('bookingRefCode', $bookingRefCode)->orWhere('gygBookingReference', $bookingRefCode)->first(); // check if bokun or gyg
            if (is_null($booking)) {
                $bookings = Booking::where('bookingRefCode', 'like', '%' . $bookingRefCode . '%')->get();
                foreach ($bookings as $book) {
                    $bknRefCode = $apiRelated->explodeBookingRefCode($book->bookingRefCode);
                    if ($bknRefCode['bkn'] == $bookingRefCode) {
                        $booking = $book;
                        break;
                    }
                }
            }

            if ($booking) {
                $to = json_decode($booking->travelers)[0]->email;
                $data = array(
                    'confirmationCode' => $this->genRandStr(),
                    'subject' => 'Check Booking Confirmation Code'
                );
                $blade = 'mail.check-booking-confirmation';

                DB::beginTransaction();
                try {
                    $checkBookingConfirmation = CheckBookingConfirmation::where('bookingReferenceCode', $bookingRefCode)->where('status', false)->first();
                    if ($checkBookingConfirmation) {
                        $checkBookingConfirmation->confirmationCode = $data['confirmationCode'];
                        $checkBookingConfirmation->save();
                    } else {
                        $confirmationData = array(
                            'bookingReferenceCode' => $bookingRefCode,
                            'email' => $to,
                            'confirmationCode' => $data['confirmationCode'],
                        );
                        CheckBookingConfirmation::create($confirmationData);
                    }
                    DB::commit();

                    Mail::send($blade, array('data' => $data), function ($message) use ($data, $to, $blade) {
                        $message->from('contact@cityzore.com', 'Cityzore');
                        $message->to($to);
                        $message->subject($data['subject']);
                    });

                    return response()->json(['message' => __('confirmationCodeHasBeenSentToYourEmail') . ' ' . $this->hideEmailAddress($to) . '. ' . __('pleaseTypeTheCodeBelow') . '.'], 200);
                } catch (Exception $e) {
                    DB::rollback();
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            } else
                return response()->json(['error' => __('bookingNotFoundRelatedWithTheBookingReferenceCode')], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function resetBKNSession()
    {
        try {
            session()->put('bookingRefCode', '');
            return response()->json(['message' => 'Process successfull'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function genRandStr(int $length = 6, string $prefix = '', string $suffix = '')
    {
        for ($i = 0; $i < $length; $i++) {
            $prefix .= random_int(0, 1) ? chr(random_int(65, 90)) : random_int(0, 9);
        }

        return $prefix . $suffix;
    }

    public function hideEmailAddress($email)
    {
        $em = explode("@", $email);
        $name = implode(array_slice($em, 0, count($em) - 1), '@');
        $len = floor(strlen($name) / 2);
        return substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
    }
}
