@include('panel-partials.head', ['page' => 'admins-index'])
@include('panel-partials.header', ['page' => 'admins-index'])
@include('panel-partials.sidebar')


    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> All Admins</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>User Details</h4>
                        <a href="{{ url('admin/create') }}" class="btn btn-default pull-right">Add New</a>
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
                            <table id="datatable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Registration At</th>
                                    @if ($isSuperUser)
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($admins as $admin)
                                    <tr>
                                        <td>
                                            <a href="{{url('admin/'.$admin->id.'/edit')}}"><span class="list-enq-name">{{ $admin->name }} {{$admin->surname}}</span></a>
                                        </td>
                                        <td><a href="mailto:{{$admin->email}}">{{ $admin->email }}</a></td>
                                        <td>{{ $admin->created_at->format('F d, Y h:ia') }}</td>
                                        @if ($isSuperUser)
                                        <td>
                                            <a href="{{ url('admin/'.$admin->id.'/edit') }}"><i class="icon-cz-edit" aria-hidden="true"></i></a>
                                        </td>
                                        <td>
                                            <style>
                                                form button{
                                                    background: none;
                                                    border: none;
                                                }
                                                form button:active{
                                                    background: none;
                                                    border: none;
                                                }
                                            </style>
                                            {!! Form::open(['method' => 'POST', 'url' => ['admin/'.$admin->id.'/destroy'] ]) !!}
                                            {{ Form::button('<i class="icon-cz-trash"  style="background: #ff0000!important;"></i>', ['type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                            {!! Form::close() !!}
                                        </td>
                                        @endif
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


@include('panel-partials.scripts', ['page' => 'admins-index'])
@include('panel-partials.datatable-scripts', ['page' => 'admins-index'])
