@include('panel-partials.head', ['page' => 'updatehomebanner'])
@include('panel-partials.header', ['page' => 'updatehomebanner'])
@include('panel-partials.sidebar')


<form id="homeBannerForm" data-type="edit" enctype="multipart/form-data" action="{{url('/general-config/postHomeBanner')}}" method="POST">
    @csrf
    <div class="col-md-12">
        <div class="form-group col sb2-12 col-md-12">
            <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
            <div style="z-index: 1;margin-bottom: 25px" class="input-field col-md-6">
                <input type="button" value="Click to update home image" id="homeBannerButton" style="border-radius:10px;background:#167ee6;color:white;height:50px!important;border:1px solid #eee;width: 100%" onclick="document.getElementById('homeBanner').click()">
                <input name="homeBanner" style="display:none" class="custom-file-upload" id="homeBanner" type="file">
                <div class="col-md-12" style="padding:0;width: 500px;height: 240px;margin-top:20px;text-align:center;border: 1px #00aced dotted">
                    <img style="width: 100%;height: 100%;" id="blah" src="{{Storage::disk('s3')->url('website-images/' . $homeBanner)}}"/>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>


@include('panel-partials.scripts', ['page' => 'updatehomebanner'])

