@include('frontend-partials.head', ['page' => 'home'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$currencyIcon = session()->get('currencyIcon');
$productModel = new \App\Product();
$productGalleryModel = new \App\ProductGallery();
$productTranslationModel = new \App\ProductTranslation();
$attractionModel = \App\Attraction::with('translations');
$attractionTranslationModel = new \App\AttractionTranslation();
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$language = \App\Language::where('code', $langCode)->first();
$langID = $language->id;
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <?php
    $config = App\Config::where('userID', -1)->first();
    $homeBanner = Storage::disk('s3')->url('website-images/' . $config->homeBanner);
    ?>
    <div class="tourz-search" style="background: url('{{$homeBanner}}') center center no-repeat; background-size: cover;height: 600px;">
        <div class="container" style="float: left;">
            <div class="row">
                <div class="tourz-search-1">
                    <h1>{{__('planYourParisTravelNow')}}</h1>
                    <p class="hidden-xs hidden-sm">{{__('searchAttractiveThingsEiffel2')}}</p>
                    <form method="GET" class="tourz-search-form tour-form-two" action="{{url($langCodeForUrl.'/s')}}"
                          autocomplete="off">
                        <div class="home-banner-search">
                            <div class="search-bar">
                                <input type="text" placeholder="{{__('searchLocationsAttractions')}}" name="q" class="search-field" id="searchInput">
                                <button type="submit" class="search-button">SEARCH</button>
                                <div class="suggestions-container"></div>
                            </div>
                            <div class="mobile-search-bar">
                                <button type="button" class="search-field" id="mobile-search-button">{{__('searchLocationsAttractions')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row hidden-xs" style="margin-bottom: 3%;margin-top: 3%;">
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-like"></i>
                <br>
                <span style="text-align: center;">{!! __('freeCancellation') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-availability"></i>
                <br>
                <span style="text-align: center;">{!! __('724support') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-payment"></i>
                <br>
                <span style="text-align: center;">{!! __('securePayment') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-mobile"></i>
                <br>
                <span style="text-align: center;">Mobile Ticket</span>
            </div>
        </div>
    </div>
</section>

<section style="background-color: #e7f4ff;margin: 3%;margin-top: 0px;">
    <div class="spe-title">
        <h2 style="background-color: white;">{!! __('parisTopAttractions') !!}</h2>
        <div class="title-line">
            <div class="tl-1" style="background: #b3afaf;"></div>
            <div class="tl-2"></div>
            <div class="tl-3" style="background: #b3afaf;"></div>
        </div>
    </div>
    <div class="rows tb-space pad-top-o pad-bot-redu">
        <div class="row home-attraction-responsive">
            <div class="home-attraction">
                <nav style="background-color: transparent;height: 100%;">
                    <script>
                        function getTours(id, element) {

                            $("body").waitMe({
                                effect: 'bounce',
                                text: '',
                                bg: 'rgba(255,255,255,0.7)',
                                color: '#f4364f',
                                maxSize: '',
                                waitTime: -1,
                                textPos: 'vertical',
                                fontSize: '',
                                source: '',
                                onClose: function () {
                                }
                            });

                            $.ajax({
                                method: "GET",
                                data: {id},
                                url: "{{ url('/home-attraction-tours') }}",
                                success: function (response) {
                                    $('#' + element).html(response)
                                },
                            }).always(function () {
                                $("body").waitMe('hide');
                            })
                        }
                    </script>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist"
                         style="border-bottom:0px;text-align: center;background-color: #63b7fa24;padding: 1px;border: 1px solid #fffdfd;">
                        @foreach($attractionModel->where('id', 1)->orWhere('id', 2)->orWhere('id', 3)->orWhere('id', 4)->orWhere('id', 5)->orWhere('id', 13)->orWhere('id', 24)->orWhere('id', 32)->get() as $attraction)
                            @if($attraction->translations)
                                @php
                                    $attractionTranslation = $attraction->translations;
                                @endphp
                                <div class="col-lg-3 col-md-4 col-sm-6 hidden-xs">
                                    <a class="nav-item nav-link home-button"
                                       onclick="getTours('{{$attraction->id}}', '{{$attractionTranslation->slug}}')"
                                       data-toggle="tab" href="#{{$attractionTranslation->slug}}" role="tab"
                                       aria-controls="nav-home" aria-selected="true"
                                       style="border-radius: 5px;width:100%;">{{$attractionTranslation->name}}</a>
                                </div>
                                <a class="nav-item nav-link home-button hidden-lg hidden-md hidden-sm"
                                   onclick="getTours('{{$attraction->id}}', '{{$attractionTranslation->slug}}')"
                                   data-toggle="tab" href="#{{$attractionTranslation->slug}}" role="tab"
                                   aria-controls="nav-home" aria-selected="true"
                                   style="border-radius: 5px;width:auto;">{{$attractionTranslation->name}}</a>
                            @else
                                <div class="col-lg-3 col-md-4 col-sm-6 hidden-xs">
                                    <a class="nav-item nav-link home-button"
                                       onclick="getTours('{{$attraction->id}}', '{{$attraction->slug}}')"
                                       data-toggle="tab" href="#{{$attraction->slug}}" role="tab"
                                       aria-controls="nav-home" aria-selected="true"
                                       style="border-radius: 5px;width: 100%;">{{$attraction->name}}</a>
                                </div>
                                <a class="nav-item nav-link home-button hidden-lg hidden-md hidden-sm"
                                   onclick="getTours('{{$attraction->id}}', '{{$attraction->slug}}')" data-toggle="tab"
                                   href="#{{$attraction->slug}}" role="tab" aria-controls="nav-home"
                                   aria-selected="true"
                                   style="border-radius: 5px;width: auto;">{{$attraction->name}}</a>
                            @endif
                        @endforeach
                    </div>
                </nav>
            </div>
            <div class="home-attraction home-attraction-loading">
                <div class="tab-content" id="nav-tabContent">
                    @foreach($activeAttractions as $key => $attraction)
                        <?php $attractionTranslation = $attraction->translations ?>
                        <div class="tab-pane fade"
                             id="@if($attractionTranslation){{$attractionTranslation->slug}}@else{{$attraction->slug}}@endif"
                             role="tabpanel" aria-labelledby="nav-home-tab"
                             style="background-color: transparent; margin-top: 2%;">
                            @if($key == 0)
                                <div class="col-lg-6" style="position: relative;padding: 0px;">
                                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}"
                                       target="blank">

                                        @php
                                            $productsOfAttraction = \App\Product::whereJsonContains("attractions",(string)$attraction->id)->count();
                                        @endphp

                                        <span
                                            class="circle-attraction">{{$productsOfAttraction}} {{__('activitiesFound')}}</span>
                                        <img
                                            src="{{Storage::disk('s3')->url('attraction-images/' . $attraction->image)}}"
                                            alt="{{$attraction->name}}"
                                            style="object-fit: cover;width:100%;height: 100%;margin: 0px 0px 21px;max-height: 300px;min-height: 300px;">
                                    </a>
                                </div>
                                <div class="col-lg-6" style="max-height: 300px;min-height: 300px;">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}"
                                               target="blank">
                                                @if($attractionTranslation)
                                                    <h2 class="attraction" >{{$attractionTranslation->name}}</h2>
                                                @else
                                                    <h2 class="attraction" >{{$attraction->name}}</h2>
                                                @endif
                                            </a>
                                            <p>
                                                @if($attractionTranslation)
                                                    {!! explode('.', html_entity_decode($attractionTranslation->description))[0] !!}
                                                    .
                                                @else
                                                    {!! explode('.', html_entity_decode($attraction->description))[0] !!}.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row home-tags-row">
                                        <div class="col-md-6 col-sm-12">
                                            @if($attractionTranslation)
                                                @if(!($attractionTranslation->tags==null))
                                                    @for($i=0;$i<count(explode('|', $attractionTranslation->tags));$i++)
                                                        <h4 class="home-tags-l">
                                                            <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language))  .'-'.$attraction->id}}"
                                                               target="blank">{{ucwords(explode('|', $attractionTranslation->tags)[$i])}}</a>
                                                        </h4>
                                                    @endfor
                                                @endif
                                            @else
                                                @if(!($attraction->tags==null))
                                                    @for($i=0;$i<count(explode('|', $attraction->tags));$i++)
                                                        <h4 class="home-tags-l">
                                                            <a href={{url('/attraction/'.$attraction->slug.'-'.$attraction->id)}} target="blank">{{ucwords(explode('|', $attraction->tags)[$i])}}</a>
                                                        </h4>
                                                    @endfor
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <h4 class="home-tags-r">{{__('startingFrom')}}
                                                {{$currencyModel::calculateCurrencyForVisitor($attraction->min_price)}}
                                                <i class="{{$currencyIcon}}"></i>
                                            </h4>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-12 hidden-xs" style="padding: 0px;">
                                    <h3 style="background-image: linear-gradient(to right, #e8e8e8, white);font-weight: 400">{{__('topToursInThisAttraction')}}</h3>
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
                                                    <div class="ribbon ribbon--orange" style="margin-top: 0px">
                                                        % {{(int)$commonFunctions->getOfferPercentage($product)}}</div>
                                                </div>
                                            @endif
                                            @if(auth()->check())
                                                @php

                                                    $data_type = auth()->user()->wishlists()->where("productID", $product->id)->count() ? "remove" : "add";

                                                @endphp
                                                <i class="add-to-wishlist icon-cz-heart-1"
                                                   @if($data_type == "remove") style="color:#ff0000;"
                                                   @endif data-product-id="{{$product->id}}"
                                                   data-type="{{$data_type}}"></i>
                                            @else
                                                <i class="add-to-wishlist icon-cz-heart-1"
                                                   data-product-id="{{$product->id}}" data-type="add"></i>
                                            @endif
                                            <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif">
                                                <div class="v_place_img">
                                                    @php
                                                        $prodImage = $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()
                                                    @endphp
                                                    <img class="mobile-image"
                                                         style="height:160px;padding: 2%;object-fit: cover;"
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
                                                         title="Tour Booking"/>
                                                </div>
                                            </a>
                                            <div class="b_pack rows">
                                                <div class="row" style="height: 95px;width: 100%;">
                                                    <div class="col-md-12" style="padding-left: 8%;padding-top: 1%;">
                                                        <?php
                                                        $categoryID = \App\Category::where('categoryName', $product->category)->first()->id;
                                                        $categoryTranslation = \App\CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $langID)->first();
                                                        ?>
                                                        @if($categoryTranslation)
                                                            <p class="product-category">{{$categoryTranslation->categoryName}}</p>
                                                        @else
                                                            <p class="product-category">
                                                                <span>{{$product->category}}</span></p>
                                                        @endif
                                                        <h3 style="position: absolute;color: #1d6db2;text-decoration: revert;font-weight: 400;margin-top: 0px;">
                                                            @if ($productTranslation)
                                                                <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}"
                                                                   style="font-size: 14px; font-weight: 600;color: #1d6db2;    text-decoration: underline #cdcdcd;">{{$productTranslation->title}}</a>
                                                            @else
                                                                <a href="{{url($langCodeForUrl.'/'.$product->url)}}"
                                                                   style="font-size: 16px; font-weight: 600;color: #1d6db2;text-decoration: underline #cdcdcd;">{{$product->title}}</a>
                                                            @endif
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="row container">
                                                        <div
                                                            class="hidden-lg hidden-md hidden-sm col-xs-12 mobile_desc">
                                                            @if($productTranslation)
                                                                <p>{{$productTranslation->shortDesc}}</p>
                                                            @else
                                                                <p>{{$product->shortDesc}}</p>
                                                            @endif
                                                        </div>
                                                    </div>


                                                    <div class="row container home-product"
                                                         style="display: table-cell;padding-top: 5%;height: 123px;">

                                                        @if(array_key_exists("isFreeCancellation", $productSkills))

                                                            <p style="line-height: 10px;font-size: 13px;color: #69bc6b;">
                                                                <i class="icon-cz-blockout"
                                                                   style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('freeCancellation')}}</i>
                                                            </p>

                                                        @endif

                                                        @if(array_key_exists("isSkipTheLine", $productSkills))

                                                            <p style="line-height: 10px;font-size: 13px;"><i
                                                                    class="icon-cz-add-time"
                                                                    style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('skipTheLineTickets')}}</i>
                                                            </p>

                                                        @endif


                                                        @if(array_key_exists("tourDuration", $productSkills))

                                                            <p style="line-height: 10px;font-size: 13px;"><i
                                                                    class="icon-cz-hour"
                                                                    style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">Duration: {{$productSkills["tourDuration"]}} {{$productSkills["tourDurationDate"]}}</i>
                                                            </p>

                                                        @endif


                                                        @if(array_key_exists("guideInformation", $productSkills))
                                                            <p style="line-height: 10px;font-size: 13px;"><i
                                                                    class="icon-cz-logs"
                                                                    style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{implode(", ",$productSkills["guideInformation"])}}</i>
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="col-lg-12" style="height: 20px;">
                                                        <div class="mobile_from">
                                                        <span style="font-size:14px;">{{__('from')}}
                                                            <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                                            @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                                @if($specialOffer != 0)
                                                                    <?php
                                                                    $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - (($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100), 2);
                                                                    ?>
                                                                    <span class="special-offer-price"
                                                                          style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                            class="{{$currencyIcon}}"></i></span>
                                                                    <span
                                                                        class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}</span>
                                                                    <i class="{{$currencyIcon}}"></i>
                                                                    <?php
                                                                    $commissionerEarns = $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                                                    ?>
                                                                @else
                                                                    {{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}
                                                                    <i class="{{$currencyIcon}}"></i>
                                                                    <?php
                                                                    $commissionerEarns = $currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                                                    ?>
                                                                @endif
                                                                {{$commissionerEarns}} COM<i
                                                                    class="{{$currencyIcon}}"></i>
                                                            @else
                                                                @if($specialOffer != 0)

                                                                    @php
                                                                        $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - ($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                                                    @endphp
                                                                    <span class="special-offer-price"
                                                                          style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                            class="{{$currencyIcon}}"></i></span>
                                                                    <span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                            class="{{$currencyIcon}}"></i></span>
                                                                @else
                                                                    <span style="color:#ffad0c; font-size: 18px">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                            class="{{$currencyIcon}}"
                                                                            style="color: #ffad0c"></i></span>
                                                                @endif
                                                            @endif
                                                        </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12"
                                                         style="border-top: 1px solid #cdcdcd;height: 26px;padding-top: 1%;">
                                                        <div class="rating" style="margin-left: 3%;">
                                                            {!! $commonFunctions->showStarsforRate($product->rate) !!}

                                                            @if(!($product->rate == null))
                                                                <label
                                                                    style="font-size: 13px;vertical-align: text-bottom; float:right;color: #1A2B50; padding-left: 3px;">{{number_format((float)(round($product->rate, 1)), 1, '.', '')}} </label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="rows pad-bot-redu tb-space">
        <div class=" top-paris-tours container">
            <div class="spe-title">
                <h2>{!! __('topParisTours') !!}</h2>
                <div class="title-line">
                    <div class="tl-1" style="background: #b3afaf;"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3" style="background: #b3afaf;"></div>
                </div>
            </div>
            <div>
                <div class="row">
                    @foreach($homePageProduct as $product)
                        <?php
                        $productTranslation = $product->translations;

                        $productSkills = $commonFunctions->getProductSkills($product);
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 b_packages wow slideInUp"
                             data-wow-duration="0.5s">
                            @if(!($commonFunctions->getOfferPercentage($product) == 0))
                                <div class="band">
                                    <div class="ribbon  ribbon--orange" style="margin-top: 0px">
                                        % {{(int)$commonFunctions->getOfferPercentage($product)}}</div>
                                </div>
                            @endif
                        <!-- @if($product->id == 5)
                            <div class="band" style="margin: 5%;background-color: #fc6c4f;color: white;">
                                <span style="padding-left: 17%;">New</span>
                            </div>
@endif -->
                            @if(auth()->check())
                                @php

                                    $data_type = auth()->user()->wishlists()->where("productID", $product->id)->count() ? "remove" : "add";

                                @endphp
                                <i class="add-to-wishlist icon-cz-heart-1"
                                   @if($data_type == "remove") style="color:#ff0000;"
                                   @endif data-product-id="{{$product->id}}" data-type="{{$data_type}}"></i>
                            @else
                                <i class="add-to-wishlist icon-cz-heart-1" data-product-id="{{$product->id}}"
                                   data-type="add"></i>
                            @endif
                            <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif">
                                <div class="v_place_img">
                                    <img class="mobile-image" style="height:195px;padding: 2%;object-fit: cover;"
                                         @if ($product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first())
                                         src="{{Storage::disk('s3')->url('product-images-xs/' . $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()->src)}}"
                                         @else
                                         src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                                         @endif
                                         @if ($productTranslation)
                                         alt="{{$productTranslation->title}}"
                                         @else
                                         alt="{{$product->title}}"
                                         @endif
                                         title="Tour Booking"/>
                                </div>
                            </a>
                            <div class="b_pack rows" style="">
                                <div class="row" style="height: 95px;width: 100%;">
                                    <div class="col-md-12" style="padding-left: 8%;padding-top: 1%;">
                                        <?php
                                        $categoryID = \App\Category::where('categoryName', $product->category)->first()->id;
                                        $categoryTranslation = \App\CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $langID)->first();
                                        ?>
                                        @if($categoryTranslation)
                                            <p class="product-category">{{$categoryTranslation->categoryName}}</p>
                                        @else
                                            <p class="product-category">{{$product->category}}</p>
                                        @endif
                                        <h3 style="position: absolute;color: #1d6db2;text-decoration: revert;font-weight: 400;margin-top: 0px;">
                                            @if ($productTranslation)
                                                <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}"
                                                   style="font-size: 16px; font-weight: 600;color: #1d6db2;text-decoration: underline #cdcdcd;">{{$productTranslation->title}}</a>
                                            @else
                                                <a href="{{url($langCodeForUrl.'/'.$product->url)}}"
                                                   style="font-size: 16px; font-weight: 600;color: #1d6db2;text-decoration: underline #cdcdcd;">{{$product->title}}</a>
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
                                    <div class="row container home-product"
                                         style="display: table-cell;padding-top: 6%;height: 123px;">

                                        @if(array_key_exists("isFreeCancellation", $productSkills))

                                            <p style="line-height: 10px;font-size: 13px;color: #69bc6b;"><i
                                                    class="icon-cz-blockout"
                                                    style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('freeCancellation')}}</i>
                                            </p>

                                        @endif

                                        @if(array_key_exists("isSkipTheLine", $productSkills))

                                            <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-add-time"
                                                                                             style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{__('skipTheLineTickets')}}</i>
                                            </p>

                                        @endif


                                        @if(array_key_exists("tourDuration", $productSkills))

                                            <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-hour"
                                                                                             style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">Duration: {{$productSkills["tourDuration"]}} {{$productSkills["tourDurationDate"]}}</i>
                                            </p>

                                        @endif


                                        @if(array_key_exists("guideInformation", $productSkills))
                                            <p style="line-height: 10px;font-size: 13px;"><i class="icon-cz-logs"
                                                                                             style="width: fit-content;padding-left: 3px;font-size: 15px;font-style: inherit;">{{implode(", ",$productSkills["guideInformation"])}}</i>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-lg-12" style="height: 20px;">
                                        <div class="mobile_from">
                                            <span style="font-size:14px;">{{__('from')}}
                                                <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                                @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                    @if($specialOffer != 0)
                                                        <?php
                                                        $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - ($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100), 2);
                                                        ?>
                                                        <span class="special-offer-price" style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                class="{{$currencyIcon}}"></i></span>
                                                        <span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                class="{{$currencyIcon}}"></i></span>
                                                        <?php
                                                        $commissionerEarns = $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                                        ?>
                                                    @else
                                                        {{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}
                                                        <i class="{{$currencyIcon}}"></i>
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

                                                        <span class="special-offer-price" style="font-size: 18px;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                class="{{$currencyIcon}}"></i></span>
                                                        <span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                class="{{$currencyIcon}}"></i></span>
                                                    @else
                                                        <span style="color:#ffad0c;font-size: 18px">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                class="{{$currencyIcon}}"
                                                                style="color: #ffad0c;"></i></span>
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12"
                                         style="border-top: 1px solid #cdcdcd;height: 26px;padding-top: 1%;">
                                        <div class="rating" style="margin-left: 3%;">
                                            {!! $commonFunctions->showStarsforRate($product->rate) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row" style="text-align: center;">
                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('all-products'))}}"
                       class="link-btn">{{__('seeAllTours')}}</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="rows pad-bot-redu tb-space hidden-xs" style="background-color: #4e47480f;">
        <div class="container">
            <div class="col-lg-4 col-md-12 col-sm-12 big-bus-banner-text" style="margin-top: 3%;">
                <div class="row">
                    <span>Seine River Cruise Tours</span>
                </div>
                <div class="row" style="padding: 10px;">
                    @php
                        $attr = \App\Attraction::with('translations')->where('id', 2)->first();
                    @endphp
                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attr, $language))}}"
                       class="link-btn" style="text-align: center;">{{__('seeAllTours')}}</a>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="example">
                    <div class="block">
                        <div class="side -main"></div>
                        <div class="side -left"></div>
                    </div>
                    <div class="block">
                        <div class="side -main"></div>
                        <div class="side -left"></div>
                    </div>
                    <div class="block">
                        <div class="side -main"></div>
                        <div class="side -left"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row hidden-xs" style="margin-bottom: 3%;margin-top: 3%;">
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-like"></i>
                <br>
                <span style="text-align: center;">{!! __('freeCancellation') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-availability"></i>
                <br>
                <span style="text-align: center;">{!! __('724support') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-payment"></i>
                <br>
                <span style="text-align: center;">{!! __('securePayment') !!}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 home-icons" style="text-align: center;">
                <i class="icon-cz-mobile"></i>
                <br>
                <span style="text-align: center;">Mobile Ticket</span>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="spe-title spe-title-1">
        <h2>{!! __('topParisToursThisMonth') !!}</h2>
        <div class="title-line">
            <div class="tl-1" style="background: #b3afaf;"></div>
            <div class="tl-2"></div>
            <div class="tl-3" style="background: #b3afaf;"></div>
        </div>
    </div>
    <div class="rows pla pad-bot-redu tb-space">
        <div class="pla1 p-home container">
            <div class="spe-title spe-title-1">
                <p>{{__('topParisToursText')}}</p>
            </div>
            <div class="col-md-12">
                @foreach($productsForSeine as $i => $product)
                    <?php
                    $productTranslation = $productTranslationModel->where('productID', $product->id)->where('languageID', $langID)->first();
                    ?>
                    @if($productTranslation)
                        <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}">
                            @else
                                <a href="{{url($langCodeForUrl.'/'.$product->url)}}">
                                    @endif
                                    <div class="col-md-6 col-sm-6 col-xs-12 place" style="padding: 0px;">
                                        <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 0px;">
                                            <img
                                                @if($productGalleryModel::where('id', $product->coverPhoto)->first())
                                                src="{{Storage::disk('s3')->url('product-images/' . $productGalleryModel::where('id', $product->coverPhoto)->first()->src)}}"
                                                @else
                                                src="{{Storage::disk('s3')->url('product-images/default_product.jpg')}}"
                                                @endif
                                                alt="{{$product->title}}"
                                                style="max-height: 280px;min-width: 300px;min-height: 280px;object-fit: cover;"/>
                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-12"
                                             style="background-color: white;max-height: 300px;min-width: 280px;min-height: 280px;padding: 3%;">
                                            @if($productTranslation)
                                                <h3 style="font-size: 15px;line-height: 20px; color:#1d6db2;font-weight: 400;height: 60px;text-align: center;">
                                                    <i class="icon-cz-angle-right"></i>{{$productTranslation->title}}
                                                </h3>
                                            @else
                                                <h3 style="font-size: 15px;line-height: 20px; color:#1d6db2;font-weight: 400;height: 60px;text-align: center;">
                                                    <i class="icon-cz-angle-right"></i>{{$product->title}}</h3>
                                            @endif
                                            <hr style="margin:0px; padding: 0px;">
                                            @if($productTranslation)
                                                <p style="height: 115px;margin-top: 4%;">{!! html_entity_decode(mb_substr($productTranslation->shortDesc, 0, 75, "UTF-8")) !!}
                                                    ...</p>
                                            @else
                                                <p style="height: 115px;margin-top: 4%;">{!! html_entity_decode(mb_substr($product->shortDesc, 0, 75, "UTF-8")) !!}
                                                    ...</p>
                                            @endif
                                            <div class="row">
                                                @if($productTranslation)
                                                    <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}"
                                                       class="btn" target="_blank"
                                                       style="background-color: #f51b5c !important;">{{__('details')}}</a>
                                                @else
                                                    <a href="{{url($langCodeForUrl.'/'.$product->url)}}" class="btn"
                                                       target="_blank"
                                                       style="background-color: #f51b5c !important;">{{__('details')}}</a>
                                                @endif
                                                <span style="font-size:18px;float: right;">
                                            <ul style="margin-right: 41px;">
                                                <li style="list-style-type: none;margin-right: 73px;">
		                                        <div class="starburst3"><span><span><span><span><span><span><span><span>
                                             <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                                                                    @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                                                        @if($specialOffer != 0)
                                                                                            <?php
                                                                                            $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - (($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100), 2);
                                                                                            ?>
                                                                                            <div
                                                                                                style="padding-top: 0px;color:white;">{{__('from')}}</div>
                                                                                            <div
                                                                                                class="special-offer-price"
                                                                                                style="font-size: 18px;color:white;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                                                    class="{{$currencyIcon}}"></i></div>
                                                                                            <i class="{{$currencyIcon}}"
                                                                                               style="font-size: 15px !important;"></i>
                                                                                            <span class="strikeout"
                                                                                                  style="color:white;">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}</span>
                                                                                            <?php
                                                                                            $commissionerEarns = $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                                                                            ?>
                                                                                        @else
                                                                                            <div
                                                                                                style="padding-top: 0px;color:white;">{{__('from')}}</div>{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}
                                                                                            <i class="{{$currencyIcon}}"></i>
                                                                                            <?php
                                                                                            $commissionerEarns = $currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                                                                            ?>
                                                                                        @endif
                                                                                        {{$commissionerEarns}} COM<i
                                                                                            class="{{$currencyIcon}}"></i>
                                                                                    @else
                                                                                        @if($specialOffer != 0)
                                                                                            @php
                                                                                                $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) - ($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product)) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                                                                            @endphp
                                                                                            <div
                                                                                                style="padding-top: 0px;color:white;">{{__('from')}}</div>
                                                                                            <div
                                                                                                class="special-offer-price"
                                                                                                style="font-size: 18px;color:white;">{{number_format($specialOfferPrice, 2, '.', '')}}<i
                                                                                                    class="{{$currencyIcon}}"></i></div>
                                                                                            <div class="strikeout"
                                                                                                 style="white-space: nowrap;color:white;">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                                                    class="{{$currencyIcon}}"
                                                                                                    style="font-size: 15px !important;"></i></div>
                                                                                        @else
                                                                                            <div
                                                                                                style="padding-top: 15px;color:white;">{{__('from')}}</div>
                                                                                            <div
                                                                                                style="color:white; font-size: 18px">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPriceHome($product))}}<i
                                                                                                    class="{{$currencyIcon}}"
                                                                                                    style="color: white"></i></div>
                                                                                        @endif
                                                                                    @endif
                                        </span></span></span></span></span></span></span></span></div>
	</li>
                                                   </span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span>
                                </a>
                                </li>
                                </ul>

                                </span>
            </div>
        </div>
    </div>
    @if ($i % 2 == 1)
    </div>
    <div class="col-md-12" >
        @endif
        @endforeach
        <div class="col-md-12 text-center" style="height: 43px;">
            @php
                $attr = \App\Attraction::with('translations')->whereSlug('eiffel-tower')->first();
            @endphp
            <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attr, $language))}}" class="link-btn">{{__('allEiffelTowerTours')}}</a>
        </div>
    </div>
    </div>
    <div class="col-lg-12 hidden-xs hidden-sm hidden-md" style="background-color: white;height: 340px;">
        <div class="col-lg-3" style="left: 0px;">
            <img src="https://cityzore.s3.eu-central-1.amazonaws.com/website-images/12047318_m.jpg"
                 style="height: 340px;float: right;left: 0px;">
        </div>
        <div class="col-lg-9" style="background-color: aliceblue; min-height: 340px;">
            <div class="row" style="margin-top: 4%;">
                    <span
                        style="font-size:30px;text-align: center;position: absolute;border: 1px solid white;border-radius: 5%;border-radius: 10px;background-color: white;padding: 1%;">
                        {{__('discoverTheExperiences')}}
                    </span>
            </div>
            <div class="container" style="margin-top: 10%;width: 75%;text-align: center;">
                <?php $i = 1; ?>
                @foreach($attractionModel->where('isActive', 1)->take(18)->get() as $attraction)
                    @if($attraction->translations)
                        @php
                            $attractionTranslation = $attraction->translations;
                        @endphp
                        <span class="tag-span"
                              style="background-color: #1d6db2;color: white;font-weight: 400;padding: 8px;margin-right: -5px;margin-bottom: 10px;">{{$i}}</span>
                        <a class="nav-item nav-link attraction-tag" target="blank"
                           href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}"
                           style="line-height: 60px;padding: 7px;margin-bottom: 50px;border-radius: 0px;border: 1px solid #1d6db2;background-color:white;color:black;display: initial;"><span>{{$attractionTranslation->name}}</span></a>
                    @else
                        <span style="white-space: nowrap;">
                                    <span class="tag-span"
                                          style="background-color: #1d6db2;color: white;font-weight: 400;padding: 8px;margin-right: -5px;margin-bottom: 10px;">{{$i}}</span>
                                <a class="nav-item nav-link attraction-tag" target="blank"
                                   href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}"
                                   style="line-height: 60px;padding: 7px;margin-bottom: 50px;border-radius: 0px;border: 1px solid #1d6db2;background-color:white;color:black;display: initial;"><span
                                        class="home-button-span">{{$attraction->name}}</span></a>
                                </span>
                    @endif
                    <?php $i++; ?>
                @endforeach
            </div>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'home'])
