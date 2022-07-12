@include('panel-partials.head', ['page' => 'seo-for-pages'])
@include('panel-partials.header', ['page' => 'seo-for-pages'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit SEO for Pages</h4>
                    <select name="" id="" class="shaselect" style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;" onchange="window.location.href = this.value">
                        <option value="/general-config/seo-for-pages" {{$type == 'cz' ? 'selected' : ''}}>Cityzore.com</option>
                        <option value="/general-config/seo-for-pages-pct" {{$type == 'pct' ? 'selected' : ''}}>Pariscitytours.fr</option>
                        <option value="/general-config/seo-for-pages-pctcom" {{$type == 'pctcom' ? 'selected' : ''}}>Paris-city-tours.com</option>
                        <option value="/general-config/seo-for-pages-ctp" {{$type == 'ctp' ? 'selected' : ''}}>Citytours.paris</option>
                    </select>
                    <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Url</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($pages as $page)
                                <tr>
                                    <td>{{$page->name}}</td>
                                    <td>{{$page->url}}</td>
                                    <td>
                                        <div class="col-lg-12" style="height: 30px;"><a href="{{url('/general-config/changeMetaTags/'.$page->id.'/'.$type)}}"><i class="icon-cz-edit"></i></a></div>
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


@include('panel-partials.scripts', ['page' => 'seo-for-pages'])
@include('panel-partials.datatable-scripts', ['page' => 'seo-for-pages'])
