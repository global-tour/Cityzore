          @php
            $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
          @endphp
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i class="icon-cz-copy"></i> Booking Customer Contact Modal</h5>
                    <input type="hidden" id="bookingID" value="{{$booking->id}}">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                      <ul class="nav nav-tabs" style="display: flex; justify-content: center;">
                    <li class="active" style="width: 45%;"><a class="text-center" data-toggle="tab" href="#mail">Mail <i class="icon-cz-mail"></i></a></li>
                    <li style="width: 45%;"><a class="text-center" data-toggle="tab" href="#whatsapp">Whatsapp <i class="icon-cz-whatsapp"></i></a> </li>

                  </ul>

                  <div class="tab-content" style="margin-top: 20px;">
                    <div id="mail" class="tab-pane fade in active">
                      <h3>Mail Form</h3>

                      @php
                          $decodedTraveler = json_decode($booking->travelers, true);
                          $customerEmail = $decodedTraveler[0]["email"];
                          $phoneNumber = $decodedTraveler[0]["phoneNumber"];
                          $fullName = $booking->fullName;
                      @endphp


                      <form action="" id="mail-form" style="margin-top: 20px;">
                           <input type="hidden" name="booking_id" value="{{$booking->id}}">

                           <div class="form-group">
                              <label for="">To:</label>
                              <input type="text" disabled="disabled" name="mail_to" value="{{$customerEmail}}" style="background-color: aliceblue; color: black;">
                          </div>
                          <div class="form-group">
                              <label for="">Title:</label>
                              <input type="text" name="mail_title" value="Cityzore Booking Information" style="background-color: aliceblue;">
                          </div>
                              @php
                              $defaultMessage = '';
                                    $customerTemplates = $booking->bookingOption->customer_mail_templates ? json_decode($booking->bookingOption->customer_mail_templates, true) : [];
                                    if(!empty($customerTemplates["en"])){
                                        $defaultMessage = $customerTemplates["en"];

                                        if(strpos($defaultMessage, "#NAME SURNAME#") !== false){
                                           $defaultMessage = str_replace("#NAME SURNAME#", $fullName, $defaultMessage);
                                        }

                                          if(strpos($defaultMessage, "#SENDER#") !== false){
                                           $defaultMessage = str_replace("#SENDER#", auth()->guard('admin')->user()->name, $defaultMessage);
                                        }

                                        if(strpos($booking->dateTime, "dateTime") === false){
                                           $meetingDateTime = \Carbon\Carbon::parse($booking->dateTime)->format("d/m/Y H:i:s");
                                        }else{
                                            $meetingDateTime = $booking->date ." ".json_decode($booking->hour, true)[0]["hour"];
                                        }

                                        if(strpos($defaultMessage, "#DATE#") !== false) {
                                            $defaultMessage = str_replace("#DATE#", $meetingDateTime, $defaultMessage);
                                        }



                                    }









                                @endphp



                                <div class="form-group">
                                    <ul class="text-center change-mail-message-language">
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="en">en</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="fr">fr</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="tr">tr</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="ru">ru</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="es">es</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="de">de</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="it">it</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="pt">pt</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="nd">nd</li>
                                    </ul>
                                </div>
                           <div class="form-group">
                              <label for="">Message:</label>
                              <textarea name="mail_message" id="" cols="30" rows="10" style="background-color: aliceblue; height: 400px;">{!!$defaultMessage ?? ''!!}
                              </textarea>
                          </div>

                          <div class="form-group">
                              <button type="button" class="send-mail-message-button btn btn-block btn-success active">Send Mail <i class="icon-cz-rocket"></i></button>
                          </div>
                      </form>




                    @if($booking->contacts()->count())
                    <ul class="text-center before_senders_lists">

                    @foreach($booking->contacts as $key => $contact)
                           <li data-contact-message="{{$contact->mail_message}}" style="padding: 4px 8px; width: 100%; border: solid 1px #ccc; background-color: #fafafa; cursor: pointer; margin-top: 2px;" data-toggle="collapse" href="#mailCollapse{{$key}}" role="button" aria-expanded="false" aria-controls="mailCollapse{{$key}}">
                               {{$contact->sender->name}} - {{$contact->sender->surname}} - ({{$contact->created_at->format("d/m/Y H:i:s")}}) <br>
                               <b>Files: </b> {{$contact->files}}
                           </li>
                            <div class="collapse" id="mailCollapse{{$key}}">
                                <div class="card card-body">
                                    <?php echo nl2br($contact->mail_message) ?>
                                </div>
                            </div>



                    @endforeach

                    @php
                        $lastContact = $booking->contacts[count($booking->contacts)-1];
                        $checkInformation = json_decode($lastContact->check_information, true);
                    @endphp

                    <div style="margin-top: 5px;">
                        <input class="form-check-input" type="checkbox" value="" id="mailCheck" @if($checkInformation["status"]) disabled checked @endif>
                        <label class="form-check-label" for="mailCheck">
                            <b>Check</b>
                        </label>
                        @if($checkInformation["status"])
                            <br>
                            <span style="color: #2fa360">Checked on {{$checkInformation["check_date"]}} by {{$checkInformation["checker"]}}</span>
                        @endif
                    </div>


                     </ul>
                    @endif







                    </div>
                    <div id="whatsapp" class="tab-pane fade">
                      <h3>Whatsapp Form</h3>
                      <form action="" id="whatsapp-form" style="margin-top: 20px;">
                        <input type="hidden" name="booking_id" value="{{$booking->id}}">

                          <div class="form-group">
                              <label for="">To:</label>
                              <input type="text" name="whatsapp_to" value="{{$phoneNumber}}" style="background-color: aliceblue; color: black;">
                          </div>

                             <div class="form-group">
                                    <ul class="text-center change-mail-message-language">
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="en">en</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="fr">fr</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="tr">tr</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="ru">ru</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="es">es</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="de">de</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="it">it</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="pt">pt</li>
                                        <li style="display: inline; border: solid 1px #ccc; padding: 4px 6px; cursor: pointer; text-transform: uppercase;" data-id="{{$booking->id}}" data-lang="nd">nd</li>
                                    </ul>
                                </div>















                                @php
                                    $defaultMessage = '';
                                    $customerWhatsAppTemplates = $booking->bookingOption->customer_whatsapp_templates ? json_decode($booking->bookingOption->customer_whatsapp_templates, true) : [];
                                    if(!empty($customerWhatsAppTemplates["en"])){
                                        $defaultMessage = $customerWhatsAppTemplates["en"];

                                        if(strpos($defaultMessage, "#NAME SURNAME#") !== false){
                                           $defaultMessage = str_replace("#NAME SURNAME#", $fullName, $defaultMessage);
                                        }

                                          if(strpos($defaultMessage, "#SENDER#") !== false){
                                           $defaultMessage = str_replace("#SENDER#", auth()->guard('admin')->user()->name, $defaultMessage);
                                        }

                                           if(strpos($booking->dateTime, "dateTime") === false){
                                           $meetingDateTime = \Carbon\Carbon::parse($booking->dateTime)->format("d/m/Y H:i:s");
                                        }else{
                                            $meetingDateTime = $booking->date ." ".json_decode($booking->hour, true)[0]["hour"];
                                        }

                                        if(strpos($defaultMessage, "#DATE#") !== false) {
                                            $defaultMessage = str_replace("#DATE#", $meetingDateTime, $defaultMessage);
                                        }



                                    }


                                  $bookingExtraFiles = \App\Booking::findOrFail($booking->id)->extra_files;
                                   $defaultMessage .= "\n\n";
                                      if($bookingExtraFiles->count()) {
                                        $sayac = 1;
                                    foreach($bookingExtraFiles as $file) {
                                       $defaultMessage.= "\n\n".$file->image_base_name.": ".$file->image_name."";

                                       $sayac++;
                                    }
                                      }
                                @endphp


                                 @php
                                 $fullText = "whatsapp://send?text=".(urlencode($defaultMessage)) ?? '' ."&phone=".$phoneNumber;

                                 @endphp





                           <div class="form-group">
                              <label for="">Message:</label>
                               <textarea name="whatsapp_message" id="" cols="30" rows="10" @if(strlen($fullText) > 2057) style="background-color: #F5B7B1; height: 400px;" @else style="background-color: aliceblue; height: 400px;" @endif>{{ ($defaultMessage) ?? '' }}</textarea>
                          </div>

                           <div class="form-group">
                                <a class="send-whatsapp-message-button btn btn-block btn-success active" href=
                                    "whatsapp://send?text={{ (urlencode($defaultMessage)) ?? '' }}&phone={{$phoneNumber}}"
                                    data-action="share/whatsapp/share"
                                    target="_blank">
                                    Share to whatsapp
                                </a>

                          </div>
                          <span class="character-length" style="text-align: center; font-size: 15px; color: #A93226;">


                            @if(strlen($fullText) > 2057)

                            Character {{strlen($fullText)}} - character limit exceeded!


                            @else

                            Character {{strlen($fullText)}}

                            @endif



                          </span>
                      </form>
                    </div>

                  </div>
                </div>







                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary contactModal" data-dismiss="modal">Close</button>

                </div>
