@include('panel-partials.head', ['page' => 'platforms-edit'])
@include('panel-partials.header', ['page' => 'platforms-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> All Platforms</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$platform->name}}</h4>
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

                    <form action="{{url('platform/'.$platform->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div style="text-align: center" class="row">
                            <div class="input-field col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" value="{{$platform->name}}" class="validate @error('name') is-invalid @enderror" name="name" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">Platform Name</label>
                            </div>
                            <div class="input-field col-md-3 col-sm-6 col-xs-12">
                                <input id="color" type="text" value="{{$platform->color}}" class="validate @error('color') is-invalid @enderror" name="color" required autocomplete="color" autofocus>
                                @error('color')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="color">Platform Text Color</label>
                            </div>
                            <div class="input-field col-md-3 col-sm-6 col-xs-12">
                                <input id="colorBg" type="text" value="{{$platform->colorBg}}" class="validate @error('colorBg') is-invalid @enderror" name="colorBg" required autocomplete="colorBg" autofocus>
                                @error('colorBg')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="colorBg">Platform Background Color</label>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;margin-right: 10px;">
                            <div class="input-field col s12">
                                <button type="submit" class="btn btn-success pull-right" >Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'platforms-edit'])
