@include('panel-partials.head', ['page' => 'translateblog'])
@include('panel-partials.header', ['page' => 'translateblog'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Blog from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Blog from English to {{$languageToTranslate->name}}</h2>
        <form id="translateBlogForm" method="POST" action="{{url('general-config/saveBlogTranslation/'.$blogID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <p style="font-size: 20px !important;font-weight: bold">Blog Title</p>
                    <div class="col-md-6">
                        <input readonly class="form-control" type="text" value="{{$blog->title}}" name="titleEnglish" id="titleEnglish">
                    </div>
                    <div class="col-md-6">
                        <input class="form-control" type="text" value="@if($blogTranslation) {{$blogTranslation->title}} @endif" name="title" id="title">
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <p style="font-size: 20px !important;font-weight: bold">Content</p>
                    <div class="col-md-6">
                        <div class="">
                            {!! html_entity_decode($blog->postContent) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <textarea class="form-control" name="postContent" id="postContent" style="height: 500px;">@if($blogTranslation) {{$blogTranslation->postContent}} @endif</textarea>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 50px; visibility: hidden;">
                    <div class="col-md-2">
                        <label>Category Slug</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$blog->category}}" name="categoryEnglish" id="categoryEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($blogTranslation) {{$blogTranslation->category}} @endif" name="category" id="category">
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


@include('panel-partials.scripts', ['page' => 'translateblog'])
