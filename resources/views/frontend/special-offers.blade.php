@include('frontend-partials.head', ['page' => 'special-offers'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$productTranslationModel = new \App\ProductTranslation();
$langID = \App\Language::where('code', $langCode)->first()->id;

?>

    <section class="hot-page2-alp hot-page2-pa-sp-top">
        <div class="container" style="width: 90%">
            <div class="row">
                <div class="hot-page2-alp-con">
                    <div class="col-md-12 hot-page2-alp-con-right">
                        <div class="hot-page2-alp-con-right-1">
                            <div class="row">
                                <div class="spe-title">
                                    <h2 style="margin-top: 2%;">{!! __('specialOffersforParisCityTours') !!}</h2>
                                    <div class="title-line">
                                        <div class="tl-1"></div>
                                        <div class="tl-2"></div>
                                        <div class="tl-3"></div>
                                    </div>
                                </div>
                                @if(count($products)==0)
                                    <div class="col-lg-12">
                                        <span style="font-size: 15px !important;">{!! __('thereIsNoSpecialOffer') !!}</span>
                                    </div>
                                @else
                                    @foreach ($products as $product)
                                        @php
                                            $offerPercentage = (new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product);
                                        @endphp
                                        @if($offerPercentage > 0)
                                            <div class="col-lg-4">
                                                <div class="hot-page2-alp-r-list">
                                                    <div class="col-md-12 hot-page2-alp-r-list-re-sp">
                                                        <a href="{{url($langCodeForUrl.'/'.$product->url)}}">
                                                            <div class="band">
                                                                <div class="ribbon  ribbon--orange" style="margin-top: 0px">% {{round($offerPercentage)}}</div>
                                                            </div>
                                                            <div class="hot-page2-hli-1">
                                                                <img
                                                                    @if($product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', $product->coverPhoto)->first())
                                                                    src="{{Storage::disk('s3')->url('product-images/' . $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', $product->coverPhoto)->first()->src)}}"
                                                                    @else
                                                                    src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                                                                    @endif
                                                                    alt="" style="image-rendering: pixelated;width: 100%;max-height: 240px;min-height: 240px;">
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="trav-list-bod">
                                                                <h3 style="margin-top: 3%;font-size: 21px;">
                                                                    <?php
                                                                    $productTranslation = $productTranslationModel->where('productID', $product->id)->where('languageID', $langID)->first();
                                                                    ?>
                                                                    @if ($productTranslation)
                                                                        <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}" style="font-size: 15px;font-weight: 500;color: #1A2B50">{{$productTranslation->title}}</a>
                                                                    @else
                                                                        <a href="{{url($langCodeForUrl.'/'.$product->url)}}" style="font-size: 15px;font-weight: 500;color: #1A2B50">{{$product->title}}</a>
                                                                    @endif
                                                                </h3>
                                                            <div class="dir-rat-star" style="font-size: 20px;">
                                                                <div class="rating" style="direction: ltr;width:100%;">
                                                                    @for($i=1;$i<=$product->rate;$i++)
                                                                        <i class="icon-cz-star" style="color: #ffad0c; font-size: 20px;"></i>
                                                                    @endfor
                                                                    @if(!($product->rate == null))
                                                                        <div style="font-size: 13px;vertical-align: text-bottom;">({{$product->rate}}/5)</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="hot-page2-alp-ri-p3 tour-alp-ri-p3">
                                                            <span class="hot-list-p3-1">{{__('pricesStarting')}}</span>
                                                            <span class="hot-list-p3-2">
                                                                @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                                    <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}
                                                                    <?php
                                                                    $commissionerEarns = App\Currency::calculateCurrencyForVisitor(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - App\Currency::calculateCurrencyForVisitor(((new App\Http\Controllers\Helpers\CommonFunctions)->getCommissionMinPrice($product, auth()->guard('web')->user()->id))));
                                                                    ?>
                                                                    <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                                                @else
                                                                    <?php
                                                                        $specialOffer = (new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product);
                                                                    ?>
                                                                    @if(!($specialOffer == 0))
                                                                        <?php
                                                                            $specialOfferPrice = App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100);
                                                                        ?>
                                                                        <span class="special-offer-price" style="font-size: 25px;"><i class="{{session()->get('currencyIcon')}}"></i>{{number_format((float)$specialOfferPrice, 2, '.', '')}}</span><br>
                                                                        <span class="strikeout"><i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                                                    @else
                                                                        <span><i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                                                    @endif
                                                                @endif
                                                            </span>
                                                            <span class="hot-list-p3-4">
                                                                <a href="{{url($langCodeForUrl.'/'.$product->url)}}" class="hot-page2-alp-quot-btn">{{__('bookNow')}}</a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'special-offers'])

