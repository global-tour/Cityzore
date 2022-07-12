<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Option Creation Page</title>
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
    <link rel="stylesheet" href="{{asset('css/admin/option-create-styles.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.css">
      <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
      <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <style>
        .toast-alert {
            background-color: #e57373!important;
        }

        .toast-success {
            background-color: #43a047!important;
        }

        .tagify__tag-text{
            color: black!important;
            font-size: 14px!important;
        }
        #meetingComment::placeholder{
            color: black!important;
            font-weight: bolder!important;
        }

        .select2-results__option, .select2-selection__choice {
            color: black!important;
        }

        .manipulate-form-control {
            height: auto!important;
        }
    </style>
</head>
<body>
