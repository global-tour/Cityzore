<!DOCTYPE html>
<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>
<html lang=@if($langCodeForUrl==null) "en" @else "{{$langCodeForUrl}}" @endif>
<head>
    <link rel="stylesheet" href="{{asset('css/font/cityzore.css')}}">
    <!--== META TAGS ==-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--== FAV ICON ==-->
    <link rel="shortcut icon" href="{{asset('img/fav.ico')}}">
    <!-- GOOGLE FONTS -->
    <link rel="stylesheet" href="{{asset('css/main/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/main/materialize.css')}}">
    <link rel="stylesheet" href="{{asset('css/main/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('css/main/mob.css')}}">
    <link rel="stylesheet" href="{{asset('css/font/cityzore.css')}}">
    <link href="{{asset('css/main/header-styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('css/main/custom.css')}}">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-125555717-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-125555717-2');
    </script>
    <!-- Hotjar Tracking Code for https://www.cityzore.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:1919876,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    <!-- Microsoft Session Recorder -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "94jazcitsc");
    </script>

    <!-- Global site tag (gtag.js) - Google Ads: 1009135479 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1009135479"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'AW-1009135479');
    </script>




 <style>
                 .tourz-sear-btn > input {
                width: 100%!important;
            }
            .searchInputNotValid {
                background-color: #ff8066!important;
            }
            .suggestions-container {
                display: none;
                position: absolute;
                top: 43px;
                z-index: 100;
                background-color: #fff;
                border: 1px solid #c6c8d0;
                max-height: 400px;
                overflow-y: auto;
                overflow-x: hidden;
                min-width: 320px;
                text-align: left;
            }
            .suggestion-item {
                border-top: 1px solid #c6c8d0;
                min-height: 40px;
                cursor: pointer;
                min-width:320px;
                padding:10px;
            }
            .suggestion-item:hover {
                background-color: #1593ff!important;
                color: #fff!important;
            }
 </style>




<?php
    $language = App\Language::where('code', $langCode)->first();
    $reqUri = $_SERVER['REQUEST_URI'];
    if ($page == 'all-attractions') {
        $reqUriArray = explode('-', $reqUri);
        $countArray = count($reqUriArray);
        unset($reqUriArray[$countArray-1]);
        $reqUri = implode('-', $reqUriArray);
        $pageMetaTag = App\Page::where('url', $reqUri)->first();
    }
    else {
        if ($reqUri == '/'.$langCodeForUrl) {
            $reqUri = '/';
        } else {
            $reqUri = str_replace('/'.$langCodeForUrl.'/', '/', $reqUri);
        }
        $pageMetaTag = App\Page::where('url', $reqUri)->first();
    }
    $title = '';
    $description = '';
    $keywords = '';
    if ($pageMetaTag) {
        $title = $pageMetaTag->title;
        $description = $pageMetaTag->description;
        $keywords = $pageMetaTag->keywords;
        $isTherePageMetaTagTranslation = App\PageMetaTagsTrans::where('pageID', $pageMetaTag->id ?? null)->where('languageID', $language->id)->first();
        if ($isTherePageMetaTagTranslation) {
            $title = $isTherePageMetaTagTranslation->title;
            $description = $isTherePageMetaTagTranslation->description;
            $keywords = $isTherePageMetaTagTranslation->keywords;
        }
    }
?>


@if($page == 'product')
        <?php
        $languages = \App\Language::where('isActive', '=', 1)->get();
        ?>
        <link rel=”canonical” href="{{url()->current()}}"/>
        <link href="https://www.cityzore.com/{{$product->url}}" rel="alternate" hreflang="en"  />
        @foreach($languages as $lan)
            <?php $productUrl = \App\ProductTranslation::where('productID', '=', $product->id)->where('languageID','=', $lan->id)->get(); ?>
            @foreach($productUrl as $p)
                <link href="https://www.cityzore.com/{{$lan->code}}/{{$p->url}}" rel="alternate" hreflang="{{$lan->code}}"/>
            @endforeach
        @endforeach
            <script type="application/ld+json">
            {
              "@context" : "http://schema.org",
              "@type" : "Product",
              @if($metaTag)
                "name" : "{{$metaTag->title}}",
                "description" : "{{$metaTag->description}}",
              @endif
                "image" : "{{Storage::disk('s3')->url('product-images/' . $image)}}",
              "offers" : {
                "@type" : "Offer",
                "priceCurrency": "EUR",
                "price" : "<?php $specialOffer = (new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product); ?>@if($specialOffer != 0){{round(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100),2)}}@else{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}@endif"
              },
              "aggregateRating" : {
                "@type" : "AggregateRating",
                "ratingValue" : "{{$product->rate}}/5",
                "ratingCount": "55"
              },
              "sameAs": [ "https://www.facebook.com/pariscitytours.fr/",
                "https://www.instagram.com/pariscitytours.fr/",
                "https://twitter.com/Parisviptrips" ]
            }
        </script>
        @if($metaTag)
        <title>{{$metaTag->title}}</title>
        <meta name="description" content="{{$metaTag->description}}">
        <meta name="keywords" content="{{$metaTag->keywords}}">
        <meta property="og:url" content="{{url()->current()}}" />
        <meta property="og:title" content="{{$metaTag->title}}" />
        <meta property="og:type" content=”website” />
        <meta property="og:description" content="{{$metaTag->description}}" />
        <meta property="og:image:secure_url" content="{{Storage::disk('s3')->url('product-images-xs/' . $image)}}" />
        <meta property="og:image:width" content="400" />
        <meta property="og:image:height" content="400" />
        <!-- Twitter cards -->
        <meta name="twitter:title" content="{{$metaTag->title}}" />
        <meta name="twitter:description" content="{{$metaTag->description}}" />
        <meta name="twitter:site" content="@parisviptrips" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" content="{{Storage::disk('s3')->url('product-images-xs/' . $image)}}" />

        @else
            <title>{!! html_entity_decode($product->title)!!}</title>
            <meta name="description" content="{!! html_entity_decode($product->shortDesc)!!}">
            <meta name="keywords" content="{!! html_entity_decode($product->title)!!}">
        @endif
            <style>
                .strikeout {
                    position: relative;
                    font-size: 19px;
                }
                .strikeout::after {
                    border-bottom: .13em solid #ffae0d;
                    content: "";
                    left: 0px;
                    line-height: .3em;
                    margin-top: calc(0.125em / 2 * -1);
                    position: absolute;
                    right: 0;
                    top: 50%;
                }
                .special-offer-price{
                    font-size: 19px;
                }
                .datepicker {
                    width: 100% !important;
                }

            </style>
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('css/main/product-styles.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('css/lightbox.min.css')}}" rel="stylesheet" type="text/css">
        <style>
            .toast-alert {
                background-color: #e57373!important;
                font-size: 14px;
            }
            .toast-success{
                background-color: #0f9d58!important;
                font-size: 14px;
            }
        </style>
    @elseif($page == 'wishlists')
        <style>
            .toast-alert {
                background-color: #e57373!important;
                font-size: 14px;
            }
            .toast-success{
                background-color: #0f9d58!important;
                font-size: 14px;
            }
        </style>
    @elseif($page == 'become-a-supplier')
    <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">
    <link rel="stylesheet" href="{{asset('css/main/become-a-supplier-styles.css')}}">
    @elseif($page == 'all-products')

        @unless($pageMetaTag)
            <?php
            $pageMetaTag = App\Page::where('url', '/all-products')->first();
            $isTherePageMetaTagTranslation = App\PageMetaTagsTrans::where('pageID', $pageMetaTag->id ?? null)->where('languageID', $language->id)->first();
            if ($isTherePageMetaTagTranslation) {
                $title = $isTherePageMetaTagTranslation->title;
                $description = $isTherePageMetaTagTranslation->description;
                $keywords = $isTherePageMetaTagTranslation->keywords;
            }
            ?>
        @endunless


        <script type="application/ld+json">
            {
              "@context" : "http://schema.org",
              "@type" : "Product",
              @if($pageMetaTag)
                "name" : "{{$title}}",
                "description" : "{{$description}}",
              @endif
              "sameAs": [ "https://www.facebook.com/pariscitytours.fr/",
                "https://www.instagram.com/pariscitytours.fr/",
                "https://twitter.com/Parisviptrips" ]
            }
        </script>
        @if($pageMetaTag)
            <title>{{$title}}</title>
            <meta name="description" content="{{$description}}">
            <meta name="keywords" content="{{$keywords}}">
            <meta property=”og:title” content="{{$title}}" />
            <meta property=”og:type” content=”website” />
            <meta property=”og:description” content="{{$description}}" />
            <meta property="og:image:width" content="400" />
            <meta property="og:image:height" content="400" />
            <!-- Twitter cards -->
            <meta name="twitter:title" content="{{$title}}" />
            <meta name="twitter:description" content="{{$description}}" />
            <meta name="twitter:site" content="@parisviptrips" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:image" content="{{asset('img/paris-city-tours-logo.png')}}" />

        @else
            <title>Paris City Tours</title>
            <meta name="description" content="All Paris City Tours">
            <meta name="keywords" content="Paris City Tours">
        @endif

        <style>
            .strikeout {position: relative;color: black;}
            .strikeout::after {border-bottom: .2em solid #ffae0d;content: "";left: 35%;
                line-height: .2em;margin-top: calc(0.125em / 2 * -1);position: absolute;
                width: 30%;right: 0;top: 50%;}
        </style>
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <style>
            .ap-dropbtn:after {
                display:block!important;
                content: ''!important;
                border-bottom: solid 3px #f4364f!important;
                transform: scaleX(0)!important;
                transition: transform 250ms ease-in-out!important;
            }

            .ap-dropbtn:hover:after {
                transform: scaleX(1)!important;
            }

            .ap-dropbtn {
                background-color: #f2f1f1!important;
                color: #1a2b50!important;
                padding: 2px!important;
                font-size: 13px!important;
                border: none!important;
            }

            .ap-dropdownprod {
                position: relative!important;
                display: inline-block!important;
            }

            .ap-dropdown-content {
                display: none!important;
                position: absolute!important;
                background-color: #f1f1f1!important;
                min-width: 180px!important;
                z-index: 1!important;
                opacity: 1!important;
            }

            .ap-dropdown-content span {
                color: #1a2b50 !important;
                padding: 12px 16px!important;
                text-decoration: none!important;
                display: block!important;
                cursor: pointer!important;
            }

            .ap-dropdown-content span:hover {background-color: #ddd!important;}

            .ap-dropdownprod:hover .ap-dropdown-content {display: block!important;}

            .ap-dropdownprod:hover .ap-dropbtn {background-color: #f2f1f1!important;}

        </style>

    @elseif($page == 'attractions')



        @unless($pageMetaTag)
            <?php
                $pageMetaTag = App\Page::where('url', '/attraction/'.$attraction->slug)->first();
                $isTherePageMetaTagTranslation = App\PageMetaTagsTrans::where('pageID', $pageMetaTag->id ?? null)->where('languageID', $language->id)->first();
                if ($isTherePageMetaTagTranslation) {
                    $title = $isTherePageMetaTagTranslation->title;
                    $description = $isTherePageMetaTagTranslation->description;
                    $keywords = $isTherePageMetaTagTranslation->keywords;
                }
                else {
                    $title = $pageMetaTag->title ?? '';
                    $description = $pageMetaTag->description ?? '';
                    $keywords = $pageMetaTag->keywords ?? '';
                }


            ?>
        @endunless

        <script type="application/ld+json">
            {
              "@context" : "http://schema.org",
              "@type" : "Product",
              @if($pageMetaTag)
                "name" : "{{$title}}",
                "description" : "{{$description}}",
              @endif
            "image" : "{{asset('img/eiffel-tower-attraction-banner.jpg')}}",
            "sameAs": [ "https://www.facebook.com/pariscitytours.fr/",
              "https://www.instagram.com/pariscitytours.fr/",
              "https://twitter.com/Parisviptrips" ]
          }
        </script>
        @if($pageMetaTag)
            <title>{{$title}}</title>
            <meta name="description" content="{{$description}}">
            <meta name="keywords" content="{{$keywords}}">
            <meta property=”og:title” content="{{$title}}" />
            <meta property=”og:type” content=”website” />
            <meta property=”og:description” content="{{$description}}" />
            <meta property="og:image:width" content="400" />
            <meta property="og:image:height" content="400" />
                <meta property="og:image:secure_url" content="{{asset('img/eiffel-tower-attraction-banner.jpg')}}" />
            <!-- Twitter cards -->
            <meta name="twitter:title" content="{{$title}}" />
            <meta name="twitter:description" content="{{$description}}" />
            <meta name="twitter:site" content="@parisviptrips" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:image" content="{{asset('img/eiffel-tower-attraction-banner.jpg')}}" />

        @else
            <title>Paris City Tours</title>
            <meta name="description" content="Paris City Tours">
            <meta name="keywords" content="Paris City Tours">
        @endif

        <style>
            .strikeout {position: relative;color: black;}
            .strikeout::after {border-bottom: .2em solid #ffae0d;content: "";left: 35%;
                line-height: .2em;margin-top: calc(0.125em / 2 * -1);position: absolute;
                width: 30%;right: 0;top: 50%;}
        </style>
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <style>
            .ap-dropbtn:after {
                display:block!important;
                content: ''!important;
                border-bottom: solid 3px #f4364f!important;
                transform: scaleX(0)!important;
                transition: transform 250ms ease-in-out!important;
            }

            .ap-dropbtn:hover:after {
                transform: scaleX(1)!important;
            }

            .ap-dropbtn {
                background-color: #f2f1f1!important;
                color: #1a2b50!important;
                padding: 2px!important;
                font-size: 13px!important;
                border: none!important;
            }

            .ap-dropdownprod {
                position: relative!important;
                display: inline-block!important;
            }

            .ap-dropdown-content {
                display: none!important;
                position: absolute!important;
                background-color: #f1f1f1!important;
                min-width: 180px!important;
                z-index: 1!important;
                opacity: 1!important;
            }

            .ap-dropdown-content span {
                color: #1a2b50 !important;
                padding: 12px 16px!important;
                text-decoration: none!important;
                display: block!important;
                cursor: pointer!important;
            }

            .ap-dropdown-content span:hover {background-color: #ddd!important;}

            .ap-dropdownprod:hover .ap-dropdown-content {display: block!important;}

            .ap-dropdownprod:hover .ap-dropbtn {background-color: #f2f1f1!important;}

        </style>

    @elseif($page == 'search')
        <meta name="robots" content="noindex">
        <style>
            .strikeout {position: relative;color: black;}
            .strikeout::after {border-bottom: .2em solid #ffae0d;content: "";left: 35%;
                line-height: .2em;margin-top: calc(0.125em / 2 * -1);position: absolute;
                width: 30%;right: 0;top: 50%;}
        </style>
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <style>
            .ap-dropbtn:after {
                display:block!important;
                content: ''!important;
                border-bottom: solid 3px #f4364f!important;
                transform: scaleX(0)!important;
                transition: transform 250ms ease-in-out!important;
            }

            .ap-dropbtn:hover:after {
                transform: scaleX(1)!important;
            }

            .ap-dropbtn {
                background-color: #f2f1f1!important;
                color: #1a2b50!important;
                padding: 2px!important;
                font-size: 13px!important;
                border: none!important;
            }

            .ap-dropdownprod {
                position: relative!important;
                display: inline-block!important;
            }

            .ap-dropdown-content {
                display: none!important;
                position: absolute!important;
                background-color: #f1f1f1!important;
                min-width: 180px!important;
                z-index: 1!important;
                opacity: 1!important;
            }

            .ap-dropdown-content span {
                color: #1a2b50 !important;
                padding: 12px 16px!important;
                text-decoration: none!important;
                display: block!important;
                cursor: pointer!important;
            }

            .ap-dropdown-content span:hover {background-color: #ddd!important;}

            .ap-dropdownprod:hover .ap-dropdown-content {display: block!important;}

            .ap-dropdownprod:hover .ap-dropbtn {background-color: #f2f1f1!important;}

        </style>
    @elseif($page == 'special-offers')
        @if($pageMetaTag)
            <title>{{$title}}</title>
            <meta name="description" content="{{$description}}">
            <meta name="keywords" content="{{$keywords}}">
            <meta property=”og:title” content="{{$title}}" />
            <meta property=”og:type” content=”website” />
            <meta property=”og:description” content="{{$description}}" />
            <meta property="og:image:width" content="400" />
            <meta property="og:image:height" content="400" />
            <!-- Twitter cards -->
            <meta name="twitter:title" content="{{$title}}" />
            <meta name="twitter:description" content="{{$description}}" />
            <meta name="twitter:site" content="@parisviptrips" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:image" content="{{asset('img/paris-city-tours-logo.png')}}" />

        @else
            <title>Paris City Tours Special Offers</title>
            <meta name="description" content="Paris City Tours Special Offers">
            <meta name="keywords" content="Paris City Tours Special Offers">
        @endif
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        <style>
            .strikeout {position: relative;color: black;}
            .strikeout::after {border-bottom: .2em solid #ffae0d;content: "";left: 35%;
                line-height: .2em;margin-top: calc(0.125em / 2 * -1);position: absolute;
                width: 30%;right: 0;top: 50%;}
        </style>
    @elseif ($page == 'login' || $page == 'register' || $page == 'booking-confirmation' || $page == 'check-booking')
    <link rel="stylesheet" href="{{asset('css/admin/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/main/auth-styles.css')}}">
    <style>
        .toast-alert {
            background-color: #e57373!important;
            font-size: 14px;
        }
        .toast-success{
            background-color: #0f9d58!important;
            font-size: 14px;
        }
    </style>
        @if($page == 'check-booking')
            <title>{{__('checkBooking')}}</title>
            <style>
                @media only screen and (min-width: 768px) {
                    #voucherUrl button, #invoiceUrl button {
                      width: 50%;
                      margin-left: 25%;
                    }
                }

                #cover-spin {
                    position:fixed;
                    width:100%;
                    left:0;right:0;top:0;bottom:0;
                    background-color: rgba(255,255,255,0.8);
                    z-index:9999;
                    display:none;
                }

                @-webkit-keyframes spin {
                    from {-webkit-transform:rotate(0deg);}
                    to {-webkit-transform:rotate(360deg);}
                }

                @keyframes spin {
                    from {transform:rotate(0deg);}
                    to {transform:rotate(360deg);}
                }

                #cover-spin::after {
                    content:'';
                    display:block;
                    position:absolute;
                    left:48%;top:40%;
                    width:40px;height:40px;
                    border-style:solid;
                    border-color:black;
                    border-top-color:transparent;
                    border-width: 4px;
                    border-radius:50%;
                    -webkit-animation: spin .8s linear infinite;
                    animation: spin .8s linear infinite;
                }
            </style>
        @endif
    @elseif ($page == 'credit-card-details')
        <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">
        <link rel="stylesheet" href="{{asset('css/admin/style.css')}}">
        <style>
            .toast-alert {
                background-color: #e57373!important;
                font-size: 14px;
            }
            .toast-success{
                background-color: #0f9d58!important;
                font-size: 14px;
            }
            .suggestions-container {
                display: none;
                position: absolute;
                top: 43px;
                z-index: 100;
                background-color: #fff;
                border: 1px solid #c6c8d0;
                max-height: 400px;
                overflow-y: auto;
                overflow-x: hidden;
                min-width: 320px;
                text-align: left;
            }
        </style>

    @elseif ($page == 'checkout' || $page == 'external-payment-details')
    <script>
        // That script must be on head part of the page
        window.history.forward();
        function noBack() {
            window.history.forward();
        }
    </script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">
    <style>
        .dangerr{
            background-color: #FADBD8 !important;
        }
        .country-code-empty-border{
            border: 1px solid #900000!important;
            border-radius: 4px!important;
        }
        .toast-alert {
            background-color: #e57373!important;
            font-size: 14px;
        }
        .toast-success{
            background-color: #0f9d58!important;
            font-size: 14px;
        }
    </style>
    @elseif ($page == 'cart')
    <style>
        .toast-alert{
            background-color: #e57373!important;
            font-size: 14px;
        }

        .toast-success{
            background-color: #0f9d58!important;
            font-size: 14px;
        }

    </style>
    @elseif($page == 'cities')
    <style>
        .cards {
            display: flex;
            display: -webkit-flex;
            justify-content: center;
            -webkit-justify-content: center;
            max-width: 820px;
        }

        .card--1 .card__img, .card--1 .card__img--hover {
            background-image: url('https://images.pexels.com/photos/45202/brownie-dessert-cake-sweet-45202.jpeg?auto=compress&cs=tinysrgb&h=750&w=1260');
        }

        .card--2 .card__img, .card--2 .card__img--hover {
            background-image: url('https://images.pexels.com/photos/307008/pexels-photo-307008.jpeg?auto=compress&cs=tinysrgb&h=750&w=1260');
        }

        .card__like {
            width: 18px;
        }

        .card__clock {
            width: 15px;
            vertical-align: middle;
            fill: #AD7D52;
        }
        .card__time {
            font-size: 12px;
            color: #AD7D52;
            vertical-align: middle;
            margin-left: 5px;
        }

        .card__clock-info {
            float: right;
        }

        .card__img {
            visibility: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 100%;
            height: 235px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;

        }

        .card__info-hover {
            position: absolute;
            padding: 16px;
            width: 100%;
            opacity: 0;
            top: 0;
        }

        .card__img--hover {
            transition: 0.2s all ease-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 100%;
            position: absolute;
            height: 235px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            top: 0;

        }
        .card {
            margin-right: 25px;
            transition: all .4s cubic-bezier(0.175, 0.885, 0, 1);
            background-color: #fff;
            width: 100%;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 13px 10px -7px rgba(0, 0, 0,0.1);
        }
        .card:hover {
            box-shadow: 0px 30px 18px -8px rgba(0, 0, 0,0.1);
            transform: scale(1.10, 1.10);
        }

        .card__info {
            z-index: 2;
            background-color: #fff;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            padding: 16px 24px 24px 24px;
        }

        .card__category {
            font-family: 'Raleway', sans-serif;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 2px;
            font-weight: 500;
            color: #868686;
        }

        .card__title {
            margin-top: 5px;
            margin-bottom: 10px;
            font-family: 'Roboto Slab', serif;
        }

        .card__by {
            font-size: 12px;
            font-family: 'Raleway', sans-serif;
            font-weight: 500;
        }

        .card__author {
            font-weight: 600;
            text-decoration: none;
            color: #AD7D52;
        }

        .card:hover .card__img--hover {
            height: 100%;
            opacity: 0.3;
        }

        .card:hover .card__info {
            background-color: transparent;
            position: relative;
        }

        .card:hover .card__info-hover {
            opacity: 1;
        }

    </style>
    @elseif($page == 'home')
        <?php
        $languages = \App\Language::where('isActive', '=', 1)->where('id', '!=', 1)->get();
        ?>
        <meta name="google-site-verification" content="-UpSaeP0l2_xToAewDFympIA95LQ9hypg8kINC7stik" />
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <!-- <link rel="vahtml" href="https://www.cityzore.com/amp"> -->
            <link rel=”canonical” href="{{url()->current()}}"/>
            <link href="https://www.cityzore.com/" rel="alternate" hreflang="en" />
        @foreach($languages as $lan)
                <link href="https://www.cityzore.com/{{$lan->code}}" rel="alternate" hreflang="{{$lan->code}}" />
        @endforeach
            @if($pageMetaTag)
                <title>{{$title}}</title>
                <meta name="description" content="{{$description}}">
                <meta name="keywords" content="{{$keywords}}">
                <!-- OPENGRAPH -->
                <meta property="og:url" content="{{url()->current()}}" />
                <meta property="og:title" content="{{$title}}" />
                <meta property="og:type" content=”website” />
                <meta property="og:description" content="{{$description}}" />
                <meta property="og:image:secure_url" content="{{asset('img/eiffel-tower-attraction-banner.jpg')}}" />
                <meta property="og:image:width" content="400" />
                <meta property="og:image:height" content="300" />
                <!-- Twitter cards -->
                <meta name="twitter:title" content="{{$title}}" />
                <meta name="twitter:description" content="{{$description}}" />
                <meta name="twitter:site" content="@parisviptrips" />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:image" content="{{asset('img/eiffel-tower-attraction-banner.jpg')}}" />
            @endif

            <script type="application/ld+json">
                {
                  "@context": "https://schema.org/",
                  "@type": "TravelAgency",
                  "@image": "{{asset('img/eiffel-tower-attraction-banner.jpg')}}",
                  @if($pageMetaTag)
                    "name" : "{{$title}}",
                    "description" : "{{$description}}",
                  @endif
                  "sameAs": [ "https://www.facebook.com/pariscitytours.fr/",
                    "https://www.instagram.com/pariscitytours.fr/",
                    "https://twitter.com/Parisviptrips" ]
                }
            </script>
        <style>

            .strikeout {
                position: relative;
                font-size: 15px;
            }
            .strikeout::after {
                border-bottom: .2em solid #ffae0d;
                content: "";
                left: 0;
                line-height: .2em;
                margin-top: calc(0.125em / 2 * -1);
                position: absolute;
                right: 0;
                top: 50%;
            }
            .special-offer-price{
                font-size: 19px;
                color: #ffad0c;
            }
        </style>
    @elseif($page == 'home-amp')
        <meta name="google-site-verification" content="-UpSaeP0l2_xToAewDFympIA95LQ9hypg8kINC7stik" />
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <meta name="description" content="Search attractive things to do, find various activity packages and experience the most exciting tours at Paris. Paris City Tours">
        <title>Paris City Tours - Cityzore</title>
        <style>
            .tourz-sear-btn > input {
                width: 100%!important;
            }
            .searchInputNotValid {
                background-color: #ff8066!important;
            }
            .suggestions-container {
                display: none;
                position: absolute;
                top: 43px;
                z-index: 100;
                background-color: #fff;
                border: 1px solid #c6c8d0;
                max-height: 400px;
                overflow-y: auto;
                overflow-x: hidden;
                min-width: 320px;
                text-align: left;
            }
            .suggestion-item {
                border-top: 1px solid #c6c8d0;
                min-height: 40px;
                cursor: pointer;
                min-width:320px;
                padding:10px;
            }
            .suggestion-item:hover {
                background-color: #1593ff!important;
                color: #fff!important;
            }
            .strikeout {
                position: relative;
                font-size: 15px;
            }
            .strikeout::after {
                border-bottom: .2em solid #ffae0d;
                content: "";
                left: 0;
                line-height: .2em;
                margin-top: calc(0.125em / 2 * -1);
                position: absolute;
                right: 0;
                top: 50%;
            }
            .special-offer-price{
                font-size: 19px;
                color: #f4364f;
            }
        </style>
    @elseif ($page == 'email' || $page == 'reset')
        <style>
            .toast-alert {
                background-color: #e57373!important;
                font-size: 14px;
            }
            .toast-success{
                background-color: #0f9d58!important;
                font-size: 14px;
            }
        </style>
    @elseif($page == 'booking-successful')
    <!-- Event snippet for Eiffel Summit Satın alma Mart 2020 conversion page -->
        <script>
            gtag('event', 'conversion', {
                'send_to': 'AW-1009135479/OIhYCL-dxcgBEPfemOED',
                'transaction_id': ''
            });
        </script>

    @elseif ($page == 'become-a-commissioner')
        <link rel="stylesheet" href="{{asset('css/main/become-a-supplier-styles.css')}}">
        <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">
        <style>
        .toast-alert {
            background-color: #e57373!important;
            font-size: 14px;
        }
        .toast-success{
            background-color: #0f9d58!important;
            font-size: 14px;
        }
        </style>


  @elseif ($page == 'commissions')

  <style>

    .affiliated{
        background: #E8DAEF;
    }

    table > thead{
        cursor: pointer;
        border-top: solid 1px #ABB2B9;
        border-bottom: solid 1px #fafafa;
        transition: all .3s;
    }
      table > thead:hover{
        background-color: #808B96 !important;
        transform: scale(1.05);
      }


      table > tbody{
        display: none;


      }
  </style>

    @elseif($page == 'faq')
    <?php
        $frequentlyAskedQuestions = \App\FAQ::with('translate')->get();
        ?>

        <script type="application/ld+json">
            {
                "@context":"https://schema.org",
                "@type":"FAQPage",
                "mainEntity":[
                    @foreach($frequentlyAskedQuestions as $faq)
                        @if(! is_null($faq->translate))
                            @php
                                $faq = $faq->translate;
                            @endphp
                            {
                            "@type":"Question",
                            "name":"{{$faq->question}}",
                                    "acceptedAnswer":{
                                    "@type":"Answer",
                                    "text":"{{$faq->answer}}"
                                    }
                                },
                            @else
                            {
                                "@type":"Question",
                                "name":"{{$faq->question}}",
                                "acceptedAnswer":{
                                "@type":"Answer",
                                "text":"{{$faq->answer}}"
                                }
                            },
                        @endif
                    @endforeach
                            {
                                "@type":"Question",
                                "name":"Is it cheaper to buy Eiffel Tower tickets online?",
                                "acceptedAnswer":{
                                "@type":"Answer",
                                "text":"You can find online the same price that if you buy the tickets the day of your visit a desk.  For this reason, if you buy tickets online, you will be advantageous nevertheless waiting in line and your ticket will be skipping the line."
                                }
                            }
                ]
            }
        </script>

    @elseif($page == 'blog')
        <style>
            /* Blog CSS */


            /*.post-module {*/
            /*    position: relative;*/
            /*    z-index: 1;*/
            /*    display: block;*/
            /*    background: #FFFFFF;*/
            /*    min-width: 270px;*/
            /*    height: 470px;*/
            /*    -webkit-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.15);*/
            /*    -moz-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.15);*/
            /*    box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.15);*/
            /*    -webkit-transition: all 0.3s linear 0s;*/
            /*    -moz-transition: all 0.3s linear 0s;*/
            /*    -ms-transition: all 0.3s linear 0s;*/
            /*    -o-transition: all 0.3s linear 0s;*/
            /*    transition: all 0.3s linear 0s;*/
            /*    margin-bottom: 30px;*/
            /*}*/
            /*.post-module:hover,*/
            /*.hover {*/
            /*    -webkit-box-shadow: 0px 1px 35px 0px rgba(0, 0, 0, 0.3);*/
            /*    -moz-box-shadow: 0px 1px 35px 0px rgba(0, 0, 0, 0.3);*/
            /*    box-shadow: 0px 1px 35px 0px rgba(0, 0, 0, 0.3);*/
            /*}*/
            /*.post-module:hover .thumbnail img,*/
            /*.hover .thumbnail img {*/
            /*    -webkit-transform: scale(1.1);*/
            /*    -moz-transform: scale(1.1);*/
            /*    transform: scale(1.1);*/
            /*    opacity: 0.6;*/

            /*}*/
            /*.post-module .thumbnail {*/
            /*    padding: 0;*/
            /*    background: #000000;*/
            /*    overflow: hidden;*/
            /*}*/
            /*.post-module .post-content .date {*/
            /*    position: absolute;*/
            /*    top: -70px;*/
            /*    right:150px;*/
            /*    z-index: 1;*/
            /*    background: #e74c3c;*/
            /*    width: 55px;*/
            /*    height: 55px;*/
            /*    padding: 12.5px 0;*/
            /*    -webkit-border-radius: 100%;*/
            /*    -moz-border-radius: 100%;*/
            /*    border-radius: 100%;*/
            /*    color: #FFFFFF;*/
            /*    font-weight: 700;*/
            /*    -webkit-box-sizing: border-box;*/
            /*    -moz-box-sizing: border-box;*/
            /*    box-sizing: border-box;*/
            /*}*/
            /*.post-content .date .day {*/
            /*    margin-top: -5px;*/
            /*    font-size: 18px;*/
            /*}*/
            /*.post-content .date .month {*/
            /*    font-size: 12px;*/
            /*    text-transform: uppercase;*/
            /*}*/
            /*.post-module .thumbnail img {*/
            /*    border: none;*/
            /*    display: block;*/
            /*    width: 100%;*/
            /*    max-height: 230px;*/
            /*    -webkit-transition: all 0.3s linear 0s;*/
            /*    -moz-transition: all 0.3s linear 0s;*/
            /*    -ms-transition: all 0.3s linear 0s;*/
            /*    -o-transition: all 0.3s linear 0s;*/
            /*    transition: all 0.3s linear 0s;*/
            /*}*/
            /*.post-module .post-content {*/
            /*    position: absolute;*/
            /*    bottom: 0;*/
            /*    background: #FFFFFF;*/
            /*    width: 100%;*/
            /*    padding: 30px;*/
            /*    -webkti-box-sizing: border-box;*/
            /*    -moz-box-sizing: border-box;*/
            /*    box-sizing: border-box;*/
            /*    -webkit-transition: all 0.3s cubic-bezier(0.37, 0.75, 0.61, 1.05) 0s;*/
            /*    -moz-transition: all 0.3s cubic-bezier(0.37, 0.75, 0.61, 1.05) 0s;*/
            /*    -ms-transition: all 0.3s cubic-bezier(0.37, 0.75, 0.61, 1.05) 0s;*/
            /*    -o-transition: all 0.3s cubic-bezier(0.37, 0.75, 0.61, 1.05) 0s;*/
            /*    transition: all 0.3s cubic-bezier(0.37, 0.75, 0.61, 1.05) 0s;*/
            /*}*/
            /*.post-module .post-content .title {*/
            /*    margin: 0;*/
            /*    padding: 0 0 10px;*/
            /*    color: #333333;*/
            /*    font-size: 26px;*/
            /*    font-weight: 700;*/
            /*}*/
            /*.post-module .post-content .sub_title {*/
            /*    margin: 0;*/
            /*    padding: 0 0 20px;*/
            /*    color: #e74c3c;*/
            /*    font-size: 20px;*/
            /*    font-weight: 400;*/
            /*}*/
            /*.post-module .post-content .description {*/
            /*    display: none;*/
            /*    color: #666666;*/
            /*    font-size: 14px;*/
            /*    line-height: 1.8em;*/
            /*}*/
            /*.post-module .post-content .post-meta {*/
            /*    margin: 30px 0 0;*/
            /*    color: #999999;*/
            /*}*/
            /*.post-module .post-content .post-meta .timestamp {*/
            /*    margin: 0 16px 0 0;*/
            /*}*/
            /*.post-module .post-content .post-meta a {*/
            /*    color: #999999;*/
            /*    text-decoration: none;*/
            /*}*/
            /*.hover .post-content .description {*/
            /*    display: block !important;*/
            /*    height: auto !important;*/
            /*    opacity: 1 !important;*/
            /*}*/

            /*.hover .post-content .date{*/
            /*    opacity: 0 !important;*/
            /*}*/

            /*.post-card{*/
            /*    padding: 3%;*/
            /*}*/

            /*.post-module .post-content .author{*/
            /*    display: none;*/
            /*    position: absolute;*/
            /*    top: -34px;*/
            /*    right:1px;*/
            /*    z-index: 1;*/
            /*    background: #e74c3c;*/
            /*    padding: 5px 12px;*/
            /*    max-width: 200px;*/
            /*    color: #FFFFFF;*/
            /*    font-weight: 700;*/
            /*    text-align: center;*/
            /*    -webkit-box-sizing: border-box;*/
            /*    -moz-box-sizing: border-box;*/
            /*    box-sizing: border-box;*/
            /*}*/

            /*.hover .post-content .author{*/
            /*    display:block!important;*/
            /*}*/

            .utilities h3 {
                margin: 0;
                padding-bottom: 10px;
                border-bottom: 3px solid #e74c3c;
                text-align: center;
                margin-bottom: 20px;
                color: #000;
            }

            .utilities ul {
                display: flex;
                flex-direction: column;
                padding: 0;
                list-style: none;
                width: 100%;
            }

            .utilities li {
                width: 100%;
                padding: 5px 10px;
            }

            .utilities li a {
                width: 100%;
                color: #2d5d73;
                font-weight: 500;
            }

            .categories li a.active::after{
                width: 30%;
            }

            .categories li a::after {
                content: '';
                width: 0;
                height: 3px;
                background: #e85242;
                display: block;
                border-radius: 8px;
                transition: .3s ease-in-out;
            }

            .categories li:hover a:after {
                width: 30%;
            }

            .attractions ul li a {
                display: flex;
                width: 100%;
                flex-direction: column;
            }

            .attractions ul li a img {
                width: 100%;
                object-fit: cover;
                border-radius: 8px;
            }
            .attraction-thumb {
                width: 100%;
                margin-bottom: 10px;
                position: relative;
                min-height: 150px;
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: #fff;
                overflow: hidden;
            }

            .attraction-thumb p{
                z-index: 1;
                font-size: 16px;
                font-weight: bold;
            }

            .attraction-thumb::after{
                content: '';
                width: 100%;
                height: 100%;
                position: absolute;
                background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.6) 26%, rgba(0,0,0,0.5) 48%, rgba(255,255,255,0.49763655462184875) 100%);
                z-index: 0;
            }

            .attraction-thumb .activity {
                right: 0;
                bottom: 0;
                padding: 5px;
                font-size: 12px;
                line-height: 1;
                z-index: 1;
            }

            .paginate-area {
                text-align: center;
                margin-top: 45px;
            }
            .post-item {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                border: 1px solid #d9d9d9;
                border-radius: 8px;
                overflow: hidden;
                max-height: 500px;
            }

            .post-image {
                width: 100%;
                min-height: 230px;
                overflow: hidden;
                position: relative;
            }

            a.post-item:hover .post-image img {
                transform: scale(1.1);
            }

            .post-image img {
                width: 100%;
                height: 100%;
                object-fit: fill;
                transition: .3s ease-in-out all;
            }

            .post-content {
                display: flex;
                flex-direction: column;
                padding: 10px;
                color: #000;
                height: 100%;
            }

            .post-info {
                display: flex;
                justify-content: center;
                gap: 15px;
            }

            .date {
                position: absolute;
                right: 10px;
                bottom: 10px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: #e74c3c;
                justify-content: center;
                align-items: center;
                display: flex;
                flex-direction: column;
                color: #fff;
                font-weight: bold;
                font-size: 12px;
            }

            .date span {
                line-height: 1;
            }

            .post-content.post-title {
                margin-top: 20px;
            }

            p.post-title {
                overflow-wrap: break-word;
                margin-top: 20px;
                text-align: center;
                font-size: 17px;
                font-weight: bold;
                color: #2d5d73;
            }

            .description {
                text-align: center;
                margin-bottom: auto;
                margin-top: 20px;
            }

        </style>

    @elseif($page == 'blog-inner')

        <style>
            .mobile-content img {
                max-width: 100%;
            }

            img{
                width: 100%;
                object-fit: cover;
            }
        </style>
        <meta name="description" content="{{$blogPost->metaTag()->first()->description}}">
    <title>{{$blogPost->metaTag()->first()->title}}</title>
        <meta name="keywords" content="{{$blogPost->metaTag()->first()->keywords}}">

    @elseif($page == 'successful-register')

    @elseif($page == 'successful-verification')



    @else
        @if($pageMetaTag)
            <title>{{$title}}</title>
            <meta name="description" content="{{$description}}">
            <meta name="keywords" content="{{$keywords}}">
            <meta property=”og:title” content="{{$title}}" />
            <meta property=”og:type” content=”website” />
            <meta property=”og:description” content="{{$description}}" />
            <meta property="og:image:width" content="400" />
            <meta property="og:image:height" content="400" />
            <!-- Twitter cards -->
            <meta name="twitter:title" content="{{$title}}" />
            <meta name="twitter:description" content="{{$description}}" />
            <meta name="twitter:site" content="@parisviptrips" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:image" content="{{asset('img/paris-city-tours-logo.png')}}" />
            <script type="application/ld+json">
            {
              "@context" : "http://schema.org",
              "@type" : "Product",
              @if($pageMetaTag)
                    "name" : "{{$title}}",
                "description" : "{{$description}}",
              @endif
                "sameAs": [ "https://www.facebook.com/pariscitytours.fr/",
                  "https://www.instagram.com/pariscitytours.fr/",
                  "https://twitter.com/Parisviptrips" ]
              }
            </script>
        @else
            <title>Paris City Tours</title>
            <meta name="description" content="Paris City Tours">
            <meta name="keywords" content="Paris City Tours">
        @endif

    @endif

</head>
<body>
