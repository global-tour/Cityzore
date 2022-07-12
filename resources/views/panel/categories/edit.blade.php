@include('panel-partials.head', ['page' => 'categories-edit'])
@include('panel-partials.header', ['page' => 'categories-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> All Categories</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$category->categoryName}}</h4>
                </div>
                <div class="tab-inn">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{url('category/'.$category->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div style="text-align: center" class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" value="{{$category->categoryName}}" class="validate @error('name') is-invalid @enderror" name="name" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">@lang('registration.name')</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="row" >
                                    <select class="mdb-select" multiple name="attractions[]" id="attractions">
                                        <option value="" disabled>None</option>
                                        @foreach($attractions as $attraction)
                                            <option {{in_array($attraction->id,$selected_att,true) ? 'selected':''}}  value="{{$attraction->id}}">{{$attraction->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="attractionErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;margin-right: 10px;">
                            <div class="input-field col s12">
                                <input type="submit" class="btn btn-primary large btn-large pull-right" value="Update" style="font-size: 18px; height: 50px; width: 15%;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'categories-edit'])
