@include('panel-partials.head', ['page' => 'bookings-specialrefcode'])
@include('panel-partials.header', ['page' => 'bookings-specialrefcode'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add Special Reference Code</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Add Special Reference Code</h2>
        <form style="margin-top: 50px;" method="POST" action="{{url('booking/storeSpecialRefCode')}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="bookingID" class="bookingID" value="{{$booking->id}}">
            <div class="row">
                <div class="form-group">
                    <div class="input-field col s12">
                        <textarea id="specialRefCode" name="specialRefCode" type="text" class="form-control" style="height: 100px;">{{$booking->specialRefCode}}</textarea>
                        <label for="specialRefCode" style="top: 0.2rem;">Special Reference Code</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'bookings-specialrefcode'])
