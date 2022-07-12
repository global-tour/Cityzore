@include('frontend-partials.head', ['page' => 'attractions'])
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
$langID = \App\Language::where('code', $langCode)->first()->id;
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$attractionTranslation = \App\AttractionTranslation::where('languageID', $langID)->where('attractionID', $attractionID)->first();
?>
<section class="hot-page2-alp hot-page2-pa-sp-top">
    <div class="container-fluid">
        <div class="row">
            <div class="hot-page2-alp-con">
                <div class="col-md-12 hot-page2-alp-con-right">
                    <div class="hot-page2-alp-con-right-1">
                        <div class="row">
                            <div class="col-lg-12 hidden-xs attraction-banner" style="margin-bottom: 1%;">
                                <div class="row">
                                    <div class="col-lg-12" style="text-align: center;">
                                        @if(!$attractionTranslation)
                                            <h1 style="text-align: center;font-size: 40px;color: white; font-weight: bold;padding-top: 10px;">{{$attraction->name}}</h1>
                                        @else
                                            <h1 style="text-align: center;font-size: 40px;color: white; font-weight: bold;padding-top: 40px;">{{$attractionTranslation->name}}</h1>
                                        @endif
                                    </div>
                                </div>
                            </div>

                             <form action="{{url()->current()}}" method="get" id="filter-form">

                            <div class="container">
                                <input type="hidden" id="q" value="">
                                <input type="hidden" id="searchDateFrom" value="">
                                <input type="hidden" id="searchDateTo" value="">
                                <input type="hidden" id="searchType" value="">
                                <input type="hidden" class="isAttractionPage" value="1">
                                <div class="col-lg-5" style="border-radius: 5%;">
                                    <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 1%;margin-top: 1%;">
                                        <span class="col-md-12" style="font-size: 18px;">{{__('availableDates')}}</span>
                                        <div class="col-md-5">
                                            <label style="font-size: 12px;">{{__('from')}}</label>
                                            <input type='text' class='datepicker-from' name="from_date" @if(request()->from_date) value="{{request()->from_date}}"  @endif data-language='en' placeholder="{{__('from')}}"  id="select-search2" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/>
                                        </div>
                                        <div class="col-md-5">
                                            <label style="font-size: 12px;">{{__('to')}}</label>
                                            <input type='text' class='datepicker-to' name="to_date" @if(request()->to_date) value="{{request()->to_date}}"  @endif data-language='en' placeholder="{{__('to')}}" id="select-search3" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;" />
                                        </div>
                                        <div class="col-md-1" style="text-align: center; margin-top: 28px; margin-bottom: 20px;">
                                            <button class="btn link-btn" id="checkAvailability" style="width: auto;margin-left: -17px;">Check</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3" style="border-radius: 5%;">
                                    <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 1%;margin-top: 1%;">
                                        <span class="col-md-12" style="font-size: 18px;">{{__('categories')}}</span>
                                        <div class="col-md-12" style="padding-top: 23px;height: 80px;">


                                            <select class="form-check-input  mdb-select" name="category[]" multiple style="height: 40px;">
                                                 @foreach($categories as $i => $category)
                                                <?php
                                                $categoryID = \App\Category::where('categoryName', $category)->first()->id;
                                                $categoryTranslation = \App\CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $langID)->first();
                                                ?>
                                                <option @if(!empty($requestCategories) && in_array($category, $requestCategories) ) selected @endif data-cat-name="@if(!$categoryTranslation) {{$category}} @else {{$categoryTranslation->categoryName}} @endif" value="@if(!$categoryTranslation) {{$category}} @else {{$categoryTranslation->categoryName}} @endif">@if(!$categoryTranslation) {{$category}} @else {{$categoryTranslation->categoryName}} @endif</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2" style="border-radius: 5%;">
                                    <div class="col-lg-12" style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 5%;padding: 1%;margin-top: 1%;">
                                        <span class="col-md-12" style="font-size: 18px;">{{__('price')}}</span>
                                        <div class="col-md-12" style="padding-top: 23px;height: 80px;">
                                              @php
                                           $twenty_five = ceil($currencyModel::calculateCurrencyForVisitor(25));
                                           $fifty = ceil($currencyModel::calculateCurrencyForVisitor(50));
                                           $seventy_five = ceil($currencyModel::calculateCurrencyForVisitor(75));
                                           $hundred = ceil($currencyModel::calculateCurrencyForVisitor(100));
                                        @endphp
                                            <select class="form-check-input mdb-select" multiple name="price[]" style="height: 40px;">
                                                <option @if(!empty(request()->price) && in_array("0-25", request()->price) ) selected @endif value="0-25" data-price="0-25">0-{{$twenty_five}} <i class="{{session()->get('currencyIcon')}}"></i></option>

                                                 <option @if(!empty(request()->price) && in_array("25-50", request()->price) ) selected @endif value="25-50" data-price="25-50">{{$twenty_five}}-{{$fifty}} <i class="{{session()->get('currencyIcon')}}"></i></option>

                                                  <option @if(!empty(request()->price) && in_array("50-75", request()->price) ) selected @endif value="50-75" data-price="50-75">{{$fifty}}-{{$seventy_five}} <i class="{{session()->get('currencyIcon')}}"></i></option>

                                                   <option @if(!empty(request()->price) && in_array("75-100", request()->price) ) selected @endif value="75-100" data-price="75-100">{{$seventy_five}}-{{$hundred}} <i class="{{session()->get('currencyIcon')}}"></i></option>

                                                   <option @if(!empty(request()->price) && in_array("100-10000000", request()->price) ) selected @endif value="100-10000000" data-price="100-10000000">{{$hundred}}+ <i class="{{session()->get('currencyIcon')}}"></i></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="col-lg-12">
                                        <input class="hot-page2-alp-quot-btn" type="reset" value="Reset Form" id="reset-form" style="width: 100%;">
                                    </div>
                                    <div class="col-md-12" style="margin-top: 20px;">
                                        <label style="font-size: 17px;">{{__('sortBy')}}:</label>
                                        <input type="hidden" id="selectedSortType" value="recommended">
                                        <div class="ap-dropdownprod" style="border-right: 1px solid #e7ebef!important; width: 150px; padding-left: 10px;">


                                            <select class="shaselect form-control" name="sort" id="sort">

                                                <option value="">{{__('recommended')}}</option>
                                                <option value="price-asc" @if(!empty(request()->sort) && request()->sort == "price-asc")  selected @endif>{{__('price')}} {{__('lowToHigh')}}</option>
                                                <option value="price-desc" @if(!empty(request()->sort) && request()->sort == "price-desc") selected  @endif>{{__('price')}} {{__('highToLow')}}</option>
                                                <option value="rating-desc" @if(!empty(request()->sort) && request()->sort == "rating-desc") selected  @endif>{{__('rating')}} {{__('highToLow')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-lg-3" style="margin-top: 20px; font-size: 17px;">
                                    @if(!$attractionTranslation){{$attraction->name}}@else{{$attractionTranslation->name}}@endif - <span id="productCount">{{$products->total()}}</span> {{__('activityFound')}}
                                </div>
                            </div>
                            <div class="col-md-3 hidden-xs">
                               <!--  <div class="col-lg-12" style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
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
                                                    <input class="form-check-input categoriesCheckBox" @if(!empty(request()->category) && in_array($category, request()->category) ) checked @endif type="checkbox" name="category[]" id="category{{$i}}" value="{{$category}}" data-cat-name="{{$category}}">
                                                    <label class="form-check-label" style="font-size: 1.3rem;" for="category{{$i}}">
                                                        @if(!$categoryTranslation) {{$category}} @else {{$categoryTranslation->categoryName}} @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="attractions" value="{{$attraction->id}}">
                                    </div>
                                </div> -->
                            </div>






                        </form>


                            <div class="container" id="productsDiv">




                               @foreach ($products as $product)

                               @php


                                  $coverID = $product["m"]->coverPhoto;


                                  if(!is_null($coverID)){

                                    $productImage = \App\ProductGallery::findOrFail($coverID)->src;

                                  } else{
                                     $productImage = "default_product.jpg";
                                  }





                                if($langCode == 'en'){
                                    $productTitle =  $product["m"]->title;
                                    $productShort =  str_limit($product["m"]->shortDesc, 100, '...');
                                     $productUrl = "/".($product["m"]->url ?? '');

                                }else{
                                 if(empty($product["m"]->translations)){
                                    continue;
                                 }


                                  $productTitle =  $product["m"]->translations->title ?? '';
                                  $productShort =  str_limit($product["m"]->translations->shortDesc ?? '', 100, '...');
                                   $productUrl = "/".session()->get("userLanguage")."/".($product["m"]->translations->url ?? '');
                                }
                                $rating = $product["m"]->rate;


                               @endphp


                                <div class="hot-page2-alp-r-list">
                                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 hot-page2-alp-r-list-re-sp">
                                        <a href="{{$productUrl}}">







                                       @if($product["special"] > 0)
                                         <div class="band2">
                                            <div class="ribbon ribbon--orange" style="margin-top: 0px">% {{$product["special"]}}</div>
                                        </div>
                                        @endif









                                            <div class="hot-page2-hli-1" style="border-radius: 10px;">






                                                <img src="{{Storage::disk('s3')->url('product-images-xs')}}/{{$productImage}}" alt="" style="border-radius: 5%;padding: 5%;min-height: 155px;max-height: 185px;">

                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="border-right: 1px solid #dedede;">
                                        <div class="trav-list-bod">
                                            <a href="{{$productUrl}}">
                                                <h2 style="font-size: 17px;">{{$productTitle}}</h2>
                                            </a>
                                            <div class="dir-rat-star" style="font-size: 15px;">
                                                <div class="rating" style="direction: ltr;width:100%;">
                                                    @for ($i = 0; $i < 5; $i++)
                                                    @if($i < ceil($rating))
                                                       <i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>
                                                       @else
                                                       <i class="icon-cz-star" style="color: #ccc; font-size: 15px;"></i>
                                                       @endif
                                                    @endfor





                                                    <label style="font-size: 13px;vertical-align: text-bottom; float:left;color: #1A2B50;">{{$rating ?? 0}}/5 |</label>
                                                </div>
                                            </div>
                                            <div>{{$productShort}}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <div class="hot-page2-alp-ri-p3 tour-alp-ri-p3">
                                            <span class="hot-list-p3-1">Prices Starting</span>
                                            <span class="hot-list-p3-2" style="font-size: 17px;">

                                                @if($product["special"] > 0)

                                                <span style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{round($currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($product["m"]->id))-$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($product["m"]->id))*$product["special"]/100, 2)}}</span>





                                                     <span class="strikeout" style="font-size: 17px;">
                                                        <i class="icon-cz-eur"></i>{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($product["m"]->id))}}
                                                    </span>

                                                    @else

                                                      <span style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{$currencyModel::calculateCurrencyForVisitor($commonFunctions->getMinPrice($product["m"]->id))}}</span>



                                                @endif




                                            </span>
                                            <span class="hot-list-p3-4">
                                                <a id="2" href="{{$productUrl}}" class="hot-page2-alp-quot-btn">Book Now</a>
                                            </span>
                                        </div>
                                    </div>
                                </div>


                               @endforeach



















                               {!! $products->appends(request()->input())->links() !!}




                            </div>
                            <div class="col-md-9 text-center" id="loadingDiv" style="margin-top: 50px; display: none;">
                                <img src="{{asset('/img/loading.gif')}}"  width="50" alt="loading"/>
                            </div>
                            <div class="col-md-9 col-md-offset-3" style="margin-top: 10px;">
                                <div id="paginator"></div>
                            </div>
                            <div class="col-lg-12 attraction-banner2" style="padding: 4%;">
                                @if(!$attractionTranslation)
                                    <p style="font-size: 18px; margin: 3%;">{!! html_entity_decode($attraction->description) !!}</p>
                                @else
                                    <p style="font-size: 18px; margin: 3%;">{!! html_entity_decode($attractionTranslation->description) !!}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'paginate-attractions'])

