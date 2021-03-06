<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ticket</title>
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
            top: 150px;
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
            @if($booking->gygBookingReference == null)
                <span>Reference No: {{$bkn}}</span>
            @else
                <span>Reference No: {{$booking->gygBookingReference}}</span>
            @endif
        </div>
        <div class="col-md-6" style="float: right;font-size: 14px;">
            <span>Traveler: {{$travelerName}} {{$travelerLastname}}</span>
        </div>
    </div>
    <div class="row" style="margin-top:35px;padding-top:5px;">
        <div class="col-lg-12">
            <h1 style="border-bottom: 1px solid black; border-top: 1px solid black; text-align: center;">E-TICKET</h1>
        </div>
    </div>
</header>
<footer class="center" style="font-size: 11px;">
    pariscitytours.fr is website of Paris Business and Travel. www.pariscitytours.fr 26 Allee Etienne Ventenat 92500 Rueil Malmaison, France
    <br>
    Tel:+33184208801 Licence No:IM09212003 VAT:FR68533160842
</footer>
<main>

    @php
        $montArr = [];
        $montIndex = 0;
    @endphp

    @foreach(json_decode($booking->bookingItems, true) as $participants)
        @for($r=0; $r<$participants['count']; $r++)
            @php
                array_push($montArr, $participants['category']);
            @endphp
        @endfor
    @endforeach

    @if(!($ticketTypeArr == null))
        @foreach($ticketTypeArr as $ticketType)
            @if((($ticketType == '5' || $ticketType == '3' || $ticketType == '20' || $ticketType == '19' || $ticketType == '17' || $ticketType == '18' || $ticketType == '16' || $ticketType == '6' || $ticketType == '25'  || $ticketType == '27' || $ticketType == '29' || $ticketType == '28' || $ticketType == '30' || $ticketType == '32' || $ticketType == '33' || $ticketType == '34')))
                @for($i=0; $i<$participantSum; $i++)
                    <p>
                        <table style="width:100%">
                            <tr>
                                <th height="25%" width="25%">

                                        <img src="{{$productImage}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>

                                </th>
                                <th width="75%" style="float: left; text-align: center;">
                                    @if($url == 'cityzore')
                                        <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @elseif($url == 'pariscitytours')
                                        <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @else
                                        <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @endif
                                    <h2>{{isset($product) ? $product->title : "-"}}</h2>
                                    <span>For: {{\App\TicketType::where('id', '=', $ticketType)->first()->name}}</span><br>
                    <p style="font-weight: normal;">{{$options->title}}</p>
                    @if($ticketType == '5')
                        <div>VN Passeport</div>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($versaillesBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '"></div>
                        <div>{{$versaillesBarcode[$i]['code']}}</div>
                    @endif
                    @if($ticketType == '6')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($arcdeTriomphe[$i]['code'])) !!} "></div>
                        <div>{{$arcdeTriomphe[$i]['code']}}</div>
                    @elseif($ticketType == '27')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($pompidou[$i]['code'])) !!} "></div>
                        <div>{{$pompidou[$i]['code']}}</div>
                    @elseif($ticketType == '25')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($grevin[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$grevin[$i]['code']}}</div>
                    @elseif($ticketType == '29')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($picasso[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$picasso[$i]['code']}}</div>
                    @elseif($ticketType == '28')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($rodin[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$rodin[$i]['code']}}</div>
                    @elseif($ticketType == '30')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($montparnasse[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$montparnasse[$i]['code']}}</div>
                    @elseif($ticketType == '32')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($orsay[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$orsay[$i]['code']}}</div>
                    @elseif($ticketType == '33')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($orangerie[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$orangerie[$i]['code']}}</div>
                    @elseif($ticketType == '34')
                        <p>Barcode:</p>
                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($museedelarme[$i]['code'], $generator::TYPE_CODE_128))}} . '" style="margin-top: 5%;width: 200px; height: 40px;"></div>
                        <div style="margin-top: 2%;">{{$museedelarme[$i]['code']}}</div>
                    @elseif($ticketType == '20')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($sainteChapelleBarcode[$i]['code'])) !!} "></div>
                        <div>{{$sainteChapelleBarcode[$i]['code']}}</div>
                    @elseif($ticketType == '16')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($basiliqueBarcode[$i]['code'])) !!} "></div>
                        <div>{{$basiliqueBarcode[$i]['code']}}</div>
                    @elseif($ticketType == '18')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($pantheonBarcode[$i]['code'])) !!} "></div>
                        <div>{{$pantheonBarcode[$i]['code']}}</div>
                    @elseif($ticketType == '19')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($sainteChapelleConciergerieBarcode[$i]['code'])) !!} "></div>
                        <div>{{$sainteChapelleConciergerieBarcode[$i]['code']}}</div>
                    @elseif($ticketType == '17')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($conciergerieBarcode[$i]['code'])) !!} "></div>
                        <div>{{$conciergerieBarcode[$i]['code']}}</div>
                    @elseif($ticketType == '3')
                        <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($bigBus[$i]['code'])) !!} "></div>
                        <div>{{$bigBus[$i]['code']}}</div>
                    @endif
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
                                <td class="center" style="border-top: 1px solid black;">{{ \Illuminate\Support\Carbon::make(is_array(json_decode($booking->dateTime))?json_decode($booking->dateTime, true)[0]['dateTime']:$booking->dateTime)->format('D, d-F-Y')  }}</td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @if($booking->hour)
                                        @foreach(json_decode($booking->hour, true) as $dateTime)
                                            <p><strong>{{$dateTime['hour']}}</strong></p>
                                        @endforeach
                                    @else
                                        @php
                                            if(strpos($booking->dateTime, "dateTime") !== false){
                                                $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }else{
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }

                                            if(explode('+', $explodedHour)[0]=='00:00:00')
                                                $explodedHour="Operating Hours";
                                            else
                                                $explodedHour=explode('+', $explodedHour)[0];
                                        @endphp
                                        <p><strong>{{$explodedHour}}</strong></p>
                                    @endif
                                </td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @if($ticketType == 30)
                                        @foreach($montArr as $montKey => $montEl)
                                            @if($montKey == $montIndex)
                                                {{$montEl}} : 1 <br>
                                                @php
                                                    $montIndex++;
                                                    break;
                                                @endphp
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach(json_decode($booking->bookingItems, true) as $participants)
                                            {{$participants['category']}} : {{ ($participants['count'] ?? 1) }} <br>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>

                            @if($ticketType == '34')
                                <tr style="height: 30px">
                                    <td colspan="2" style="border-top: 1px solid black" align="right">Amount: </td>
                                    <td colspan="1" style="border-top: 1px solid black" align="center">
                                        @foreach($montArr as $montKey => $montEl)
                                            @if($montKey == $montIndex)
                                                @foreach($museedelarmePricing as $mt => $tt)
                                                    @if(strtolower($montEl) == $mt) {{ $tt .' '. $booking->currency->currency }} @endif<br>
                                                @endforeach
                                                @php
                                                    $montIndex++;
                                                    break;
                                                @endphp
                                            @endif
                                        @endforeach
                            @endif

                            @if($ticketType == '3' || $ticketType == '34')
                                <tr style="">
                                    <td colspan="3" align="right" style="border-top: 1px solid black">
                                        <span>{{($i+1)}} / {{$participantSum}}</span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <h3 class="center" style="font-size: 12px;">Additional Information</h3>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Address :</td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;">
                                    @if($options->addresses)
                                        @php
                                            $addresses = json_decode($options->addresses, true);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            -{{$address["address"]}}<br>
                                        @endforeach
                                    @else
                                        @if($options->meetingPoint == null)
                                            -{{$options->meetingPointDesc}}
                                        @else
                                            -{{$options->meetingPoint}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @if(!($options->meetingComment==null))
                                <tr>
                                    <td style="font-weight: bold">Important Information :</td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 12px;">
                                        @for($m=0;$m<count(explode('|', $options->meetingComment));$m++)
                                            -{{explode('|', $options->meetingComment)[$m]}} <br>
                                        @endfor
                                    </td>
                                </tr>
                            @endif
                            @if(!($booking->specialRefCode == null))
                                <tr>
                                    <td style="font-weight: bold">Special Reference Code:</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">{{$booking->specialRefCode}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold">Cancellation Policy : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-{{$options->cancelPolicy}}</span></td>
                            </tr>
                        </table>
                        <table style="width: 100%; border-top: 1px dotted black;border-bottom: 1px dotted black;">
                            <tr style="width: 100%;vertical-align: text-top;">
                                <td style="font-weight: bold;width: 50%;">What's Included</td>
                                <td style="font-weight: bold;width: 50%;">What's Not Included</td>
                            </tr>
                            <tr style="width: 100%;vertical-align: text-top;">
                                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                                    @for($y=0;$y<count(explode('|', $options->included));$y++)
                                        -{{explode('|', $options->included)[$y]}} <br>
                                    @endfor
                                </td>
                                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                                    @for($x=0;$x<count(explode('|', $options->notIncluded));$x++)
                                        -{{explode('|', $options->notIncluded)[$x]}} <br>
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
                                    -Phone Number: {{isset($product) ? json_decode($product->phoneNumber, true)[0] : "+33184208801"}} <br>
                                    -E-mail Address: contact@cityzore.com
                                </td>
                            </tr>
                            @if($ticketType == '3')
                                <tr>
                                    <td>
                                        <img style="position:relative; width:100%; margin-top:60px; margin-bottom:240px;" src="{{public_path('img/big-bus-map.png')}}">
                                    </td>
                                </tr>
                            @endif
                        </table>
                        </p>
                        @endfor





                @elseif($ticketType == "24")



                  @for($i=0; $i<$participantSum; $i++)
                    <p>
                        <table style="width:100%">
                            <tr>
                                <th height="25%" width="25%">
                                        <img src="{{$productImage}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>

                                </th>
                                <th width="75%" style="float: left; text-align: center;">
                                    @if($url == 'cityzore')
                                        <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @elseif($url == 'pariscitytours')
                                        <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @else
                                        <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                    @endif
                                    <h2>{{isset($product) ? $product->title : "-"}}</h2>
                                    <span>For: {{\App\TicketType::where('id', '=', $ticketType)->first()->name}}</span><br>
                    <p style="font-weight: normal;">{{$options->title}}</p>

                    @php
                        $decoded_data = json_decode($booking->tootbus_booking_response, true);

                    @endphp

                                     <div><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(130)->generate($decoded_data["data"]["voucher"]["deliveryOptions"][0]["deliveryValue"])) !!} "></div>

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
                                <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(is_array(json_decode($booking->dateTime))?json_decode($booking->dateTime, true)[0]['dateTime']:$booking->dateTime))}}</td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @if($booking->hour)
                                        @foreach(json_decode($booking->hour, true) as $dateTime)
                                            <p><strong>{{$dateTime['hour']}}</strong></p>
                                        @endforeach
                                    @else
                                        @php
                                            if(strpos($booking->dateTime, "dateTime") !== false){
                                                $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }else{
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }

                                            if(explode('+', $explodedHour)[0]=='00:00:00')
                                                $explodedHour="Operating Hours";
                                            else
                                                $explodedHour=explode('+', $explodedHour)[0];
                                        @endphp
                                        <p><strong>{{$explodedHour}}</strong></p>
                                    @endif
                                </td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @foreach(json_decode($booking->bookingItems, true) as $participants)
                                        {{$participants['category']}} : {{$participants['count']}} <br>
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
                            <tr>
                                <td style="font-weight: bold">Address :</td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;">
                                    @if($options->addresses)
                                        @php
                                            $addresses = json_decode($options->addresses, true);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            -{{$address["address"]}}<br>
                                        @endforeach
                                    @else
                                        @if($options->meetingPoint == null)
                                            -{{$options->meetingPointDesc}}
                                        @else
                                            -{{$options->meetingPoint}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @if(!($options->meetingComment==null))
                                <tr>
                                    <td style="font-weight: bold">Important Information :</td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 12px;">
                                        @for($m=0;$m<count(explode('|', $options->meetingComment));$m++)
                                            -{{explode('|', $options->meetingComment)[$m]}} <br>
                                        @endfor
                                    </td>
                                </tr>
                            @endif
                            @if(!($booking->specialRefCode == null))
                                <tr>
                                    <td style="font-weight: bold">Special Reference Code:</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">{{$booking->specialRefCode}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold">Cancellation Policy : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-{{$options->cancelPolicy}}</span></td>
                            </tr>
                        </table>
                        <table style="width: 100%; border-top: 1px dotted black;border-bottom: 1px dotted black;">
                            <tr style="width: 100%;vertical-align: text-top;">
                                <td style="font-weight: bold;width: 50%;">What's Included</td>
                                <td style="font-weight: bold;width: 50%;">What's Not Included</td>
                            </tr>
                            <tr style="width: 100%;vertical-align: text-top;">
                                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                                    @for($y=0;$y<count(explode('|', $options->included));$y++)
                                        -{{explode('|', $options->included)[$y]}} <br>
                                    @endfor
                                </td>
                                <td style="font-size: 12px;width: 50%;vertical-align: text-top;">
                                    @for($x=0;$x<count(explode('|', $options->notIncluded));$x++)
                                        -{{explode('|', $options->notIncluded)[$x]}} <br>
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
                                    -Phone Number: {{isset($product) ? json_decode($product->phoneNumber, true)[0] : "+33184208801"}} <br>
                                    -E-mail Address: contact@cityzore.com
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="{{public_path('img/big-bus-map.png')}}">
                                </td>
                            </tr>
                        </table>
                        </p>
                        @endfor
                    @elseif($ticketType == '7')
                        @for($i=0; $i<$participantSum; $i++)
                            <table style="width: 100%;">
                                <tr>
                                    <th height="25%" width="25%">
                                        <img src="{{public_path('img/Logo_ONP.JPG')}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>
                                    </th>
                                    <th width="75%" style="float: left; text-align: center;">
                                        @if($url == 'cityzore')
                                            <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @elseif($url == 'pariscitytours')
                                            <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @else
                                            <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @endif
                                        <h2>Visite autonome du Palais Garnier</h2>
                                        <span>For: {{\App\TicketType::where('id', '=', $ticketType)->first()->name}}</span><br>
                                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($operaBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '"></div>
                                        <div><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($operaBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '" class="right-barcode"></div>
                                        <div>{{$operaBarcode[$i]['code']}}</div>
                                        <div class="opera-price">{{round(($booking->totalPrice)/($participantSum), 2)}} &euro;</div>
                                    </th>
                                </tr>
                            </table>
                            <table style="width:100%; border-bottom: 1px solid black; margin-top: 2%;">
                                <tr style="width:100%;">
                                    <td class="center" style="font-size: 14px;width:50%;">Booking Date and Time</td>
                                    <td class="center" style="font-size: 14px; width:50%;">Travel Date and Time</td>
                                    <td class="center" style="font-size: 14px; width:50%;">Participants</td>
                                    <td class="center" style="font-size: 14px; width:50%;">Licence No</td>
                                </tr>
                                <tr style=" background-color: #FFFAFA;">
                                    <td class="center" style="border-top: 1px solid black;">{{date('d/m/Y, h:i', strtotime($booking->created_at))}}</td>
                                    <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(is_array(json_decode($booking->dateTime))?json_decode($booking->dateTime, true)[0]['dateTime']:$booking->dateTime))}}</td>
                                        @if($booking->hour)
                                            @foreach(json_decode($booking->hour, true) as $dateTime)
                                                <p><strong>{{$dateTime['hour']}}</strong></p>
                                            @endforeach
                                        @else
                                            @php
                                                if(strpos($booking->dateTime, "dateTime") !== false){
                                                    $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                    $explodedHour=explode('T', $booking->dateTime)[1];
                                                }else{
                                                    $explodedHour=explode('T', $booking->dateTime)[1];
                                                }

                                                if(explode('+', $explodedHour)[0]=='00:00:00')
                                                    $explodedHour="Operating Hours";
                                                else
                                                    $explodedHour=explode('+', $explodedHour)[0];
                                            @endphp
                                            <p><strong>{{$explodedHour}}</strong></p>
                                        @endif
                                    </td>
                                    <td class="center" style="border-top: 1px solid black;">1</td>
                                    <td class="center" style="border-top: 1px solid black;">n??2-1075039</td>
                                </tr>
                            </table>
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <h3 class="center" style="font-size: 12px;">Information Additionnelle </h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;font-size: 12px;">Addresse : </td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-Angle des rues Scribe et Auber, 75009 Paris</span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;font-size: 12px;">?? savoir</td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 11px;">
                                        - Horaires de visite => Le Palais Garnier est ouvert et accessible ?? la visite : <br>
                                        - de 10 h ?? 17 h (acc??s jusqu????? 16h30) de d??but septembre ?? mi-juillet <br>
                                        - Le Palais Garnier sera ouvert de 10h ?? 18h (dernier accueil des visiteurs ?? 17h00) du 22 juin au 6 septembre 2020. <br>
                                        - Sauf jours de repr??sentations et de fermetures exceptionnelles (avant votre visite, consultez le site operadeparis.fr). <br>
                                        - Vendu ?? l'avance - ni repris, ni ??chang?? <br>
                                        - Ce billet est valable un jour et uniquement pour une entr??e ?? la date s??lectionn??e <br>
                                        - L???acc??s ?? la salle de spectacle peut ??tre restreint ou rendu impossible pour des raisons techniques et/ou artistiques. <br>
                                        - Il est obligatoire ?? partir de 11 ans, de porter un masque tout au long de la visite. <br>
                                        - Les vestiaires sont ferm??s pendant les heures de visite. Il n???est pas possible d???entrer avec une poussette, une trottinette ou tout autre bagage volumineux.
                                    </td>
                                </tr>
                            </table>
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <h3 class="center" style="font-size: 12px;">Additional Information</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;font-size: 12px;">Address : </td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-Angle des rues Scribe et Auber, 75009 Paris</span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;font-size: 12px;">Know Before You Go</td>
                                </tr>
                                <tr>
                                    <td class="margin" style="font-size: 11px;">
                                        - Visiting hours => The Palais Garnier is open and accessible to visitors: <br>
                                        - from 10 a.m. to 5 p.m. (access until 4.30 p.m.) from early September to mid-July <br>
                                        - from 10 a.m. to 6 p.m. (access until 5:00 p.m.) from June 22 to September 6 included <br>
                                        - Except on days of exceptional performances and closings (before your visit, consult the site operadeparis.fr). <br>
                                        - Sold in advance - non refundable - non amendable <br>
                                        - This ticket is valid for one day and only for entry on the selected date <br>
                                        - Access to the performance hall may be restricted or made impossible for technical and / or artistic reasons. <br>
                                        - It is mandatory to wear a mask throughout the visit for people over 11 years old. <br>
                                        - Cloakrooms are closed. It is not allowed to enter with a pushchair, a scooter or any kind of voluminous luggage.
                                    </td>
                                </tr>
                            </table>
                        @endfor
                    @elseif($ticketType == '4')

                        <p>
                            <table style="width:100%">
                                <tr>
                                    <th height="25%" width="25%">
                                        <img src="{{$productImage}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>

                                    </th>
                                    <th width="75%" style="float: left; text-align: center;">
                                        @if($url == 'cityzore')
                                            <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @elseif($url == 'pariscitytours')
                                            <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @else
                                            <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                        @endif
                                        <h2>{{isset($product) ? $product->title : "-"}}</h2>
                                        <span>For: {{\App\TicketType::where('id', '=', $ticketType)->first()->name}}</span><br>
                        <p style="font-weight: normal;">{{$options->title}}</p>
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
                                <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(is_array(json_decode($booking->dateTime))?json_decode($booking->dateTime, true)[0]['dateTime']:$booking->dateTime))}}</td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @if($booking->hour)
                                        @foreach(json_decode($booking->hour, true) as $dateTime)
                                            <p><strong>{{$dateTime['hour']}}</strong></p>
                                        @endforeach
                                    @else
                                        @php
                                            if(strpos($booking->dateTime, "dateTime") !== false){
                                                $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }else{
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }

                                            if(explode('+', $explodedHour)[0]=='00:00:00')
                                                $explodedHour="Operating Hours";
                                            else
                                                $explodedHour=explode('+', $explodedHour)[0];
                                        @endphp
                                        <p><strong>{{$explodedHour}}</strong></p>
                                    @endif
                                </td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @foreach(json_decode($booking->bookingItems, true) as $participants)
                                        {{$participants['category']}} : {{$participants['count']}} <br>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <h3 class="center" style="font-size: 14px;">Additional Information</h3>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Address : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;">
                                    @if($options->addresses)
                                        @php
                                            $addresses = json_decode($options->addresses, true);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            -{{$address["address"]}}<br>
                                        @endforeach
                                    @else
                                        @if($options->meetingPoint == null)
                                            -{{$options->meetingPointDesc}}
                                        @else
                                            -{{$options->meetingPoint}}
                                        @endif
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
                            @if(!($booking->specialRefCode == null))
                                <tr>
                                    <td style="font-weight: bold">Special Reference Code:</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">{{$booking->specialRefCode}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold">Cancellation Policy : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-{{$options->cancelPolicy}}</span></td>
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
                                    -Phone Number: {{isset($product) ? json_decode($product->phoneNumber, true)[0] : "+33184208801"}} <br>
                                    -E-mail Address: contact@cityzore.com
                                </td>
                            </tr>
                        </table>
                        </p>
                    @else
                        <p>
                        <table style="width:100%">
                            <tr>
                                <th height="25%" width="25%">

                                        <img src="{{$productImage}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>

                                </th>
                                <th width="75%" style="float: left; text-align: center;">
                                    <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="width: 200px; height:60px;padding-right: 30%; padding-left: 30%;"/>
                                    <h2>{{isset($product) ? $product->title : "-"}}</h2>
                                    <span>For: {{\App\TicketType::where('id', '=', $ticketType)->first()->name}}</span><br>
                                    <span style="font-weight: normal;">{{$options->title}}</span>
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
                                <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(is_array(json_decode($booking->dateTime))?json_decode($booking->dateTime, true)[0]['dateTime']:$booking->dateTime))}}</td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @if($booking->hour)
                                        @foreach(json_decode($booking->hour, true) as $dateTime)
                                            <p><strong>{{$dateTime['hour']}}</strong></p>
                                        @endforeach
                                    @else
                                        @php
                                            if(strpos($booking->dateTime, "dateTime") !== false){
                                                $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }else{
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }

                                            if(explode('+', $explodedHour)[0]=='00:00:00')
                                                $explodedHour="Operating Hours";
                                            else
                                                $explodedHour=explode('+', $explodedHour)[0];
                                        @endphp
                                        <p><strong>{{$explodedHour}}</strong></p>
                                    @endif
                                </td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @foreach(json_decode($booking->bookingItems, true) as $participants)
                                        {{$participants['category']}} : {{$participants['count']}} <br>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <h3 class="center" style="font-size: 14px;">Additional Information</h3>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Address : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;">
                                    @if($options->addresses)
                                        @php
                                            $addresses = json_decode($options->addresses, true);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            -{{$address["address"]}}<br>
                                        @endforeach
                                    @else
                                        @if($options->meetingPoint == null)
                                            -{{$options->meetingPointDesc}}
                                        @else
                                            -{{$options->meetingPoint}}
                                        @endif
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
                            @if(!($booking->specialRefCode == null))
                                <tr>
                                    <td style="font-weight: bold">Special Reference Code:</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">{{$booking->specialRefCode}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold">Cancellation Policy : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 12px;"><span style="font-weight: normal;">-{{$options->cancelPolicy}}</span></td>
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
                                    -Phone Number: {{isset($product) ? json_decode($product->phoneNumber, true)[0] : "+33184208801"}} <br>
                                    -E-mail Address: contact@cityzore.com
                                </td>
                            </tr>
                        </table>
                        </p>
                    @endif
                    @endforeach
                    @elseif($ticketTypeArr == null)
                        @if(($booking->gygBookingReference == null) && $booking->isBokun == 0 && $booking->isViator == 0)
                            <p>
                                <table style="width:100%">
                                    <tr>
                                        <th height="25%" width="25%">

                                                <img src="{{$productImage}}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:4%;"/>

                                        </th>
                                        <th width="75%" style="float: left; text-align: center;">
                                            @if($url == 'cityzore')
                                                <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                            @elseif($url == 'pariscitytours')
                                                <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                            @else
                                                <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                            @endif                        <h2 style="font-size:16px;">{{$product->title}}</h2>
                            <p style="font-weight: normal;">{{$options->title}}</p>
                            @if($options->mobileBarcode == 1)
                                <div style="padding-left:30px; margin-top: 3%;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($bkn, $generator::TYPE_CODE_128))}} . '"></div>
                            @endif
                            </th>
                            </tr>
                            </table>
                            <table style="width:100%; border-bottom: 1px solid black;">
                                <tr style="width:100%;">
                                    <td class="center" style="font-size: 14px;width:50%;">Travel Date</td>
                                    <td class="center" style="font-size: 14px; width:50%;">Travel Time</td>
                                    <td class="center" style="font-size: 14px; width:50%;">Participants</td>
                                </tr>
                                <tr style="background-color: #FFFAFA;">
                                    <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</td>
                                    <td class="center" style="border-top: 1px solid black;">
                                        @foreach(json_decode($booking->hour, true) as $dateTime)
                                            <p><strong>{{$dateTime['hour']}}</strong></p>
                                        @endforeach
                                    </td>
                                    <td class="center" style="border-top: 1px solid black;">
                                        @foreach(json_decode($booking->bookingItems, true) as $participants)
                                            {{$participants['category']}} : {{$participants['count']}} <br>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <h3 class="center" style="font-size: 14px;">Additional Information</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Address : </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">
                                        @if($options->addresses)
                                            @php
                                                $addresses = json_decode($options->addresses, true);
                                            @endphp
                                            @foreach ($addresses as $address)
                                                -{{$address["address"]}}<br>
                                            @endforeach
                                        @else
                                            @if($options->meetingPoint == null)
                                                -{{$options->meetingPointDesc}}
                                            @else
                                                -{{$options->meetingPoint}}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @if(!($booking->specialRefCode == null))
                                    <tr>
                                        <td style="font-weight: bold">Special Reference Code:</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px;">{{$booking->specialRefCode}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="font-weight: bold">Cancellation Policy : </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;"><span style="font-weight: normal;">-{{$options->cancelPolicy}}</span></td>
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
                            @if(!($rCode == null))
                                <table style="width: 100%;border-bottom: 1px dotted black;">
                                    <tr>
                                        <td style="font-weight: bold">Restaurant Reservation Code</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px;">
                                            -{{$rCode->rCode}}
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            <table>
                                <tr>
                                    <td style="font-weight: bold">Contact Information</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px;">
                                        -Phone Number: {{json_decode($product->phoneNumber, true)[0]}} <br>
                                        -E-mail Address: contact@cityzore.com
                                    </td>
                                </tr>
                            </table>
                            </p>
                        @elseif(!($booking->gygBookingReference == null) || $booking->isBokun == 1 || $booking->isViator == 1)
                            <p>
                                <table style="width:100%">
                                    <tr>
                                        <th width="75%" style="float: left; text-align: center;">
                                            @if($url == 'cityzore')
                                                <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                            @elseif($url == 'pariscitytours')
                                                <img src="{{ public_path('img/paris-city-tours-logo2.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                                            @else
                                                <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>
                        @endif
                        <p style="font-weight: normal;">{{$options->title}}</p>
                            @if($options->mobileBarcode == 1)
                                @if(!($booking->gygBookingReference == null))
                                    <div style="padding-left:30px; margin-top: 3%;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($booking->gygBookingReference, $generator::TYPE_CODE_128))}} . '"></div>
                                @elseif($booking->isBokun == 1 || $booking->isViator == 1)
                                    <div style="padding-left:30px; margin-top: 3%;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($booking->bookingRefCode, $generator::TYPE_CODE_128))}} . '"></div>
                                @endif
                            @endif
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

                                @php
                                     if(strpos($booking->dateTime, "dateTime") !== false){
                                                $dateTime = json_decode($booking->dateTime, true)[0]["dateTime"];

                                            }else{
                                                $dateTime=$booking->dateTime;
                                            }
                                @endphp
                                <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(explode('T', $dateTime)[0]))}}</td>
                                <td class="center" style="border-top: 1px solid black;">
                                    <p>
                                        <strong>
                                            <?php

                                            if(strpos($booking->dateTime, "dateTime") !== false){
                                                $explodedHour = json_decode($booking->dateTime, true)[0]["dateTime"];
                                                 $explodedHour=explode('T', $booking->dateTime)[1];
                                            }else{
                                                $explodedHour=explode('T', $booking->dateTime)[1];
                                            }



                                             ?>
                                            @if(explode('+', $explodedHour)[0]=='00:00:00')
                                                Operating Hours
                                            @else
                                                {{explode('+', $explodedHour)[0]}}
                                            @endif
                                        </strong>
                                    </p>
                                </td>
                                <td class="center" style="border-top: 1px solid black;">
                                    @foreach(json_decode($booking->bookingItems, true) as $participants)
                                        {{$participants['category']}} : {{$participants['count']}} <br>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <h3 class="center" style="font-size: 14px;">Additional Information</h3>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Address : </td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 14px;">
                                    @if($options->addresses)
                                        @php
                                            $addresses = json_decode($options->addresses, true);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            -{{$address["address"]}}<br>
                                        @endforeach
                                    @else
                                        @if($options->meetingPoint == null)
                                            -{{$options->meetingPointDesc}}
                                        @else
                                            -{{$options->meetingPoint}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Comment :</td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 14px;">
                                    @for($i=0;$i<count(explode('|', $options->meetingComment));$i++)
                                        -{{explode('|', $options->meetingComment)[$i]}} <br>
                                    @endfor
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Contact Information</td>
                            </tr>
                            <tr>
                                <td class="margin" style="font-size: 14px;">
                                    -Phone Number: +33184208801 <br>
                                    -E-mail Address: contact@cityzore.com
                                </td>
                            </tr>
                        </table>
                        </p>
                    @endif
                    @endif
                    @if(in_array('4', $ticketTypeArr))
                        <p>
                        <table style="width:100%;margin-top:10%; table-layout:fixed;">
                            <tr>
                                @for($i=0; $i<$participantSum; $i++)
                                    <th>
                                        <div style="position:relative; margin-bottom:3%; margin-top: 3%;margin-left: 0px; background-image: url('{{public_path('img/ticket.png')}}'); background-repeat: no-repeat; background-size: 100% auto;
                                         border: 4px dotted slategray;margin-left: 0px; height: 200px; width: 100%;">


                                        <div style="position:absolute; top:17px; padding-left: 15px; font-weight: normal;font-size: 10px;">Billet pour 1 Adulte</div>
                                        <div style="position:absolute; top:32px; padding-left: 15px;font-weight: normal;font-size: 10px;">Valable jusqu'au
                                            {{$cruiseBarcode[$i]['endTime']}}</div>


                                        <div style="position:absolute; top:47px; padding-left: 15px; font-weight: normal;font-size: 10px;">Num??ro de r??servation </div>

                                        <div class="bottom-group" style="margin-top: 13%;">
                                        <div style="padding-left: 26px; margin-top: 0%;">{{$cruiseBarcode[$i]['code']}}</div>
                                        <div style="padding-left: 26px; margin-top: 0%;">{{$travelerName}} {{$travelerLastname}}</div>
                                        <div style="padding-left:26px; margin-top: 0%;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($cruiseBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '"></div>
                                        </div>

                                        </div>


                                    </th>

                                    @if($participantSum == 1)

                                      <th style="visibility: hidden;">
                                        <div style="position:relative; margin-bottom:3%; margin-top: 3%;margin-left: 0px; background-image: url('{{public_path('img/ticket.png')}}'); background-repeat: no-repeat; background-size: 100% auto;
                                         border: 4px dotted slategray;margin-left: 0px; height: 200px; width: 100%;">


                                        <div style="position:absolute; top:17px; padding-left: 15px; font-weight: normal;font-size: 10px;">Billet pour 1 Adulte</div>
                                        <div style="position:absolute; top:32px; padding-left: 15px;font-weight: normal;font-size: 10px;">Valable jusqu'au
                                            {{$cruiseBarcode[$i]['endTime']}}</div>


                                        <div style="position:absolute; top:47px; padding-left: 15px; font-weight: normal;font-size: 10px;">Num??ro de r??servation </div>

                                        <div class="bottom-group" style="margin-top: 13%;">
                                        <div style="padding-left: 26px; margin-top: 0%;">{{$cruiseBarcode[$i]['code']}}</div>
                                        <div style="padding-left: 26px; margin-top: 0%;">{{$travelerName}} {{$travelerLastname}}</div>
                                        <div style="padding-left:26px; margin-top: 0%;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($cruiseBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '"></div>
                                        </div>

                                        </div>


                                    </th>


                                    @endif





                                    @if ($i % 2 == 1)
                            </tr><tr>
                            @endif
                            @endfor
                        </table>
                        </p>
                    @endif
</main>
</body>
