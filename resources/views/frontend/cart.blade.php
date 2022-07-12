@include('frontend-partials.head', ['page' => 'cart'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$productModel = new \App\Product();
$optionModel = new \App\Option();
$productTranslationModel = new \App\ProductTranslation();
$optionTranslationModel = new \App\OptionTranslation();
$languageModel = new \App\Language();
$currencyIcon = session()->get('currencyIcon');
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$language = $languageModel->where('code', $langCode)->first();

?>
<section>
    <div class="container">
        <div class="spe-title col-md-12" style="padding-top: 80px;">
            <h2>{!! __('shoppingCart') !!}</h2>
            <div class="title-line">
                <div class="tl-1"></div>
                <div class="tl-2"></div>
                <div class="tl-3"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row" style="margin-bottom: 50px;">
            <div class="col-md-8 col-xs-12">
                <div class="row hidden-xs hidden-sm" style="border-bottom: 1px solid darkgrey;padding-bottom: 1%">
                    <div class="col-lg-2">
                        <span>{{__('item')}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{__('name')}}</span>
                    </div>
                    <div class="col-lg-3">
                        <span>{{__('total')}}</span>
                    </div>
                    <div class="col-lg-1">
                        <span>{{__('actions')}}</span>
                    </div>
                </div>
                <?php
                $isCommissioner = auth()->check() && auth()->guard('web')->user()->isActive == 1;
                ?>
                @if($cart == '[]')
                    <tr>
                        <td>
                            <strong>{{__('cartIsEmpty')}}</strong>
                        </td>
                    </tr>
                @endif
                @foreach($cart as $c)
                    <div data-cart-id = "{{$c->id}}" class="row" style="padding-top: 1%;border-bottom: 1px solid darkgrey;margin-top: 10px;">
                        <div class="col-lg-2">
                            <div class="thumb_cart">
                                <?php
                                    $productID = $optionModel->where('id', $c->optionID)->first()->products()->first()->id;
                                    $product = $productModel->findOrFail($productID);
                                    $image = $product->productGalleries()->first();
                                ?>
                                @if($image)
                                    <img src="{{Storage::disk('s3')->url('product-images-xs/' . $image->src)}}" alt="Image">
                                @else
                                    <img src="{{asset('/img/gallery/s2.jpeg')}}" alt="Image">
                                @endif
                            </div>
                        </div>

                        <?php
                        $productTranslation = $productTranslationModel->where('productID', $c->productID)->where('languageID', $language->id)
                            ->where(function($query) {
                                $query->where('title', '!=', null)
                                    ->where('shortDesc', '!=', null)
                                    ->where('fullDesc', '!=', null)
                                    ->where('highlights', '!=', null)
                                    ->where('included', '!=', null)
                                    ->where('notIncluded', '!=', null)
                                    ->where('knowBeforeYouGo', '!=', null)
                                    ->where('category', '!=', null)
                                    ->where('cancelPolicy', '!=', null);
                            })->first();
                        ?>
                        <div class="col-lg-5">
                            <span class="item_cart">
                                <a href="@if($productTranslation){{url($langCodeForUrl.'/'.$productTranslation->url)}}@else{{url($langCodeForUrl.'/'.$productModel->findOrFail($c->productID)->url)}}@endif">
                                    <b style="color: #2d5d73;font-size: 16px;">
                                        @if ($productTranslation)
                                        {{$productTranslation->title}}
                                        @else
                                        {{$productModel->findOrFail($c->productID)->title}}
                                        @endif
                                    </b>
                                </a>
                                <?php
                                $optionTranslation = $optionTranslationModel->where('optionID', $c->optionID)->where('languageID', $language->id)
                                    ->where(function($query) {
                                        $query->where('title', '!=', null)
                                            ->where('description', '!=', null);
                                    })->first();
                                ?>
                                <br>
                                <strong>
                                    @if ($optionTranslation)
                                    {{$optionTranslation->title}}
                                    @else
                                    {{$optionModel->where('id', $c->optionID)->first()->title}}
                                    @endif
                                </strong>
                                <br>
                                <br>
                                <img src="{{asset('img/icon/dbl4.png')}}"  alt="Paris City Tours" style="width: 20px;vertical-align: text-top;"/>
                                <span style="font-size: 17px">{{$c->date}}</span>
                                <img src="{{asset('img/icon/clock.png')}}"  alt="Paris City Tours" style="width: 20px;vertical-align: bottom;"/>
                                @foreach(json_decode($c->hour, true) as $hour)
                                <span style="margin-left:5px;font-weight:bold;color: #ffad0c">
                                    {{$hour['hour']}}
                                </span>
                                @endforeach
                            </span>
                        </div>
                        <div class="col-lg-3">
                            <style>
                                table.priceTable tr, table.priceTable td, table.priceTable th{
                                    border: 1px solid #eeeeee;
                                    width: 130px;
                                    text-align: center;
                                    font-size: 11px;
                                    margin-top: 10px;
                                    margin-bottom: 10px;
                                }
                            </style>
                            <table class="priceTable" style="margin-left:-20px;font-size:18px;width: 100%">
                                @foreach(json_decode($c->bookingItems, true) as $bookingItems)
                                    <tr>
                                        <th>{{__(strtolower($bookingItems['category']))}}</th>
                                        <td style>
                                            <div class="col-lg-4 input-group number-spinner" style="width: 100%;">
                                                <span>{{$bookingItems['count']}}</span>
                                                <!-- <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span class="glyphicon glyphicon-minus"></span></button>
                                                </span>
                                                <input value="{{$bookingItems['count']}}" id="{{$bookingItems['category'].$c->id}}" type="text" class="spinnerInputs form-control text-center" style="text-align:center;width:125%;height: 36px;border: 1px solid;border-color: #ccc;">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span class="glyphicon glyphicon-plus"></span></button>
                                                </span> -->
                                            </div>
                                        </td>
                                        <?php
                                        $option = $optionModel->where('id', $c->optionID);
                                        $pricing = $option->first()->pricings()->first();
                                        $adultPrice = 0;
                                        $youthPrice = 0;
                                        $childPrice = 0;
                                        $infantPrice = 0;
                                        $euCitizenPrice = 0;
                                        $minPersonArr = json_decode($pricing->minPerson, true);
                                        $maxPersonArr = json_decode($pricing->maxPerson, true);
                                        ?>
                                        @if($bookingItems['category'] == 'ADULT')
                                            <?php
                                            $adultPriceArr = json_decode($pricing->adultPrice, true);
                                            foreach ($maxPersonArr as $index => $item) {
                                                if ($bookingItems['count'] >= $minPersonArr[$index] && $bookingItems['count'] <= $item) {
                                                    $adultPrice = $adultPriceArr[$index];
                                                }
                                            }
                                            ?>
                                            <td><i class="{{$currencyIcon}}"></i>{{$currencyModel->calculateCurrencyForVisitor($bookingItems['count'] * $adultPrice)}}</td>
                                        @endif
                                        @if($bookingItems['category'] == 'EU_CITIZEN')
                                            <?php
                                            $euCitizenPriceArr = json_decode($pricing->euCitizenPrice, true);
                                            foreach ($maxPersonArr as $index => $item) {
                                                if ($bookingItems['count'] >= $minPersonArr[$index] && $bookingItems['count'] <= $item) {
                                                    $euCitizenPrice = $euCitizenPriceArr[$index];
                                                }
                                            }
                                            ?>
                                            <td><i class="{{$currencyIcon}}"></i>{{$currencyModel->calculateCurrencyForVisitor($bookingItems['count'] * $euCitizenPrice)}}</td>
                                        @endif
                                        @if($bookingItems['category'] == 'YOUTH')
                                            <?php
                                            $youthPriceArr = json_decode($pricing->youthPrice, true);
                                            foreach ($maxPersonArr as $index => $item) {
                                                if ($bookingItems['count'] >= $minPersonArr[$index] && $bookingItems['count'] <= $item) {
                                                    $youthPrice = $youthPriceArr[$index];
                                                }
                                            }
                                            ?>
                                            <td><i class="{{$currencyIcon}}"></i>{{$currencyModel->calculateCurrencyForVisitor($bookingItems['count'] * $youthPrice)}}</td>
                                        @endif
                                        @if($bookingItems['category'] == 'CHILD')
                                            <?php
                                            $childPriceArr = json_decode($pricing->childPrice, true);
                                            foreach ($maxPersonArr as $index => $item) {
                                                if ($bookingItems['count'] >= $minPersonArr[$index] && $bookingItems['count'] <= $item) {
                                                    $childPrice = $childPriceArr[$index];
                                                }
                                            }
                                            ?>
                                            <td><i class="{{$currencyIcon}}"></i>{{$currencyModel->calculateCurrencyForVisitor($bookingItems['count'] * $childPrice)}}</td>
                                        @endif
                                        @if($bookingItems['category'] == 'INFANT')
                                            <?php
                                            $infantPriceArr = json_decode($pricing->infantPrice, true);
                                            foreach ($maxPersonArr as $index => $item) {
                                                if ($bookingItems['count'] >= $minPersonArr[$index] && $bookingItems['count'] <= $item) {
                                                    $infantPrice = $infantPriceArr[$index];
                                                }
                                            }
                                            ?>
                                            <td><i class="{{$currencyIcon}}"></i>{{$currencyModel->calculateCurrencyForVisitor($bookingItems['count'] * $infantPrice)}}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="col-lg-2 options" style="text-align: center;margin-top: 10px; margin-bottom: 10px;">
                            <!-- <button data-cart-id="{{$c->id}}" class="updateCart" style="border: none;background: none;">{{__('update')}}</button> -->
                            <a href="{{url($langCodeForUrl.'/deleteItemFromCart/'.$c->id)}}" ><img src="{{asset('img/icon/trash.png')}}" class="info-icon delete-cart" /></a>
                        </div>
                    </div>
                    <div class="row" style="border-bottom: 1px solid darkgrey;padding-bottom: 1%;padding-top: 1%;">
                        <div class="col-lg-8" style="margin-top: 1%;">
                            <img src="{{asset('img/icon/clock.png')}}"  alt="Paris City Tours" class="info-icon" />
                            <span>{{$optionModel->where('id', $c->optionID)->first()->tourDuration}} @if($optionModel->where('id', $c->optionID)->first()->tourDurationDate=='h') {{__('hours')}} @elseif($optionModel->where('id', $c->optionID)->first()->tourDurationDate=='d') {{__('days')}} @elseif($optionModel->where('id', $c->optionID)->first()->tourDurationDate=='m') {{__('minutes')}} @endif </span>
                            <img src="{{asset('img/icon/ticket.png')}}"  alt="Paris City Tours" class="info-icon" />
                            <span>{{__('mobileTicket')}}</span>
                        </div>
                        <div class="col-lg-4">
                            <span style="font-size:12px!important;font-weight: bold">{{__('totalPrice')}}:
                                @if($c->totalPriceWOSO != $c->totalPrice)
                                    <del><i class="{{$currencyIcon}}"></i>{{$c->totalPriceWOSO}}</del>
                                @endif
                                    <i class="{{$currencyIcon}}"></i>{{$c->totalPrice}}
                            </span>
                            @if($isCommissioner)
                                <br>
                                <span style="font-size:12px;!important;font-weight: bold">
                                    {{__('commission')}}:
                                    <i class="{{$currencyIcon}}"></i>{{$c->totalCommission}}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
                </div>
                <aside class="col-md-4 col-xs-12">
                    <div class="box_style_1">
                        <h3 class="inner">- {{__('summary')}} -</h3>
                        <table class="table table_summary">
                            <tbody>
                            <tr>
                                <td>
                                    {{__('totalTicket')}}
                                </td>
                                <?php $ticketCount = 0 ?>
                                <td class="text-right">
                                    @foreach($cart as $cartTicket)
                                        <?php $ticketCount += $cartTicket->ticketCount; ?>
                                    @endforeach
                                        {{$ticketCount}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{__('totalItem')}}
                                </td>
                                <td class="text-right">
                                    {{$itemCount}}
                                </td>
                            </tr>
                            <?php $totalCommission = 0 ?>
                            @if($isCommissioner)
                            <tr>
                                <td style="font-weight: bold;">
                                    {{__('commission')}}
                                </td>
                                <td class="text-right" style="font-weight: bold;">
                                    @foreach($cart as $cartTotal)
                                        <?php $totalCommission += $cartTotal->totalCommission ?>
                                    @endforeach
                                    <i class="{{$currencyIcon}}"></i>{{$totalCommission}}
                                </td>
                            </tr>
                            @endif
                            <?php
                                $totalPrice = 0;
                                $totalPriceWOSO = 0;
                                $discount = 0;
                            ?>
                            @foreach($cart as $cartTotal)
                                <?php
                                $totalPrice += $cartTotal->totalPrice;
                                $totalPriceWOSO += $cartTotal->totalPriceWOSO;
                                $discount = $totalPriceWOSO - $totalPrice;
                                ?>
                            @endforeach
                            <tr>
                                <td style="font-weight: bold;">
                                    {{__('totalCost')}}
                                </td>
                                <td class="text-right" style="font-weight: bold;">
                                    <i class="{{$currencyIcon}}"></i>{{$totalPriceWOSO}}
                                </td>
                            </tr>
                            @if ($discount != 0)
                                <tr>
                                    <td style="font-weight: bold;">
                                        {{__('totalDiscount')}}
                                    </td>
                                    <td class="text-right" style="font-weight: bold;">
                                        <i class="{{$currencyIcon}}"></i>{{round($discount, 3)}}
                                    </td>
                                </tr>
                            @endif
                            <tr class="total">
                                <td style="font-size:13px;font-weight: bold;">
                                    {{__('amountOfPayment')}}
                                </td>
                                <td class="text-right" style="font-weight: bold;">
                                    <i class="{{$currencyIcon}}"></i>{{$totalPrice}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        @if(count($cart) > 0)
                            <a id="checkOut" class="btn_full">{{__('checkOut')}}</a>
                        @else
                            <a id="checkOut" class="btn_full">{{__('checkOut')}}</a>
                        @endif
                            <a class="btn_full_outline" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('all-products'))}}"><i class="icon-right"></i> {{__('continueShopping')}}</a>
                        <input type="hidden" id="checkoutUrl" value="{{$commonFunctions->getRouteLocalization('checkout')}}" >
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'cart'])
