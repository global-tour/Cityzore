@include('frontend-partials.head', ['page' => '404'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;

?>

    <section>
        <div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 4%;">
            <div class="container">
                <div class="col-md-6">
                    <img src="{{asset('img/404.png')}}" style="width:100%;">
                    <div class="spe-title">
                        <p>Sorry, the page you requested was not found</p>
                        <p style="font-weight: bold;color: #253d52;">We can help!</p>
                    </div>
                    <div class="col-md-8" style="margin-right: 15%;margin-left: 15%;">
                        <form>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="text">Your Message</label>
                                <input type="text" class="form-control" id="text" placeholder="Your Message" style="height: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="spe-title">
                        <h2>It looks like  <span style="color: #5d4aad;">you're lost</span> </h2>
                        <div class="title-line">
                            <div class="tl-1"></div>
                            <div class="tl-2"></div>
                            <div class="tl-3"></div>
                        </div>
                        <p>Some Paris Attractions</p>
                    </div>
                         @php
                            $lang = \App\Language::where('code', (empty($langCodeForUrl) ? "en" : $langCodeForUrl))->first();
                           
                        @endphp
                    <div class="col-md-12">
                        @php
                             $slug = "eiffel-tower";
                             $attraction = \App\Attraction::where('slug', $slug)->first();
                             if(!empty($attraction) && $lang->id != 1){
                                $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where("languageID", $lang->id)->first();
                                $slug = $attractionTranslation->slug;
                             }
                        @endphp
                        
                        <a href="{{url($langCodeForUrl.'/attraction/'.$slug.'-'.$attraction->id)}}">
                            <div class="tour-mig-like-com">
                                <div class="tour-mig-lc-img"> <img src="{{asset('img/eiffel-tower-attraction.jpg')}}" alt="Eiffel Tower" style="height: 200px;"> </div>
                                <div class="tour-mig-lc-con">
                                    <h5>Eiffel Tower</h5>
                                    <p><span>12 Packages</span> Starting from <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(39)}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                         @php
                             $slug = "seine-river-cruise";
                             $attraction = \App\Attraction::where('slug', $slug)->first();
                             if(!empty($attraction) && $lang->id != 1){
                                $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where("languageID", $lang->id)->first();
                                $slug = $attractionTranslation->slug;
                             }
                        @endphp
                        <a href="{{url($langCodeForUrl.'/attraction/'.$slug.'-'.$attraction->id)}}">
                            <div class="tour-mig-like-com">
                                <div class="tour-mig-lc-img"> <img src="{{asset('img/seine-river-attraction.jpg')}}" alt="Seine River"> </div>
                                <div class="tour-mig-lc-con tour-mig-lc-con2">
                                    <h5>Seine River</h5>
                                    <p>Starting from <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(15)}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                         @php
                             $slug = "louvre-museum";
                             $attraction = \App\Attraction::where('slug', $slug)->first();
                             if(!empty($attraction) && $lang->id != 1){
                                $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where("languageID", $lang->id)->first();
                                $slug = $attractionTranslation->slug;
                             }
                        @endphp
                        <a href="{{url($langCodeForUrl.'/attraction/'.$slug.'-'.$attraction->id)}}">
                            <div class="tour-mig-like-com">
                                <div class="tour-mig-lc-img"> <img src="{{asset('img/louvre-museum-attraction2.jpg')}}" alt="Louvre Museum"> </div>
                                <div class="tour-mig-lc-con tour-mig-lc-con2">
                                    <h5>Louvre Museum</h5>
                                    <p>Starting from <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(15)}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                         @php
                             $slug = "big-bus-hop-on-hop-off";
                             $attraction = \App\Attraction::where('slug', $slug)->first();
                             if(!empty($attraction) && $lang->id != 1){
                                $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where("languageID", $lang->id)->first();
                                $slug = $attractionTranslation->slug;
                             }
                        @endphp
                        <a href="{{url($langCodeForUrl.'/attraction/'.$slug.'-'.$attraction->id)}}">
                            <div class="tour-mig-like-com">
                                <div class="tour-mig-lc-img"> <img src="{{asset('img/paris-big-bus.jpg')}}" alt=""> </div>
                                <div class="tour-mig-lc-con tour-mig-lc-con2">
                                    <h5>Big Bus</h5>
                                    <p>Starting from <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(25)}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">

                        @php
                             $slug = "cabaret-shows";
                             $attraction = \App\Attraction::where('slug', $slug)->first();
                             if(!empty($attraction) && $lang->id != 1){
                                $attractionTranslation = \App\AttractionTranslation::where('attractionID', $attraction->id)->where("languageID", $lang->id)->first();
                                $slug = $attractionTranslation->slug;
                             }
                        @endphp

                        <a href="{{url($langCodeForUrl.'/attraction/'.$slug.'-'.$attraction->id)}}">
                            <div class="tour-mig-like-com">
                                <div class="tour-mig-lc-img"> <img src="{{asset('img/cabaret-shows-paris.jpg')}}" alt=""> </div>
                                <div class="tour-mig-lc-con tour-mig-lc-con2">
                                    <h5>Cabaret Shows</h5>
                                    <p>Starting from <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(24)}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => '404'])
