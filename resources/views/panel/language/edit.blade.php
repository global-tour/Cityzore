@include('panel-partials.head', ['page' => 'language-edit'])
@include('panel-partials.header', ['page' => 'language-edit'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Edit Language Variables</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <div class="col-md-12">
            <div class="col-md-2 pull-right">
                <input type="text" class="form-control" value="" id="searchInput" placeholder="Search">
            </div>
        </div>
        <h2>Edit Variables for {{$lang->name}}</h2>
        <form style="margin-top: 20px;" id="languageUpdateForm" method="POST" action="{{url('language/update')}}" class="form-horizontal form-label-left languageUpdateForm">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="fileName" value="{{$lang->fileName}}">
            <input type="hidden" name="languageID" id="languageID" value="{{$lang->id}}">
            <input type="hidden" id="pageCount" value="{{$pageCount}}">
            <div class="row" id="keyValuesRow">
                @foreach($langArr as $key => $value)
                <div class="col-md-12">
                    <div class="col-md-3">
                        <label for="list-title">{{$key}}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="{{$key}}" value="{{$value}}" class="validate keyValueInputs">
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="input-field col s12 text-center">
                    <span class="btn btn-primary" id="prevButton" disabled data-direction="prev"><i class="icon-cz-angle-left"></i>Prev</span>
                    <span class="btn btn-sm btn-primary pageButton activeButton" data-step="0">1</span>
                    <span class="btn btn-sm btn-primary dotButton willBeAddedDot" style="display:none;" disabled>...</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton twoAndThree" data-step="1">2</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton twoAndThree" data-step="2">3</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton beforeFive" data-step="3">4</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton fiveButton" data-step="4">5</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton nextAfterFive" style="display:none;" data-step="5">6</span>
                    <span class="btn btn-sm btn-primary dotButton willBeRemovedDot" disabled>...</span>
                    <span class="btn btn-sm btn-primary pageButton disabledButton" data-step="{{$pageCount-1}}">{{$pageCount}}</span>
                    <span class="btn btn-sm btn-primary" id="nextButton" data-step="0" data-direction="next">Next<i class="icon-cz-angle-right"></i></span>
                    <button id="languageUpdateSubmitButton" class="btn btn-large pull-right">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'language-edit'])
