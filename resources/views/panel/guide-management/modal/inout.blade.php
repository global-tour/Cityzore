       

        <ul class="nav nav-tabs">
          <li class="active" style="width: 33%; text-align: center;"><a data-toggle="tab" href="#information">Information</a></li>
          <li style="width: 33%; text-align: center;"><a data-toggle="tab" href="#logs">Logs</a></li>
          <li style="width: 33%; text-align: center;"><a data-toggle="tab" href="#locations">Locations</a></li>
        </ul>


      

<div class="tab-content">
  <div id="information" class="tab-pane fade in active" style="padding: 20px;">
      

  
 <form action="" id="shift-form-update">
  <input type="hidden" name="_token" value="{{csrf_token()}}">
  <input type="hidden" name="action" value="update_shift_form">
  <input type="hidden" name="shift_id" value="{{$shift->id}}">
  <input type="hidden" name="shift_date" value="{{!empty($shift->time_in) ? $shift->time_in->format('Y-m-d') : '0000-00-00'}}">
    <div class="information-div">
        <div class="clock-item" style="width: 48%;">
            <label style="font-weight: 700; font-size: 17px;">In </label>
              <input @if($shift->time_in) value="{{$shift->time_in->format("Y-m-d")}}"  @endif autocomplete="off" type='text' class='datepicker-from' name="from_date" data-language='en' placeholder="{{__('In Date')}}"  style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/><br>
            <input type="time" min="00:00:00" max="23:59:59" step="1" class="form-control" autocomplete="off" name="shift_clock_in" placeholder="clock-in" value="{{!empty($shift->time_in) ? $shift->time_in->format('H:i:s') : '00:00:00'}}">


        </div>
        <div class="clock-item" style="width: 48%;">
            <label for="" style="font-weight: 700; font-size: 17px;">Out</label>

             <input @if($shift->time_out) value="{{$shift->time_out->format("Y-m-d")}}"  @endif type='text' class='datepicker-to' autocomplete="off" name="to_date" data-language='en' placeholder="{{__('Out Date')}}"  style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/><br>
            <input type="time" min="00:00:00" max="23:59:59" step="1" class="form-control" autocomplete="off" name="shift_clock_out" placeholder="clock-out" value="{{ !empty($shift->time_out) ? $shift->time_out->format('H:i:s') : '00:00:00'}}">
        </div>

        <div class="clock-note" style="width: 48%;">
            <label style="font-weight: 700;">Clock In Note</label><br>
            <label >{{$shift->message_in}}</label>
        </div>
         <div class="clock-note" style="width: 48%;">
            <label style="font-weight: 700;">Clock Out Note</label><br>
            <label >{{$shift->message_out}}</label>
        </div>
    </div>

    <div class="supervisor-notes">
        <textarea class="form-control" name="shift_supervisor_message" cols="30" rows="5" placeholder="Add Some İnfo as Supervisor">{{$shift->supervisor_message}}</textarea>
    </div>

</form>

    <div class="add-supervisor-message">
        <label class="label label-success" id="shift-save-change-button"> Save Changes</label>
    </div>
   
  </div>
  <div id="logs" class="tab-pane fade" style="padding: 20px;">
   <div class="log-table">
       
       <table class="table table-bordered table-hover">
           <thead>
               <tr>
                   <th>Date</th>
                   <th>Logger</th>
                   <th>Activity</th>
                   <th>Settings</th>
               </tr>
           </thead>

           <tbody>

            @foreach ($shift->logs as $log)
             
                 <tr>
                   <td>{{$log->created_at->format('d-m-Y')}}</td>
                   <td>{{$log->logger_email}}</td>
                   <td>{!! $log->activity_message !!}</td>
                   <td style="cursor: pointer;"  data-id="{{$log->id}}" class="shift-log-delete-button"><i class="icon-cz-trash"></i> Delete</td>
                 </tr>

            @endforeach


           
            
           </tbody>
       </table>
   </div>
   
  </div>
  <div id="locations" class="tab-pane fade" style="padding: 20px;">
   <div class="location-wrap">

    @if($shift->latitude_in || $shift->longitude_in)
       
       <div class="map-in" id="maploc" style="width: 48%; height: 300px;">
        
       </div>
    @else
    <div class="alert alert-danger" style="font-size:20px; width: 48%; height: 300px; align-items: center; align-content: center; display: flex; justify-content: center;">
         No Checkout
       </div>
    @endif   



       @if($shift->latitude_out || $shift->longitude_out)
       <div class="map-out" id="maploc2" style="width: 48%; height: 300px;">
  
       </div>
       @else

       <div class="alert alert-danger" style="font-size:20px; width: 48%; height: 300px; align-items: center; align-content: center; display: flex; justify-content: center;">
         No Checkout
       </div>

       @endif
   </div>
    
  </div>
</div>

{{--<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>--}}
 {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuSR4HZ3vO2qp_nfRM0QXRXm31ewkh5dI&language=en&libraries=places"
        async defer></script>--}}

<script>

   
    jQuery(document).ready(function($) {
        $( ".datepicker-from, .datepicker-to" ).datepicker( "refresh" );

        $(".datepicker-from, .datepicker-to").datepicker({
          dateFormat: "yy-mm-dd"
        });



    //$("input[name='shift_clock_in'], input[name='shift_clock_out']").timepicker({ 'timeFormat': 'H:i:s', 'step': 15 });

    });
   


  
  function initMap() {

    @if(!empty($shift->latitude_in))
    let map;
 
 position = new google.maps.LatLng({{$shift->latitude_in}}, {{$shift->longitude_in}});

  map = new google.maps.Map(document.getElementById("maploc"), {
    center: { lat: {{$shift->latitude_in}}, lng: {{$shift->longitude_in}} },
    zoom: 10,
  });

   marker = new google.maps.Marker({
            position: position,
            map: map
        });

   @endif


@if(!empty($shift->latitude_out))

  let map2;

  position = new google.maps.LatLng({{$shift->latitude_out ?? '0'}}, {{$shift->longitude_out ?? '0'}});

    map2 = new google.maps.Map(document.getElementById("maploc2"), {
    center: { lat: {{$shift->latitude_out ?? '0'}}, lng: {{$shift->longitude_out ?? '0'}} },
    zoom: 10,
  });

       marker = new google.maps.Marker({
            position: position,
            map: map2
        });

       @endif
}


  setTimeout(initMap, 1000);
  
</script>


<style>
  
  mark{
    background-color: yellow;
    color: black;
  }
</style>