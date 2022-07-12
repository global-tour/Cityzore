@include('panel-partials.head', ['page' => 'pagemetatagstrans'])
@include('panel-partials.header', ['page' => 'pagemetatagstrans'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Page List</h4>
                     <select name="" id="" class="shaselect" style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;" onchange="window.location.href = this.value">
                        <option value="/general-config/page-meta-tags-translations" {{$type == 'cz' ? 'selected' : ''}}>Cityzore.com</option>
                        <option value="/general-config/page-meta-tags-translations-pct" {{$type == 'pct' ? 'selected' : ''}}>Pariscitytours.fr</option>
                        <option value="/general-config/page-meta-tags-translations-pctcom" {{$type == 'pctcom' ? 'selected' : ''}}>Paris-city-tours.com</option>
                        <option value="/general-config/page-meta-tags-translations-ctp" {{$type == 'ctp' ? 'selected' : ''}}>Citytours.paris</option>
                    </select>
                    <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
                </div>
                <div class="tab-inn">
                    <input type="hidden" name="pageID" id="pageID" value="1">
                    <input type="hidden" name="isRun" id="isRun" value="0">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Url</th>
                                <th>Translate Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($pages as $page)
                                <tr>
                                    <td>{{$page->name}}</td>
                                    <td>{{$page->url}}</td>
                                    <td>
                                        @foreach($languages as $lang)
                                            <p>{{$lang->name}}:
                                                @if((new \App\Http\Controllers\Admin\ConfigController)->isTranslated($page->id, $lang->id, 'pagemetatags', $type))
                                                    <span style="color:#84A98C;">Translated</span>
                                                @else
                                                    <span style="color:#D1495B;">Not Translated</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="col-lg-12" style="height: 30px;">
                                            <select data-page-id="{{$page->id}}" data-platform="{{$type}}" class="browser-default custom-select languageID" id="languageID">
                                                <option selected value="">Translate</option>
                                                @foreach($languages as $lang)
                                                    <option value="{{$lang->id}}">{{$lang->name}}</option>
                                                @endforeach
                                            </select>
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


@include('panel-partials.scripts', ['page' => 'pagemetatagstrans'])
@include('panel-partials.datatable-scripts', ['page' => 'pagemetatagstrans'])
