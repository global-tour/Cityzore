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
    <div class="col-lg-12 text-center" >
        <img align="center" src="{{url('https://cityzore.com/img/paris-city-tours-logo.png')}}" class="logo" style="width: 200px; height:60px;"/>
    </div>
    <div class="col-lg-12 text-center">
        <h3 style="font-weight: bold">This Mail Redirected Cityzore Error Page</h3>
        <h3 style="font-weight: bold" >Guest Mail Adress</h3>
        <a href="mailto:{{$data['mail']}}">{{$data['mail']}}</a>
        <h3 style="font-weight: bold" >Guest Mail Message</h3>
        <p>{{$data['message']}}</p>
    </div>
</body>
</html>
