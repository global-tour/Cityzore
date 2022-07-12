@include('panel-partials.head', ['page' => 'users-index'])
@include('panel-partials.header', ['page' => 'users-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> All Users</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>User Details</h4>
                    <a href="{{ url('user/create') }}" class="btn btn-default pull-right">Add New</a>
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
                                <th>Avatar</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Registration At</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td><span class="list-img"><img src="{{asset('/'.$user->avatar)}}" alt=""></span>
                                </td>
                                <td><a href="{{url('user/'.$user->id.'/edit')}}"><span class="list-enq-name">{{ $user->name }} {{$user->surname}}</span></a>
                                </td>
                                <td><a href="mailto:{{$user->email}}">{{ $user->email }}</a></td>
                                <td>{{ $user->created_at->format('F d, Y h:ia') }}</td>
                                <td>
                                    <a href="{{ url('user/'.$user->id.'/edit') }}"><i class="icon-cz-edit" aria-hidden="true"></i></a>
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
                                    {!! Form::open(['method' => 'POST', 'url' => ['user/'.$user->id.'/destroy'] ]) !!}
                                    {{ Form::button('<i class="icon-cz-trash" style="background: #ff0000!important;"></i>', ['type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                    {!! Form::close() !!}
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


@include('panel-partials.scripts', ['page' => 'users-index'])
@include('panel-partials.datatable-scripts', ['page' => 'users-index'])

