@include('panel-partials.head', ['page' => 'subuser-create'])
@include('panel-partials.header', ['page' => 'subuser-create'])
@include('panel-partials.sidebar')


<div class="sb1-1">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                        <h4>Create A Sub User</h4>
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
                    <form id="subUserForm" action="{{url('subuser/create')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="name" type="text" class="validate  @error('companyName') is-invalid @enderror" name="name" required autofocus>
                                @error('companyName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">First Name</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="surname" type="text" class="validate  @error('companyShortCode') is-invalid @enderror" name="surname" required autofocus>
                                @error('companyShortCode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="surname">Last Name</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="email" type="text" class="validate" name="email" autocomplete="false" required>
                                <label for="email">E-mail</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="password" type="password" name="password" autocomplete="false" required>
                                <label for="password">Password</label>
                            </div>
                            <div style="margin-bottom: 25px">
                                <span style="font-size: 24px!important;margin-bottom: 25px" class="col-md-12">Choose Roles</span>
                                @foreach($roles as $role)
                                    @if(Auth::guard('supplier')->check() || Auth::guard('subUser')->check())
                                        @if($role->id != 7 && $role->id != 8)
                                            <input id="role{{$role->id}}" type="checkbox" value="{{$role->id}}" name="roles[]">
                                            <label for="role{{$role->id}}">{{$role->name}}</label>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <div class="row">
                                <div class="input-field col-md-offset-4 col-md-3 s12">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a type="button" href="{{url('/')}}" class="btn btn-primary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'subuser-create'])
