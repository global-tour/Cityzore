@include('panel-partials.head', ['page' => 'statistic'])
@include('panel-partials.header', ['page' => 'statistic'])
@include('panel-partials.sidebar')

<div class="sb2-2-2">
    <ul>
        <li><a href="/"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"> Dashboard</li>
    </ul>
</div>
<div class="row">
    <div style="margin-top: 50px" class="col-md-12">
        <div class="pull-right daterange" id="daterange-btn"></div>
        <div class="text-center apex-date-span" style="margin-bottom: -48px;text-align: center!important;">
            <span>Yükleniyor...</span><br>
            <img style="margin-top: 50px" src="/img/loading1.gif"  alt="">
        </div>
        <div id="chart"></div>
    </div>
</div>
<div class="row " style="margin-bottom: 50px">
    <div class="col-lg-6 col-md-12">
        <div class="text-center" id="daterange-btn-opt-div">
            <button class="btn btn-xs btn-default daterange" style="margin: 30px" id="daterange-btn-opt">Yükleniyor...
            </button>
        </div>
        <div class="text-center apex-date-span-opt" style="text-align: center!important;">
            <img style="margin-top: 50px;margin-bottom: -28px" src="/img/loading1.gif" alt="">
        </div>
        <div id="chart-opt"></div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="text-center" id="daterange-btn-opt-c-div">
            <button class="btn btn-xs btn-default daterange" style="margin: 30px" id="daterange-btn-opt-cancel">
                Yükleniyor...
            </button>
        </div>
        <div class="text-center apex-date-span-opt-cancel" style="text-align: center!important;">
            <img style="margin-top: 50px;margin-bottom: -28px" src="/img/loading1.gif" alt="">
        </div>
        <div id="chart-opt-cancel"></div>
    </div>
</div>
<div class="row " style="margin-bottom: 150px">
    <div class="col-lg-6 col-md-12">
        <div id="chart-column"></div>
    </div>
    <div class="col-lg-3 col-md-12">
        <div id="chart-monochrome"></div>
    </div>
    <div class="col-lg-3 col-md-12">
        <div id="chart-monochrome2"></div>
    </div>
</div>
<br>
</div>


@include('panel-partials.scripts', ['page' => 'statistic'])
