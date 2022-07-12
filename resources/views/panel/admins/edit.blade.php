@include('panel-partials.head', ['page' => 'admins-edit'])
@include('panel-partials.header', ['page' => 'admins-edit'])
@include('panel-partials.sidebar')

    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Edit Admin</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Edit {{$admin->name}} <a data-user-id="{{$admin->id}}" href="#" data-toggle="modal" data-target="#create-chat-admin-modal" class="btn @if($admin->chats()->count()) btn-success @else btn-danger @endif active btn-sm register-for-chat-for-admin-fire-button">Register For Chat <i class="icon-cz-share"></i></a></h4>

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

                @if(session()->has('success'))
                
                <div class="alert-success" style="margin: 20px; padding: 20px;">
                    {{session()->get('success')}}
                </div>

                @endif

                        <form action="{{url('admin/'.$admin->id.'/update')}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('POST')
                            <div style="text-align: center" class="row">
                                <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <input id="name" type="text" value="{{$admin->name}}" class="validate @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <label for="name">@lang('registration.name')</label>
                                </div>
                                <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <input id="surname" type="text" value="{{$admin->surname}}" class="validate" name="surname" required>
                                    <label for="surname">@lang('registration.surname')</label>
                                </div>
                                <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <input id="email" type="email" value="{{$admin->email}}" class="validate @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    <label for="email">@lang('registration.email')</label>
                                </div>

                                
                                 <div class="input-field col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input id="company" type="text" class="validate @error('company') is-invalid @enderror" value="{{$admin->company}}" name="company" value="{{ old('company') }}" autocomplete="company">
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
                                      @php
                                       
                                      @endphp
                                       <option @if(in_array($permission, json_decode($admin->permissions, true) )) selected @endif value="{{$permission}}" >{{str_replace("_", " ", $permission)}}</option>
                                    @endforeach



                                </select>
                                
                            </div>
                            </div>
                            </div>










                            <div class="row">
                                <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <input id="password" type="password" value="" class="validate @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                    <label for="password">@lang('registration.password')</label>
                                </div>
                                <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <input id="password-confirm" type="password" value="" class="validate" name="password_confirmation" autocomplete="new-password">
                                    <label for="password-confirm">@lang('registration.confirm_password')</label>
                                </div>
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



                       



                            <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 10px; font-size: 18px; height: 50px;">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@include('panel-partials.modals.admins.create_chat_admin_modal')
@include('panel-partials.scripts', ['page' => 'admins-edit'])
