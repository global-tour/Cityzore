@include('panel-partials.head', ['page' => 'categories-index'])
@include('panel-partials.header', ['page' => 'categories-index'])
@include('panel-partials.sidebar')
<head>
    <style>
        .popup {
            position: relative;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .table > tbody > tr > td {
            vertical-align: middle !important;
        }
    </style>
</head>

<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> All Categories</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Category Details</h4>
                    <a href="{{ url('category/create') }}" class="btn btn-default pull-right">Add New</a>
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
                                <th>Id</th>
                                <th style="width: 30%">Category Name</th>
                                <th>Connected Attractions</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{$category->id}}</td>
                                    <td class="w-100">{{$category->categoryName}}</td>
                                    <td>
                                        @foreach($attractions as $attraction)
                                            @if($attraction->category_id==$category->id)
                                                <a class="popup" href="{{ url('attraction/'.$attraction->attraction_id.'/edit') }}"><label for="myPopup1" style="cursor:pointer;margin-right:2px;font-size: 11px;width:80%"
                                                                                                                                           class="col-md-2 label label-primary"><span class="popuptext" id="myPopup1">{{$attraction->name}}</span></label></a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ url('category/'.$category->id.'/edit') }}"><i class="icon-cz-edit" aria-hidden="true"></i></a>
                                    </td>
                                    <td>
                                        <style>
                                            form button {
                                                background: none;
                                                border: none;
                                            }

                                            form button:active {
                                                background: none;
                                                border: none;
                                            }
                                        </style>
                                        {!! Form::open(['method' => 'POST', 'url' => ['category/'.$category->id.'/destroy'] ]) !!}
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


@include('panel-partials.scripts', ['page' => 'categories-index'])
@include('panel-partials.datatable-scripts', ['page' => 'categories-index'])

