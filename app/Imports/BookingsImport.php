<?php

namespace App\Imports;

use App\Booking;
use App\Currency;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Option;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookingsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {

        $status = [
            'CONFIRMED' => 0,
            'CANCELLED' => 2,
            'PENDING' => 4
        ];
        $refArray = array();
        foreach ($rows as $row) {
            try {

                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['creation_date']);

                $startDate = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date']));

                $travelers = [];
                $fullNameArray = explode(' ', str_replace(',', '', $row['customer']));
                $travelers[] = ['email' => $row['email'], 'firstName' => Arr::last($fullNameArray), 'lastName' => Arr::first($fullNameArray), 'phoneNumber' => $row['phone_number']];

                $productID = explode('-', str_replace(' ', '', $row['product_id']));

                // strpos($productID[0], 'PBT') =>false
                $productRefCode = array_filter($productID, function ($item) {
                    if (strpos($item, 'PBT') !== false) {
                        return $item;
                    } else {
                        return null;
                    }
                });
                $productRefCode = empty($productRefCode) ? null : $productRefCode[array_keys($productRefCode)[0]];

                $currency = Currency::where('currency', $row['sale_currency'])->first();


                $bookingItems = [];

                if (strpos($row['participants'], ",")) {
                    $participants = explode(',', str_replace(' ', '', $row['participants']));


                    foreach ($participants as $par) {
                        $par = explode(':', str_replace(' ', '', $par));
                        $bookingItems[] = ['category' => strtoupper(rtrim(preg_replace("/\((.*)\)/", "", Arr::first($par)), 's')), 'count' => Arr::last($par)];
                    }


                } else {

                    $participants = explode(':', str_replace(' ', '', $row['participants']));
                    $bookingItems[] = ['category' => strtoupper(rtrim(preg_replace("/\((.*)\)/", "", Arr::first($participants)), 's')), 'count' => Arr::last($participants)];

                }

                $bookingRef = $row['external_booking_ref'];
                if (in_array($row['status'], $status, true)) {
                    $statusCode = $status[$row['status']];
                } else {
                    $statusCode = 0;
                }
                if ($bookingRef == "") $bookingRef = "NODATA";
                $bookingRefStatus = in_array($bookingRef, $refArray);

                $hasBooking = Booking::where('bookingRefCode', $bookingRef)->first();
                if ($hasBooking && !$bookingRefStatus) {
                    $hasBooking->status = $statusCode;
                    $hasBooking->save();
                } else {
                    $bookingRefNew = $bookingRef;
                    if ($bookingRefStatus) {
                        $number = 1;
                        do {
                            $bookingRefNew = $bookingRef . '-' . $number;
                            $number++;
                            $arrayStatus = in_array($bookingRefNew, $refArray, true);
                        } while ($arrayStatus);
                    }
                    array_push($refArray, $bookingRefNew);
                    $booking = new Booking();
                    $booking->optionRefCode = $row['commission'] ?? 'empty';
                    $booking->created_at = $date;
                    $booking->isBokun = true;
                    $booking->reservationRefCode = $row['cart_confirmation_code'];
                    if ($row['seller'] == 'Viator.com') {
                        $booking->bookingRefCode = 'BR-' . $bookingRefNew;
                    } elseif ($row['seller'] == 'Musement') {
                        $booking->bookingRefCode = $bookingRefNew;
                    } else {
                        $booking->bookingRefCode = $row['product_confirmation_code'];
                    }
                    $booking->travelers = json_encode($travelers);
                    $booking->fullName = $row['customer'];
                    $booking->productRefCode = $productRefCode;
                    $booking->dateTime = Carbon::parse($startDate->format("Y-m-d H:i:s"))->toIso8601String();
                    $booking->date = (string)$startDate->format('d/m/Y');
                    $booking->dateForSort = $startDate->format('Y-m-d');
                    $booking->hour = json_encode([['hour' => $startDate->format('H:i')]]);
                    $booking->status = $statusCode;
                    $booking->totalPrice = $row['total_price_with_discount'];
                    $booking->currencyID = $currency->id;
                    $booking->bookingItems = json_encode($bookingItems);
                    $booking->fromWebsite = $row['seller'];
                    $avID = [];
                    $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                    foreach ($bAvs as $bAv) {
                        array_push($avID, $bAv->id);
                    }
                    $booking->avID = json_encode($avID);

                    if ($booking->save()) {

                    }
                }
            } catch (\Exception $exception) {
                dd($exception->getMessage());
            }
        }
    }
}
