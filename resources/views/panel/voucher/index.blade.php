@include('panel-partials.head', ['page' => 'voucher-index'])
@include('panel-partials.header', ['page' => 'voucher-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Vouchers</h4>
                    <a href="{{url('/voucher/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>Booking Reference Number</th>
                                    <th>Traveler</th>
                                    <th>Download</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <th>{{$voucher->bookingRefCode}}</th>
                                    <th>{{$voucher->traveler}}</th>
                                    <th>
                                        <button class="btn btn-xs btn-primary btn-block" style="width: 150px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;"><a href="{{url('print-voucher/'.$voucher->id)}}" target="_blank" style="color: black; font-weight: bold">Download Voucher</a></button>
                                    </th>
                                    <th>
                                        <a href="{{url('voucher/'.$voucher->id.'/edit')}}"><i class="icon-cz-edit"></i></a>
                                    </th>
                                    <th>
                                        <form method="POST" action="{{url('voucher/'.$voucher->id.'/delete')}}">
                                            @method('POST')
                                            @csrf
                                            {{ Form::button('<i class="icon-cz-trash" style="background: #ff0000!important;"></i>', ['style="background:transparent;border:none;"', 'type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                        </form>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'voucher-index'])
@include('panel-partials.datatable-scripts', ['page' => 'voucher-index'])
