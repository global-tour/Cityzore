<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<table id="cartTable" class="table">

</table>
<div class="mm1-com-cart mm1-s1" style="text-align: center; display: none;">
    <div class="ed-course-in">
        <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('cart'))}}" class="btn btn-success" style="width:100%;background-color: #2d5d73; line-height: 0px; color: white; border-color: #2d5d73; margin-bottom:5px;">
            {{__('goToCart')}}
        </a>
        <br>
        <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('checkout'))}}" class="btn btn-success" style="width:100%;background-color: #f4364f; line-height: 0px; color: white; border-color: #f4364f;">
            {{__('checkOut')}}
        </a>
    </div>
</div>




