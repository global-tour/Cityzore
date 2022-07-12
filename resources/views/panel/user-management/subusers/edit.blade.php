@include('panel-partials.head', ['page' => 'subuser-edit'])
@include('panel-partials.header', ['page' => 'subuser-edit'])
@include('panel-partials.sidebar', ['page' => 'subuser-edit'])


<div class="sb1-1">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit Sub User</h4>
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
                <div>
                    <form action="{{url('/subuser/'.$subUser->id.'/update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="container-fluid">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="name" type="text" value="{{$subUser->name}}" class="validate @error('companyName') is-invalid @enderror" name="name" required autofocus>
                                    @error('companyName')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                    <label for="name">First Name</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="surname" type="text" value="{{$subUser->surname}}" class="validate  @error('companyShortCode') is-invalid @enderror" name="surname" required autofocus>
                                    @error('companyShortCode')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                    <label for="surname">Last Name</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="email" type="text" value="{{$subUser->email}}" class="validate" name="email" required>
                                    <label for="email">E-mail</label>
                                </div>

                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="margin-bottom: 25px; display: block">
                                                <span style="font-size: 24px!important;margin-bottom: 25px" class="col-md-12">Choose Roles</span>
                                                @foreach($roles as $role)
                                                    @if(Auth::guard('supplier')->check() || Auth::guard('subUser')->check())
                                                    @if($role->id != 7 && $role->id != 8)
                                                        <input id="role{{$role->id}}" type="checkbox" value="{{$role->id}}" name="roles[]" @if(in_array($role->id, $subUserRoleArray)) checked @endif>
                                                        <label for="role{{$role->id}}">{{$role->name}}</label>
                                                    @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col-md-offset-4 col-md-3 s12">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a type="button" href="{{url('/')}}" class="btn btn-primary">Cancel</a>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'subuser-edit'])
