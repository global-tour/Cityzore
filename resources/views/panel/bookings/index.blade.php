@include('panel-partials.head', ['page' => 'bookings-index'])
@include('panel-partials.header', ['page' => 'bookings-index'])
@include('panel-partials.sidebar')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<head>
    <style>
        .datepicker-from, .datepicker-to, .c-datepicker-from, .c-datepicker-to {
            background: url(../../img/icon/dbl4.png) no-repeat left center #fff;
            border: 5px;
            height: 45px;
            border-radius: 2px;
            padding: 0px 10px 0px 35px;
            box-sizing: border-box;
            font-size: 14px;
            background-size: 17px;
            background-position-x: 0px;
            padding-left: 25px !important;
        }

        .inputs {
            border: outset;
            padding-top: 15px;
        }

        .inputp {
            border: outset;
            padding-top: 20px;
            padding-right: 20px;
            padding-bottom: 20px;
            margin-bottom: 10px;
            margin-left: 0px;
            margin-right: 0px;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            display: contents;
        }

        .sm-btn {
            font-size: 10px;
            height: 20px;
            padding: 5px;
            line-height: 10px;
        }

        .sm-btn-success {
            background-color: green
        }

        .sm-btn-danger {
            background-color: red
        }

        .pagination li.active {
            background-color: unset;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
            color: unset;
            border: unset;
            background-color: unset;
            background: unset;

        }
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            padding: unset;
        }


    </style>
</head>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ url('/bookings-excel-import') }}" id="excel-import-form" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i
                            class="icon-cz-copy"></i> Bokun Excel Import</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="stats"></div>
                    <div class="form-group">
                        <label class="control-label" for="">Select Excel File</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<section>
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> All Bookings
                </a>
            </li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left"
                                                                                     aria-hidden="true"></i> Panel</a>
            </li>
        </ul>
    </div>
    <div class="db">
        <div class="col-lg-12">
            <div class="db-2-com db-2-main" style="background-color: white; padding: 25px;">
                <h4 style="margin-bottom: 2%; display: inline-block">Travel Booking</h4>
                <div style="float: right">
                    <button type="button" class="btn btn-primary" id="showHideFilters" data-shown="1"
                            style="margin-right: 10px">Hide
                    </button>
                    @if (auth()->guard('admin')->check())
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                            Excel Import
                        </button>
                    @endif
                </div>

                <div class="filters">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-md-12" style="margin-top: 20px;">Product</label>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <select class="browser-default custom-select select2" name="product"
                                            id="productSelect">
                                        <option value="-1" selected></option>
                                        @foreach ($products  as $key => $value)
                                            <option value="{{$key}}">{{'#'.$key.' '.$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-12" style="margin-top: 20px;">Option</label>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <select class="browser-default custom-select select2" name="option"
                                            id="optionSelect">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->guard('supplier')->check())
                        <div class="row">
                            <div class="col-md-6">
                                <label class="col-md-12" style="margin-top: 20px;">Restaurant</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select class="browser-default custom-select select2" name="restaurant"
                                                id="restaurantSelect">
                                            @foreach ($restaurants  as $key => $value)
                                                <option value="{{$key}}">{{'#'.$key.' '.$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        @if (auth()->guard('admin')->check())
                            <div class="col-md-6" id="selectInputsArea">
                                <label class="col-md-12 pull-left" style="margin-top: 20px;">Platforms
                                    <button class="sm-btn-success sm-btn btn pull-right" style="display: none">Clear
                                    </button>
                                    <button class="sm-btn-danger sm-btn btn pull-right " style="display: none">Reset
                                    </button>
                                </label>


                                <div class="col-md-12">
                                    <select class="mdb-select" multiple name="selectInputs[]" id="selectInputs">
                                        @foreach($platforms as $platform)
                                            <option selected value="{{$platform->id}}">{{$platform->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6 ">
                            <label class="col-md-12" style="margin-top: 20px;">Booking Status</label>
                            <div class="col-md-12">
                                <select class="mdb-select" multiple name="bookingStatus[]" id="bookingStatus">
                                    <option selected value="approvedBookings">Approved</option>
                                    <option selected value="pendingBookings">Pending</option>
                                    <option selected value="cancelledBookings">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @if (auth()->guard('admin')->check())
                        <div class="row inputp">
                            <div class="col-md-3">
                                <label class="col-md-12">Payment By Supplier</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select class="browser-default custom-select col-md-12" name="payment_supplier"
                                                id="payment_supplier">
                                            @foreach ($suppliers as $key => $value)
                                                <option value="{{$key == "33" ? "-1" : $key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="col-md-12">Payment By Affiliate</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select class="browser-default custom-select col-md-12" name="payment_affiliate"
                                                id="payment_affiliate">
                                            @foreach ($affiliated as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="col-md-12">Payment Method</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select name="paymentMethod" id="paymentMethod"
                                                class="browser-default custom-select col-md-12">
                                            <option value="">ALL</option>
                                            <option value="CREDIT CARD">CREDIT CARD</option>
                                            <option value="COMMISSION">COMMISSION</option>
                                            <option value="API">API</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-12">Commissioners</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select name="commissioner" id="commissioner"
                                                class="browser-default custom-select col-md-12">
                                            <option value="0">ALL</option>
                                            @foreach($comissioners as $commissioner)
                                                <option
                                                    value="{{$commissioner->id}}">{{$commissioner->name}} {{$commissioner->surname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="col-md-12">With Imported</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select name="withImported" id="withImported"
                                                class="browser-default custom-select col-md-12">
                                            <option value="">choose</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="col-md-6 inputs">
                                <h4 align="center" class="col-md-12" style="color: rgb(38, 166, 154)">Travelled Date
                                    Range</h4>
{{--                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">--}}
{{--                                    <i class="fa fa-calendar"></i>&nbsp;--}}
{{--                                    <span></span> <i class="fa fa-caret-down"></i>--}}
{{--                                </div>--}}
                                <div class="col-md-6">
                                    <input type='text' class='datepicker-from' data-language='en'
                                           placeholder="{{ __('From') }}"/>
                                </div>
                                <div class="col-md-6">
                                    <input type='text' class='datepicker-to' data-language='en'
                                           placeholder="{{ __('To') }}"/>
                                </div>
                            </div>
                            <div class="col-md-6 inputs">
                                <h4 align="center" class="col-md-12" style="color: rgb(38, 166, 154)">Booked Date
                                    Range</h4>
                                <div class="col-md-6">
                                    <input type='text' class='c-datepicker-from' data-language='en'
                                           placeholder="{{ __('From') }}"/>
                                </div>
                                <div class="col-md-6">
                                    <input type='text' class='c-datepicker-to' data-language='en'
                                           placeholder="{{ __('To') }}"/>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="col-md-12 inputs">
                                <div class="col-md-12">
                                    <h4 align="center" style="color: rgb(38, 166, 154)">Advanced Search</h4>
                                </div>
                                <div class="col-md-4">
                                    <input type='text' class="advancedSearch" id="searchBooking"
                                           placeholder="Booking Number"/>
                                </div>
                                <div class="col-md-4">
                                    <input type='text' class="advancedSearch" id="searchInvoice"
                                           placeholder="Invoice ID"/>
                                </div>
                                <div class="col-md-4">
                                    <input type='text' class="advancedSearch" id="searchTraveler"
                                           placeholder="Traveler Name"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="margin-bottom: -40px!important;">
                            <button id="exportToExcelButton" class="btn btn-primary pull-right" style="margin: 20px">
                                Excel
                            </button>
                            <button id="clearFiltersButton" class="btn btn-primary pull-right" style="margin: 20px">
                                Clear
                            </button>
                            <button id="applyFiltersButton" class="btn btn-primary pull-right" style="margin: 20px">
                                Apply
                            </button>
                        </div>
                    </div>

                </div>


                <div class="db-2-main-com db-2-main-com-table">

                    <div style="clear: both;">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session()->has('success'))

                            <div class="alert-success" style="margin: 20px; padding: 20px;">
                                {{session()->get('success')}}
                            </div>

                        @endif

                        @if(session()->has('error'))

                            <div class="alert-danger" style="margin: 20px; padding: 20px;">
                                {{session()->get('error')}}
                            </div>

                        @endif
                    </div>


                    <table id="datatable" class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Tour</th>
                            <th>Booking Ref</th>
                            <th>Status</th>
                            <th>R-Code</th>
                            <th>Sales Informations</th>
                            <th>More</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<script>

    window.addEventListener('load', function () {
        $('#excel-import-form').submit(function (e) {
            e.preventDefault();
            const form = $('#excel-import-form');
            $.ajax({
                url: form.attr('action'),
                method: "POST",
                data: new FormData(form[0]),
                contentType: false,
                processData: false,
                beforeSend: function () {
                    form.find('.btn-primary').attr('disabled', true).html(`<i class="fa fa-spinner fa-spin"></i> Please Wait...`);
                },
                success: function (response) {
                    if (response.status) {
                        $('#stats').html(`<div class="alert alert-success">${response.message}</div>`)
                        form.find('.btn-primary').removeAttr('disabled').html('IMPORT');
                    } else {
                        $('#stats').html(`<div class="alert alert-danger">Error. Please check the excel data and upload it again.</div>`);
                        form.find('.btn-primary').removeAttr('disabled').html('IMPORT');
                    }
                },
                error: function () {
                    $('#stats').html(`<div class="alert alert-danger">Error. Please check the excel data and upload it again.</div>`);
                    form.find('.btn-primary').removeAttr('disabled').html('IMPORT');
                }
            })

        })
    })
</script>
@include('panel-partials.modals.bookings.access_checkins_modal')
@include('panel-partials.modals.bookings.customer-contact-modal')
@include('panel-partials.modals.bookings.file-import-modal')
@include('panel-partials.modals.bookings.file-invoice-modal')
@include('panel-partials.scripts', ['page' => 'bookings-index'])
@include('panel-partials.datatable-scripts', ['page' => 'bookings-index'])

