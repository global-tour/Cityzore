@include('panel-partials.head', ['page' => 'permission-index'])
@include('panel-partials.header', ['page' => 'permission-index'])
@include('panel-partials.sidebar', ['page' => 'permission-index'])


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
                    <a href="{{ url('permission/create') }}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Admin Or All Users</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{$permission->name}}</td>
                                    <td>{{$permission->description}}</td>
                                    <td>
                                        @if($permission->isGlobal)
                                            <button class="btn btn-success" onclick="changeGlobalStatus($(this), '{{$permission->id}}')">All Users</button>
                                        @else
                                            <button class="btn btn-danger" onclick="changeGlobalStatus($(this), '{{$permission->id}}')">Admin</button>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{url('/permission/'.$permission->id.'/delete')}}"><i style="background-color: #dd2c00" class="icon-cz-trash"></i></a>
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

<style>
    .btn-danger{
        background-color: #e23464!important;
    }
    .btn-success {
        background-color: #0f9d58!important;
    }
</style>
<script>
    function changeGlobalStatus(el,id){
        $.ajax({
            method: "POST",
            data: {id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            url: "/permissions/admin-or-all-users",
            success: function (response) {
                if (response.isGlobal === true) {
                    el.removeClass('btn-danger').addClass('btn-success').html('ALL USERS');
                } else {
                    el.removeClass('btn-success').addClass('btn-danger').html('ADMIN');
                }
            },
            error: function () {
                alert('Saving failed. Please try again.');
            }

        })
    }
</script>

@include('panel-partials.scripts', ['page' => 'permission-index'])
