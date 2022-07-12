        <ul class="nav nav-tabs">
          <li class="active" style="width: 33%; text-align: center;"><a data-toggle="tab" href="#information-create">Information</a></li>
          <li style="width: 33%; text-align: center;"><a data-toggle="tab" href="#logs-create">Logs</a></li>
          <li style="width: 33%; text-align: center;"><a data-toggle="tab" href="#locations-create">Locations</a></li>
        </ul>


      

<div class="tab-content">
  <div id="information-create" class="tab-pane fade in active" style="padding: 20px;">
      

  
 <form action="" id="shift-form-create">
  <input type="hidden" name="_token" value="{{csrf_token()}}">
  <input type="hidden" name="action" value="create_shift_form">
  <input type="hidden" name="meeting_id" value="{{$meeting->id}}">
  <input type="hidden" name="meeting_date" value="{{$meeting->date}}">
  <input type="hidden" name="meeting_point" value="{{$meeting_point}}">
  <input type="hidden" name="guide_id" value="{{$guide_id}}">

  
    <div class="information-div">
        <div class="clock-item" style="width: 48%;">
            <label style="font-weight: 700; font-size: 17px;">In</label>

              <input value="0000-00-00"  type='text' class='datepicker-from' autocomplete="off" name="from_date" data-language='en' placeholder="{{__('In Date')}}"  style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/><br>
           
            <input type="time" min="00:00:00" max="23:59:59" step="1" class="form-control" autocomplete="off" name="shift_clock_in" placeholder="clock-in" value="00:00:00">


        </div>
        <div class="clock-item" style="width: 48%;">
            <label for="" style="font-weight: 700; font-size: 17px;">Out</label>

            <input value="0000-00-00"  type='text' class='datepicker-to' autocomplete="off" name="to_date" data-language='en' placeholder="{{__('Out Date')}}"  style="box-shadow: 0px 2px 9px 0px #dfdfdf;-webkit-box-shadow: 0px 2px 9px 0px #dfdfdf;"/><br>


            <input type="time" min="00:00:00" max="23:59:59" step="1" class="form-control" name="shift_clock_out" autocomplete="off" placeholder="clock-out" value="00:00:00">
        </div>

        <div class="clock-note" style="width: 48%;">
            <label style="font-weight: 700;">Clock In Note</label><br>
            <label >-</label>
        </div>
         <div class="clock-note" style="width: 48%;">
            <label style="font-weight: 700;">Clock Out Note</label><br>
            <label >-</label>
        </div>
    </div>

    <div class="supervisor-notes">
        <textarea class="form-control" name="shift_supervisor_message" cols="30" rows="5" placeholder="Add Some İnfo as Supervisor"></textarea>
    </div>

</form>

    {{--<div class="add-supervisor-message">
        <label class="label label-success" id="shift-save-change-button"> Save Changes</label>
    </div>--}}
   
  </div>
  <div id="logs-create" class="tab-pane fade" style="padding: 20px;">
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

            {{--@foreach ($shift->logs as $log)
             
                 <tr>
                   <td>{{$log->created_at->format('d-m-Y')}}</td>
                   <td>{{$log->logger_email}}</td>
                   <td>{!! $log->activity_message !!}</td>
                   <td style="cursor: pointer;"  data-id="{{$log->id}}" class="shift-log-delete-button"><i class="icon-cz-trash"></i> Delete</td>
                 </tr>

            @endforeach--}}


           
            
           </tbody>
       </table>
   </div>
   
  </div>
  <div id="locations-create" class="tab-pane fade" style="padding: 20px;">
   <div class="location-wrap">
       
      <div class="alert alert-danger" style="font-size:20px; width: 48%; height: 300px; align-items: center; align-content: center; display: flex; justify-content: center;">
         No Checkout
       </div>

      

       <div class="alert alert-danger" style="font-size:20px; width: 48%; height: 300px; align-items: center; align-content: center; display: flex; justify-content: center;">
         No Checkout
       </div>

      
   </div>
    
  </div>
</div>

<script>
    
        jQuery(document).ready(function($) {
        $( ".datepicker-from, .datepicker-to" ).datepicker( "refresh" );

    $(".datepicker-from, .datepicker-to").datepicker({
  dateFormat: "yy-mm-dd"
});

    //$("input[name='shift_clock_in'], input[name='shift_clock_out']").timepicker({ 'timeFormat': 'H:i:s', 'step': 15 });

    });
</script>






