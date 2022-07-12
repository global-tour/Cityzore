@include('panel-partials.head', ['page' => 'errorlogs-index'])
@include('panel-partials.header', ['page' => 'errorlogs-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Error Logs</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>URL</th>
                                <th>Code</th>
                                <th>File</th>
                                <th>Line</th>
                                <th>Message</th>
                                <th>Date/Time</th>
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


@include('panel-partials.scripts', ['page' => 'errorlogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'errorlogs-index'])
