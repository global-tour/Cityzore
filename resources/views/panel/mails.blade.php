@include('panel-partials.head', ['page' => 'mails'])
@include('panel-partials.header', ['page' => 'mails'])
@include('panel-partials.sidebar')

<div>
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> All Mails</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Mails</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-md-12" style="margin-top: 20px;">Type</label>
                            <div class="col-md-12">
                                <div class="col-md-5">
                                    <select name="typeSelect" id="typeSelect" class="browser-default custom-select col-md-9">
                                        <option value="">ALL</option>
                                        <option value="mail.booking-successful">Booking Successful</option>
                                        <option value="bookings.mail-information">Booking Mail Information</option>
                                        <option value="mail.booking-reminder">Booking Reminder</option>
                                        <option value="mail.booking-cancel">Booking Cancel</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <label class="col-md-12" style="margin-top: 20px;">Date Range</label>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <input type='text' class='datepicker-from' data-language='en' placeholder="{{ __('From') }}"/>
                                </div>
                                <div class="col-md-6">
                                    <input type='text' class='datepicker-to' data-language='en' placeholder="{{ __('To') }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="margin-bottom: -40px!important;">
                            <button id="applyFiltersButton" class="btn btn-primary pull-left" style="margin: 20px">Apply</button>
                            <button id="clearFiltersButton" class="btn btn-primary pull-left" style="margin: 20px">Clear</button>
                        </div>
                    </div>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Index</th>
                                <th>To</th>
                                <th>Data</th>
                                <th>Type</th>
                                <th style="width: 150px;">Date</th>
                                <th style="width: 100px;">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'mails'])
@include('panel-partials.datatable-scripts', ['page' => 'mails'])
