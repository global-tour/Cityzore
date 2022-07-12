@include('panel-partials.head', ['page' => 'supplier-details'])
@include('panel-partials.header', ['page' => 'supplier-details'])
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
                    <h4>Company Page</h4>
                </div>
                <div class="tab-inn">
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
                    <h5>Files</h5>
                    <div class="tab-inn">
                        <table class="responsive-table">
                            <thead>
                            <tr>
                                <th>Confirm</th>
                                <th>File Title</th>
                                <th>File Name</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\LicenseFile::where('companyID', $supplier->id)->get() as $l)
                                <tr id="{{$l->id}}">
                                    <td>
                                        @if($l->confirm == 1)
                                            <input data-id="{{$l->id}}" checked type="checkbox" class="confirmCheck filled-in" id="filled-in-box-{{$l->id}}">
                                        @else
                                            <input data-id="{{$l->id}}" type="checkbox" class="confirmCheck filled-in" id="filled-in-box-{{$l->id}}">
                                        @endif
                                        <label style="margin-top:15px;width: 5px" for="filled-in-box-{{$l->id}}"></label>
                                    </td>
                                    <td>{{$l->title}}</td>
                                    <td>{{$l->fileName}}</td>
                                    <td>
                                        <a download="{{$l->fileName}}" href="{{env('APP_URL').'/storage/license-files/'.$l->fileName}}"><label style="margin-left:5px;font-size: 12px;font-weight: normal" class="label label-success downloadLicenseFile">Download</label></a>
                                        <label data-id="{{$l->id}}" style="margin-left:5px;font-size: 12px;font-weight: normal" class="label label-danger deleteFile">Delete</label>
                                        <label data-id="{{$l->id}}" style="margin-left:5px;font-size: 12px;font-weight: normal" class="label label-warning editSuggestFile">Edit Suggest</label>
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


@include('panel-partials.scripts', ['page' => 'supplier-details'])
