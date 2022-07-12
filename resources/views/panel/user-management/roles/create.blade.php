@include('panel-partials.head', ['page' => 'roles-create'])
@include('panel-partials.header', ['page' => 'roles-create'])
@include('panel-partials.sidebar', ['page' => 'roles-create'])


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add Permission</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Add New Role</h4>
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
                <div class="container-fluid">
                    <form action="{{url('role/store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" class="validate  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                                <label for="name">Role Name</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="description" type="text" class="validate" name="description" required>
                                <label for="desciption">Role Description</label>
                            </div>
                        </div>
                        <div>
                            <label style="margin-bottom:20px;font-size: 20px" class="col-md-12">Choose Permissions for New Role</label>
                            @foreach($permissions as $i => $permission)
                                <div class="col-md-6">
                                    <input class="permission" value="{{$permission->id}}" id="permission{{$i}}" type="checkbox" name="permission[]">
                                    <label for="permission{{$i}}">{{$permission->name}}<span style="margin-left: 5px">{{$permission->description}}</span></label>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="submit" class="btn btn-primary large btn-large" value="Add" style="padding: 10px; font-size: 18px; height: 50px; width: 15%;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'roles-create'])

