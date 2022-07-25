@include('panel-partials.head', ['page' => 'supplier-index'])
@include('panel-partials.header', ['page' => 'supplier-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>
                        @if (auth()->guard('admin')->check())
                            Supplier Details
                        @elseif(auth()->guard('supplier')->check())
                            Restaurant Details
                        @endif
                    </h4>
                    <a href="{{url('/supplier/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h5>Filters <button class="btn btn-primary" id="showHideFilters" data-shown="1">Hide</button></h5>
                </div>
                <div class="filters">
                    <div class="col-md-12">
                        <label class="col-md-12" style="margin-top: 20px;">Country</label>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" name="country" id="country">
                                    <option value="" disabled selected>Choose Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->countries_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 25px; margin-top: 25px;">
                        <div class="col-md-1">
                            <button id="applyFiltersButton" class="btn btn-primary">Apply</button>
                        </div>
                        <div class="col-md-2">
                            <button id="clearFiltersButton" class="btn btn-primary">Clear</button>
                        </div>
                    </div>
                </div>
                <div class="tab-inn">

                  @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session()->has('success'))
                
                <div class="alert-success" style="margin: 20px; padding: 20px;">
                    {{session()->get('success')}}
                </div>

                @endif




                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact </th>
                                <th>E-Mail</th>
                                <th>Country</th>
                                <th>City</th>
                                @if (auth()->guard('admin')->check())
                                <th>Is Restaurant?</th>
                                <th>Status</th>
                                @else
                                <th>Link Restaurant</th>
                                @endif
                                @if (auth()->guard('admin')->check())
                                <th>Edit</th>
                                <th>Licenses</th>
{{--                                <th>Delete</th>--}}
                                @endif
                            </tr>
                            </thead>
                            <tbody>

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
    <a href="ex1" class="close-modal remove-modal" style="z-index:99999; position:absolute;">Close</a>
</div>


@include('panel-partials.scripts', ['page' => 'supplier-index'])
@include('panel-partials.datatable-scripts', ['page' => 'supplier-index'])
