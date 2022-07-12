<div class="tab-pane fade option-setup-content" id="step3">
    <input type="hidden" id="opt_meeting_point" name="opt_meeting_point" class="opt_meeting_point" value="">
    <input type="hidden" id="opt_meeting_point_lat" name="opt_meeting_point_lat" class="opt_meeting_point_lat" value="">
    <input type="hidden" id="opt_meeting_point_long" name="opt_meeting_point_long" class="opt_meeting_point_long" value="">
    <div class="form-group" style="height: 100px!important;">
        <div class="input-field col-md-12 s12">
            <div class="input-field col-md-3 s3">
                <input id="opt_cut_time" name="opt_cut_time" type="number" class="validate form-control" min="0">
                <label for="opt_cut_time">Cut Of Time</label>
            </div>
            <div class="input-field col-md-9 s9">
                <select class="browser-default custom-select" name="opt_cut_time_date" id="opt_cut_time_date">
                    <option selected value="">Please select...</option>
                    <option value="m">Minute(s)</option>
                    <option value="h">Hour(s)</option>
                    <option value="d">Day(s)</option>
                </select>
            </div>
            <span class="opt_cut_timeErrorSpan errorSpan col-md-12 s12" style="display: none!important; color: #ff0000;">These fields are required.</span>
        </div>
    </div>
    <div class="form-group" style="height: 120px!important;">
        <div class="input-field col-md-12 s12">
            <div class="input-field col-md-3 s3">
                <input id="opt_tour_duration" name="opt_tour_duration" type="number" class="validate form-control" min="0">
                <label for="opt_tour_duration">Tour Duration</label>
            </div>
            <div class="input-field col-md-9 s9">
                <select class="browser-default custom-select" name="opt_tour_duration_date" id="opt_tour_duration_date">
                    <option selected value="">Please select...</option>
                    <option value="m">Minute(s)</option>
                    <option value="h">Hour(s)</option>
                    <option value="d">Day(s)</option>
                </select>
            </div>
            <span class="opt_tour_durationErrorSpan col-md-12 s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required.</span>
        </div>
    </div>


        <div class="form-group" style="height: 120px!important;">
        <div class="input-field col-md-12 s12">
            <div class="input-field col-md-3 s3">
                <input id="opt_guide_time" name="opt_guide_time" type="number" class="validate form-control" min="0">
                <label for="opt_guide_time">Meeting Start Time</label>
            </div>
            <div class="input-field col-md-9 s9">
                <select class="browser-default custom-select" name="opt_guide_time_type" id="opt_guide_time_type">
                    <option selected value="">Please select...</option>
                    <option value="m">Minute(s)</option>
                    <option value="h">Hour(s)</option>
                    <option value="d">Day(s)</option>
                </select>
            </div>
            {{--<span class="opt_tour_durationErrorSpan col-md-12 s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required.</span>--}}
        </div>
    </div>




            <div class="form-group" style="height: 120px!important;">
                    <div class="input-field col-md-12">
                        <div class="input-field col-md-3">
                            <input id="opt_cancel_policy_time" name="opt_cancel_policy_time" type="number" class="validate form-control" min="0" value="">
                            <label for="opt_cancel_policy_time">Cancel Policy Time</label>
                        </div>
                        <div class="input-field col-md-9">
                            <select class="browser-default custom-select" name="opt_cancel_policy_time_type" id="opt_cancel_policy_time_type">
                                <option selected value="">Please select...</option>
                                <option value="m" >Minute(s)</option>
                                <option value="h" >Hour(s)</option>
                                <option value="d" >Day(s)</option>
                            </select>
                        </div>
                        <span class="opt_cancel_policy_ErrorSpan col-md-12 s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required.</span>
                    </div>
                </div>


</div>
