@include('panel-partials.head', ['page' => 'availability-index'])
@include('panel-partials.header', ['page' => 'availability-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Availabilities</h4>
                    <a href="{{url('/av/create')}}" class="btn btn-default pull-right">Add New</a>
                    <div style="margin-top: 15px;">
                        <div>
                            <input type="checkbox" name="type" class="filled-in" id="expiredAvailabilities">
                            <label for="expiredAvailabilities">Expired Availabilities</label>
                        </div>
                        <div>
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
                                <th>Company Name</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Connected Options</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
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


@include('panel-partials.scripts', ['page' => 'availability-index'])
@include('panel-partials.datatable-scripts', ['page' => 'availability-index'])
