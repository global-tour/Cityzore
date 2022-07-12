@include('panel-partials.head', ['page' => 'paymentlogs-index'])
@include('panel-partials.header', ['page' => 'paymentlogs-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Payment Logs</h4>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Process ID</th>
                                <th>User ID</th>
                                <th>Option Title</th>
                                <th>Cart ID</th>
                                <th>Code</th>
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


@include('panel-partials.scripts', ['page' => 'paymentlogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'paymentlogs-index'])
