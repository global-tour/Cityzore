@include('panel-partials.head', ['page' => 'multiple-tickets'])
@include('panel-partials.header', ['page' => 'multiple-tickets'])
@include('panel-partials.sidebar')


<div class="ad-v2-hom-info">
    <div class="ad-v2-hom-info-inn hidden-xs">
        <ul>
            @foreach($ticketTypes as $ticket)
                <li>
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <p><i class="fa fa-arrow-up up"></i>{{$ticket->name }}</p>
                            <h5>{{ $ticket->is_used }} Used / {{ $ticket->barcodes_count }} Total : <b>Remaining {{ $ticket->barcodes_count - $ticket->is_used }}</b></h5>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#">Create Ticket</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">

    @if(session('status'))
        <div class="alert alert-danger">
            {{ session('status') }}
        </div>
        @endif
    <form action="{{url('/multiple-tickets')}}" enctype="multipart/form-data" method="POST" id="multiple-ticket-form">
        @csrf
        @method('POST')
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Create Ticket</h4>
                    </div>
                    <div class="bor">
                        <div id="alertBox" hidden style="padding: 20px 10px" class="col s12 alert alert-info">
                            <p>All barcodes have to be unique. Please enter correctly.</p>
                            <p>If you'll enter same value to two different field. System just save the first one.</p>
                        </div>
                        <div class="row">
                            <div class="input-field col col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                <select class="browser-default custom-select" id="ticketTypeSelect" name="ticketTypeSelect">
                                    <option disabled selected>Select Ticket Type</option>
                                    @foreach($ticketTypes as $tt)
                                        <option value="{{$tt->id}}">{{$tt->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-field col col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                <input style="display: none" name="barcodeCount" id="barcodeCount" type="number" required placeholder="How many tickets?">
                            </div>
                            <div class="input-field col col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <input name="barcodeDescription" id="barcodeDescription" type="text" required placeholder="Barcode description...">

                            </div>
                            <div class="input-field col col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <input name="bookingDate" type='text' class='datepicker-from' data-language='en' autocomplete="off"  placeholder="Date Range" required>
                            </div>

                            <div id="barcodeCount"></div>
                            <div id="barcodeDiv" class="input-field col s12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary downloadTicketsButton" data-usable="true">Download Tickets</button>
        <button style="display:none;" type="button" class="btn btn-warning ticket-fuse active icon-cz-umbrella">Activate Download Button</button>
    </form>
</div>


@include('panel-partials.scripts', ['page' => 'multiple-tickets'])

