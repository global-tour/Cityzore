@include('panel-partials.head', ['page' => 'role-index'])
@include('panel-partials.header', ['page' => 'role-index'])
@include('panel-partials.sidebar', ['page' => 'role-index'])


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
                    <h4>Permission Details</h4>
                    <a href="{{ url('role/create') }}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->description}}</td>
                                    <td>
                                        @foreach($role->permission()->get() as $permission)
                                            <label style="font-size: 12px" class="label label-info">{{$permission->name}}</label>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{url('/role/'.$role->id.'/delete')}}"><i style="background-color: #dd2c00" class="icon-cz-trash"></i></a>
                                        <a href="{{url('/role/'.$role->id.'/edit')}}"><i style="background-color: #0f9d58" class="icon-cz-edit"></i></a>
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


@include('panel-partials.scripts', ['page' => 'permission-index'])
