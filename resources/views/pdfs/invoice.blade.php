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
        .top-left {
            position: absolute;
            top: 128px;
            left: 20%;
        }
        p:last-child { page-break-after: never; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<footer class="center">
    cityzore.com is website of Paris Business and Travel. www.cityzore.com {{$invoice->companyAddress}}
    <br>
    Tel:+33184208801 Licence No:IM09212003 VAT:FR68533160842
</footer>
<main>
    <p>
        <div>

                <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="padding-right: 30%; padding-left: 30%;margin-top:3%;width: 200px; height:60px"/>

        </div>
        <br>
        <div style="padding-bottom: 2%;">
            Paris Business and Travel <br>
            {{$invoice->companyAddress}} <br>
            No TVA:FR68533160842 <br>
            Licence No:IM092120003 <br>
            0033(0)185084639 <br>
            contact@cityzore.com <br>
        </div>
        <table style="width:100%;border-top: 1px solid black;padding-top: 2%;">
            <tr style="width:100%;">
                @if($booking->gygBookingReference == null && $booking->isBokun == 0 && $booking->isViator == 0)
                    <td style="font-size: 14px;width:70%;">INVOICE TO: {{json_decode($booking->travelers, true)[0]['firstName']}} {{json_decode($booking->travelers, true)[0]['lastName']}} - {{explode("-",$booking->bookingRefCode)[3]}}</td>
                @else
                    <?php
                        $firstName = '';
                        $lastName = '';
                        $invoiceTo = '';
                        $travelers = json_decode($booking->travelers, true)[0];
                        if (array_key_exists('firstName', $travelers)) {
                            $firstName = $travelers['firstName'];
                        }
                        if (array_key_exists('lastName', $travelers)) {
                            $lastName = $travelers['lastName'];
                        }
                        if ($firstName == '' && $lastName == '') {
                            $invoiceTo = $travelers['email'];
                        } else {
                            $invoiceTo = $firstName . ' ' . $lastName;
                        }
                    ?>
                    @if($booking->isBokun == 0 && $booking->isViator == 0)
                        <td style="font-size: 14px;width:70%;">INVOICE TO: {{$invoiceTo}} - {{explode("-",$booking->bookingRefCode)[2]}}</td>
                    @else
                        <td style="font-size: 14px;width:70%;">INVOICE TO: {{$invoiceTo}} - {{$booking->bookingRefCode}}</td>
                    @endif
                @endif
                <td style="font-size: 14px;width: 30%;float: right;padding-right: 0px;">Invoice: {{$invoice->referenceCode}}</td>
            </tr>
            <tr style=" background-color: #FFFAFA;">
                <td class="center"></td>
                <td class="center"></td>
            </tr>
        </table>
        <table style="width:100%;border:1px solid black;">
            <tr style="width:100%;">
                <td class="center" style="font-size: 14px;border: 1px solid black;">Description</td>
                <td class="center" style="font-size: 14px;border: 1px solid black;">Date</td>
                <td class="center" style="font-size: 14px;border: 1px solid black;">Participants</td>
                <td class="center" style="font-size: 14px;border: 1px solid black;">Total</td>
            </tr>
            @if($booking->gygBookingReference == null && $booking->isBokun == 0 && $booking->isViator == 0)
                @foreach($products as $product)
                    @if($product->referenceCode == $productRefCode)
                        <tr style=" background-color: #FFFAFA;font-size:11px;border: 1px solid black">
                            <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$product->title}}</span><br>{{$options->title}}</td>
                            <td class="center" style="border: 1px solid black;">{{date('D, d-F-Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</td>
                            <td class="center" style="border: 1px solid black;">
                                @foreach(json_decode($booking->bookingItems, true) as $participants)
                                {{$participants['category']}} : {{$participants['count']}} <br>
                                @endforeach
                            </td>
                            <td class="center" style="border: 1px solid black;">{!! $currencySymbol !!} {{$booking->totalPrice}}</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr style=" background-color: #FFFAFA;font-size:11px;border: 1px solid black">
                    <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$options->title}}</span></td>
                    <td class="center" style="border: 1px solid black;">{{date('D, d-F-Y', strtotime(explode('T', $booking->dateTime)[0]))}}</td>
                    <td class="center" style="border: 1px solid black;">
                        @foreach(json_decode($booking->bookingItems, true) as $participants)
                        {{$participants['category']}} : {{$participants['count']}} <br>
                        @endforeach
                    </td>
                    <td class="center" style="border: 1px solid black;">{!! $currencySymbol !!} {{$booking->totalPrice}}</td>
                </tr>
            @endif
        </table>
        <div style="float:right;">
            Grand Total: {!! $currencySymbol !!} {{$booking->totalPrice}}
        </div>
    </p>
</main>
</body>


