@include('frontend-partials.head', ['page' => 'credit-card-details'])
@include('frontend-partials.header')
<link rel="stylesheet" href="{{ asset('css/payment-method.css') }}">
<section>
    <div class="spe-title col-md-12">
        <h2>Payment Method</h2>
        <div class="title-line">
            <div class="tl-1"></div>
            <div class="tl-2"></div>
            <div class="tl-3"></div>
        </div>
    </div>
    <div class="container py-5" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="row">

            <div class="col-lg-8 mx-auto" style="position: relative">

                <div class="payment-methods">

                    <div class="payment-method">
                        <div class="payment-method-header">
                            <input type="radio" name="paymentMethod" id="googlepay" value="googlePay">
                            <img src="https://assets.pclncdn.com/web/cart-checkout/59110d36f7/public/images/gPay.png"
                                 height="41" alt="">
                        </div>
                    </div>

                    <div class="payment-method selected-type">
                        <div class="payment-method-header">
                            <input type="radio" name="paymentMethod" id="credit-debit" value="creditDebit" checked>
                            <div class="title">
                                <h3>Credit Card or Debit</h3>
                                <p>Visa, Mastercard, American Express, Discover</p>
                            </div>
                        </div>
                        <div class="payment-detail active">
                            <form action="" id="payment-form">
                                <div class="col-md-12">
                                    <div class="form-group col-md-12">
                                        <label for="username" class="col-md-12">{{__('fullName')}}
                                            <input type="text" name="username"
                                                   placeholder="{{__('fullNamePlaceHolder')}}"
                                                   required class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="pan" class="col-md-12">{{__('cardNumber')}}
                                            <input type="text" name="pan"
                                                   placeholder="{{__('cardNumberPlaceHolder')}}"
                                                   class="form-control"
                                                   data-inputmask="'mask': '9999 9999 9999 9999'"
                                                   required>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group col-sm-4">
                                        <label class="col-md-12"
                                               for="Ecom_Payment_Card_ExpDate_Year"> Expiration
                                            <input type="text" placeholder="MM/YY" name="Ecom_Payment_Card_ExpDate_Year"
                                                   class="form-control" required
                                                   data-inputmask="'alias': 'datetime', 'inputFormat' :'mm/yy'">
                                        </label>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label class="col-md-12" for="cv2">CVV
                                            <input name="cv2" maxlength="4" placeholder="CVV" type="text" min="1"
                                                   required
                                                   class="form-control"
                                                   data-inputmask="'mask': '999'">
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="payment-method">
                        <div class="payment-method-header">
                            <input type="radio" name="paymentMethod" id="paypal" value="paypal">
                            <img src="https://assets.pclncdn.com/web/cart-checkout/59110d36f7/public/images/paypal.svg"
                                 height="24" alt="">
                        </div>
                    </div>

                </div>

                <div class="button-area">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, asperiores atque beatae
                        consequuntur deleniti ea error illum libero magni, minima molestiae nam quos recusandae
                        reiciendis similique sint tempora tenetur voluptatum.
                    </p>
                    <div class="button-type">
                        <button type="button" onclick="$('#payment-form').submit()" class="confirm-booking">Complete
                            Booking
                        </button>
                    </div>
                </div>


                <div class="bg-white rounded-lg shadow-sm p-5">
                    <div>


                        {{--                        <div id="accordion">--}}
                        {{--                            <div class="card">--}}
                        {{--                                <div class="card-header" id="headingOne">--}}
                        {{--                                    <h5 class="mb-0">--}}
                        {{--                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">--}}
                        {{--                                            Credit Card--}}
                        {{--                                        </button>--}}
                        {{--                                    </h5>--}}
                        {{--                                </div>--}}

                        {{--                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">--}}
                        {{--                                    <div class="card-body">--}}
                        {{--                                        <div id="nav-tab-card" class="">--}}
                        {{--                                        <form method="POST" action="https://sanalpos2.ziraatbank.com.tr/servlet/est3Dgate">--}}
                        {{--                                            @csrf--}}
                        {{--                                            <div class="hidden-params-for-payment">--}}
                        {{--                                                <input type="hidden" name="clientid" value="{{$clientid}}">--}}
                        {{--                                                <input type="hidden" name="amount" value="{{$amount}}">--}}
                        {{--                                                <input type="hidden" name="oid" value="{{$oid}}">--}}
                        {{--                                                <input type="hidden" name="okUrl" value="{{$okUrl}}">--}}
                        {{--                                                <input type="hidden" name="failUrl" value="{{$failUrl}}">--}}
                        {{--                                                <input type="hidden" name="islemtipi" value="{{$islemtipi}}">--}}
                        {{--                                                <input type="hidden" name="taksit" value="{{$taksit}}">--}}
                        {{--                                                <input type="hidden" name="rnd" value="{{$rnd}}">--}}
                        {{--                                                <input type="hidden" name="hash" value="{{$hash}}">--}}
                        {{--                                                <input type="hidden" name="storetype" value="{{$storetype}}" >--}}
                        {{--                                                <input type="hidden" name="refreshtime" value="{{$refreshtime}}" >--}}
                        {{--                                                <input type="hidden" name="lang" value="en">--}}
                        {{--                                                <!-- Euro -> 978, TRY -> 949, USD -> 840, GBP -> 826 Admin kısmında seçtirildikten sonra bu kısmı parametre olarak alırız -->--}}
                        {{--                                                <input type="hidden" name="currency" value="{{$currencyCode}}">--}}
                        {{--                                                <input type="hidden" name="firstName" id="firstNameHidden" value="{{$firstName}}">--}}
                        {{--                                                <input type="hidden" name="lastName" id="lastNameHidden" value="{{$lastName}}">--}}
                        {{--                                                <input type="hidden" name="email" id="checkoutEmailHidden" value="{{$email}}">--}}
                        {{--                                                <input type="hidden" name="hotel" id="hotelHidden" value="{{$hotel}}">--}}
                        {{--                                                <input type="hidden" name="phone" id="phoneHidden" value="{{$phone}}">--}}
                        {{--                                                <input type="hidden" name="comment" id="commentHidden" value="{{$comment}}">--}}
                        {{--                                                <input type="hidden" name="countryCode" id="countryHidden" value="{{$country}}">--}}
                        {{--                                                <input type="hidden" name="city" id="cityHidden" value="{{$city}}">--}}
                        {{--                                                <input type="hidden" name="streetline" id="streetLineHidden" value="{{$streetline}}">--}}
                        {{--                                                <input type="hidden" name="clientUniqueId" id="clientUniqueIdHidden" value="{{$clientUniqueId}}">--}}
                        {{--                                                <input type="hidden" name="couponIDHidden" id="couponIDHidden" value="{{$couponID}}">--}}
                        {{--                                                <input type="hidden" name="largestContactInfoArray" id="largestContactInfoArray" value="{{$largestContactInfoArray}}">--}}
                        {{--                                                <input type="hidden" id="translationArray" value="{{$translationArray}}">--}}
                        {{--                                                <input type="hidden" name="deviceType" id="deviceType">--}}
                        {{--                                            </div>--}}
                        {{--                                                                    <div class="col-md-12">--}}
                        {{--                                                                        <div class="form-group col-md-12">--}}
                        {{--                                                                            <label for="username"><span>{{__('fullName')}}</span></label>--}}
                        {{--                                                                            <input type="text" name="username" placeholder="{{__('fullNamePlaceHolder')}}" required class="form-control">--}}
                        {{--                                                                        </div>--}}
                        {{--                                                                        <div class="form-group col-md-12">--}}
                        {{--                                                                            <label for="pan"><span>{{__('cardNumber')}}</span></label>--}}
                        {{--                                                                            <input type="number" name="pan" placeholder="{{__('cardNumberPlaceHolder')}}" maxlength="20" min="1" class="form-control" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" required>--}}
                        {{--                                                                        </div>--}}
                        {{--                                                                    </div>--}}
                        {{--                                                                    <div class="col-sm-12">--}}
                        {{--                                                                        <div class="form-group col-sm-4">--}}
                        {{--                                                                            <label><span>{{__('expirationMonth')}}</span></label>--}}
                        {{--                                                                            <input type="number" placeholder="MM" name="Ecom_Payment_Card_ExpDate_Month" class="form-control" min="0" required maxlength="2" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">--}}
                        {{--                                                                        </div>--}}
                        {{--                                                                        <div class="form-group col-sm-4">--}}
                        {{--                                                                            <label><span>{{__('expirationYear')}}</span></label>--}}
                        {{--                                                                            <input type="number" placeholder="YY" name="Ecom_Payment_Card_ExpDate_Year" class="form-control" min="0" required maxlength="2" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">--}}
                        {{--                                                                        </div>--}}
                        {{--                                                                        <div class="form-group col-sm-4">--}}
                        {{--                                                                            <label><span>CVV</span></label>--}}
                        {{--                                                                            <input name="cv2" maxlength="4" type="number" min="1" required class="form-control" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">--}}
                        {{--                                                                        </div>--}}
                        {{--                                                                    </div>--}}
                        {{--                                            <div class="col-md-12">--}}
                        {{--                                                <div class="col-md-12">--}}
                        {{--                                                    <button type="submit" class="btn btn-primary col-md-offset-4 col-md-4">{{__('confirm')}}</button>--}}
                        {{--                                                </div>--}}
                        {{--                                            </div>--}}
                        {{--                                        </form>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="card">--}}
                        {{--                                <div class="card-header" id="headingTwo">--}}
                        {{--                                    <h5 class="mb-0">--}}
                        {{--                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">--}}
                        {{--                                            Google Pay--}}
                        {{--                                        </button>--}}
                        {{--                                    </h5>--}}
                        {{--                                </div>--}}
                        {{--                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">--}}
                        {{--                                    <div class="card-body">--}}
                        {{--                                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="card">--}}
                        {{--                                <div class="card-header" id="headingThree">--}}
                        {{--                                    <h5 class="mb-0">--}}
                        {{--                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">--}}
                        {{--                                            Authorize.net--}}
                        {{--                                        </button>--}}
                        {{--                                    </h5>--}}
                        {{--                                </div>--}}
                        {{--                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">--}}
                        {{--                                    <div class="card-body">--}}
                        {{--                                        <form action="/charge" method="post">--}}
                        {{--                                            {{ csrf_field() }}--}}
                        {{--                                            <p><input type="text" name="amount" placeholder="Enter Amount" /></p>--}}
                        {{--                                            <p><input type="text" name="cc_number" placeholder="Card Number" /></p>--}}
                        {{--                                            <p><input type="text" name="expiry_month" placeholder="Month" /></p>--}}
                        {{--                                            <p><input type="text" name="expiry_year" placeholder="Year" /></p>--}}
                        {{--                                            <p><input type="text" name="cvv" placeholder="CVV" /></p>--}}
                        {{--                                            <input type="submit" name="submit" value="Submit" />--}}
                        {{--                                        </form>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                    </div>
                </div>


            </div>
        </div>
    </div>

</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'credit-card-details'])
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
<script src="{{ asset('js/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.payment-method-header').on('click', function () {
            const paymentDetail = $('.payment-detail');

            if ($(this).has(paymentDetail)) {

                paymentDetail.removeClass('active');

                if (!$(this).parent().find('.payment-detail').hasClass('active')) {
                    $(this).parent().find('.payment-detail').addClass('active')
                }
            }

            $('.payment-method').removeClass('selected-type')

            $(this).parent().addClass('selected-type')

            $('input[type="radio"]').attr('checked', false)

            $(this).find($('input[type="radio"]')).attr('checked', 'checked');

            switch ($('input[type=radio]:checked').val()) {
                case 'googlePay':
                    onGooglePayLoaded('test')
                    break;
                case 'paypal':
                    $('.button-area .button-type').html(`<button type="button" onclick="$('#payment-form').submit()" class="confirm-booking">Complete
                            Booking with PayPal
                        </button>`)
                    break
                default:
                    $('.button-area .button-type').html(`<button type="button" onclick="$('#payment-form').submit()" class="confirm-booking">Complete
                            Booking
                        </button>`);
                    break;
            }
        });

        $('#payment-form').validate({
            onfocusout: function (element) {
                $(element).valid()
            }
        })

        $(":input").inputmask();
    });
</script>

<script src="{{ asset('js/googlePayJS.js') }}"></script>

<script
    async
    src="https://pay.google.com/gp/p/js/pay.js">
</script>
