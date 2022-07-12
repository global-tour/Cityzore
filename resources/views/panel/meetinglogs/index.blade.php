@include('panel-partials.head', ['page' => 'meetinglogs-index'])
@include('panel-partials.header', ['page' => 'meetinglogs-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Meeting Logs</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Process ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Option</th>
                                <th>Logger ID</th>
                                <th>Logger Email</th>
                                <th>Affected Name</th>

                                <th>Action</th>
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


@include('panel-partials.scripts', ['page' => 'meetinglogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'meetinglogs-index'])
