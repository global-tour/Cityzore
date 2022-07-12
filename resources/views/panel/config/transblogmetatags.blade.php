@include('panel-partials.head', ['page' => 'transblogmetatags'])
@include('panel-partials.header', ['page' => 'transblogmetatags'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Blog Meta Tags from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Blog Meta Tags from English to {{$languageToTranslate->name}}</h2>
        <form id="translateBlogMetaTagsForm" method="POST" action="{{url('general-config/saveBlogMetaTagsTranslation/'.$blogID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Title</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$blogMetaTags->title}}" name="titleEnglish" id="titleEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($blogMetaTagsTrans) {{$blogMetaTagsTrans->title}} @endif" name="title" id="title">
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Description</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="descriptionEnglish" id="descriptionEnglish">{{$blogMetaTags->description}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="description" id="description">@if($blogMetaTagsTrans) {{$blogMetaTagsTrans->description}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Keywords</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="keywordsEnglish" id="keywordsEnglish">{{$blogMetaTags->keywords}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="keywords" id="keywords">@if($blogMetaTagsTrans) {{$blogMetaTagsTrans->keywords}} @endif</textarea>
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


@include('panel-partials.scripts', ['page' => 'transblogmetatags'])
