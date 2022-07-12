@include('frontend-partials.head', ['page' => 'edit-profile'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

    <section>
        <div class="db">
        @include('layouts.profile-sidebar.sidebar-left')
            <div class="col-lg-9">
                <div class="db-2-com db-2-main" style="background-color: white;">
                    <h4>{{__('editMyProfile')}}</h4>
                    <div class="db-2-main-com db2-form-pay db2-form-com">
                        <form action="{{url($langCodeForUrl.'/profile/'.$user->id.'/update')}}" class="col s12" style="background-color: white;" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('POST')
                            @if (!is_null($user->commission))
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" class="validate" id="companyName" name="companyName" value="{{$user->companyName}}">
                                    <label for="companyName">{{__('companyName')}}</label>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" class="validate" id="name" name="name" value="{{$user->name}}">
                                    <label for="name">{{__('name')}}</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="validate" id="surname" name="surname" value="{{$user->surname}}">
                                    <label for="surname">{{__('surname')}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input type="password" id="password" name="password" class="validate">
                                    <label for="password">{{__('password')}}</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="validate">
                                    <label for="password_confirmation">{{__('confirmPassword')}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input type="email" class="validate" id="email" name="email" value="{{$user->email}}">
                                    <label for="email">{{__('email')}}</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input type="text" class="validate" id="address" name="address" value="{{$user->address}}">
                                    <label for="address">{{__('address')}}</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input type="text" class="validate" name="countryCode" id="countryCode" value="{{$user->countryCode}}">
                                    <label for="countryCode">{{__('countryCode')}}</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input type="number" class="validate" id="phoneNumber" name="phoneNumber" value="{{$user->phoneNumber}}">
                                    <label for="phoneNumber">{{__('phoneNumber')}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <input type="submit" class="waves-effect waves-light btn-large" value="Update" style="margin-top: 1%;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'edit-profile'])
