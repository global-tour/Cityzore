@include('panel-partials.head', ['page' => 'category-create'])
@include('panel-partials.header', ['page' => 'category-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add Category</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp" >
                <div class="inn-title">
                    <h4>Add New Category</h4>
                </div>
                <div class="tab-inn">
                    @error('email')
                    <span class="col-md-12 invalid-feedback" role="alert">
                        <strong style="color: darkred">&times; {{ $message }}</strong>
                    </span>
                    @enderror
                    @error('password')
                    <span class="col-md-12 invalid-feedback" role="alert">
                        <strong style="color:darkred">&times;  {{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                 @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{url('category/store')}}" method="POST" enctype="multipart/form-data" style="margin: 20px">
                    @csrf
                    <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input id="name" type="text" class="validate  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <label for="name">Name</label>
                        </div>
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="row" >
                                <select class="mdb-select" multiple name="attractions[]" id="attractions">
                                    <option value="" disabled selected>Choose product attraction</option>
                                    @foreach($attractions as $attraction)
                                        <option value="{{$attraction->id}}">{{$attraction->name}}</option>
                                    @endforeach
                                </select>
                                <span class="attractionErrorSpan col-md-12"
                                      style="display: none!important; color: #ff0000;">This field is required.</span>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom: 20px;margin-right: 10px;">
                        <div class="input-field col s12">
                            <input type="submit" class="btn btn-primary large btn-large pull-right" value="Add" style="font-size: 18px; height: 50px; width: 15%;">
                        </div>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>

@include('panel-partials.scripts', ['page' => 'category-create'])
