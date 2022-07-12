<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Product Creation Page</title>
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
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{asset('css/wizard-form/dropzone.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('css/admin/product-create-styles.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{asset('js/intl-tel-input/build/css/intlTelInput.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
    <link href="{{asset('../keditor/build/css/keditor.min.css')}}" rel="stylesheet">

    <link href="{{asset('foto/vendors/cropper/dist/cropper.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('foto/build/css/cropper.min.css')}}">


    <!-- Custom Theme Scripts -->
    <style>
        .toast-alert {
            background-color: #e57373!important;
        }

        .toast-success {
            background-color: #43a047!important;
        }
        .iti__flag {background-image: url({{asset('js/intl-tel-input/build/img/flags.png')}});}

        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag {background-image: url({{asset('js/intl-tel-input/build/img/flags@2x.png')}});}
        }

        img.selected {
            border: 3px solid green;
        }

        .tagify__tag-text{
            color: black!important;
            font-size: 14px!important;
        }
        #meetingComment::placeholder, #highlights::placeholder, #knowBeforeYouGo::placeholder, #included::placeholder, #notIncluded::placeholder, #tags_1::placeholder{
            color: black!important;
            font-weight: bolder!important;
        }

        .tagify{
            height: auto!important;
            margin-top: 1%;
        }

        .ke-powered-by{
            display: none;
        }

        .tagify__tag>div>* {
            text-overflow: initial!important;
        }

    </style>
</head>
<body>
