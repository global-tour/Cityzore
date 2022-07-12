@include('panel-partials.head', ['page' => 'supplier-create'])
@include('panel-partials.header', ['page' => 'supplier-create'])
@include('panel-partials.sidebar')


<div class="sb1-1">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    @if(auth()->guard('admin')->check())
                    <h4>Create A Supplier</h4>
                    @elseif(auth()->guard('supplier')->check())
                    <h4>Create A Restaurant</h4>
                    @endif
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
                </div>
                @enderror



                 @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <form action="{{url('/supplier/create')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="companyName" type="text" class="validate  @error('companyName') is-invalid @enderror" name="companyName" value="{{ old('companyName') }}" required autocomplete="companyName" autofocus>
                                @error('companyName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="companyName">Company Name</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="companyShortCode" type="text" class="validate  @error('companyShortCode') is-invalid @enderror" name="companyShortCode" value="{{ old('companyShortCode') }}" required autocomplete="companyShortCode" autofocus>
                                @error('companyShortCode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="companyName">Company Short Code</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="contactName" type="text" class="validate" name="contactName" required>
                                <label for="contactName">Contact Name</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="contactSurname" type="text" name="contactSurname" value="{{ old('contactSurname') }}" required autocomplete="contactSurname">
                                <label for="contactSurname">Contact Surname</label>
                            </div>

                            <div class="input-field col s6">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                <label for="email">E - Mail</label>
                            </div>
                            <div class="input-field col s6">
                                <select id="countryCode" name="countryCode">
                                    <option value="" selected>Country Code</option>
                                    <optgroup label="Most Popular">
                                        @foreach($country as $countries)
                                            @if($countries->countries_iso_code  == "FR" || $countries->countries_iso_code  == "US"  || $countries->countries_iso_code == "GB"  || $countries->countries_iso_code  == "TR")
                                                <option data-countryCode="{{$countries->countries_iso_code}}" value="{{$countries->countries_phone_code}}">{{$countries->countries_name}} (+{{$countries->countries_phone_code}})</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Other Countries">
                                        @foreach($country as $countries)
                                            <option data-countryCode="{{$countries->countries_iso_code}}" value="{{$countries->countries_phone_code}}">{{$countries->countries_name}} (+{{$countries->countries_phone_code}})</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="input-field col s6">
                                <input id="phoneNumber" type="text" class="validate" name="phoneNumber">
                                <label for="phoneNumber">Phone Number</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="website" value="http://" type="text" class="validate" name="website">
                                <label for="website">Website</label>
                            </div>
                            @if(auth()->guard('admin')->check())
                                <div class="input-field col s6">
                                    <input id="comission" type="text" class="validate" name="comission">
                                    <label for="comission">Comission</label>
                                </div>
                            @endif

                             @if(auth()->guard('admin')->check())
                                <div class="input-field col s6">
                                    <input id="commissioner-commission" type="text" class="validate" name="commissioner_commission">
                                    <label for="Commissioner comission">Commissioner Commission</label>
                                </div>
                            @endif
                            <div class="input-field col s6">
                                <select class="select2 browser-default custom-select" name="location" id="location" style="width:100% !important;">
                                    <option selected value="">Choose a Country</option>
                                    @foreach($country as $c)
                                        <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-field col s6">
                                <select class="select2 browser-default custom-select" name="cities" id="cities" style="width:100% !important;"></select>
                            </div>
                            <div class="input-field col s6">
                                <input id="password" type="password" class="validate @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                <label for="password">Password</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="password-confirm" type="password" class="validate" name="password_confirmation" required autocomplete="new-password">
                                <label for="password-confirm">Confirm Password</label>
                            </div>
                            @if(auth()->guard('admin')->check())
                            <div class="infput-field col s6">
                                    <input type="checkbox" name="isRestaurant" class="filled-in" id="isRestaurant" value="0" />
                                    <label for="isRestaurant">Supplier is a restaurant</label>
                            </div>

                            <div class="infput-field col s6">
                                    <input type="checkbox" name="isAsMobileUser" class="filled-in" id="isAsMobileUser" value="0" />
                                    <label for="isAsMobileUser">Access Checkin as Mobile User on Web</label>
                            </div>


                              @php
                                $permissionsArray = ["able_to_scroll" => "able_to_scroll", "ability_to_view" => "ability_to_view"];
                            @endphp
                               <div class="infput-field col s12">
                               
                                <select class="mdb-select" multiple name="permissions[]" id="permissions">
                                    <option value="[]" disabled selected>Choose permissions</option>

                                    @foreach ($permissionsArray as $permission)
                                       <option value="{{$permission}}">{{$permission}}</option>
                                    @endforeach



                                </select>
                                <label for="isAsMobileUser">Permissions</label>
                                
                                
                              </div>
                            @endif
                            <div class="infput-field col s6">
                            <div class="row">
                                <div class="input-field  col-md-3 s12" style="display: flex;">
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


@include('panel-partials.scripts', ['page' => 'supplier-create'])
