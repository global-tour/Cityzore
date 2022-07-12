@include('frontend-partials.head', ['page' => 'register'])

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$requestURL = Request::url();
$loginUrl = $langCodeForUrl.'/login';
$registerUrl = $langCodeForUrl.'/register';
if (strpos($requestURL, 'admin') !== false || strpos($requestURL, 'supplier') !== false) {
    $loginUrl = url('/login');
    $registerUrl = url('/register');
}
?>

<div style="margin-top: 50px" class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 0px;padding-bottom: 0px;">
                <div class="container">
                    <a href="{{url('/')}}">
                        <img src="{{asset('img/paris-city-tours-logo.png')}}" style="width: 200px; height: 60px;">
                    </a>
                </div>
            </div>
            <div class="card" style="background-image: url({{asset('/img/eiffel.jpg')}}); background-size: contain;border-radius: 5%;border:none;">
                <div class="col-md-offset-3 col-md-9 card-body" style="background-color: white;">
                    <form id="registerForm" method="POST" action="{{url($registerUrl)}}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{__('name')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="surname" class="col-md-4 col-form-label text-md-right">{{__('surname')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="surname" type="text" class="form-control" name="surname" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{__('email')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="emailRegister" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{__('password')}}</label>
                            <div class="col-md-6">
                                <input  style="height: 25px" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{__('confirmPassword')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="countryCode" class="col-md-4 col-form-label text-md-right">{{__('countryCode')}}</label>
                            <div class="col-md-6">
                                <select style="height: 30px" id="countryCode" class="form-control browser-default" name="countryCode">
                                    <option value="" selected>{{__('countryCode')}}</option>
                                    <option data-countryCode="FR" value="33">France (+33)</option>
                                    <option data-countryCode="TR" value="90">Turkey (+90)</option>
                                    <option data-countryCode="UK" value="44">United Kingdom (+44)</option>
                                    <option data-countryCode="US" value="1">United States (+1)</option>
                                    <optgroup label="Other countries">
                                        @foreach($countries as $country)
                                            <option data-countryCode="{{$country->countries_iso_code}}" value="{{$country->countries_phone_code }}">{{$country->countries_name}} (+{{$country->countries_phone_code}})</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phoneNumber" class="col-md-4 col-form-label text-md-right">{{__('phoneNumber')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="phoneNumber" type="text" class="form-control" name="phoneNumber">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{__('address')}}</label>
                            <div class="col-md-6">
                                <input  style="height: 25px" id="address" type="text" class="form-control" name="address">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            @error('g-recaptcha-response')
                            <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <div class="col-md-6">
                                <div class="g-recaptcha" data-sitekey="6LdOaeQfAAAAAEW47JrgIyKmKWA5dHd-4MIzbmrt"></div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button style="margin-left:20px;width: 160px;" class="btn btn-primary" id="formButton">
                                    {{__('register')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('frontend-partials.general-scripts', ['page' => 'register'])
