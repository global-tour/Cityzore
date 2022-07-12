@include('panel-partials.head', ['page' => 'gallery-create'])
@include('panel-partials.header', ['page' => 'gallery-create'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add New Images</a></li>
        </ul>
    </div>


   <div class="sb2-2-2 sb2-2-1">
       
            <div class="row filters" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <label class="col-md-12" style="margin-top: 20px;">Country</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select" name="countries" id="countries" style="width:100% !important;">
                                <option selected value="">Choose a Country</option>
                                @foreach($countries as $c)
                                    <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-12" style="margin-top: 20px;">City</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select" name="cities" id="cities">
                                <option value="" disabled selected>Choose a City</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-12" style="margin-top: 20px;">Attraction</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select" name="attractions" id="attractions">
                                <option value="" disabled selected>Choose an Attraction</option>
                            </select>
                        </div>
                    </div>
                 
                </div>
             
            </div>

   </div>

    <div class="sb2-2-2 sb2-2-1">
        <h2>Add New Images</h2>

    

       





        <div class="form-group" style="margin-top: 20px;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
                        <input type="hidden" name="ownerID" class="ownerID" value="{{$ownerID}}">
                        <input type="hidden" name="userId" class="userId" value="{{$ownerID}}">
                        <input type="hidden" name="whichPage" class="whichPage" value="gallery">
                        <p>Drag multiple files to the box below for multi upload or click to select files.</p>
                        <span hidden style="font-size: 16px" class="col-md-12 col-sm-12 label label-danger" id="pictureSizeErrorSpan">Your photo size have to be smaller than 8 MB.</span>
                        <div action="/gallery/uploadPhoto/gallery" class="dropzone" id="my-awesome-dropzone"></div>
                        <br />
                        <span class="col-md-3 col-sm-3" id="dropzoneErrorSpan" style="color: #FF0000; display: none;">You must at least upload one image.</span>
                        <br />
                        <br />
                        <br />
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <a class="btn btn-primary pull-right" href="{{url('/gallery')}}">Image Gallery</a>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'gallery-create'])
