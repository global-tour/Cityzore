@include('frontend-partials.head', ['page' => 'credit-card-details'])
@include('frontend-partials.header')

<section>
    <div class="spe-title col-md-12">
        <h2>{{__('creditCardDetails')}}</h2>
        <div class="title-line">
            <div class="tl-1"></div>
            <div class="tl-2"></div>
            <div class="tl-3"></div>
        </div>
    </div>
    <div class="container py-5" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="row">

            <div class="col-lg-8 mx-auto">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <div id="nav-tab-card" class="">
                        @if(config('app.env') === 'local')
                            <form action="{{ url('/booking-successful') }}" method="post">
                                @csrf
                                <input type="hidden" name="clientUniqueId" id="clientUniqueIdHidden"
                                       value="{{$clientUniqueId}}">
                                <input type="hidden" name="firstName" id="firstNameHidden" value="{{$firstName}}">
                                <input type="hidden" name="lastName" id="lastNameHidden" value="{{$lastName}}">
                                <input type="hidden" name="oid" value="{{$oid}}">
                                <input type="hidden" name="email" id="checkoutEmailHidden" value="{{$email}}">
                                <input type="hidden" name="phone" id="phoneHidden" value="{{$phone}}">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        @else
                            <form method="POST" action="https://sanalpos2.ziraatbank.com.tr/fim/est3Dgate">
                                <div class="hidden-params-for-payment">
                                    <div class="hidden-params-for-payment">
                                        <input type="hidden" name="clientid" value="{{$clientid}}">
                                        <input type="hidden" name="amount" value="{{$amount}}">
                                        <input type="hidden" name="oid" value="{{$oid}}">
                                        <input type="hidden" name="okUrl" value="{{$okUrl}}">
                                        <input type="hidden" name="failUrl" value="{{$failUrl}}">
                                        <input type="hidden" name="islemtipi" value="{{$islemtipi}}">
                                        <input type="hidden" name="taksit" value="{{$taksit}}">
                                        <input type="hidden" name="rnd" value="{{$rnd}}">
                                        <input type="hidden" name="hash" value="{{$hash}}">
                                        <input type="hidden" name="storetype" value="{{$storetype}}">
                                        <input type="hidden" name="refreshtime" value="{{$refreshtime}}">
                                        <input type="hidden" name="lang" value="en">
                                        <!-- Euro -> 978, TRY -> 949, USD -> 840, GBP -> 826 Admin kısmında seçtirildikten sonra bu kısmı parametre olarak alırız -->
                                        <input type="hidden" name="currency" value="{{$currencyCode}}">
                                        <input type="hidden" name="firstName" id="firstNameHidden"
                                               value="{{$firstName}}">
                                        <input type="hidden" name="lastName" id="lastNameHidden" value="{{$lastName}}">
                                        <input type="hidden" name="email" id="checkoutEmailHidden" value="{{$email}}">
                                        <input type="hidden" name="hotel" id="hotelHidden" value="{{$hotel}}">
                                        <input type="hidden" name="phone" id="phoneHidden" value="{{$phone}}">
                                        <input type="hidden" name="comment" id="commentHidden" value="{{$comment}}">
                                        <input type="hidden" name="countryCode" id="countryHidden" value="{{$country}}">
                                        <input type="hidden" name="city" id="cityHidden" value="{{$city}}">
                                        <input type="hidden" name="streetline" id="streetLineHidden"
                                               value="{{$streetline}}">
                                        <input type="hidden" name="clientUniqueId" id="clientUniqueIdHidden"
                                               value="{{$clientUniqueId}}">
                                        <input type="hidden" name="couponIDHidden" id="couponIDHidden"
                                               value="{{$couponID}}">
                                        <input type="hidden" name="largestContactInfoArray" id="largestContactInfoArray"
                                               value="{{$largestContactInfoArray}}">
                                        <input type="hidden" id="translationArray" value="{{$translationArray}}">
                                        <input type="hidden" name="deviceType" id="deviceType">
                                        <input type="hidden" name="amount" value="{{ old('amount') ?? $amount}}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-12">
                                        <label for="username"><span>{{__('fullName')}}</span></label>
                                        <input type="text" name="username" placeholder="{{__('fullNamePlaceHolder')}}"
                                               required class="form-control">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="pan"><span>{{__('cardNumber')}}</span></label>
                                        <input type="number" name="pan" placeholder="{{__('cardNumberPlaceHolder')}}"
                                               maxlength="20" min="1" class="form-control"
                                               onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57"
                                               required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group col-sm-4">
                                        <label><span>{{__('expirationMonth')}}</span></label>
                                        <input type="number" placeholder="MM" name="Ecom_Payment_Card_ExpDate_Month"
                                               class="form-control" min="0" required maxlength="2"
                                               onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label><span>{{__('expirationYear')}}</span></label>
                                        <input type="number" placeholder="YY" name="Ecom_Payment_Card_ExpDate_Year"
                                               class="form-control" min="0" required maxlength="2"
                                               onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label><span>CVV</span></label>
                                        <input name="cv2" maxlength="4" type="number" min="1" required
                                               class="form-control"
                                               onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <button type="submit"
                                                class="btn btn-primary col-md-offset-4 col-md-4">{{__('confirm')}}</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
            <div class="col-lg-4 mx-auto" style="position: inherit;">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">{{__('product')}}</th>
                        <th scope="col">{{__('details')}}</th>
                    </tr>
                    </thead>
                    <tbody class="checkout-table">
                    @foreach($cart as $i => $c)
                        <tr>
                            <th scope="row">
                                <img src="{{Storage::disk('s3')->url('product-images-xs/' . $images[$i])}}" alt=""
                                     style="border-radius: 25%; width:100px; height:100px;">
                            </th>
                            <th>
                                <span
                                    style="font-size: 14px">{{App\Product::where('id', '=', $c->productID)->first()->title}}</span>
                                <span style="font-size: 14px">
                                    @if(is_null(session()->get('totalPriceWithDiscount')))
                                        @if(auth()->guard('web')->check() && auth()->user()->isActive == 1 && !is_null(auth()->user()->commission) && !is_null($c->tempCommission))
                                            <p><i class="{{session()->get('currencyIcon')}}"></i>{{$c->tempTotalPrice}}</p>
                                        @else
                                            <p><i class="{{session()->get('currencyIcon')}}"></i>{{$c->totalPrice}}</p>
                                        @endif
                                    @else


                                        @php
                                            if(auth()->guard('web')->check() && auth()->user()->isActive == 1 && !is_null(auth()->user()->commission) && !is_null($c->tempCommission)){
                                              $pr = $c->tempTotalPrice;
                                            }else{
                                             $pr = $c->totalPrice;
                                            }

                                               foreach (json_decode(session()->get('totalPriceWithDiscount'), true) as $element){

                                                 if($element["cartID"] == $c->id){
                                                  $pr = $element["newPrice"];
                                                  break;
                                                 }
                                               }
                                        @endphp


                                        <p><i class="{{session()->get('currencyIcon')}}"></i>{{$pr}}</p>
                                    @endif
                                </span>
                                <span>
                                    @foreach(json_decode($c->bookingItems, true) as $bi)
                                        <span style="font-size: 12px">{{$bi['category']}} x {{$bi['count']}}</span>
                                    @endforeach
                                </span>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>

                    <tr>
                        <th scope="row">{{__('totalPrice')}}</th>
                        @if(is_null(session()->get('totalPriceWithDiscount')))
                            <th><i class="{{session()->get('currencyIcon')}}"></i>{{$totalPrice}}</th>
                        @else
                            @php
                                $totalPrice = 0;
                                    foreach (json_decode(session()->get('totalPriceWithDiscount'), true) as $element){
                                       $totalPrice += (float)$element["newPrice"];
                                    }





                            @endphp
                            <th><i class="{{session()->get('currencyIcon')}}"></i>{{$totalPrice}}</th>
                        @endif
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'credit-card-details'])
