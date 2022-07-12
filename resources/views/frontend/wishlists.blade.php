@include('frontend-partials.head', ['page' => 'wishlists'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$currencyIcon = session()->get('currencyIcon');
$productModel = new \App\Product();
$languageModel = new \App\Language();
$productGalleryModel = new \App\ProductGallery();
$productTranslationModel = new \App\ProductTranslation();
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <div class="db">
        @include('layouts.profile-sidebar.sidebar-left')
        <div class="col-lg-9">
            <div class="db-2-com db-2-main" style="background-color: white;">
                <h4>{{__('myWishlist')}}</h4>
                <div class="db-2-main-com db-2-main-com-table">
                    <div class="row">
                        @foreach($wishlists as $wishlist)
                            <?php
                            $language = $languageModel::where('code', $langCode)->first();
                            $productTranslation = $productTranslationModel::where('productID', $wishlist->product->id)->where('languageID', $language->id)
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
                            <input type="hidden" id="productID" value="{{$wishlist->product->id}}">
                            <div class="col-md-3 col-sm-6 col-xs-12 b_packages wow slideInUp" data-wow-duration="0.5s">
                                @if(!($commonFunctions->getOfferPercentage($wishlist->product) == 0))
                                    <div class="band">
                                        <div class="ribbon ribbon--orange" style="margin-top: 0px">% {{$commonFunctions->getOfferPercentage($wishlist->product)}}</div>
                                    </div>
                                @endif
                                <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$wishlist->product->url)}} @endif">
                                    <div class="v_place_img">
                                        <img class="mobile-image" style="height:160px;padding: 2%;"
                                             @if ($wishlist->product->ProductGalleries()->where('product_id', '=', $wishlist->product->id)->where('id', '=', $wishlist->product->coverPhoto)->first())
                                             src="{{Storage::disk('s3')->url('product-images-xs/' . $wishlist->product->ProductGalleries()->where('product_id', '=', $wishlist->product->id)->where('id', '=', $wishlist->product->coverPhoto)->first()->src)}}"
                                             @else
                                             src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                                             @endif
                                             @if ($productTranslation)
                                             alt="{{$productTranslation->title}}"
                                             @else
                                             alt="{{$wishlist->product->title}}"
                                             @endif
                                             title="Tour Booking" />
                                    </div>
                                </a>
                                <div class="b_pack rows" style="min-height: 240px;max-height: 240px;">
                                    <div class="row" style="height: 87px; margin-left: 2%;">
                                        <div class="col-md-12">
                                            <h3>
                                                @if ($productTranslation)
                                                    <a href="{{url($langCodeForUrl.'/'.$productTranslation->url)}}" style="font-size: 15px;font-weight: 500;color: #1A2B50">{{$productTranslation->title}}</a>
                                                @else
                                                    <a href="{{url($langCodeForUrl.'/'.$wishlist->product->url)}}" style="font-size: 15px;font-weight: 500;color: #1A2B50">{{$wishlist->product->title}}</a>
                                                @endif
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row" style="height: 30px;">
                                                <div class="rating" style="margin-left: 10%;">
                                                    @if(!($wishlist->product->rate==null))
                                                        @for($i=1;$i<=$wishlist->product->rate;$i++)
                                                            <i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>
                                                        @endfor
                                                        <label style="font-size: 13px;vertical-align: text-bottom; float:left;color: #1A2B50;">| ({{$wishlist->product->rate}}/5) </label>
                                                        <p><span></span></p>
                                                    @else
                                                        <div style="font-size: 13px;vertical-align: text-bottom;padding-top: 3%;">No reviews yet</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row container">
                                            <div class="hidden-lg hidden-md hidden-sm col-xs-12 mobile_desc">
                                                {!! html_entity_decode(substr($wishlist->product->shortDesc, 0, 100)) !!} ...
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mobile_from">
                                                <h4 style="font-size:13px;">{{__('from')}}
                                                    <?php $specialOffer = $commonFunctions->getOfferPercentage($wishlist->product) ?>
                                                    @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                        @if($specialOffer != 0)
                                                            <?php
                                                            $specialOfferPrice = round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id)) - (($commonFunctions->getMinPrice($wishlist->product->id)) * ($commonFunctions->getOfferPercentage($wishlist->product)) / 100),2);
                                                            ?>
                                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{$currencyIcon}}"></i>{{$specialOfferPrice}}</span>
                                                            <i class="{{$currencyIcon}}"></i><span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id))}}</span>
                                                            <?php
                                                            $commissionerEarns = $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($wishlist->product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                                            ?>
                                                        @else
                                                            <i class="{{$currencyIcon}}"></i>{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id))}}
                                                            <?php
                                                            $commissionerEarns = $currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id)) - $currencyModel::calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($wishlist->product, auth()->guard('web')->user()->id)));
                                                            ?>
                                                        @endif
                                                        <i class="{{$currencyIcon}}"></i>{{$commissionerEarns}} COM
                                                    @else
                                                        @if($specialOffer != 0)
                                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{$currencyIcon}}"></i>{{round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id)) - (($commonFunctions->getMinPrice($wishlist->product->id)) * ($commonFunctions->getOfferPercentage($wishlist->product)) / 100),2)}}</span>
                                                            <i class="{{$currencyIcon}}"></i><span class="strikeout">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id))}}</span>
                                                        @else
                                                            <i class="{{$currencyIcon}}" style="color: #f4364f;"></i><span style="color:#f4364f;font-size: 18px">{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($wishlist->product->id))}}</span>
                                                        @endif
                                                    @endif
                                                </h4>
                                            </div>
                                            <div>
                                                <button id="addRemoveWishlist" class="btn btn-primary btn-block" style="margin-top: 10px;" data-type="remove">{{__('removeWishlist')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'wishlists'])
