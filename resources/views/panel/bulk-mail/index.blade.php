@include('panel-partials.head', ['page' => 'bulkmail'])
@include('panel-partials.header', ['page' => 'bulkmail'])
@include('panel-partials.sidebar')

<div>
    <input type="hidden" id="userType" value="{{auth()->guard('admin')->check() ? 1 : 0}}">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp" style="padding: 20px">

                <div class="row">
                    <div class="col-md-2">
                        <button type="button" data-toggle="modal" data-target="#sendToSelectedModal"
                                class="waves-effect waves-light btn deep-orange">Send To Selected
                        </button>
                    </div>

                    {{--                    <div class="col-md-2">--}}
                    {{--                        <button type="button" id="sendToFiltered" class="waves-effect waves-light btn deep-orange" disabled>Send To Filtered</button>--}}
                    {{--                    </div>--}}
                </div>

                <div class="row">
                    <div class="tab-inn">
                        <table class="table table-striped table-hover" id="bulkmail-table" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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

<div class="modal fade" id="sendToSelectedModal" tabindex="-1" role="dialog" aria-labelledby="sendToSelectedModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="sendToSelectedModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <label for="">Subject</label>
                        <input type="text" name="subject" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <input id="mailContent" name="content">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="sendToSelected" class="btn waves-light">Send</button>
            </div>
        </div>
    </div>
</div>

@include('panel-partials.scripts', ['page' => 'bulkmail'])
