<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $lastTotal = 0;
    @endphp
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
    cityzore.com is website of Paris Business and Travel. www.cityzore.com 26 Allee Etienne Ventenat 92500 Rueil Malmaison, France
    <br>
    Tel:+33184208801 Licence No:IM09212003 VAT:FR68533160842
</footer>
<main>
    <p>
    <div>
        <img src="{{ public_path('img/paris-city-tours-logo.png') }}" style="width: 200px; height:60px;"/>
    </div>
    <br>
    <div style="padding-bottom: 2%;">
        Paris Business and Travel <br>
        26 Allée Etienne Ventenat 92500 Rueil Malmaison France <br>
        No TVA:FR68533160842 <br>
        Licence No:IM092120003 <br>
        0033(0)185084639 <br>
        contact@cityzore.com <br>
    </div>
    <table style="width:100%;border-top: 1px solid black;padding-top: 2%;">
        <tr style="width:100%;">
                <td style="font-size: 14px;width:70%;">INVOICE TO: {{$company}}</td>
        </tr>
        <tr style=" background-color: #FFFAFA;">
            <td class="center"></td>
            <td class="center"></td>
        </tr>
    </table>
    <table style="width:100%;border:1px solid black;">
        <tr style="width:100%;">
            <td class="center" style="font-size: 14px;border: 1px solid black;">Reference Number</td>
            <td class="center" style="font-size: 14px;border: 1px solid black;">Product Reference Code</td>
            <td class="center" style="font-size: 14px;border: 1px solid black;">Option Reference Code</td>
            <td class="center" style="font-size: 14px;border: 1px solid black;">Date</td>
            @if($companyID == 0)
              <td class="center" style="font-size: 14px;border: 1px solid black;">Com</td>
              <td class="center" style="font-size: 14px;border: 1px solid black;">Credit</td>
            @else
             <td class="center" style="font-size: 14px;border: 1px solid black;">Total</td>
            @endif

            <td class="center" style="font-size: 14px;border: 1px solid black;">Payment Method</td>
        </tr>
        @php

        $comTotal = 0;
        $creditTotal = 0;

        @endphp
        @foreach($financeBookings as $booking)
        <tr style=" background-color: #FFFAFA;font-size:11px;border: 1px solid black">
            @if(!($booking['booking']->gygBookingReference == null))
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{explode('-', $booking['booking']->bookingRefCode)[1]}}</span></td>
            @elseif($booking['booking']->isBokun == 1 || $booking['booking']->isViator == 1)
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->bookingRefCode}}</span></td>
            @else
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{explode('-', $booking['booking']->bookingRefCode)[3]}}</span></td>
            @endif




            <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->productRefCode}}</span></td>
            <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->optionRefCode}}</span></td>



            <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->date}}</span></td>


            @if(($companyID==-1 || $companyID > 0))


              @if(\App\Commission::whereDate('created_at', '<', \Carbon\Carbon::parse($booking['booking']->created_at))->where('commissionerID', $commissionerRequest)->where('optionID', $booking['booking']->bookingOption->id ?? '')->exists())

              @php
              $com = \App\Commission::whereDate('created_at', '<', \Carbon\Carbon::parse($booking['booking']->created_at))->where('commissionerID', $commissionerRequest)->where('optionID', $booking['booking']->bookingOption->id ?? '')->first();






                    $lastTotal += $booking['booking']->totalPrice-(($booking['booking']->totalPrice)*($com->commission)/100);







              @endphp


                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->totalPrice-(($booking['booking']->totalPrice)*($com->commission)/100)}} &euro;</span></td>

                @else



                @php

                 $lastTotal += $booking['booking']->totalPrice-(($booking['booking']->totalPrice)*($commissionRate)/100);

                @endphp

                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$booking['booking']->totalPrice-(($booking['booking']->totalPrice)*($commissionRate)/100)}} &euro;</span></td>

                @endif


            @elseif($companyID==0)

                @php

                if (!empty($booking['booking']->cart->tempCommission) && $booking['booking']->cart->tempCommission > 0)
                {
                    $lastTotal += $booking['booking']->cart->tempCommission;
                }
                else
                {
                    $lastTotal += $booking['booking']->cart->totalCommission;
                }

                $ttt = (!empty($booking['booking']->cart->tempCommission) && $booking['booking']->cart->tempCommission > 0) ? $booking['booking']->cart->tempCommission : $booking['booking']->cart->totalCommission;


                @endphp
                @if(\App\Invoice::where('bookingID', $booking['booking']->id)->first()->paymentMethod == "COMMISSION")
                @php
                $cmt = 1;
                $crt = 0;

                 $difTotalforCom = $booking['booking']->cart->totalPrice - $ttt;
                 $difTotalforCredit = $ttt;
                 $comTotal += $difTotalforCom;

                @endphp

                @else

                 @php
                  $cmt = 0;
                  $crt = 1;
                 $difTotalforCom = $booking['booking']->cart->totalPrice - $ttt;
                 $difTotalforCredit = $ttt;
                 $creditTotal += $difTotalforCredit;

                @endphp


                @endif
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">@if($cmt) {{$difTotalforCom}} &euro; @endif </span></td>
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">@if($crt) {{$difTotalforCredit}} &euro; @endif </span></td>


            @endif
            <td class="center" style="border: 1px solid black;">{{\App\Invoice::where('bookingID', $booking['booking']->id)->first() ? \App\Invoice::where('bookingID', $booking['booking']->id)->first()->paymentMethod : '-'}}</td>
        </tr>
        @endforeach
        @php
        $euro = $currency->where('currency', 'EUR')->first();
        @endphp
        @foreach($extraPayment as $payment)
            <tr style=" background-color: #FFFAFA;font-size:11px;border: 1px solid black">
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$payment->referenceCode}}</span></td>
                  <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">---</span></td>
            <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">---</span></td>


                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$payment->updated_at->format('d/m/Y')}}</span></td>

                @php
                $oran = $euro->value / $currency->where('currency', $payment->currency_code)->first()->value;
                $price = $payment->price * $oran;
                $lastTotal += $price > 0 ? $price : 0;
                //$totalRate += $price > 0 ? $price : 0;
                @endphp

                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$price}} &euro;</span></td>
                <td class="center" style="border: 1px solid black;">EXTERNAL PAYMENT</td>
            </tr>
        @endforeach

    </table>
    <div style="float:right;">
        @if($companyID==0)

         outgoing Total = {{$creditTotal}} &euro;<br>
         incoming Total = {{$comTotal}} &euro;<br>
         Difference = {{$creditTotal - $comTotal}} &euro;

        {{--Grand Total: {{number_format($lastTotal, 2, ',', '.')}} &euro;--}}

        @elseif($companyID==-1)
         Grand Total: {{$totalRate}} &euro;

        @else
      Grand Total: {{number_format($lastTotal, 2, ',', '.')}} &euro;
        @endif


    </div>
    </p>
</main>
</body>


