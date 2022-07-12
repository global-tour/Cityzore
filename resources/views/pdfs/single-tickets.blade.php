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
        .page-break {
            margin-right: 10px;
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
        <table style="width:100%;padding-top: 2%;">
            <tr>
            @if($barcode->ticketType == 4)
                <td class="page-break">
                    <div style="position:relative;margin-bottom:3%">
                        <img src="{{public_path('img/ticket.png')}}" width="350px" style="border: 4px dotted slategray">
                    </div>
                    <div style="position:absolute;top:20px;padding-left: 18px;font-weight: normal;font-size: 10px;">Billet pour 1 Adulte</div>
                    <div style="position:absolute;top:35px;padding-left: 18px;font-weight: normal;font-size: 10px;">Valable jusqu'au {{$endTime}}</div>
                    <div style="position:absolute;top:50px;padding-left: 18px;font-weight: normal;font-size: 10px;">Numéro de réservation: {{$reservationNumber}}</div>
                    <div style="position:absolute;top:120px;padding-left: 135px;">{{$barcodeCode}}</div>
                    <div style="position: absolute;top:150px;padding-left:30px;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($barcodeCode, $generator::TYPE_CODE_128))}} . '"></div>
                </td>
            @endif
            @if($barcode->ticketType == 5)
                <td class="page-break">
                    <div style="text-align: center">
                        <span style="font-size: 17px;">Versailles Palace Ticket</span>
                    </div>
                    <div style="position:absolute;margin-top:1%;text-align: center;"> Barcode:{{$barcodeCode}}</div>
                    <div style="position: absolute;margin-top:1%;top:50px;"><img src="data:image/png;base64,' . {{base64_encode($generator->getBarcode($barcodeCode, $generator::TYPE_CODE_128))}} . '"></div>
                </td>
            @endif
            </tr>
        </table>
    </p>
</main>
</body>


