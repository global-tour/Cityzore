@include('panel.av.head')
@include('panel.av.header')
@include('panel-partials.sidebar')

<div class="option-edit sb2-2 sb2-2-1">
    <h4>Create a New Availability</h4>
    <hr>
    <div class="col-md-12">
        <div class="form-group col-md-12" style="margin-top: 40px;">
            <input name="radioTime" type="radio" id="radioTime" value="Starting Time" checked="checked" />
            <label for="radioTime">Starting Time</label>
            <input name="radioTime" type="radio" id="radioOperatingHours" value="Operating Hours" />
            <label for="radioOperatingHours">Operating Hours</label>
        </div>
    </div>
    <div class="form-group col-md-12">
        <div class="input-field col-md-6">
            <input id="avName" name="avName" type="text" class="validate form-control">
            <label for="avName">Name</label>
        </div>
        <span class="avNameErrorSpan errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
    </div>
    <div class="col-md-12">
        <div class="form-group col-md-3">
            <label class="col-md-12" style="font-size: 1.1rem;">is Limitless?</label>
            <div class="form-group col-md-1">
                <input type="checkbox" name="limitlessTicket" class="filled-in" id="limitlessTicket" value="0" />
                <label for="limitlessTicket">Limitless</label>
            </div>
        </div>
        <div class="form-group col-md-5">
            <select class="browser-default custom-select col-md-9" name="ticketType" id="ticketType">
                <option value="" selected disabled>Choose a Ticket Type</option>
                <option value="0">No Ticket</option>
                @foreach($ticketTypes as $tt)
                    <option value="{{$tt->id}}">{{$tt->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="tab-inn" style="padding: 0!important;">
        <ul style="box-shadow: none!important;" class="collapsible availability" data-collapsible="accordion">
            <li>
                <div class="collapsible-header coll-head"><i class="icon-cz-booking"></i>Date Range | <span id="dateRangeText1" class="dateRangeText"></span></div>
                <div style="border-bottom: 0!important;" class="collapsible-body coll-body">
                    <div class="newDateDiv" style="height: 100%!important;">
                        <div class="newDateWrapper">
                            <div class="form-group">
                                <input type="text" class="dateRange1" name="daterange[]" value="" />
                            </div>
                            <div class="form-group">
                                <label>Monday</label>
                                <div class="mondayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="mondayHour" name="mondayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForMonday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="mondayHourTo" name="mondayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('monday', 1, 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('monday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularMonday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularMonday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tuesday</label>
                                <div class="tuesdayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="tuesdayHour" name="tuesdayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForTuesday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="tuesdayHourTo" name="tuesdayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('tuesday', 1, 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('tuesday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularTuesday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularTuesday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Wednesday</label>
                                <div class="wednesdayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="wednesdayHour" name="wednesdayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForWednesday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="wednesdayHourTo" name="wednesdayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('wednesday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('wednesday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularWednesday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularWednesday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Thursday</label>
                                <div class="thursdayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="thursdayHour" name="thursdayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForThursday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="thursdayHourTo" name="thursdayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('thursday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('thursday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularThursday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularThursday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Friday</label>
                                <div class="fridayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="fridayHour" name="fridayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForFriday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="fridayHourTo" name="fridayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('friday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('friday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularFriday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularFriday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Saturday</label>
                                <div class="saturdayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="saturdayHour" name="saturdayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForSaturday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="saturdayHourTo" name="saturdayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('saturday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('saturday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularSaturday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularSaturday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Sunday</label>
                                <div class="sundayDiv11">
                                    <div class="col-md-12 input-field">
                                        <div class="hourDivFrom col-md-2">
                                            <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="sundayHour" name="sundayHour1[]" value="">
                                        </div>
                                        <div class="hourDivToForSunday col-md-2" style="display: none!important;">
                                            <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="sundayHourTo" name="sundayHourTo1[]" value="">
                                        </div>
                                        <div class="addMinHourButtonDiv col-md-2">
                                            <button onclick="addMinHour('sunday', 1, 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="copyToAllBelow('sunday', 1)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-left: 30px;">
                                    <a data-toggle="collapse" href="#regularSunday" aria-expanded="false" aria-controls="collapseExample" class="regularHrefElement">
                                        Regular Times
                                    </a>
                                    <div class="collapse" id="regularSunday">
                                        @include('panel.av.regular-content')
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button onclick="addDates(1);" data-id="1" class="addNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right">Add New Date Range</button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="col-md-12" style="margin-top: 20px;">
            <button id="saveAvailabilityButton" class="waves-effect waves-light btn btn-primary btn-large pull-left">Save Availability</button>
        </div>
    </div>
</div>


@include('panel.av.create-scripts')
