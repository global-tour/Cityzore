@include('panel-partials.head', ['page' => 'voucher-edit'])
@include('panel-partials.header', ['page' => 'voucher-edit'])
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
                    <h4>Edit Voucher</h4>
                </div>
                <div class="tab-inn">
                    <span class="col-md-12 invalid-feedback" role="alert">
                        <strong style="color:darkred"></strong>
                    </span>
                    <form action="{{url('voucher/'.$voucher->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('POST')
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-field col-md-6 s6">
                                    <select class="browser-default custom-select select2" name="product" id="productId">
                                        <option selected value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{$product->id == $voucher->productID  ? 'selected' : ''}}>{{ $product->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-field col-md-6 s6">
                                    <select class="browser-default custom-select" name="option" id="optionId2" value="">
                                        <option selected value="">Select Option</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <p>Booking Date</p>
                                <input id="bookingDate" data-language='en' value="{{json_decode($voucher->dateTime, true)['date']}}" type='text' class="datepicker-here" name="bookingDate" data-position="right top" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <p>Booking Time</p>
                                <input type='time' id="bookingTime" name="bookingTime" value="{{json_decode($voucher->dateTime, true)['time']}}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <input type="text" id="travelerName" name="travelerName" value="{{$voucher->traveler}}">
                                <label for="travelerName">Traveler Name</label>
                            </div>
                            <div class="input-field col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                <input type="text" id="bookingRefCode" name="bookingRefCode" value="{{$voucher->bookingRefCode}}">
                                <label for="bknNumber">BKN Number</label>
                            </div>
                        </div>
                        <div class="row">
                            @foreach(json_decode($voucher->participants, true) as $participants)
                                <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <input type="number" id="adultCount" name="adultCount" value="{{$participants['adult']}}">
                                    <label for="adultCount">Adult Count</label>
                                </div>
                                <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <input type="number" id="youthCount" name="youthCount" value="{{$participants['youth']}}">
                                    <label for="youthCount">Youth Count</label>
                                </div>
                                <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <input type="number" id="childCount" name="childCount" value="{{$participants['child']}}">
                                    <label for="childCount">Child Count</label>
                                </div>
                                <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <input type="number" id="infantCount" name="infantCount" value="{{$participants['infant']}}">
                                    <label for="infantCount">Infant Count</label>
                                </div>
                                <div class="input-field col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <input type="number" id="euCitizenCount" name="euCitizenCount" value="{{$participants['euCitizen']}}">
                                    <label for="euCitizenCount">EU Citizen Count</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 5px; font-size: 14px; height: 30px;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'voucher-edit'])
