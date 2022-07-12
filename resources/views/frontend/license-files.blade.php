@include('frontend-partials.head', ['page' => 'license-files'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <div class="db">
    @include('layouts.profile-sidebar.sidebar-left')
        <div class="col-lg-9">
            <div class="db-2-com db-2-main" style="background-color: white;">
                <h4>{!! __('licenseFiles') !!}</h4>
                <div class="db-2-main-com db2-form-pay db2-form-com">
                    <label style="font-size: 14px;font-weight: normal;letter-spacing: 1px" class="label label-info">{{__('weWillChangeYourFileNames')}}</label>
                    <form action="{{url($langCodeForUrl.'/license-files/'.auth()->user()->id.'/update')}}" class="col s12" style="background-color: white;" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div id="licenceFilesContainer" class="col s12">
                            <button type="button" id="newFile" class="waves-effect waves-light btn-primary" style="font-size:26px;padding:2px 20px;border:none;">+</button>
                            @foreach($licenses as $l)
                            <div class="row s6">
                                <div class="input-field col s6">
                                    <input type="text" class="validate title" value="{{$l->title}}">
                                    <label for="bankName">{{__('title')}}</label>
                                </div>
                                <div class="input-field col s6">
                                    <button data-id="{{$l->id}}" type="button" class="deleteFiles waves-effect waves-light btn-danger" style="font-size:26px;padding:2px 20px;border:none;float: right">-</button>
                                </div>
                                {{--<div class="input-field col s6">
                                    <button data-id="{{$l->id}}" type="button" class="deleteFiles waves-effect waves-light btn-danger" style="font-size:26px;padding:2px 20px;border:none;float: right">-</button>
                                    <input readonly style="width: 75%" class="fileName validate" value="{{$l->fileName}}">
                                </div>--}}
                            </div>
                            @endforeach
                        </div>
                        <div class="row text-center">
                            <input type="submit" class="waves-effect waves-light btn-large" value="Update" style="margin-top: 1%;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'licence-files'])
