
  <style>


  	@media print {
  body * {
    visibility: hidden;
  }
  .meeting-wrap, .meeting-wrap * {
    visibility: visible;
  }
  .meet{
  	visibility: hidden;
  }


/*  #section-to-print {
    position: absolute;
    left: 0;
    top: 0;
  }*/
}
.meet-table{
	overflow-x: auto !important;
}

 .meet-table, th, td{
  	 border: 1px solid #ccc;
     border-collapse: collapse;
  }

  th{
  	font-weight: 700;
  	font-size: 13px !important;
  	background: #f2f2f2;
  }

  .meeting-wrap{
  	overflow-x: auto !important;
  }


  </style>

     @php
     	       if($supplierID == "all"){
                 	$supplierID = "All Suppliers";
                 }else{
                 	if($supplierID == -1)
                 		$supplierID = 33;

                 	$supplierID = App\Supplier::find($supplierID)->companyName ?? "";
                 }
     @endphp


               @foreach ($responseArray as $key => $value)
                 @php
                 $total = 0;
                 $option =  App\Option::where('referenceCode', $key)->exists() ? App\Option::where('referenceCode', $key)->first(): new App\Option();
                 $price = App\Pricing::findOrFail($option->pricings);
                 $ignoredCategories = $price->ignoredCategories ? json_decode($price->ignoredCategories, true) : [];
                 $ignoredCategories = array_map('strtoupper', $ignoredCategories);




                 @endphp
                   <div class="meeting-wrap" data-date="{{$currentDate}}" data-hour="{{$currentTime}}" data-option="{{$key}}">

				       <h3>

				          <select data-info="" class="shaselect meet" name="guide" style="background: #F5B7B1;">
                            <option value="0">Add A Guide</option>
				          	@foreach ($guides as $keyv => $guide)

				          		<option value="{{$keyv}}">{{$guide}}</option>


				          	@endforeach

				         </select>

				       </h3>



            @php
                   if($value[0]["operating_hour"] == '[{"hour":"00:00"}]'){

                    //$option = \App\Option::findOrFail($value['id']);
                    $av = $option->avs()->first();

                    foreach(json_decode($av->daily, true) as $day){
                    if($day["day"] === $value[0]["day"]){

                     $value[0]["operating_hour"] = json_encode([["hour" => $day["hourFrom"]."-".$day["hourTo"]]]);

                      break;
                    }
                    }



                  }

                  if($value[0]["operating_hour"] == '[{"hour":"00:00"}]'){
                    $value[0]["operating_hour"] = json_encode([["hour" => "!Warning: no time range has been set for this date"]]);
                  }



                @endphp



				       <div class="all-meeting-guides" data-option="{{$key}}" data-hour="{{$currentTime}}" data-date="{{$currentDate}}" data-operating-hour="{{$value[0]["operating_hour"]}}">
				       	<ul style="margin: auto;">

				       		@php
				       		if(App\Meeting::where('option', $key)->where('time', $currentTime)->where('date', $currentDate)->exists()){

				       			$meeting = App\Meeting::where('option', $key)->where('time', $currentTime)->where('date', $currentDate)->first();
				       			$admins = App\Admin::whereIn('id', json_decode($meeting->guides, true))->get();

				       		}else{
                               $admins = [];
				       		}
				       		@endphp

                              @foreach ($admins as $admin)
                              	<li title="{{ $admin->email }}">{{$admin->adminFullName}} <button data-guide-id="{{$admin->id}}" class="icon-cz-cancel remove-guide-item"><i class="icon-delete"></i></button></li>
                              @endforeach




				       	</ul>

				       </div>

				       <table class="meet-table table-responsive">

				       	<caption style="text-align: center; font-weight: 700; background: #FCF3CF; position: relative;">
                            <label for="" class="pull-right coming-ornot-total">


                            </label>
				       		{{$supplierID}} <br>
				       		{{$option->title}} - {{$currentDate}}

                             @if($currentTime === "00:00:00")




                          @php
                  if($value[0]["operating_hour"] == '[{"hour":"00:00"}]'){

                    //$option = \App\Option::findOrFail($value['id']);
                    $av = $option->avs()->first();

                    foreach(json_decode($av->daily, true) as $day){
                    if($day["day"] === $value[0]["day"]){

                     $value[0]["operating_hour"] = json_encode([["hour" => $day["hourFrom"]."-".$day["hourTo"]]]);

                      break;
                    }
                    }



                  }

                  if(!$value[0]["operating_hour"] == '[{"hour":"00:00"}]'){
                    $value[0]["operating_hour"] = json_encode([["hour" => "!Warning: no time range has been set for this date"]]);
                  }



                @endphp










                             @foreach (json_decode($value[0]["operating_hour"], true) as $ohour)
                             	- ( {{$ohour["hour"]}} )
                             @endforeach

                             @else
                             - {{$currentTime}}
                             @endif

				       	</caption>

                      <thead>
                      	 <tr>

                      	 	<th>RefCode</th>
                      	 	<th>FullName</th>
                      	 	<th>Phone</th>
                      	 	<th>Category</th>
                      	 	<th>Tickets</th>
                      	 	<th>Status</th>
                      	 </tr>

                      </thead>


                     <tbody>

                     	@foreach ($value as $v)

                     	         @php
                                 $tickets = 0;
                                 @endphp

                     	<tr>

                     		<td>{{$v["gygBookingReference"] ? $v["gygBookingReference"] : $v["bookingRefCode"]}}</td>
                     		<td>{{$v['fullName']}}</td>
                     		<td>
                     			@foreach ($v["travelers"] as $v2)
                             	<span>{{$v2->phoneNumber}}</span>
                                @endforeach


                     		</td>
                     		<td>

                     			 @php

                                 $tickets = 0;
                                 @endphp

                     			@foreach ($v['bookingItems'] as $el)

                             @if(in_array($el->category, $ignoredCategories))

                                <span style="color: #ff0000;">{{$el->category}}: {{$el->count}}</span>

                               @else
                               <span>{{$el->category}}: {{$el->count}} </span>

                               @php
                               	$tickets += (int)$el->count;
                               @endphp

                             @endif
                             @endforeach


                     		</td>
                     		<td>

                     			<label class="label label-success" style="font-size: 9px !important; margin-left: 4px;">{{$tickets}}</label>


                     		</td>
                     		<td>

                     			@php
                               $total = 0;
                     			foreach($v['check'] as $check){
                                   if($check['status'] == 1 && $check['role'] == "Guide"){
                                   		$total = 1;

                                   }
                                   if($check['status'] == 0 && $check['role'] == "Guide"){
                                   		$total = 0;

                                   }

                     			}
                     			$otherTotal = 0;
                          $otherArray = [];
                     			foreach($v['check'] as $check){

                                   if($check['status'] == 1 && $check['role'] == "Others")
                                   	$otherArray[$check['checkinable_id']] = 1;

                                    if($check['status'] == 0 && $check['role'] == "Others")
                                    $otherArray[$check['checkinable_id']] = 0;
                     			}
                                $total = $total + array_sum($otherArray);

                     			@endphp


                     			@if(!empty($v['check']) && $total)


                                  @php
                                   $comings = 0;
                                  foreach($v['check'] as $check){
                                    $personArray = $check->person;

                                    if($check->role == "Guide"){
                                    foreach($personArray as $key => $val){

                                       $comings += (int)$val;
                                     }

                                    }


                                  }


                                  @endphp


                             <label style="font-size: 11px;" class="label label-success turn-to-status" data-toggle="modal" data-target="#turn-to-status" data-id="{{$v['bookingId']}}" data-status="1">Checkin  {{$comings}}</label>
                             @else
                              <label style="font-size: 11px;" class="label label-danger turn-to-status" data-toggle="modal" data-target="#turn-to-status" data-id="{{$v['bookingId']}}" data-status="0">Not Checkin</label>
                             @endif

                     		</td>
                     	</tr>

                     	@endforeach


                     </tbody>



				       </table>

				       {{--<ul>

				       	@foreach ($value as $v)
				       	<li class="customer">


				       		<span>{{$v["gygBookingReference"] ? $v["gygBookingReference"] : $v["bookingRefCode"]}}</span> <span class="b">FullName: {{$v['fullName']}}</span>

                                 <span class="b">Category:</span>
                                 @php

                                 $tickets = 0;
                                 @endphp
                             @foreach ($v['bookingItems'] as $el)



		                      @if(in_array($el->category, $ignoredCategories))

                               <span style="color: #ff0000;">{{$el->category}}: {{$el->count}}</span>

                               @else
                               <span>{{$el->category}}: {{$el->count}} </span>

                               @php
                               	$tickets += (int)$el->count;
                               @endphp

                               @endif

                             @endforeach
                             @php
                             	$total += $tickets;
                             @endphp


                             @foreach ($v["travelers"] as $v2)
                             	<span>Phone: {{$v2->phoneNumber}}</span>
                             @endforeach


                                <div style="display: inline; float: right;">
                             <label class="label label-success" style="font-size: 9px !important; margin-left: 4px;">Ticket: {{$tickets}}</label>

                             @if($v['check'])
                             <label class="label label-success">Checkin</label>
                             @else
                              <label class="label label-danger">Not Checkin</label>
                             @endif

                             </div>




				       	</li>
				       	@endforeach




				       </ul>--}}

				       </div>

									       <div id="turn-to-status" class="modal fade" role="dialog">
					  <div class="modal-dialog">

					    <!-- Modal content-->
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title"></h4>
					      </div>
					      <div class="modal-body" id="insert-checkin-details">



					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      </div>
					    </div>

					  </div>
					</div>


               @endforeach



            <script>
                $(document).ready(function() {

var completeAllTotal = 0;
var completeComingTotal = 0;
var allTotal = 0;
var comingTotal = 0;
$(".meet-table").each(function(index, el) {


$(this).find("tbody tr").each(function(index, el) {

    var $this = $(this);
    var target = parseInt($this.find("td").eq(4).find("label").text());
    allTotal += target;


     var target_coming = ($this.find("td").eq(5).find("label").text());
     if(target_coming.trim() == "Not Checkin"){
        comingTotal += 0;
     }else{
       comingTotal += parseInt(target_coming.trim().split("  ")[1].trim());
     }

    console.log(comingTotal);
});

$(this).find(".coming-ornot-total").html("total: " + allTotal + " -  exist: " + comingTotal);
completeAllTotal += allTotal;
completeComingTotal += comingTotal;
allTotal = 0;
comingTotal = 0;


});

$(".all-option-total-coming-or-not span").html("Total: " + completeAllTotal + " - Exist: " + completeComingTotal);







});
            </script>
