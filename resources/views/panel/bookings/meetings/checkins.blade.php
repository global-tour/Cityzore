<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#summary">Summary</a></li>
  <li><a data-toggle="tab" href="#details">Details</a></li>
  
</ul>





<div class="tab-content">
  <div id="summary" class="tab-pane fade in active">





<table id="summary-table">
					      		<thead>
					      			<tr>
					      				<th>Total Tickets</th>
					      				<th>Checked By</th>
					      				<th>Role</th>
					      				<th>Checked Date</th>
					      				<th>Types</th>
					      				<th>Status</th>
					      				
					      			</tr>
					      		</thead>
					      		<tbody>





                           @php
    $guide_total = 0;
	$others_total = 0;
	$biggest = 0;
	$timestamp_control = 0;
	$total_others = [];

@endphp
@foreach ($allRecords as $record)
	<tr class="checkin-trr" data-reader-id="{{$record->checkinable_id}}" data-booking-id="{{$record->book->id}}" data-status="{{$record->status}}" data-role="{{$record->role}}" data-timestamp="{{$record->updated_at->timestamp}}" data-ticket="{{$record->ticket}}">
		<td>{{$record->ticket}}</td>
		<td>{{$record->email}}</td> 
		<td>{{$record->role}}</td>
		<td>{{$record->created_at->format('d-m-Y - H:i')}}</td>

		<td>
			@php
			    $personTotal = 0;
				$types = $record->person;
		    @endphp

           @foreach($types as $type => $typeValue)

           <span>{{$type}} : {{$typeValue}} </span><br>

           @php
           	$personTotal += (int)$typeValue;
           @endphp

           @endforeach
  
        

		</td>
		<td>
			<span class="person">{{$personTotal}}</span> / {{$record->ticket}}
			<label class="label {{$record->status == 1 ? 'label-success' : 'label-danger'}} check-status" data-task="check" data-check-id="{{$record->id}}">{{$record->status == 1 ? 'Approved' : 'Cancelled'}}</label>
			</td>
		
	</tr>

	@if($record->status == 1 && $record->role == "Others")
	@php
	    $total_others[$record->checkinable_id] = $personTotal;
		
	@endphp
	 
	@endif

	@if($record->status == 0 && $record->role == "Others")
	@php

	    $total_others[$record->checkinable_id] = 0;
		
	@endphp
	 
	@endif




	@php
	if($record->status == 1 && $record->role == "Guide"){
		if($record->updated_at->timestamp > $timestamp_control){
			$timestamp_control = $record->updated_at->timestamp;
			$guide_total = $personTotal;
		}
	}
	@endphp
@endforeach


@if($allRecords->count())
<tr>
	<td colspan="6" class="last-info"><span>Others: {{array_sum($total_others)}}</span> |  <span>Guide: {{$guide_total}}</span> =  <span>Total: {{$guide_total + array_sum($total_others)}}</span></td>
</tr>
@endif

<script>
	
	$(document).ready(function() {
		
  
   
 /*   $(".checkin-tr").reverse().forEach( function(element, index) {
    	if($(element).attr("data-status") == 1 && $(element).attr("data-role") == "Guide"){
    		console.log("testtt");
    		console.log($(element).addClass('success'));
    		return false;
    	}
    });*/
    var biggest = 0;
   $("#summary-table tbody .checkin-trr").each(function(index, el) {
    	if($(el).attr("data-status") == 1 && $(el).attr("data-role") == "Guide"){
    		

    		

    		if(parseInt($(el).attr('data-timestamp')) > biggest){
    			biggest = parseInt($(el).attr('data-timestamp')); 
    		}
            
    		

    	}


    		if($(el).attr("data-status") == 0 && $(el).attr("data-role") == "Guide"){
    		

    		biggest = 0;

    		
            
    		

    	}

    });

    if(biggest){
    	$("#summary-table tr[data-timestamp='"+biggest+"']").addClass('success');
    }else{
       $(".last-info").hide();
    }
   




      $("#summary-table tbody .checkin-trr").each(function(index, el) {
    	if($(el).attr("data-status") == 1 && $(el).attr("data-role") == "Others"){
    		

    		
           
    		$(el).addClass('success-others');
    		
            console.log(" status 1");
    		

    	}else if($(el).attr("data-status") == 0 && $(el).attr("data-role") == "Others"){
         var booking_id = $(el).attr("data-booking-id");
          var timestamp = $(el).attr("data-timestamp");
        

        
       $("#summary-table tbody .checkin-trr.success-others").each(function(index2, el2) {

       	  var tmp = $(el2).attr("data-timestamp");
       	  var bk_id = $(el2).attr("data-booking-id");

          

       	  if(booking_id == bk_id && (parseInt(tmp) < parseInt(timestamp))){
       	  	$(el2).removeClass('success-others');
       	  	console.log("status 0");
       	  	
       	  }
       	
       });



    	}

    });

      if($(".success-others").length > 0){
       $(".last-info").show();
      }
    

	});
</script>


<style>
	#summary-table .checkin-trr{
      display: none;
	}
	#summary-table .checkin-trr.success{
		background: #D5F5E3;
		display: table-row;
		
	}
	#summary-table .checkin-trr.success-others{
		background: #E8DAEF;
		display: table-row;
		
	}
</style>







					      			
					      		</tbody>
					      	</table>














    
  </div>
  <div id="details" class="tab-pane fade">



 <table id="details-table">
					      		<thead>
					      			<tr>
					      				<th>Total Tickets</th>
					      				<th>Checked By</th>
					      				<th>Role</th>
					      				<th>Checked Date</th>
					      				<th>Types</th>
					      				<th>Status</th>
					      				
					      			</tr>
					      		</thead>
					      		<tbody>





                           @php
    $guide_total = 0;
	$others_total = 0;
	$biggest = 0;
	$timestamp_control = 0;
	$total_others = [];

@endphp
@foreach ($allRecords as $record)
	<tr class="checkin-trr" data-status="{{$record->status}}" data-role="{{$record->role}}" data-timestamp="{{$record->updated_at->timestamp}}" data-ticket="{{$record->ticket}}">
		<td>{{$record->ticket}}</td>
		<td>{{$record->email}}</td> 
		<td>{{$record->role}}</td>
		<td>{{$record->created_at->format('d-m-Y - H:i')}}</td>
		<td>
			@php
			    $personTotal = 0;
				$types = $record->person;
		    @endphp

           @foreach($types as $type => $typeValue)

           <span>{{$type}} : {{$typeValue}}<br></span>

           @php
           	$personTotal += (int)$typeValue;
           @endphp

           @endforeach
  
        

		</td>
		<td>
			<span class="person">{{$personTotal}}</span> / {{$record->ticket}}
			<label class="label {{$record->status == 1 ? 'label-success' : 'label-danger'}} check-status" data-task="check" data-check-id="{{$record->id}}">{{$record->status == 1 ? 'Approved' : 'Cancelled'}}</label>
			</td>
		
	</tr>

	@if($record->status == 1 && $record->role == "Others")
	@php
		$total_others[$record->checkinable_id] = $personTotal;
	@endphp
	 
	@endif

	@if($record->status == 0 && $record->role == "Others")
	@php

	    $total_others[$record->checkinable_id] = 0;
		
	@endphp
	 
	@endif




	@php
	if($record->status == 1 && $record->role == "Guide"){
		if($record->updated_at->timestamp > $timestamp_control){
			$timestamp_control = $record->updated_at->timestamp;
			$guide_total = $personTotal;
		}
	}
	@endphp
@endforeach


@if($allRecords->count())
<tr>
	<td colspan="6"><span>Others: {{array_sum($total_others)}}</span> |  <span>Guide: {{$guide_total}}</span> =  <span>Total: {{$guide_total + array_sum($total_others)}}</span></td>
</tr>
@endif

<script>
	
	$(document).ready(function() {
		
  
   
 /*   $(".checkin-tr").reverse().forEach( function(element, index) {
    	if($(element).attr("data-status") == 1 && $(element).attr("data-role") == "Guide"){
    		console.log("testtt");
    		console.log($(element).addClass('success'));
    		return false;
    	}
    });*/
    var biggest = 0;
   $("#details-table tbody .checkin-trr").each(function(index, el) {
    	if($(el).attr("data-status") == 1 && $(el).attr("data-role") == "Guide"){
    		

    		

    		if(parseInt($(el).attr('data-timestamp')) > biggest){
    			biggest = parseInt($(el).attr('data-timestamp')); 
    		}
            
    		

    	}

    });

    if(biggest){
    	$("#details-table tr[data-timestamp='"+biggest+"']").addClass('success');
    }
   




      $("#details-table tbody .checkin-trr").each(function(index, el) {
    	if($(el).attr("data-status") == 1 && $(el).attr("data-role") == "Others"){
    		

    		

    		$(el).addClass('success-others');
            
    		

    	}else if($(el).attr("data-status") == 0 && $(el).attr("data-role") == "Others"){
         var booking_id = $(el).attr("data-booking-id");
          var timestamp = $(el).attr("data-timestamp");
        

        
       $("#details-table tbody .checkin-trr.success-others").each(function(index2, el2) {

       	  var tmp = $(el2).attr("data-timestamp");
       	  var bk_id = $(el2).attr("data-booking-id");

          

       	  if(booking_id == bk_id && (parseInt(tmp) < parseInt(timestamp))){
       	  	$(el2).removeClass('success-others');
       	  	console.log("status 0");
       	  	
       	  }
       	
       });



    	}

    });


      
    

	});
</script>


<style>
	
	#details-table .success{
		background: #D5F5E3;
	}
	#details-table .success-others{
		background: #E8DAEF;
	}
</style>







					      			
					      		</tbody>
					      	</table>






    
  </div>
 
</div>


  	            