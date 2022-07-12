@include('panel-partials.head', ['page' => 'attraction-create'])
@include('panel-partials.header', ['page' => 'attraction-create'])
@include('panel-partials.sidebar')

<style>
   
</style>


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add New Attraction</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">

       
   @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
           <li>Please fill in all required fields</li>
        </ul>
    </div>
@endif


        <h2>Add New Attraction</h2>
        <form method="POST" action="{{url('attraction/store')}}" class="form-horizontal form-label-left" enctype="multipart/form-data">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="input-field col s12">
                    <div class="col-lg-12 border-col">
                        <p style="font-size: 18px !important;font-weight: bold;">Attraction Name</p>
                        <input id="list-title" type="text" name="name" value="" class="validate @error('name') is-invalid @enderror">
                    </div>
                </div>
                <div class="input-field col s12">
                    <div class="col-md-12 border-col">
                        <p style="font-size: 18px !important;font-weight: bold;">Attraction Description</p>
                        <textarea name="description" id="description" class="materialize-textarea form-control @error('description') is-invalid @enderror"></textarea>
                    </div>
                </div>
                <div class="input-field col s12">
                    <div class="col-lg-12 border-col">
                        <p style="font-size: 18px !important;font-weight: bold;margin: 2% 0 2% 0;">Add Image for Attraction</p>
                        <input type="file" name="attractionImage" class="@error('attractionImage') is-invalid @enderror">
                    </div>
                </div>
                <div class="col-md-12">
                <div class="col-md-12 border-col" style="margin-top: 30px;">
                    <div class="col-md-4">
                        <label class="col-md-12 " style="margin-top: 20px;font-size: 16px !important;">Country</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select" name="countries" class="@error('countries') is-invalid @enderror" id="countries" style="width:100% !important;">
                                <option selected value="">Choose a Country</option>
                                @foreach($countries as $c)
                                    <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-md-12" style="margin-top: 20px;font-size: 16px !important;">City</label>
                        <div class="col-md-12">
                            <select class="select2 browser-default custom-select @error('cities') is-invalid @enderror" name="cities" id="cities">
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
                    </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 border-col">
                                <span style="font-size: 15px !important;">Add Tag</span>
                                <input name="tagAttraction" id="tagAttraction" type="text" class="tags form-control @error('tagAttraction') is-invalid @enderror"/>
                            </div>
                            <span class="notIncludedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                    </div>
                </div>
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'attraction-create'])
