@include('panel-partials.head', ['page' => 'gallery-editcityphoto'])
@include('panel-partials.header', ['page' => 'gallery-editcityphoto'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add Photo to a City</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit Photo of {{$cityImage->city}}</h4>
                </div>
                <div class="tab-inn">
                    <form action="{{url('gallery/uploadOnlyPhotoForCity')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <input type="hidden" name="cityImageID" value="{{$cityImage->id}}">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="col-md-12" style="margin-top: 20px;">Country</label>
                                    <div class="col-md-12">
                                        <label class="col-md-12">{{$cityImage->countryName->countries_name}}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-md-12" style="margin-top: 20px;">City</label>
                                    <div class="col-md-12">
                                        <label class="col-md-12">{{$cityImage->city}}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input type="button" value="Choose a Photo!" id="coverPhotoButton" style="border-radius:10px;background:#167ee6;color:white;height:50px!important;border:1px solid #eee;width: 100%;margin-top: 30px;" onclick="document.getElementById('coverPhoto').click()">
                                    <input name="coverPhoto" style="display:none" class="custom-file-upload" id="coverPhoto" type="file">
                                </div>
                            </div>
                            <div style="z-index: 1;margin-bottom: 25px" class="col-md-12">
                                <div class="col-md-3"></div>
                                <div class="col-md-9" style="padding:0;width: 500px;height: 240px;margin-top:20px;text-align:center;border: 1px #00aced dotted">
                                    <img style="width: 100%;height: 100%;" id="blah" src="{{Storage::disk('s3')->url('city-images/' . $cityImage->image)}}"/>
                                </div>
                            </div>
                            <div class="input-field col s12">
                                <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 10px; font-size: 18px; height: 50px;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'gallery-editcityphoto'])
