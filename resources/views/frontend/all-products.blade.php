@include('frontend-partials.head', ['page' => 'all-products'])
@include('frontend-partials.header')
<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$language = App\Language::where('code', $langCode)->first();
$langID = $language->id;
$attractionTranslationModel = new \App\AttractionTranslation();
?>
    <section class="hot-page2-alp hot-page2-pa-sp-top">
        <div class="container-fluid">
            <div class="row">
                <div class="hot-page2-alp-con">
                    <div class="col-md-12 hot-page2-alp-con-right">
                        <div class="hot-page2-alp-con-right-1">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="q" value="">
                                    <input type="hidden" id="searchDateFrom" value="">
                                    <input type="hidden" id="searchDateTo" value="">
                                    <input type="hidden" id="searchType" value="">
                                    <div class="col-md-3" style="margin-top: 20px; font-size: 17px;">
                                        {{__('allProducts')}} - <span id="productCount"></span> {{__('activityFound')}}
                                    </div>
                                    <div class="col-md-offset-7 col-md-2" style="margin-top: 20px;">
                                        <label style="font-size: 17px;">{{__('sortBy')}}:</label>
                                        <input type="hidden" id="selectedSortType" value="recommended">
                                        <div class="ap-dropdownprod" style="border-right: 1px solid #e7ebef!important; width: 150px; padding-left: 10px;">
                                            <button class="ap-dropbtn fromCenter sortButton">{{__('recommended')}} &#9660;</button>
                                            <div class="ap-dropdown-content">
                                                <span style="display: none;" class="sortType" data-sort="recommended">
                                                    {{__('recommended')}}
                                                </span>
                                                <span style="display: none;" class="sortType" data-sort="priceAsc">
                                                    {{__('price')}} ({{__('lowToHigh')}})
                                                </span>
                                                <span style="display: none;" class="sortType" data-sort="priceDesc">
                                                    {{__('price')}} ({{__('highToLow')}})
                                                </span>
                                                <span style="display: none;" class="sortType" data-sort="ratingDesc">
                                                    {{__('rating')}} ({{__('highToLow')}})
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 hidden-xs">
                                    <div class="col-lg-12" style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                            <span class="col-md-12" style="font-size: 16px;">{{__('availableDates')}}</span>
                                            <div class="col-md-12">
                                                <label style="font-size: 12px;">{{__('from')}}</label>
                                                <input type='text' class='datepicker-from' data-language='en' id="select-search2" placeholder="{{__('from')}}" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/>
                                            </div>
                                            <div class="col-md-12" style="margin-top: 30px;">
                                                <label style="font-size: 12px;">{{__('to')}}</label>
                                                <input type='text' class='datepicker-to' data-language='en' placeholder="{{__('to')}}" id="select-search3"  style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/>
                                            </div>
                                            <div class="col-md-12" style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
                                                <button class="btn link-btn" id="checkAvailability"  style="width: auto !important;">{{__('checkAvailability')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                            <span class="col-md-12" style="font-size: 18px;">{{__('categories')}}</span>
                                            <div class="col-md-12">
                                                <input type="hidden" id="categories" value="">
                                                @foreach($categories as $i => $category)
                                                    <?php
                                                    $categoryID = \App\Category::where('categoryName', $category)->first()->id;
                                                    $categoryTranslation = \App\CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $langID)->first();
                                                    ?>
                                                <div class="form-check">
                                                    <input class="form-check-input categoriesCheckBox" type="checkbox" name="category{{$i}}" id="category{{$i}}" value="0" data-cat-name="{{$category}}">
                                                    <label class="form-check-label" style=" font-size: 1.3rem;" for="category{{$i}}">
                                                        @if(!$categoryTranslation){{$category}}@else{{$categoryTranslation->categoryName}}@endif
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                            <span class="col-md-12" style="font-size: 18px; margin-top: 20px;">{{__('attractions')}}</span>
                                            <div class="col-md-12">
                                                <input type="hidden" id="attractions" value="">
                                                @foreach($attractions as $i => $attraction)
                                                    <?php
                                                    $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where('languageID', $langID)->first();
                                                    ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input attractionsCheckBox" type="checkbox" name="attraction{{$i}}" id="attraction{{$i}}" value="0" data-attraction-id="{{$attraction->id}}">
                                                        <label class="form-check-label" style="font-size: 1.3rem;" for="attraction{{$i}}">
                                                            @if(!$attractionTranslation){{$attraction->name}}@else{{$attractionTranslation->name}}@endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                            <span class="col-md-12" style="font-size: 18px;margin-top: 20px;">{{__('price')}}</span>
                                            <div class="col-md-12">
                                                <input type="hidden" id="page" value="1">
                                                <input type="hidden" id="prices" value="">
                                                <div class="form-check">
                                                    <input class="form-check-input pricesCheckBox" type="checkbox" name="price0" id="price0" value="0" data-price="0-25">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="price0">
                                                        0-25 <i class="{{session()->get('currencyIcon')}}"></i>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input pricesCheckBox" type="checkbox" name="price1" id="price1" value="0" data-price="25-50">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="price1">
                                                        25-50 <i class="{{session()->get('currencyIcon')}}"></i>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input pricesCheckBox" type="checkbox" name="price2" id="price2" value="0" data-price="50-75">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="price2">
                                                        50-75 <i class="{{session()->get('currencyIcon')}}"></i>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input pricesCheckBox" type="checkbox" name="price3" id="price3" value="0" data-price="75-100">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="price3">
                                                        75-100 <i class="{{session()->get('currencyIcon')}}"></i>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input pricesCheckBox" type="checkbox" name="price4" id="price4" value="0" data-price="100+">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="price4">
                                                        100+ <i class="{{session()->get('currencyIcon')}}"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9" id="productsDiv" style="display:none;">

                                </div>
                                <div class="col-md-9 text-center" id="loadingDiv" style="margin-top: 50px;">
                                    <img src="{{asset('/img/loading.gif')}}"  width="50" alt="loading"/>
                                </div>
                                <div class="col-md-9 col-md-offset-3" style="margin-top: 10px;">
                                    <div id="paginator"></div>
                                </div>
                                <div class="col-lg-12 hidden-xs tour_head1 tout-map map-container" style="margin-left: 15%;margin-right: 15%;">
                                    <p style="font-weight: 400;font-size: 22px;">Top 4 sights</p>
                                    @foreach($attractions->take(4) as $attraction)
                                    <?php $attractionTranslation = $attractionTranslationModel->where('languageID', $language->id)->where('attractionID', $attraction->id)->first(); ?>
                                        <div class="thumbex" style="margin: 10px 9px 30px;max-width: 350px;min-width: 240px;float:left;height: 235px;border-radius: 12px;">
                                            <div class="thumbnail" style="min-width: 350px;height: 236px;">
                                                @if($attractionTranslation)
                                                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}" target="blank">
                                                    <img src="{{Storage::disk('s3')->url('attraction-images/' . $attraction->image)}}" alt="{{$attractionTranslation->name}}">
                                                    <span style="font-size: 22px;">{{$attractionTranslation->name}}</span>
                                                @else
                                                    <a href="{{url('attraction/'.$attraction->slug.'-'.$attraction->id)}}">
                                                    <img src="{{Storage::disk('s3')->url('attraction-images/' . $attraction->image)}}" alt="{{$attraction->name}}">
                                                    <span style="font-size: 22px;">{{$attraction->name}}</span>
                                                @endif
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'all-products'])

