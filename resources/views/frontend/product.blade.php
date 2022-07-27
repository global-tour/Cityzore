@include('frontend-partials.head', ['page' => 'product'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$currencyModel = new \App\Currency();
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$language = App\Language::where('code', $langCode)->first();
$attractionTranslationModel = new \App\AttractionTranslation();

?>
<input type="hidden" class="isProductPage" value="1">
<input type="hidden" class="maxAvdate" value="">
<input type="hidden" id="getAvailableDatesIterator" value="0">
<input type="hidden" id="cartRouteLocalization" value="{{$commonFunctions->getRouteLocalization('cart')}}">
<input type="hidden" id="langHidden" value="{{$language->id}}">

<section>
    <div class="rows banner_book" id="inner-page-title">
        <div style="margin-right: 10%; margin-left: 10%;">
            <div class="banner_book_1 hidden-lg hidden-md hidden-sm">
                <ul>
                    <li class="dl1">{{__('location')}} :
                        {{$product->countryName->countries_name}}
                    </li>
                    <?php
                    $isCommissioner = auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1';
                    ?>
                    <li class="dl2" @if($isCommissioner) style="font-size: 14px;" @endif>{{__('price')}} :
                        <?php $specialOffer = $commonFunctions->getOfferPercentage($product); ?>
                        @if($isCommissioner)
                            <?php

                            if($product->supplierID != -1){
                                $supp = \App\Supplier::findOrFail($product->supplierID);
                                if($supp->commissioner_commission){
                                    $commission = $supp->commissioner_commission;
                                }else{
                                    $commission = auth()->guard('web')->user()->commission;
                                }

                            }else{
                               $commission = auth()->guard('web')->user()->commission;
                            }

                            $commissionType = auth()->guard('web')->user()->commissionType ?? 'percentage';

                            ?>
                            @if($specialOffer != 0)
                                <?php
                                $specialOfferPrice = round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - (($commonFunctions->getMinPrice($product->id)) * $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getOfferPercentage($product))) / 100), 2);
                                ?>
                                <span style="font-size: 14px;" class="special-offer-price"><i class="{{session()->get('currencyIcon')}}"></i>{{$specialOfferPrice}}</span>
                                <i class="{{session()->get('currencyIcon')}}"></i><span style="font-size: 14px;" class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                                <?php
                                $commissionerEarns = $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                ?>
                                <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                            @else
                                <i class="{{session()->get('currencyIcon')}}"></i>{{round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)), 2)}}
                                <?php
                                $commissionerEarns = $currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                ?>
                                <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                            @endif
                            <input type="hidden" value="{{$commission}}" id="commissionerEarns">
                            <input type="hidden" value="{{$commissionType}}" id="commissionerEarnsType">
                        @else
                            @if($specialOffer != 0)
                                <span class="special-offer-price"><i class="{{session()->get('currencyIcon')}}"></i>{{round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - ( $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getMinPrice($product->id))) * ($commonFunctions->getOfferPercentage($product)) / 100), 2)}}</span>
                                <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                            @else
                                <i class="{{session()->get('currencyIcon')}}"></i><span style="font-size: 15px">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                            @endif
                        @endif
                    </li>
                    <li class="dl3">Duration : {{$options[0]['tourDuration']}} @if($options[0]['tourDurationDate']=='m') Minute(s) @elseif($options[0]['tourDurationDate']=='h') Hour(s) @elseif($options[0]['tourDurationDate']=='d') Day(s) @endif</li>
                    <li class="dl4 hidden-xs"><a href="#">Book Now</a> </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="product-page" style="margin-left: 10%; margin-right: 10%;border-top: 1px solid #e8e9ec;">
        <div class="wrapper">
            <div class="main">
                <div class="tour_head">
                    @if(!($product->rate == null))
                        <span class="tour_rat">{{number_format((float)(round($product->rate, 1)), 1, '.', '')}}/5 </span>
                    @endif
                    <span class="tour_rat">{{count($comments)}} {{__('review')}}</span>
                    <span>|
                        {{$product->countryName->countries_name}}, {{$product->city}}
                    </span>
                    <span>|
                        {{__('duration')}} : {{$options[0]['tourDuration']}} @if($options[0]['tourDurationDate']=='m') {{__('minutes')}} @elseif($options[0]['tourDurationDate']=='h') {{__('hours')}} @elseif($options[0]['tourDurationDate']=='d') {{__('days')}} @endif
                    </span>
                    <h1 style="margin-top: 1%;">
                        @if($productTranslation)
                            {{$productTranslation->title}}
                        @else
                            {{$product->title}}
                        @endif
                    </h1>
                </div>

                <div class="col-lg-12 tour_head1 hotel-book-room" style="padding: 0px;">
                    <div class="band hidden-xs">

                        <div class="ribbon-product">
                            <span class="ribbon3">
                                <li class="dl2" style="font-size: 18px;color: white;" >{{__('from')}}
                                    <?php $specialOffer = $commonFunctions->getOfferPercentage($product); ?>
                                    @if($isCommissioner)
                                        <?php
                                        $commission = auth()->guard('web')->user()->commission;
                                        ?>
                                        @if($specialOffer != 0)
                                            <?php
                                            $specialOfferPrice = round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - ($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) * ($commonFunctions->getOfferPercentage($product)) / 100), 2);
                                            ?>
                                            <span style="font-size: 14px;" class="special-offer-price"><i class="{{session()->get('currencyIcon')}}"></i>{{number_format($specialOfferPrice, 2, '.', '')}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span style="font-size: 14px;" class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                                            <?php
                                            $commissionerEarns = $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                            ?>
                                            <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i>{{round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)), 2)}}
                                            <?php
                                            $commissionerEarns = $currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                            ?>
                                            <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                        @endif
                                        <input type="hidden" value="{{$commission}}" id="commissionerEarns">
                                    @else
                                        @if($specialOffer != 0)
                                            @php
                                                $specialOfferPrice = round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - ($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) * ($commonFunctions->getOfferPercentage($product)) / 100), 2);
                                            @endphp
                                            <span class="special-offer-price"><i class="{{session()->get('currencyIcon')}}"></i>{{number_format($specialOfferPrice, 2, '.', '')}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i><span style="font-size: 20px">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                                        @endif
                                    @endif
                            </li>
                            </span>
                        </div>
                    </div>
                    <div class="gallery">
                        <?php $count = 1; ?>
                        @foreach($productImages as $productImage)
                        @if($count < 5)
                            <figure class="gallery__item gallery__item--<?php echo $count ?>">
                                <a data-lightbox="gallery" href="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}"><img style="width: 100%;height: 100%" class="gallery__img" src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" alt="{{$productImage->alt}}"></a>
                            </figure>
                            @else

                            <figure class="gallery__item gallery__item--<?php echo $count ?>" style="display: none;">
                                <a data-lightbox="gallery" href="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}"><img style="width: 100%;height: 100%" class="gallery__img" src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" alt="{{$productImage->alt}}"></a>
                            </figure>

                            @endif
                                <?php $count++; ?>

                        @endforeach
                            <button class="hidden-xs all-images"><i class="icon-cz-picture"></i>{{__('viewAllPhotos')}}</button>
                    </div>



                   <div class="col-lg-12">
                    <?php
                       $productSkills = $commonFunctions->getProductSkills($product);
                   ?>
                    <div class="col-lg-8 col-xs-12" style="margin-top: 1%;margin-bottom: -9px;padding-left: 0px;">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-xs-12" style="padding: 0px;">
                                <div style="padding: 5%;font-size: 15px;background-color: #ffad0c59;"><i class="icon-cz-mobile" style="padding-right: 1%;"></i>{{__('mobileTicket')}}</div>
                            </div>
                        @if(array_key_exists("isSkipTheLine", $productSkills))
                            <div class="col-lg-3 col-sm-6 col-xs-12" style="padding: 0px;">
                                <div style="padding: 5%;font-size: 15px;background-color: #ffad0c8c;"><i class="icon-cz-add-time" style="padding-right: 1%;"></i>{{__('skipTheLineTickets')}}</div>
                            </div>
                        @endif
                       <div class="col-lg-3 col-sm-6 col-xs-12" style="padding: 0px;">
                            <div style="padding: 5%;font-size: 15px;background-color: #ffad0cc7;"><i class="icon-cz-hour" style="padding-right: 1%;"></i>{{__('tourDuration')}}: {{$options[0]['tourDuration']}} @if($options[0]['tourDurationDate']=='m') {{__('minutes')}} @elseif($options[0]['tourDurationDate']=='h') {{__('hours')}} @elseif($options[0]['tourDurationDate']=='d') {{__('days')}} @endif</div>
                        </div>
                        @if(array_key_exists("guideInformation", $productSkills))
                           <div class="col-lg-3 col-sm-6 col-xs-12" style="padding: 0px;">
                                <div style="padding: 5%;font-size: 15px;background-color: #ffad0c;"><i class="icon-cz-logs" style="padding-right: 1%;"></i>{{implode(", ",$productSkills["guideInformation"])}}</div>
                           </div>
                        @endif
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-12 product-whishlist" style="margin-top: 1%; justify-content: flex-start; display: flex; flex-direction: row-reverse;">
                        @if(is_null($wishlist))
                            <button data-type="add" class="btn btn-primary" id="addRemoveWishlist" style="background-color: #1d6db2;">
                                {{__('addWishlist')}}
                            </button>
                        @else
                            <button data-type="remove" class="btn btn-primary" id="addRemoveWishlist">
                                {{__('removeWishlist')}}
                            </button>
                        @endif
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank" style="">
                            <img style="width: 40px;" src="{{asset('/img/facebook2.png')}}" alt="facebook">
                        </a>
                        <a target="_blank" href="https://twitter.com/share?url={{Request::fullUrl()}}" class="twitter-share-button" data-count="vertical" style="">
                            <img style="width: 40px;" src="{{asset('/img/twitter2.png')}}" alt="twitter">
                        </a>
                        <a target="_blank" href="https://api.whatsapp.com/send?text={{Request::fullUrl()}}" style="">
                            <img style="width: 40px;" src="{{asset('/img/whatsapp2.png')}}" alt="whatsapp">
                        </a>
                    </div>
                    </div>


                    <!-- <div id="myCarousel1" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner carousel-inner1" role="listbox">
                            <?php $count = 0; ?>
                            @foreach($productImages as $productImage)
                                <div style="width: 100%;height: 400px" class="item @if($count==0) active @endif">
                                    <img style="width: 100%;height: 100%" src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" alt="{{$productImage->alt}}">
                                </div>
                                <?php $count++; ?>
                            @endforeach
                        </div>
                        <a class="left carousel-control" href="#myCarousel1" role="button" data-slide="prev"> <span><i style="font-size:40px;background: none" class="icon-cz-angle-left hotel-gal-arr" aria-hidden="true"></i></span> </a>
                        <a class="right carousel-control" href="#myCarousel1" role="button" data-slide="next"> <span><i style="font-size:40px;background: none" class="icon-cz-angle-right hotel-gal-arr " aria-hidden="true"></i></span> </a>
                    </div> -->
                </div>
                <!-- <div class=" col-lg-12 shareOnSocial">
                    <div class="pull-right">
                        @if(is_null($wishlist))
                            <button data-type="add" class="btn btn-primary" id="addRemoveWishlist">
                                {{__('addWishlist')}}
                            </button>
                        @else
                            <button data-type="remove" class="btn btn-primary" id="addRemoveWishlist">
                                {{__('removeWishlist')}}
                            </button>
                        @endif
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
                            <img style="width: 50px;" src="{{asset('/img/facebook2.png')}}" alt="facebook">
                        </a>
                        <a target="_blank" href="https://twitter.com/share?url={{Request::fullUrl()}}" class="twitter-share-button" data-count="vertical">
                            <img style="width: 50px;" src="{{asset('/img/twitter2.png')}}" alt="twitter">
                        </a>
                        <a target="_blank" href="https://api.whatsapp.com/send?text={{Request::fullUrl()}}">
                            <img style="width: 50px;" src="{{asset('/img/whatsapp2.png')}}" alt="whatsapp">
                        </a>
                    </div>
                </div> -->
                <div class="row">
                <div class="col-lg-8 col-xs-12" style="padding-top: 1%;">
                    <p class="productContentTitles">{{__('highlights')}}</p>
                    <ul style="padding-left: 0px;">
                        @if($productTranslation)
                            @for($i=0;$i<count(explode('|', $productTranslation->highlights));$i++)
                                <p><span>&#183;&nbsp</span>{{explode('|', $productTranslation->highlights)[$i]}}</p>
                            @endfor
                        @else
                            @for($i=0;$i<count(explode('|', $product->highlights));$i++)
                                <p>&#183;&nbsp{{explode('|', $product->highlights)[$i]}}</p>
                            @endfor
                        @endif
                    </ul>
                </div>
                <div class="col-lg-4 col-xs-12 hidden-xs hidden-sm">
                    <div class="gradient-border" id="box" style="margin-top: 10%;margin-right: 20%;">
                        <div class="product-right-book">
                            <span style="font-size: 25px;">{{__('from')}}
                                <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                    @if($specialOffer != 0)
                                        <?php
                                        $specialOfferPrice = round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - $currencyModel->calculateCurrencyForVisitor((($commonFunctions->getMinPrice($product->id))) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                        ?>
                                        <span class="special-offer-price" style="font-size: 25px;color: #ffad0c;">{{$specialOfferPrice}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                        <br>
                                        <span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                        <?php
                                        $commissionerEarns = $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                        ?>
                                    @else
                                        <i class="{{session()->get('currencyIcon')}}"></i>{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}
                                        <?php
                                        $commissionerEarns = $currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                        ?>
                                    @endif
                                    <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                @else
                                    @if($specialOffer != 0)

                                        <span class="special-offer-price" style="font-size: 25px;color: #ffad0c;">{{round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - (  $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getMinPrice($product->id))) * ($commonFunctions->getOfferPercentage($product)) / 100),2)}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                        <br>
                                        <span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                    @else
                                        <span style="color:#ffad0c;font-size: 27px">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}<i class="{{session()->get('currencyIcon')}}" style="color: #ffad0c;"></i></span>
                                    @endif
                                @endif
                        </span><br><br>
                            <button>Book Now</button>
                        </div>
                    </div>
                </div>
            </div> <!--end of row-->

            <div class="row">
                <div class="col-lg-8 col-md-8 col-xs-12">
                    <p class="productContentTitles">{{__('description')}}</p>
                    @if($productTranslation)
                        <p>{!! html_entity_decode($productTranslation->shortDesc) !!}</p>
                        <p><a onclick="javascript:ShowHide('HiddenDiv')">{{__('readMore')}}</a></p>
                        <div class="mid" id="HiddenDiv" style="display: none;">
                            {!! html_entity_decode($productTranslation->fullDesc) !!}
                        </div>
                    @else
                        <p>{!! html_entity_decode($product->shortDesc) !!}</p>
                        <p><a onclick="javascript:ShowHide('HiddenDiv')">{{__('readMore')}}</a></p>
                        <div class="mid" id="HiddenDiv" style="display: none;">
                            {!! html_entity_decode($product->fullDesc) !!}
                        </div>
                    @endif
                </div>
            </div> <!--end of row-->

                <div class="icon-float" style="background-color: transparent;text-align: center;">
                    <button class="fixed-book hidden-lg hidden-md hidden-sm" id="book-focus">{{__('bookNow')}}</button>
                </div>
                <div class="col-lg-8 hidden-xs" style="margin-top: 5%;padding-left: 0px;">
                    @for($i=0; $i<count($options); $i++)
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" onclick="changeOptionCard({{$options[$i]->id}})" style="padding-left: 0px;">
                            <ul style="padding-left: 0px !important;">
                                <li class="option optionMovable" id="movable{{$options[$i]->id}}" style="border-radius: 10px;">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                            <ul>
                                                <?php
                                                $optionTranslation = App\OptionTranslation::where('optionID', $options[$i]->id)->where('languageID', $language->id)
                                                    ->where(function($query) {
                                                        $query->where('title', '!=', null)
                                                            ->where('description', '!=', null);
                                                    })->first();
                                                ?>
                                                @if($optionTranslation)
                                                    <li class="name">
                                                        <h2 style="font-size: 20px;color:#253d52">{{$optionTranslation->title}}</h2>

                                                    </li>
                                                @else
                                                    <li class="name">
                                                        <h2 style="font-size: 20px;color:#253d52">{{$options[$i]->title}}</h2>
                                                    </li>
                                                @endif

                                                @if($optionTranslation)
                                                        <li class="description">
                                                            <p style="color:#253d52;">
                                                                {{$optionTranslation->description}}
                                                            </p>
                                                        </li>
                                                @else
                                                        <li class="description">
                                                            <p style="color:#253d52;">
                                                                {{$options[$i]->description}}
                                                            </p>
                                                        </li>
                                                @endif
                                                <br> <p style="color:#253d52">{{__('tourDuration')}}: {{$options[$i]->tourDuration}} @if($options[$i]->tourDurationDate == 'm') {{__('minutes')}} @elseif($options[$i]->tourDurationDate == 'h') {{__('hours')}} @elseif($options[$i]->tourDurationDate == 'd') {{__('days')}} @endif</p>
                                            </ul>
                                        </div>
                                        <div class="col-lg-3" style="padding: 0px;margin-top: 3%;">
                                            <ul style="padding-left: 0px;">
                                                @php
                                                    $specialOfferForThisOption = $commonFunctions->getOfferPercentageForSpecificOption($product, $options[$i]);
                                                @endphp
                                                <li>
                                                    @if($specialOfferForThisOption)
                                                    <p style="font-size:20px;font-weight:bold;color: #1a1818;">

                                                        <span style="color:#ffad0c;font-size:20px;font-weight:bold;white-space: nowrap;">
                                                            <i class="{{session()->get('currencyIcon')}}"></i>
                                                            {{number_format($currencyModel->calculateCurrencyForVisitor($options[$i]['price']) - $currencyModel->calculateCurrencyForVisitor($options[$i]['price'])*((int)$specialOfferForThisOption/100), 2, '.', '')}}
                                                        </span>

                                                        <span style="color:#8a8181;font-size:20px;font-weight:bold;white-space: nowrap;">
                                                            <i class="{{session()->get('currencyIcon')}}"></i>
                                                            <span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($options[$i]['price'])}}</span>
                                                        </span>
                                                    </p>
                                                    @else

                                                      <p style="font-size:20px;font-weight:bold;color: #1a1818;">
                                                        <span style="color:#ffad0c;font-size:20px;font-weight:bold;white-space: nowrap;">
                                                            <i class="{{session()->get('currencyIcon')}}"></i>
                                                            {{number_format($currencyModel->calculateCurrencyForVisitor($options[$i]['price']), 2, '.', '')}}
                                                        </span>
                                                    </p>

                                                    @endif
                                                </li>
                                                <li>
                                                    <button class="book-now btn" id="book-now">
                                                        Select
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endfor
                        <div class="col-lg-12 col-xs-12 tour_head1" style="padding-left: 0px">
                            <p class="productContentTitles">{{__('includes')}}</p>
                            <div id="incNotIncBlock">
                            @if($productTranslation)
                                @for($i=0;$i<count(explode('|', $productTranslation->included));$i++)
                                    <p><span style="color: green">&#10004;&nbsp</span>{{explode('|', $productTranslation->included)[$i]}}</p>
                                @endfor
                            @else
                                @for($i=0;$i<count(explode('|', $product->included));$i++)
                                    <p><span style="color: green">&#10004;&nbsp</span>{{explode('|', $product->included)[$i]}}</p>
                                @endfor
                            @endif
                            @if($productTranslation)
                                @for($i=0;$i<count(explode('|', $productTranslation->notIncluded));$i++)
                                    <p><span style="color: red">&#10008;&nbsp</span>{{explode('|', $productTranslation->notIncluded)[$i]}}</p>
                                @endfor
                            @else
                                @for($i=0;$i<count(explode('|', $product->notIncluded));$i++)
                                    <p><span style="color: red">&#10008;&nbsp</span>{{explode('|', $product->notIncluded)[$i]}}</p>
                                @endfor
                            @endif
                            </div>
                        </div>
                        <div class="col-lg-12 col-xs-12 tour_head1" id="know-before-you-go-wrap" style="padding-left: 0px;">
                            <p class="productContentTitles">{{__('knowBeforeYouGo')}}</p>
                            <div id="knowBeforeYouGoBlock">
                            @if($productTranslation)
                                @for($i=0;$i<count(explode('|', $productTranslation->knowBeforeYouGo));$i++)
                                    @if($i < 3)
                                        <p><span>&#9864;&nbsp</span>{{explode('|', $productTranslation->knowBeforeYouGo)[$i]}}</p>
                                    @else
                                        <p class="read-more-p" style="display: none;"><span>&#9864;&nbsp</span>{{explode('|', $productTranslation->knowBeforeYouGo)[$i]}}</p>
                                    @endif
                                @endfor
                            @else
                                @for($i=0;$i<count(explode('|', $product->knowBeforeYouGo));$i++)
                                    @if($i < 3)
                                        <p><span>&#9864;&nbsp</span>{{explode('|', $product->knowBeforeYouGo)[$i]}}</p>
                                    @else
                                        <p class="read-more-p" style="display: none;"><span>&#9864;&nbsp</span>{{explode('|', $product->knowBeforeYouGo)[$i]}}</p>
                                    @endif
                                @endfor
                            @endif
                            </div>
                        </div>
                </div>
<div class="col-lg-4" style="padding: 0px;">
        <div class="tour_r" id="datepickerProduct" style="float: right;">
            <div id="box_style_1" class="box_style_1" style="margin-top: 5%;">
                <p id="alert-special-offer" class="text-success">

                </p>
                <div style="margin-bottom: 100px">
                    <span class="cartSuccessSpan successSpan col s12" style="display: none!important; color: #0f9d58;">{{__('product3')}}</span>
                    <span class="cartErrorSpan errorSpan col s12" style="display: none!important; color: darkred;">{{__('product4')}}</span>
                    <table class="table table_summary">
                        <tbody class="find">
                        <tr class="hidden-lg hidden-md hidden-sm">
                            <td colspan="2">
                                <div class="input-field col s12">
                                    <ul>
                                        @foreach($options as $i => $o)
                                            <?php
                                            $optionTranslation = App\OptionTranslation::where('optionID', $o->id)->where('languageID', $language->id)->first();
                                            ?>

                                            <li>
                                                <a href="#">
                                                    <input type="radio" name="pr_opt" @if($i==0)  checked @endif id="productOption-{{$o->id}}"  class="productOption" value="{{$o->id}}" data-title="@if($optionTranslation) {{$optionTranslation->title}} @else {{$o->title}}  @endif">
                                                    @if($optionTranslation)
                                                        <label style="top:0; font-size: 14px; color: #000;position: inherit !important;" for="productOption-{{$o->id}}">{{$optionTranslation->title}}</label>
                                                    @else
                                                        <label style="top:0; font-size: 14px; color: #000;position: inherit !important;" for="productOption-{{$o->id}}">{{$o->title}}</label>
                                                    @endif
                                                </a>
                                            </li>

                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="choose-person-count row" style="display: none;">
                                    @foreach($options as $ind => $option)
                                        <input type="hidden" value="{{json_encode($ignoredCategories[$ind])}}" id="ignoredCategories{{$option->id}}" />
                                    @endforeach
                                    <input type="hidden" value="1" id="ticketCount" />
                                    <input type="hidden" value="" id="disabledDates" />
                                    <input type="hidden" value="" id="selectedDate" />
                                    <input type="hidden" value="" id="availabilityType" />
                                    <input type="hidden" value="{{$translationArray}}" id="translationArray" />
                                    <div class="none col-lg-12 col-md-12"  id="bank">
                                        <div class="col-lg-6 col-xs-6">
                                            <div id="adultDiv">
                                                <div class="row" id="adultSpan">
                                                    {{__('adult')}} <span id="adultAgeSpan" style="font-size: 12px;"></span>
                                                </div>
                                                <div class="row">
                                                    <div class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                        <input value="1" name="adultCount" type="text" id="adultInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                        <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xs-6">
                                            <div id="youthDiv">
                                                <div class="row" id="youthSpan" style="display: none;">
                                                    {{__('youth')}} <span id="youthAgeSpan" style="font-size: 12px;"></span>
                                                </div>
                                                <div class="row" id="youthDiv">
                                                    <div  class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                        <input value="0" type="text" id="youthInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                        <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xs-6">
                                            <div id="childDiv">
                                                <div class="row" id="childSpan" style="display: none;">
                                                    {{__('child')}} <span id="childAgeSpan" style="font-size: 12px;"></span>
                                                </div>
                                                <div class="row" id="childDiv">
                                                    <div  class="input-group number-spinner">
                                                                <span class="input-group-btn" style="border: none;">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn"><span>-</span></button>
                                                                </span>
                                                        <input value="0" type="text" id="childInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                        <span class="input-group-btn" style="border: none;">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up"><span>+</span></button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xs-6">
                                            <div id="infantDiv">
                                                <div class="row" id="infantSpan" style="display: none;">
                                                    {{__('infant')}} <span id="infantAgeSpan" style="font-size: 12px;"></span>
                                                </div>
                                                <div class="row" id="infantDiv">
                                                    <div  class="none input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                        <input value="0" type="text" id="infantInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                        <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xs-6">
                                            <div id="euCitizenDiv">
                                                <div class="row" id="euCitizenSpan" style="display: none;">
                                                    {{__('euCitizen')}} <span id="euCitizenAgeSpan" style="font-size: 12px;"></span>
                                                </div>
                                                <div class="row" id="euCitizenDiv">
                                                    <div  class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                        <input value="0" type="text" id="euCitizenInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                        <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-field col s12" id="divShow">
                                    <span>{{__('chooseADate')}}:</span>
                                    <div type='text' id="date" name="bookingDate" class="datepicker-here" data-language='{{$langCode}}' value="" >
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="dateErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('product5')}}</div>
                    <div id="maxPersonErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('exceededMaxPerson')}}</div>
                    <div id="minPersonErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('belowMin')}}</div>
                    <div class="option-name">

                    </div>
                    <hr>
                    <div class="choose-time row">

                    </div>
                    <hr>
                    <div id="priceInfoSection" style="display: none;color: #1a1818;">

                    </div>
                </div>
                <hr>
                <div>
                    <a id="bookNow" disabled class="btn_full" style="margin-top: 5%;">{{__('bookNow')}}</a>
                </div>
            </div>
        </div>
</div>
                <!-- <div class="col-lg-6 tour_r">
                    <div class="box_style_1" style="margin-top: 5%; width: 65%;margin-left: 10%;">
                        <p id="alert-special-offer" class="text-success">

                        </p>
                        <div style="margin-bottom: 100px">
                            <span class="cartSuccessSpan successSpan col s12" style="display: none!important; color: #0f9d58;">{{__('product3')}}</span>
                            <span class="cartErrorSpan errorSpan col s12" style="display: none!important; color: darkred;">{{__('product4')}}</span>
                            <table class="table table_summary">
                                <tbody>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-field col s12">
                                            <ul>
                                                @foreach($options as $i => $o)
                                                    <?php
                                                    $optionTranslation = App\OptionTranslation::where('optionID', $o->id)->where('languageID', $language->id)->first();
                                                    ?>

                                                    <li>
                                                        <a href="#">
                                                            <input type="radio" name="pr_opt" @if($i==0)  checked @endif id="productOption-{{$o->id}}"  class="productOption" value="{{$o->id}}" data-title="@if($optionTranslation) {{$optionTranslation->title}} @else {{$o->title}}  @endif">
                                                            @if($optionTranslation)
                                                                <label style="top:0; font-size: 14px; color: #000;" for="productOption-{{$o->id}}">{{$optionTranslation->title}}</label>
                                                            @else
                                                                <label style="top:0; font-size: 14px; color: #000;" for="productOption-{{$o->id}}">{{$o->title}}</label>
                                                            @endif
                                                        </a>
                                                    </li>

                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="choose-person-count row" style="display: none;">
                                            @foreach($options as $ind => $option)
                                                <input type="hidden" value="{{json_encode($ignoredCategories[$ind])}}" id="ignoredCategories{{$option->id}}" />
                                            @endforeach
                                            <input type="hidden" value="1" id="ticketCount" />
                                            <input type="hidden" value="" id="disabledDates" />
                                            <input type="hidden" value="" id="selectedDate" />
                                            <input type="hidden" value="" id="availabilityType" />
                                            <input type="hidden" value="{{$translationArray}}" id="translationArray" />
                                            <div class="none col-lg-12 col-md-12"  id="bank">
                                                <div class="col-lg-6 col-xs-6">
                                                    <div id="adultDiv">
                                                        <div class="row" id="adultSpan">
                                                            {{__('adult')}} <span id="adultAgeSpan" style="font-size: 12px;"></span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                                <input value="1" name="adultCount" type="text" id="adultInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-xs-6">
                                                    <div id="youthDiv">
                                                        <div class="row" id="youthSpan" style="display: none;">
                                                            {{__('youth')}} <span id="youthAgeSpan" style="font-size: 12px;"></span>
                                                        </div>
                                                        <div class="row" id="youthDiv">
                                                            <div  class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                                <input value="0" type="text" id="youthInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-xs-6">
                                                    <div id="childDiv">
                                                        <div class="row" id="childSpan" style="display: none;">
                                                            {{__('child')}} <span id="childAgeSpan" style="font-size: 12px;"></span>
                                                        </div>
                                                        <div class="row" id="childDiv">
                                                            <div  class="input-group number-spinner">
                                                                <span class="input-group-btn" style="border: none;">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn"><span>-</span></button>
                                                                </span>
                                                                <input value="0" type="text" id="childInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                                <span class="input-group-btn" style="border: none;">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up"><span>+</span></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-xs-6">
                                                    <div id="infantDiv">
                                                        <div class="row" id="infantSpan" style="display: none;">
                                                            {{__('infant')}} <span id="infantAgeSpan" style="font-size: 12px;"></span>
                                                        </div>
                                                        <div class="row" id="infantDiv">
                                                            <div  class="none input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                                <input value="0" type="text" id="infantInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-xs-6">
                                                    <div id="euCitizenDiv">
                                                        <div class="row" id="euCitizenSpan" style="display: none;">
                                                            {{__('euCitizen')}} <span id="euCitizenAgeSpan" style="font-size: 12px;"></span>
                                                        </div>
                                                        <div class="row" id="euCitizenDiv">
                                                            <div  class="input-group number-spinner" style="margin: 5%;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="dwn" style="padding: 0px 8px;"><span>-</span></button>
                                                                </span>
                                                                <input value="0" type="text" id="euCitizenInput" class="spinnerInputs form-control text-center" style="height: 26px;">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-default spinnerButtons" data-dir="up" style="padding: 0px 8px;"><span>+</span></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-field col s12" id="divShow">
                                            <span>{{__('chooseADate')}}:</span>
                                            <div type='text' id="date" name="bookingDate" class="datepicker-here" data-language='{{$langCode}}' value="" >
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div id="dateErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('product5')}}</div>
                            <div id="maxPersonErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('exceededMaxPerson')}}</div>
                            <div id="minPersonErrorSpan" style="padding:5px;font-size: 14px;background-color: #e57373;color: white; display:none;" class="col s12">{{__('belowMin')}}</div>
                            <div class="option-name">

                            </div>
                            <div class="choose-time row">

                            </div>
                            <hr>
                            <div id="priceInfoSection" style="display: none;color: #1a1818;">

                            </div>
                        </div>
                        <hr>
                        <div>
                            <a id="bookNow" disabled class="btn_full" style="margin-top: 5%;">{{__('bookNow')}}</a>
                        </div>
                    </div>
                </div> -->
             <div class="row">
                 <div class="col-lg-12 col-xs-12 tour_head1" style="width: 85%;">
                    <p class="productContentTitles">{{__('meetingPoint')}}</p>
                    <p>{{$options[0]->meetingPoint}}</p>
                    @if(!($options[0]->meetingComment==null))
                        <?php
                        $optionTranslation = App\OptionTranslation::where('optionID', $options[0]->id)->where('languageID', $language->id)->first();
                        ?>
                        @if($optionTranslation)
                            <p>
                                @for($m=0;$m<count(explode('|', $optionTranslation->meetingComment));$m++)
                                    -{{explode('|', $optionTranslation->meetingComment)[$m]}} <br>
                                @endfor
                            </p>
                        @else
                            @for($m=0;$m<count(explode('|', $options[0]->meetingComment));$m++)
                                -{{explode('|', $options[0]->meetingComment)[$m]}} <br>
                            @endfor
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 tour_head1 tout-map map-container" style="width: 85%;">
                    <p class="productContentTitles">{{__('cancelPolicy')}}</p>
                    @if($productTranslation)
                        <p>{{$productTranslation->cancelPolicy}}</p>
                    @else
                        <p>{{$product->cancelPolicy}}</p>
                    @endif
                </div>
              </div>
                @if (!is_null($product->productFiles))
                    <div>
                        <p class="productContentTitles">Necessary Files</p>
                        @foreach(json_decode($product->productFiles, true) as $file)
                            <p><a href="{{url('/downloadProductFile/'.$file)}}" target="_blank">{{$file}}</a></p>
                        @endforeach
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-12 tour_head1 tout-map map-container" style="width: 85%;">
                        <p class="productContentTitles">{{__('attractionsForThisTour')}}</p>
                    </div>
                        @foreach($productAttraction as $attraction)
                        <?php $attractionTranslation = $attractionTranslationModel->where('languageID', $language->id)->where('attractionID', $attraction->id)->first(); ?>
                            <div class="thumbex" style="margin: 10px 9px 30px;max-width: 300px;min-width: 240px;float:left">
                                <div class="thumbnail">
                                    @if($attractionTranslation)
                                        <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}" target="blank">
                                            <img src="{{Storage::disk('s3')->url('/attraction-images/' . $attraction->image)}}" alt="{{$attractionTranslation->name}}">
                                        <span>{{$attractionTranslation->name}}</span>
                                    @else
                                        <a href="{{url('attraction/'.$attraction->slug.'-'.$attraction->id)}}">
                                        <img src="{{Storage::disk('s3')->url('/attraction-images/' . $attraction->image)}}" alt="{{$attraction->name}}">
                                        <span>{{$attraction->name}}</span>
                                    @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                </div>
                <input id="productID" type="hidden" value="{{$product->id}}">
            </div>
        </div>
        <div>
            <div class="dir-rat">
                @if(!($product->rate == null))
                    <div class="dir-rat-inn dir-rat-review">
                        <p class="productContentTitles" style="background-image: linear-gradient(to right, #1d6db2, white);color: white;">{{__('customerReviews')}}</p>
                        @for($i=1;$i<=$product->rate;$i++)
                            <i class="icon-cz-star" style="color: #ffad0c; font-size: 25px;"></i>
                        @endfor
                        <span style="vertical-align: text-bottom;">{{round($product->rate, 1)}}/5</span>
                    </div>
                @endif
                <div class="dir-rat-inn dir-rat-review">
                    @foreach ($comments as $comment)
                        <div class="row commentDisplay" style="display: none; border-bottom: 1px solid #dcd9d9;margin-top: 1%;">
                            <div class="col-md-2 dir-rat-left">
                                <p style="background-color: #89b3d7; height: 25px; width: 25px;border-radius: 15px;color: white;margin-left: 45%;">{{substr($comment->username, 0, 1)}}</p>
                                <p>{{$comment->username}}<span>{{date('d-F-Y, H:i', strtotime($comment->created_at))}}</span> </p>
                            </div>
                            <div class="col-md-9 dir-rat-right">
                                <div class="dir-rat-star" style="font-size: 20px;">
                                    <div class="rating" style="direction: ltr;width:100%;">
                                        @for($i=1;$i<=$comment->rate;$i++)
                                            <i class="icon-cz-star" style="color: #ffad0c; font-size: 20px;"></i>
                                        @endfor
                                        <p style="float: left;">{{round($comment->rate, 1)}}/5</p>
                                    </div>
                                </div>
                                <p style="font-weight: bold;">{{$comment->title}}</p>
                                <p>{{$comment->description}}</p>
                            </div>
                        </div>
                    @endforeach
                    <a href="#" id="load" class="link-btn" style="text-align: center; margin-top: 1%;">{{__('loadMore')}}</a>
                </div>
                    <form class="dir-rat-form" method="GET" action="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('comment'))}}">
                        @csrf

                        <input type="hidden" id="recaptchaToken" name="recaptchaToken">
                        <input type="hidden" id="recaptchaAction" name="recaptchaAction">

                        @if(auth()->user())
                        <div class="dir-rat-inn dir-rat-title">
                            <p>{{__('writeYourRatingHere')}}</p>
                            <span id="ratingErrorSpan" class="label-danger" style="display:none;margin-left:20px;border-radius:5px;padding:6px 6px;color: white;font-size: 12px">{{__('addRatingToYourComment')}}</span>
                            <fieldset class="rating" style="margin-top: -5px">
                                <input type="radio" id="star5" name="rating" value="5" />
                                <label class="full" for="star5" title="Awesome - 5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star4half" name="rating" value="4 and a half" />
                                <label class="half" for="star4half" title="Pretty good - 4.5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label class="full" for="star4" title="Pretty good - 4 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star3half" name="rating" value="3 and a half" />
                                <label class="half" for="star3half" title="Meh - 3.5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star3" name="rating" value="3" />
                                <label class="full" for="star3" title="Meh - 3 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star2half" name="rating" value="2 and a half" />
                                <label class="half" for="star2half" title="Kinda bad - 2.5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label class="full" for="star2" title="Kinda bad - 2 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star1half" name="rating" value="1 and a half" />
                                <label class="half" for="star1half" title="Meh - 1.5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label class="full" for="star1" title="Sucks big time - 1 star" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                                <input type="radio" id="starhalf" name="rating" value="half" />
                                <label class="half" for="starhalf" title="Sucks big time - 0.5 stars" style="font-size: 30px;"><i class="icon-cz-star" style="font-size: 25px;"></i></label>
                            </fieldset>
                        </div>
                        <div class="dir-rat-inn">
                            <div class="form-group col-md-6 pad-left-o">
                                <input required type="text" class="form-control" name="name" placeholder="{{__('name')}}" style="border: 1px solid #dedede;"> </div>
                            <div class="form-group col-md-6 pad-left-o">
                                <input required type="email" class="form-control" name="email" placeholder="{{__('email')}}" style="border: 1px solid #dedede;"> </div>
                            <div class="form-group col-md-12 pad-left-o">
                                <input required type="text" class="form-control" name="title" placeholder="{{__('title')}}" style="border: 1px solid #dedede;"> </div>
                            <div class="form-group col-md-12 pad-left-o">
                                <textarea required placeholder="{{__('writeYourMessage')}}" name="description"></textarea>
                            </div>
                            <div class="form-group col-md-12 pad-left-o">
                                <input type="button" id="sendComment" value="{{__('submit1')}}" class="link-btn">
                            </div>
                        </div>
                        @endif
                        <input type="hidden" name="productID" value="{{$product->id}}">
                        <input type="hidden" id="totalPriceWOSOHidden" value="">
                        <input type="hidden" id="totalPriceHidden" value="">
                        <input type="hidden" id="specials" value="">
                        <input type="hidden" id="euroValue" value="{{$euroValue}}">
                        <input type="hidden" id="desiredCurrencyValue" value="">
                        <input type="hidden" id="type" name="type" value="product">
                        <input type="hidden" class="specialOffer" value="">
                        <input type="hidden" class="dayToPrice" value="">
                        <input type="hidden" class="typeToPrice" value="">
                        <input type="hidden" class="currency-icon-for-calendar" value="{{session()->get('currencyIcon')}}">
                        <input type="hidden" class="errorType" value="">
                    </form>
            </div>
        </div>
        <div class="dir-rat-inn dir-rat-review">
            @if($product->supplierID == -1)
                <p style="font-weight: bold;">Paris Business and Travel</p>
            @endif
            @if(!($product->rate == null))
                @for($i=1;$i<=$product->rate;$i++)
                    <i class="icon-cz-star" style="color: #ffad0c; font-size: 20px;"></i>
                @endfor
                <span style="vertical-align: text-bottom;">{{round($product->rate, 1)}}</span>
            @endif
            <br>
            <span>({{$product->referenceCode}})</span>
        </div>
        <div class="similar-tours row">
            <p class="similar-title productContentTitles" style="margin-bottom: 3%;">{{__('youMightAlsoLike')}}</p>
            <div class="col-lg-12">
                @foreach($youMightAlsoLikeArray as $product)
                    <?php
                    $productTranslation = App\ProductTranslation::where('productID', $product->id)->where('languageID', $language->id)
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
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 b_packages wow slideInUp" data-wow-duration="0.5s">
                        @if(!($commonFunctions->getOfferPercentage($product) == 0))
                            <div class="band">
                                <div class="ribbon  ribbon--orange" style="margin-top: 0px">% {{$commonFunctions->getOfferPercentage($product)}}</div>
                            </div>
                        @endif
                        <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif">
                            <div class="v_place_img" style="padding: 2%">
                                <img class="mobile-image" style="height:160px;padding: 2%;"
                                     @if ($product->ProductGalleries()->where('id', '=', $product->coverPhoto)->first())
                                     src="{{Storage::disk('s3')->url('product-images-xs/' . $product->ProductGalleries()->where('id', '=', $product->coverPhoto)->first()->src)}}"
                                     {{--                                     @if ($product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first())--}}
                                     {{--                                     src="{{Storage::disk('s3')->url('product-images-xs/' . $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()->src)}}"--}}
                                     @else
                                     src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                                     @endif
                                     alt="{{$product->title}}" title="Tour Booking" />
                            </div>
                        </a>
                        <div class="col-lg-12 b_pack rows">
                            <div class="row" style="height: 95px;width: 100%;">
                                <div class="col-md-12">
                                    <p class="product-category">{{$product->category}}</p>
                                        <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif" style="font-size: 15px;font-weight: 500;color: #1A2B50">
                                            @if ($productTranslation)
                                                <h3 style="font-size: 15px;font-weight: 400;color: #1d6db2;text-decoration: revert;">{{$productTranslation->title}}</h3>
                                            @else
                                                <h3 style="font-size: 15px;font-weight: 400;color: #1d6db2;text-decoration: revert;">{{$product->title}}</h3>
                                            @endif
                                        </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row container">
                                    <div class="hidden-lg hidden-md hidden-sm col-xs-12 mobile_desc">
                                        {!! html_entity_decode(substr($product->shortDesc, 0, 100)) !!} ...
                                    </div>
                                </div>
                                <?php
                                $productSkills = $commonFunctions->getProductSkills($product);
                                ?>
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
                                <div class="col-lg-12">
                                    <div class="mobile_from">
                                        <span style="font-size:18px;">{{__('from')}}
                                            <?php $specialOffer = $commonFunctions->getOfferPercentage($product) ?>
                                            @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                                @if($specialOffer != 0)
                                                    <?php
                                                    $specialOfferPrice = round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - (($commonFunctions->getMinPrice($product->id)) * ($commonFunctions->getOfferPercentage($product)) / 100),2);
                                                    ?>
                                                    <span class="special-offer-price" style="font-size: 18px;color: #ffad0c;"><i class="{{session()->get('currencyIcon')}}"></i>{{$specialOfferPrice}}</span>
                                                    <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}</span>
                                                    <?php
                                                    $commissionerEarns = $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                                    ?>
                                                @else
                                                    <i class="{{session()->get('currencyIcon')}}"></i>{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}
                                                    <?php

                                                    $commissionerEarns = $currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                                    ?>
                                                @endif
                                                <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                            @else
                                                @if($specialOffer != 0)

                                                     <span class="special-offer-price" style="font-size: 25px;color: #ffad0c;">{{round($currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id)) - (  $currencyModel->calculateCurrencyForVisitor(($commonFunctions->getMinPrice($product->id))) * ($commonFunctions->getOfferPercentage($product)) / 100),2)}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                                        <br>
                                                        <span class="strikeout">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}<i class="{{session()->get('currencyIcon')}}"></i></span>
                                                    @else
                                                        <span style="color:#ffad0c;font-size: 27px">{{$currencyModel->calculateCurrencyForVisitor($commonFunctions->getMinPrice($product->id))}}<i class="{{session()->get('currencyIcon')}}" style="color: #ffad0c;"></i></span>
                                                    @endif
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12" style="border-top: 1px solid #cdcdcd;height: 26px;padding-top: 1%;">
                                    <div class="row" style="height: 30px;">
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
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@if(!(auth()->guard('web')->check()))
    <style>#COM{display: none;}</style>
@else
    @if(is_null(auth()->guard('web')->user()->commission))
        <style>#COM{display: none;}</style>
    @endif
@endif

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'product'])
