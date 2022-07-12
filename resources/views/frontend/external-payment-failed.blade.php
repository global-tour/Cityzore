@include('frontend-partials.head', ['page' => 'external-payment-failed'])
@include('frontend-partials.header')

<div class="container" style="margin-top: 5%;">
    <div class="alert alert-danger" role="alert">
        <p>{{__('paymentFailed')}}</p>
        <p>{{__('paymentFailed1')}}</p>
        <p>{{__('errorCode')}}: {{$errorCode}}</p>
        <p>{{__('errorMessage')}}: {{$errorMessage}}</p>
    </div>
</div>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'external-payment-failed'])

