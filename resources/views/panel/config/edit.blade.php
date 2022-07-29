@include('panel-partials.head', ['page' => 'config-edit'])
@include('panel-partials.header', ['page' => 'config-edit'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Edit Configuration</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Edit Configuration</h2>
        <form id="configUpdateForm" method="POST" action="{{url('config/'.$config->id.'/update')}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Select Currency</label>
                    <select class="browser-default custom-select col-md-9" name="currencyID" id="currencyID">
                        @foreach($currencies as $currency)
                            <option value="{{$currency->id}}" @if ($currency->id == $config->currencyID) selected @endif>{{$currency->currency}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Select Language</label>
                    <select class="browser-default custom-select col-md-9" name="languageID" id="languageID">
                        @foreach($languages as $lang)
                            <option value="{{$lang->id}}" @if ($lang->id == $config->languageID) selected @endif>{{$lang->name}}</option>
                        @endforeach
                    </select>
                </div>
                @if (auth()->guard('admin')->check())
                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Auto Coupon Discount Type on New User Registration</label>
                    <select class="browser-default custom-select col-md-9" name="couponDiscountType" id="couponDiscountType">
                        <option value="percent" @if ($config->couponDiscountType == 'percent') selected @endif>Percent</option>
                        <option value="net rate" @if ($config->couponDiscountType == 'net rate') selected @endif>Net Rate</option>
                    </select>
                </div>
                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Auto Coupon Discount Amount on New User Registration</label>
                    <input class="col s9 col-md-9" type="number" value="{{$config->couponDiscountAmount}}" name="couponDiscountAmount" id="couponDiscountAmount">
                </div>

                <div class="col-md-12" style="margin-top: 50px;">
                    <label class="col-md-3">Maximum Shifting Distance for Guide (meter)</label>
                    <input class="col s9 col-md-9" type="number" value="{{$config->meeting_distance}}" name="meeting_distance" id="meeting_distance">
                </div>

                    <div class="col-md-12" style="margin-top: 50px;">
                        <label class="col-md-3">hide options for sold avs</label>
                        <input class="col s1 col-md-1" type="checkbox" style="opacity: 1;" {{$config->hide_options_for_sold_avs ? 'checked' : ''}} name="hide_options_for_sold_avs">
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'config-edit'])
