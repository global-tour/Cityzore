@include('panel.av.head')
@include('panel.av.header')

<div style="margin-top: 100px;" class="container-fluid">
    <div class="row pull-right" style="margin-right: 1%;margin-bottom: 10px">
        <a href="/availabilities" role="button" class="btn btn-default btn-lg hidden-xs " data-toggle="modal">Return</a>
        <a href="/" role="button" class="btn btn-default btn-lg hidden-xs " data-toggle="modal">Go to Dashboard</a>
    </div>
</div>
<div class="" style="margin-bottom: 10%; margin-right: 5%; margin-left: 5%;">
    <input type="hidden" id="isLimitlessHidden" value="{{$availability->isLimitless}}">
    <input type="hidden" id="availabilityId" value="{{$availability->id}}">
    <input type="hidden" id="availabilityType" value="{{$availability->availabilityType}}">
    <input type="hidden" id="minDate" value="{{$minDate}}">
    <input type="hidden" id="maxDate" value="{{$maxDate}}">
    <input type="hidden" id="avTicketType" value="{{$availability->avTicketType}}">
    <input type="hidden" id="selectedDate" value="">
    <input type="hidden" id="avdateCount" value="{{count($avdates)}}">
    <input type="hidden" id="notValidForBlockout" value="{{$notValidForBlockout}}">
    <div class="col-md-12">
        <div class="col-lg-2">
            <a class="anchorClass btn btn-primary" style="background: #555;width: 100%;" href="#calendarOperations">Calendar Operations</a>
        </div>
        <div class="col-lg-2">
            <a class="anchorClass btn btn-primary" style="background: #555;width: 100%;" href="#generalInformation">General Information</a>
        </div>
        <div class="col-lg-2">
            <a class="anchorClass btn btn-primary" style="background: #555;width: 100%;" href="#dateOperations">Date Operations</a>
        </div>
        <div class="col-lg-3">
            <a class="anchorClass btn btn-primary" style="background: #555;width: 100%;" href="#disableEnableDaysMonthsYears">Disable/Enable Days - Months - Years</a>
        </div>
        <div class="col-lg-2">
            <a class="anchorClass btn btn-primary" style="background: #555;width: 100%;" href="#connectedProductsOptions">Connected Products/Options</a>
        </div>
    </div>
    <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px;"><b><a style="font: inherit!important; color: inherit!important;" id="calendarOperations">Calendar Operations</a></b></label>
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                Please note that if you want to add tickets to a specific date or time, you need to click to the date two times.<br>
                If you want to add bulk tickets, you need to select two different dates.
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="datepicker-here" data-range="true" data-language='en'></div>
            </div>
            <div class="col-md-12 col-md-12 col-sm-12" style="display: block;overflow-x: auto;white-space: nowrap;padding: 0px;">
                <div id="hourParentDiv" style="margin-left: auto;margin-right: auto;width: 72%;margin-top: 1%;">
                    <label class="col-md-2 hidden-xs" id="dateLabel" style="font-size: 1.1rem; color: #0e76a8;"></label>
                    <label class="col-md-6 col-xs-12 hidden-xs" style="font-size: 1.1rem;">Hour and Ticket Count Configuration</label>
                    <div class="col-md-3 col-xs-7" id="disableEnableDiv" style="display: none; margin-bottom: 2%;">
                        <button class="btn btn-primary" id="disableEnableDayButton" style=""></button>
                    </div>
                    <div class="help-tip hidden-xs hidden-sm">
                        <p style="width: 125px;">Add New Hour</p>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-6 col-xs-5">
                        <button class="btn btn-primary" id="addNewDateTimeAndTicket" style="padding: 0 1rem;margin-bottom: 2%; display:none;background: #26a69a;">+</button>
                    </div>
                    <div id="hourTicketDiv">

                    </div>
                    @if($availability->availabilityType == 'Starting Time')
                        <div class="col-md-12" style="margin: 0; padding: 0; margin-bottom: 10px; display: none;">
                            <a data-toggle="collapse" href="#regularNewElement" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularNewElement" data-type="single">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div id="bulkTicketDiv" style="display: none;">
                    <label class="col-md-12" style="font-size: 1.1rem; color: #0e76a8;">Add tickets to all hours between <label id="bulkDateLabel" style="font-size: 1.1rem; color: #0e76a8;"></label></label>
                    <div class="col-md-1" style="margin-top: 20px;">
                        <label for="bulkTicketCount">Ticket Count</label>
                        <input id="bulkTicketCount" type="number" class="col-md-12" value="0">
                    </div>
                    <div class="col-md-2" style="margin-top: 20px;">
                        <button class="btn btn-primary" id="saveBulkTicket" style="background: #26a69a;">Save Tickets</button>
                    </div>
                    <div class="col-md-3" style="margin-top: 20px;">
                        <button class="btn btn-primary" id="disableDateRangeButton" style="background: #f4364f;">Disable Date Range</button>
                    </div>
                    <div class="col-md-3" style="margin-top: 20px;">
                        <button class="btn btn-primary" id="enableDateRangeButton" style="background: #26a69a;">Enable Date Range</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 initiallyHiddenDivs" style="">
        <hr style="height: 10px!important;">
    </div>
    <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px;">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px;"><b><a style="font: inherit!important; color: inherit!important;" id="generalInformation">General Information</a></b></label>
        <div class="col-md-12" style="margin-top: 30px;">
            <div class="input-field col-md-6">
                <input id="avName" name="avName" type="text" class="validate form-control" value="{{$availability->name}}">
                <label for="avName">Name</label>
            </div>
        </div>
        <div class="col-md-12" style="margin-top: 30px;">
            <div class="form-group col-md-3">
                <label class="col-md-12" style="font-size: 1.1rem;">is Limitless?</label>
                <div class="form-group col-md-1">
                    <input type="checkbox" name="limitlessTicket" class="filled-in" id="limitlessTicket" value="{{$availability->isLimitless}}" @if($availability->isLimitless == 1) checked @endif />
                    <label for="limitlessTicket">Limitless</label>
                </div>
            </div>
            <div class="form-group col-md-4">
                <select class="browser-default custom-select col-md-9" name="ticketType" id="ticketType">
                    <option value="" selected disabled>Choose a Ticket Type</option>
                    <option value="0">No Ticket</option>
                    @foreach($ticketTypes as $tt)
                        <option value="{{$tt->id}}" @if(count($availability->ticketType) > 0) @if($availability->ticketType()->first()->id == $tt->id) selected @endif @endif>{{$tt->name}}</option>
                    @endforeach
                </select>
            </div>
            @if ($availability->availabilityType == 'Operating Hours' && $availability->avTicketType != 4)
                <div class="form-group col-md-4">
                    <input name="radioDailyOrDateRange" type="radio" id="radioDaily" value="Daily" @if ($availability->avTicketType == 2) checked="checked" @endif />
                    <label for="radioDaily">Daily Ticket</label>
                    <input name="radioDailyOrDateRange" type="radio" id="radioDateRange" value="Date Range" @if ($availability->avTicketType == 3) checked="checked" @endif />
                    <label for="radioDateRange">Date Range</label>
                </div>
            @endif
        </div>
        <div class="col-md-12" style="margin-bottom: 20px;">
            <button class="btn btn-primary" id="informationSaveButton" style="background: #26a69a;">Save Changes</button>
        </div>
    </div>
    <div class="col-md-12 initiallyHiddenDivs" style="">
        <hr style="height: 10px!important;">
    </div>
    <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px"><b><a style="font: inherit!important; color: inherit!important;" id="dateOperations">Date Operations</a></b></label>
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                You can extend date range by clicking the dates, remove or add new date range. By default, newly added date ranges are added as 1 month.
            </div>
        </div>
        <div class="col-md-12 dateRangeWrapper">

        </div>
        <div class="col-md-12">
            <div class="alert alert-info" role="alert" style="display: inline-block;margin-top: 3%;">
                Select which date range you want to add new hours. Then add new hours to any week day you want and click Save Hours button.
            </div>
        </div>
        <div class="col-md-12 weekDayHoursWrapper" style="margin-top: 50px!important;">
            <div class="col-md-12">
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Monday</label>
                    <div class="mondayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="mondayHour" name="mondayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForMonday col-md-3" style="">
                                    <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="mondayHourTo" name="mondayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <div class="help-tip hidden-xs hidden-sm">
                                        <p style="width: 125px;">Add New Hour</p>
                                    </div>
                                    <button onclick="addMinHour('monday', 1, 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float: right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <div class="help-tip hidden-xs hidden-sm">
                                    <p style="width: 125px;">Copy to Below</p>
                                </div>
                                <button onclick="copyToAllBelow('monday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularMonday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularMonday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Tuesday</label>
                    <div class="tuesdayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="tuesdayHour" name="tuesdayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForTuesday col-md-3" style="">
                                    <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="tuesdayHourTo" name="tuesdayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('tuesday', 1, 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('tuesday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularTuesday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularTuesday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Wednesday</label>
                    <div class="wednesdayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="wednesdayHour" name="wednesdayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForWednesday col-md-3" style="">
                                    <input style="width: 110%;"  type="time" class="validate form-control col-md-12 s12" id="wednesdayHourTo" name="wednesdayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('wednesday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('wednesday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularWednesday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularWednesday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Thursday</label>
                    <div class="thursdayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="thursdayHour" name="thursdayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForThursday col-md-3" style="">
                                    <input style="width: 110%;"  type="time" class="validate form-control col-md-12 s12" id="thursdayHourTo" name="thursdayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('thursday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('thursday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularThursday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularThursday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Friday</label>
                    <div class="fridayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="fridayHour" name="fridayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForFriday col-md-3" style="">
                                    <input style="width: 110%;"  type="time" class="validate form-control col-md-12 s12" id="fridayHourTo" name="fridayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('friday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('friday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularFriday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularFriday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Saturday</label>
                    <div class="saturdayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="saturdayHour" name="saturdayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForSaturday col-md-3" style="">
                                    <input style="width: 110%;"  type="time" class="validate form-control col-md-12 s12" id="saturdayHourTo" name="saturdayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('saturday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('saturday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularSaturday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularSaturday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="form-group col-lg-5 col-md-6 col-sm-6 col-xs-12">
                    <label>Sunday</label>
                    <div class="sundayDiv11">
                        <div class="col-md-12 input-field">
                            <div class="hourDivFrom col-md-3">
                                <input style="width: 110%;" type="time" class="validate form-control col-md-12 s12" id="sundayHour" name="sundayHour1[]" value="">
                            </div>
                            @if($availability->availabilityType == 'Operating Hours')
                                <div class="hourDivToForSunday col-md-3" style="">
                                    <input style="width: 110%;"  type="time" class="validate form-control col-md-12 s12" id="sundayHourTo" name="sundayHourTo1[]" value="">
                                </div>
                            @endif
                            @if($availability->availabilityType != 'Operating Hours')
                                <div class="addMinHourButtonDiv col-md-3 col-xs-6">
                                    <button onclick="addMinHour('sunday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small" style="float:right;background:#726E97;"><i class="icon-cz-add-date"></i></button>
                                </div>
                            @endif
                            <div class="col-md-2 col-xs-6">
                                <button onclick="copyToAllBelow('sunday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="background:#F0A202;"><i class="icon-cz-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    @if($availability->availabilityType == 'Starting Time')
                        <div style="margin-left: 30px;">
                            <a data-toggle="collapse" href="#regularSunday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                Regular Times
                            </a>
                            <div class="collapse" id="regularSunday">
                                @include('panel.av.regular-content')
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <button class="btn btn-primary" id="saveWeekdayTimeButton" style="margin-bottom: 20px; background: #26a69a;">Save Hours</button>
    </div>
    <div class="col-md-12 initiallyHiddenDivs" style="">
        <hr style="height: 10px!important;">
    </div>
    <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 50px;">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px;"><b><a style="font: inherit!important; color: inherit!important;" id="disableEnableDaysMonthsYears">Disable/Enable Days - Months - Years</a></b></label>
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                Click Days/Months/Years to disable them.
            </div>
        </div>
        <div class="col-md-12" style="margin-top: 30px;">
            <label class="col-md-12" style="font-size: 1.1rem; width:100%; margin-bottom: 20px;">Days</label>
            @foreach ($weekDays as $day)
            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <input type="checkbox" name="daysOfWeek[]" class="filled-in" id="{{$day}}" @if(!is_null($disabledWeekDays) && in_array($day, $disabledWeekDays)) value="1" checked @else value="0" @endif />
                <label for="{{$day}}">{{ucfirst($day)}}</label>
            </div>
            @endforeach
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="">
            <button class="btn btn-primary" id="daysOfWeekButton" style="background: #26a69a;">Save Changes for Days</button>
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="">
            <hr style="height: 10px!important;">
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="margin-top: 30px;">
            <label class="col-md-12" style="font-size: 1.1rem; width:100%; margin-bottom: 20px;">Months</label>
            @foreach ($months as $month => $monthName)
            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-6">
                <input type="checkbox" name="monthsOfYear[]" class="filled-in" id="{{$month}}" @if(!is_null($disabledMonths) && in_array($month, $disabledMonths)) value="1" checked @else value="0" @endif />
                <label for="{{$month}}">{{$monthName}}</label>
            </div>
            @endforeach
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="">
            <button class="btn btn-primary" id="monthsOfYearButton" style="background: #26a69a;">Save Changes for Months</button>
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="">
            <hr style="height: 10px!important;">
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="margin-top: 30px;">
            <label class="col-md-12" style="font-size: 1.1rem; margin-bottom: 20px;">Years</label>
            @foreach ($years as $year)
            <div class="form-group col-md-1">
                <input type="checkbox" name="years[]" class="filled-in" id="{{$year}}" @if(!is_null($disabledYears) && in_array($year, $disabledYears)) value="1" checked @else value="0" @endif />
                <label for="{{$year}}">{{$year}}</label>
            </div>
            @endforeach
        </div>
        <div class="col-md-12 initiallyHiddenDivs" style="margin-bottom: 20px;">
            <button class="btn btn-primary" id="yearsButton" style="background: #26a69a;">Save Changes for Years</button>
        </div>
    </div>
    <div class="col-md-12 initiallyHiddenDivs" style="">
        <hr style="height: 10px!important;">
    </div>
    <div class="wrapperClass col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 50px;">
        <label class="col-md-12" style="font-size: 20px; margin-top: 20px;"><b><a style="font: inherit!important; color: inherit!important;" id="connectedProductsOptions">Connected Products/Options</a></b></label>
        <div class="col-md-12">
            <label class="col-md-12" style="font-size: 18px; margin-top: 20px;"><b>Connected Products</b></label>
            @foreach($connectedProducts as $prod)
            <div class="col-md-2 text-center" style="margin-bottom: 3px;">
                <a class="col-md-12" target="_blank" href="{{url('/product/'.$prod->id.'/edit')}}" id="connectedProduct" style="border-radius: 10px; color: #ffffff; background-color: #726E97; font-size:16px!important; margin-bottom: 5px;">{{$prod->referenceCode}}</a>
            </div>
            @endforeach
        </div>
        <div class="col-md-12" style="margin-bottom: 20px;">
            <label class="col-md-12" style="font-size: 18px; margin-top: 20px;"><b>Connected Options</b></label>
            @foreach($connectedOptions as $opt)
            <div class="col-md-2 text-center" style="margin-bottom: 3px;">
                <a class="col-md-12" target="_blank" href="{{url('/option/'.$opt->id.'/edit')}}" id="connectedOption" style="border-radius: 10px; color: #ffffff; background-color: #F0A202; font-size:16px!important; margin-bottom: 5px;">{{$opt->referenceCode}}</a>
            </div>
            @endforeach
        </div>
    </div>
    <button onclick="topFunction()" id="scrollToTop" title="Go to top"><i class="icon-cz-angle-up"></i></button>
</div>


<div class="modal fade" id="bookingsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="modal-title" id="exampleModalLabel">Bookings</h5>
            </div>
            <div class="col-md-6">
                <h6 id="itemsEl" style="text-align: right;"></h6>
            </div>
        </div>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background-color: #ff3200" data-dismiss="modal" onclick="$('#bookingsModal .modal-body').html('')">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="onGoingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="modal-title" id="exampleModalLabel">On-Going</h5>
            </div>
        </div>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background-color: #ff3200" data-dismiss="modal" onclick="$('#onGoingModal .modal-body').html('')">Close</button>
      </div>
    </div>
  </div>
</div>

@include('panel.av.scripts')
