@include('panel-partials.head', ['page' => 'dashboard'])
@include('panel-partials.header', ['page' => 'dashboard'])
@include('panel-partials.sidebar')

<div id="releaseNotes" style="text-align: center;font-size: 22px!important;" class="col-md-12">
    <label class="alert-danger col-md-12">
        <p style="padding-top:5px;font-size: 16px!important;">You may read the improvements related our latest versions here.</p>
    </label>
</div>
<div class="sb2-2-2">
    <ul>
        <li><a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Dashboard</a></li>
    </ul>
</div>
@if(Auth::guard('admin')->check())
    <div class="ad-v2-hom-info">
        <div class="ad-v2-hom-info-inn hidden-xs">
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="ad-hom-box ad-hom-box-1">
                    <span class="ad-hom-col-com ad-hom-col-1"><i class="icon-cz-eur"></i></span>
                    <div class="ad-hom-view-com">
                        <p><i class="fa fa-arrow-up up"></i>Income for 1 Month</p>
                        <h3><i class="{{$defIcon}}"></i> {{$totalAmount}}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="ad-hom-box ad-hom-box-2">
                    <span class="ad-hom-col-com ad-hom-col-2"><i class="icon-cz-booking" ></i></span>
                    <div class="ad-hom-view-com">
                        <p><i class="fa fa-arrow-up up"></i>Booked Baskets (%)</p>
                        <h3>{{$cartPercentage}}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="ad-hom-box ad-hom-box-3">
                    <span class="ad-hom-col-com ad-hom-col-3"><i class="icon-cz-cancel" ></i></span>
                    <div class="ad-hom-view-com">
                        <p><i class="fa fa-arrow-up up"></i>Cancelled Bookings (%)</p>
                        <h3>{{$bookingPercentage}}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="ad-hom-box ad-hom-box-4">
                    <span class="ad-hom-col-com ad-hom-col-4"><i class="icon-cz-umbrella"></i></span>
                    <div class="ad-hom-view-com">
                        <p><i class="fa  fa-arrow-up up"></i>GYG Bookings / All (%)</p>
                        <h3>{{$gygPercentage}}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="sb2-2-3">
    <div class="row">
        @if(!empty($ticketArray))
        <div class="col-xs-12" style="margin-bottom: 20px">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Remaining Barcodes</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Ticket Type Name</th>
                                <th class="text-center">Ticket Count</th>
                                <th class="text-center">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ticketArray as $t)
                                <tr class="{{$t['diff']<1 ? 'bg-danger':'' }}">
                                    <td><span class="txt-dark weight-500">{{$t['ticket_type_name']}}</span></td>
                                    <td class="text-center">{{$t['count']}}</td>
                                    <td class="text-center">
                                        @if($t['diff']<1)
                                            <span class="label label-danger">Sold</span>
                                        @else
                                            <span class="label label-warning">Run Out</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            @endif
        <div class="col-md-6">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Last 5 Bookings</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Traveler</th>
                                <th>Reference Code</th>
                                <th>Booking Date</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bookingsLast5 as $s)
                                <?php
                                $travelers = json_decode($s->travelers, true);
                                $firstName = '';
                                if (array_key_exists('firstName', $travelers[0])) {
                                    $firstName = $travelers[0]['firstName'];
                                }
                                $lastName = '';
                                if (array_key_exists('lastName', $travelers[0])) {
                                    $lastName = $travelers[0]['lastName'];
                                }
                                $fullName = $firstName . ' ' . $lastName;
                                ?>
                                @if(!($s->gygBookingReference == NULL))
                                    <tr style="background-color: rgba(255,165,0,0.44);">
                                        <td><span class="txt-dark weight-500">{{$fullName}}</span></td>
                                        <td>{{explode('-', $s->bookingRefCode)[2]}}</td>
                                        <td><span class="txt-success"><span>{{date('d-m-Y', strtotime($s->created_at))}}</span></span></td>
                                        <td><span class="txt-dark weight-500"><i style="background: none; color: #9c9b99;" class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency($s->totalPrice, $config->currencyName->value, $s->currencyID)}}</span>
                                        </td>
                                        <td>
                                            @if($s->status === 0 || $s->status === 1)
                                                <span class="label label-success">Active</span><span class="label label-warning">GYG</span>
                                            @elseif($s->status === 2 || $s->status === 3)
                                                <span class="label label-danger">Cancelled</span><span class="label label-warning">GYG</span>
                                            @elseif($s->status === 4)
                                                <span class="label label-warning">Pending</span><span class="label label-warning">GYG</span>
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><span class="txt-dark weight-500">{{$fullName}}</span></td>
                                        @if($s->isBokun == 1 || $s->isViator == 1)
                                            <td>{{$s->bookingRefCode}}</td>
                                        @else
                                            <td>{{explode('-', $s->bookingRefCode)[3]}}</td>
                                        @endif
                                        <td><span class="txt-success"><span>{{date('d-m-Y', strtotime($s->created_at))}}</span></span></td>
                                        <td><span class="txt-dark weight-500"><i style="background: none; color: #9c9b99;" class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency($s->totalPrice, $config->currencyName->value, $s->currencyID)}}</span>
                                        </td>
                                        <td>
                                            @if($s->status === 0 || $s->status === 1)
                                                <span class="label label-success">Active</span>
                                            @elseif($s->status === 2 || $s->status === 3)
                                                <span class="label label-danger">Cancelled</span>
                                            @elseif($s->status === 4)
                                                <span class="label label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Most Booked Options</h4>
                    <p>Airport Hotels The Right Way To Start A Short Break Holiday</p>
                    <ul id='dropdown2' class='dropdown-content'>
                        <li><a href="#!">Add New</a></li>
                        <li><a href="#!">Edit</a></li>
                        <li><a href="#!">Update</a></li>
                        <li class="divider"></li>
                        <li><a href="#!"><i class="material-icons">delete</i>Delete</a></li>
                        <li><a href="#!"><i class="material-icons">subject</i>View All</a></li>
                        <li><a href="#!"><i class="material-icons">play_for_work</i>Download</a></li>
                    </ul>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Reference Code</th>
                                <th>Title</th>
                                <th>Total Earning</th>
                                <th>Booking Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result as $r)
                                <tr>
                                    @if(in_array($r->optionRefCode, $mapped_refcodes))
                                        <td><span class="txt-dark weight-500"></span>{{$r->optionRefCode}}</td>
                                        <td><span class="txt-success"><span></span>{{$r->title}}</span></td>
                                        <td>
                                    <span class="txt-dark weight-500">
                                        <i style="background: none; color: #9c9b99;" class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency(json_encode($totalPricesForOption[$r->optionRefCode][0]['totalPrice']), $config->currencyName->value)}}
                                    </span>
                                        </td>
                                        <td>
                                            <span class="txt-dark">{{$r->totalCount}}</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>


@include('panel-partials.scripts', ['page' => 'dashboard'])
@include('layouts.modal-box.release-notes')
