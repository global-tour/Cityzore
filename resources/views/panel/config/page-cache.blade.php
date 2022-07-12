@include('panel-partials.head', ['page' => 'config-page-cache'])
@include('panel-partials.header', ['page' => 'config-page-cache'])
@include('panel-partials.sidebar')
<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#">
                    <i aria-hidden="true" class="fa fa-home">
                    </i>
                    Home
                </a>
            </li>
            <li class="active-bre">
                <a href="#">
                    Page Cache Configuration
                </a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>
            Edit Page Cache Configuration
        </h2>
        <form action="{{ url('/cache-config') }}" method="post">
            @if($message = session('message'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
            @endif
            <input class="csrfToken" name="_token" type="hidden" value="{{ csrf_token() }}">
              
              
                <div class="row" style="padding: 15px 5px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <h4 style="padding: 17px 0;">
                                    Dashboard Page
                                </h4>
                            </div>
                            <div class="col-md-3">
                                <input id="" max="5" min="1" name="dashboard[cache_time]" placeholder="cache time (hour)" type="number" @if(array_key_exists('dashboard', $data)) value="{{ $data['dashboard']['cache_time'] }}" @endif />
                            </div>
                            <div class="col-md-2" style="padding: 15px 0;">
                                <input class="form-control" id="" name="dashboard[cache_activation]" style="opacity:1; position:relative;" type="checkbox" @if(array_key_exists('dashboard', $data)) checked="checked" @endif  />
                                <span>
                                    Activation
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <button class="btn-block" type="submit">
                            Save Changes
                        </button>
                    </div>
                </div>
            </input>
        </form>
    </div>
</div>
@include('panel-partials.scripts', ['page' => 'config-page-cache'])
