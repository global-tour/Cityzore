<div class="offcanvas-tools">
    <div class="offcanvas-title">{{ $booking->fullName }}</div>
    <div class="offcanvas-close-button">
        <i class="fa fa-times"></i>
    </div>
</div>

<div class="offcanvas-body">
    <ul class="nav nav-tabs tab-list">
        @if(!auth()->guard('supplier')->check())
            <li class="{{ $attr && $attr == '#' ? 'active' : '' }}">
                <a data-toggle="tab" href="#bookingDetails">
                    <i class="fa fa-info" aria-hidden="true"></i>
                    <span>Booking Details</span>
                </a>
            </li>
        @endif

        <li class="{{ !is_null($attr) && $attr == '#special-ref-code' ? 'active' : '' }}">
            <a data-toggle="tab" href="#special-ref-code">
                <i class="fa fa-barcode" aria-hidden="true"></i>
                <span>Special Ref. Code</span>
            </a>
        </li>

        @if(!auth()->guard('supplier')->check())

            <li class="{{ !is_null($attr) && $attr == '#import' ? 'active' : '' }}">
                <a data-toggle="tab" href="#import">
                    <i class="fa fa-upload" aria-hidden="true"></i>
                    <span>Import</span>
                </a>
            </li>
        @endif

        @if(!auth()->guard('supplier')->check())

            <li class="{{ !is_null($attr) && $attr == '#contact' ? 'active' : '' }}">
                <a data-toggle="tab" href="#contact">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <span>Contact</span>
                </a>
            </li>
        @endif

        @if(!auth()->guard('supplier')->check())
            <li>
                <a data-toggle="tab" href="#whatsapp">
                    <i class="fa fa-whatsapp" aria-hidden="true"></i>
                    <span>Whatsapp</span>
                </a>
            </li>
        @endif

        <li class="{{ !is_null($attr) && $attr == '#comment' ? 'active' : '' }}">
            <a data-toggle="tab" href="#comment">
                <i class="fa fa-comments-o" aria-hidden="true"></i>
                <span>Comment</span>
            </a>
        </li>

        @if(!auth()->guard('supplier')->check())

            <li class="{{ !is_null($attr) && $attr == '#invoice' ? 'active' : '' }}">
                <a data-toggle="tab" href="#invoice">
                    <i class="fa fa-file" aria-hidden="true"></i>
                    <span>Invoice</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="tab-content">

    @if(!auth()->guard('supplier')->check())
        <!-- BOOKINGDETAILS::start -->
            <div id="bookingDetails" class="tab-pane fade {{ $attr && $attr == '#' ? 'active in' : '' }}">
                <form id="bookingDetailsForm" data-submit="{{ url('/update-booking-detail') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $booking->id }}">

                    <div class="row">

                        <div class="input-field col s6">
                            <input id="firstName[]" name="firstName" type="text"
                                   class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                                   value="{{json_decode($booking->travelers, true)[0]['firstName']}}">
                            <label for="firstName" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Traveler
                                Name</label>
                        </div>

                        <div class="input-field col s6">
                            <input id="lastName[]" name="lastName" type="text"
                                   class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                                   value="{{json_decode($booking->travelers, true)[0]['lastName']}}">
                            <label for="last_name2" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Traveler
                                Surname</label>
                        </div>

                        <div class="input-field col s6">
                            <input id="email" name="email" type="email"
                                   class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                                   value="{{json_decode($booking->travelers, true)[0]['email']}}">
                            <label for="email" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Traveler
                                Email</label>
                        </div>

                        <div class="input-field col s6">
                            <input id="phoneNumber" name="phoneNumber" type="text"
                                   class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                                   value="{{json_decode($booking->travelers, true)[0]['phoneNumber']}}">
                            <label for="phone" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Traveler
                                Phone</label>
                        </div>

                        <div class="input-field col s6">
                            <input type="text" id="travelerHotel" name="travelerHotel"
                                   class="validate {{ !is_null($booking->travelerHotel) ? 'valid' : '' }}"
                                   value="{{$booking->travelerHotel}}">
                            <label for="travelerHotel" class="{{ !is_null($booking->travelerHotel) ? 'active' : '' }}">Hotel
                                Address</label>
                        </div>

                        <div class="input-field col s6">
                            <input type="number" id="totalPrice" name="totalPrice"
                                   class="validate {{ !is_null($booking->totalPrice) ? 'valid' : '' }}"
                                   value="{{$booking->totalPrice}}">
                            <label for="number"
                                   class="{{ !is_null($booking->totalPrice) ? 'active' : '' }}">Price</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s6">
                            <input id="bookingDate"
                                   type="text" class="dateranger-single validate valid"
                                   value="{{ $booking->bookingDateTime['org'] }}" name="dateTime">
                            <label for="bookingDate" class="active">Booking Date</label>
                        </div>

                        <div class="input-field col s6">
                            <select class="mdb-select" name="platformID" id="platformID">
                                @foreach($platforms as $platform)
                                    <option
                                        value="{{$platform->id}}" {{$booking->platformID == $platform->id  ? 'selected' : ''}}>{{$platform->name}}</option>
                                @endforeach
                            </select>
                            <label for="selectInput">Company</label>
                        </div>

                        @if($booking->invoc)
                            <div class="input-field col s12">
                                <textarea name="companyAddress" id="companyAddress" class="validate valid" cols="20"
                                          rows="3">{{$booking->invoc->companyAddress}}</textarea>
                                <label for="companyAddress" class="active" style="padding: 6px">Company Addresss</label>
                            </div>
                        @endif

                    </div>

                    <div class="row">
                        @foreach($booking->availabilityHours as $ind => $boAvDa)
                            @if($boAvDa["hourFrom"]!=null)
                                <div class="col s12">
                                    <p><b>{{$boAvDa["availabilityName"]}}</b></p>
                                    <p style="margin-left: 10px; margin-top: 5px;">Booking Time</p>
                                    @if($boAvDa["availabilityType"] == "Starting Time")
                                        <div class="input-field col s2">
                                            <input value="{{$boAvDa["hourFrom"]}}" class="file-path validate"
                                                   type="time" name="hourFrom_{{$ind}}" placeholder="Time">
                                        </div>
                                    @elseif($boAvDa["availabilityType"] == "Operating Hours")
                                        <div class="input-field col s2">
                                            <input value="{{$boAvDa["hourFrom"]}}" class="file-path validate"
                                                   type="time" name="hourFrom_{{$ind}}" placeholder="Time">
                                        </div>
                                        <div class="input-field col s2">
                                            <input value="{{$boAvDa["hourTo"]}}" class="file-path validate" type="time"
                                                   name="hourTo_{{$ind}}" placeholder="Time">
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>

                    {{--                <hr>--}}

                    {{--                <h4 style="margin-bottom: 20px">Booking Status</h4>--}}

                    {{--                <div class="row">--}}
                    {{--                    @if($booking->bookingStatus['status'] == 'Canceled')--}}
                    {{--                        <div class="col s4">--}}
                    {{--                            <div class="badge"--}}
                    {{--                                 style="background: #a94442; display: flex; justify-content: center; align-items: center; width: 60%; min-height: 35px;">--}}
                    {{--                                <span class="text-white">Canceled</span>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    @else--}}
                    {{--                        <div class="input-field col s6">--}}
                    {{--                            <select class="mdb-select" name="status" id="status">--}}
                    {{--                                <option value="" disabled selected>- Select Status -</option>--}}
                    {{--                                <option value="0">Approved</option>--}}
                    {{--                                <option value="3">Canceled</option>--}}
                    {{--                            </select>--}}
                    {{--                            <label for="status">Status</label>--}}
                    {{--                        </div>--}}
                    {{--                    @endif--}}

                    {{--                </div>--}}

                    <div class="row">
                        <div class="input-field col s6">
                            <button type="submit" class="btn btn-primary"
                                    @if($booking->bookingStatus['status'] == 'Canceled') disabled @endif>
                                {{ $booking->bookingStatus['status'] == 'Canceled' ? 'Canceled' : 'Update' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
            <!-- BOOKINGDETAILS::end -->
    @endif

    @if(!auth()->guard('supplier')->check())
        <!-- IMPORT::start -->
            <div id="import" class="tab-pane fade {{ !is_null($attr) && $attr == '#import' ? 'active in' : '' }}">

                @if($booking->extra_files_count)
                    <table class="table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($booking->extra_files as $file)
                            <tr>
                                <td>{{ $file->image_base_name }}</td>
                                <td>
                                    <div class="file-actions">
                                        <a href="/downloadExtraFile/{{$file->id}}">
                                            <i class="fa fa-download " style="font-size: 18px"></i>
                                        </a>
                                        <a href="{{ $file->image_name }}" target="_blank">
                                            <i class="fa fa-eye text-success" style="font-size: 18px"></i>
                                        </a>
                                        <a href="javascript:;" target="_blank" data-delete-file="{{ $file->id }}">
                                            <i class="fa fa-trash-o text-danger" style="font-size: 18px"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <hr>
                @endif

                <div class="row" style="margin-bottom: 10px">
                    <div class="col s6">
                        <button type="button" class="btn btn-primary" id="add-file-input"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <form data-submit="{{ url('/bookings-extra-file-import') }}" id="booking-extra-file-import-form">
                    @csrf
                    <input type="hidden" value="{{ $booking->id }}" name="booking_id">
                    <div class="import-file-container">
                        <div class="form-group">
                            <div style="display: flex; justify-content: center;">
                                <input type="text" class="form-control" name="filename[]" required
                                       style="height: 2.2rem;">
                                <input type="file" class="form-control" name="file[]" required>
                                <button class="btn btn-sm btn-danger active remove-unload-item"><i
                                        class="icon-cz-trash"></i></button>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary"
                            @if($booking->bookingStatus['status'] == 'Canceled') disabled @endif>
                        {{ $booking->bookingStatus['status'] == 'Canceled' ? 'Canceled' : 'Import' }}
                    </button>
                </form>
            </div>
            <!-- IMPORT::end -->
    @endif


    @if(!auth()->guard('supplier')->check())
        <!-- CONTACT::start -->
            <div id="contact" class="tab-pane fade {{ !is_null($attr) && $attr == '#contact' ? 'active in' : '' }}">
                <form id="mail-to-customer" data-submit="{{url("/send-mail-to-customer")}}">
                    @csrf
                    <div class="row">

                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <div class="input-field col s12">
                            <input id="email" name="mail_to" type="text"
                                   class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                                   value="{{json_decode($booking->travelers, true)[0]['email']}}"
                                   style="background: aliceblue">
                            <label for="email" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Email</label>
                        </div>

                        <div class="input-field col s12">
                            <input id="mail_title" name="mail_title" type="text"
                                   class="validate valid"
                                   value="Cityzore Booking Information" style="background: aliceblue">
                            <label for="mail_title" class="active">Title</label>
                        </div>

                        <div class="input-field col s12">
                            <ul class="text-center change-mail-message-language">
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase; background: #0e76a8; color: #fff"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['en'] ?? '' }}">en
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['fr'] ?? '' }}">fr
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['tr'] ?? '' }}">tr
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['ru'] ?? '' }}">ru
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['es'] ?? '' }}">es
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['de'] ?? '' }}">de
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['it'] ?? '' }}">it
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['pt'] ?? '' }}">pt
                                </li>
                                <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                    data-id="{{$booking->id}}" data-lang="{{ $booking->defaultMessage['nd'] ?? '' }}">nd
                                </li>
                            </ul>
                        </div>

                        <div class="input-field col s12">
                    <textarea name="mail_message" id="" class="validate valid" cols="30" rows="10"
                              style="background-color: aliceblue; height: 400px;">{{ $booking->defaultMessage['en'] ?? '' }}</textarea>
                            <label for="mail_message" class="active">Message</label>
                        </div>

                    </div>


                    <button type="submit" class="btn btn-primary"
                            @if($booking->bookingStatus['status'] == 'Canceled') disabled @endif>
                        {{ $booking->bookingStatus['status'] == 'Canceled' ? 'Canceled' : 'Send Mail' }}
                    </button>
                </form>


                @if($booking->contacts)
                    <ul class="text-center before_senders_lists" style="margin-top: 12px">

                        @foreach($booking->contacts as $key => $contact)
                            <li data-contact-message="{{$contact->mail_message}}"
                                style="padding: 4px 8px; width: 100%; border: solid 1px #ccc; background-color: {{ $contact->statusColor }}; cursor: pointer; margin-top: 2px;"
                                data-toggle="collapse" href="#mailCollapse{{$key}}" role="button" aria-expanded="false"
                                aria-controls="mailCollapse{{$key}}">
                                {{ $contact->status == 0 ? 'Queued' : ($contact->status == 1 ? 'Sent' : 'Failed' ) }}
                                <br>
                                {{$contact->sender->name}} - {{$contact->sender->surname}} -
                                ({{$contact->created_at->format("d/m/Y H:i:s")}}) <br>
                                <b>Files: </b> {{$contact->files}}
                            </li>
                            <div class="collapse" id="mailCollapse{{$key}}">
                                <div class="card card-body">
                                    {!! nl2br($contact->mail_message) !!}
                                </div>
                            </div>
                        @endforeach

                    </ul>
                @endif
                @if(!$booking->contact_booking_count)
                    <p style="margin-top: 10px; color: red; text-align: center; font-size: 13px">Please send a
                        message!</p>
                @elseif(!$booking->bookingInformation['mailCheck'])
                    <div class="mail-check-container">
                        <form data-submit="{{ url('checkCustomerMail') }}">
                            @csrf
                            <input type="hidden" value="{{ $booking->id }}" name="booking_id">
                            <input class="form-check-input" name="mail_check" type="checkbox" value="1" id="mailCheck">
                            <label class="form-check-label" for="mailCheck" onclick="$(this).closest('form').submit()">
                                <b>Check</b>
                            </label>
                        </form>
                    </div>
                @else
                    <p style="margin-top: 10px; color: #2fa360; text-align: center; font-size: 13px">Checked
                        on {{  json_decode($booking->contactBooking->check_information, 1)['check_date'] }}
                        by {{ json_decode($booking->contactBooking->check_information, 1)['checker'] }}</p>
                @endif
            </div>
            <!-- CONTACT::end -->
    @endif


    @if(!auth()->guard('supplier')->check())
        <!-- WHATSAPP::start -->
            <div id="whatsapp" class="tab-pane fade">
                <div class="input-field col s12">
                    <input id="phone" name="phone" type="text"
                           class="validate {{ !is_null($booking->travelers) ? 'valid' : '' }}"
                           value="{{json_decode($booking->travelers, true)[0]['phoneNumber'] ?? ''}}"
                           style="background: aliceblue">
                    <label for="phone" class="{{ !is_null($booking->travelers) ? 'active' : '' }}">Phone Number</label>

                    <div class="input-field col s12">
                        <ul class="text-center change-whatsapp-message-language">
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase; background: #0e76a8; color: #fff"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['en'] ?? '' }}">en
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['fr'] ?? '' }}">fr
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['tr'] ?? '' }}">tr
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['ru'] ?? '' }}">ru
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['es'] ?? '' }}">es
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['de'] ?? '' }}">de
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['it'] ?? '' }}">it
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['pt'] ?? '' }}">pt
                            </li>
                            <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;"
                                data-id="{{$booking->id}}" data-lang="{{ $booking->whatsappMessage['nd'] ?? '' }}">nd
                            </li>
                        </ul>
                    </div>

                    <div class="input-field col s12">
                    <textarea name="whatsapp_message" id="" class="validate valid" cols="30" rows="10"
                              style="background-color: aliceblue; height: 400px;">{{ $booking->whatsappMessage['en'] ?? '' }}</textarea>
                        <label for="whatsapp_message" class="active">Message</label>
                    </div>

                    <div class="input-field col s6">
                        @if($booking->bookingStatus['status'] == 'Canceled')
                            <a href="javascript:;" class="btn btn-primary" disabled="">Canceled</a>
                        @else
                            <a href="whatsapp://send?text={{ isset($booking->whatsappMessage['en']) ? urlencode($booking->whatsappMessage['en']) : '' }}&phone={{json_decode($booking->travelers, true)[0]['phoneNumber'] ?? ''}}"
                               class="btn btn-primary" id="share-to-whatsapp">Share To Whatsapp </a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- WHATSAPP::end -->
    @endif


    <!-- COMMENT::start -->
        <div id="comment" class="tab-pane fade {{ !is_null($attr) && $attr == '#comment' ? 'active in' : '' }}">
            <form data-submit="{{url("/add-comment-to-booking")}}">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <div class="row">
                    <div class="input-field col s12">
                    <textarea name="comment" id="comment" class="validate {{ $booking->adminComment ? 'valid' : '' }}"
                              cols="30" rows="10"
                              style="background-color: aliceblue; height: 400px;">{{ $booking->adminComment ?? '' }}</textarea>
                        <label for="comment" class="{{ $booking->adminComment ? 'active' : '' }}">Message</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col input-field s6">
                        <button type="submit" class="btn btn-primary"
                                @if($booking->bookingStatus['status'] == 'Canceled') disabled @endif>
                            {{ $booking->bookingStatus['status'] == 'Canceled' ? 'Canceled' : 'Add Comment' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- COMMENT::end -->

        <!-- SPECREFCODE::start -->
        <div id="special-ref-code"
             class="tab-pane fade {{ !is_null($attr) && $attr == '#special-ref-code' ? 'active in' : '' }}">
            <form data-submit="{{url("/add-special-ref-code")}}">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <div class="row">
                    <div class="input-field col s12">
                    <textarea name="specialRefCode" id="specialRefCode"
                              class="validate {{ $booking->specialRefCode ? 'valid' : '' }}" cols="30" rows="10"
                              style="background-color: aliceblue; height: 400px;">{{ $booking->specialRefCode ?? '' }}</textarea>
                        <label for="specialRefCode"
                               class="{{ $booking->specialRefCode ? 'active' : '' }}">Special Ref. Code</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col input-field s6">
                        <button type="submit" class="btn btn-primary">
                            SUBMIT
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- SPECREFCODE::end -->


    @if(!auth()->guard('supplier')->check())
        <!-- INVOICE::start -->
            <div id="invoice" class="tab-pane fade {{ !is_null($attr) && $attr == '#invoice' ? 'active in' : '' }}">
                <div class="row">
                    <div class="col s12">
                        @if($booking->invoice_numbers)
                            <table class="table-bordered">
                                <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                @foreach($booking->invoice_numbers as $invoice)
                                    <tbody>
                                    <tr>
                                        <td>
                                            @switch($invoice->type)
                                                @case(1)
                                                <p style="margin: 0">{{ $invoice->invoice_number }}</p>
                                                @break
                                                @case(11)
                                                <a target="_blank"
                                                   href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}">
                                                    {{$invoice->src}}
                                                </a>
                                                @break
                                                @case(12)
                                                <a target="_blank"
                                                   href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}">
                                                    {{$invoice->src}}
                                                </a>
                                                @break
                                                @case(13)
                                                <a target="_blank"
                                                   href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}">
                                                    {{$invoice->src}}
                                                </a>
                                                @break
                                                @case(21)
                                                <a target="_blank"
                                                   href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}">
                                                    {{$invoice->src}}
                                                </a>
                                                @break
                                            @endswitch
                                        </td>
                                        <td width="60">
                                            <a href="#" class="btn-clean btn-icon" style="text-align: center"
                                               onclick="console.log($(this).find('form').submit())">
                                                <form data-submit="{{ url('delete-invoice-number') }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                                </form>
                                                <i class="fa fa-trash-o" style="font-size: 20px"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>

                <form data-submit="{{ url('import-invoice-number') }}" style="margin-top: 20px"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <div class="input-field col s12">
                            <select id="invoice-select-input" class="mdb-select">
                                <option value="file">File</option>
                                <option value="number" selected>Number</option>
                            </select>
                            <label for="invoice-select-input">Choise Invoice Type</label>
                        </div>

                        <div class="invoice-wrapper">

                            <div class="input-field col s12">
                                <input type="text" name="invoice_number" placeholder="Invoice number">
                            </div>

                            <div class="input-field col s12" style="display: none">
                                <input type="file" name="invoice_file"
                                       style="border-bottom: 1px solid #9e9e9e; padding-bottom: 15px; width: 100%;">
                            </div>

                        </div>


                        <div class="input-field col s6">
                            <button type="submit" class="btn btn-primary">
                                SUBMIT
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <!-- INVOICE::end -->
        @endif
    </div>

</div>

<script>
    $(document).ready(function () {
        $('.dateranger-single').daterangepicker({
            format: 'dd/mm/yyyy',
            singleDatePicker: true,
            startDate: "{{ \Illuminate\Support\Carbon::make($booking->bookingDateTime['org'])->format('m/d/Y') }}"
        });

        $('.mdb-select').material_select();

        $(document).on('change', '#invoice-select-input', function () {

            $('.invoice-wrapper input').val('');

            if ($(this).val() == 'file') {
                $('.invoice-wrapper input[type="text"]').parent().hide()
                $('.invoice-wrapper input[type="file"]').parent().show()
            } else {
                $('.invoice-wrapper input[type="text"]').parent().show()
                $('.invoice-wrapper input[type="file"]').parent().hide()
            }
        })

        function deleteFormSubmit(el) {
            console.log(el)
        }
    })
</script>

