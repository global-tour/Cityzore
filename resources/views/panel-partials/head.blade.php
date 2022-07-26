
<!DOCTYPE html>
<html style="height: 100%;" lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="top">

<head>
    <title>Cityzore Panel</title>
    <!--== META TAGS ==-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--== FAV ICON ==-->
    <link rel="shortcut icon" href="{{asset('img/fav.ico')}}">

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Quicksand:300,400,500" rel="stylesheet">

    <!-- FONT-AWESOME ICON CSS -->

    <!--== ALL CSS FILES ==-->
    <link rel="stylesheet" href="{{asset('css/admin/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/font/cityzore.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/mob.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/materialize.css')}}">

    <style>
        .toast-alert {
            background-color: #e57373!important;
        }

        .toast-success {
            background-color: #43a047!important;
        }
    </style>
    @if($page == 'supplier-index' || $page == 'restaurants-index')
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/admin/jquery.modal.min.css')}}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />

        <style>
        .btn, .btn-large{
            font-weight: lighter;
        }
        .select2{
            width: 100%!important;
            font-size: 14px;
        }
        form button{
            background: none;
            border: none;
            float: left;
        }
        form button:active{
            background: none;
            border: none;
            float: left;
        }
        .toggle-on.btn {
            padding-right: 180px!important;
        }
        .toggle-on {
            position: relative!important;
            top: 0!important;
            bottom: 0!important;
            left: 0!important;
            right: 100%!important;
            margin: 0!important;
            border: 0!important;
            width: 100%!important;
            border-radius: 0!important;
        }
        .toggle{
            width: 120px!important;
        }
        .btn, .btn-large{
            font-weight: lighter!important;
        }

        .toggle-off.btn {
            padding-left: 15px!important;
        }
    </style>
    @elseif($page == 'attraction-edit' || $page == 'attraction-create')
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <style>
            .border-col {
                border: 1px solid #e2dddd;
                padding: 2%;
            }
            .tagify__input, .tagify__tag>div>*{
                font-size: 16px !important;
            }

        </style>
    @elseif($page == 'language-edit')
    <style>
        .disabledButton {
            background: #eaeaea;
            color: #c2c2c2;
        }
    </style>
    @elseif($page == 'language-index')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .toggle-on.btn {
            padding-right: 180px;

        }
        .toggle.btn{width: 20px}
        .toggle-on {
            position: relative;
            top: 0;
            bottom: 0;
            left: 0;
            right: 100%;
            margin: 0;
            border: 0;
            width: 100%;
            border-radius: 0;
        }
        .toggle{width: 50%}
        .btn, .btn-large{
            font-weight: lighter;
        }

        .toggle-off.btn {
            padding-left: 15px;
        }
    </style>
    @elseif($page == 'product-editpct')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
    <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
    <style>
        .tagify__tag-text{
            color: black!important;
            font-size: 14px!important;
        }

        #highlights::placeholder, #knowBeforeYouGo::placeholder, #included::placeholder, #notIncluded::placeholder, #tags_1::placeholder{
            color: black!important;
            font-weight: bolder!important;
        }

        .tagify{
            height: auto!important;
        }

        .ke-powered-by{
            display: none;
        }

        .tagify__tag>div>* {
            text-overflow: initial!important;
        }
    </style>
    @elseif($page == 'product-editpctcom')
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <style>
            .tagify__tag-text{
                color: black!important;
                font-size: 14px!important;
            }

            #highlights::placeholder, #knowBeforeYouGo::placeholder, #included::placeholder, #notIncluded::placeholder, #tags_1::placeholder{
                color: black!important;
                font-weight: bolder!important;
            }

            .tagify{
                height: auto!important;
            }

            .ke-powered-by{
                display: none;
            }

            .tagify__tag>div>* {
                text-overflow: initial!important;
            }
        </style>
    @elseif($page == 'product-editctp')
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <style>
            .tagify__tag-text{
                color: black!important;
                font-size: 14px!important;
            }

            #highlights::placeholder, #knowBeforeYouGo::placeholder, #included::placeholder, #notIncluded::placeholder, #tags_1::placeholder{
                color: black!important;
                font-weight: bolder!important;
            }

            .tagify{
                height: auto!important;
            }

            .ke-powered-by{
                display: none;
            }

            .tagify__tag>div>* {
                text-overflow: initial!important;
            }
        </style>

    @elseif($page == 'product-indexpct' || $page == 'option-indexpct')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
   <style>

         .popup {
            position: relative;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .popup:hover .popuptext{
            visibility: visible;
        }

        /* The actual popup */
        .popup .popuptext {
            word-wrap: break-spaces;
            white-space: normal;
            line-break: initial;
            visibility: hidden;
            width: 400px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 2px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -80px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 20%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        form button{
            background: none;
            border: none;
            float: left;
        }
        form button:active{
            background: none;
            border: none;
            float: left;
        }
        .toggle-on.btn {
            padding-right: 180px!important;
        }
        .toggle-on {
            position: relative!important;
            top: 0!important;
            bottom: 0!important;
            left: 0!important;
            right: 100%!important;
            margin: 0!important;
            border: 0!important;
            width: 100%!important;
            border-radius: 0!important;
        }
        .toggle{
            width: 120px!important;
        }
        .btn, .btn-large{
            font-weight: lighter!important;
        }

        .toggle-off.btn {
            padding-left: 15px!important;
        }

        .connectedToApi {
            background: #dc3545!important;
        }

        .disconnectedToApi {
            background: #28a745!important;
        }
        .more-info {
            display: none;
        }


   </style>


       @elseif($page == 'product-indexpctcom' || $page == 'option-indexpctcom')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
   <style>

         .popup {
            position: relative;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .popup:hover .popuptext{
            visibility: visible;
        }

        /* The actual popup */
        .popup .popuptext {
            word-wrap: break-spaces;
            white-space: normal;
            line-break: initial;
            visibility: hidden;
            width: 400px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 2px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -80px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 20%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        form button{
            background: none;
            border: none;
            float: left;
        }
        form button:active{
            background: none;
            border: none;
            float: left;
        }
        .toggle-on.btn {
            padding-right: 180px!important;
        }
        .toggle-on {
            position: relative!important;
            top: 0!important;
            bottom: 0!important;
            left: 0!important;
            right: 100%!important;
            margin: 0!important;
            border: 0!important;
            width: 100%!important;
            border-radius: 0!important;
        }
        .toggle{
            width: 120px!important;
        }
        .btn, .btn-large{
            font-weight: lighter!important;
        }

        .toggle-off.btn {
            padding-left: 15px!important;
        }

        .connectedToApi {
            background: #dc3545!important;
        }

        .disconnectedToApi {
            background: #28a745!important;
        }
        .more-info {
            display: none;
        }


   </style>


       @elseif($page == 'product-indexctp' || $page == 'option-indexctp')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
   <style>

         .popup {
            position: relative;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .popup:hover .popuptext{
            visibility: visible;
        }

        /* The actual popup */
        .popup .popuptext {
            word-wrap: break-spaces;
            white-space: normal;
            line-break: initial;
            visibility: hidden;
            width: 400px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 2px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -80px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 20%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        form button{
            background: none;
            border: none;
            float: left;
        }
        form button:active{
            background: none;
            border: none;
            float: left;
        }
        .toggle-on.btn {
            padding-right: 180px!important;
        }
        .toggle-on {
            position: relative!important;
            top: 0!important;
            bottom: 0!important;
            left: 0!important;
            right: 100%!important;
            margin: 0!important;
            border: 0!important;
            width: 100%!important;
            border-radius: 0!important;
        }
        .toggle{
            width: 120px!important;
        }
        .btn, .btn-large{
            font-weight: lighter!important;
        }

        .toggle-off.btn {
            padding-left: 15px!important;
        }

        .connectedToApi {
            background: #dc3545!important;
        }

        .disconnectedToApi {
            background: #28a745!important;
        }
        .more-info {
            display: none;
        }


   </style>


    @elseif($page == 'product-index' || $page == 'option-index')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="http://jschr.github.io/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet">
    <style>
        .popup {
            position: relative;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .popup:hover .popuptext{
            visibility: visible;
        }

        /* The actual popup */
        .popup .popuptext {
            word-wrap: break-spaces;
            white-space: normal;
            line-break: initial;
            visibility: hidden;
            width: 400px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 2px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -80px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 20%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        form button{
            background: none;
            border: none;
            float: left;
        }
        form button:active{
            background: none;
            border: none;
            float: left;
        }
        .toggle-on.btn {
            padding-right: 180px!important;
        }
        .toggle-on {
            position: relative!important;
            top: 0!important;
            bottom: 0!important;
            left: 0!important;
            right: 100%!important;
            margin: 0!important;
            border: 0!important;
            width: 100%!important;
            border-radius: 0!important;
        }
        .toggle{
            width: 120px!important;
        }
        .btn, .btn-large{
            font-weight: lighter!important;
        }

        .toggle-off.btn {
            padding-left: 15px!important;
        }

        .connectedToApi {
            background: #dc3545!important;
        }

        .disconnectedToApi {
            background: #28a745!important;
        }
        .more-info {
            display: none;
        }
    </style>
    @elseif($page == 'special-offers-create')
    <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <style>
        .foo { color: #808080; text-size: smaller; }
        .select2-search--dropdown .select2-search__field{
            height: 30px;
        }
    </style>
    @elseif($page == 'commissioners-edit')
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <style>
        .foo { color: #808080; text-size: smaller; }
        .select2-search--dropdown .select2-search__field{
            height: 30px;
        }
    </style>
    @elseif($page == 'tickets-create')
    <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <style>
        .foo { color: #808080; text-size: smaller; }
        .select2-search--dropdown .select2-search__field{
            height: 30px;
        }

        #typeSelect{
            border: 1px solid #aaa;
            height: 28px;
            line-height: 28px;
            font-size: 14px;
            border-radius: 4px;
        }
    </style>
    @elseif($page == 'bookings-create' || $page == 'bookings-edit')
    <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">

    @elseif($page == 'meetings-index')
    <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">







    @elseif($page == 'finance-bills')
    <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css" integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA==" crossorigin="anonymous" />



      @elseif($page == 'guides-index')

    <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
{{--    <link href="{{asset('css/jquery.timepicker.css')}}" rel="stylesheet" type="text/css">--}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    @elseif($page == 'guides-planning')

    {{-- <link href="{{asset('/calendar/css/bootstrap.css')}}" rel="stylesheet"> --}}
    <link href="{{asset('/calendar/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('/calendar/css/smoothness/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('/calendar/css/fullcalendar.print.css')}}" media="print" rel="stylesheet">
    <link href="{{asset('/calendar/css/fullcalendar.css')}}" rel="stylesheet">
    <link href="{{asset('/calendar/lib/spectrum/spectrum.css')}}" rel="stylesheet">
    <link href="{{asset('/calendar/lib/timepicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet">

    @elseif($page == 'bookings-index')
    <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
        <style>

            .light_green{
             background: #D4EFDF;
            }

            .light_red{
             background: #FADBD8;
            }

             .light_red:hover{
                background: transparent;
            }

            .light_green:hover{
                background: transparent;
            }

              .light_red:active{
                background: transparent !important;
            }

            .light_green:active{
                background: transparent !important;
            }

            .extra-image-wrap{
                cursor: pointer;
                background: #f2f2f2;
            }
            .extra-image-wrap:hover{
                background: #ccc;
            }

            .extra-image-wrap label{
                cursor: pointer;
                transition: all ease 0.3s;
            }

            .extra-image-wrap:hover label{
                transform: scale(1.2);
                margin-left: 10px;
            }

           .invoice-check{
            display: block;
            transform: rotate(45deg);
            position: relative;
            width: 50px;
            left: -10px;
            transition: all ease 0.3s;
           }

           .invoice-check:hover{
            transform: scale(1.4) rotate(45deg);
            cursor: pointer;

           }


            .pending{
                background-color: #f0ad4e!important;
                color: white!important;
            }
            .canceled{
                background-color: #c9302c!important;
                color: white!important;
            }
            .active2{
                background-color: #449d44!important;
                color: white!important;
            }
            input[type='radio']:focus{
                background: none;
            }
            span.thumb, span.active2{
                display: none;}
            .tri-state-toggle {
                background: rgba(165,170,174,0.25);
                box-shadow: inset 0 2px 8px 0 rgba(165,170,174,0.25);
                border-radius: 24px;
                overflow: hidden;
                display: inline-flex;
                flex-direction: column;
            }

            .tri-state-toggle-button {
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 150px;
                background-color: transparent;
                border: 1px solid transparent;
                color: #727C8F;
                cursor: pointer;
                 -webkit-transition: all 0.5s ease-in-out;
                  -moz-transition:    all 0.5s ease-in-out;
                  -o-transition:      all 0.5s ease-in-out;
            }
            .tri-state-toggle-button:nth-child(1){
                border-bottom: 1px solid #CCCCCC;
                }
            .tri-state-toggle-button:nth-child(2){
                border-top: 1px solid #CCCCCC;
                border-bottom: 1px solid #CCCCCC;
            }
            .tri-state-toggle-button:nth-child(3){
                border-top:1px solid #CCCCCC;
            }
            .tri-state-toggle-button:focus {
                outline: none;
            }
            .select2-selection__choice {
                font-size: 13px;
            }
            .select2-selection__choice__remove {
                font-size: 15px;
            }

        </style>

    @elseif($page == 'bookings-v2-index')

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/staterestore/1.1.1/css/stateRestore.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.7/css/scroller.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('keditor/build/css/keditor.min.css')}}">
        <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
        <style>
            .dataTables_wrapper .dataTables_length{
                float: unset;
            }

            .dataTables_wrapper .dataTables_filter input{
                border: unset;
                border-bottom: 1px solid #26a69a;
            }

            .dataTables_wrapper .dataTables_filter {
                float: unset;
            }

            input[type="search"] {
                height: auto;
            }


            /*.pagination li.active {*/
            /*    background-color: unset;*/
            /*}*/

            /*.dataTables_wrapper .dataTables_paginate .paginate_button:hover{*/
            /*    color: unset;*/
            /*    border: unset;*/
            /*    background-color: unset;*/
            /*    background: unset;*/

            /*}*/
            /*.dataTables_wrapper .dataTables_paginate .paginate_button{*/
            /*    padding: unset;*/
            /*}*/
            /*.pagination li a {*/
            /*    font-size: 14px!important;*/
            /*}*/

            [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
                position: unset;
                opacity: 1;
                width: unset;
                height: unset;
                left: unset;
                cursor: pointer;
            }
            .table > thead > tr > th {
                vertical-align: middle;
            }

            label{
                display: flex;
                align-items: center;
                gap: 10px;
            }

            select{
                display: unset;
                width: auto;
            }
            select.mdb-select{
                display: none;
            }
            .dataTables_wrapper .dataTables_processing{
                top: 0!important;
                display: flex;
                justify-content: center;
                align-items: center;
                background: rgba(255, 255, 255, 0.7)!important;
                height: 100%!important;
                margin-top: 0!important;
            }
            .btn-clean {
                color: #B5B5C3 !important;
                background-color: transparent !important;
                border-color: transparent !important;
            }

            .btn-clean:hover, .btn-clean:focus, .btn-clean.focus {
               color: #3699FF !important;
               background-color: #F3F6F9 !important;
               border-color: transparent !important;
           }


            .nav-link {
                display: flex;
                align-items: center;
                transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
                padding: 0.75rem 1.5rem;
                color: #7E8299;
            }
            .nav-link .nav-icon {
                line-height: 0;
                color: #7E8299;
                width: 2rem;
            }
            .nav-link .nav-text {
                font-size: 12px;
            }

            .nav .show > .nav-link, .nav .nav-link:hover:not(.disabled), .nav .nav-link.active {
                transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
                color: #3699FF;
            }

            .table thead th {
                font-weight: 500;
                color: #B5B5C3 !important;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 0.1rem;
            }
            .table thead th.sorting_desc, .table thead th.sorting_asc {
                color: #3699FF !important;
                font-weight: 600;
            }

            .date-row {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 5px;
                background: #253d5214;
                margin: 0 auto;
                border-radius: 8px;
                min-width: 150px;
            }

            .date-row .month-container {
                width: 100%;
                text-align: center;
                padding: 6px 10px;
                /* display: inline-block; */
                box-sizing: border-box;
                color: #fff;
                font-weight: bold;
                font-size: 13px;
                border-radius: 6px 6px 0 0;
            }

            .date-row .day-container {
                font-size: 25px;
                font-weight: bold;
                color: #f23434;
                /* margin-bottom: 1px; */
            }

            .date-row .year-container {
                font-weight: bold;
                font-size: 13px;
            }

            .date-row .time-container {
                font-size: 12px;
                font-weight: bold;
                margin-bottom: 7px;
                text-align: center;
            }

            .select2-container{
                max-width: 300px!important;
            }

            /*th.actions {*/
            /*    display: flex;*/
            /*    flex-direction: column;*/
            /*    width: 100%;*/
            /*    flex: 1;*/
            /*    gap: 5px;*/
            /*}*/
            .responsive-table {
                width: 100%;
                overflow-x: auto;
                padding: 20px;
            }
            .active-booking{
                background-color: #daf1dbc7 !important
            }
            .booking-information-container {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: center;
            }

            .booking-information-container strong {
                font-size: 13px;
            }

            .booking-offcanvas {
                position: fixed;
                top: 0;
                right: -100%;
                width: 40%;
                background: #fff;
                display: block;
                height: 100vh;
                z-index: 9999;
                transition: .15s ease-in-out all;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .booking-offcanvas.active-canvas{
                right: 0;
            }

            .offcanvas-overlay {
                display: block;
                position: fixed;
                top: 0;
                right: -100%;
                width: 100%;
                height: 100vh;
                background: rgba(0,0,0, 0.3);
                z-index: 9998;
            }

            .offcanvas-overlay.active-overlay{
                right: 0;
            }
            .offcanvas-tools {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                font-size: 20px;
                border-bottom: 2px solid #d9d9d9;
            }

            .offcanvas-close-button {
                display: flex;
                border: 1px solid #ddd;
                border-radius: 6px;
                padding: 6px;
                color: #d9d9d9;
                cursor: pointer;
                transition: .15s ease-in-out all;
            }

            .offcanvas-close-button:hover {
                border-color: #000;
                color: #000;
            }
            .special-ref-code {
                color: #fff;
                padding: 5px;
                border-radius: 8px;
            }

            .offcanvas-body {
                padding: 20px 15px;
            }

            .tab-content {
                margin-top: 20px;
            }
            .mail-check-container {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: 5px;
            }

            .mail-check-container input[type="checkbox"] {
                display: none;
            }

            .file-actions{
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
            .search-introduction {
                display: flex;
                width: 100%;
                font-weight: bold;
                font-size: 11px;
                text-align: center;
                color: #7a7979;
            }

            .row.flex-vertical-centered {
                display: flex;
                align-items: center;
            }

            a.daterangepicker-delete-val {
                border: 1px solid #d9d9d9;
                margin-top: -15px;
                display: block;
                text-align: center;
                padding: 5px;
            }
            div.dt-buttons{
                display: flex;
                align-items: center;
            }
            .dt-buttons .dt-button{
                padding: 0 8px;
            }

            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
                vertical-align: middle;
            }

            .daterangepicker{
                z-index: 10001;
            }

            .status-button{
                display: flex;
                justify-content: center;
                align-items: center;
                width: 90%;
                min-height: 28px;
                margin: 0 auto;
                flex-direction: column;
                gap: 5px;
            }

            @media all and (max-width: 768px) {
                .booking-offcanvas{
                    width: 100%;
                }
            }

            @media (min-width: 768px) and (max-width: 1440px){
                .booking-offcanvas{
                    width: 70%;
                }
            }

            strong.select2-results__group {
                font-size: 13px;
                color: #000;
                background: #e2f4e3;
            }

            .expand-collapse-div {
                padding: 0!important;
                margin-left: 0!important;
                width: 100%!important;
            }

            .offcanvas-body ul.nav {
                display: flex;
                align-items: center;
                overflow-x: scroll;
                overflow-y: hidden;
            }

            .offcanvas-body ul li {
                flex-shrink: 0;
            }

            .offcanvas-body ul::-webkit-scrollbar {height: 5px;background-color: #614141;}

            .offcanvas-body ul::-webkit-scrollbar-track {-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);border-radius: 10px;background-color: #F5F5F5;}
        </style>
    @elseif($page == 'bulkmail')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('keditor/build/css/keditor.min.css')}}">


        {{--        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js" />--}}
{{--        <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />--}}

        <style>
            .dataTables_wrapper .dataTables_length{
                float: unset;
            }

            .dataTables_wrapper .dataTables_filter input{
                border: unset;
                border-bottom: 1px solid #26a69a;
            }

            .dataTables_wrapper .dataTables_filter {
                float: unset;
            }

            input[type="search"] {
                height: auto;
            }


            /*.pagination li.active {*/
            /*    background-color: unset;*/
            /*}*/

            /*.dataTables_wrapper .dataTables_paginate .paginate_button:hover{*/
            /*    color: unset;*/
            /*    border: unset;*/
            /*    background-color: unset;*/
            /*    background: unset;*/

            /*}*/
            /*.dataTables_wrapper .dataTables_paginate .paginate_button{*/
            /*    padding: unset;*/
            /*}*/
            /*.pagination li a {*/
            /*    font-size: 14px!important;*/
            /*}*/

            [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
                position: unset;
                opacity: 1;
                width: unset;
                height: unset;
                left: unset;
                cursor: pointer;
            }
            .table > thead > tr > th {
                vertical-align: middle;
            }

            label{
                display: flex;
                align-items: center;
                gap: 10px;
            }

            select{
                display: unset;
                width: auto;
            }
            select.mdb-select{
                display: none;
            }
        </style>
    @elseif($page == 'on-goings')
        <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    @elseif($page == 'availability-index' || $page == 'pricings-index')
    <style>
        button {
            background-color: transparent;
            border:none;
        }
         .popup {
             position: relative;
             cursor: pointer;
             -webkit-user-select: none;
             -moz-user-select: none;
             -ms-user-select: none;
             user-select: none;
         }

        .popup:hover .popuptext{
            visibility: visible;
        }

        /* The actual popup */
        .popup .popuptext {
            word-wrap: break-spaces;
            white-space: normal;
            line-break: initial;
            visibility: hidden;
            width: 400px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 2px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -80px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 20%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }
        </style>
    @elseif($page == 'translateblog')
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">

        @if(env('APP_ENV', 'prod') == 'prod')
            <style>
                .ke-powered-by{
                    display: none;
                }
                img {
                    max-width: 800px;
                }
            </style>
        @endif
    @elseif($page == 'blog-create' || $page == 'blog-edit')
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">

        @if(env('APP_ENV', 'prod') == 'prod')
        <style>
            .ke-powered-by{
                display: none;
            }
        </style>
        @endif
    @elseif($page == 'voucher-create')
        <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    @elseif($page == 'voucher-edit')
        <link rel="stylesheet" type="text/css" href="{{asset('js/airdatepicker/datepicker.css')}}">
    @elseif($page == 'commissioners-index')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .toggle-on.btn {
            padding-right: 180px;

        }
        .toggle.btn{width: 20px}
        .toggle-on {
            position: relative;
            top: 0;
            bottom: 0;
            left: 0;
            right: 100%;
            margin: 0;
            border: 0;
            width: 100%;
            border-radius: 0;
        }
        .toggle{width: 50%}
        .btn, .btn-large{
            font-weight: lighter;
        }

        .toggle-off.btn {
            padding-left: 15px;
        }
    </style>
    @elseif($page == 'special-offers-index')
        <style>
            form button{
                background: none;
                border: none;
                float: left;
            }
            form button:active{
                background: none;
                border: none;
                float: left;
            }
        </style>
    @elseif($page == 'external-payment-create' || $page == 'external-payment-index')

        <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
        <style>
            .dataTables_wrapper .dataTables_filter input{
                border: unset;
                border-bottom: 1px solid #9e9e9e;
                border-radius: unset;
                padding: unset;
                background-color: unset;
                margin-left: unset;
            }

            .d-flex{
                display: flex;
            }

            .justify-content-center{
                justify-content: center !important;
            }
            .align-items-center {
                align-items: center!important;
            }

            #external-payment_filter{
                width: 100%;
            }

            .justify-content-end{
                justify-content: end;
            }
           #external-payment_wrapper label {
                display: flex;
                width: 100%;
                justify-content: center;
                align-items: center;
                gap: 10px;
            }
        </style>
    @elseif($page == 'paymentlogs-index')
        <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <style>

            .dataTables_wrapper .dataTables_filter input{
                border: unset;
                border-bottom: 1px solid #9e9e9e;
                border-radius: unset;
                padding: unset;
                background-color: unset;
                margin-left: unset;
            }

            .d-flex{
                display: flex;
            }

            .justify-content-center{
                justify-content: center !important;
            }
            .align-items-center {
                align-items: center!important;
            }

            #paymentlogs-table_filter {
                width: 100%;
            }

            .justify-content-end{
                justify-content: end;
            }
            #paymentlogs-table_wrapper label {
                display: flex;
                width: 100%;
                justify-content: center;
                align-items: center;
                gap: 10px;
            }
        </style>
    @elseif($page == 'barcodes-create')

    @elseif($page == 'multiple-tickets')

    @elseif($page == 'ticket-types-index')
        <style>
            .notUsableAsTicket {
                background: #dc3545!important;
            }

            .usableAsTicket {
                background: #28a745!important;
            }
        </style>
    @elseif($page == 'gallery-index')
        <style>
            .img-wrap {
                position: relative;
            }
            .img-wrap .photoClose {
                z-index: 100;
                font-size: 25px!important;
                width: 25px;
                cursor: pointer;
            }
            .img-wrap .photoEdit {
                z-index: 100;
                width: 25px;
                cursor: pointer;
            }
            .bigImageDiv {
                cursor: pointer;
                max-height: 100%;
                max-width: 100%;
            }
        </style>
    @elseif($page == 'gallery-create')
        <link href="{{asset('css/wizard-form/dropzone.min.css')}}" rel="stylesheet" />
    @elseif($page == 'coupon-index')
        <style>
            input{padding: 0!important;}
        </style>
    @elseif($page == 'coupon-create')
        <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    @elseif($page == 'comment-index')
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <style>
            .toggle{
                width: 120px!important;
            }
            .btn, .btn-large{
                font-weight: lighter;
            }
            .toggle-on.btn {
                padding-right: 180px!important;
            }
            .toggle-on {
                position: relative!important;
                top: 0!important;
                bottom: 0!important;
                left: 0!important;
                right: 100%!important;
                margin: 0!important;
                border: 0!important;
                width: 100%!important;
                border-radius: 0!important;
            }
            .toggle-off.btn {
                padding-left: 15px!important;
            }
        </style>
    @elseif ($page == 'change-meta-tags')

    @elseif($page == 'product-sort')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
        <style>
            #products .sortable{
                margin-top:5px;
                padding:10px 15px;
                display:block;
                border: 1px solid #ddd;
                background: #eee;
                color:black;
                font-weight: bolder;
                letter-spacing: .5px;
            }
        </style>
    @elseif($page == 'translateproductforall')
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
    @elseif($page == 'translateproduct')
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
    @elseif($page == 'translateattraction')
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
        <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">
        <style>
            p, input, textarea {
                font-size: 15px !important;
            }
            .tagify__input, .tagify__tag>div>*{
                font-size: 16px !important;
            }
        </style>
    @elseif($page == 'barcodes-index')
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <style>
            .toggle-on.btn {
                padding-right: 180px!important;
            }
            .toggle-on {
                position: relative!important;
                top: 0!important;
                bottom: 0!important;
                left: 0!important;
                right: 100%!important;
                margin: 0!important;
                border: 0!important;
                width: 100%!important;
                border-radius: 0!important;
            }
            .toggle{
                width: 120px!important;
            }
        </style>
    @elseif($page=='statistic')
        <link rel="stylesheet" href="{{asset('custom/apexcharts-bundle/dist/apexcharts.css')}}">
        <link rel="stylesheet" href="{{asset('custom/bootstrap-daterangepicker/daterangepicker.css')}}">
        <link rel="stylesheet" href="{{asset('custom/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">
        <link rel="stylesheet" href="{{asset('custom/admin-lte/AdminLTE.min.css')}}">
    @elseif($page=='dashboard')
        <style>
            .ad-hom-col-com i {
                font-size: 65px;
            }
            .col-xl-3 {
                padding: 0;
            }
            @media (min-width: 1400px)
            {
                .col-xl-3 {
                    width: 25%;
                }
            }
        </style>
    @endif

</head>
<body style="height: 100%;">




