@include('panel-partials.head', ['page' => 'blog-create'])
@include('panel-partials.header', ['page' => 'blog-create'])
@include('panel-partials.sidebar')


<form id="blogForm" data-type="create" enctype="multipart/form-data" action="{{url('/blog/createNewBlogPost')}}"
      method="POST">
    @csrf
    <input type="hidden" id="type" name="type" value="{{$type}}">
    <div class="col-md-12">
        <div class="form-group col sb2-12 col-md-12">
            <div class="col-md-6">
                <div style="z-index: 1;" class="input-field col-md-12">
                    <input name="postTitle" placeholder="Post Title.." maxlength="255"
                           style="border-radius:10px;padding:0 10px;border: 1px solid #eee;width: 96%" id="postTitle"
                           type="text" class="validate form-control">
                </div>
                <div style="margin-top:-20px;margin-left:-15px;z-index: 1;" class="col-md-12">
                    <div class="input-field col-md-12">
                        <select name="category" class="browser-default custom-select" id="category"
                                style="border-radius:10px;height:50px;width:106% !important;">
                            <option value="" selected disabled>Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->categoryName}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="z-index: 1;" class="input-field col-md-12">
                    <input placeholder="Meta Title..." maxlength="255"
                           style="border-radius:10px;padding:0 10px;border: 1px solid #eee;width: 96%" id="metaTitle"
                           name="metaTitle" type="text" class="validate form-control">
                </div>
                <div style="margin-top:0;z-index: 1;" class="input-field col-md-12">
                    <input placeholder="Meta Description..."
                           style="border-radius:10px;padding:0 10px;border: 1px solid #eee;width: 96%"
                           id="metaDescription" name="metaDescription" type="text" class="validate form-control">
                </div>
                <div style="z-index: 1;margin-top: 0;" class="input-field col-md-12">
                    <input placeholder="Keywords..." maxlength="255"
                           style="border-radius:10px;padding:0 10px;border: 1px solid #eee;width: 96%" id="metaKeywords"
                           name="metaKeywords" type="text" class="validate form-control">
                </div>
                <div style="margin-top:-20px;margin-left:-15px;z-index: 1;margin-bottom: 10px" class="col-md-12">
                    <div class="input-field col-md-12">
                        <select name="attraction" class="browser-default custom-select" id="attraction"
                                style="border-radius:10px;height:50px;width:106% !important;">
                            <option value="" selected disabled>Select Attraction</option>
                            @foreach($attractions as $attraction)
                                <option value="{{$attraction->id}}">{{$attraction->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div style="z-index: 1;margin-bottom: 25px" class="input-field col-md-6">
                <input type="button" value="Click here and choose a cover photo !" id="coverPhotoButton"
                       style="border-radius:10px;background:#167ee6;color:white;height:50px!important;border:1px solid #eee;width: 100%"
                       onclick="document.getElementById('coverPhoto').click()">
                <input name="coverPhoto" style="display:none" class="custom-file-upload" id="coverPhoto" type="file">
                <div class="col-md-12"
                     style="padding:0;width: 500px;height: 240px;margin-top:20px;text-align:center;border: 1px #00aced dotted">
                    <img style="width: 100%;height: 100%;" id="blah"
                         src="https://x.kinja-static.com/assets/images/logos/placeholders/default.png"/>
                </div>
            </div>
        </div>
        <div class="sb1-12 col-md-12">
            <input id="postContent" name="postContent">
        </div>
        <div class="col-md-12" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>


@include('panel-partials.scripts', ['page' => 'blog-create'])
