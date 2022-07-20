@include('panel-partials.head', ['page' => 'voucher-template-index'])
@include('panel-partials.header', ['page' => 'voucher-template-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Voucher Templates</h4>
                    <a href="{{url('/voucher-template/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($voucherTemplates as $voucher)
                                <tr>
                                    <th>{{$voucher->id}}</th>
                                    <th>{{$voucher->name}}</th>

                                    <th>
                                        <a href="{{url('voucher-template/'.$voucher->id.'/edit')}}"><i class="icon-cz-edit"></i></a>
                                    </th>
                                    <th>
                                        <form method="POST" action="{{url('voucher-template/'.$voucher->id)}}">
                                            @method('DELETE')
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


@include('panel-partials.scripts', ['page' => 'voucher-template-index'])
@include('panel-partials.datatable-scripts', ['page' => 'voucher-template-index'])
