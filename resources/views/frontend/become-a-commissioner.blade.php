@include('frontend-partials.head', ['page' => 'become-a-commissioner'])

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 4%;">
    <div class="container">
        <a href="{{url('/')}}"><img src="{{asset('img/paris-city-tours-logo.png')}}" style="width: 200px; height:60px;"></a>
    </div>
</div>
<section>
    <input type="hidden" id="translationArray" value="{{$translationArray}}">
    <div class="rows tb-space pad-top-o pad-bot-redu">
        <div class="container supplier-form">
            <div class="left-side col-md-4 hidden-xs">
                <div class="row" style="margin-bottom: 20%;">
                    <img src="{{asset('img/supplier1.jpg')}}" alt="" class="center">
                    <div class="steps" align="center">
                        <span class="dot">1</span>
                        <span style="font-size: 13px;">{{__('becomeACommissioner1')}}</span>
                    </div>
                </div>
                <div class="row">
                    <img src="{{asset('img/supplier2.jpg')}}" alt="" class="center">
                    <div class="steps">
                        <span class="dot2">2</span>
                        <span style="font-size: 13px;">{{__('becomeACommissioner2')}}</span>
                    </div>
                </div>
            </div>
            <div class="right-side col-md-8 col-xs-12">
                <div class="spe-title">
                    <h2 style="font-size:26px!important;margin-top: 4%;">{{__('becomeACommissioner')}}</h2>
                    @if(session()->has("error"))

                        <strong style="color: darkred">{{session()->get("error")}}</strong>

                    @endif
                    <div class="tab-inn">
                        @error('email')
                        <span class="col-md-12 invalid-feedback" role="alert">
                            <strong style="color: darkred">&times; {{ $message }}</strong>
                        </span>
                        @enderror
                        @error('password')
                        <span class="col-md-12 invalid-feedback" role="alert">
                            <strong style="color:darkred">&times;  {{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div>
                        <form id="becomeACommissionerForm"
                              action="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('become-a-commissioner-store'))}}"
                              method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" id="recaptchaToken" name="recaptchaToken">
                            <input type="hidden" id="recaptchaAction" name="recaptchaAction">
                            <div class="row">
                                <div class="input-field col s12">
                                    <p>{{__('companyName')}}</p>
                                    <input type="text"
                                           class="form-control validate  @error('companyName') is-invalid @enderror"
                                           id="companyName" name="companyName" value="{{ old('companyName') }}" required
                                           autocomplete="companyName" autofocus>
                                    @error('companyName')
                                    <span class="invalid-feedback" role="alert"></span>
                                    @enderror
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('contactName')}}</p>
                                    <input id="contactName" type="text" class="form-control validate @error('contactName') is-invalid @enderror" name="contactName"
                                           required>
                                    @error('contactName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('contactSurname')}}</p>
                                    <input id="contactSurname" type="text" class="form-control validate @error('contactSurname') is-invalid @enderror"
                                           name="contactSurname" value="{{ old('contactSurname') }}" required
                                           autocomplete="contactSurname">
                                    @error('contactSurname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <p>{{__('countryCode')}}</p>
                                    <select id="countryCode" name="countryCode" class="browser-default custom-select">
                                        <option value="" selected>{{__('countryCode')}}</option>
                                        <optgroup label="{{__('mostPopular')}}">
                                            @foreach($country as $countries)
                                                @if($countries->countries_iso_code  == "FR" || $countries->countries_iso_code  == "US"  || $countries->countries_iso_code == "GB"  || $countries->countries_iso_code  == "TR")
                                                    <option data-countryCode="{{$countries->countries_iso_code}}"
                                                            value="+{{$countries->countries_phone_code}}">{{$countries->countries_name}}
                                                        (+{{$countries->countries_phone_code}})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="{{__('otherCountries')}}">
                                            @foreach($country as $countries)
                                                <option data-countryCode="{{$countries->countries_iso_code}}"
                                                        value="+{{$countries->countries_phone_code}}">{{$countries->countries_name}}
                                                    (+{{$countries->countries_phone_code}})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('phoneNumber')}}</p>
                                    <input id="phoneNumber" type="number" class="form-control validate"
                                           name="phoneNumber">
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('website')}}</p>
                                    <input id="website" value="http://" type="text" class="form-control validate"
                                           name="website">
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('email')}}</p>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                           autocomplete="email" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <p>{{__('password')}}</p>
                                    <input id="password" type="password"
                                           class="form-control validate @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="new-password">
                                </div>
                                <div class="input-field col s6">
                                    <p>{{__('confirmPassword')}}</p>
                                    <input id="password-confirm" type="password" class="form-control validate"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row justify-content-center" style="display: flex; justify-content: center; margin-top: 35px">
                                @error('g-recaptcha-response')
                                <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="col-md-6">
                                    <div class="g-recaptcha"
                                         data-sitekey="6LdOaeQfAAAAAEW47JrgIyKmKWA5dHd-4MIzbmrt"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit"
                                            class="btn btn-primary saveCommissionerButton">{{__('add')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'become-a-commissioner'])
