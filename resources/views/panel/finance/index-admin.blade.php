@include('panel-partials.head', ['page' => 'finance-index'])
@include('panel-partials.header', ['page' => 'finance-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li>
            <a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li class="active-bre">
            <a href="#">Finance</a>
        </li>
        <li class="page-back">
            <a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a>
        </li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <div class="inn-title">
        <h4>Finance</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{url('/finance/get-bookings')}}" id="finance">
                @csrf
                <div class="row">
                    @if (auth()->guard('admin')->check())
                        <div class="input-field col s4">
                            <select class="browser-default custom-select" id="companySelect" name="companySelect">
                                <option disabled selected>Select Company</option>
                                <option value="-1">Paris Business and Travel</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->companyName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-field col s4">
                            <select class="browser-default custom-select" id="commissioner" name="commissioner">
                                <option disabled selected>Select Commissioner</option>
                                @foreach($commissioners as $commissioner)
                                    <option value="{{$commissioner->id}}">{{$commissioner->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-field col s4">
                            <select class="browser-default custom-select" id="platforms" name="platforms">
                                <option disabled selected>Select Platforms</option>
                                @foreach($platforms as $platform)
                                    <option value="{{$platform->id}}">{{$platform->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @if ($areThereBookings)
                        <button class="btn btn-default pull-right" type="submit" style="margin:2%;">Get Bookings</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'finance-index'])
@include('panel-partials.datatable-scripts', ['page' => 'finance-index'])

