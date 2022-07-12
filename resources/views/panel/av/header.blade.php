<div class="container-fluid sb1" >
    <div class="row" style="@isset($pageTitle) display:flex; justify-content: space-between; align-items: center @endisset">
        <div class="col-md-2 col-sm-3 col-xs-6">
            <a id="logo" href="/" class="logo"><img src="{{asset('img/paris-city-tours-logo.png')}}" alt="" style="margin-top: 4%;"/>
            </a>
        </div>
        @isset($pageTitle)
            <div class="col-md-6">
                <h3 style="color: #000">{{ $pageTitle }}</h3>
            </div>
        @endisset
        <div class="col-md-2 col-sm-3 col-xs-6" style="float: right;font-size: 17px;">
            <a class="waves-effect dropdown-button top-user-pro" href="{{ url('logout') }}" onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">{{ __('Logout') }}<i class="fa fa-sign-out"></i></a>
            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>
