@include('panel-partials.head', ['page' => 'voucher-create'])
@include('panel-partials.header', ['page' => 'voucher-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Create Voucher</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Create Voucher</h4>
                </div>
                <div class="tab-inn">
                    <span class="col-md-12 invalid-feedback" role="alert">
                        <strong style="color:darkred"></strong>
                    </span>
                    <form action="{{url('voucher/store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-field col-md-6 s6">
                                    <select class="browser-default custom-select select2" name="product" id="productId">
                                        <option selected value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option data-foo="{{$product->referenceCode}}" value="{{$product->id}}">{{$product->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-field col-md-6 s6">
                                    <select class="browser-default custom-select" name="option" id="optionId">
                                        <option selected value="">Select Option</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <p>Booking Date</p>
                                <input id="bookingDate" data-language='en' type='text' class="datepicker-here" name="bookingDate" data-position="right top" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <p>Booking Time</p>
                                <input type='time' id="bookingTime" name="bookingTime"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <input type="text" id="travelerName" name="travelerName">
                                <label for="travelerName">Traveler Name</label>
                            </div>
                            <div class="input-field col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <input type="text" id="bookingRefCode" name="bookingRefCode">
                                <label for="bknNumber">BKN Number</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-12">
                                <select class="mdb-select" multiple searchable="Search here.." name="ticketTypes[]" id="ticketTypes">
                                    <option value="" disabled selected>Choose Ticket Type</option>
                                    @foreach($ticketTypes as $ticketType)
                                        <option value="{{$ticketType->id}}">{{$ticketType->name}}</option>
                                    @endforeach
                                </select>
                                <span class="attractionErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" id="adultCount" name="adultCount">
                                <label for="adultCount">Adult Count</label>
                            </div>
                            <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" id="youthCount" name="youthCount">
                                <label for="youthCount">Youth Count</label>
                            </div>
                            <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" id="childCount" name="childCount">
                                <label for="childCount">Child Count</label>
                            </div>
                            <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" id="infantCount" name="infantCount">
                                <label for="infantCount">Infant Count</label>
                            </div>
                            <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" id="euCitizenCount" name="euCitizenCount">
                                <label for="euCitizenCount">EU Citizen Count</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="submit" class="btn btn-primary large btn-large" value="Download" style="padding: 5px; font-size: 14px; height: 30px; width: 15%;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'voucher-create'])
