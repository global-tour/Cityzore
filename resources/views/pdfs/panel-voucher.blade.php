<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <style type="text/css" media="all">
        body {
            width:100%;
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }
        footer {
            position: fixed;
            bottom: -35px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            text-align: center;
            border-top: 1px solid black;
        }
        .center {
            text-align: center;
        }
        .margin {
            padding-left : 5%;
        }
        .cruise {
            left:0px;
            width: 60%;
            position: relative;
            margin-bottom:2%;
            border: 3px dotted darkgray;
        }
        .top-left {
            position: absolute;
            top: 0px;
            padding-left:50px;
            float: left;
        }

        .top-left-code {
            position: absolute;
            top: 50px;
            margin-top:0px;
        }

        .opera-image {
            position: relative;
        }

        .opera-text {
            position: absolute;
            top: 188px;
            left: 220px;
            font-size: 9px;
            font-weight: bold;
        }

        .price{
            position: absolute;
            top: 198px;
            right: 235px;
            font-size: 11px;
            font-weight: bold;
        }

        .invitation {
            position: absolute;
            top: 188px;
            right: 235px;
            font-size: 9px;
        }

        .barcode-1 {
            position: absolute;
            top: 297px;
            left: 212px;
            width: 180px;
        }

        .barcode-text {
            position: absolute;
            top: 286px;
            right: 230px;
            font-size: 8px;
            color:white;
        }

        .barcode-text-right {
            position: absolute;
            top: 286px;
            left: 565px;
            font-size: 8px;
            color:white;
        }

        .barcode-text-date {
            position: absolute;
            top: 296px;
            right: 230px;
            font-size: 8px;
            color:white;
        }

        .barcode-date-right {
            position: absolute;
            top: 296px;
            left: 565px;
            font-size: 8px;
            color:white;
        }

        .barcode-text-licence {
            position: absolute;
            top: 306px;
            right: 230px;
            font-size: 8px;
            color:white;
        }

        .barcode-licence-right {
            position: absolute;
            top: 306px;
            left: 565px;
            font-size: 8px;
            color:white;
        }

        .right-barcode{
            position: absolute;
            top: 140px;
            left: 550px;
            width: 255px;
            height: 35px;
            transform:          rotate(270deg);
            -ms-transform:      rotate(270deg);
            -moz-transform:     rotate(270deg);
            -webkit-transform:  rotate(270deg);
            -o-transform:       rotate(270deg);
        }
        header {
            position: fixed;
            top: -34px;
            left: 0px;
            right: 0px;
        }

        .title {
            margin-top: 3%;
            margin-left: 30px;
        }

        .information {
            margin-left: 3%;
            font-weight: normal;
            font-size: 12px;
        }

        .address {
            margin-left: 40px;
            position: absolute;
            font-weight: normal;
            font-size: 12px;
        }

        .opera-price {
            margin-top:2%;
            font-size: 16px;
        }

        p { page-break-before: never; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<header>
    <div class="row" style="width:100%;">
        <div class="col-md-6" style="float: left; font-size: 14px;">
            <span>@if(!($voucher->bookingRefCode==null))Reference No: {{$voucher->bookingRefCode}}@endif</span>
        </div>
        <div class="col-md-6" style="float: right;font-size: 14px;">
            <span>@if(!($voucher->traveler==null))Traveler: {{$voucher->traveler}}@endif</span>
        </div>
    </div>
    <div class="row" style="margin-top:35px;padding-top:5px;">
        <div class="col-lg-12">
            <h1 style="border-bottom: 1px solid black; border-top: 1px solid black; text-align: center;">TICKET</h1>
        </div>
    </div>
</header>
<footer class="center" style="font-size: 11px;">
    cityzore.com is website of Paris Business and Travel. www.cityzore.com 26 Allee Etienne Ventenat 92500 Rueil Malmaison, France
    <br>
    Tel:+33184208801 Licence No:IM09212003 VAT:FR68533160842
</footer>
<main>
    <p>
        <table style="width:100%">
            <tr>
                <th height="25%" width="25%">
                    <img src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>
                </th>
                <th width="75%" style="float: left; text-align: center;">
                    <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                    <h2>{{$product->title}}</h2>
                    <p style="font-weight: normal;">@if(!($options==null)){{$options->title}}@endif</p>
                </th>
            </tr>
        </table>
        <table style="width:100%; border-bottom: 1px solid black;">
            <tr style="width:100%;">
                <td class="center" style="font-size: 14px;width:50%;">Travel Date</td>
                <td class="center" style="font-size: 14px; width:50%;">Travel Time</td>
                <td class="center" style="font-size: 14px; width:50%;">Participants</td>
            </tr>
            <tr style=" background-color: #FFFAFA;">
                <td class="center" style="border-top: 1px solid black;">{{$dateV}}</td>
                <td class="center" style="border-top: 1px solid black;">@if(!(json_decode($voucher->dateTime, true)['time']) == null){{date('H:i', strtotime(json_decode($voucher->dateTime, true)['time']))}}@endif</td>
                <td class="center" style="border-top: 1px solid black;">
                    @foreach(json_decode($voucher->participants, true) as $participants)
                        @if(!($participants['adult']==null))
                            Adult : {{$participants['adult']}}
                        @endif
                        @if(!($participants['youth']==null))
                            Youth : {{$participants['youth']}}
                        @endif
                        @if(!($participants['child']==null))
                            Child : {{$participants['child']}}
                        @endif
                        @if(!($participants['infant']==null))
                            Infant : {{$participants['infant']}}
                        @endif
                        @if(!($participants['euCitizen']==null))
                            Europe Citizen : {{$participants['euCitizen']}}
                        @endif
                    @endforeach
                </td>
            </tr>
        </table>
        <table style="width:100%;">
            <tr>
                <td>
                    <h3 class="center" style="font-size: 12px;">Additional Information</h3>
                </td>
            </tr>
            @if(!($options==null))
                <tr>
                    <td style="font-weight: bold">Address :</td>
                </tr>
                <tr>
                    <td class="margin" style="font-size: 12px;">
                        @if($options->meetingPoint == null)
                        -{{$options->meetingPointDesc}}
                        @else
                        -{{$options->meetingPoint}}
                        @endif
                    </td>
                </tr>
                @if(!($options->meetingComment==null))
                    <tr>
                        <td style="font-weight: bold">Important Information :</td>
                    </tr>
                    <tr>
                        <td class="margin" style="font-size: 12px;">
                            @for($i=0;$i<count(explode('|', $options->meetingComment));$i++)
                                -{{explode('|', $options->meetingComment)[$i]}} <br>
                            @endfor
                        </td>
                    </tr>
                @endif
            @endif
            <tr>
                <td style="font-weight: bold">Cancellation Policy : </td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-{{$product->cancelPolicy}}</span></td>
            </tr>
        </table>
        <table style="width: 100%; border-top: 1px dotted black;border-bottom: 1px dotted black;">
            <tr style="width: 100%;vertical-align: text-top;">
                <td style="font-weight: bold;width: 50%;">What's Included</td>
                <td style="font-weight: bold;width: 50%;">What's Not Included</td>
            </tr>
            <tr style="width: 100%;vertical-align: text-top;">
                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                    @for($i=0;$i<count(explode('|', $options->included));$i++)
                        -{{explode('|', $options->included)[$i]}} <br>
                    @endfor
                </td>
                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                    @for($i=0;$i<count(explode('|', $options->notIncluded));$i++)
                        -{{explode('|', $options->notIncluded)[$i]}} <br>
                    @endfor
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="font-weight: bold">Contact Information</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 12px;">
                    -Phone Number: {{$product->phoneNumber}} <br>
                    -E-mail Address: contact@cityzore.com
                </td>
            </tr>
        </table>
    </p>
</main>
</body>
