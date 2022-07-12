@include('panel-partials.head', ['page' => 'users-edit'])
@include('panel-partials.header', ['page' => 'users-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit User</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$user->name}}</h4>
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




                    <form action="{{url('user/'.$user->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div style="text-align: center" class="row">
                            <div class="input-field col s3">
                                <div style="width: 230px;height: 230px;margin-bottom: 10px; ">
                                    <img id="previewHolder" style="width:100%;height:100%;border-radius: 50%; border: 3px solid #bbbbbb" src="{{asset('/')}}{{$user->avatar}}">
                                </div>
                                @if($user->avatar)
                                    <input style="margin-left: 15px" onchange="pressed()" value="{{asset('/')}}{{$user->avatar}}" id="avatar" type="file" class="validate" name="avatar">
                                @endif
                            </div>
                            @if (!is_null($user->commission))
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="companyName" type="text" value="{{$user->companyName}}" class="validate" name="companyName" autocomplete="companyName" autofocus>
                                <label for="companyName">{{__('companyName')}}</label>
                            </div>
                            @endif
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" value="{{$user->name}}" class="validate @error('name') is-invalid @enderror" name="name" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">@lang('registration.name')</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="surname" type="text" value="{{$user->surname}}" class="validate" name="surname" required>
                                <label for="surname">@lang('registration.surname')</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="email" type="email" value="{{$user->email}}" class="validate @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="email">@lang('registration.email')</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <select style="height: 30px" id="countryCode" class="validate" name="countryCode">
                                    @if($user->countryCode)
                                        @foreach($country as $countries)
                                            @if($user->countryCode == $countries->countries_phone_code)
                                                <option selected value="+({{$user->countryCode}})">{{$countries->countries_name}} +({{$user->countryCode}})</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option selected value="">Country Code</option>
                                    @endif
                                    <optgroup label="Most Popular">
                                        @foreach($country as $countries)
                                            @if($countries->countries_iso_code  == "FR" || $countries->countries_iso_code  == "US"  || $countries->countries_iso_code == "GB"  || $countries->countries_iso_code  == "TR")
                                                <option data-countryCode="{{$countries->countries_iso_code}}" value="+{{$countries->countries_phone_code}}">{{$countries->countries_name}} (+{{$countries->countries_phone_code}})</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Other Countries">
                                        @foreach($country as $countries)
                                            <option data-countryCode="{{$countries->countries_iso_code}}" value="+{{$countries->countries_phone_code}}">{{$countries->countries_name}} (+{{$countries->countries_phone_code}})</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="phoneNumber" type="text" value="{{$user->phoneNumber}}" class="validate" name="phoneNumber">
                                <label for="phoneNumber">@lang('registration.phone_number')</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="address" type="text" value="{{$user->address}}" class="validate" name="address">
                                <label for="address">@lang('registration.address')</label>
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

                           <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input @if(!empty($user->affiliate_unique)) readonly @endif style="font-size: 17px; color: blue;" value="{{$user->affiliate_unique}}" id="affiliate_unique" type="text" class="validate @error('affiliate_unique') is-invalid @enderror" name="affiliate_unique" autocomplete="affiliate_unique">
                            <label for="password">Affiliate Token (for commissioner)</label>
                        </div>

                        @if(empty($user->affiliate_unique))
                        <div class="input-field col-lg-1 col-md-1 col-sm-1"><label class="label label-success"><i class="icon-cz-connection" id="generate-unique" style="cursor: pointer; font-size: 30px;"></i></label></div>
                        @endif
                       
                        </div>

                        {{-- @if($user->isActive != 1)
                        <div class="infput-field col s6">
                            <input type="checkbox" name="isCommissioner" class="filled-in" @if($user->isActive == 1) checked @endif id="isCommissioner" value="{{$user->isActive}}" />
                            <label for="isCommissioner">User is a commissioner</label>
                        </div>
                        @endif --}}
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="checkbox" name="isCommissioner" class="filled-in" id="isCommissioner" @if($user->commission) checked value="1" @else value="0" @endif />
                                <label for="isCommissioner">User is a commisioner</label>
                            </div>
                            <div class="col s6" id="commissionDiv" style="@if(!$user->commission) display: none; @endif">
                                <div class="input-field col s6">
                                    <select name="commissionType">
                                        <option value="percentage" @if($user->commissionType == 'percentage') selected @endif>Percentage</option>
                                        <option value="money" @if($user->commissionType == 'money') selected @endif>Money</option>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="commission" class="filled-in" value="{{$user->commission}}">
                                    <label for="commission">Commission</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="platform">
                                        <option value="noPlatform" selected>No Platform</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{$platform->id}}" @if($user->platform && $platform->id == $user->platform->platform_id) selected @endif>{{$platform->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="platform">Platform</label>
                                </div>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 10px; font-size: 18px; height: 50px;">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'users-edit'])
