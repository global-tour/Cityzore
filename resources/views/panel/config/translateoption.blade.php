@include('panel-partials.head', ['page' => 'translateoption'])
@include('panel-partials.header', ['page' => 'translateoption'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Option from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Option from English to {{$languageToTranslate->name}}</h2>
        <form id="translateProductForm" method="POST" action="{{url('general-config/saveOptionTranslation/'.$optionID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Title</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$option->title}}" name="titleEnglish" id="titleEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($optionTranslation) {{$optionTranslation->title}} @endif" name="title" id="title">
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Description</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="descriptionEnglish" id="descriptionEnglish">{{$option->description}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="description" id="description">@if($optionTranslation) {{$optionTranslation->description}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Meeting Comment</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="meetingCommentEnglish" id="meetingCommentEnglish">{{$option->meetingComment}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="meetingComment" id="meetingComment">@if($optionTranslation) {{$optionTranslation->meetingComment}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Included</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="includedEnglish" id="includedEnglish">{{$option->included}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="included" id="included">@if($optionTranslation) {{$optionTranslation->included}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Not Included</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="notIncludedEnglish" id="notIncludedEnglish">{{$option->notIncluded}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="notIncluded" id="notIncluded">@if($optionTranslation) {{$optionTranslation->notIncluded}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Know Before You Go</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="knowBeforeYouGoEnglish" id="knowBeforeYouGoEnglish">{{$option->knowBeforeYouGo}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="knowBeforeYouGo" id="knowBeforeYouGo">@if($optionTranslation) {{$optionTranslation->knowBeforeYouGo}} @endif</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large bnt-primary" value="Save Translation">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'translateoption'])
