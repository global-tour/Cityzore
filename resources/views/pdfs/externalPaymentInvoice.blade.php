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
                <td style="font-size: 14px;width: 30%;float: right;padding-right: 0px;">Invoice: {{$externalPayment->invoiceID}}</td>
            </tr>
            <tr style=" background-color: #FFFAFA;">
                <td class="center"></td>
            </tr>
        </table>
        <table style="width:100%;border:1px solid black;">
            <tr style="width:100%;">
                <td class="center" style="font-size: 14px;border: 1px solid black;">Description</td>
                <td class="center" style="font-size: 14px;border: 1px solid black;">Date</td>
                <td class="center" style="font-size: 14px;border: 1px solid black;">Total</td>
            </tr>
            <tr style=" background-color: #FFFAFA;font-size:11px;border: 1px solid black">
                <td class="center" style="border: 1px solid black;"><span style="font-size: 13px;">{{$externalPayment->message}}</span></td>
                <td class="center" style="border: 1px solid black;">{{date('D, d-F-Y', strtotime($externalPayment->updated_at))}}</td>
                @php
                    $currencySymbol = "";
                    switch ($externalPayment->currency) {
                        case '978':
                            $currencySymbol = "&#8364;";
                            break;
                        case '949':
                            $currencySymbol = "&#8378;";
                            break;
                        case '840':
                            $currencySymbol = "&#36;";
                            break;
                        case '826':
                            $currencySymbol = "&#163;";
                            break;

                        default:
                            $currencySymbol = "&#8364;";
                            break;
                    }
                @endphp
                <td class="center" style="border: 1px solid black;"><?php echo $currencySymbol ?> {{$externalPayment->price}}</td>
            </tr>
        </table>
        <div style="float:right;">
            Grand Total: <?php echo $currencySymbol ?> {{$externalPayment->price}}
        </div>
    </p>
</main>
</body>


