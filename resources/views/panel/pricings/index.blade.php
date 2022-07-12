@include('panel-partials.head', ['page' => 'pricings-index'])
@include('panel-partials.header', ['page' => 'pricings-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Pricings</h4>
                    <a href="{{url('/pricing/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <input type="hidden" id="pageID" name="pageID" value="0">
                            <input type="hidden" id="isRun" name="isRun" value="0">
                            <thead>
                            <tr>
                                <th>Pricing Name</th>
                                <th>Type</th>
                                <th>Company</th>
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


@include('panel-partials.scripts', ['page' => 'pricings-index'])
@include('panel-partials.datatable-scripts', ['page' => 'pricings-index'])
