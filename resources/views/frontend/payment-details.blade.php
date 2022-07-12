@include('frontend-partials.head', ['page' => 'payment-details'])
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
                <h4>{!! __('paymentDetails') !!}</h4>
                <div class="db-2-main-com db2-form-pay db2-form-com">
                    <form action="{{url($langCodeForUrl.'/payment-details/'.auth()->user()->id.'/update')}}" class="col s12" style="background-color: white;" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" class="validate" id="bankName" name="bankName" @if(!is_null($payment)) value="{{$payment->bankName}}" @endif>
                                <label for="bankName">{{__('bankName')}}</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" class="validate" id="bankBranch" name="bankBranch" @if(!is_null($payment)) value="{{$payment->bankBranch}}" @endif>
                                <label for="bankBranch">{{__('bankBranch')}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <input type="text" class="validate" id="city" name="city" @if(!is_null($payment)) value="{{$payment->city}}" @endif>
                                <label for="city">{{__('city')}}</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" class="validate" id="district" name="district" @if(!is_null($payment)) value="{{$payment->district}}" @endif>
                                <label for="district">{{__('district')}}</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" class="validate" id="postalCode" name="postalCode" @if(!is_null($payment)) value="{{$payment->postalCode}}" @endif>
                                <label for="postalCode">{{__('postalCode')}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" class="validate" id="swift" name="swift" @if(!is_null($payment)) value="{{$payment->swift}}" @endif>
                                <label for="swift">{{__('swift')}}</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" class="validate" id="iban" name="iban" @if(!is_null($payment)) value="{{$payment->iban}}" @endif>
                                <label for="iban">{{__('iban')}}</label>
                            </div>
                        </div>
                        <div class="row text-center">
                            <input type="submit" class="waves-effect waves-light btn-large" value="Update" style="margin-top: 1%;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'payment-details'])
