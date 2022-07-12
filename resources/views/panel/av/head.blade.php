<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .toast-alert {
            background-color: #e57373!important;
        }

        .toast-success {
            background-color: #43a047!important;
        }

        .toggle {
            width: 128px!important;
        }
        .toggle-group > .btn-danger.active {
            background-color: #f4364f;
            border-color: #f4364f;
        }
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }
        .toggle-group > .btn-success {
            background: #26a69a!important;
            border-color: #26a69a!important;
        }
        .toggle-group > .toggle-handle {
            background: #ffffff!important;
        }
        .datepicker {
            width: 58% !important;
        }
        #scrollToTop {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed/sticky position */
            bottom: 20px; /* Place the button at the bottom of the page */
            right: 30px; /* Place the button 30px from the right */
            z-index: 99; /* Make sure it does not overlap */
            border: none; /* Remove borders */
            outline: none; /* Remove outline */
            background-color: #26a69a; /* Set a background color */
            color: white; /* Text color */
            cursor: pointer; /* Add a mouse pointer on hover */
            padding: 15px; /* Some padding */
            border-radius: 27px; /* Rounded corners */
            font-size: 18px; /* Increase font size */
        }

        #scrollToTop:hover {
            background-color: #555; /* Add a dark-grey background on hover */
        }

        @media only screen and (max-width: 600px) {
            .datepicker {
                width: 118% !important;
                margin-left: -20px !important;
            }
            .datepicker--cell {
                min-height: 40px;
            }

            #hourParentDiv {
                margin: 0px !important;
            }

            #disableEnableDiv {
                float: right;
            }
        }
    </style>
</head>
<body>
