@include('frontend-partials.head', ['page' => 'login'])

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$requestURL = Request::url();
$loginUrl = $langCodeForUrl.'/login';
$passwordResetUrl = $langCodeForUrl.'/password/reset';
$registerUrl = $langCodeForUrl.'/register';
if (strpos($requestURL, 'admin') !== false || strpos($requestURL, 'supplier') !== false) {
    $loginUrl = url('/login');
    $passwordResetUrl = url('/password/reset');
    $registerUrl = url('/register');
}
?>

    <div style="margin-top: 70px" class="container">
        <div class="row justify-content-center">
            <table class="table table-bordered" style="width:50%;background-color:white;">
                <tbody>
                <tr>
                    <th class="triangle" rowspan="2" style="width:92%;padding-left: 15%;padding-right: 15%;">
                        <a href="{{url('/')}}">
                            <img src="{{asset('img/paris-city-tours-logo.png')}}" style="margin-top: 3%;margin-bottom: 5%;width: 200px;"  alt="Paris City Tours" />
                        </a>
                        <p style="font-size: 15px !important;">Welcome back, <br> Please login to your account.</p>
                        <div style="display: none;" id="loginModalFormAlertDiv" class="alert alert-danger" role="alert">

                        </div>
                        <form id="loginModalForm" method="POST" action="{{$loginUrl}}">
                            @csrf
                            @if (strpos($requestURL, 'supplier') !== false)
                                <input id="supplier" type="radio" name="guard" value="supplier" checked>
                                <label for="supplier">Supplier</label>
                                <input id="subUser" type="radio" name="guard" value="subUser">
                                <label for="subUser">Sub User</label>
                            @else
                                <input checked type="radio" hidden name="guard" id="guard" value="admin">
                            @endif
                                <div class="form-group row">
                                <label class="col-md-5 col-form-label">{{__('email')}}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label">{{__('password')}}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                            <div class="form-group row">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{__('rememberMe')}}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <button id="loginSubmitButton" type="submit" class="btn btn-primary" style="margin-bottom: 5%;color:black;background-color: transparent">
                                    {{__('login')}}
                                </button>
                                <a style="box-shadow: none;font-size: 10px;" class="btn btn-link" href="{{$passwordResetUrl}}">
                                    {{__('forgotYourPassword')}}
                                </a>
                            </div>
                        </form>
                    </th>
                    <th onclick="document.location = '<?php echo $registerUrl; ?>'" style="box-shadow: 3px 3px 5px 6px #ffed2f78;cursor: pointer;background-color: #f9af12;height: 50%;">
                        <div class="vertical">
                            <span style="color:white;font-size: 18px !important;padding-top: 13px;">Create an Account</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th style="box-shadow: 3px 3px 5px 6px #8e181878;background-color: #b64a2a;height: 50%;padding-bottom: 10px;cursor: pointer;">
                        <a href="https://www.cityzore.com/become-a-commissioner">
                            <div class="vertical">
                                <span style="color: white;font-size: 18px !important;padding-top:71px;">Become a Supplier</span>
                            </div>
                        </a>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@include('frontend-partials.general-scripts', ['page' => 'login'])
