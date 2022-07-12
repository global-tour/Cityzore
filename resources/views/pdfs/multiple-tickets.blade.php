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
            @foreach($cruiseBarcodeArray as $barcode)
                @if($barcode->ticketType == 4)
                    <td class="page-break">
                        <div style="position:relative;margin-bottom:3%">
                            <img src="{{ public_path('img/ticket.png') }}" width="350px" style="border: 4px dotted slategray">
                        </div>
                        <div style="position:absolute;top:20px;padding-left: 18px;font-weight: normal;font-size: 10px;">Billet pour 1 Adulte</div>
                        <div style="position:absolute;top:35px;padding-left: 18px;font-weight: normal;font-size: 10px;">Valable jusqu'au {{ $barcode->endTime }}</div>
                        <div style="position:absolute;top:50px;padding-left: 18px;font-weight: normal;font-size: 10px;">Numéro de réservation: {{ $barcode->reservationNumber }}</div>
                        <div style="position:absolute;top:120px;padding-left: 135px;">{{ $barcode->code }}</div>
                        <div style="position: absolute;top:150px;padding-left:30px;"><img src="data:image/png;base64,' . {{ $barcode->barcodeGenerator }} . '"></div>
                        @if(!is_null($barcode->description))
                            <div style="position:absolute;top:133px;padding-left: 18px;text-align: center;font-weight: normal;font-size:10px;">{{ $barcode->description }}</div>
                        @endif
                    </td>
                    <br>
                @elseif($barcode->ticketType == 5)
                    <td class="page-break" style="width: 50%; height: 150px;">
                        <div style="position:absolute;margin-top:1%;text-align: center;"> Barcode:{{ $barcode->code }}</div>
                        <div style="position:absolute;margin-top:3%;text-align: center; font-size: 14px;">
                            <span style="font-size: 17px;">Versailles Palace Ticket {{ $loop->iteration }}</span>
                        </div>
                        <div style="position: absolute;margin-top:1%;top:50px;"><img src="data:image/png;base64,' . {{ $barcode->barcodeGenerator }} . '"></div>
                    </td>
                @endif
                @if ($loop->iteration % 2 == 0)
                    </tr><tr>
                @endif
            @endforeach
    </table>
    </p>
</main>
</body>


