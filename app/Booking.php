<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class Booking extends Model
{
    protected $table = 'bookings';

    public function physicalTickets()
    {
        return $this->belongsToMany(Physicalticket::class, 'booking_physicalticket');
    }

    public function bookingOption()
    {
        return $this->belongsTo('App\Option', 'optionRefCode', 'referenceCode');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currencyID')->where('isActive', 1);
    }

    public function bookingProduct()
    {
        return $this->belongsTo('App\Product', 'productRefCode', 'referenceCode');
    }

    public function bookingCurrency()
    {
        return $this->belongsTo('App\Currency', 'currencyID', 'id');
    }

    public function options()
    {
        return $this->belongsTo(Option::class);
    }

    public function invoices()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoc()
    {
        return $this->hasOne(Invoice::class, 'bookingID', 'id');
    }

    public function check()
    {
        return $this->hasMany(Checkin::class, 'booking_id');
    }

    public function affiliater()
    {
        return $this->belongsTo(User::class, 'affiliateID');
    }

    public function extra_files()
    {
        return $this->hasMany(BookingImage::class, 'booking_id');
    }

    public function invoice_numbers()
    {
        return $this->hasMany(BookingInvoice::class, 'booking_id');
    }

    public function contacts()
    {
        return $this->hasMany(BookingContactMailLog::class, 'booking_id')->orderBy('id');
    }

    public function contactBooking()
    {
        return $this->hasOne(BookingContactMailLog::class, 'booking_id', 'id')->orderBy('id', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Supplier::class, 'rCodeID', 'id');
    }

    public function platform()
    {
        return $this->hasOne(Platform::class, 'id', 'platformID');
    }

    public function mails()
    {
        return $this->hasMany(Mails::class, 'bookingID', 'id');
    }

    public function getCancelPolicyStatusAttribute()
    {
        if ($this->bookingOption->cancelPolicyTime) {

            $bookingTime = is_array(json_decode($this->attributes['dateTime'])) ? json_decode($this->attributes['dateTime'], true)[0]['dateTime'] : $this->attributes['dateTime'];

            switch ($this->bookingOption->cancelPolicyTimeType) {

                case 'm':
                    $current = Carbon::now()->addMinutes($this->bookingOption->cancelPolicyTime);

                    return Carbon::make($current)->lessThan($bookingTime);

                case 'h':
                    $current = Carbon::now()->addHours($this->bookingOption->cancelPolicyTime);

                    return Carbon::make($current)->lessThan($bookingTime);

                case 'd':

                    $current = Carbon::now()->addDays($this->bookingOption->cancelPolicyTime);

                    return Carbon::make($current)->lessThan($bookingTime);

            }

        }

        return false;
    }

    public function getBookingDateTimeAttribute()
    {
        if ($this->attributes['dateTime']) {

            if (!is_null($this->attributes['gygBookingReference'])) {
                $color = 'orange';
            } elseif ($this->attributes['isBokun'] == 1 || $this->attributes['isViator'] == 1) {
                $color = '#000';
            } else {
                $color = '#0b9e0d';
            }

            if (is_array(json_decode($this->attributes['dateTime'], 1))){
                $time = '';
                if (!is_null($this->attributes['hour'])) {
                    foreach (json_decode($this->attributes['hour'], 1) as $item) {
                        $time .= $item['hour'] . '<br>';
                    }
                }

                return [
                    'org' => Carbon::make(json_decode($this->attributes['dateTime'], true)[0]['dateTime'])->format('Y-m-d H:i:s'),
                    'year' => Carbon::make(json_decode($this->attributes['dateTime'], true)[0]['dateTime'])->format('Y'),
                    'month' => Carbon::make(json_decode($this->attributes['dateTime'], true)[0]['dateTime'])->format('F'),
                    'day' => Carbon::make(json_decode($this->attributes['dateTime'], true)[0]['dateTime'])->format('d'),
                    'dayD' => Carbon::make(json_decode($this->attributes['dateTime'], true)[0]['dateTime'])->format('D'),
                    'time' => $time,
                    'color' => $color
                ];
            }

            return [
                'org' => Carbon::make($this->attributes['dateTime'])->format('Y-m-d H:i:s'),
                'year' => Carbon::make($this->attributes['dateTime'])->format('Y'),
                'month' => Carbon::make($this->attributes['dateTime'])->format('F'),
                'day' => Carbon::make($this->attributes['dateTime'])->format('d'),
                'dayD' => Carbon::make($this->attributes['dateTime'])->format('D'),
                'time' => Carbon::make($this->attributes['dateTime'])->format('H:i'),
                'color' => $color
            ];
        }

        return '';
    }

    public function getAvailabilityHoursAttribute()
    {
        $bookingAvData = [];

        if (!is_null($this->attributes['avID'])) {
            $availabilities = Av::whereIn('id', json_decode($this->attributes['avID'], 1))->get();
        }else{
            $availabilities = $this->bookingOption->avs;
        }

        $date = \Carbon\Carbon::parse($this->bookingDateTime['org'])->format('d/m/Y');

        foreach ($availabilities as $key => $val) {
            $bookingAvData[$key]["availabilityType"] = $val->availabilityType;
            $bookingAvData[$key]["availabilityName"] = $val->name;
            $bookingAvData[$key]["date"] = $date;

            if (!is_null($this->attributes['hour'])) {
                $hour = json_decode($this->attributes['hour'], true);

                $h = $hour[$key];

                $h = preg_replace('/\s+/', '', $h["hour"]);
                if (count(explode('-', $h)) == 2) {
                    $bookingAvData[$key]["hourFrom"] = explode('-', $h)[0];
                    $bookingAvData[$key]["hourTo"] = explode('-', $h)[1];
                } else {
                    $bookingAvData[$key]["hourFrom"] = $h;
                    $bookingAvData[$key]["hourTo"] = null;
                }
            } else {

                if (is_array(json_decode($this->attributes['dateTime'], 1))) {
                    if (isset(json_decode($this->attributes['dateTime'], true)[$key])) {
                        $hour = json_decode($this->attributes['dateTime'], true);
                        $hour = \Carbon\Carbon::parse($hour[$key]["dateTime"])->format('H:i');
                    }
                } else {
                    $hour = Carbon::parse($this->attributes['dateTime'])->format('H:i');
                }

                $bookingAvData[$key]["hourFrom"] = $hour;
                $bookingAvData[$key]["hourTo"] = null;
            }
        }
        return $bookingAvData;
    }

    public function getTourDetailsAttribute()
    {

        $row = [];
        $travelers = json_decode($this->attributes['travelers'], 1);

        $row['leadTraveler'] = $this->attributes['fullName'] ?? '';
        $row['phoneNumber'] = !is_null($travelers) ? $travelers[0]['phoneNumber'] : '';
        $row['email'] = !is_null($travelers) ? $travelers[0]['email'] : '';
        $row['product'] = $this->bookingProduct->title ?? '';
        $row['option'] = $this->bookingOption->title ?? '';

        if (!is_null($this->attributes['gygBookingReference'])) {
            $row['product'] = 'GYG Product';
        }

        return $row;
    }

    public function getBookingDetailsAttribute()
    {
        $row = [];
        $bookingRefColumn = '';

        $row['created_at'] = $this->attributes['created_at'];

        if ($this->attributes['bookingRefCode']) {

            if ($this->attributes['isBokun'] || $this->attributes['isViator']) {
                $row['bkn'] = $this->attributes['bookingRefCode'];
            } else {
                $arr = explode('-', $this->attributes['bookingRefCode']);
                $row['bkn'] = $arr[array_key_last($arr)];
            }
        }


        foreach (json_decode($this->attributes['bookingItems'], true) as $participants) {
            $bookingRefColumn .= $participants['category'] . ': ' . $participants['count'] . ' / ';
        }

        $row['gyg'] = !is_null($this->attributes['gygBookingReference']) ? $this->attributes['gygBookingReference'] : '';

        $row['participants'] = rtrim($bookingRefColumn, ' / ');
        $row['currency'] = session()->get('currencyIcon') ?? 'icon-cz-eur';
        $row['price'] = $this->attributes['totalPrice'] ?? '0.00';
        $row['lang'] = strtolower($this->attributes['language']);

        return $row;
    }

    public function getSalesInformationAttribute()
    {
        $row = [];

        $row['invoice_id'] = $this->invoc->referenceCode ?? '';

        if (!is_null($this->attributes['gygBookingReference'])) {
            $row['payment'] = 'GETYOURGUIDE';
        } elseif (is_null($this->attributes['gygBookingReference']) && ($this->attributes['isBokun'] || $this->attributes['isViator'])) {
            $row['payment'] = $this->invoc->paymentMethod ?? '';
        } else {
            $row['payment'] = $this->invoc->paymentMethod ?? '' . ' - ' . $this->platform->name ;
        }

        $row['affiliater'] = $this->attributes['affiliater'] ?? '';

        return $row;
    }

    public function getBookingInformationAttribute()
    {
        $row = [];

        $row['mailCheck'] = $this->contactBooking ? json_decode($this->contactBooking->check_information, 1)['status'] : false;
        $row['mailSended'] = $this->contactBooking ? $this->contactBooking->status : 0 ;
        $row['contactCheck'] = $this->extra_files->count() > 0;
        $row['specialRefCode']['status'] = !is_null($this->attributes['specialRefCode']);
        $row['specialRefCode']['code'] = $this->attributes['specialRefCode'] ?? '';
        $row['invoices'] = $this->invoice_numbers->count();
        $row['updated_at'] = Carbon::make($this->updated_at)->format('Y-m-d H:i:s') ?? '';

        return $row;
    }

    public function getDefaultMessageAttribute()
    {
        $messages = [];
        $customerTemplate = $this->bookingOption->customer_mail_templates ? json_decode($this->bookingOption->customer_mail_templates, 1) : [];

        foreach ($customerTemplate as $lang => $message) {

            if ($message) {
                if (strpos($message, "#NAME SURNAME#")) {
                    $message = str_replace("#NAME SURNAME#", $this->attributes['fullName'], $message);
                }

                if (strpos($message, "#SENDER#") !== false) {
                    $message = str_replace("#SENDER#", auth()->guard('admin')->user()->name, $message);
                }

                if (strpos($this->attributes['dateTime'], "dateTime") === false) {
                    $meetingDateTime = \Carbon\Carbon::parse($this->attributes['dateTime'])->format("d/m/Y H:i:s");
                } else {
                    $meetingDateTime = $this->attributes['date'] . " " . json_decode($this->attributes['hour'], true)[0]["hour"];
                }

                if (strpos($message, "#DATE#") !== false) {
                    $message = str_replace("#DATE#", $meetingDateTime, $message);
                }

                $messages[$lang] = $message;
            }else{
                $messages[$lang] = '';
            }

        }

        return $messages;
    }

    public function getWhatsappMessageAttribute()
    {
        $messages = [];
        $customerTemplate = $this->bookingOption->customer_whatsapp_templates ? json_decode($this->bookingOption->customer_whatsapp_templates, 1) : [];

        foreach ($customerTemplate as $lang => $message) {

            if ($message) {
                if (strpos($message, "#NAME SURNAME#")) {
                    $message = str_replace("#NAME SURNAME#", $this->attributes['fullName'], $message);
                }

                if (strpos($message, "#SENDER#") !== false) {
                    $message = str_replace("#SENDER#", auth()->guard('admin')->user()->name, $message);
                }

                if (strpos($this->attributes['dateTime'], "dateTime") === false) {
                    $meetingDateTime = \Carbon\Carbon::parse($this->attributes['dateTime'])->format("d/m/Y H:i:s");
                } else {
                    $meetingDateTime = $this->attributes['date'] . " " . json_decode($this->attributes['hour'], true)[0]["hour"];
                }

                if (strpos($message, "#DATE#") !== false) {
                    $message = str_replace("#DATE#", $meetingDateTime, $message);
                }

                $messages[$lang] = $message;
            }else{
                $messages[$lang] = '';
            }

        }



        return $messages;
    }

    public function getBookingStatusAttribute()
    {
        if ($this->attributes['isBokun'] || $this->attributes['isViator']) {
            switch ($this->attributes['status']) {
                case 0:
                    return [
                        'status'    => 'Approved',
                        'bgColor'   => '#3c763d'
                    ];
                case 2:
                case 3:
                    return [
                        'status'    => 'Canceled',
                        'bgColor'   => '#a94442'
                    ];
            }
        }else{

            switch ($this->attributes['status']) {
                case 0:
                    return [
                        'status'    => 'Approved',
                        'bgColor'   => '#3c763d'
                    ];
                case 2:
                case 3:
                    return [
                        'status'    => 'Canceled',
                        'bgColor'   => '#a94442'
                    ];
                case 4:
                case 5:
                    return [
                        'status'    => 'Pending',
                        'bgColor'   => '#8a6d3b'
                    ];

            }

        }
    }

    // status descriptions
    // 0: Normal Booking
    // 1: Made an ammendment
    // 2: Normal Cancel
    // 3: Manual Cancel (From our system)
    // 4: Pending
    // 5: Pending for Confirmation

}
