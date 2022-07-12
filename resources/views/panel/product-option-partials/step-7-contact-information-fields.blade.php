<div class="tab-pane fade option-setup-content" id="step7">


              <input type="hidden" id="addresses" name="addresses" class="add" value="">


                    <div id="map-area">
                  

                      <div class="pac-card4" id="pac-card4">
                        <div>
                            <div id="title4">
                                Autocomplete search
                            </div>
                            <div id="type-selector4" class="pac-controls4">
                                <input type="radio" name="type" id="changetype-all4" checked="checked">
                                <label for="changetype-all4">All</label>

                                <input type="radio" name="type" id="changetype-establishment4">
                                <label for="changetype-establishment4">Establishments</label>

                                <input type="radio" name="type" id="changetype-address4">
                                <label for="changetype-address4">Addresses</label>

                                <input type="radio" name="type" id="changetype-geocode4">
                                <label for="changetype-geocode4">Geocodes</label>
                            </div>
                            <div id="strict-bounds-selector4" class="pac-controls4">
                                <input type="checkbox" id="use-strict-bounds4" value="">
                                <label for="use-strict-bounds4">Strict Bounds</label>
                            </div>
                        </div>
                        <div id="pac-container4" style="z-index: 9999!important;">
                            <input id="pac-input4" type="text"
                                   placeholder="Enter a location" value="">
                        </div>
                    </div>
                    <div id="map4"></div>
                    <div id="infowindow-content4">
                        <img src="" width="16" height="16" id="place-icon4">
                        <span id="place-name4"  class="title4"></span><br>
                        <span id="place-address4"></span>
                    </div>





                    <div id="selected-address2">

                      

                        

                        
                    </div>
               
           </div>
           <br><br><hr>




    <div class="col-md-12" style="margin-bottom: 25px;font-size: 16px!important;letter-spacing: 1px;">
        <label class="col-md-8 label label-info">If you would like to delete a contact information field, please leave it blank.</label>
        <button style="position: absolute;right: 0;top:-15px" class=" btn" id="addNewContactInformationLabel">Add New Contact Box</button>
    </div>
    <h4>Contact Informations</h4>
    <div id="contactInformationDiv" class="form-group">
        <input id="contactInformationIterator" hidden value="1">
        <div class="contact-info-group col-md-12">
            <div class="col-md-6">
                <input class="contact-info-title" style="border: none!important;" name="newContactInformation0" id="newContactInformation0" placeholder="Add a name...">
            </div>
            <div class="col-md-6">
                <input class="contact-info-checkbox" value="0" type="checkbox" id="isRequired0">
                <label for="isRequired0">is Required?</label>
            </div>
        </div>
    </div>
    <div class="">

    </div>
    <div class="col-md-6">
        <input class="col-md-12" value="0" type="checkbox" id="contactForAllTravelers">
        <label for="contactForAllTravelers">Would you like to get informations for all travelers?</label>
    </div>








            <input type="hidden" name="customer_mail_templates" value="">
            <div class="col-md-12" style="margin-top: 30px;">
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

              <div class="tab-content" id="customer-tab-content-wrap">
                <div id="en" class="tab-pane fade in active">
                  
                  <textarea name="en" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                <div id="fr" class="tab-pane fade">
                 
                  <textarea name="fr" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                <div id="tr" class="tab-pane fade">
                  
                  <textarea name="tr" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                <div id="ru" class="tab-pane fade">
                 
                 <textarea name="ru" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                 <div id="es" class="tab-pane fade">
                 
                  <textarea name="es" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                 <div id="de" class="tab-pane fade">
                 
                  <textarea name="de" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                 <div id="it" class="tab-pane fade">
                 
                  <textarea name="it" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                 <div id="pt" class="tab-pane fade">
                 
                  <textarea name="pt" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
                <div id="nd" class="tab-pane fade">
                 
                  <textarea name="nd" value="" id="" cols="30" rows="10" style="height: 300px;"></textarea>
                </div>
              </div>
            </div>
                  
                  
              </div><!--end of col-->
              
</div>

