<div class="tab-pane fade option-setup-content" id="step6">
    <div class="form-group">
        <div class="input-field col s12">
            <span style="font-size: 18px!important;">Comment(Optional)</span>
            <input name="meetingComment" id="meetingComment" type="text" class="tags form-control"/>
        </div>
    </div>
    <div class="form-group" style="height: 30px!important;">
        <input name="radioMPorT" type="radio" id="meetingPointPin" value="Meeting Point" checked="checked" />
        <label for="meetingPointPin">Meeting Point</label>
        <input name="radioMPorT" type="radio" id="meetingPointDesc" value="Transfer" />
        <label for="meetingPointDesc">Transfer</label>
    </div>
    <div class="form-group" id="meetingPointPinDiv">
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
            <span id="place-name"  class="title"></span><br>
            <span id="place-address"></span>
        </div>
        <span class="meetingPointErrorSpan errorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
    </div>
    <span class="meetingPointDescErrorSpan errorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
    <div id="meetingPointDescDiv" style="display: none;" class="form-group">
        <div class="input-field col-md-12 s12">
            <input id="meetingPointDescInput" name="meetingPointDescInput" type="text" class="validate form-control">
            <label for="meetingPointDescInput">Description</label>
        </div>
    </div>
</div>

