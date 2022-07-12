@include('frontend-partials.head', ['page' => 'external-payment-successful'])
@include('frontend-partials.header')


<div class="container" style="margin-top: 5%;">
    <div class="alert alert-success" role="alert" style="text-align: left;">
        <p>{{__('paymentSuccessful')}}</p>
    </div>
</div>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'external-payment-successful'])

