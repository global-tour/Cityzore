@include('panel-partials.head', ['page' => 'translatecity'])
@include('panel-partials.header', ['page' => 'translatecity'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate City from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate City from English to {{$languageToTranslate->name}}</h2>
        <form id="translateCityForm" method="POST" action="{{url('general-config/saveCityTranslation/'.$cityID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Name</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$city->name}}" name="nameEnglish" id="nameEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($cityTranslation) {{$cityTranslation->name}} @endif" name="name" id="name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large bnt-primary" value="Save Translation">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'translatecity'])
