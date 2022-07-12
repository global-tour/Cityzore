@include('panel-partials.head', ['page' => 'transprodmetatags'])
@include('panel-partials.header', ['page' => 'transprodmetatags'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Product Meta Tags from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Product Meta Tags from English to {{$languageToTranslate->name}}</h2>
        <form id="translateProductForm" method="POST" action="{{url('general-config/saveProductMetaTagsTranslation/'.$productID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Title</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$productMetaTags->title}}" name="titleEnglish" id="titleEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($prodMetaTagsTrans) {{$prodMetaTagsTrans->title}} @endif" name="title" id="title">
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Description</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="descriptionEnglish" id="descriptionEnglish">{{$productMetaTags->description}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="description" id="description">@if($prodMetaTagsTrans) {{$prodMetaTagsTrans->description}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Keywords</label>
                    </div>
                    <div class="col-md-5">
                        <textarea readonly class="materialize-textarea form-control" name="keywordsEnglish" id="keywordsEnglish">{{$productMetaTags->keywords}}</textarea>
                    </div>
                    <div class="col-md-5">
                        <textarea class="materialize-textarea form-control" name="keywords" id="keywords">@if($prodMetaTagsTrans) {{$prodMetaTagsTrans->keywords}} @endif</textarea>
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


@include('panel-partials.scripts', ['page' => 'transprodmetatags'])
