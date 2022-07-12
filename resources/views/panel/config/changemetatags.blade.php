@include('panel-partials.head', ['page' => 'change-meta-tags'])
@include('panel-partials.header', ['page' => 'change-meta-tags'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Change Page Meta Tags</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Change Page Meta Tags</h2>
        <form id="changePageMetaTags" method="POST" action="{{url('/general-config/saveMetaTags/'.$page->id.'/'.$platform)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group" style="margin-top: 40px;">
                        <div class="input-field col s12">
                            <input id="title" name="title" type="text" class="validate form-control" value="{{$page->title}}">
                            <label for="title">Title</label>
                            <span style="color: red;"><span id="charCounter">{{250 - strlen($page->title)}}</span> characters left</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-field col s12">
                            <textarea name="description" id="description" class="materialize-textarea form-control">{{$page->description}}</textarea>
                            <label for="description">Description</label>
                            <span style="color: red;"><span id="charCounter">{{250 - strlen($page->description)}}</span> characters left</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-field col s12">
                            <textarea name="keywords" id="keywords" class="materialize-textarea form-control">{{$page->keywords}}</textarea>
                            <label for="keywords">Keywords</label>
                            <span style="color: red;"><span id="charCounter">{{250 - strlen($page->keywords)}}</span> characters left</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large" id="submitButton" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'change-meta-tags'])
