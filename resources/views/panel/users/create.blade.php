@include('panel-partials.head', ['page' => 'users-create'])
@include('panel-partials.header', ['page' => 'users-create'])
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
                    <h4>Add New User</h4>
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
                <form action="{{url('user/store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input  value="{{asset('')}}user_default_avatar.png" id="avatar" type="file" class="validate" name="avatar">
                            <div style="width: 230px;height: 230px;margin-bottom: 10px; ">
                                <img id="previewHolder" style="width:100%;height:100%;border-radius: 50%; border: 3px solid #bbbbbb" src="{{asset('/uploads/avatars/'."default_user_avatar.png")}}">
                            </div>
                        </div>
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
                            <input id="surname" type="text" class="validate" name="surname" required>
                            <label for="surname">Surname</label>
                        </div>
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input id="email" type="email" class="validate @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <select id="countryCode" class="validate" name="countryCode">
                            <option value="" selected>Country Code</option>
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
                            <input id="phoneNumber" type="number" class="validate" name="phoneNumber">
                            <label for="phoneNumber">Phone</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="address" type="text" class="validate" name="address">
                            <label for="address">Address</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input id="password" type="password" class="validate @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <label for="password">Password</label>
                        </div>
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input id="password-confirm" type="password" class="validate" name="password_confirmation" required autocomplete="new-password">
                            <label for="password-confirm">Confirm Password</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input style="font-size: 17px; color: blue;" id="affiliate_unique" type="text" class="validate @error('affiliate_unique') is-invalid @enderror" name="affiliate_unique" autocomplete="affiliate_unique">
                            <label for="password">Affiliate Token (for commissioner)</label>
                        </div>
                        <div class="input-field col-lg-1 col-md-1 col-sm-1"><label class="label label-success"><i class="icon-cz-connection" id="generate-unique" style="cursor: pointer; font-size: 30px;"></i></label></div>
                       
                    </div>


                    <div class="row">
                        <div class="input-field col s6">
                            <input type="checkbox" name="isCommissioner" class="filled-in" id="isCommissioner"  />
                            <label for="isCommissioner">User is a commisioner</label>
                        </div>
                        <div class="col s6" id="commissionDiv" style="display: none">
                            <div class="input-field col s6">
                                <select name="commissionType">
                                    <option selected value="percentage">Percentage</option>
                                    <option value="money">Money</option>
                                </select>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="commission" class="filled-in">
                                <label for="commission">Commission</label>
                            </div>
                            <div class="input-field col s6">
                                <select name="platform">
                                    <option value="noPlatform" selected>No Platform</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{$platform->id}}">{{$platform->name}}</option>
                                    @endforeach
                                </select>
                                <label for="platform">Platform</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="submit" class="btn btn-primary large btn-large" value="Add" style="padding: 10px; font-size: 18px; height: 50px; width: 15%;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('panel-partials.scripts', ['page' => 'users-create'])
