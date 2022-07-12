@include('panel-partials.head', ['page' => 'admins-create'])
@include('panel-partials.header', ['page' => 'admins-create'])
@include('panel-partials.sidebar')
<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add User</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Add New Admin</h4>
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

                </div>
                <div class="container">
                    <form action="{{url('admin/store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="name" type="text" class="validate  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                <label for="name">@lang('registration.name')</label>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="surname" type="text" class="validate" name="surname" required>
                                <label for="surname">@lang('registration.surname')</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="email" type="email" class="validate @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                <label for="email">@lang('registration.email')</label>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong style="color: darkred">&times; {{ $message }}</strong>
                                </span>
                                @enderror
                            </div>


                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="company" type="text" class="validate @error('company') is-invalid @enderror" name="company" value="{{ old('email') }}" autocomplete="company">
                                <label for="company">@lang('registration.companyName')</label>
                            </div>
                        </div>

                        <div class="row">

                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">

                                @php
                                $permissionsArray = ["able_to_scroll" => "able_to_scroll", "ability_to_view" => "ability_to_view"];
                                @endphp
                                <div class="row">
                                    <span style="font-size: 20px!important;padding-bottom: 5%;">Permissions (optional)</span> <br>
                                    <select class="mdb-select" multiple name="permissions[]" id="permissions">
                                        <option value="[]" disabled selected>Choose permissions</option>

                                        @foreach ($permissionsArray as $permission)
                                        <option value="{{$permission}}">{{$permission}}</option>
                                        @endforeach



                                    </select>

                                </div>
                            </div>
                        </div>








                        <div class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="password" type="password" class="validate @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                <label for="password">@lang('registration.password')</label>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong style="color:darkred">&times; {{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="password-confirm" type="password" class="validate" name="password_confirmation" required autocomplete="new-password">
                                <label for="password-confirm">@lang('registration.confirm_password')</label>
                            </div>
                            @if(Auth::guard('admin')->user()->isSuperUser == 1)
                            <div style="margin-bottom: 25px">
                                <span style="font-size: 24px!important;margin-bottom: 25px" class="col-md-12">Choose Roles</span>
                                @foreach($roles as $role)
                                <input id="role{{$role->id}}" type="checkbox" value="{{$role->id}}" name="roles[]">
                                <label for="role{{$role->id}}">{{$role->name}}</label>
                                @endforeach
                            </div>
                            @endif







                            <div class="row" style="margin-bottom: 20px;">
                                <div class="input-field col s12">
                                    <input type="submit" class="btn btn-primary large btn-large" value="Add" style="padding: 10px; font-size: 18px; height: 50px;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'admins-create'])