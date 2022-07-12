@include('panel-partials.head', ['page' => 'userlogs-index'])
@include('panel-partials.header', ['page' => 'userlogs-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Actions</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Product Ref. Code</th>
                                <th>Option Ref. Code</th>
                                <th>Page</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Date</th>
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


@include('panel-partials.scripts', ['page' => 'userlogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'userlogs-index'])
