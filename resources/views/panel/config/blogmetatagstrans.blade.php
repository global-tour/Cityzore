@include('panel-partials.head', ['page' => 'blogmetatagstrans'])
@include('panel-partials.header', ['page' => 'blogmetatagstrans'])
@include('panel-partials.sidebar')
<style>
    .inn-title .select-dropdown,  .inn-title .caret{
        display: none!important;
    }
</style>

<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Blog Meta Tags Translations</h4>
                    <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
                    <select name="" id="" class="" style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;" onchange="window.location.href = this.value">
                        <option value="/general-config/blog-meta-tags-translations" {{$type == 'cz' ? 'selected' : ''}}>Cityzore.com</option>
                        <option value="/general-config/blog-meta-tags-translations-pct" {{$type == 'pct' ? 'selected' : ''}}>Pariscitytours.fr</option>
                        <option value="/general-config/blog-meta-tags-translations-pctcom" {{$type == 'pctcom' ? 'selected' : ''}}>Paris-city-tours.com</option>
                        <option value="/general-config/blog-meta-tags-translations-ctp" {{$type == 'ctp' ? 'selected' : ''}}>Citytours.paris</option>
                    </select>
                </div>
                <div class="tab-inn">
                    <input type="hidden" id="pageID" name="pageID" value="1">
                    <input type="hidden" id="isRun" name="isRun" value="0">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Blog Title</th>
                                <th>Translate Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($blogs as $blog)
                                <tr>
                                    <td style="width: 25%;">{{$blog->title}}</td>
                                    <td>
                                        @foreach($languages as $lang)
                                            <p>{{$lang->name}}:
                                                @if((new \App\Http\Controllers\Admin\ConfigController)->isTranslated($blog->id, $lang->id, 'blogmetatags', $type))
                                                    <span style="color:#84A98C;">Translated</span>
                                                @else
                                                    <span style="color:#D1495B;">Not Translated</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="col-lg-12" style="height: 30px;">
                                            <select data-blog-id="{{$blog->id}}" data-platform="{{$type}}" class="browser-default custom-select languageID" id="languageID">
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


@include('panel-partials.scripts', ['page' => 'blogmetatagstrans'])
@include('panel-partials.datatable-scripts', ['page' => 'blogmetatagstrans'])
