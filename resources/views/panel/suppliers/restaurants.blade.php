@include('panel-partials.head', ['page' => 'restaurants-index'])
@include('panel-partials.header', ['page' => 'restaurants-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre">
            <a href="#">
                @if(auth()->guard('admin')->check())
                    All Suppliers
                @elseif(auth()->guard('supplier')->check())
                    All Restaurants
                @endif
            </a>
        </li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>
                        @if(auth()->guard('admin')->check())
                            Supplier Details
                        @elseif(auth()->guard('supplier')->check())
                            Restaurant Details
                        @endif
                    </h4>
                    <a href="{{url('/supplier/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Contact </th>
                                <th>E-Mail</th>
                                <th>Phone</th>
                                <th>Website</th>
                                <th>Link Restaurant</th>
                                <th>Reg. At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($suppliers as $supplier)
                                <tr>
                                <td>{{$supplier->id}}</td>
                                <td>{{$supplier->companyName}}</td>
                                <td><span class="list-enq-name">{{ $supplier->contactName }} {{$supplier->contactSurname}}</span></td>
                                <td><a href="mailto:{{$supplier->email}}">{{ $supplier->email }}</a></td>
                                <td><a href="tel:{{$supplier->countryCode}}{{ $supplier->phoneNumber }}">{{$supplier->countryCode}}{{ $supplier->phoneNumber }}</a></td>
                                <td><a href="{{$supplier->website}}">{{$supplier->website}}</a></td>
                                <td><p><a class="modalOpen" data-supplier-id="{{$supplier->id}}" href="#ex1" rel="modal:open">Open Modal</a></p></td>
                                <td>{{ $supplier->created_at->format('d M, Y h:ia') }}</td>
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
<div id="ex1" class="modal">
    <select style="width: 50%" multiple="multiple" id="supplierSelect" class="select2"></select>
    <button id="sendRestaurants" type="button">SEND</button>
</div>


@include('panel-partials.scripts', ['page' => 'restaurants-index'])
@include('panel-partials.datatable-scripts', ['page' => 'restaurants-index'])
