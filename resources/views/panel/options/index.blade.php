@include('panel-partials.head', ['page' => 'option-index'])
@include('panel-partials.header', ['page' => 'option-index'])
@include('panel-partials.sidebar')


<div>
    <input type="hidden" id="userType" value="{{auth()->guard('admin')->check() ? 1 : 0}}">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Options</h4>
                    <a href="{{url('/option/create')}}" class="btn btn-default pull-right">Add New</a>

                    <div class="row">
                        <div class="col-md-3">
                            <label class="col-md-12">Availability</label>
                            <select name="availabilitySelect" id="availabilitySelect"
                                    class="browser-default custom-select col-md-9">
                                <option value="">ALL</option>
                                <option value="VALID">VALID</option>
                                <option value="EXPIRED">EXPIRED</option>
                            </select>
                        </div>

                        @if(auth()->guard('admin')->check())
                            <div class="col-md-3">
                                <label for="" class="col-md-12">Suppliers</label>
                                <select name="availabilitySelect" id="availabilitySelect"
                                        class="browser-default custom-select col-md-9">
                                    <option value="">ALL</option>
                                    <option value="VALID">VALID</option>
                                    <option value="EXPIRED">EXPIRED</option>
                                </select>
                            </div>
                        @endif

                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-2">
                            <button id="applyFiltersButton" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </div>

                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <input type="hidden" id="pageID" name="pageID" value="0">
                            <input type="hidden" id="isRun" name="isRun" value="0">
                            <thead>
                            <tr>
                                <th>Reference Code</th>
                                <th>Title</th>
                                <th>Pricing</th>
                                <th>Availability</th>
                                <th>Connected Products</th>
                                <th>Commission</th>
                                @if(auth()->guard('admin')->check())
                                    <th>Supplier</th>
                                @endif
                                <th>Publish</th>
                                <th>Actions</th>
                                @if (auth()->guard('admin')->check())
                                    <th>API</th>
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


@include('panel-partials.scripts', ['page' => 'option-index'])
@include('panel-partials.datatable-scripts', ['page' => 'option-index'])
@include('layouts.modal-box.option-products')
@include('layouts.modal-box.option-supp')
