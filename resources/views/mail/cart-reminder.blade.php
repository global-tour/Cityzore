
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- GOOGLE FONTS -->
    <title>Cityzore-Booking Cancelled</title>
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
</div>
<div class="col-lg-12">
    <h3>Dear {{$data['name']}} {{$data['surname']}}, </h3>
    <p>There are items in your cart!</p>
    <p>This is the option on your cart : {{$data['optionTitle']}}</p>
    <a href="https://cityzore.com/cart" style="padding: 1%;background-color: #0727a8;color: white;display: flow-root;position: relative;bottom: 0px;border-radius: 5px;text-align: center;width: 90%;text-decoration: none;" >Continue Shopping</a>
</div>
<div class="col-lg-12 information">
    <p>​PARIS BUSINESS AND TRAVEL <br>
        26 Allée Etienne Ventenat <br>
        92500 Rueil Malmaison <br>
        France <br>
        Tel1: <a href="tel:0033184208801">0033(0)184208801</a> <br>
        Tel2: <a href="tel:0033185084639">0033(0)185084639</a> <br>
        Tel3: <a href="tel:0033629632393">0033(0)629632393</a> <br>
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
