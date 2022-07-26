@include('panel-partials.head', ['page' => 'paymentlogs-index'])
@include('panel-partials.header', ['page' => 'paymentlogs-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-add-blog sb2-2-1">
    <div class="inn-title">
        <h4>Payment Logs</h4>
    </div>
    <div class="bor">
        <div class="row" style="margin-top: 50px">
            <table id="paymentlogs-table" class="table table-striped table-hover" style="width: 100%"></table>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'paymentlogs-index'])
{{--@include('panel-partials.datatable-scripts', ['page' => 'paymentlogs-index'])--}}
