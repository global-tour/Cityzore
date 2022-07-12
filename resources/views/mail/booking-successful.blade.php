@php
    $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- GOOGLE FONTS -->
    <title>Cityzore-New Booking</title>
    <style>
        body {
            margin-right: 15%;
            margin-left: 15%;
        }
        p, h3 {
            font-size: 16px;
        }
    </style>
</head>
<body>

    @php
     if(!empty($data["booking_id"])){
              $booking = \App\Booking::findOrFail($data["booking_id"]);
              $option = \App\Option::where('referenceCode', '=', $booking->optionRefCode)->first();

                     if(!is_null($option->customer_mail_templates) && !empty(json_decode($option->customer_mail_templates, true)["en"])){
                      $traveler = json_decode($booking->travelers,true)[0];

                       if(strpos($booking->dateTime, "dateTime") === false){
                       $meetingDateTime = \Carbon\Carbon::parse($booking->dateTime)->format("d/m/Y H:i:s");
                        }else{
                           $meetingDateTime = $booking->date ." ".json_decode($booking->hour, true)[0]["hour"];
                        }



                     $mailTemplate = json_decode($option->customer_mail_templates, true)["en"];
                     $mailTemplate = str_replace("#NAME SURNAME#", $traveler["firstName"]." ".$traveler['lastName'], $mailTemplate);
                     $mailTemplate = str_replace("#SENDER#", "Paris Business & Travel", $mailTemplate);
                     $mailTemplate = str_replace("#DATE#", $meetingDateTime, $mailTemplate);



                     $voucher_url = "<br><br>You Can Download Your Voucher with this Link ".url('/print-pdf-frontend/'.$cryptRelated->encrypt($booking->id));
                     $mailTemplate .= $voucher_url;











                     $mailTemplate = nl2br($mailTemplate);
                 }
             }
    @endphp
    <div class="col-lg-12">
        <img src="{{url('https://cityzore.com/img/paris-city-tours-logo.png')}}" class="logo" style="width: 200px; height:60px;"/>
        <img src="{{url('https://cityzore.com/mail/r/'.$data["mail_code"])}}" width="1" height="1">

    </div>
    <div class="col-lg-12">
        <h3>Dear {{$data['name']}} {{$data['surname']}}, </h3>
        <p>Your booking is successfully made.</p>
        <p>
            <strong>Reference Number:</strong> {{$data['BKNCode']}}
        </p>
        <p>
            <strong>Name:</strong> {{$data['name']}} {{$data['surname']}}
        </p>
        <p>
            <strong>Date:</strong> {{$data['date']}}
        </p>
        <p>
            <strong>Hour:</strong> {{$data['hour']}}
        </p>
        <p>
            <strong>Activity:</strong> {{$data['options']}}
        </p>
        <p>
            You can use <a href="https://www.cityzore.com/voucher/{{$data['hash']}}">this link</a> to display your voucher
        </p>
        @if(!empty($mailTemplate))
        <br>
        <p>{!!$mailTemplate!!}</p>
        @endif
    </div>

    <div class="col-lg-12 information">
        <p>​PARIS BUSINESS AND TRAVEL <br>
            26 Allée Etienne Ventenat <br>
            92500 Rueil Malmaison <br>
            France <br>
            Tel1:<span style="color:dodgerblue">0033(0)184208801</span> <br>
            Tel2:<span style="color:dodgerblue">0033(0)185084639</span> <br>
            Tel3:<span style="color:dodgerblue">0033(0)629632393</span> <br>
            No TVA: FR68533160842 <br>
            Licence No: IM092120003 <br>
        </p>
        <br>
        <a href="https://cityzore.com">See our other tours!</a>
        <p>Follow us on Social Media!</p>
        <p>
            <a href="https://www.facebook.com/pariscitytours.fr/" target="blank"><img src="{{url('https://cityzore.com/img/facebook.png')}}" width="55px" height="55px"></a>
            <a href="https://www.instagram.com/pariscitytours.fr/" target="blank"><img src="{{url('https://cityzore.com/img/instagram.png')}}" width="52px" height="52px"></a>
            <a href="https://twitter.com/Parisviptrips" target="blank"><img src="{{url('https://cityzore.com/img/twitter.png')}}" width="55px" height="55px"></a>
        </p>
    </div>
</body>
</html>
