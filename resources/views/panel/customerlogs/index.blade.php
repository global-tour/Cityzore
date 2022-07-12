@include('panel-partials.head', ['page' => 'customerlogs-index'])
@include('panel-partials.header', ['page' => 'customerlogs-index'])
@include('panel-partials.sidebar')

<style>
        .datepicker{
        width: 100% !important;

    }
    #table-container{
        margin-top: 50px;
    }

</style>


    <div class="container">


   <div class="row" style="margin-bottom: 30px;">

    <div class="date-wrap">

        <form action="#">

            <div class="form-group">
                <select name="month" class="shaselect" style="display: block;">

                    @foreach($allMonths as $month)
                    <option value="{{$month->new_date}}">{{$month->month_date}} - Total Login: {{$month->data}}</option>
                    @endforeach
                </select>
            </div>
        </form>


    </div>


  </div>

     </div>






<div class="container" id="chart-container">
 <div class="row">

    <div id="resizable" style="height: 370px;border:1px solid gray;">
    <div id="chartContainer" style="height: 100%; width: 100%;"></div>
</div>


  </div>

</div>





<div class="sb2-2-3" id="table-container">













    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Customer Logs</h4>
                </div>




                <div class="tab-inn">










                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Process ID</th>
                                <th>Booking ID</th>
                                <th>Reference Number</th>
                                <th>Customer Email</th>
                                <th>Customer Name</th>
                                <th>Option</th>
                                <th>Action</th>
                                <th>Date</th>
                                <th>Total</th>
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

<div class="modal fade" id="customerLogsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="modal-title" id="exampleModalLabel">Customer Logs</h5>
                    </div>
                </div>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background-color: #ff3200" data-dismiss="modal" onclick="$('#customerLogsModal .modal-body').html('')">Close</button>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'customerlogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'customerlogs-index'])
