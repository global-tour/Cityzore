@include('frontend-partials.head', ['page' => 'cities'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$language = App\Language::where('code', $langCode)->first();
?>
<section>
    <div class="spe-title">
        <h2>{{__('popularCities')}}</h2>
        <div class="title-line">
            <div class="tl-1"></div>
            <div class="tl-2"></div>
            <div class="tl-3"></div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row" style="margin-bottom: 3%;">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6" style="text-align: center;">
                <i class="icon-cz-like" style="font-size: 30px;text-align: center"></i>
                <br>
                <span style="text-align: center;">{{__('freeCancellation')}}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6" style="text-align: center;">
                <i class="icon-cz-availability" style="font-size: 30px;text-align: center"></i>
                <br>
                <span style="text-align: center;">24/7 {{__('support')}}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6" style="text-align: center;">
                <i class="icon-cz-payment" style="font-size: 30px;text-align: center"></i>
                <br>
                <span style="text-align: center;">{{__('securePayment')}}</span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6" style="text-align: center;">
                <i class="icon-cz-disscount" style="font-size: 30px;text-align: center"></i>
                <br>
                <span style="text-align: center;">{{__('lowestPriceGuarantee')}}</span>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container" style="display: flex;">
        @foreach($cities as $city)
            <?php
            $cityImage = \App\CityImage::where('city', $city->city)->first();
            $cityObject = \App\City::where('name', $city->city)->first();
            $cityTranslation = \App\CityTranslation::where('cityID', $cityObject->id)->where('languageID', $language->id)->first();
            $cityName = $cityObject->name;
            if ($cityTranslation) {
                $cityName = $cityTranslation->name;
            }
            ?>
            <div class="thumbex">
                <div class="thumbnail">
                    <a href="{{url($langCodeForUrl.'/s?q='.$cityName.'&dateFrom=&dateTo=&type=city')}}">
                        @if(is_null($cityImage))
                        <img src="https://bit.ly/2vnI5ZM"/>
                        @else
                        <img src="{{Storage::disk('s3')->url('city-images/' . $cityImage->image)}}" alt="{{$cityImage->city}}">
                        @endif
                        <span>{{$cityName}}</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'frontend.home'])
