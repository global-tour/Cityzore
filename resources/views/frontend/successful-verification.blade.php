@include('frontend-partials.head', ['page' => 'successful-verification'])
@include('frontend-partials.header')


<div class="container" style="margin-top: 5%;">
    <div class="alert" role="alert" style="text-align: center;text-align: center;border: 1px dotted #c62346;background: rgba(33, 255, 5, 0.05);">
        <span style="font-size: 50px;color: #c62346;padding-top: 20px;height: 65px;width: 65px;border: 1px solid #c62346;border-radius: 50%;display: inline-block;">&#10003;</span>
        <p style="font-size: 25px;padding: 3%;">Succesful Verification</p>
        <p style="font-size: 18px;padding: 1%">Dear {{$user->name}} {{$user->surname}},</p>
        <p style="font-size: 18px;">You have been verification your account successfully! You could log in now to CityZore.</p>
        <p style="font-size: 18px;">Enjoy your trip!</p>
        <a href="{{url('/login')}}"><button class="btn btn-success" style="margin-top: 3%;background-color:#c62346;border-color: #c62346;">Click Here For To Login !</button></a>
    </div>
</div>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'successful-verification'])

