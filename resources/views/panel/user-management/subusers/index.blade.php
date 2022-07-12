@include('panel-partials.head', ['page' => 'subuser-index'])
@include('panel-partials.header', ['page' => 'subuser-index'])
@include('panel-partials.sidebar', ['page' => 'subuser-index'])


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> All Permissions</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Sub Users</h4>
                    <a href="{{ url('subuser/create') }}" class="btn btn-default pull-right">Add New Sub User</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>E-mail</th>
                                <th>Roles</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($subUsers as $subUser)
                                <tr>
                                    <td>{{$subUser->name}} {{$subUser->surname}}</td>
                                    <td>{{$subUser->email}}</td>
                                    <td>
                                        @if(!is_null($subUser->roles))
                                            @foreach(json_decode($subUser->roles, true) as $role)
                                                <label style="font-size: 12px" class="label label-info">{{$role}}</label>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{url('/subuser/'.$subUser->id.'/delete')}}"><i style="background-color: #dd2c00" class="icon-cz-trash"></i></a>
                                        <a href="{{url('/subuser/'.$subUser->id.'/edit')}}"><i style="background-color: #0f9d58" class="icon-cz-edit"></i></a>
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


@include('panel-partials.scripts', ['page' => 'subuser-index'])
