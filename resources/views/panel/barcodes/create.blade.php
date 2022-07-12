@include('panel-partials.head', ['page' => 'barcodes-create'])
@include('panel-partials.header', ['page' => 'barcodes-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add Booking</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left"
                                                                                 aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <form method="POST">
        @csrf
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="inn-title">
                    <h4>Add Barcode</h4>
                </div>

                <div class="bor">

                    @if(session()->has('error'))
                        <div id="alertBox" style="padding: 20px 10px" class="col-md-12 alert alert-danger">
                            <p class="text-danger">{{session()->get('error')}}</p>


                        </div>
                    @endif

                    @if(session()->has('success'))

                        @if(session()->get('success_data')["counter"] > 0)
                            <div id="alertBox" style="padding: 20px 10px" class="col-md-12 alert alert-success">
                                <p class="text-success">{{session()->get('success')}}</p>
                                Imported Data Count Successfully:
                                <big>{{session()->get('success_data')["counter"]}}</big>


                            </div>
                        @endif





                        @if(session()->get('failed_data')["counter"] > 0)
                            <div id="alertBox" style="padding: 20px 10px" class="col-md-12 alert alert-danger">
                                <p>
                                    Number of files that could not be uploaded because they are already Imported:
                                    <big>{{session()->get('failed_data')["counter"]}} </big> <br>

                                @if(session()->get('failed_data')["counter"] > 0)
                                    <ul>
                                        @foreach (session()->get('failed_data')["items"] as $item)
                                            <li>{{$item}}</li>
                                        @endforeach
                                    </ul>
                                    @endif
                                    </p>
                            </div>
                        @endif

                    @endif

                    <div id="alertBox" hidden style="padding: 20px 10px" class="col-md-12 alert alert-info">
                        <p>All barcodes have to be unique. Please enter correctly.</p>
                        <p>If you'll enter same value to two different field. System just save the first one.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" data-toggle="modal"
                                    data-target="#barcodeModal" class="btn btn-success active">
                                Import Barcodes
                            </button>
                        </div>
                        <div class="input-field col-lg-2 col-md-3 col-sm-5 col-xs-6">
                            <select class="browser-default custom-select" id="ticketTypeSelect"
                                    name="ticketTypeSelect">
                                <option disabled selected>Select Ticket Type</option>
                                @foreach($ticketType as $tt)
                                    <option value="{{$tt->id}}">{{$tt->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-fieldcol-lg-2 col-md-3 col-sm-5 col-xs-6">
                            <input style="display: none" name="barcodeCount" id="barcodeCount" type="text"
                                   placeholder="How many barcodes?">
                        </div>
                        <div class="input-field col-lg-2 col-md-3 col-sm-5 col-xs-6">
                            <button hidden id="barcodeCreateButton" type="button" class="btn btn-primary">Create
                            </button>
                        </div>
                        <div class="input-field col-lg-2 col-md-3 col-sm-5 col-xs-6">
                            <button hidden id="deleteButton" type="button" class="btn btn-primary">Delete All
                            </button>
                        </div>
                        <div id="barcodeDiv" class="input-field col s12">
                        </div>
                        <button id="sendBarcodes" type="button" class="btn btn-primary">Save Barcodes</button>


                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<!-- Modal  Barcode-->
<div class="modal fade" id="barcodeModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Excel File Import Form</h4>
            </div>
            <form id="cruiseBarcodeImport" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label for="importTicketType">Select Ticket Type</label>
                        <select class="browser-default custom-select form-control" id="importTicketType"
                                name="ticketTypeSelect">
                            <option disabled selected>Select Ticket Type</option>
                            @foreach($ticketType as $tt)
                                <option value="{{$tt->id}}">{{$tt->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="file">Choose Excel File For Import</label>
                        <input type="file" id="file" name="file" class="form-control" value="Import File">
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <a href="{{ url('storage/barcodeTemplate.xlsx') }}" download="download" class="btn btn-warning">Download Excel Template</a>
                        <button type="submit" class="btn btn-success active deactiveOnClick">İmport File</button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>


@include('panel-partials.scripts', ['page' => 'barcodes-create'])
