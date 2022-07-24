@include('panel-partials.head', ['page' => 'on-goings'])
@include('panel-partials.header', ['page' => 'on-goings'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>
                        On-Goings
                    </h4>
                </div>
                <div class="col-md-12" style="margin-bottom: 20px; margin-top: 10px;">
                    <h5>Filters <button class="btn btn-primary" id="showHideFilters" data-shown="1">Hide</button></h5>
                </div>
                <div class="filters">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="col-md-12" style="margin-top: 20px;">Product</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select class="browser-default custom-select select2" name="product" id="productSelect">
                                            <option value="-1" selected>Choose A Product</option>
                                            @foreach ($products  as $key => $value)
                                              <option value="{{$key}}">{{'#'.$key.' '.$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="col-md-12" style="margin-top: 20px;">Option</label>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <select class="browser-default custom-select select2" name="option" id="optionSelect">

                                        </select>
                                    </div>
                                </div>
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
                        <div class="col-md-3 col-md-offset-4">
                            <button id="remove-old-cart-items-from-on-goings" class="btn btn-danger">Remove Old Cart Items From On goings</button>
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
                                <th>Booking Items</th>
                                <th>Created At</th>
                                <th>Product Title</th>
                                <th>Option Title</th>
                                <th>Total Price</th>
                                <th>From</th>
                                <th>DateTime</th>
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


@include('panel-partials.scripts', ['page' => 'on-goings'])
@include('panel-partials.datatable-scripts', ['page' => 'on-goings'])
