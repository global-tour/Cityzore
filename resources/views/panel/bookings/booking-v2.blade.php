@include('panel-partials.head', ['page' => 'bookings-v2-index'])
@include('panel-partials.header', ['page' => 'bookings-v2-index'])
@include('panel-partials.sidebar')

<div>
    <input type="hidden" id="userType" value="{{auth()->guard('admin')->check() ? 1 : 0}}">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp" style="padding: 20px">
                <div class="row" style="float: right">
                    <div class="col-md-6">
                        <a href="#" id="expand-collapse-div">
                            <i class="fa fa-expand" style="font-size: 18px"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="responsive-table">
                        <table class="table table-hover" id="all-bookings-table" width="100%">
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="booking-offcanvas"></div>
<div class="offcanvas-overlay"></div>

@include('panel-partials.scripts', ['page' => 'bookings-v2-index'])
