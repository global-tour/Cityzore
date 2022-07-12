@include('panel-partials.head', ['page' => 'language-create'])
@include('panel-partials.header', ['page' => 'language-create'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Add New Language</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Add New Language</h2>
        <form id="languageStoreForm" method="POST" action="{{url('language/store')}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Select Language</label>
                    <select class="browser-default custom-select col-md-9" name="langCode" id="languageSelector">
                        @foreach($langCodes as $key => $langCode)
                            <option value="{{$key}}">{{$langCode}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group input-field col-md-12">
                    <input name="displayName" type="text" id="displayName">
                    <label style="margin-left: 20px" for="displayName">Display Name</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'language-create'])
