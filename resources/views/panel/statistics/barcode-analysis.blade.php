@include('panel-partials.head', ['page' => 'dashboard'])
@include('panel-partials.header', ['page' => 'dashboard'])
@include('panel-partials.sidebar')

<head>
    <link rel="stylesheet" href="{{asset('custom/apexcharts-bundle/dist/apexcharts.css')}}">
    <link rel="stylesheet" href="{{asset('custom/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('custom/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('custom/admin-lte/AdminLTE.min.css')}}">
    <style>
        span{
            font-size: 11px!important;
            margin: 4px;
        }
        span span{
            margin-right: -7px;
        }
        .label{
            line-height: 2;
        }
        .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
            vertical-align: middle;
        }
    </style>
</head>


<div class="sb2-2-2">
    <ul>
        <li><a href="/"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"> Dashboard</li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>

    </ul>
</div>
<div class="row">
    <div class="col-xs-12" style="margin-bottom: 20px">
        <div class="box-inn-sp">
            <div class="inn-title">
                <h4>Remaining Barcodes</h4>
                <button class="btn btn-default pull-right" id="daterange-btn">Select Date</button>
            </div>
            <div class="tab-inn">
                <div class="table-responsive table-desi">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width: 110px">Barcode Date</th>
                            <th class="text-center">Booking Date</th>
                            <th class="text-center w-75">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($barcodeArray as $barcode)
                            <tr >
                                <td><span class="txt-dark weight-500">{{$barcode['barcode_date']}}</span></td>
                                <td>
                                    @foreach($barcode['booking_date'] as $key=>$value)
                                    <span class="label label-primary">{{$key}} <span class="label label-danger">{{$value}}</span></span>

                                    @endforeach
                                </td>
                                <td class="text-center w-75">
                                    <span class="label label-warning ">{{$barcode['total']}}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
</div>


@include('panel-partials.scripts', ['page' => 'dashboard'])
<script src="{{asset('custom/apexcharts-bundle/dist/apexcharts.js')}}"></script>
<script src="{{asset('custom/moment/min/moment.min.js')}}"></script>
<script src="{{asset('custom/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('custom/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

<script>


    $(document).ready(function () {

        var d = new Date();
        var date = d.getFullYear() + '-' + (d.getMonth()) + '-' + d.getDate();
        start_date = date;
        finish_date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

        $('#daterange-btn').daterangepicker({
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {
                $('.apex-date-span').html('<img style="margin-bottom: -188px" src="/img/loading1.gif"  alt="">');
                getUpdateBarcode(start.format('Y-M-D'), end.format('Y-M-D'));
            }
        );


        //Date picker
        $('#datepicker').datepicker({
            autoclose: true
        })
    });


    function getUpdateBarcode(start_date, finish_date) {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/statistic/barcode-analysis/update',
            data: {
                startDate: start_date,
                finishDate: finish_date,
            },
            success: function (data) {
                $('.table-hover tbody').html(data.barcode);
            }
        });

    }

</script>
