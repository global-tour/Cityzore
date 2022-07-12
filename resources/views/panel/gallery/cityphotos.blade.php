@include('panel-partials.head', ['page' => 'gallery-cityphotos'])
@include('panel-partials.header', ['page' => 'gallery-cityphotos'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> City Photos</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <div class="box-inn-sp">
        <div class="inn-title">
            <h4>City Photos</h4>
            <a href="{{url('/gallery/addCityPhoto')}}" class="btn btn-default pull-right">Add New City Photo</a>
        </div>
        <div class="bor">
            <div class="row" style="margin-top: 50px;overflow-x: auto;">
                <table  id="datatable" class="table">
                    <thead>
                    <tr>
                        <th>Country</th>
                        <th>City</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cityPhotos as $cityPhoto)
                        <tr>
                            <td>{{$cityPhoto->countryName->countries_name}}</td>
                            <td>{{$cityPhoto->city}}</td>
                            <td>
                                <a class="btn btn-primary" href="{{url('/gallery/editCityPhoto/'.$cityPhoto->id)}}">Edit Photo</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'gallery-cityphotos'])
@include('panel-partials.datatable-scripts', ['page' => 'gallery-cityphotos'])
