@include('panel-partials.head', ['page' => 'bookings-edit'])
@include('panel-partials.header', ['page' => 'bookings-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit Booking</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <h2 style="margin-bottom: 2%;">Edit Booking</h2>
    <ul class="nav nav-tabs tab-list">
        <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-info" aria-hidden="true"></i> <span>Traveler</span></a></li>
        <li><a id="menu1Tab" data-toggle="tab" href="#menu1"><i class="fa fa-info" aria-hidden="true"></i> <span>Booking</span></a></li>
    </ul>
    <form action="{{url('booking/'.$bookings->id.'/update')}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('POST')
        <input type="hidden" name="_method" value="POST">
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Traveler</h4>
                    </div>
                    <div class="bor">
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="firstName[]" name="firstName" type="text" class="validate" value="{{json_decode($bookings->travelers, true)[0]['firstName']}}">
                                <label for="firstName">Traveler Name</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="lastName[]" name="lastName" type="text" class="validate" value="{{json_decode($bookings->travelers, true)[0]['lastName']}}">
                                <label for="last_name2">Traveler Surname</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="email" name="email" type="email" class="validate" value="{{json_decode($bookings->travelers, true)[0]['email']}}">
                                <label for="email">Traveler Email</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="phoneNumber" name="phoneNumber" type="text" class="validate" value="{{json_decode($bookings->travelers, true)[0]['phoneNumber']}}">
                                <label for="phone">Traveler Phone</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" id="travelerHotel" name="travelerHotel" class="validate" value="{{$bookings->travelerHotel}}">
                                <label for="travelerHotel">Hotel Address</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="number" id="totalPrice" name="totalPrice" class="validate" value="{{$bookings->totalPrice}}">
                                <label for="number">Price</label>
                            </div>
                        </div>
                        <div class="row">
                            <button class="btn btn-large waves-effect waves-light" id="nextButton">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="menu1" class="tab-pane fade">
                <div class="inn-title">
                    <h4>Booking</h4>
                </div>

                <div class="bor" style="margin-bottom: 0">
                    <div class="row">
                        <div class="input-field col s6">
                            <input value="{{$bookingAvData[0]["date"]}}" id="bookingDate" data-language='en' type='text' class="datepicker-here validate" name="dateTime" data-position="right top">
                            <label for="bookingDate">Booking Date</label>
                        </div>
                        <div class="input-field col s6">
                            <select class="mdb-select" name="platformID" id="platformID">
                                @foreach($platforms as $platform)
                                    <option value="{{$platform->id}}" {{$bookings->platformID == $platform->id  ? 'selected' : ''}}>{{$platform->name}}</option>
                                @endforeach
                            </select>
                            <label for="selectInput">Company</label>
                        </div>
                    </div>
                    @if($invoice!=null)
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea name="companyAddress" id="companyAddress" cols="20" rows="3" style="height: 60px">{{$invoice}}</textarea>
                                <label for="companyAddress" style="padding: 6px">Company Addresss</label>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bor">
                    @foreach($bookingAvData as $ind => $boAvDa)
                        @if($boAvDa["hourFrom"]!=null)
                            <div class="row">
                                <p><b>{{$boAvDa["availabilityName"]}}</b></p>
                                <p style="margin-left: 10px; margin-top: 5px;">Booking Time</p>
                                @if($boAvDa["availabilityType"] == "Starting Time")
                                    <div class="input-field col s4">
                                        <input value="{{$boAvDa["hourFrom"]}}" class="file-path validate" type="time" name="hourFrom_{{$ind}}" placeholder="Time">
                                    </div>
                                @elseif($boAvDa["availabilityType"] == "Operating Hours")
                                    <div class="input-field col s4">
                                        <input value="{{$boAvDa["hourFrom"]}}" class="file-path validate" type="time" name="hourFrom_{{$ind}}" placeholder="Time">
                                    </div>
                                    <div class="input-field col s4">
                                        <input value="{{$boAvDa["hourTo"]}}" class="file-path validate" type="time" name="hourTo_{{$ind}}" placeholder="Time">
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
    </form>
</div>


@include('panel-partials.scripts', ['page' => 'bookings-edit'])
