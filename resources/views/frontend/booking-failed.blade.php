@include('frontend-partials.head', ['page' => 'booking-failed'])
@include('frontend-partials.header')

<div class="container" style="margin-top: 5%;">
    <div class="alert alert-danger" role="alert">
        <p>{{__('bookingFailed')}}</p>
        <p>{{__('bookingFailed1')}}</p>
        <p>{{__('errorCode')}}: {{$errorCode}}</p>
        <p>{{__('errorMessage')}}: {{$errorMessage}}</p>
    </div>
</div>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'booking-failed'])

