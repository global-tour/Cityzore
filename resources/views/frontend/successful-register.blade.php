@include('frontend-partials.head', ['page' => 'successful-register'])
@include('frontend-partials.header')


<div class="container" style="margin-top: 5%;">
    <div class="alert" role="alert" style="text-align: center;text-align: center;border: 1px dotted #c62346;background: rgba(33, 255, 5, 0.05);">
        <span style="font-size: 50px;color: #c62346;padding-top: 20px;height: 65px;width: 65px;border: 1px solid #c62346;border-radius: 50%;display: inline-block;">&#10003;</span>
        <p style="font-size: 25px;padding: 3%;">Succesful Registration</p>
        <p style="font-size: 18px;">You have been registered successfully ! Please check your e-mail address which you use when register.</p>
        <a href="{{url('/')}}"><button class="btn btn-success" style="margin-top: 3%;background-color:#c62346;border-color: #c62346;">Back to Home</button></a>
    </div>
</div>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'successful-register'])

