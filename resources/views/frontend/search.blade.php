@include('frontend-partials.head', ['page' => 'search'])
@include('frontend-partials.header')

@php
    $commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions();
    $isCommissioner = auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1';

@endphp


<section class="hot-page2-alp hot-page2-pa-sp-top">
    <div class="container-fluid">
        <div class="row">
            <div class="hot-page2-alp-con">
                <div class="col-md-12 hot-page2-alp-con-right">
                    <div class="hot-page2-alp-con-right-1">

                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="q" value="{{$q}}">
                                <input type="hidden" id="searchDateFrom" value="{{$dateFrom}}">
                                <input type="hidden" id="searchDateTo" value="{{$dateTo}}">
                                <input type="hidden" id="searchType" value="{{$type}}">
                            </div>
                            <div class="col-md-3 hidden-xs">
                                <div class="col-lg-12"
                                     style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                    <div class="col-lg-12"
                                         style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 10px;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <span class="col-md-12" style="font-size: 18px;">{{__('availableDates')}}</span>
                                        <div class="col-md-12">
                                            <label style="font-size: 12px;">{{__('from')}}</label>
                                            <input type='text' class='datepicker-from' data-language='en'
                                                   placeholder="{{__('From')}}" value="{{$dateFrom}}"
                                                   id="select-search2"
                                                   style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/>
                                        </div>
                                        <div class="col-md-12" style="margin-top: 30px;">
                                            <label style="font-size: 12px;">{{__('to')}}</label>
                                            <input type='text' class='datepicker-to' data-language='en'
                                                   placeholder="{{ __('To') }}" value="{{$dateTo}}" id="select-search2"
                                                   style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/>
                                        </div>
                                        <div class="col-md-12"
                                             style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
                                            <button class="btn link-btn"
                                                    id="checkAvailability">{{__('checkAvailability')}}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12"
                                     style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                    <div class="col-lg-12"
                                         style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 10px;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <span class="col-md-12" style="font-size: 18px;">{{__('categories')}}</span>
                                        <div class="col-md-12">
                                            <input type="hidden" id="categories" value="">
                                            @foreach($categories as $i => $category)
                                                <div class="form-check">
                                                    <input class="form-check-input categoriesCheckBox" type="checkbox"
                                                           name="category{{$i}}" id="category{{$i}}" value="0"
                                                           data-cat-name="{{$category}}">
                                                    <label class="form-check-label" style="font-size: 1.3rem;"
                                                           for="category{{$i}}">
                                                        {{$category}}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12"
                                     style="border: 1px solid #dfdfdf;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                    <div class="col-lg-12"
                                         style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;border-radius: 10px;border-radius: 5%;padding: 5%;margin-top: 5%;">
                                        <span class="col-md-12"
                                              style="font-size: 18px;margin-top: 20px;">{{__('price')}}</span>
                                        <div class="col-md-12">
                                            <input type="hidden" id="prices" value="">
                                            <div class="form-check">
                                                <input class="form-check-input pricesCheckBox" type="checkbox"
                                                       name="price0" id="price0" value="0" data-price="0-25">
                                                <label class="form-check-label" style="font-size: 1.3rem;" for="price0">
                                                    0-25 <i class="{{session()->get('currencyIcon')}}"></i>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pricesCheckBox" type="checkbox"
                                                       name="price1" id="price1" value="0" data-price="25-50">
                                                <label class="form-check-label" style="font-size: 1.3rem;" for="price1">
                                                    25-50 <i class="{{session()->get('currencyIcon')}}"></i>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pricesCheckBox" type="checkbox"
                                                       name="price2" id="price2" value="0" data-price="50-75">
                                                <label class="form-check-label" style="font-size: 1.3rem;" for="price2">
                                                    50-75 <i class="{{session()->get('currencyIcon')}}"></i>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pricesCheckBox" type="checkbox"
                                                       name="price3" id="price3" value="0" data-price="75-100">
                                                <label class="form-check-label" style="font-size: 1.3rem;" for="price3">
                                                    75-100 <i class="{{session()->get('currencyIcon')}}"></i>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pricesCheckBox" type="checkbox"
                                                       name="price4" id="price4" value="0" data-price="100+">
                                                <label class="form-check-label" style="font-size: 1.3rem;" for="price4">
                                                    100+ <i class="{{session()->get('currencyIcon')}}"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9" id="productsDiv">
                                <div class="row">
                                    <div class="col-md-2" style="margin-top: 20px; font-size: 17px;">
                                        {{$q}} - <span id="productCount">{{ $total }}</span> {{__('activityFound')}}
                                    </div>
                                    <div class="col-md-offset-7 col-md-3" style="margin-top: 20px;">
                                        <label style="font-size: 17px;">{{__('sortBy')}}:</label>
                                        <input type="hidden" id="selectedSortType" value="recommended">
                                        <div class="ap-dropdownprod"
                                             style="border-right: 1px solid #e7ebef!important; width: 150px; padding-left: 10px;">
                                            <button class="ap-dropbtn fromCenter sortButton">{{__('recommended')}}&#9660;
                                            </button>
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
                                @forelse($items as $item)
                                    <div class="hot-page2-alp-r-list">

                                        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 hot-page2-alp-r-list-re-sp">
                                            <a href="{{ !is_null($item->translations) ? $item->translations->url : $item->url }}">
                                                @if($commonFunctions->getOfferPercentage($item))
                                                    <div class="band2">
                                                        <div class="ribbon ribbon--orange" style="margin-top: 0px">
                                                            % {{ $commonFunctions->getOfferPercentage($item) }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="hot-page2-hli-1" style="border-radius: 10px;"><img
                                                        src="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url('product-images-xs/'. (!is_null($item->productCover) && !is_null($item->productCover->src) ? $item->productCover->src: 'default_product.jpg' )) }}"
                                                        alt=""
                                                        style="border-radius: 5%;padding: 5%;min-height: 155px;max-height: 185px;">
                                                </div>
                                            </a>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"
                                             style="border-right: 1px solid #dedede;">
                                            <div class="trav-list-bod">
                                                <a href="{{ !is_null($item->translations) ? $item->translations->url : $item->url }}">
                                                    <h2 style="font-size: 17px;">{{ !is_null($item->translations) ? $item->translations->title : $item->title }}</h2>
                                                </a>
                                                <div class="dir-rat-star" style="font-size: 15px;">
                                                    <div class="rating" style="direction: ltr;width:100%;">

                                                        @if($item->rate)
                                                            @for($i = 0; $i < ceil($item->rate) ; $i++)

                                                                <i class="icon-cz-star"
                                                                   style="color: #ffad0c; font-size: 15px;"></i>

                                                            @endfor
                                                            <label
                                                                style="font-size: 13px;vertical-align: text-bottom; float:right;color: #1A2B50; padding-left: 3px;">{{ ceil($item->rate) }}
                                                                /5 </label>
                                                        @else
                                                            <div style="font-size: 13px;vertical-align: text-bottom;">No
                                                                reviews yet
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>
                                                <div>
                                                    {{ \Illuminate\Support\Str::limit(!is_null($item->translations) ? $item->translations->shortDesc : $item->shortDesc, 200, '...') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                            <div class="hot-page2-alp-ri-p3 tour-alp-ri-p3">
                                                <span class="hot-list-p3-1">Prices Starting</span>
                                                <span class="hot-list-p3-2" style="font-size: 17px;">
                                                    @php
                                                        $specialOfferPrice = \App\Currency::calculateCurrencyForVisitor($commonFunctions->getMinPrice($item->id)) - (($commonFunctions->getMinPrice($item->id)) * ($commonFunctions->getOfferPercentage($item)) / 100);
                                                        $specialOfferPrice = number_format((float)$specialOfferPrice, 2, '.', '');
                                                        $normalPrice = \App\Currency::calculateCurrencyForVisitor($commonFunctions->getMinPrice($item->id));
                                                    @endphp
                                                    @if($commonFunctions->getOfferPercentage($item))
                                                        <span class="special-offer-price" style="font-size: 17px;">
                                                            <i class="{{ session()->get('currencyIcon') }}"></i>

                                                            {{ $specialOfferPrice }}
                                                        </span>
                                                        <span class="strikeout" style="font-size: 17px;">
                                                            <i class="{{ session()->get('currencyIcon') }}"></i>
                                                            {{ $normalPrice }}
                                                        </span>
                                                    @else
                                                        <i class="{{ session()->get('currencyIcon') }}"></i>
                                                        {{ $normalPrice }}
                                                    @endif

                                                </span>
                                                <span class="hot-list-p3-4">
                                                    <a href="{{ !is_null($item->translations) ? $item->translations->url : $item->url }}"
                                                       class="hot-page2-alp-quot-btn">
                                                        Book Now
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    Not Found!
                                @endforelse
                                {{ $items->appends(request()->query())->links() }}
                            </div>
                            <div class="col-md-9 text-center" id="loadingDiv" style="margin-top: 50px; display: none">
                                <img src="{{asset('/img/loading.gif')}}" width="50" alt="loading"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'search-specific'])

