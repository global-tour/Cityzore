<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$currencyIcon = session()->get('currencyIcon');
$productModel = new \App\Product();
$productGalleryModel = new \App\ProductGallery();
$productTranslationModel = new \App\ProductTranslation();
$attractionTranslationModel = new \App\AttractionTranslation();
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$language = \App\Language::where('code', $langCode)->first();
$langID = $language->id;
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$attractionTranslation = $attractionTranslationModel->where('languageID', $langID)->where('attractionID', $attraction->id)->first();
?>
<div class="col-lg-6" style="position: relative;padding: 0px;">
    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}" target="blank">
       @php
            $productsOfAttraction = \App\Product::whereJsonContains("attractions",(string)$attraction->id)->count();
        @endphp
        <span class="circle-attraction">{{$productsOfAttraction}} {{__('activitiesFound')}}</span>
        <img src="{{Storage::disk('s3')->url('attraction-images/' . $attraction->image)}}" alt="{{$attraction->name}}" style="object-fit: cover;width:100%;height: 100%;margin: 0px 0px 21px;max-height: 300px;min-height: 300px;">
    </a>
</div>
<div class="col-lg-6" style="max-height: 300px;min-height: 300px;">
    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}" target="blank">
        @if($attractionTranslation)
            <h2 class="attraction">{{$attractionTranslation->name}}</h2>
        @else
            <h2 class="attraction">{{$attraction->name}}</h2>
        @endif
    </a>
    <p>
        @if($attractionTranslation)
            {!! explode('.', html_entity_decode($attractionTranslation->description))[0] !!}.
        @else
            {!! explode('.', html_entity_decode($attraction->description))[0] !!}.
        @endif
    </p>
    <p>
    <div class="col-lg-6 col-sm-6 hidden-xs" style="height: 100px;bottom:0px;position: absolute;">
        @if($attractionTranslation)
            @if(!($attractionTranslation->tags==null))
                @for($i=0;$i<count(explode('|', $attractionTranslation->tags));$i++)
                    <h4 class="home-tags" style="width: 250px;text-align: center;background-color: #f51b5c;border:none;"><a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language))  .'-'.$attraction->id}}" target="blank" style="color:white;font-size: 15px;">{{explode('|', $attractionTranslation->tags)[$i]}}</a></h4>
                @endfor
            @endif
        @else
            @if(!($attraction->tags==null))
                @for($i=0;$i<count(explode('|', $attraction->tags));$i++)
                    <h4 class="home-tags" style="width: 250px;text-align: center;background-color: #f51b5c;border:none;"><a href={{url('/attraction/'.$attraction->slug.'-'.$attraction->id)}} target="blank" style="color:white;font-size: 15px;">{{explode('|', $attraction->tags)[$i]}}</a></h4>
                @endfor
            @endif
        @endif
    </div>
    <div class="col-lg-6 col-xs-12" style="height: 100px;bottom:0px;right: 2%;position: absolute;width: 270px;text-align: center;">
        <p class="attraction-price" style="width: 250px;">{{__('startingFrom')}}
            {{$currencyModel::calculateCurrencyForVisitor($attraction->min_price)}}<i class="{{$currencyIcon}}"></i>
        </p>
    </div>
</div>
<div class="col-lg-12 hidden-xs" style="padding: 0px;">
    <h3>{{__('topToursInThisAttraction')}}</h3>
    @php
        $topProds = \App\Product::with(['translations'])->where('attractions', 'like','%"'.$attraction->id.'"%')->where('isPublished',1)->where('isDraft', 0)->where('isSpecial', 0)->take(4)->get();
    @endphp
    @foreach($topProds as $product)
        <?php
        $productTranslation = $product->translations;

        $productSkills = $commonFunctions->getProductSkills($product);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            @if(!($commonFunctions->getOfferPercentage($product) == 0))
                <div class="band">
                    <div class="ribbon ribbon--orange" style="margin-top: 0px">% {{(int)$commonFunctions->getOfferPercentage($product)}}</div>
                </div>
            @endif
               @if(auth()->check())
                    @php

                       $data_type = auth()->user()->wishlists()->where("productID", $product->id)->count() ? "remove" : "add";

                    @endphp
                    <i class="add-to-wishlist icon-cz-heart-1" @if($data_type == "remove") style="color:#ff0000;" @endif data-product-id="{{$product->id}}" data-type="{{$data_type}}"></i>
                    @else
                    <i class="add-to-wishlist icon-cz-heart-1" data-product-id="{{$product->id}}" data-type="add"></i>
               @endif
            <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif">
                <div class="v_place_img">
                    @php
                        $prodImage = $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()
                    @endphp
                    <img class="mobile-image" style="height:160px;padding: 2%;object-fit: cover;"
                         @if ($prodImage)
                         src="{{Storage::disk('s3')->url('product-images-xs/' . $prodImage->src)}}"
                         @else
                         src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                         @endif
                         @if ($productTranslation)
                         alt="{{$productTranslation->title}}"
                         @else
                         alt="{{$product->title}}"
                         @endif
                         title="Tour Booking" />
                </div>
            </a>
            <div class="b_pack rows">
                <div class="row" style="height: 95px;width: 100%;">
                    <div class="col-md-12" style="padding-left: 8%;padding-top: 1%;">
                        <p class="product-category">{{$product->category}}</p>
                        <h3 style="position: absolute;color: #1d6db2;text-decoration: revert;font-weight: 400;margin-top: 0px;">
                            @if ($productTranslation)
                                <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}" style="font-size: 14px; font-weight: 600;color: #1d6db2;    text-decoration: underline #cdcdcd;">{{$productTranslation->title}}</a>
                            @else
                                <a href="{{url($langCodeForUrl.'/'.$product->url)}}" style="font-size: 16px; font-weight: 600;color: #1d6db2;text-decoration: underline #cdcdcd;">{{$product->title}}</a>
                            @endif
                        </h3>
                    </div>
                </div>
                <div class="row">
                    <div class="row container">
                        <div class="hidden-lg hidden-md hidden-sm col-xs-12 mobile_desc">
                            @if($productTranslation)
                                <p>{{$productTranslation->shortDesc}}</p>
                            @else
                                <p>{{$product->shortDesc}}</p>
                            @endif
                        </div>
                    </div>
                      <div class="row container" style="display: table-cell;padding-top: 5%;padding-bottom: 5%;height: 123px;">

                                                      @if(array_key_exists("isFreeCancellation", $productSkills))

                                                        <p style="line-height: 10px;font-size: 13px;color: #69bc6b;"><i class="icon-cz-blockout" style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('freeCancellation')}}</i></p>

                                                        @endif

                                                         @if(array_key_exists("isSkipTheLine", $productSkills))

                                                        <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-add-time" style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('skipTheLineTickets')}}</i></p>

                                                        @endif


                                                     @if(array_key_exists("tourDuration", $productSkills))

                                                        <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-hour" style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">Duration: {{$productSkills["tourDuration"]}} {{$productSkills["tourDurationDate"]}}</i></p>

                                                        @endif


                                                         @if(array_key_exists("guideInformation", $productSkills))
                                                        <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-logs" style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{implode(", ",$productSkills["guideInformation"])}}</i></p>
                                                        @endif
                                                    </div>
                    <div class="col-lg-12" style="height: 20px;">
                        <div class="mobile_from">
                            <span style="font-size:14px;">{{__('from')}}
                                <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                    @if($specialOffer != 0)
                                        <?php
                                        $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - (($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                        ?>
                                        <span class="special-offer-price" style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i class="{{$currencyIcon}}"></i></span>
                                        <span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}</span><i class="{{$currencyIcon}}"></i>
                                        <?php
                                        $commissionerEarns = $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                        ?>
                                    @else
                                        {{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i class="{{$currencyIcon}}"></i>
                                        <?php
                                        $commissionerEarns = $currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                        ?>
                                    @endif
                                    {{$commissionerEarns}} COM<i class="{{$currencyIcon}}"></i>
                                @else
                                    @if($specialOffer != 0)

                                        @php
                                            $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - ($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                        @endphp
                                        <span class="special-offer-price" style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i class="{{$currencyIcon}}"></i></span>
                                        <span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i class="{{$currencyIcon}}"></i></span>
                                    @else
                                        <span style="color:#ffad0c; font-size: 18px">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i class="{{$currencyIcon}}" style="color: #ffad0c"></i></span>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12" style="border-top: 1px solid #cdcdcd;height: 26px;padding-top: 1%;">
                        <div class="rating" style="margin-left: 3%;">
                            {!! $commonFunctions->showStarsforRate($product->rate) !!}

                            @if(!($product->rate == null))
                                <label style="font-size: 13px;vertical-align: text-bottom; float:right;color: #1A2B50; padding-left: 3px;">{{number_format((float)(round($product->rate, 1)), 1, '.', '')}} </label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
