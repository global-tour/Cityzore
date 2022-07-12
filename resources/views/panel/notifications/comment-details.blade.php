@if($notificationType == 'NEW_COMMENT')
@include('panel-partials.head', ['page' => 'comment-index'])

<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Product Ref. Code</th>
                                <th>Product Name</th>
                                <th>Username</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Rate</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span style="margin-bottom: 5px" class="list-enq-name">{{\App\Product::where('id', '=', $object->productID)->first()->referenceCode}}</span></td>
                                    <td><span class="list-enq-name">{{\App\Product::where('id', '=', $object->productID)->first()->title}}</span></td>
                                    <td>{{$object->username}}</td>
                                    <td>{{$object->title}}</td>
                                    <td>{{$object->description}}</td>
                                    <td>{{$object->rate}}</td>
                                    <td><input data-id="{{$object->id}}" class="toggle-class5" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Confirmed" data-off="Not Confirmed" {{ $object->status ? 'checked' : '' }}></td>
                                    <td>
                                        <form method="POST" action="{{url('comments/'.$object->id.'/delete')}}">
                                            @method('POST')
                                            @csrf
                                            {{ Form::button('<i class="icon-cz-trash" style="background: #ff0000!important;"></i>', ['style="background:transparent;border:none;"', 'type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
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


@include('panel-partials.scripts', ['page' => 'comment-index'])
@include('panel-partials.datatable-scripts', ['page' => 'comment-index'])
@endif
