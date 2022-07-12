@include('panel-partials.head', ['page' => 'platform-create'])
@include('panel-partials.header', ['page' => 'platform-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add Platform</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp" >
                <div class="inn-title">
                    <h4>Add New Platform</h4>
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
                <form action="{{url('platform/store')}}" method="POST" enctype="multipart/form-data" style="margin: 20px">
                    @csrf
                    <div class="row">
                        <div class="input-field col-md-6 col-sm-12 col-xs-12">
                            <input id="name" type="text" class="validate  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <label for="name">Name</label>
                        </div>
                        <div class="input-field col-md-3 col-sm-6 col-xs-12">
                            <input id="color" type="text" value="#ffffff" class="validate @error('color') is-invalid @enderror" name="color" required autocomplete="color" autofocus>
                            @error('color')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <label for="color">Platform Text Color</label>
                        </div>
                        <div class="input-field col-md-3 col-sm-6 col-xs-12">
                            <input id="colorBg" type="text" value="#449d44" class="validate @error('colorBg') is-invalid @enderror" name="colorBg" required autocomplete="colorBg" autofocus>
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
                            <input type="submit" class="btn btn-primary large btn-large pull-right" value="Add" >
                        </div>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>

@include('panel-partials.scripts', ['page' => 'platform-create'])
