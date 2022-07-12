@include('panel-partials.head', ['page' => 'special-offers-create'])
@include('panel-partials.header', ['page' => 'special-offers-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="{{url('/')}}"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li class="active-bre"><a href="#"> Add New</a>
        </li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a>
        </li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <div class="box-inn-sp">
        <div class="inn-title">
            <h4>Create New Special Offer</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" id="selectedDate" value="">
                <input type="hidden" id="owner" value="{{$owner}}">
                <input type="hidden" id="max-value" value="">
                <div class="col-md-12">
                    <div class="form-group input-field col-md-offset-1 col-md-4 s4">
                        <select class="browser-default custom-select select2" name="product" id="productSelect">
                            <option data-foo="" selected>Choose a Product</option>
                            @foreach($products as $product)
                                <option data-foo="{{$product->referenceCode}}" value="{{$product->id}}">{{$product->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group input-field col-md-4 s4">
                        <select class="browser-default custom-select select2" name="option" id="optionSelect"></select>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 20px; display: none;" id="informationPart">
                    <div class="col-md-4 text-center">
                        <label class="col-md-12">Min. Person/Cart Total</label>
                        <div class="col-md-12">
                            <input name="minPersonOrCartTotal" type="radio" id="minPerson" checked="checked" value="minPerson" />
                            <label for="minPerson">Min. Person</label>

                            <input name="minPersonOrCartTotal" type="radio" id="minCartTotal" value="minCartTotal" />
                            <label for="minCartTotal">Min. Cart Total</label>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="input-field col-md-6 s6">
                            <input id="minimum" name="minimum" type="number" class="validate form-control" value="1">
                            <label for="minimum">Minimum</label>
                        </div>

                        <a data-toggle="collapse" href="#maximumUsabilityBlock" aria-expanded="false" aria-controls="collapseExample">
                            Maximum Usability
                        </a>
                        <div class="col-md-6 s6 collapse" id="maximumUsabilityBlock" style="margin-top: -5px;">
                            <input id="maximumUsability" name="maximumUsability" type="number" class="validate form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <label class="col-md-12">Discount Type</label>
                        <div class="col-md-12">
                            <input name="percentageMoney" type="radio" id="percentage" checked="checked" value="percentage" />
                            <label for="percentage">Percentage (%)</label>

                            <input name="percentageMoney" type="radio" id="money" value="money" />
                            <label for="money">Money (€)</label>
                        </div>
                    </div>
                    <div class="col-md-7" style="margin-top: 30px;">
                        <div id="dateRangeDiv" class="col-md-2">
                            <input name="dateType" type="radio" id="dateRange" value="dateRange" />
                            <label for="dateRange">Date Range</label>
                        </div>

                        <div id="weekDayDiv" class="col-md-2">
                            <input name="dateType" type="radio" id="weekDay" value="weekDay" />
                            <label for="weekDay">Week Day</label>
                        </div>

                        <div id="randomDayDiv" class="col-md-2">
                            <input name="dateType" type="radio" id="randomDay" value="randomDay" />
                            <label for="randomDay">Random Day</label>
                        </div>

                        <div id="dateTimesDiv" class="col-md-2">
                            <input name="dateType" type="radio" id="dateTimes" value="dateTimes" />
                            <label for="dateTimes">Dates & Times</label>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 30px">
                        <select class="custom-select" id="forWho" name="forWho">
                            <option selected value="All">All</option>
                            <option value="Users">For Users</option>
                            <option value="Commissioners">For Commissioners</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" id="calendarPart" style="display: none;margin-top: 50px; margin-bottom: 30px;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div id="datepicker-various" data-language='en' class="datepicker-various col-md-12 s12"></div>
                        </div>
                        <div class="col-md-8" style="display:none;" id="discountDRRDDiv">
                            <div class="input-field col-md-3 s3">
                                <input id="discountDRRD" name="discountDRRD" type="number" class="validate form-control" value="1" @if($owner == 'supplier') max="50" @endif>
                                <label for="discountDRRD">Discount</label>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" id="saveDRRDButton">Save Changes</button>
                            </div>
                        </div>
                        <div class="col-md-9" style="display:none;" id="discountDTDiv">

                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="inputPart" style="display:none; margin-top: 30px;">
                    <label class="col-md-12" style="font-size: 1.1rem;">Days Of Week</label>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="monday">Discount for Monday</label>
                        <input id="monday" name="monday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="tuesday">Discount for Tuesday</label>
                        <input id="tuesday" name="tuesday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="wednesday">Discount for Wednesday</label>
                        <input id="wednesday" name="wednesday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="thursday">Discount for Thursday</label>
                        <input id="thursday" name="thursday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="friday">Discount for Friday</label>
                        <input id="friday" name="friday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="saturday">Discount for Saturday</label>
                        <input id="saturday" name="saturday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-6">
                        <label for="sunday">Discount for Sunday</label>
                        <input id="sunday" name="sunday" type="number" class="validate form-control" value="0" @if($owner == 'supplier') max="50" @endif>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" id="saveWDButton">Save Changes</button>
                    </div>
                </div>
                <div class="col-md-12 text-center" id="previouslyAddedOffersDiv" style="display: none;">

                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'special-offers-create'])

