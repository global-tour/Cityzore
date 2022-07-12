@include('frontend-partials.head', ['page' => 'checkout'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$isCommissioner = auth()->check() && auth()->guard('web')->user()->commission != null;
$isAdmin = auth()->check() && !is_null(auth()->guard('web')->user()->ccEmail) && auth()->guard('web')->user()->commission == null;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>
<section onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
    <div class="form form-spac rows">
        <div class="container">
            <div class="spe-title col-md-12">
                <h2>{{__('checkout')}}</h2>
                <div class="title-line">
                    <div class="tl-1" style="background: #8e8484;"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3" style="background: #8e8484;"></div>
                </div>
            </div>
            <form id="checkoutForm" method="post"
                  action="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('credit-card-details'))}}">
                @csrf
                <div class="hidden-params-for-payment">
                    <input type="hidden" id="clientid" name="clientid" value="{{$clientId}}">
                    <input id="amount" type="hidden" name="amount" value="{{$amount}}">
                    <input type="hidden" name="oid" value="{{$oid}}">
                    <input type="hidden" name="okUrl" value="{{$okUrl}}">
                    <input type="hidden" name="failUrl" value="{{$failUrl}}">
                    <input type="hidden" name="islemtipi" value="{{$islemtipi}}">
                    <input type="hidden" name="taksit" value="{{$taksit}}">
                    <input type="hidden" name="rnd" value="{{$rnd}}">
                    <input type="hidden" name="hash" value="{{$hash}}">
                    <input type="hidden" name="storetype" value="3d_pay">
                    <input type="hidden" name="refreshtime" value="5">
                    <input type="hidden" name="lang" value="en">
                    <!-- Euro -> 978, TRY -> 949, USD -> 840, GBP -> 826 Admin kısmında seçtirildikten sonra bu kısmı parametre olarak alırız -->
                    <input type="hidden" name="currency" value="{{$currencyCode}}">
                    <input type="hidden" name="firstName" id="firstNameHidden"
                           @if(auth()->check()) value="{{auth()->user()->name}}" @else value="" @endif>
                    <input type="hidden" name="lastName" id="lastNameHidden"
                           @if(auth()->check()) value="{{auth()->user()->surname}}" @else value="" @endif>
                    <input type="hidden" name="email" id="checkoutEmailHidden"
                           @if(auth()->check()) value="{{auth()->user()->email}}" @else value="" @endif>
                    <input type="hidden" name="hotel" id="hotelHidden" value="">
                    <input type="hidden" name="phone" id="phoneHidden"
                           @if(auth()->check()) value="{{auth()->user()->countryCode}} {{auth()->user()->phoneNumber}}"
                           @else value="" @endif>
                    <input type="hidden" name="comment" id="commentHidden" value="">
                    <input type="hidden" name="countryCode" id="countryHidden" value="">
                    <input type="hidden" name="city" id="cityHidden" value="">
                    <input type="hidden" name="streetline" id="streetLineHidden" value="">
                    <input type="hidden" name="clientUniqueId" id="clientUniqueIdHidden" value="{{$clientUniqueId}}">
                    <input type="hidden" name="couponIDHidden" id="couponIDHidden">
                    <input type="hidden" id="isCommissionChanged" name="isCommissionChanged" value="0">
                    <input type="hidden" id="largestContactInfoArray" name="largestContactInfoArray"
                           value="{{json_encode($largestContactInfoArray)}}">
                    <input type="hidden" id="totalTicketCount" name="totalTicketCount" value="{{$totalTicketCount}}">
                </div>
                <div class="checkout">
                    @if(session()->has('error'))
                        @php
                            $error = true;
                        @endphp
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="alert alert-danger">
                                    <p class="text-center">
                                        {{session()->get('error')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-7">
                        <div class="col-lg-12"
                             style="margin-bottom: 20px;background-image: linear-gradient(to right, #e8e8e8, white);padding: 2%;">
                            <span>{{__('yourDetails')}}</span>
                        </div>
                        <div class="col-lg-12"
                             style="border: 1px solid #80808024;margin-bottom: 1%;background-color: #8080800d;">
                            @if($contactForAllTravelers == 0)
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span>{{__('name')}}</span>
                                        <input required type="text" class="form-control" name="firstName" id="firstName"
                                               placeholder="{{__('name')}}"
                                               @if(auth()->check()) value="{{auth()->user()->name}}"
                                               @else value='{{old("firstName")}}' @endif>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span>{{__('surname')}}</span>
                                        <input required type="text" class="form-control" name="lastName" id="lastName"
                                               placeholder="{{__('surname')}}"
                                               @if(auth()->check()) value="{{auth()->user()->surname}}"
                                               @else value='{{old("lastName")}}' @endif>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <span>{{__('email')}}</span>
                                        <input required type="email" class="form-control"
                                               @if(!empty($error)) style="background-color: #FADBD8;"
                                               @endif name="email" id="checkoutEmail" aria-describedby="emailHelp"
                                               placeholder="{{__('email')}}"
                                               @if(auth()->check()) value="{{auth()->user()->email}}"
                                               @else value='{{old("email")}}' @endif>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <span>{{__('emailConfirmation')}}</span>
                                        <input required type="email" class="form-control"
                                               @if(!empty($error)) style="background-color: #FADBD8;"
                                               @endif name="email2" id="checkoutEmail2" aria-describedby="emailHelp"
                                               placeholder="{{__('emailConfirmation')}}"
                                               @if(auth()->check()) value="{{auth()->user()->email}}"
                                               @else value='{{old("email2")}}' @endif>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span>{{__('hotel')}}({{__('optional')}})</span>
                                        <input type="text" class="form-control" name="hotel" id="hotel"
                                               placeholder="{{__('enterHotelAddress')}}" value="{{old("hotel")}}">
                                    </div>
                                </div>
                                <div class="form-group col-lg-4">
                                    <span>{{__('countryCode')}}*</span>
                                    <select required id="countryCode" name="countryCode"
                                            class="browser-default custom-select select2">
                                        <option value="" selected>{{__('countryCode')}}</option>
                                        @foreach($countries as $country)
                                            <option
                                                @if(old("countryCode") == "+".$country->countries_phone_code) selected
                                                @endif value="+{{$country->countries_phone_code}}">{{$country->countries_name}}
                                                (+{{$country->countries_phone_code}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-8">
                                    <span>{{__('phoneNumber')}}</span>
                                    <input required type="text" class="form-control" name="phone" id="phone"
                                           placeholder="{{__('phoneNumber')}}"
                                           @if(auth()->check()) value="{{auth()->user()->phoneNumber}}"
                                           @else value='{{old("phone")}}' @endif>
                                </div>
                                @if(count($largestContactInfoArray) > 0)
                                    @foreach($largestContactInfoArray as $largestContactInfo)
                                        <div class="form-group col-lg-6">
                                            <span>{{$largestContactInfo['title']}}</span>
                                            <input @if($largestContactInfo['isRequired'] == 1)required
                                                   @endif type="text" name="{{$largestContactInfo['name']}}"
                                                   data-option-id="{{$largestContactInfo['optionID']}}"
                                                   class="contactInfoField form-control"
                                                   placeholder="{{strtolower($largestContactInfo['title'])}}">
                                        </div>
                                    @endforeach
                                @endif
                            @else
                                @for($i=0;$i<$totalTicketCount;$i++)
                                    <h4 style="border-bottom: 1px solid #075175;color: #e23464">{{__('traveler')}} {{$i+1}}</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span>{{__('name')}}</span>
                                                <input required type="text" class="form-control" name="firstName{{$i}}"
                                                       id="firstName{{$i}}" placeholder="{{__('name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span>{{__('surname')}}</span>
                                                <input required type="text" class="form-control" name="lastName{{$i}}"
                                                       id="lastName{{$i}}" placeholder="{{__('surname')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span>{{__('email')}}</span>
                                                <input required type="email" class="form-control"
                                                       name="checkoutEmail{{$i}}" id="checkoutEmail{{$i}}"
                                                       aria-describedby="emailHelp" placeholder="{{__('email')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span>{{__('hotel')}}({{__('optional')}})</span>
                                                <input type="text" class="form-control" name="hotel{{$i}}"
                                                       id="hotel{{$i}}" placeholder="{{__('enterHotelAddress')}}">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <span>{{__('countryCode')}}</span>
                                            <input required type="text" class="form-control" name="countryCode{{$i}}"
                                                   id="countryCode{{$i}}" placeholder="{{__('countryCode')}}">
                                        </div>
                                        <div class="form-group col-lg-9">
                                            <span>{{__('phoneNumber')}}</span>
                                            <input required type="text" class="form-control" name="phone{{$i}}"
                                                   id="phone{{$i}}" placeholder="{{__('phoneNumber')}}">
                                        </div>
                                        @if(count($largestContactInfoArray) > 0)
                                            @foreach($largestContactInfoArray as $largestContactInfo)
                                                <div class="form-group col-lg-6">
                                                    <span>{{$largestContactInfo['title']}}</span>
                                                    <input @if($largestContactInfo['isRequired'] == 1)required
                                                           @endif type="text" name="{{$largestContactInfo['name'].$i}}"
                                                           data-option-id="{{$largestContactInfo['optionID']}}"
                                                           class="contactInfoField form-control"
                                                           placeholder="{{strtolower($largestContactInfo['title'])}}">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endfor
                            @endif
                        </div>
                        <span class="col-lg-12"
                              style="margin-bottom: 25px;background-image: linear-gradient(to right, #e8e8e8, white);padding: 2%;">{{__('comment')}}</span>
                        <div style="margin-top: 25px!important;">
                            <div class="form-group" style="">
                                <div class="col-lg-12"
                                     style="border: 1px solid #80808024;margin-bottom: 1%;background-color: #8080800d;padding-top: 1%;">
                                    <span style="margin-top: 1%;">{{__('comment')}}({{__('optional')}})</span>
                                    <textarea type="textarea" id="comment" class="materialize-textarea form-control"
                                              placeholder="{{__('commentExample')}}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 hidden-xs"
                             style="margin-bottom: 20px; margin-top: 20px;background-image: linear-gradient(to right, #e8e8e8, white);padding: 2%;">
                            <span>{{__('billingAddress')}}<span
                                    style="font-size: 13px;">({{__('optional')}})</span></span>
                        </div>
                        <div class="col-lg-12 hidden-xs"
                             style="border: 1px solid #80808024;margin-bottom: 1%;background-color: #8080800d;padding-top: 1%;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <span>{{__('country')}}</span>
                                    <input type="text" class="form-control" value="{{old('country')}}" id="country"
                                           placeholder="{{__('country')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <span>{{__('city')}}</span>
                                    <input type="text" class="form-control" value="{{old('city')}}" id="city"
                                           placeholder="{{__('city')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <span>{{__('streetLine')}}</span>
                                    <input type="text" class="form-control" value="{{old('streetLine')}}"
                                           id="streetLine" placeholder="{{__('streetLine')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="row" style="margin-bottom: 20px;">
                            <span>{{__('yourOrder')}}</span>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">{{__('product')}}</th>
                                <th scope="col">{{__('details')}}</th>
                            </tr>
                            </thead>
                            <tbody class="checkout-table">

                            @if($isCommissioner)


                                @include('frontend.checkout-for-commissioner')
                            @else
                                @foreach($cart as $i => $c)
                                    <tr>
                                        <th scope="row">
                                            <img src="{{Storage::disk('s3')->url('product-images-xs/' . $images[$i])}}"
                                                 alt="" style="border-radius: 25%; width:100px; height:100px;">
                                        </th>
                                        <th>
                                            <span
                                                style="font-size: 14px">{{App\Product::where('id', '=', $c->productID)->first()->title}}</span><br>
                                            <span style="font-size: 14px;@if($isCommissioner)display:none;@endif">
                                                <p>
                                                    <i class="{{session()->get('currencyIcon')}}"></i>{{$c->totalPrice + $c->totalCommission}}
                                                </p>
                                            </span>
                                            <span>
                                                @foreach(json_decode($c->bookingItems, true) as $bi)
                                                    <span
                                                        style="font-size: 12px">{{$bi['category']}} x {{$bi['count']}}</span>
                                                @endforeach
                                            </span><br>
                                            <span style="margin-top: 10px">
                                                <img src="{{asset('img/icon/dbl4.png')}}" alt="Paris City Tours"
                                                     style="width: 20px;"/>
                                <span style="font-size: 16px">{{$c->date}}</span>
                                <img src="{{asset('img/icon/clock.png')}}" alt="Paris City Tours"
                                     style="width: 20px;"/>
                                @foreach(json_decode($c->hour, true) as $hour)
                                                    <span style="margin-left:5px;font-weight:bold;color: #ffad0c;font-size: 16px">
                                    {{$hour['hour']}}
                                </span>
                                                @endforeach
                                            </span>
                                        </th>
                                        <?php $specials = $c->specials; ?>
                                    </tr>
                                @endforeach
                                <?php
                                $discount = 0;
                                if ($totalCommission == 0) {
                                    $discount = $totalPriceWOSO - $totalPrice;
                                }
                                ?>
                                @if($isCommissioner)
                                    <tr style="display: none">
                                        <th scope="row">{{__('total')}}</th>
                                        <td><i class="{{session()->get('currencyIcon')}}"></i> <input
                                                style="height:auto;border:none;width: 25%" readonly
                                                value="{{$totalPriceWOSO}}" id="totalPrice"></td>
                                    </tr>
                                @else
                                    <tr>
                                        <th scope="row">{{__('total')}}</th>
                                        <td><i class="{{session()->get('currencyIcon')}}"></i> <input
                                                style="height:auto;border:none;width: 25%" readonly
                                                value="{{$totalPriceWOSO}}" id="totalPrice"></td>
                                    </tr>
                                @endif

                                @if($isCommissioner)

                                    <tr>
                                        <th>{{__('commission')}}</th>
                                        <td>
                                            <div class="col-lg-4 input-group number-spinner">
                                                <input id="totalCommission" step="0.01" name="totalCommission"
                                                       value="{{$totalCommission}}" max="{{$totalCommission}}" min="0"
                                                       type="number" class="spinnerInputs form-control text-center"
                                                       style="border:none;text-align:center;width:100%;height: auto;">
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="row">{{__('totalDiscount')}}</td>
                                    <td id="totalDiscount"><i
                                            class="{{session()->get('currencyIcon')}}"></i> {{$discount}}</td>
                                <tr>
                                    <th scope="row">{{__('amountOfPayment')}}</th>
                                    @if(is_null(session()->get('totalPriceWithDiscount')))
                                        @if ($discount != 0)
                                            <td id="amountOfPayment" style="font-weight:bold;color: #1d6db2"><i
                                                    class="{{session()->get('currencyIcon')}}"></i>{{$totalPrice}}</td>
                                        @else
                                            <td id="amountOfPayment" style="font-weight:bold;color: #1d6db2"><i
                                                    class="{{session()->get('currencyIcon')}}"></i>{{$totalPriceWOSO}}
                                            </td>
                                        @endif
                                    @else
                                        <td id="amountOfPayment" style="font-weight:bold;color: #1d6db2"><i
                                                class="{{session()->get('currencyIcon')}}"></i>{{session()->get('totalPriceWithDiscount')}}
                                        </td>
                                    @endif
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="col-md-12">
                            <span style="font-size: 14px;">{{__('paymentMethod')}}:</span>
                            <img src="https://cdn.getyourguide.com/tf/assets/static/payment-methods/mastercard.svg"
                                 style="width: 30px;">
                            <img src="https://cdn.getyourguide.com/tf/assets/static/payment-methods/visa.svg"
                                 style="width: 30px;">
                            <hr>
                        </div>
                        <div class="col-md-12" @if($isCommissioner) style="display: block;" @endif>
                            <p>
                                <input name="radio" type="radio" id="creditCardRadio" value="1" checked="checked"/>
                                <label style="font-size: 1.1rem!important;"
                                       for="creditCardRadio">{{__('creditCard')}}</label>
                            </p>
                            @if($isAdmin || (auth()->guard('web')->check() && auth()->guard('web')->user()->id == 21) || (auth()->guard('web')->check() && auth()->guard('web')->user()->id == 466))
                                <p>
                                    <input data-form="not submitted" name="radio" type="radio" id="comissionRadio"
                                           value="0"/>
                                    <label style="font-size: 1.1rem!important;"
                                           for="comissionRadio">{{__('commission')}}</label>
                                </p>
                            @endif
                            @if($isAdmin)
                                <select name="platformID" id="platformID">

                                    <option value="">Choose Platform</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{$platform->id}}">{{$platform->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <hr>
                        @if (!$isCommissioner)
                            <div class="col-md-12" style="margin: 20px 0">
                                <button type="button" style="width: 100%" id="wantCoupon"
                                        class="btn">{{__('clickToUseACoupon')}}</button>
                                @if(is_null($specials) || true)
                                    <div id="couponDiv" class="input-field col-md-12"
                                         style="display: none; border: 1px #0e76a8 solid; margin-top: 0; padding-top: 20px;">
                                        <div class="col-md-8">
                                            <input id="couponInput"
                                                   style="height:30px;padding-left: 10px;border: 1px solid #aaa"
                                                   type="text" placeholder="">
                                        </div>
                                        <div class="col-md-4">
                                            <a id="couponUseIt" type="button"
                                               style="letter-spacing:2px;margin-top: 5px;"
                                               class="col-md-4">{{__('use')}}</a>
                                        </div>
                                    </div>
                                @else
                                    <div class="input-field col-md-12 text-center">
                                        <span style="font-size:14px;color: #dd2c00;">{{__('couponMessage')}}</span>
                                    </div>
                                @endif
                                <div style="display:none;margin-top: 20px" id="usedCouponsDiv" class="col-md-12">
                                    <div style="border:1px #00aced solid;border-radius:10px;background-color: #00aced"
                                         class="col-md-12">
                                        <span id="usedCoupons"
                                              style="padding-top:60px;font-size:14px;color: white"></span>
                                        <a id="deleteCoupon" style="float:right;color: black">X</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div style="text-align: left; margin-top: 20px; margin-bottom: 10px;" class="col-md-12 terms">
                            <input type="checkbox" id="termsCheck">
                            <label for="termsCheck"
                                   style="font-size:13px;color:black;">{!! __('iAcceptTermsAndConditions', ['urlLink' => url($langCodeForUrl.'/terms-and-conditions')]) !!}</label>
                        </div>
                        @if (!auth()->check())
                            <div style="text-align: left; margin-top: 5px; margin-bottom: 20px;"
                                 class="col-md-12 terms">
                                <input type="checkbox" id="registerCheck" name="registerCheck" checked="checked"
                                       value="1">
                                <label for="registerCheck"
                                       style="font-size:13px;color:black;">{{__('iAcceptRegistering')}}</label>
                            </div>
                        @endif
                        <hr>
                        <div id="placeOrderDiv">
                            <button disabled id="placeOrder" class="btn btn-danger"
                                    style="width:100%; font-weight:bold;">{{__('placeOrder')}}</button>
                        </div>
                        @if($isCommissioner)
                            <div id="shareCartDiv">
                                <button id="shareCartButton" class="btn btn-primary"
                                        style="width:100%; font-weight:bold; background-color: #1ebea5; margin-top: 20px;">{{__('shareCart')}}</button>
                                <div id="shareInputs"
                                     style="display: none; margin-top: 10px;padding: 35px; border: 1px solid; border-radius: 20px;">
                                    <input type="checkbox" id="emailCheck" value="1" checked>
                                    <label for="emailCheck"
                                           style="font-size:13px;color:black;">{{__('viaEmail')}}</label>
                                    <input type="checkbox" id="whatsappCheck" value="1" checked>
                                    <label for="whatsappCheck"
                                           style="font-size:13px;color:black;">{{__('viaWhatsApp')}}</label>
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="shareButton">{{__('share')}}</button>
                                    </div>
                                    <div style="display: none;" class="text-center" id="whatsappLink">
                                        <div style="margin-top: 20px;" id="whatsappAlert" class="alert alert-danger"
                                             role="alert">
                                            {{__('shareCartDesc')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'checkout'])

