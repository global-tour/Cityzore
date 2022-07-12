<div id="optionModal" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="margin-top: 50px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeOptionModal" data-dismiss="modal" aria-hidden="true" style="opacity: 1!important;">x</button>
                <h3 id="myModalLabel">Create a New Option</h3>
            </div>
            <div class="modal-body" id="myWizard" style="height: 600px!important; overflow-y: auto!important;">

                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="6" style="width: 20%;">
                        Step 1 of 6
                    </div>
                </div>

                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav nav-pills option-setup-panel">
                            <li class="active"><a id="step1Tab" href="#step1" data-toggle="tab" data-step="1">Step 1</a></li>
                            <li><a id="step2Tab" href="#step2" data-toggle="tab" data-step="2" disabled="">Step 2</a></li>
                            <li><a id="step3Tab" href="#step3" data-toggle="tab" data-step="3" disabled="">Step 3</a></li>
                            <li><a id="step4Tab" href="#step4" data-toggle="tab" data-step="4" disabled="">Step 4</a></li>
                            <li><a id="step5Tab" href="#step5" data-toggle="tab" data-step="5" disabled="">Step 5</a></li>
                            <li><a id="step6Tab" href="#step6" data-toggle="tab" data-step="6" disabled="">Step 6</a></li>
                            <li><a id="step7Tab" href="#step7" data-toggle="tab" data-step="7" disabled="">Step 7</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    @include('panel.product-option-partials.step-1-information')
                    @include('panel.product-option-partials.step-2-min-max-count')
                    @include('panel.product-option-partials.step-3-cut-of-time')
                    @include('panel.product-option-partials.step-4-pricing')
                    @include('panel.product-option-partials.step-5-availability')
                    @include('panel.product-option-partials.step-6-meeting-point')
                    @include('panel.product-option-partials.step-7-contact-information-fields')
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary nextBtnForOpt btn-lg pull-right" data-step="1" type="button"  style="margin-bottom:30px;">Next</button>
                <button class="btn btn-primary prevBtnForOpt btn-lg pull-left" type="button"  style="margin-bottom:30px; margin-right: 10px; display:none;">Previous</button>
                <input style="display: none!important; margin-right: 10px;" type="submit" class="btn btn-primary pull-right" value="Save Availability" id="av_button">
                <input style="display: none!important; margin-right: 10px;" class="btn btn-primary pull-right" type="submit" id="opt_button" value="Save Option">
                <input style="display: none!important; margin-right: 10px;" data-form="save" type="submit" class="btn btn-primary pull-right" value="Save Pricing" id="price_button">
            </div>
        </div>
    </div>
</div>
