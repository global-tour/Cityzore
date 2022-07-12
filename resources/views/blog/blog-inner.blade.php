@include('frontend-partials.head', ['page' => 'blog-inner'])
@include('frontend-partials.header', ['page' => 'blog-inner'])
<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$currencyIcon = session()->get('currencyIcon');
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$language = \App\Language::where('code', $langCode)->first();
$langID = $language->id;
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;


if ($langCode == "en") {

    $url = $blogPost->url;
    $title = $blogPost->title;
    $postContent = $blogPost->postContent;

    $categoryName = $category[0]['categoryName'];

} else {
    if (!empty($blogPosts->translations)) {
        $url = $blogPost->translations->url;
        $title = $blogPost->translations->title;
        $postContent = $blogPost->translations->postContent;

    } else {
        $url = $blogPost->url;
        $title = $blogPost->title;
        $postContent = $blogPost->postContent;
    }


    $categoryName = $category[0]->translations['categoryName'];
}
?>
<div class="container">

    <div class="row">
        <input type="hidden" class="isBlogDetailPage" name="isBlogDetailPage" value="1">

        <div class="col-md-10">
            <h1>{{$title}}</h1>
            <p style="color: #676464;">{{date('D, d-F-Y', strtotime($blogPost->created_at))}}
                // {{$categoryName}}</p>
            <img style="width: 100%;max-height: 500px;border-radius: 5px;padding-bottom: 1%;" id="blah"
                 src="{{Storage::disk('s3')->url('blog/' . \App\BlogGallery::findOrFail($blogPost->coverPhoto)->src)}}"/>
            <div class="row" style="position: relative">
                <div class="col-md-2" style="position: sticky; top: 70px">
                    <div class="blog-sidenav hidden-xs hidden-sm">
                        <p style="text-align: center;">Share</p>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
                            <img style="width: 50px;" src="{{asset('/img/facebook2.png')}}">
                        </a>
                        <a target="_blank" href="https://twitter.com/share?url={{Request::fullUrl()}}"
                           class="twitter-share-button" data-count="vertical">
                            <img style="width: 50px;" src="{{asset('/img/twitter2.png')}}">
                        </a>
                        <a target="_blank" href="https://api.whatsapp.com/send?text={{Request::fullUrl()}}">
                            <img style="width: 50px;" src="{{asset('/img/whatsapp2.png')}}">
                        </a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="blog-main hidden-xs">
                        <span>
                            {!! html_entity_decode($postContent) !!}
                        </span>
                    </div>
                    <div class="mobile-content visible-xs">
                        <span>
                            {!! html_entity_decode($postContent) !!}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 hidden-sm hidden-xs">
            <div class="right-sidebar">
                <h3>Recent Post</h3>
                @foreach($recentPost as $blogPosts)

                    @php
                        $lc = $langCodeForUrl;
                               if($langCode == "en"){

                        $url = $blogPosts->url;
                        $title = $blogPosts->title;
                        $postContent = $blogPosts->postContent;

                        }else{

                            if(!empty($blogPosts->translations)){
                            $url = $blogPosts->translations->url;
                            $title = $blogPosts->translations->title;
                            $postContent = $blogPosts->translations->postContent;

                            }else{
                                $lc = '';
                                $url = $blogPosts->url;
                                $title = $blogPosts->title;
                                $postContent = $blogPosts->postContent;
                            }

                        }
                    @endphp


                    <div class="recent-post" style="border-bottom: 1px dotted gray;margin-bottom: 1%;">
                        <a href="{{url($lc.'/blog'.$url)}}">
                            <h2 class="title" style="font-size: 17px;padding: 0px;color:#333;">{{$title}}</h2>
                        </a>
                        <img
                            src="{{Storage::disk('s3')->url('blog/' . \App\BlogGallery::findOrFail($blogPosts->coverPhoto)->src)}}"
                            alt="{{\App\BlogGallery::findOrFail($blogPosts->coverPhoto)->alt}}"
                            style="max-height: 130px">
                        <p style="color: #676464;">{{date('D, d-F-Y', strtotime($blogPosts->created_at))}}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12 hidden-xs" style="padding: 0px;">
            <h3 style="background-image: linear-gradient(to right, #e8e8e8, white);font-weight: 400">Top Tours</h3>
            @php
                $topProds = \App\Product::with(['translations'])->where('attractions', 'like','%"'.$blogPost->attraction.'"%')->where('isPublished',1)->where('isDraft', 0)->where('isSpecial', 0)->take(4)->get();
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
    </div>
</div>
<div class="col-md-12">
    @include('frontend-partials.footer')
</div>

@include('frontend-partials.general-scripts', ['page' => 'blog-inner'])
