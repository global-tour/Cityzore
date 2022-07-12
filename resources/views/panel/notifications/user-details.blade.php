@if($notificationType == 'USER_REGISTER' || $notificationType == 'COMPANY_REGISTER')
    <div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table class="table table-hover">
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
                            <tr>
                                <td>
                                    <span class="list-img"><img src="{{asset('/'.$object->avatar)}}" alt=""></span>
                                </td>
                                <td>
                                    <a href="{{url('user/'.$object->id.'/edit')}}"><span class="list-enq-name">{{ $object->name }} {{$object->surname}}</span></a>
                                </td>
                                <td>
                                    <a href="mailto:{{$object->email}}">{{ $object->email }}</a>
                                </td>
                                <td>{{ $object->created_at->format('F d, Y h:ia') }}</td>
                                <td>
                                    <a href="{{ url('user/'.$object->id.'/edit') }}"><i class="icon-cz-edit" aria-hidden="true"></i></a>
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
                                    {!! Form::open(['method' => 'POST', 'url' => ['user/'.$object->id.'/destroy'] ]) !!}
                                    {{ Form::button('<i class="icon-cz-trash" style="background: #ff0000!important;"></i>', ['type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
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
@endif


