@include('panel.option-partials.head')
@include('panel.option-partials.header')
@include('panel-partials.sidebar')


<div class="option-edit sb2-2 sb2-2-1">
    <h2 style="margin-bottom: 2%;">Create a New Option</h2>
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
                    <input id="opt_title" name="opt_title" type="text" class="validate form-control">
                    <label for="opt_title">Title</label>
                </div>
                <span class="opt_titleErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <div class="form-group">
                <div class="input-field col s12">
                    <textarea id="opt_desc" name="opt_desc" type="text"
                              class="materialize-textarea form-control"></textarea>
                    <label for="opt_desc">Description</label>
                </div>
                <span class="opt_descErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
            </div>
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12"
                     style="margin-bottom: 15px;">
                    <div>
                        <span style="font-size: 20px!important; color: black;">What's Included</span><br>
                        <span
                            style="color:blue;font-size:13px!important;">Example: Hotel pickup and drop-off</span>
                        <input name="included" id="included" type="text"
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
                        <input name="notIncluded" id="notincluded" type="text"
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
                        <input name="knowBeforeYouGo" id="beforeyougo" type="text"
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

            <input type="hidden" name="skip_the_line" value="0">
            <input type="hidden" name="is_free_cancellation" value="0">
            <input type="hidden" name="guide_information" value="">


            <div class="form-group">
                <div class="input-field col s12">
                    <input id="minPerson" name="minPerson" type="number" class="validate form-control" min="1">
                    <label for="minPerson">Min. Person Count</label>
                </div>
            </div>
            <div class="form-group">
                <div class="input-field col s12">
                    <input id="maxPerson" name="maxPerson" type="number" class="validate form-control" min="1">
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
                                <input type="checkbox" id="is-free-cancellation">
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
                                <input type="checkbox" id="skip-the-line">
                                <span class="lever"></span>
                            </label>
                        </div>

                    </div>


                </div>


            </div>

            <div class="row" style="padding: 0 30px;">
                <div class="form-group">

                    <input type="checkbox" class="filled-in guide_information" id="live-guide" value="Live Guide"/>
                    <label for="live-guide">Live Guide</label>
                </div>

                <div class="form-group">

                    <input type="checkbox" class="filled-in guide_information" id="audio-guide" value="Audio Guide"/>
                    <label for="audio-guide">Audio Guide</label>
                </div>

                <div class="form-group">

                    <input type="checkbox" class="filled-in" id="mobile-barcode" value="Mobile Barcode"/>
                    <label for="mobile-barcode">Mobile Barcode</label>
                </div>
            </div>


            <span class="minMaxPersonErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required and Min. Person Count must be equal or less than Max. Person Count.</span>
        </div>
        <div class="tab-pane fade option-setup-content" id="step3">
            <input type="hidden" id="opt_meeting_point" name="opt_meeting_point" class="opt_meeting_point" value="">
            <input type="hidden" id="opt_meeting_point_lat" name="opt_meeting_point_lat" class="opt_meeting_point_lat"
                   value="">
            <input type="hidden" id="opt_meeting_point_long" name="opt_meeting_point_long"
                   class="opt_meeting_point_long" value="">
            <div class="form-group" style="height: 30px!important;">
                <input name="radioMPorT" type="radio" id="meetingPointPin" value="Meeting Point" checked="checked"/>
                <label for="meetingPointPin">Meeting Point</label>
                <input name="radioMPorT" type="radio" id="meetingPointDesc" value="Transfer"/>
                <label for="meetingPointDesc">Transfer</label>
            </div>
            <div id="meetingPointPinDiv" class="form-group">
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
                               placeholder="Enter a location">
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
                    <div class="input-field colmd-12">
                        <input name='meetingComment' id="meetingComment" placeholder='Meeting Comment'>
                    </div>
                </div>
            </div>
            <span class="meetingPointDescErrorSpan errorSpan col-md-12"
                  style="display: none!important; color: #ff0000;">This field is required.</span>
            <div id="meetingPointDescDiv" style="display: none;" class="form-group">
                <div class="input-field col-md-12 s12">
                    <input id="meetingPointDescInput" name="meetingPointDescInput" type="text"
                           class="validate form-control">
                    <label for="meetingPointDescInput">Description</label>
                </div>
            </div>
            <div class="form-group" style="height: 100px!important;">
                <div class="input-field col-md-12 s12">
                    <div class="input-field col-md-3">
                        <input id="opt_cut_time" name="opt_cut_time" type="number" class="validate form-control"
                               min="0">
                        <label for="opt_cut_time">Cut Of Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_cut_time_date" id="opt_cut_time_date">
                            <option selected value="">Please select...</option>
                            <option value="m">Minute(s)</option>
                            <option value="h">Hour(s)</option>
                            <option value="d">Day(s)</option>
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
                               class="validate form-control" min="0">
                        <label for="opt_tour_duration">Tour Duration</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_tour_duration_date"
                                id="opt_tour_duration_date">
                            <option selected value="">Please select...</option>
                            <option value="m">Minute(s)</option>
                            <option value="h">Hour(s)</option>
                            <option value="d">Day(s)</option>
                        </select>
                    </div>
                    <span class="opt_tour_durationErrorSpan col-md-12errorSpan"
                          style="display: none!important; color: #ff0000;">These fields are required.</span>
                </div>
            </div>


            <div class="form-group" style="height: 120px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_guide_time" name="opt_guide_time" type="number" class="validate form-control"
                               min="0">
                        <label for="opt_guide_time">Meeting Start Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_guide_time_type"
                                id="opt_guide_time_type">
                            <option selected value="">Please select...</option>
                            <option value="m">Minute(s)</option>
                            <option value="h">Hour(s)</option>
                            <option value="d">Day(s)</option>
                        </select>
                    </div>
                    {{--<span class="opt_guide_durationErrorSpan col-md-12errorSpan" style="display: none!important; color: #ff0000;">These fields are required.</span>--}}
                </div>
            </div>


            <div class="form-group" style="height: 120px!important;">
                <div class="input-field col-md-12">
                    <div class="input-field col-md-3">
                        <input id="opt_cancel_policy_time" name="opt_cancel_policy_time" type="number"
                               class="validate form-control" min="0" value="">
                        <label for="opt_cancel_policy_time">Cancel Policy Time</label>
                    </div>
                    <div class="input-field col-md-9">
                        <select class="browser-default custom-select" name="opt_cancel_policy_time_type"
                                id="opt_cancel_policy_time_type">
                            <option selected value="">Please select...</option>
                            <option value="m">Minute(s)</option>
                            <option value="h">Hour(s)</option>
                            <option value="d">Day(s)</option>
                        </select>
                    </div>
                    <span class="opt_cancel_policy_ErrorSpan col-md-12 s12 errorSpan"
                          style="display: none!important; color: #ff0000;">These fields are required.</span>
                </div>
            </div>


        </div>
        <div class="tab-pane fade option-setup-content" id="step4">
            <h4>Choose a Pricing</h4>
            <div class="form-group">
                <select class="select2 browser-default custom-select" name="pricings" id="pricings">
                    <option selected>Choose a Pricing</option>
                    @foreach($pricings as $pricing)
                        <option value="{{$pricing->id}}">{{$pricing->title}}</option>
                    @endforeach
                </select>
                <span class="pricingsErrorSpan errorSpan col s12" style="display: none!important; color: #ff0000;">You must choose a pricing.</span>
            </div>
            <h4 style="margin-top: 20px;">Choose Ticket Type</h4>
            <div class="form-group">
                <select class="select2 browser-default custom-select" name="ticketTypes" id="ticketTypes">
                    <option value="" selected>Not Selected</option>
                    @foreach($ticketTypes as $ticketType)
                        <option value="{{$ticketType->id}}">{{$ticketType->name}}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" class="avCount" value="0"/>
            <div class="form-group" style="margin-top: 40px;">
                <label>Is This Option Mixed?</label>
                <div class="col-md-12" style="height: 55px;">
                    <div class="col-md-2">
                        <input name="isMixed" type="radio" id="radioMixedNo" value="0" checked="checked"/>
                        <label for="radioMixedNo">No</label>
                    </div>
                    <div class="col-md-4">
                        <input name="isMixed" type="radio" id="radioMixedYes" value="1"/>
                        <label for="radioMixedYes">Yes</label>
                    </div>
                </div>
            </div>
            <div class="avPane select-av">
                <div class="form-group col-md-12" style="margin-bottom: 30px;">
                    <select class="browser-default custom-select col-md-11 availabilities av-select"
                            name="availabilities[]" id="availabilities">
                        <option value="" selected>Choose an Availability</option>
                        @foreach($availabilities as $availability)
                            <option value="{{$availability->id}}">{{$availability->name}}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary addNewAvSelectBox col-md-1" style="display: none;">+</button>
                    <span class="availabilitiesErrorSpan errorSpan col s12"
                          style="display: none!important; color: #ff0000;">You must choose an availability.</span>
                </div>
            </div>
            <div class="form-group">
                <h4 style="margin-top: 40px;">Blockout Hours</h4>
                <hr>
                <div class="btn btn-primary addBlockoutBlock" style="background-color: #e34f2a;">+</div>
                <div class="blockoutContainer">

                </div>
            </div>
        </div>
        <div class="tab-pane fade option-setup-content" id="step5">

            <div id="map-area">
                <input type="hidden" name="addresses" value="">

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
            <div id="contactInformationDiv" class="form-group">
                <input id="contactInformationIterator" hidden value="1">
                <div class="contact-info-group col-md-12">
                    <div class="col-md-6">
                        <input class="contact-info-title" style="border: none!important;" name="newContactInformation0"
                               id="newContactInformation0" placeholder="Add a name...">
                    </div>
                    <div class="col-md-6">
                        <input class="contact-info-checkbox" value="0" type="checkbox" id="isRequired0">
                        <label for="isRequired0">is Required?</label>
                    </div>
                </div>
            </div>
            <button id="addNewContactInformationLabel">Add New Contact Box</button>
            <div class="avPane">

            </div>
            <div class="col-md-6">
                <input class="col-md-12" value="0" type="checkbox" id="contactForAllTravelers">
                <label for="contactForAllTravelers">Would you like to get informations for all travelers?</label>
            </div>


            <input type="hidden" name="customer_mail_templates" value="">
            <input type="hidden" name="customer_whatsapp_templates" value="">

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

                                    <textarea name="en" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="fr" class="tab-pane fade">

                                    <textarea name="fr" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="tr" class="tab-pane fade">

                                    <textarea name="tr" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="ru" class="tab-pane fade">

                                    <textarea name="ru" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="es" class="tab-pane fade">

                                    <textarea name="es" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="de" class="tab-pane fade">

                                    <textarea name="de" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="it" class="tab-pane fade">

                                    <textarea name="it" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="pt" class="tab-pane fade">

                                    <textarea name="pt" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="nd" class="tab-pane fade">

                                    <textarea name="nd" id="" cols="30" rows="10" style="height: 300px;"></textarea>
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

                                    <textarea name="en" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="fr_wp" class="tab-pane fade">

                                    <textarea name="fr" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="tr_wp" class="tab-pane fade">

                                    <textarea name="tr" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="ru_wp" class="tab-pane fade">

                                    <textarea name="ru" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="es_wp" class="tab-pane fade">

                                    <textarea name="es" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="de_wp" class="tab-pane fade">

                                    <textarea name="de" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="it_wp" class="tab-pane fade">

                                    <textarea name="it" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="pt_wp" class="tab-pane fade">

                                    <textarea name="pt" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                                <div id="nd_wp" class="tab-pane fade">

                                    <textarea name="nd" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div><!--end of col-->


        </div>
    </div>
    <div class="col-md-12">
        <button class="btn btn-primary nextBtnForOpt btn-lg pull-right" data-step="1" type="button"
                style="margin-bottom:30px;">Next
        </button>
        <button data-action="save" class="btn btn-primary saveUpdateBtnForOpt btn-lg" type="button"
                style="float:right; display:none;">Save
        </button>
        <button class="btn btn-primary prevBtnForOpt btn-lg pull-right" type="button"
                style="margin-bottom:30px; margin-right: 10px; display:none;">Previous
        </button>
    </div>
</div>


@include('panel.option-partials.scripts')
