@include('panel-partials.head', ['page' => 'translatecountry'])
@include('panel-partials.header', ['page' => 'translatecountry'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Country from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Country from English to {{$languageToTranslate->name}}</h2>
        <form id="translateCountryForm" method="POST" action="{{url('general-config/saveCountryTranslation/'.$countryID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Name</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$country->countries_name}}" name="countries_nameEnglish" id="countries_nameEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($countryTranslation) {{$countryTranslation->countries_name}} @endif" name="countries_name" id="countries_name">
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


@include('panel-partials.scripts', ['page' => 'translatecountry'])
