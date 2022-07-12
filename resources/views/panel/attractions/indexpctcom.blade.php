@include('panel-partials.head', ['page' => 'attraction-indexpctcom'])
@include('panel-partials.header', ['page' => 'attraction-indexpctcom'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Attractions</h4>
                    <a href="{{url('/attraction/create')}}" class="btn btn-default pull-right">Add New</a>

                      @if (auth()->guard('admin')->check())
{{--                    <a href="{{url('/switchToPCT?route=productPCT')}}" class="btn btn-default pull-right" style="margin-left: 10px;">Switch To PCT</a>--}}
                        <select name="" id="" class="shaselect" style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;" onchange="window.location.href = this.value">
                            <option value="/attraction" {{$type == 'cz' ? 'selected': ''}}>Cityzore.com</option>
                            <option value="/attractionPCT" {{$type == 'pct' ? 'selected': ''}}>Pariscitytours.fr</option>
                            <option value="/attractionPCTcom" {{$type == 'pctcom' ? 'selected': ''}}>Paris-city-tours.com</option>
                            <option value="/attractionCTP" {{$type == 'ctp' ? 'selected': ''}}>Citytours.paris</option>
                        </select>
                    @endif

                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>Attraction Name</th>
                                    <th>Status</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'attraction-indexpctcom'])
@include('panel-partials.datatable-scripts', ['page' => 'attraction-indexpctcom'])
