@include('panel-partials.head', ['page' => 'commissioners-index'])
@include('panel-partials.header', ['page' => 'commissioners-index'])
@include('panel-partials.sidebar')


    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Commissioners</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Commissioners</h4>
                        <a href="{{url('/user/create')}}" class="btn btn-default pull-right">Add New</a>
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

                        <div class="table-responsive table-desi">
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company Name</th>
                                        <th>Full Name</th>
                                        <th>E-Mail</th>
                                        <th>Commission Type</th>
                                        <th>Commission</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="width: 100%;">
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->companyName}}</td>
                                    <td>{{$user->name}} {{$user->surname}}</td>
                                    <td><a href="mailto:{{$user->email}}">{{$user->email}}</a></td>
                                    <td>
                                        <div class="col-md-7">
                                            <input type="text" readonly value="{{$user->commissionType}}" class="commissionerCommissionType" />
                                        </div>
                                        <div class="col-md-4">
                                            <button data-commissioner-id="{{$user->id}}" class="btn btn-primary saveCommissionerCommissionType" style="display: none;">Save</button>
                                        </div>
                                    </td>
                                    <td width="150">
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center">
                                            <input type="number" step="0.1" readonly value="{{$user->commission}}" class="commissionerCommission" style="max-width: 45px;"/>
                                            <button data-commissioner-id="{{$user->id}}" class="btn btn-primary saveCommissionerCommission" style="display: none;">Save</button>
                                        </div>
                                    </td>
                                    <td><input data-id="{{$user->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="Not Active" {{ $user->isActive == 1 ? 'checked' : '' }}></td>
                                    <td>
                                        <a href="{{url('commissioner/'.$user->id.'/details')}}" style="float:left"><i style="font-size: 15px;padding-bottom:10px;padding-right:12px;padding-left: 5px" class="icon-cz-preview"></i></a>
                                        <a href="{{url('commissioner/'.$user->id.'/editCommissions')}}" style="float:left"><i style="font-size: 20px;padding: 4px" class="icon-cz-add-commission"></i></a>
                                        <a href="{{url('user/'.$user->id.'/edit')}}" style="float:left"><i style="font-size:18px;" class="icon-cz-edit"></i></a>
                                        <a href="{{url('commissioner/'.$user->id.'/turnToStandardUser')}}" style="float:left"><i style="font-size:18px;background-color:#dd2c00;" class="icon-cz-trash"></i></a>
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


@include('panel-partials.scripts', ['page' => 'commissioners-index'])
@include('panel-partials.datatable-scripts', ['page' => 'commissioners-index'])
