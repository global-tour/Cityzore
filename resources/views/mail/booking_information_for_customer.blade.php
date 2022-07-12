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
    <div class="col-lg-12">
        <img src="{{url('https://cityzore.com/img/paris-city-tours-logo.png')}}" class="logo" style="width: 200px; height:60px;"/>
        <img src="{{url('https://cityzore.com/mail/r/'.$data["mail_code"])}}" width="1" height="1">
    </div>
    <div class="col-lg-12">

        @php
           /*
            $html = "<br><br>";
            $bookingExtraFiles = \App\Booking::findOrFail($data["booking_id"])->extra_files;
            if($bookingExtraFiles->count()) {
                $sayac = 1;
                foreach($bookingExtraFiles as $file) {
                    $html.= $file->image_base_name.": <a href='".$file->image_name."' > view barcode </a> <br><br>";
                    $sayac++;
                }
            }
            */
        @endphp
        {!!nl2br($data["mail_message"])!!}


    </div>

    <div class="col-lg-12 information">
        <p>You can see all of your bookings in <a href="https://cityzore.com/my-profile">your profile</a></p>
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
