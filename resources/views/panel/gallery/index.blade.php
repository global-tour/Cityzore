@include('panel-partials.head', ['page' => 'gallery-index'])
@include('panel-partials.header', ['page' => 'gallery-index'])
@include('panel-partials.sidebar')


<section>
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> All Images
                </a>
            </li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-2 sb2-2-1">
        <div class="col-lg-12">
            <div class="col-md-12" style="margin-bottom: 20px; margin-top: 20px;">
                <h5>Filters <button class="btn btn-primary" id="showHideFilters" data-shown="1">Hide</button></h5>
            </div>
            <div class="filters">
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
                                <option value="" selected>Choose an Attraction</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-12" style="margin-top: 20px;">Supplier</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select" name="suppliers" id="suppliers">
                                <option value="" selected>Choose a Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->companyName}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 20px;">
                        <label class="col-md-12">ID</label>
                        <div class="col-md-12">
                            <input id="galleryID" name="galleryID" type="text" class="validate form-control">
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 20px;">
                        <label class="col-md-12">Name</label>
                        <div class="col-md-12">
                            <input id="galleryName" name="galleryName" type="text" class="validate form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-bottom: 25px; margin-top: 25px;">
                    <div class="col-md-1">
                        <button id="applyFiltersButton" class="btn btn-primary">Apply</button>
                    </div>
                    <div class="col-md-2">
                        <button id="clearFiltersButton" class="btn btn-primary">Clear</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <a class="btn btn-primary pull-right" href="{{url('/gallery/create')}}">Add New Images</a>
            </div>
            <h4 style="margin-bottom: 2%;">All Images</h4>
            <div id="lightgallery" class="img-wrap">
                <div style="height: 250px;" class="col-md-12">
                @foreach($gallery as $i => $g)
                @if(is_null($attraction) && count($gallery) > 1)
                <div class="col-md-2">
                    <div class="col-md-12">
                        <img class="bigImageDiv" data-href="{{Storage::disk('s3')->url('product-images/' . $g->src)}}" src="{{Storage::disk('s3')->url('product-images-xs/' . $g->src)}}" />
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6" style="margin-top: 10px;">
                            <a data-toggle="modal" href="#galleryEditModal" class="galleryEditModal">
                                <span class="photoEdit" data-id="{{$g->id}}" data-alt="{{$g->alt}}" data-name="{{$g->name}}" data-title-suffix="{{$titleSuffix}}" data-city="{{$g->category}}" data-country="{{$g->country}}" data-attractions="{{$g->attractions}}" data-uploaded-by="{{$g->uploadedBy}}">
                                    <i class="icon-cz-edit"></i>
                                </span>
                                <span>{{$g->id}}</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <span class="col-md-2 photoClose" data-id="{{$g->id}}">&times;</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-2">
                    <div class="col-md-12">
                        <img class="bigImageDiv" data-href="{{Storage::disk('s3')->url('product-images/' . $g['src'])}}" src="{{Storage::disk('s3')->url('product-images-xs/' . $g['src'])}}" />
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6" style="margin-top: 10px;">
                            <a data-toggle="modal" href="#galleryEditModal" class="galleryEditModal">
                                <span class="photoEdit" data-id="{{$g['id']}}" data-alt="{{$g['alt']}}" data-name="{{$g['name']}}" data-title-suffix="{{$titleSuffix}}" data-city="{{$g['category']}}" data-country="{{$g['country']}}" data-attractions="{{$g['attractions']}}" data-uploaded-by="{{$g['uploadedBy']}}">
                                    <i class="icon-cz-edit"></i>
                                </span>
                                <span>{{$g['id']}}</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <span class="col-md-2 photoClose" data-id="{{$g['id']}}">&times;</span>
                        </div>
                    </div>
                </div>
                @endif
                @if ($i % 6 == 5)
                </div>
                <div style="height: 250px;" class="col-md-12">
                @endif
                @endforeach
            </div>
        </div>
    </div>
</section>


@include('layouts.modal-box.gallery-edit-modal')
@include('panel-partials.scripts', ['page' => 'gallery-index'])
