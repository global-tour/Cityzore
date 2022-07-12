@include('panel-partials.head', ['page' => 'language-index'])
@include('panel-partials.header', ['page' => 'language-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All Languages</h4>
                    <a href="{{url('/language/create')}}" class="btn btn-default pull-right">Add New Language</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($langs as $l)
                                <tr>
                                    <td>{{$l->name}}</td>
                                    <td>{{$l->displayName}}</td>
                                    <td><input data-id="{{$l->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="Passive" {{ $l->isActive  ? 'checked' : '' }}></td>
                                    <td style="width: 10%;">
                                        <div class="input-field" style="margin-top: 0px;">
                                            <a href="{{ url('language/'.$l->id.'/edit') }}"><i class="icon-cz-edit" aria-hidden="true"></i></a>
                                        </div>

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


@include('panel-partials.scripts', ['page' => 'language-index'])
@include('panel-partials.datatable-scripts', ['page' => 'language-index'])
