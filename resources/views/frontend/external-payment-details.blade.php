@include('frontend-partials.head', ['page' => 'booking-successful'])
@include('frontend-partials.header')


<section>
    <div class="container">
        <div class="spe-title col-md-12" style="padding-top: 80px;">
            <h2>{!! __('paymentDetails') !!}</h2>
            <div class="title-line">
                <div class="tl-1"></div>
                <div class="tl-2"></div>
                <div class="tl-3"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="well well-lg">
                    <p>{{__('email')}}: {{$payment->email}}</p>
                    <p>
                        {{__('price')}}:
                        @switch($currency)
                            @case('978')
                            <i class="icon-cz-eur"></i>
                            @break
                            @case('949')
                            <i class="icon-cz-try"></i>
                            @break
                            @case('840')
                            <i class="icon-cz-usd"></i>
                            @break
                            @case('826')
                            <i class="icon-cz-gbp"></i>
                            @break
                        @endswitch
                        {{$payment->price}}
                    </p>
                    <p>{{__('message')}}: {{$payment->message}}</p>
                </div>
                @if ($payment->is_paid == 0)
                <hr>
                <div class="container py-5" style="margin-top: 50px; margin-bottom: 50px;">
                    <div class="row">
                        <div class="col-lg-12 mx-auto">
                            <div class="bg-white rounded-lg shadow-sm p-5">
                                <div id="nav-tab-card" class="">
                                    <form method="POST" action="https://sanalpos2.ziraatbank.com.tr/servlet/est3Dgate">
                                        @csrf
                                        <div class="hidden-params-for-payment">
                                            <input type="hidden" name="clientid" value="{{$clientId}}">
                                            <input type="hidden" name="amount" value="{{$amount}}">
                                            <input type="hidden" name="oid" value="{{$oid}}">
                                            <input type="hidden" name="okUrl" value="{{$okUrl}}">
                                            <input type="hidden" name="failUrl" value="{{$failUrl}}">
                                            <input type="hidden" name="islemtipi" value="{{$islemtipi}}">
                                            <input type="hidden" name="taksit" value="{{$taksit}}">
                                            <input type="hidden" name="rnd" value="{{$rnd}}">
                                            <input type="hidden" name="hash" value="{{$hash}}">
                                            <input type="hidden" name="storetype" value="3d_pay" >
                                            <input type="hidden" name="refreshtime" value="5" >
                                            <input type="hidden" name="lang" value="en">
                                            <!-- Euro -> 978, TRY -> 949, USD -> 840, GBP -> 826 Admin kısmında seçtirildikten sonra bu kısmı parametre olarak alırız -->
                                            <input type="hidden" name="currency" value="{{$currency}}">
                                            <input type="hidden" name="paymentid" value="{{$payment->id}}">
                                            <input type="hidden" name="createdBy" value="{{$payment->createdBy}}">
                                            <input type="hidden" name="email" value="{{$payment->email}}">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group col-md-12">
                                                <label for="username"><span>{{__('fullName')}}</span></label>
                                                <input type="text" name="username" placeholder="{{__('fullNamePlaceHolder')}}" required class="form-control">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label><span>{{__('cardType')}}</span></label>
                                                <select name="cardType" class="browser-default form-control">
                                                    <option value="1">VISA</option>
                                                    <option value="2">Master Card</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="pan"><span>{{__('cardNumber')}}</span></label>
                                                <input type="text" name="pan" placeholder="{{__('cardNumberPlaceHolder')}}" maxlength="20" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group col-sm-4">
                                                <label><span>{{__('expirationMonth')}}</span></label>
                                                <input type="text" placeholder="MM" name="Ecom_Payment_Card_ExpDate_Month" class="form-control" required maxlength="2">
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <label><span>{{__('expirationYear')}}</span></label>
                                                <input type="text" placeholder="YY" name="Ecom_Payment_Card_ExpDate_Year" class="form-control" required maxlength="2">
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <label><span>CVV</span></label>
                                                <input name="cv2" maxlength="4" type="text" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary col-md-offset-4 col-md-4">{{__('confirm')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <p>{{__('paymentLinkUsed')}}</p>
                @endif
            </div>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'booking-successful'])

