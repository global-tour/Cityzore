<!--== MAIN CONTRAINER ==-->

<div class="container-fluid sb1">
    <div class="row">
        <!--== LOGO ==-->
        <div class="col-md-2 col-sm-3 col-xs-6 sb1-1">
            <a href="#" class="btn-close-menu">X</a>
            <a href="#" class="atab-menu hidden-lg"><i class="icon-cz-hamburger" aria-hidden="true" style="font-size: 14px;"></i></a>
            <a id="logo" href="/" class="logo"><img src="{{asset('img/paris-city-tours-logo.png')}}" alt=""/>
            </a>
        </div>
        @if(auth()->guard('admin')->check())
            <div style="top:5px;position:absolute;font-size: 12px;padding: 15px;" class="col-lg-8 col-md-8 col-sm-8 hidden-xs label label-primary">Hello {{auth()->guard('admin')->user()->name}} ! Welcome to CityZore Beta v1.0.
            You may encounter with some errors. If you'll catch a bug, please <a style="text-decoration: underline;color: white" href="mailto:software@cityzore.com?subject=I caught the bug!&body=Dear Software Team, I caught the bug in your system!">contact</a> with software team.</div>
        @elseif(auth()->guard('supplier')->check())
            <div style="top:5px;position:absolute;font-size: 12px;padding: 15px;" class="col-lg-8 col-md-8 col-sm-8 hidden-xs label label-primary">Hello {{auth()->guard('supplier')->user()->companyName}} ! Welcome to CityZore Beta v1.0.
                You may encounter with some errors. If you'll catch a bug, please <a style="text-decoration: underline;color: white" href="mailto:software@cityzore.com?subject=I caught the bug!&body=Dear Software Team, I caught the bug in your system!">contact</a> with software team.</div>
        @endif
        @if ($page != 'bookings-index-for-restaurant')
        <div class="col-md-2 col-sm-3 col-xs-6" style="float: right;font-size: 17px;">
            <a class="waves-effect dropdown-button top-user-pro" href="{{ url('logout') }}" onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">{{ __('Logout') }}<i class="icon-cz-logout"></i></a>
            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @if (auth()->guard('admin')->check())
            @include('dynamic-components.notifications')
            @endif
        </div>
        @endif
    </div>
</div>
