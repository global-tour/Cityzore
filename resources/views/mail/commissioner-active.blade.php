<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Cityzore-Agency Active</title>
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
    <h3>Dear {{$data['name']}}, </h3>
    <p>We activated your account.</p>
    <p>You can buy tickets and sell them right now.</p>
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
