@include('panel-partials.head', ['page' => 'attraction-edit'])
@include('panel-partials.header', ['page' => 'attraction-edit'])
@include('panel-partials.sidebar')


    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Edit Attraction</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-3">


   @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
           <li>Please fill in all required fields</li>
        </ul>
    </div>
   @endif



        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Edit {{$attraction->name}} Attraction</h4>
                    </div>
                    <div class="tab-inn">
                        <form action="{{url('attraction/'.$attraction->id.'/update')}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('POST')
                            <input type="hidden" id="attractionID" value="{{$attraction->id}}">
                            <input type="hidden" id="type" name="type" value="cz">
                            <div class="row">
                                <div class="input-field col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-12 border-col">
                                        <p style="font-size: 18px !important;font-weight: bold;">Attraction Name</p>
                                        <input id="name" type="text" value="{{$attraction->name}}" class="validate" name="name" style="font-size: 15px;" required autocomplete="name" autofocus>
                                    </div>
                                    <div class="col-md-12 border-col">
                                        <p style="font-size: 18px !important;font-weight: bold;">Attraction Description</p>
                                        <textarea name="description" id="description" class="materialize-textarea form-control">
                                         {!! html_entity_decode($attraction->description) !!}
                                        </textarea>
                                    </div>
                                    <div class="col-md-12 border-col">
                                        <p style="font-size: 18px !important;font-weight: bold;margin: 2% 0 2% 0;">Add Image for Attraction</p>
                                        <div class="col-lg-12">
                                            <div class="col-lg-4">
                                                @if(!($attraction->image==null))
                                                    <img src="{{Storage::disk('s3')->url('/attraction-images/' . $attraction->image)}}" alt="" style="border-radius: 5%; width:500px; height:300px;">
                                                @endif
                                            </div>
                                            <div class="6" style="float: left;">
                                                <input type="file" name="attractionImage">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 border-col" style="margin-top: 30px;">
                                    <p style="font-size: 18px !important;font-weight: bold;margin: 2% 0 2% 0;">Add City for Attraction</p>
                                    <div class="col-md-4">
                                        <p class="col-md-12" style="margin-top: 20px;font-size: 16px !important;">Country</p>
                                        <div class="col-md-12">
                                            <select class="select2 browser-default custom-select" name="countries" id="countries" style="width:100% !important;">
                                                <option selected value="">Choose a Country</option>
                                                @foreach($countries as $c)
                                                    <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="col-md-12" style="margin-top: 20px;font-size: 16px !important;">City</p>
                                        <div class="col-md-12">
                                            <select class="select2 browser-default custom-select" name="cities" id="cities">
                                                <option value="" disabled selected>Choose City</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-12" style="margin-top: 40px;">
                                            <button class="btn btn-primary" id="bindCityButton">Bind City</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="citiesSpan" style="margin-top: 40px;">
                                        @if (!is_null($attraction->cities))
                                        @foreach(json_decode($attraction->cities, true) as $city)
                                        <span class="col-md-1" id="bindedCity" style="cursor: pointer; text-align: center; background-color: #075175; font-size: 17px !important;margin: 20px; padding: 10px; color: #ffffff;">
                                            {{$city}} <span data-city="{{$city}}" style="cursor: pointer;" class="pull-right" id="deleteBindedCity">X</span>
                                        </span>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                <?php
                                $tags = implode('{}', explode('|', $attraction->tags));
                                ?>
                                <div class="col-md-12 col-sm-12 col-xs-12 border-col">
                                    <p style="font-size: 18px !important;font-weight: bold;margin: 2% 0 2% 0;">Add Tag for Attraction</p>
                                    <input value="{{$tags}}" name="tagAttraction" id="tagAttraction" type="text" class="tags form-control" style="font-size: 20px !important;"/>
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


@include('panel-partials.scripts', ['page' => 'attraction-edit'])
