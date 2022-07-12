@include('frontend-partials.head', ['page' => 'cc-details-for-shared-cart'])
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
                        <form method="POST" action="https://sanalpos2.ziraatbank.com.tr/servlet/est3Dgate">
                            @csrf
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
                                <input type="hidden" name="storetype" value="{{$storetype}}" >
                                <input type="hidden" name="refreshtime" value="{{$refreshtime}}" >
                                <input type="hidden" name="lang" value="en">
                                <!-- Euro -> 978, TRY -> 949, USD -> 840, GBP -> 826 Admin kısmında seçtirildikten sonra bu kısmı parametre olarak alırız -->
                                <input type="hidden" name="currency" value="{{$currencyCode}}">
                                <input type="hidden" name="firstName" id="firstNameHidden" value="{{$firstName}}">
                                <input type="hidden" name="lastName" id="lastNameHidden" value="{{$lastName}}">
                                <input type="hidden" name="email" id="checkoutEmailHidden" value="{{$email}}">
                                <input type="hidden" name="phone" id="phoneHidden" value="{{$phone}}">
                                <input type="hidden" name="cartIds" id="cartIdsHidden" value="{{$cartIds}}">
                                <input type="hidden" name="userID" id="userIDHidden" value="{{$userID}}">
                                <input type="hidden" name="currencyID" id="currencyIDHidden" value="{{$currencyID}}">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="username"><span>{{__('fullName')}}</span></label>
                                    <input type="text" name="username" placeholder="{{__('fullNamePlaceHolder')}}" required class="form-control">
                                </div>
                                <div class="form-group col-md-12">
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
            <div class="col-lg-4 mx-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">{{__('product')}}</th>
                        <th scope="col">{{__('details')}}</th>
                    </tr>
                    </thead>
                    <tbody class="checkout-table">
                    @foreach($carts as $i => $c)
                        <tr>
                            <th scope="row">
                                <img src="{{Storage::disk('s3')->url('product-images-xs/' . $images[$i])}}" alt="" style="border-radius: 25%; width:100px; height:100px;">
                            </th>
                            <th>
                                <span style="font-size: 14px">{{App\Product::where('id', '=', $c->productID)->first()->title}}</span>
                                <span style="font-size: 14px">
                                    <p><i class="{{$currencySymbol}}"></i>
                                        @if (!is_null($c->tempTotalPrice))
                                        {{$c->tempTotalPrice}}
                                        @else
                                        {{$c->totalPrice}}
                                        @endif
                                    </p>
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
                        <th>
                            <i class="{{$currencySymbol}}"></i>{{$amount}}
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'cc-details-for-shared-cart'])
