@if($notificationType == 'AVAILABILITY_EXPIRED' ||$notificationType == 'TICKET_ALERT')
    @include('panel-partials.head', ['page' => 'availability-index'])
    @if($notificationType == 'AVAILABILITY_EXPIRED')
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="tab-inn">
                        <div class="table-responsive table-desi" style="overflow-x: inherit;">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a href="{{url('availability/'.$object->id.'/edit')}}"><span class="list-enq-name">{{$object->name}}</span></a></td>
                                        <td>{{$object->type}}</td>
                                        <td>
                                            <div class="col-lg-12">
                                                <a href="{{url('availability/'.$object->id.'/edit')}}" style="height: 30px;float: left;"><i class="icon-cz-edit"></i></a>
                                            </div>
                                            <form method="POST" action="{{url('availability/'.$object->id.'/delete')}}">
                                                @method('POST')
                                                @csrf
                                                {{ Form::button('<div class="col-lg-12" style="height: 30px;margin-left: -6px;"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></div>', ['type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                            </form>
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
    @else
    <div class="sb2-2-3">
        <div class="row">
            <div class="col-md-12">
                <div class="box-inn-sp">
                    <div class="tab-inn">
                        <div class="table-responsive table-desi" style="overflow-x: inherit;">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($object  as $o)
                                <tr>
                                    <td><a href="{{url('availability/'.$o->id.'/edit')}}"><span class="list-enq-name">{{$o->name}}</span></a></td>
                                    <td>{{$o->type}}</td>
                                    <td>
                                        <div class="col-lg-12">
                                            <a href="{{url('availability/'.$o->id.'/edit')}}" style="height: 30px;float: left;"><i class="icon-cz-edit"></i></a>
                                        </div>
                                        <form method="POST" action="{{url('availability/'.$o->id.'/delete')}}">
                                            @method('POST')
                                            @csrf
                                            {{ Form::button('<div class="col-lg-12" style="height: 30px;margin-left: -6px;"><i class="icon-cz-trash" style="background: #ff0000!important;"></i></div>', ['type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                        </form>
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
    @endif
    @include('panel-partials.scripts', ['page' => 'availability-index'])
    @include('panel-partials.datatable-scripts', ['page' => 'availability-index'])
@endif
