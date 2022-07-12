<div class="container-fluid sb1">
    <div class="row">
        <div class="col-md-2 col-sm-3 col-xs-6 sb1-1">
            <a id="logo" href="/" class="logo"><img src="{{asset('img/paris-city-tours-logo.png')}}" alt="" style="margin-top: 4%;"/>
            </a>
        </div>
        <div class="col-md-2 col-sm-3 col-xs-6" style="float: right;font-size: 17px;">
            <a class="waves-effect dropdown-button top-user-pro" href="{{ url('logout') }}" onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">{{ __('Logout') }}<i class="fa fa-sign-out"></i></a>
            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>
