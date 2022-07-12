@include('panel-partials.head', ['page' => 'bookings-comment'])
@include('panel-partials.header', ['page' => 'bookings-comment'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add Booking Comment</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Add Booking Comment</h2>
        <form method="POST" action="{{url('booking/storeComment')}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="bookingID" class="bookingID" value="{{$id}}">
            <div class="row">
                <div class="form-group">
                    <div class="input-field col s12">
                        <textarea id="bookingComment" name="bookingComment" type="text" class="materialize-textarea form-control"></textarea>
                        <label for="bookingComment">Booking Comment</label>
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


@include('panel-partials.scripts', ['page' => 'bookings-comment'])
