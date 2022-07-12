@include('panel-partials.head', ['page' => 'bookings-index-for-restaurant'])
@include('panel-partials.header', ['page' => 'bookings-index-for-restaurant'])


<div class="container-fluid" style="margin: 30px;">
    <section>
        <div class="sb2-2-2">
            <ul>
                <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
                <li class="active-bre"><a href="#"> All Bookings
                    </a>
                </li>
                <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
            </ul>
        </div>
        <div class="db">
            <div class="col-lg-12">
                <div class="db-2-com db-2-main" style="background-color: white; padding: 25px;">
                    <h4 style="margin-bottom: 2%;">Bookings for your Restaurant</h4>
                    <div class="db-2-main-com db-2-main-com-table">
                        <table id="datatable" class="responsive-table">
                            <thead class="hidden-md hidden-sm hidden-xs">
                            <tr>
                                <th>Date</th>
                                <th>Tour</th>
                                <th>Booking Ref.</th>
                                <th>R-Code</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="date" style="background-color: #253d5214; text-align: center; width: 95px;">
                                            @if(!($booking->gygBookingReference == null))
                                                <p class="monthContainer" style="background-color: orange; color: white;">{{date('F', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                            @elseif($booking->status == 2 || $booking->status == 3)
                                                <p class="canceled monthContainer">{{date('F', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                            @elseif($booking->status == 0 || $booking->status == 1)
                                                <p class="active2 monthContainer">{{date('F', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                            @elseif($booking->status == 4)
                                                <p class="month pending monthContainer">{{date('F', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                            @endif
                                                <p class="day"><strong style="color: #f23434; font-size:25px;">{{date('d', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</strong><br>{{date('D', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                            <p class="years" style="color: black"><strong>{{date('Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</strong></p>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12" style="text-align: center">
                                                    <p>Time</p>
                                                    <p><strong>{{date('H:i', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @if($booking->gygBookingReference === null)
                                        <td style="font-size: 15px; width: 288px;"><strong>{{\App\Option::where('referenceCode', '=', explode('-', $booking->reservationRefCode)[1])->first()->title}}</strong>
                                    @else
                                    <td style="font-size: 15px; width: 288px;"><strong>GYG Product</strong>
                                    @endif
                                        <p><strong>Option:</strong>
                                            @foreach($options as $option)
                                                @if($option->referenceCode == $booking->optionRefCode)
                                                    {{$option->title}}
                                                @endif
                                            @endforeach
                                        </p>
                                        <p><strong>Lead Traveler:</strong>{{json_decode($booking->travelers, true)[0]['firstName']}} {{json_decode($booking->travelers, true)[0]['lastName']}}</p>
                                        <p><strong>Phone Number:</strong>{{json_decode($booking->travelers, true)[0]['phoneNumber']}}</p>
                                        <p><strong>E-mail Address:</strong>{{json_decode($booking->travelers, true)[0]['email']}}</p>
                                        @if(!($booking->travelerHotel == null))
                                            <p><strong>Hotel Address:</strong> {{$booking->travelerHotel}}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <p style="font-size: 15px; font-weight: bold;">{{explode('-', $booking->bookingRefCode)[3]}}</p>
                                        <p style="font-weight: bold;">Booked on:</p>
                                        <p>{{date('d-m-Y H:i', strtotime($booking->created_at))}}</p>
                                        <p style="font-weight: bold;">Participants:</p>
                                        <p>
                                            @foreach(json_decode($booking->bookingItems, true) as $participants)
                                                {{$participants['category']}} : {{$participants['count']}} <br>
                                            @endforeach
                                        </p>
                                    </td>
                                        <td>
                                        <div class="col-md-12">
                                            <div class="col-md-8">
                                                @if(!is_null($booking->rCodeID))
                                                    <input type="text" readonly value="{{App\Rcode::where('id', $booking->rCodeID)->first()->rCode}}" class="rCodeInput" />
                                                @else
                                                    <input type="text" readonly value="" class="rCodeInput" />
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <button data-booking-status="{{$booking->status}}" data-booking-id="{{$booking->id}}" class="btn btn-primary saveRCodeInput" style="display: none;">Save</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


@include('panel-partials.scripts', ['page' => 'bookings-index-for-restaurant'])
@include('panel-partials.datatable-scripts', ['page' => 'bookings-index-for-restaurant'])


