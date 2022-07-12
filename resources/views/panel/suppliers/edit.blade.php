@include('panel-partials.head', ['page' => 'supplier-edit'])
@include('panel-partials.header', ['page' => 'supplier-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit Profile</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$supplier->companyName}}</h4>
                    <input type="hidden" id="supplierID" value="{{$supplier->id}}">
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



                    <div class="navbar">
                        <div class="navbar-inner">
                            <ul class="nav nav-pills option-setup-panel">
                                <li class="active"><a id="step1Tab" href="#step1" data-toggle="tab" data-step="1">Profile Details</a></li>
                                <li><a id="step2Tab" href="#step2" data-toggle="tab" data-step="2" disabled="">Payment & Bank Details</a></li>
                                <li><a id="step3Tab" href="#step3" data-toggle="tab" data-step="3" disabled="">License Files</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade in active option-setup-content" id="step1">
                            <form action="{{url('/supplier/'.$supplier->id.'/update')}}" enctype="multipart/form-data" method="POST">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="companyName" type="text" class="validate  @error('companyName') is-invalid @enderror" name="companyName" value="{{$supplier->companyName}}" required autocomplete="companyName" autofocus>
                                        @error('companyName')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                        <label for="companyName">Company Name</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="companyShortCode" type="text" class="validate  @error('companyShortCode') is-invalid @enderror" name="companyShortCode" value="{{$supplier->companyShortCode}}" required autocomplete="companyShortCode" autofocus>
                                        @error('companyShortCode')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                        <label for="companyName">Company Short Code</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="contactName" type="text" class="validate" name="contactName" required value="{{$supplier->contactName}}">
                                        <label for="contactName">Contact Name</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="contactSurname" type="text" name="contactSurname" value="{{$supplier->contactSurname}}" required autocomplete="contactSurname">
                                        <label for="contactSurname">Contact Surname</label>
                                    </div>

                                    <div class="input-field col s6">
                                        <input id="email" type="email" name="email" value="{{$supplier->email}}" required autocomplete="email">
                                        <label for="email">E - Mail</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <select id="countryCode" name="countryCode">
                                            @if (!is_null($supplier->countryCode))
                                                <option value="{{$supplier->countryCode}}" selected>{{App\Country::where('countries_phone_code', explode('+', $supplier->countryCode)[1])->first()->countries_iso_code}}</option>
                                            @endif
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
                                        <input id="phoneNumber" type="number" class="validate" name="phoneNumber" value="{{$supplier->phoneNumber}}">
                                        <label for="phoneNumber">Phone Number</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="website" type="text" class="validate" name="website" value="{{$supplier->website}}">
                                        <label for="website">Website</label>
                                    </div>
                                    <div class="input-field col s6">
                                        @if(auth()->guard('admin')->check())
                                        <input id="comission" type="text" class="validate" name="comission" value="{{$supplier->comission}}">
                                        @else
                                            <input id="comission" type="text" class="validate" name="comission" value="{{$supplier->comission}}" readonly>
                                        @endif
                                        <label for="comission">Comission</label>
                                    </div>


                                     <div class="input-field col s6">
                                        @if(auth()->guard('admin')->check())
                                        <input id="commissioner_commission" type="text" class="validate" name="commissioner_commission" value="{{$supplier->commissioner_commission}}">
                                        @else
                                            <input id="commissioner_commission" type="text" class="validate" name="commissioner_commission" value="{{$supplier->commissioner_commission}}" readonly>
                                        @endif
                                        <label for="commissioner_commission">Commissioner Commission</label>
                                    </div>
                                    <div style="margin-bottom: 25px" class="col-md-12">
                                        <div class="input-field col s6">
                                            <select class="select2 browser-default custom-select" name="location" id="location" style="width:100% !important;">
                                                @if (!is_null($supplier->country) && $supplier->country != 0)
                                                    <option selected value="{{$supplier->country}}">{{$supplier->countryName->countries_name}}</option>
                                                @endif
                                                @foreach($country as $c)
                                                    <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-field col s6">
                                            <select class="select2 browser-default custom-select" name="cities" id="cities" style="width:100% !important;">
                                                <option value="{{$supplier->city}}">{{$supplier->city}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="password" type="password" placeholder="********" class="validate @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                        <label for="password">Password</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="password-confirm" type="password" placeholder="********" class="validate" name="password_confirmation" autocomplete="new-password">
                                        <label for="password-confirm">Confirm Password</label>
                                    </div>
                                    @unless(Auth::guard('supplier')->check())
                                    <div class="infput-field col s6">
                                        <input type="checkbox" name="isRestaurant" class="filled-in" @if($supplier->isRestaurant == 1) checked @endif id="isRestaurant" value="{{$supplier->isRestaurant}}" />
                                        <label for="isRestaurant">Supplier is a restaurant</label>
                                    </div>




                                    @php
                                        $roles = json_decode($supplier->roles, true);

                                    @endphp

                                      <div class="infput-field col s6">
                                      <input type="checkbox" @if(in_array("Supplier_Mobile", $roles)) checked  @endif name="isAsMobileUser" class="filled-in" id="isAsMobileUser" @if(in_array("Supplier_Mobile", $roles)) value="1" @else value="0"  @endif />
                                      <label for="isAsMobileUser">Access Checkin as Mobile User on Web</label>
                                      </div>












                                 
                                       @php
                                $permissionsArray = ["able_to_scroll" => "able_to_scroll", "ability_to_view" => "ability_to_view"];
                            @endphp
                               <div class="infput-field col s12">
                               
                                <select class="mdb-select" multiple name="permissions[]" id="permissions">
                                    <option value="[]" disabled selected>Choose permissions</option>

                                    @foreach ($permissionsArray as $permission)
                                      <option @if(in_array($permission, json_decode($supplier->permissions, true) )) selected @endif value="{{$permission}}" >{{str_replace("_", " ", $permission)}}</option>
                                    @endforeach



                                </select>
                                <label for="isAsMobileUser">Permissions</label>
                                
                                
                              </div>




                                    @endunless
                                    <div class="row">
                                        <div class="input-field col-md-3 s12">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="reset" class="btn btn-primary">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade option-setup-content" id="step2">
                            <form id="paymentDetailsForm" action="{{url('/supplier/'.$supplier->id.'/updatePaymentDetails')}}" enctype="multipart/form-data" method="POST">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="bankName" type="text" class="validate" name="bankName" value="@if(!is_null($payment)) {{$payment->bankName}} @endif" required autocomplete="bankName" autofocus>
                                        <label for="bankName">Bank Name</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="bankBranch" type="text" class="validate" name="bankBranch" value="@if(!is_null($payment)) {{$payment->bankBranch}} @endif" required autocomplete="bankBranch" autofocus>
                                        <label for="bankBranch">Bank Branch</label>
                                    </div>
                                    <div class="input-field col s4">
                                        <input id="city" type="text" class="validate" name="city" required value="@if(!is_null($payment)) {{$payment->city}} @endif">
                                        <label for="city">City</label>
                                    </div>
                                    <div class="input-field col s4">
                                        <input id="district" type="text" name="district" value="@if(!is_null($payment)) {{$payment->district}} @endif" required autocomplete="district">
                                        <label for="district">District</label>
                                    </div>
                                    <div class="input-field col s4">
                                        <input id="postalCode" type="text" name="postalCode" value="@if(!is_null($payment)) {{$payment->postalCode}} @endif" required autocomplete="postalCode">
                                        <label for="postalCode">Postal Code</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="swift" type="text" class="validate" name="swift" value="@if(!is_null($payment)) {{$payment->swift}} @endif">
                                        <label for="swift">Swift</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="iban" type="text" class="validate" name="iban" value="@if(!is_null($payment)) {{$payment->iban}} @endif">
                                        <label for="iban">IBAN</label>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col-md-offset-4 col-md-3 s12">
                                            <button type="submit" class="btn btn-primary" id="paymentDetailSaveButton">Save</button>
                                            <button type="reset" class="btn btn-primary">Cancel</button>
                                        </div>
                                    </div>
                                    <div style="display: none; margin-top: 20px;" class="alert alert-danger" role="alert" id="verificationAlertDiv">
                                        <p>We sent you a verification e-mail.</p>
                                        <p>In order to update your payment details, you need to type the verification code below.</p>
                                        <p>
                                            <div class="col s12">
                                                <input style="width: 50%;" id="verificationCode" type="text" class="validate col-md-6" name="verificationCode" value="">
                                            </div>
                                        </p>
                                        <p>
                                            <button id="submitVerificationCodeButton" class="btn btn-primary">Submit Verification Code</button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade option-setup-content" id="step3">
                            <div class="tab-content">
                                <div class="row">
                                    <div class="col s6">
                                        <h5>Company Details</h5>
                                        <div class="tab-inn">
                                            <span style="font-weight: bold">Company Name : </span><span>{{$supplier->companyName}}</span><br>
                                            <span style="font-weight: bold">Company Short Code : </span><span>{{$supplier->companyShortCode}}</span><br>
                                            <span style="font-weight: bold">Contact Name : </span><span>{{$supplier->contactName}} {{$supplier->contactSurname}}</span><br>
                                            <span style="font-weight: bold">E-mail : </span><span>{{$supplier->email}}</span><br>
                                            <span style="font-weight: bold">Phone Number : </span><span>{{$supplier->countryCode}} {{$supplier->phoneNumber}}</span><br>
                                            <span style="font-weight: bold">Web Site : </span><span>{{$supplier->website}}</span><br>
                                            <span style="font-weight: bold">Active Status : </span><span>@if($supplier->isActive == 1)<label class="label label-success">Active</label>@else<label class="label label-warning">Pending</label>@endif</span><br>
                                            <span style="font-weight: bold">Restaurant Status : </span><span>@if($supplier->isRestaurant == 1)<label class="label label-success">Yes</label>@else<label class="label label-warning">No</label>@endif</span><br>
                                            @if(!is_null($supplier->createdBy))<span style="font-weight: bold">Created By : </span><span>{{\App\Supplier::findOrFail($supplier->createdBy)->companyName}}</span><br>@endif
                                            <span style="font-weight: bold">Commission Rate : </span><span>% {{$supplier->comission}}</span><br>
                                        </div>
                                    </div>
                                    <div class="col s6">
                                        @if($payment)
                                            <h5>Bank Details</h5>
                                            <div class="tab-inn">
                                                <span style="font-weight: bold">Bank Name : </span><span>{{$payment->bankName}}</span><br>
                                                <span style="font-weight: bold">Bank Branch : </span><span>{{$payment->bankBranch}}</span><br>
                                                <span style="font-weight: bold">City : </span><span>{{$payment->city}}</span><br>
                                                <span style="font-weight: bold">District : </span><span>{{$payment->district}}</span><br>
                                                <span style="font-weight: bold">Postal Code : </span><span>{{$payment->postalCode}}</span><br>
                                                <span style="font-weight: bold">Swift : </span><span>{{$payment->swift}}</span><br>
                                                <span style="font-weight: bold">IBAN : </span><span>{{$payment->iban}}</span><br>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col s12">
                                        <h5>Files</h5>
                                        <div class="tab-inn">
                                            <button type="button" id="newFile" class="waves-effect waves-light btn-primary" style="margin-left:15px;float:right;font-size:20px;padding:2px 20px;border:none;">+</button>
                                            <div>
                                                <form enctype="multipart/form-data" method="POST" action="{{url($supplier->id.'/licenseSave')}}" id="filesTable">
                                                    @csrf
                                                    <button type="submit" id="uploadFiles" class="waves-effect waves-light btn-primary" style="display:none;float:right;font-size:20px;padding:2px 20px;border:none;">Upload Files</button>
                                                </form>
                                            </div>
                                            <table class="responsive-table">
                                                <thead>
                                                <tr>
                                                    <th>File Title</th>
                                                    <th>File Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach(\App\LicenseFile::where('companyID', $supplier->id)->get() as $l)
                                                    <tr id="{{$l->id}}">
                                                        <td>{{$l->title}}</td>
                                                        <td>{{$l->fileName}}</td>
                                                        <td>
                                                            <a download="{{$l->fileName}}" href="{{env('APP_URL').'/storage/license-files/'.$l->fileName}}"><label style="margin-left:5px;font-size: 12px;font-weight: normal" class="label label-success downloadLicenseFile">Download</label></a>
                                                            <label data-id="{{$l->id}}" style="margin-left:5px;font-size: 12px;font-weight: normal" class="label label-danger deleteFile">Delete</label>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'supplier-edit'])
