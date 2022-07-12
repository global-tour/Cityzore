<div class="tab-pane fade option-setup-content" id="step2">
     <input type="hidden" name="is_free_cancellation" value="0">
     <input type="hidden" name="skip_the_line" value="0">
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



   
                         <br><hr>

                               <div class="row" style="margin-bottom: 50px;">
                            

                        

                           <div class="form-group">

                             
                            <div class="input-field col s12">
                             
                                 <div class="switch mar-bot-20">
                                    
                                        <label>
                                            <span style="font-size: 12px; color: #000; padding: 0 0 0 30px;">Free Cancellation ?</span>
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
                               
                               <input type="checkbox" class="filled-in guide_information" @if(!empty($option->guideInformation) && in_array("Live Guide", json_decode($option->guideInformation, true))) selected  @endif  id="live-guide" value="Live Guide" />
                               <label for="live-guide">Live Guide</label>
                             </div>

                            <div class="form-group">
                               
                               <input type="checkbox" class="filled-in guide_information" @if(!empty($option->guideInformation) && in_array("Audio Guide", json_decode($option->guideInformation, true))) selected  @endif id="audio-guide" value="Audio Guide" />
                               <label for="audio-guide">Audio Guide</label>
                           </div>

                           <div class="form-group">
                               
                               <input type="checkbox" class="filled-in" id="mobile-barcode" value="Mobile Barcode" />
                               <label for="mobile-barcode">Mobile Barcode</label>
                           </div>
                           </div>



    <span class="minMaxPersonErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">These fields are required and Min. Person Count must be equal or less than Max. Person Count.</span>
</div>
