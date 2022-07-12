@if($notificationType == 'CITYZORE_BOOKING' || $notificationType == 'GYG_BOOKING' || $notificationType == 'BOKUN_BOOKING')
    @include('panel-partials.head', ['page' => 'bookings-index'])
    <section>
    <div class="db">
        <div class="col-lg-12">
            <div class="db-2-com db-2-main" style="background-color: white; padding: 25px;">
                <div class="db-2-main-com db-2-main-com-table">
                    <table class="table">
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
                            <tr>
                                <td>
                                    <div class="date" style="background-color: #253d5214; text-align: center; width: 95px;">
                                        @if(!($object->gygBookingReference == null))
                                            <p class="monthContainer" style="background-color: orange; color: white;">{{date('F', strtotime($object->dateTime))}}</p>
                                        @elseif($object->status == 2 || $object->status == 3)
                                            <p class="canceled monthContainer">{{date('F', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</p>
                                        @elseif($object->status == 0)
                                            <p class="active2 monthContainer">{{date('F', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</p>
                                        @elseif($object->status == 4 || $object->status == 5)
                                            <p class="month pending monthContainer">{{date('F', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</p>
                                        @endif
                                        @if(!($object->gygBookingReference == null))
                                            <p class="day"><strong style="color: #f23434; font-size:25px;">{{date('d', strtotime($object->dateTime))}}</strong><br>{{date('D', strtotime($object->dateTime))}}</p>
                                            <p class="years" style="color: black"><strong>{{date('Y', strtotime($object->dateTime))}}</strong></p>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12" style="text-align: center">
                                                    <p>Time</p>
                                                    <?php $explodedHour=explode('T', $object->dateTime)[1]; ?>
                                                    <p>
                                                        <strong>
                                                            @if(explode('+', $explodedHour)[0]=='00:00:00')
                                                                Operating Hours
                                                            @else
                                                                {{explode('+', $explodedHour)[0]}}
                                                            @endif
                                                        </strong>
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="day"><strong style="color: #f23434; font-size:25px;">{{date('d', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</strong><br>{{date('D', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</p>
                                            <p class="years" style="color: black"><strong>{{date('Y', strtotime(json_decode($object->dateTime, true)[0]['dateTime']))}}</strong></p>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12" style="text-align: center">
                                                    <p>Time</p>
                                                    @foreach(json_decode($object->hour, true) as $hour)
                                                        <p><strong>{{$hour['hour']}}</strong></p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                @if($object->gygBookingReference === null)
                                    <td style="font-size: 15px; width: 288px;"><strong>{{\App\Product::where('referenceCode', '=', explode('-', $object->reservationRefCode)[0])->first()->title}}</strong>
                                @else
                                    <td style="font-size: 15px; width: 288px;"><strong>GYG Product</strong>
                                @endif
                                <p><strong>Option:</strong>
                                    @foreach(\App\Option::all() as $option)
                                        @if($option->referenceCode == $object->optionRefCode)
                                            {{$option->title}}
                                        @endif
                                    @endforeach
                                </p>
                                <p><strong>Lead Traveler:</strong>{{json_decode($object->travelers, true)[0]['firstName']}} {{json_decode($object->travelers, true)[0]['lastName']}}</p>
                                <p><strong>Phone Number:</strong><a href="tel:{{json_decode($object->travelers, true)[0]['phoneNumber']}}"> {{json_decode($object->travelers, true)[0]['phoneNumber']}}</a> </p>
                                <p><strong>E-mail Address:</strong><a href="mailto:{{json_decode($object->travelers, true)[0]['email']}}"> {{json_decode($object->travelers, true)[0]['email']}}</a></p>
                                @if(!($object->travelerHotel == null))
                                    <p><strong>Hotel Address:</strong> {{$object->travelerHotel}}</p>
                                @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($object->gygBookingReference == null)
                                        <p><strong>{{explode("-",$object->bookingRefCode)[3]}}</strong></p>
                                    @else
                                        <p><strong>{{$object->gygBookingReference}}</strong></p>
                                    @endif
                                    <p><strong>Booked On:</strong> <br> {{date('d-m-Y H:i', strtotime($object->created_at))}}</p>
                                    <p><strong>Participants:</strong>
                                        @foreach(json_decode($object->bookingItems, true) as $participants)
                                            {{$participants['category']}} : {{$participants['count']}} <br>
                                        @endforeach
                                    </p>
                                    <p><strong>Price:</strong> <i class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency($object->totalPrice, $config->currencyName->value, $object->currencyID)}}</p>
                                </td>
                                <td>
                                    <div class="tri-state-toggle">
                                        @if($object->status === 0)
                                            <div data-booking-id="{{$object->id}}" data-content="0" class="active2 tri-state-toggle-button toggle-button1">
                                                Approved
                                            </div>
                                            <div data-booking-id="{{$object->id}}" data-content="4" class="tri-state-toggle-button toggle-button2">
                                                Pending
                                            </div>
                                            <div data-booking-id="{{$object->id}}" data-content="3" class="tri-state-toggle-button toggle-button3">
                                                Cancelled
                                            </div>
                                        @elseif($object->status === 4 || $object->status == 5)
                                            <div data-booking-id="{{$object->id}}" data-content="0" class="tri-state-toggle-button toggle-button1">
                                                Approved
                                            </div>
                                            <div data-booking-id="{{$object->id}}" data-content="4" class="pending tri-state-toggle-button toggle-button2">
                                                Pending
                                            </div>

                                            <div data-booking-id="{{$object->id}}" data-content="3" class="tri-state-toggle-button toggle-button3">
                                                Cancelled
                                            </div>
                                        @elseif($object->status === 3 || $object->status === 2)
                                            <div data-booking-id="{{$object->id}}" data-content="3" class="canceled tri-state-toggle-button toggle-button3">
                                                Cancelled
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        @if ($object->status != 0)
                                            <div class="col-md-8">
                                                @if(!is_null($object->rCodeID))
                                                    <input type="text" readonly value="{{App\Rcode::where('id', $object->rCodeID)->first()->rCode}}" class="rCodeInput" />
                                                @else
                                                    <input type="text" readonly value="" class="rCodeInput" />
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <button data-booking-status="{{$object->status}}" data-booking-id="{{$object->id}}" class="btn btn-primary saveRCodeInput" style="display: none;">Save</button>
                                            </div>
                                        @elseif($object->status == 0 && !is_null($object->rCodeID))
                                            <span>{{App\Rcode::where('id', $object->rCodeID)->first()->rCode}}</span>
                                        @else
                                            <p>This booking doesn't have a restaurant option</p>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <p><strong>Invoice ID:</strong> <br>
                                        @if($object->gygBookingReference==null)
                                            {{\App\Invoice::where('bookingID', '=', $object->id)->first()->referenceCode}}
                                        @endif
                                    </p>
                                    <p><strong>Sale Type:</strong> <br>  Instant Confirmation</p>
                                    <p><strong>Payment Method:</strong> <br>  <span style="text-transform: capitalize">
                                    @if($object->gygBookingReference==null)
                                        {{\App\Invoice::where('bookingID','=', $object->id)->first()->paymentMethod}}</span></p>
                                    @endif
                                </td>
                                <td style="width:50px;">
                                    <a href="{{url('print-pdf/'.$object->id)}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Voucher</button></a>
                                    <a href="{{url('print-invoice/'.$object->id)}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Download Invoice</button></a>
                                    <a href="{{url('booking/'.$object->id.'/edit')}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


@include('panel-partials.scripts', ['page' => 'bookings-index'])
@include('panel-partials.datatable-scripts', ['page' => 'bookings-index'])
@endif
