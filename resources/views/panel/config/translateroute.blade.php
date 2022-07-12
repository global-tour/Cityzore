@include('panel-partials.head', ['page' => 'translateroute'])
@include('panel-partials.header', ['page' => 'translateroute'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Route from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Route from English to {{$languageToTranslate->name}}</h2>
        <form id="translateRouteForm" method="POST" action="{{url('general-config/saveRouteTranslation/'.$routeID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Title</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$route->route}}" name="routeEnglish" id="routeEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($routeLocalization) {{$routeLocalization->route}} @endif" name="route" id="route">
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


@include('panel-partials.scripts', ['page' => 'translateroute'])
