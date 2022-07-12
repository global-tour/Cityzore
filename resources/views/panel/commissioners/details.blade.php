@include('panel-partials.head', ['page' => 'commissioners-details'])
@include('panel-partials.header', ['page' => 'commissioners-details'])
@include('panel-partials.sidebar')


    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Commissioner</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Commissioner: {{$user->name}}</h4>
                        <a href="{{url('/commissioner/'.$user->id.'/editCommissions')}}" class="btn btn-default pull-right">Add New Commission</a>
                    </div>
                    <div class="tab-inn">
                        <div class="table-responsive table-desi">
                            <table class="table">
                                <thead>
                                    <tr role="row">
                                        <th>Company Name</th>
                                        <th>E-Mail</th>
                                        <th>General Commission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td><a href="mailto:{{$user->email}}">{{$user->email}}</a></td>
                                    <td>
                                        <div class="col-md-4">
                                            <input type="number" step="0.1" readonly value="{{$user->commission}}" class="commissionerCommission" />
                                        </div>
                                        <div class="col-md-4">
                                            <button data-commissioner-id="{{$user->id}}" class="btn btn-primary saveCommissionerCommission" style="display: none;">Save</button>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{url('commissioner/'.$user->id.'/editCommissions')}}" style="float:left"><i class="icon-cz-edit"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br><br>
                            <h4>Files</h4>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Confirm</th>
                                    <th>File Title</th>
                                    <th>File Name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(\App\LicenseFile::where('companyID', $user->id)->get() as $l)
                                    <tr>
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
                            <br><br><br><br>
                            <h4>Commissions</h4>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Option Name</th>
                                        <th>Commission</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @foreach($commissions as $commission)
                                    <tr>
                                        <td style="font-weight: bold">{{{\App\Option::where('id', '=', $commission['optionID'])->first()->title}}}</td>
                                        <td style="font-weight: bold">{{$commission['commission']}}</td>
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


@include('panel-partials.scripts', ['page' => 'commissioners-details'])
@include('panel-partials.datatable-scripts', ['page' => 'commissioners-details'])
