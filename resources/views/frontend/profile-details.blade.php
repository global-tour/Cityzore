@include('frontend-partials.head', ['page' => 'profile-details'])
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
                    <h4>{{__('myProfile')}}</h4>
                    <div class="db-2-main-com db-2-main-com-table">
                        <table class="responsive-table">
                            <tbody>
                            <tr>
                                <td>{{__('name')}}</td>
                                <td>:</td>
                                <td>{{auth()->user()->name}}</td>
                            </tr>
                            <tr>
                                <td>{{__('surname')}}</td>
                                <td>:</td>
                                <td>{{auth()->user()->surname}}</td>
                            </tr>
                            <tr>
                                <td>{{__('email')}}</td>
                                <td>:</td>
                                <td>{{auth()->user()->email}}</td>
                            </tr>
                            <tr>
                                <td>{{__('phone')}}</td>
                                <td>:</td>
                                <td>{{auth()->user()->countryCode}}{{auth()->user()->phoneNumber}}</td>
                            </tr>
                            <tr>
                                <td>{{__('address')}}</td>
                                <td>:</td>
                                <td>{{auth()->user()->address}}</td>
                            </tr>
                            <tr>
                                <td>{{__('status')}}</td>
                                <td>:</td>
                                @if(auth()->user()->isActive == 1)
                                <td><span class="db-done">{{__('active')}}</span></td>
                                @else
                                <td><span class="db-not-done">{{__('notActive')}}</span></td>
                                @endif
                            </tr>
                            @if(!is_null(auth()->user()->affiliate_unique))
                             <tr>
                                <td>{{__('token')}}</td>
                                <td>:</td>
                                <td>{{"https://www.cityzore.com/"}}<mark>?affiliate={{auth()->user()->affiliate_unique}}</mark></td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="db-mak-pay-bot">
                            <a href="{{url($langCodeForUrl.'/profile/'.auth()->user()->id.'/edit')}}" class="waves-effect waves-light btn-large">{{__('editMyProfile')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'profile-details'])
