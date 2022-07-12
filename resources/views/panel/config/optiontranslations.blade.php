@include('panel-partials.head', ['page' => 'optiontranslations'])
@include('panel-partials.header', ['page' => 'optiontranslations'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Option Translations</h4>
                    <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
                </div>
                <div class="tab-inn">
                    <input type="hidden" id="pageID" name="pageID" value="1">
                    <input type="hidden" id="isRun" name="isRun" value="0">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference Code</th>
                                <th>Title</th>
                                <th>Option Status</th>
                                <th>Translate Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach ($options as $option)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td style="width: 10%;">{{$option->referenceCode}}</td>
                                    <td style="width: 25%;">{{$option->title}}</td>
                                    <td>
                                        @if($option->isPublished == 1)
                                            <span style="background-color: green;color:white;font-weight: bold;padding: 5px;">Option Published</span>
                                        @else
                                            <span style="background-color: red;color:white;font-weight: bold;padding: 5px;">Option Not Published</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($languages as $lang)
                                            <p>{{$lang->name}}:
                                                @if((new \App\Http\Controllers\Admin\ConfigController)->isTranslated($option->id, $lang->id, 'option'))
                                                    <span style="color:#84A98C;">Translated</span>
                                                @else
                                                    <span style="color:#D1495B;">Not Translated</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="col-lg-12" style="height: 30px;">
                                            <select data-option-id="{{$option->id}}" class="browser-default custom-select languageID" id="languageID">
                                                <option selected value="">Translate</option>
                                                @foreach($languages as $lang)
                                                    <option value="{{$lang->id}}">{{$lang->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'optiontranslations'])
@include('panel-partials.datatable-scripts', ['page' => 'optiontranslations'])
