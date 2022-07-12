@include('panel.option-partials.head')
@include('panel.option-partials.header')
@include('panel-partials.sidebar')


<div class="option-edit sb2-2 sb2-2-1">
    <h2 style="margin-bottom: 2%;">Edit Option {{$option->title}}</h2>

    <a href="{{asset('/av/create')}}">
        <button class="btn btn-primary" style="float: right;">Create Availability</button>
    </a>

    @if($option->tootbus()->count() || $option->bigBus()->count())
        <a href="#" style="margin-right: 5px !important;">
            <button class="btn btn-success active" id="tootbus-modal-trigger-button" data-toggle="modal"
                    data-target="#tootbus-modal" style="float: right;">
                Connected ({{ $option->tootbus()->count() + $option->bigBus()->count() }})
            </button>
        </a>
    @else

        <a href="#" style="margin-right: 5px !important;">
            <button class="btn btn-danger active" id="tootbus-modal-trigger-button" data-toggle="modal"
                    data-target="#tootbus-modal" style="float: right;">
                Not Connected
            </button>
        </a>

    @endif
    <input type="hidden" id="optionId" value="{{$option->id}}">
    <input type="hidden" name="page_id" class="pageID" value="{{$pageID}}">
    <hr>
    <div class="navbar">
        <div class="navbar-inner">
            <ul class="nav nav-pills option-setup-panel">
                <li class="active"><a id="step1Tab" href="#step1" data-toggle="tab" data-step="1">Step 1</a></li>
                <li><a id="step2Tab" href="#step2" data-toggle="tab" data-step="2" disabled="">Step 2</a></li>
                <li><a id="step3Tab" href="#step3" data-toggle="tab" data-step="3" disabled="">Step 3</a></li>
                <li><a id="step4Tab" href="#step4" data-toggle="tab" data-step="4" disabled="">Step 4</a></li>
                <li><a id="step5Tab" href="#step5" data-toggle="tab" data-step="5" disabled="">Step 5</a></li>

            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade in active option-setup-content" id="step1">
            <div class="form-group">
                <div class="input-field col s12">
                    <input id="opt_title" name="opt_title" type="text" class="validate form-control"
                           value="{{$option->title}}">
                    <label for="opt_title">Title</label>
                </div>
                <span class="opt_titleErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <div class="form-group">
                <div class="input-field col s12">
                    <textarea id="opt_desc" name="opt_desc" type="text"
                              class="materialize-textarea form-control">{{$option->description}}</textarea>
                    <label for="opt_desc">Description</label>
                </div>
                <span class="opt_descErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <?php
            $included = implode('{}', explode('|', $option->included));
            $notIncluded = implode('{}', explode('|', $option->notIncluded));
            $knowBeforeYouGo = implode('{}', explode('|', $option->knowBeforeYouGo));

            ?>
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12"
                     style="margin-bottom: 15px;">
                    <div>
                        <span style="font-size: 20px!important; color: black;">What's Included</span><br>
                        <span
                            style="color:blue;font-size:13px!important;">Example: Hotel pickup and drop-off</span>
                        <input name="included" id="included" value="{{$included}}" type="text"
                               class="tags form-control manipulate-form-control"/>
                        <a id="includedcollapsetrigger" data-toggle="collapse" href="#includedcollapse"
                           aria-expanded="false" aria-controls="includedcollapse">
                            Copy Paste Operation
                        </a>

                        <div class="collapse" id="includedcollapse">
                            <div class="form-group">
                                <label for="includedarea">Seperator: ⚈</label>
                                <textarea class="form-control" id="includedarea" rows="5"></textarea>
                            </div>
                            <button id="includedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">
                                Process
                            </button>
                        </div>
                    </div>
                </div>
                <span class="includedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12"
                     style="margin-bottom: 15px;">
                    <div>
                        <span style="font-size: 20px!important; color: black;">What's Not Included</span><br>
                        <span style="color:blue;font-size:13px!important;">Example: Food and drinks</span>
                        <input name="notIncluded" id="notincluded" value="{{$notIncluded}}" type="text"
                               class="tags form-control manipulate-form-control"/>
                        <a id="notincludedcollapsetrigger" data-toggle="collapse" href="#notincludedcollapse"
                           aria-expanded="false" aria-controls="notincludedcollapse">
                            Copy Paste Operation
                        </a>

                        <div class="collapse" id="notincludedcollapse">
                            <div class="form-group">
                                <label for="includedarea">Seperator: ⚈</label>
                                <textarea class="form-control" id="notincludedarea" rows="5"></textarea>
                            </div>
                            <button id="notincludedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">
                                Process
                            </button>
                        </div>
                    </div>
                </div>
                <span class="notIncludedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12"
                     style="margin-bottom: 15px;">
                    <div>
                        <span style="font-size: 20px!important; color: black;">Know Before You Go</span><br>
                        <span style="color:blue;font-size:13px!important;">Example: This tour is not recommended for people with limited mobility.</span>
                        <input name="knowBeforeYouGo" id="beforeyougo" value="{{$knowBeforeYouGo}}" type="text"
                               class="tags form-control manipulate-form-control"/>
                        <a id="beforeyougocollapsetrigger" data-toggle="collapse" href="#beforeyougocollapse"
                           aria-expanded="false" aria-controls="beforeyougocollapse">
                            Copy Paste Operation
                        </a>

                        <div class="collapse" id="beforeyougocollapse">
                            <div class="form-group">
                                <label for="includedarea">Seperator: ⚈</label>
                                <textarea class="form-control" id="beforeyougoarea" rows="5"></textarea>
                            </div>
                            <button id="beforeyougoprocess" class="btn" style="background-color: #1B3033; color: #FFF;">
                                Process
                            </button>
                        </div>
                    </div>
                </div>
                <span class="beforeyougoErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
        </div>
        <div class="tab-pane fade option-setup-content" id="step2">

            <input type="hidden" name="skip_the_line" value="{{$option->isSkipTheLine}}">
            <input type="hidden" name="is_free_cancellation" value="{{$option->isFreeCancellation}}">
            <input type="hidden" name="guide_information" value="{{$option->guideInformation}}">
            <div class="form-group">
                <div class="input-field col s12">
                    <input id="minPerson" name="minPerson" type="number" class="validate form-control" min="1"
                           value="{{$option->minPerson}}">
                    <label for="minPerson">Min. Person Count</label>
                </div>
            </div>
            <div class="form-group">
                <div class="input-field col s12">
                    <input id="maxPerson" name="maxPerson" type="number" class="validate form-control" min="1"
                           value="{{$option->maxPerson}}">
                    <label for="maxPerson">Max. Person Count</label>
                </div>
            </div>


            <br>
            <hr>


            <div class="row" style="margin-bottom: 50px;">


                <div class="form-group">


                    <div class="input-field col s12">

                        <div class="switch mar-bot-20">

                            <label>
                                <span
                                    style="font-size: 12px; color: #000; padding: 0 0 0 30px;">Free cancellation ?</span>
                                <input type="checkbox" @if($option->isFreeCancellation == 1) checked
                                       @endif id="is-free-cancellation">
                                <span class="lever"></span>
                            </label>
                        </div>

                    </div>


                </div>


            </div>


            <div class="row" style="margin-bottom: 50px;">


                <div class="form-group">


                    <div class="input-field col s12">

                        <div class="switch mar-bot-20">

                            <label>
                                <span style="font-size: 12px; color: #000; padding: 0 0 0 30px;">Skip The Line ?</span>
                                <input type="checkbox" @if($option->isSkipTheLine == 1) checked
                                       @endif id="skip-the-line">
                                <span class="lever"></span>
                            </label>
                        </div>

                    </div>


                </div>


            </div>

            <div class="row" style="padding: 0 30px;">
                <div class="form-group">

                    <input type="checkbox" class="filled-in guide_information"
                           @if(!empty($option->guideInformation) && in_array("Live Guide", json_decode($option->guideInformation, true))) checked
                           @endif  id="live-guide" value="Live Guide"/>
                    <label for="live-guide">Live Guide</label>
                </div>

                <div class="form-group">

                    <input type="checkbox" class="filled-in guide_information"
                           @if(!empty($option->guideInformation) && in_array("Audio Guide", json_decode($option->guideInformation, true))) checked
                           @endif  id="audio-guide" value="Audio Guide"/>
                    <label for="audio-guide">Audio Guide</label>
                </div>

                <div class="form-group">

                    <input type="checkbox" class="filled-in" @if($option->mobileBarcode == 1) checked
                           @endif  id="mobile-barcode" value="Mobile Barcode"/>
                    <label for="mobile-barcode">Mobile Barcode</label>
                </div>
            </div>


            <span class="minMaxPersonErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required and Min. Person Count must be equal or less than Max. Person Count.</span>
        </div>
        <div class="tab-pane fade option-setup-content" id="step3">
            <input type="hidden" id="opt_meeting_point" name="opt_meeting_point" class="opt_meeting_point"
                   value="{{$option->meetingPoint}}">
            <input type="hidden" id="opt_meeting_point_lat" name="opt_meeting_point_lat" class="opt_meeting_point_lat"
                   value="{{$option->meetingPointLat}}">
            <input type="hidden" id="opt_meeting_point_long" name="opt_meeting_point_long"
                   class="opt_meeting_point_long" value="{{$option->meetingPointLong}}">
            <div class="form-group" style="height: 30px!important;">
                <input name="radioMPorT" type="radio" id="meetingPointPin" value="Meeting Point"
                       @if(!is_null($option->meetingPoint)) checked="checked" @endif/>
                <label for="meetingPointPin">Meeting Point</label>
                <input name="radioMPorT" type="radio" id="meetingPointDesc" value="Transfer"
                       @if(!is_null($option->meetingPointDesc)) checked="checked" @endif/>
                <label for="meetingPointDesc">Transfer</label>
            </div>
            <div class="form-group" id="meetingPointDiv"
                 @if(is_null($option->meetingPoint)) style="display: none;" @endif>
                <div class="pac-card" id="pac-card">
                    <div>
                        <div id="title">
                            Autocomplete search
                        </div>
                        <div id="type-selector" class="pac-controls">
                            <input type="radio" name="type" id="changetype-all" checked="checked">
                            <label for="changetype-all">All</label>

                            <input type="radio" name="type" id="changetype-establishment">
                            <label for="changetype-establishment">Establishments</label>

                            <input type="radio" name="type" id="changetype-address">
                            <label for="changetype-address">Addresses</label>

                            <input type="radio" name="type" id="changetype-geocode">
                            <label for="changetype-geocode">Geocodes</label>
                        </div>
                        <div id="strict-bounds-selector" class="pac-controls">
                            <input type="checkbox" id="use-strict-bounds" value="">
                            <label for="use-strict-bounds">Strict Bounds</label>
                        </div>
                    </div>
                    <div id="pac-container" style="z-index: 9999!important;">
                        <input id="pac-input" type="text"
                               placeholder="Enter a location" value="{{$option->meetingPoint}}">
                    </div>
                </div>
                <div id="map"></div>
                <div id="infowindow-content">
                    <img src="" width="16" height="16" id="place-icon">
                    <span id="place-name" class="title"></span><br>
                    <span id="place-address"></span>
                </div>
                <span class="meetingPointErrorSpan errorSpan col-md-12"
                      style="display: none!important; color: #ff0000;">This field is required.</span>
                <div class="form-group">
                    <div class="input-field col-md-12">
                        <?php
                        $meetingComment = implode('{}', explode('|', $option->meetingComment));
                        ?>
                        <input name='meetingComment' id="meetingComment" placeholder='Meeting Comment'
                               value='{{$meetingComment}}'>
                    </div>
                </div>
            </div>
            <span class="meetingPointDescErrorSpan errorSpan col-md-12"
                  style="display: none!important; color: #ff0000;">This field is required.</span>
            <div id="meetingPointDescDiv" class="form-group"
                 @if(is_null($option->meetingPointDesc)) style="display: none;" @endif>
                <div class="input-field col-md-12">
                    <input id="meetingPointDescInput" name="meetingPointDescInput" type="text"
                           class="validate form-control" value="{{$option->meetingPointDesc}}">
                    <label for="meetingPointDescInput">Description</label>
                </div>
            </div>
            <div class="form-group" style="height: 100px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_cut_time" name="opt_cut_time" type="number" class="validate form-control" min="0"
                               value="{{$option->cutOfTime}}">
                        <label for="opt_cut_time">Cut Of Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_cut_time_date" id="opt_cut_time_date">
                            <option value="">Please select...</option>
                            <option value="m" @if($option->cutOfTimeDate == 'm') selected @endif>Minute(s)</option>
                            <option value="h" @if($option->cutOfTimeDate == 'h') selected @endif>Hour(s)</option>
                            <option value="d" @if($option->cutOfTimeDate == 'd') selected @endif>Day(s)</option>
                        </select>
                    </div>
                    <span class="opt_cut_timeErrorSpan errorSpan col-md-12"
                          style="display: none!important; color: #ff0000;">These fields are required.</span>
                </div>
            </div>
            <div class="form-group" style="height: 120px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_tour_duration" name="opt_tour_duration" type="number"
                               class="validate form-control" min="0" value="{{$option->tourDuration}}">
                        <label for="opt_tour_duration">Tour Duration</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_tour_duration_date"
                                id="opt_tour_duration_date">
                            <option selected value="">Please select...</option>
                            <option value="m" @if($option->tourDurationDate == 'm') selected @endif>Minute(s)</option>
                            <option value="h" @if($option->tourDurationDate == 'h') selected @endif>Hour(s)</option>
                            <option value="d" @if($option->tourDurationDate == 'd') selected @endif>Day(s)</option>
                        </select>
                    </div>
                    <span class="opt_tour_durationErrorSpan col-md-12 s12 errorSpan"
                          style="display: none!important; color: #ff0000;">These fields are required.</span>
                </div>
            </div>

            <div class="form-group" style="height: 120px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_guide_time" name="opt_guide_time" type="number" class="validate form-control"
                               min="0" value="{{$option->guideTime}}">
                        <label for="opt_guide_time">Meeting Start Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_guide_time_type"
                                id="opt_guide_time_type">
                            <option selected value="">Please select...</option>
                            <option value="m" @if($option->guideTimeType == 'm') selected @endif>Minute(s)</option>
                            <option value="h" @if($option->guideTimeType == 'h') selected @endif>Hour(s)</option>
                            <option value="d" @if($option->guideTimeType == 'd') selected @endif>Day(s)</option>
                        </select>
                    </div>
                    {{--<span class="opt_tour_durationErrorSpan col-md-12 s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required.</span>--}}
                </div>
            </div>


            <div class="form-group" style="height: 120px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_cancel_policy_time" name="opt_cancel_policy_time" type="number"
                               class="validate form-control" min="0" value="{{$option->cancelPolicyTime}}">
                        <label for="opt_cancel_policy_time">Cancel Policy Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_cancel_policy_time_type"
                                id="opt_cancel_policy_time_type">
                            <option selected value="">Please select...</option>
                            <option value="m" @if($option->cancelPolicyTimeType == 'm') selected @endif>Minute(s)
                            </option>
                            <option value="h" @if($option->cancelPolicyTimeType == 'h') selected @endif>Hour(s)</option>
                            <option value="d" @if($option->cancelPolicyTimeType == 'd') selected @endif>Day(s)</option>
                        </select>
                    </div>
                    <span class="opt_cancel_policy_ErrorSpan col-md-12 s12 errorSpan"
                          style="display: none!important; color: #ff0000;">These fields are required.</span>
                </div>
            </div>


        </div>
        <div class="tab-pane fade option-setup-content" id="step4">
            <h4>Choose a Pricing</h4>
            <hr>
            <div class="form-group">
                <select class="select2 browser-default custom-select" name="pricings" id="pricings">
                    <option value="">Choose a Pricing</option>
                    @foreach($pricings as $pricing)
                        <option value="{{$pricing->id}}"
                                @if($option->pricings == $pricing->id) selected @endif>{{$pricing->title}}</option>
                    @endforeach
                </select>
                <span class="pricingsErrorSpan errorSpan col s12" style="display: none!important; color: #ff0000;">You must choose a pricing.</span>
            </div>
            <h4 style="margin-top: 20px;">Choose Ticket Type</h4>
            <div class="form-group">
                <select class="select2 browser-default custom-select" name="ticketTypes" id="ticketTypes">
                    <option value="" selected>Not Selected</option>
                    @foreach($ticketTypes as $ticketType)
                        <option value="{{$ticketType->id}}"
                                @if(in_array($ticketType->id, $optionTicketTypes)) selected @endif>{{$ticketType->name}}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" class="avCount" value="0"/>
            <h4 style="margin-top: 40px;">Choose an Availability</h4>
            <hr>
            <div class="form-group">
                <label>Is This Option Mixed?</label>
                <div class="col-md-12" style="height: 55px;">
                    <div class="col-md-2">
                        <input name="isMixed" type="radio" id="radioMixedNo" value="0"
                               @if($option->isMixed == 0) checked="checked" @endif/>
                        <label for="radioMixedNo">No</label>
                    </div>
                    <div class="col-md-4">
                        <input name="isMixed" type="radio" id="radioMixedYes" value="1"
                               @if($option->isMixed == 1) checked="checked" @endif/>
                        <label for="radioMixedYes">Yes</label>
                    </div>
                </div>
            </div>
            <div class="avPane select-av">
                @if (count($option_availabilities) > 0)
                    @foreach($option_availabilities as $i => $opt_av)
                        <div class="form-group col-md-12" style="margin-bottom: 30px;">
                            <select class="browser-default custom-select col-md-11 availabilities av-select"
                                    name="availabilities[]" id="availabilities">
                                <option value="">Choose an Availability</option>
                                @foreach($availabilities as $availability)
                                    <option value="{{$availability->id}}"
                                            @if($opt_av->id == $availability->id) selected @endif>{{$availability->name}}</option>
                                @endforeach
                            </select>
                            @if($i == 0)
                                <button class="btn btn-primary addNewAvSelectBox col-md-1"
                                        @if(count($option_availabilities) == 1) style="display: none;" @endif>+
                                </button>
                            @else
                                <button class="btn btn-primary deleteNewAvSelectBox col-md-1">x</button>
                            @endif
                            <span class="availabilitiesErrorSpan errorSpan col s12"
                                  style="display: none!important; color: #ff0000;">You must choose an availability.</span>
                        </div>
                    @endforeach
                @else
                    <div class="form-group col-md-12" style="margin-bottom: 30px;">
                        <select class="browser-default custom-select col-md-11 availabilities" name="availabilities[]"
                                id="availabilities">
                            <option value="" selected>Choose an Availability</option>
                            @foreach($availabilities as $availability)
                                <option value="{{$availability->id}}">{{$availability->name}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary addNewAvSelectBox col-md-1" style="display: none;">+</button>
                        <span class="availabilitiesErrorSpan errorSpan col s12"
                              style="display: none!important; color: #ff0000;">You must choose an availability.</span>
                    </div>
                @endif
            </div>
            <?php use \App\Http\Controllers\VariableController; ?>
            <div class="form-group">
                <h4 style="margin-top: 40px;">Blockout Hours</h4>
                <hr>
                <div class="btn btn-primary addBlockoutBlock" style="background-color: #e34f2a;">+</div>
                <div class="blockoutContainer">
                    @foreach($blockoutHours as $blockoutHour)
                        <div class="row">
                            <div class="col-md-2 s12">
                                <label>Months</label>
                                <select class="browser-default custom-select col-md-11 months" multiple>
                                    @foreach(VariableController::returnMonths() as $key => $month)
                                        <option value="{{$key}}"
                                                @if(isset($blockoutHour["months"]) && in_array($key, $blockoutHour["months"])) selected @endif>{{$month}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 s12">
                                <label>Days</label>
                                <select class="browser-default custom-select col-md-11 days" multiple>
                                    @foreach(VariableController::returnDays() as $day)
                                        <option value="{{$day}}"
                                                @if(isset($blockoutHour["days"]) && in_array($day, $blockoutHour["days"])) selected @endif>{{$day}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 s12">
                                <label>Hours</label>
                                <select class="browser-default custom-select col-md-11 hours" multiple>
                                    @if(isset($blockoutHour["hours"]))
                                        @foreach($blockoutHour["hours"] as $hour)
                                            <option value="{{$hour}}" selected>{{$hour}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 s12">
                                <div class="btn btn-primary addBlockoutHour" style="background-color: #1E8449" ;>+</div>
                                <input style="margin: 0 30px 20px 0;" type="time"
                                       class="validate form-control blockoutHour" value="">
                            </div>
                            <div class="col-md-2 s12">
                                <div class="btn btn-primary removeBlockoutBlock" style="background-color: #FF3333" ;>x
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tab-pane fade option-setup-content" id="step5">

            <div id="map-area">
                <input type="hidden" name="addresses" value="{{$option->addresses}}">

                <div class="pac-card2" id="pac-card2">
                    <div>
                        <div id="title2">
                            Autocomplete search
                        </div>
                        <div id="type-selector2" class="pac-controls2">
                            <input type="radio" name="type" id="changetype-all2" checked="checked">
                            <label for="changetype-all2">All</label>

                            <input type="radio" name="type" id="changetype-establishment2">
                            <label for="changetype-establishment2">Establishments</label>

                            <input type="radio" name="type" id="changetype-address2">
                            <label for="changetype-address2">Addresses</label>

                            <input type="radio" name="type" id="changetype-geocode2">
                            <label for="changetype-geocode2">Geocodes</label>
                        </div>
                        <div id="strict-bounds-selector2" class="pac-controls2">
                            <input type="checkbox" id="use-strict-bounds2" value="">
                            <label for="use-strict-bounds2">Strict Bounds</label>
                        </div>
                    </div>
                    <div id="pac-container2" style="z-index: 9999!important;">
                        <input id="pac-input2" type="text"
                               placeholder="Enter a location" value="">
                    </div>
                </div>
                <div id="map2"></div>
                <div id="infowindow-content2">
                    <img src="" width="16" height="16" id="place-icon2">
                    <span id="place-name2" class="title2"></span><br>
                    <span id="place-address2"></span>
                </div>


                <div id="selected-address">

                    @php
                        $addresses = json_decode($option->addresses, true);
                    @endphp

                    @if(!empty($addresses))

                        @foreach ($addresses as $address)

                            <div class="selected-item"
                                 data-address-title="{{empty($address["address_title"]) ? '' : $address["address_title"]}}"
                                 data-address="{{$address["address"]}}" data-address-lat="{{$address["address_lat"]}}"
                                 data-address-lng="{{$address["address_lng"]}}">
                                <div class="title-area"><input type="text" class="form-control address-title"
                                                               value="{{empty($address["address_title"]) ? '' : $address["address_title"]}}">
                                </div>
                                <span>{{$address["address"]}}: ({{$address["address_lat"]}}) lng: ({{$address["address_lng"]}}) </span>
                                <i class="delete-address-item pull-right icon-cz-trash"></i>
                            </div>



                        @endforeach


                    @endif


                </div>

            </div>
            <br><br>
            <hr>


            <div class="col-md-12" style="margin-bottom: 25px;font-size: 16px!important;letter-spacing: 1px;">
                <label class="col-md-8 label label-info">If you would like to delete a contact information field, please
                    leave it blank.</label>
                <button style="position: absolute;right: 0;top:-15px" class=" btn" id="addNewContactInformationLabel">
                    Add New Contact Box
                </button>
            </div>
            <h4>Contact Informations</h4>
            @if(!is_null($option->contactInformationFields))
                <div id="contactInformationDiv" class="form-group">
                    <input id="contactInformationIterator" hidden
                           value="{{count(json_decode($option->contactInformationFields, true))}}">
                    @foreach(json_decode($option->contactInformationFields, true) as $key =>  $contactInformationField)
                        <div class="contact-info-group col-md-12">
                            <div class="col-md-6">
                                <input class="contact-info-title" style="border: none!important;"
                                       name="newContactInformation{{$key}}" id="newContactInformation{{$key}}"
                                       placeholder="Add a name..." value="{{$contactInformationField['title']}}">
                            </div>
                            <div class="col-md-6">
                                <input class="contact-info-checkbox"
                                       @if($contactInformationField['isRequired'] == 1) value="1" checked
                                       @endif type="checkbox" id="isRequired{{$key}}">
                                <label for="isRequired{{$key}}">is Required?</label>
                                <button class="btn btn-primary remove-row btn-lg pull-right" type="button"
                                        onclick="$(this).parent().parent().remove();" style="">Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div id="contactInformationDiv" class="form-group">
                    <input id="contactInformationIterator" hidden value="1">
                    <div class="contact-info-group col-md-12">
                        <div class="col-md-6">
                            <input class="contact-info-title" style="border: none!important;"
                                   name="newContactInformation0" id="newContactInformation0"
                                   placeholder="Add a name...">
                        </div>
                        <div class="col-md-6">
                            <input class="contact-info-checkbox" value="0" type="checkbox" id="isRequired0">
                            <label for="isRequired0">is Required?</label>
                            <button class="btn btn-primary remove-row btn-lg pull-right" type="button"
                                    onclick="$(this).parent().parent().remove();" style="">Remove
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            <div class="avPane">

            </div>


            <div class="col-md-6">
                <input class="col-md-12" @if($option->contactForAllTravelers == 1) checked value="1" @else value="0"
                       @endif type="checkbox" id="contactForAllTravelers">
                <label for="contactForAllTravelers">Would you like to get informations for all travelers?</label>
            </div>


            @php
                $customerTemplates = $option->customer_mail_templates ? json_decode($option->customer_mail_templates, true) : [];
                $customerWhatsAppTemplates = $option->customer_whatsapp_templates ? json_decode($option->customer_whatsapp_templates, true) : [];
            @endphp
            <input type="hidden" name="customer_mail_templates" value="{{$option->customer_mail_templates}}">
            <input type="hidden" name="customer_whatsapp_templates" value="{{$option->customer_whatsapp_templates}}">

            <div class="col-md-12" style="margin-top: 30px;">
                <div class="row">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#mail">Mail Template <i class="icon-cz-mail"></i></a>
                        </li>
                        <li><a data-toggle="tab" href="#whatsapp">WhatsApp Template <i class="icon-cz-whatsapp"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="customer-tab-content-wrap">
                    <div id="mail" class="tab-pane fade in active">
                        <h1>Mail Template For Customer</h1>

                        <div class="form-group">

                            <ul class="nav nav-tabs">

                                <li class="active"><a data-toggle="tab" href="#en">EN</a></li>
                                <li><a data-toggle="tab" href="#fr">FR</a></li>
                                <li><a data-toggle="tab" href="#tr">TR</a></li>
                                <li><a data-toggle="tab" href="#ru">RU</a></li>
                                <li><a data-toggle="tab" href="#es">ES</a></li>
                                <li><a data-toggle="tab" href="#de">DE</a></li>
                                <li><a data-toggle="tab" href="#it">IT</a></li>
                                <li><a data-toggle="tab" href="#pt">PT</a></li>
                                <li><a data-toggle="tab" href="#nd">ND</a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="en" class="tab-pane fade in active">

                                    <textarea name="en" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["en"] ?? ''}}</textarea>
                                </div>
                                <div id="fr" class="tab-pane fade">

                                    <textarea name="fr" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["fr"] ?? ''}}</textarea>
                                </div>
                                <div id="tr" class="tab-pane fade">

                                    <textarea name="tr" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["tr"] ?? ''}}</textarea>
                                </div>
                                <div id="ru" class="tab-pane fade">

                                    <textarea name="ru" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["ru"] ?? ''}}</textarea>
                                </div>
                                <div id="es" class="tab-pane fade">

                                    <textarea name="es" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["es"] ?? ''}}</textarea>
                                </div>
                                <div id="de" class="tab-pane fade">

                                    <textarea name="de" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["de"] ?? ''}}</textarea>
                                </div>
                                <div id="it" class="tab-pane fade">

                                    <textarea name="it" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["it"] ?? ''}}</textarea>
                                </div>
                                <div id="pt" class="tab-pane fade">

                                    <textarea name="pt" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["pt"] ?? ''}}</textarea>
                                </div>
                                <div id="nd" class="tab-pane fade">

                                    <textarea name="nd" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerTemplates["nd"] ?? ''}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="whatsapp" class="tab-pane fade">
                        <h1>WhatsApp Template For Customer</h1>

                        <div class="form-group">

                            <ul class="nav nav-tabs">

                                <li class="active"><a data-toggle="tab" href="#en_wp">EN</a></li>
                                <li><a data-toggle="tab" href="#fr_wp">FR</a></li>
                                <li><a data-toggle="tab" href="#tr_wp">TR</a></li>
                                <li><a data-toggle="tab" href="#ru_wp">RU</a></li>
                                <li><a data-toggle="tab" href="#es_wp">ES</a></li>
                                <li><a data-toggle="tab" href="#de_wp">DE</a></li>
                                <li><a data-toggle="tab" href="#it_wp">IT</a></li>
                                <li><a data-toggle="tab" href="#pt_wp">PT</a></li>
                                <li><a data-toggle="tab" href="#nd_wp">ND</a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="en_wp" class="tab-pane fade in active">

                                    <textarea name="en" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["en"] ?? ''}}</textarea>
                                </div>
                                <div id="fr_wp" class="tab-pane fade">

                                    <textarea name="fr" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["fr"] ?? ''}}</textarea>
                                </div>
                                <div id="tr_wp" class="tab-pane fade">

                                    <textarea name="tr" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["tr"] ?? ''}}</textarea>
                                </div>
                                <div id="ru_wp" class="tab-pane fade">

                                    <textarea name="ru" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["ru"] ?? ''}}</textarea>
                                </div>
                                <div id="es_wp" class="tab-pane fade">

                                    <textarea name="es" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["es"] ?? ''}}</textarea>
                                </div>
                                <div id="de_wp" class="tab-pane fade">

                                    <textarea name="de" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["de"] ?? ''}}</textarea>
                                </div>
                                <div id="it_wp" class="tab-pane fade">

                                    <textarea name="it" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["it"] ?? ''}}</textarea>
                                </div>
                                <div id="pt_wp" class="tab-pane fade">

                                    <textarea name="pt" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["pt"] ?? ''}}</textarea>
                                </div>
                                <div id="nd_wp" class="tab-pane fade">

                                    <textarea name="nd" id="" cols="30" rows="10"
                                              style="height: 500px;">{{$customerWhatsAppTemplates["nd"] ?? ''}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <form id="option-files-form">
                        @csrf
                        <h4 style="margin-top: 40px;">Files</h4>
                        <hr>
                        <div class="btn btn-primary addFileBlock" style="background-color: #e34f2a;">+</div>
                        <input type="hidden" name="option_id" value="{{$option->id}}"/>
                        <div class="filesContainer">
                            @foreach($optionFiles as $optionFile)
                                <div class="row" style="margin-bottom: 5px; margin-top: 5px;">
                                    <div class="col-md-4 s12" style="padding-top: 7px;">
                                        <input type="text" disabled value="{{$optionFile->fileName}}"
                                               data-id="{{$optionFile->id}}"/>
                                    </div>
                                    <div class="col-md-2 s12">
                                        <div class="btn btn-primary removeUploadedFileBlock"
                                             style="background-color: #FF3333" ;>x
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button class="btn btn-primary saveUploadedFiles"
                                style="background-color: #1c2d3f; margin-top: 5px;" type="submit">Save Files
                        </button>
                    </form>
                </div>


            </div><!--end of col-->


        </div>


    </div>

</div>
<div class="col-md-12">
    <button class="btn btn-primary nextBtnForOpt btn-lg pull-right" data-step="1" type="button"
            style="margin-bottom:30px;">Next
    </button>
    <button class="btn btn-primary prevBtnForOpt btn-lg pull-left" type="button"
            style="margin-bottom:30px; margin-right: 10px; display:none;">Previous
    </button>
    <button data-action="update" class="btn btn-primary saveUpdateBtnForOpt btn-lg pull-right" type="button"
            style="display:none;">Save
    </button>
</div>


<!-- tootbus Modal -->
<div class="modal fade" id="tootbus-modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Api Connection Modal</h4>
            </div>
            <div class="modal-body">

                <ul class="nav nav-tabs">

                    <li class="nav-item active">
                        <a class="nav-link" aria-current="page" data-toggle="tab"
                           href="#tootbus">Tootbus @if($option->tootbus()->count()) ({{$option->tootbus()->count()}}
                            ) @endif</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#big-bus">Big Bus @if($option->bigBus()->count())
                                ({{$option->bigBus()->count()}}) @endif</a>
                    </li>

                </ul>

                <div class="tab-content">

                    <div id="tootbus" class="tab-pane fade in active">
                        <form action="#" id="tootbus-connection-form" style="margin-top: 15px">

                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <input type="hidden" name="action" value="set_tootbus_connection">
                            <input type="hidden" name="option_id" value="{{$option->id}}">

                            @if($option->tootbus()->count())

                                <input type="hidden" name="tootbus_id" value="{{$option->tootbus->id}}">

                            @else
                                <input type="hidden" name="tootbus_id" value="">
                            @endif

                            <div class="form-group">
                                <label for="">tootbus Product ID</label>
                                <input type="text" class="form-control" name="tootbus_product_id"
                                       @if($option->tootbus()->count()) value="{{$option->tootbus->tootbus_product_id}}" @endif>
                            </div>

                            <div class="form-group">
                                <label for="">tootbus Option ID</label>
                                <input type="text" class="form-control" name="tootbus_option_id"
                                       @if($option->tootbus()->count()) value="{{$option->tootbus->tootbus_option_id}}" @endif>
                            </div>
                        </form>

                        <div id="tootbus-option-data">
                            @if($option->tootbus()->count())
                                @php
                                    $toot_body = json_decode($option->tootbus->body, true);
                                    $apiRelated = new \App\Http\Controllers\Helpers\ApiRelated();
                                    $jsonq = $apiRelated->prepareJsonQ();
                                    $nahid_json =$jsonq->json($option->tootbus->body);

                                    $query_option_id = !empty($option->tootbus->tootbus_option_id) ? $option->tootbus->tootbus_option_id: "DEFAULT";
                                    $target_option = $nahid_json->from("options")->where("id", "=", $query_option_id)->first();

                                     $html = "";
                                    foreach($target_option["units"] as $unit){
                                     $html.= "<span>".$unit["id"]."</span> ";
                                    }

                                    if(empty($toot_body["internalName"])){
                                        $toot_body["internalName"] = "";
                                    }


                                @endphp
                                <p class="availability-type">
                                    <b>Type:</b><br>
                                    {{$toot_body["availabilityType"]}}
                                </p>
                                <p class="title">
                                    <b>Title:</b><br>
                                    {{!empty($toot_body["title"]) ? $toot_body["title"] : $toot_body["internalName"]}}
                                </p>
                                <p class="short-description">
                                    <b>Short Description:</b><br>
                                    {{!empty($toot_body["shortDescription"]) ? $toot_body["shortDescription"] : ''}}
                                </p>
                                <p class="units">
                                    <b>Active Units</b><br>

                                    {!!$html!!}

                                </p>

                                <div class="container-fluid">


                                    <div class="row">

                                        @if(!empty($toot_body["galleryImages"]))
                                            @foreach ($toot_body["galleryImages"] as $img)
                                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-top: 10px;"><img
                                                        style="width: 100%; height: 100px;" src="{{$img["url"]}}"
                                                        alt=""></div>
                                            @endforeach
                                        @endif

                                    </div>

                                </div>



                            @endif

                        </div>

                        <button type="button" class="btn btn-success active" id="save-tootbus-information-button">
                            Connect
                        </button>
                        <div class="delete-button-area" style="display: inline;"> @if($option->tootbus()->count())
                                <button data-id="{{$option->tootbus->id}}" type="button" class="btn btn-danger active"
                                        id="delete-tootbus-information-button">Disconnect
                                </button> @endif
                        </div>
                    </div>

                    <div id="big-bus" class="tab-pane fade">

                        @if($option->bigBus()->count())
                            @php
                                $bigbus = json_decode($option->bigBus->body, 1);
                            @endphp

                            <table class="table table-hover" style="margin-top: 15px">
                                <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Option Name</th>
                                    <th>Type</th>
                                    <th>Active Units</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ $bigbus['id'] }}</td>
                                    <td>{{ $bigbus['internalName'] }}</td>
                                    <td>{{ $bigbus['options'][0]['internalName'] }}</td>
                                    <td>{{ $bigbus['availabilityType'] }}</td>
                                    <td>
                                        @foreach($bigbus['options'][0]['units'] as $unit)
                                            @if($loop->last)
                                                {{ $unit['internalName'] }}
                                            @else
                                                {{ $unit['internalName'] }},
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <button type="button"
                                    class="btn btn-danger active"
                                    data-disconnect="{{ $option->bigBus->id }}"
                                    style="margin-top: 15px">
                                Disonnect
                            </button>

                        @else
                        <!-- BIGBUS-FORM::start -->
                            <form action="#" id="bigbusConnectionForm" style="margin-top: 15px">

                                <input type="hidden" name="option_id" value="{{$option->id}}">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden" name="type" value="bigbusConnect">

                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Product</label>
                                        <select class="custom-select select2 select2-hidden-accessible"
                                                name="productId"
                                                id="productId">
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <!-- BIGBUS-FORM::end -->

                            <button type="button"
                                    class="btn btn-success active"
                                    data-connect
                                    style="margin-top: 15px">
                                Connect
                            </button>

                        @endif

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


@include('panel.option-partials.scripts')

<script>
    $('#productId').select2({
        ajax: {
            url: '/get-bigbus-products',
            type: 'post',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            },
            delay: 800,
            processResults: function (data) {
                return {
                    results: $.map(data.data, function (item) {
                        return {
                            text: item.internalName,
                            id: item.id
                        }
                    })
                }
            }
        },
        placeholder: 'Search for a product',
    })

    $(document).ready(function () {
        $('[data-connect]').on('click', function () {
            waitme(false);
            const $form = $(this).parent().find('form')[0];
            const form = $($form).serialize();
            // const _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/api-connect-form',
                type: 'post',
                data: form,
                beforeSend: function () {
                },
                success: function (data) {
                    waitme(true)
                    Materialize.toast(data.message, 4000, 'toast-success');
                    $($form).find('select').attr('disabled', 'disabled')

                    setTimeout(() => {
                        location.reload()
                    }, 1300)

                },
                error: function (error) {
                    Materialize.toast(error.responseJSON.errorMessage, 4000, 'toast-alert');
                    waitme(true)
                }
            })
        })

        $('[data-disconnect]').on('click', function () {
            waitme(false)

            $.ajax({
                url: '/api-disconnect-form',
                type: 'post',
                data: {
                    id: $(this).data('disconnect'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    waitme(true)
                    Materialize.toast(data.message, 4000, 'toast-success');
                    setTimeout(() => {
                        location.reload()
                    }, 1300)
                },
                error: function (error) {

                },
            })
        });

        function waitme(close) {
            if (close) {
                $('#tootbus-modal .modal-body').waitMe('hide')
            } else {
                $('#tootbus-modal .modal-body').waitMe({
                    effect: 'win8',
                    text: '',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    waitTime: -1,
                    textPos: 'vertical',
                    fontSize: '',
                    source: '',
                    onClose: function () {
                    }
                });
            }
        }
    })
</script>
