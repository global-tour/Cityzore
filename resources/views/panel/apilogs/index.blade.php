@include('panel-partials.head', ['page' => 'apilogs-index'])
@include('panel-partials.header', ['page' => 'apilogs-index'])
@include('panel-partials.sidebar')


    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>All Actions</h4>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 20px; margin-top: 20px;">
                        <h5>Filters <button class="btn btn-primary" id="showHideFilters" data-shown="1">Hide</button></h5>
                    </div>
                    <div class="filters">
                        <div class="col-md-12">
                            <label class="col-md-12" style="margin-top: 20px;">Request Type</label>
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="getAvailabilities" value="1" checked="checked"/>
                                    <label for="getAvailabilities">Get Availabilities</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="reserve" value="1" checked="checked"/>
                                    <label for="reserve">Reserve</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="cancelReservation" value="1" checked="checked"/>
                                    <label for="cancelReservation">Cancel Reservation</label>
                                </div>
                                <div class="col-md-offset-6"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="book" value="1" checked="checked"/>
                                    <label for="book">Book</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="cancelBooking" value="1" checked="checked"/>
                                    <label for="cancelBooking">Cancel Booking</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" name="type" class="filled-in" id="notifyPush" value="1" checked="checked"/>
                                    <label for="notifyPush">Notify Availability Update</label>
                                </div>
                                <div class="col-md-offset-6"></div>
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
                    </div>
                    <div class="tab-inn">
                        <div class="table-responsive responsive table-desi" style="overflow-x: inherit;">
                            <table id="datatable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Request Type</th>
                                    <th>Query</th>
                                    <th>Request/Response</th>
                                    <th>Option Ref. Code</th>
                                    <th>Request Time</th>
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


@include('panel-partials.scripts', ['page' => 'apilogs-index'])
@include('panel-partials.datatable-scripts', ['page' => 'apilogs-index'])
