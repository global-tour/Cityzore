@include('frontend-partials.head', ['page' => 'all-attractions'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section class="hot-page2-alp hot-page2-pa-sp-top">
    <div class="container" style="width: 90%">
        <div class="row">
            <div class="hot-page2-alp-con">
                <div class="col-md-12 hot-page2-alp-con-right">
                    <div class="hot-page2-alp-con-right-1">
                        <div class="row">
                            <div class="spe-title">
                                <h2 style="margin-top: 2%;">{{__('allAttractions')}}</h2>
                                <div class="title-line">
                                    <div class="tl-1"></div>
                                    <div class="tl-2"></div>
                                    <div class="tl-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($attractions as $attraction)
                                <div class="col-lg-4">
                                    <div class="row">

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'all-attractions'])

