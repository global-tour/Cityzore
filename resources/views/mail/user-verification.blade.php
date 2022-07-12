<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- GOOGLE FONTS -->
    <title>Cityzore-Shared Cart</title>
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
<table align="center">
    <tbody>
    <tr>
        <td align="center" valign="top">
            <table style="max-width:600px;text-align:left">
                <tbody>
                <tr>
                    <td style="padding:16px 0 0;text-align:center" align="center">
                        <a style="display:inline-block;height:48px;width:144px" href="https://cityzore.com" target="_blank">
                            <img style="height:48px;width:144px" align="center" alt="Cityzore" src="https://cityzore.com/img/paris-city-tours-logo.png" width="144">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 0 0;text-align:center" align="center">
                        <p style="color:hsl(0,0%,24%);font-size:18px;font-family:BlinkMacSystemFont,'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;line-height:22.5px;margin:0">
                            Dear {{$data['firstName']}} {{$data['lastName']}},
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:50px 18px 0">
                        <div style="margin:0;padding:0;text-align:center">
                            <a style="background:hsl(348,78%,98%);border:2px solid hsl(348,48%,96%);border-radius:4px;color:hsl(348,78%,56%);display:block;font-size:14px;font-weight:bold;line-height:22.5px;text-decoration:none;font-family:BlinkMacSystemFont,'Helvetica Neue',Helvetica,Arial,sans-serif;padding:12px" href="{{$data['verificationLink']}}" target="_blank">
                                Please click this link to verification your account
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:38px 18px">
                        <div style="border-top:1px solid hsl(0,0%,88%)"></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 18px 38px">
                        <table style="width:100%">
                            <tbody>
                            <tr>
                                <td style="color:hsl(0,0%,50%);font-family:BlinkMacSystemFont,'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:18.75px;text-align:center">
                                    <p style="margin:0;padding:0">
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
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
