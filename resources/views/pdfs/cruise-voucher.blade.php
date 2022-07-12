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
            padding-left: 5%;
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

        p { page-break-before: never; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<footer class="center">
    cityzore.com is website of Paris Business and Travel. www.cityzore.com 26 Allee Etienne Ventenat 92500 Rueil Malmaison, France
    <br>
    Tel:+33184208801 Licence No:IM09212003 VAT:FR68533160842
</footer>
<main>
    <p>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6" style="float: left;">
                    <span>Reference No: {{$bkn}}</span>
                </div>
                <div class="col-md-6" style="float: right;">
                    <span>Traveler: {{$travelerName}} {{$travelerLastname}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h1 style="border-bottom: 1px solid black; border-top: 1px solid black; text-align: center">TICKET</h1>
                </div>
            </div>
            <div class="row">
            </div>
        </div>
        <table style="width:100%">
            <tr>
                <th height="25%" width="25%">
                @foreach($productImage as $productImg)
                    @if($productImg->isCoverPhoto == 1)
                        <img src="{{ 'C:/wamp/www/Global-Tours/public/product-images/'.$productImg->name }}" style="border-radius:5px; width: 220px; height: 220px; position: absolute; margin-top:2%;"/>
                    @endif
                @endforeach
                </th>
                <th width="75%" style="float: left; text-align: center;">
                    <img src="{{ 'C:/wamp/www/Global-Tours/public/img/paris-city-tours-logo.png' }}" style="padding-right: 30%; padding-left: 30%;"/>
                        <h2>{{$product->title}}</h2>
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
                <td class="center" style="border-top: 1px solid black;">{{date('D, d-F-Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</td>
                <td class="center" style="border-top: 1px solid black;">{{date('H:i', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</td>
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
                <td style="font-weight: bold">Meeting Point : <span style="font-weight: normal;">{{$options->meetingPoint}}</span></td>
            </tr>
            @if($booking->gygBookingReference == null)
            <tr>
                <td style="font-weight: bold">Highlights</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    -{{$product->highlights}}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold">Know Before You Go</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    -{{$options->knowBeforeYouGo}}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold">What's Included</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    - {{$options->included}}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold">What's Not Included</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    - {{$options->notIncluded}}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold">Cancel Policy : <span style="font-weight: normal;">{{$product->cancelPolicy}}</span></td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold">Contact Information</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    -Phone Number: {{$product->phoneNumber}} <br>
                    -E-mail Address: contact@cityzore.com
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold">Company Information</td>
            </tr>
            <tr>
                <td class="margin" style="font-size: 14px;">
                    Paris Business and Travel <br>
                    26 Allee Etienne Ventenat, <br>
                    92500 Rueil Malmaison, FR <br>
                    +33184208801
                </td>
            </tr>
        </table>
    </p>
    @endif
    <p>
        <table style="width:100%">
            <tr>
            @for($i=0; $i<$participantSum; $i++)
                @if(in_array('Cruise Ticket', $ticketTypeArr))
                    <th>
                        <div style="position:relative;margin-bottom:3%">
                            <img src="C:/wamp/www/Global-Tours/public/img/ticket.png" width="350px" style="border: 4px dotted slategray">
                        </div>
                        <div style="position:absolute;top:20px;padding-left: 18px;font-weight: normal;font-size: 10px;">Billet pour 1 Adulte</div>
                        <div style="position:absolute;top:35px;padding-left: 18px;font-weight: normal;font-size: 10px;">Valable jusqu'au 31/12/2019</div>
                        <div style="position:absolute;top:50px;padding-left: 18px;font-weight: normal;font-size: 10px;">Numéro de réservation </div>
                        <div style="position:absolute;top:120px;padding-left: 135px;">{{$cruiseBarcode[$i]['code']}}</div>
                        <div style="position: absolute;top:150px;padding-left:30px;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($cruiseBarcode[$i]['code'], $generator::TYPE_CODE_128))}} . '"></div>
                    </th>
                @endif
                @if ($i % 2 == 1)
                    </tr><tr>
                @endif
            @endfor
        </table>
    </p>
</main>
</body>
