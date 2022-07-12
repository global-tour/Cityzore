@include('panel-partials.head', ['page' => 'bookings-create'])
@include('panel-partials.header', ['page' => 'bookings-create'])
@include('panel-partials.sidebar')


    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add Booking</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2 style="margin-bottom: 2%;">Add Booking</h2>
        <ul class="nav nav-tabs tab-list">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-info" aria-hidden="true"></i> <span>Product</span></a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-bed" aria-hidden="true"></i> <span>Booking Details</span></a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-picture-o" aria-hidden="true"></i> <span>Customer Details</span></a></li>
        </ul>
        <form method="POST" action="{{url('booking/store')}}">
            @csrf
            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="box-inn-sp">
                        <div class="inn-title">
                            <h4>Add Booking Product</h4>
                        </div>
                        <div class="bor">
                            <div class="row">
                                @if (auth()->guard('admin')->check())
                                <div class="input-field col s12">
                                    <select class="browser-default custom-select" id="companySelect" name="companySelect">
                                        <option disabled selected>Select Company</option>
                                        @foreach($supplier as $sup)
                                            <option value="{{$sup->id}}">{{$sup->companyName}}</option>
                                        @endforeach
                                        <option value="-1">Paris Business and Travel</option>
                                    </select>
                                </div>
                                <div class="input-field col s12">
                                    <select class="browser-default custom-select" id="productSelect" name="productSelect"></select>
                                </div>
                                @elseif (auth()->guard('supplier')->check())
                                <div class="input-field col s12">
                                    <select class="browser-default custom-select" id="productSelect" name="productSelect">
                                        <option value="">Please select a product</option>
                                        @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="input-field col col-md-4 col-xs-3">
                                    <p>Booking Date</p>
                                    <input id="bookingDate" data-language='en' type='text' class="datepicker-here" name="bookingDate" data-position="right top" />
                                </div>
                                <div class="input-field col s12">
                                    <select class="browser-default custom-select" id="optionSelect" name="optionSelect">
                                    </select>
                                </div>
                                <div id="timeContainer" class="input-field col s12"></div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <a data-toggle="tab" href="#menu1">
                                            <input type="submit" class="waves-effect waves-light btn-large" value="Next">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <div class="inn-title">
                        <h4>Booking Details</h4>
                    </div>
                    <div class="bor">
                        <div class="row">
                            <div class="input-field col-md-4 col-xs-12">
                                <p>Price (Optional)</p>
                                <input id="t2-price" type="number" class="validate" name="bookingPrice">
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-md-3 col-xs-12">
                                <p>Adult Count</p>
                                <input id="adultCount" name="adultCount" type="number" value="1">
                            </div>
                            <div class="input-field col-md-3 col-xs-12">
                                <p>Youth Count</p>
                                <input id="youthCount" name="youthCount" type="number">
                            </div>
                            <div class="input-field col-md-3 col-xs-12">
                                <p>Child Count</p>
                                <input id="childCount" name="childCount" type="number">
                            </div>
                            <div class="input-field col-md-3 col-xs-12">
                                <p>Infant Count</p>
                                <input id="infantCount" name="infantCount" type="number">
                            </div>
                            <div class="input-field col-md-3 col-xs-12">
                                <p>EU CITIZEN Count</p>
                                <input id="euCitizenCount" name="euCitizenCount" type="number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <a data-toggle="tab" href="#menu2">
                                    <input type="submit" class="waves-effect waves-light btn-large" value="Next">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <div class="inn-title">
                        <h4>Customer Details</h4>
                    </div>
                    <div class="bor">
                        <div class="input-field col s6">
                            <input name="firstName" id="first_name" type="text" class="validate">
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="input-field col s6">
                            <input name="lastName" id="last_name2" type="text" class="validate">
                            <label for="last_name2">Last Name</label>
                        </div>
                        <div class="input-field col s6">
                            <input name ="email" id="email" type="email" class="validate">
                            <label for="email">Email</label>
                        </div>
                        <div class="input-field col s6">
                            <input name='phoneNumber' id="phone" type="text" class="validate">
                            <label for="phone">Phone</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="number" id="number" class="validate">
                            <label for="number">Supplier Ref. Code</label>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="submit" class="waves-effect waves-light btn-large" value="Submit">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@include('panel-partials.scripts', ['page' => 'bookings-create'])
