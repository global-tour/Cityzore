          @php
            $cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
            $bookingItems = json_decode($booking->bookingItems, true);
            $pricing = $booking->bookingOption->pricings()->first();
            $ignoredCategories = $pricing->ignoredCategories ? json_decode($pricing->ignoredCategories, true) : [];
            $ignoredCategories = array_map('strtoupper', $ignoredCategories);
            
            if($booking->check()->count()){
               $checkObj = $booking->check()->where('status', 1)->orderBy("id", "desc")->first(); 
               $persons = $checkObj->person;
           }else{
               $checkObj = collect();
               $persons = [];
           }
            
            
          @endphp
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i class="icon-cz-copy"></i> Booking Checkins Modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                   <form action="#" id="access-checkins-form">
                    <input type="hidden" name="booking_id" value="{{$booking->id}}">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="action" value="insert_access_checkins_form_data">

                    <div class="form-group">
                               <label for="">Booking Reference Code</label>
                               <input type="text" name="refCode" value="">
                    </div>

                    <div class="form-group">
                        <a href="#" id="click_to_change_values" class="btn btn-warning btn-block active">Click to change values <i class="icon-cz-edit"></i></a>
                    </div>
                       
                       

                       <div class="group-wrap" style="display: none;">
                       @foreach ($bookingItems as $element)
                       @if(!in_array($element["category"], $ignoredCategories))
                           <div class="form-group">
                               <label for="">{{$element["category"]}} - ( {{$element["count"]}} )</label>
                               <input data-max="{{$element["count"]}}" type="number" name="category[{{$element["category"]}}]" @if($booking->check()->count()) style="background-color: aliceblue;" value="{{$persons[$element["category"]] ?? $element["count"]}}" @else value="{{$element["count"]}}"  @endif>
                           </div>
                         @endif  
                       @endforeach
                       </div>

                       <div class="form-group">
                           <button type="button" class="btn btn-success btn-sm active btn-block" id="access-checkins-form-submit-button">Check In <i class="icon-cz-rocket"></i></button>
                       </div>

                   </form>
               
                    
                 
                </div>

                 

                  


                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                </div>
            