@include('panel-partials.head', ['page' => 'comment-index'])
@include('panel-partials.header', ['page' => 'comment-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <input type="hidden" id="productType" value="productCZ">
                <div class="inn-title">
                    <h4>All Comments</h4>
                </div>
                <div class="col-md-12" style="margin-bottom: 20px; margin-top: 20px;">
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
                    <div class="col-md-12">
                        <label class="col-md-12">Attraction</label>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" name="attraction" id="attraction">
                                    <option value="" disabled selected>Choose Attraction</option>
                                    @foreach($attractions as $attraction)
                                        <option value="{{$attraction->id}}">{{$attraction->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @if(Auth::guard('admin')->check())
                    <div class="col-md-12">
                        <label class="col-md-12">Supplier</label>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" name="supplier" id="supplier">
                                    <option value="" disabled selected>Choose Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->companyName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <label class="col-md-12" style="margin-top: 20px;">Rating</label>
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <input type="checkbox" name="one" class="filled-in" id="one" value="1" checked="checked"/>
                                <label for="one">1</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="two" class="filled-in" id="two" value="1" checked="checked"/>
                                <label for="two">2</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="three" class="filled-in" id="three" value="1" checked="checked"/>
                                <label for="three">3</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="four" class="filled-in" id="four" value="1" checked="checked"/>
                                <label for="four">4</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="five" class="filled-in" id="five" value="1" checked="checked"/>
                                <label for="five">5</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="col-md-12" style="margin-top: 20px;">Status</label>
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <input type="checkbox" name="confirmed" class="filled-in" id="confirmed" value="1" checked="checked"/>
                                <label for="confirmed">Confirmed</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="notConfirmed" class="filled-in" id="notConfirmed" value="1" checked="checked"/>
                                <label for="notConfirmed">Not Confirmed</label>
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
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>Product Ref. Code</th>
                                    <th>Product Name</th>
                                    <th>Username</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Rate</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Delete</th>
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


@include('panel-partials.scripts', ['page' => 'comment-index'])
@include('panel-partials.datatable-scripts', ['page' => 'comment-index'])
