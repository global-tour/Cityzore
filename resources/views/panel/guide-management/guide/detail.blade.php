@include('panel-partials.head', ['page' => 'guides-index'])
@include('panel-partials.header', ['page' => 'guides-index'])
@include('panel-partials.sidebar')

<style>
*{
    color-adjust: exact;  -webkit-print-color-adjust: exact; print-color-adjust: exact; 
}

    #guide-table td{
    font-weight: 700;
    cursor: pointer;
    border-bottom: 1px solid #f2f2f2;
    }

 

     .inline-table td{
        padding: 0 !important;
        border: solid 1px #ccc;
     }

     .table-desi tbody tr td.nop{
        padding: 10px 0 !important;
     }

     .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
        vertical-align: middle;
     }

     .tr-purple{
        background-color: #c1bcfb;
     }
     .tr-purple:hover{
        background-color: #D2B4DE;
        cursor: pointer;
     }

     .tr-yellow{
        background-color: #ffc984;
     }
     .tr-yellow:hover{
        background-color: #ffdeb5;
        cursor: pointer;
     }


     .tr-danger{
        background-color: #E6B0AA;
     }
     .tr-danger:hover{
        background-color: #f8ccc8;
        cursor: pointer;
     }



     .td-location{
        background: #f2f2f2;
        border-radius: 2px;
        padding: 1px 4px;
     }

     .information-div{
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;

     }

     .btn{
            display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
     }

     .btn-danger{
        color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
     }

     .btn-danger:hover, .btn-danger:focus, .btn-danger:active, .btn-danger.active, .open>.dropdown-toggle.btn-danger {
    color: #fff;
    background-color: #e48683;
    border-color: #e48683; /*set the color you want here*/
}

.log-table th{
    background: #f2f2f2;
}

.location-wrap{
    display: flex;
    justify-content: space-between;
}

.clock-item input{
    font-size: 20px !important;
}

#guide-table .active{
  
    border: solid 1px #333;
}










/*Media Print*/

@media print {
  
    *{
        color-adjust: exact;  -webkit-print-color-adjust: exact; print-color-adjust: exact !important; 
        padding: 0;
        margin: 0;
    }

  .sb2-2-1, .sb1, .sb2-1, .print-button {
    display: none;
  }

  #main-print-table{
    width: 100% !important;
  }

  .sb2-2{
    width: 100% !important;
    padding: 0 !important;
    margin-left: 0 !important;
  }

  .scheduled-tr{
    background-color: #ff0000 !important;
  }

  .update-shift *{
    background-color: #D4EFDF !important;
    -webkit-print-color-adjust: exact;
  }

   .tr-purple *{
    background-color: #c1bcfb !important;
    -webkit-print-color-adjust: exact;
   }  

   .tr-danger *{
    background-color: #E6B0AA !important;
    -webkit-print-color-adjust: exact;
   }

   #print-summary-table .sb2-2-1{
    display: block !important;
   }

   

   .set-off-day{
    display: none !important;
   }

   tr.set-off-day-tr, tr.set-off-day-tr *{
    background-color: #e0e0ad !important;
    -webkit-print-color-adjust: exact;
   }
}
</style>







<div class="container-fluid" id="main-container">
	


<div class="sb2-2-3">




<div class="row">
	
<div class="col-md-2">

    <div class="sb2-2-1">
                    <div>
                       <form action="{{url()->current()}}" id="index-month-form">
    <select name="index_guide_search" id="" class="form-control shaselect">
       
        <optgroup label="past">

            @for ($i=$to_past; $i>0; $i--)
                <option @if(request()->index_guide_search == \Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')) selected  @endif data-start="{{\Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')}}" data-end="{{\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')}}" value="{{\Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->copy()->subMonths($i)->format('F')}}</option>
            @endfor
            
        </optgroup>
        
        <optgroup label="current">
            <option @if(request()->index_guide_search == \Carbon\Carbon::now()->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d') || !isset(request()->index_guide_search)) selected @endif value="{{\Carbon\Carbon::now()->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->format('F')}}</option>
        </optgroup>


     <optgroup label="future">
         @for ($i=1; $i<=($to_future+1); $i++)
                <option @if(request()->index_guide_search == \Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')) selected  @endif data-start="{{\Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')}}" data-end="{{\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')}}" value="{{\Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->copy()->addMonths($i)->format('F')}}</option>
            @endfor

        </optgroup>
    </select>
    </form>
                    </div>
                    
                    <table class="table" id="guide-table">
                        <thead>
                          {{--   <tr>
                             
                                <th>User</th>
                                
                            </tr> --}}
                        </thead>
                        <tbody>

                            @foreach ($guides as $guide)
                                <tr @if($target_guide->id === $guide->id) class="active"  @endif>
                                
                                <td><a style="color: #000; font-weight: 700;" href="{{url('guide/detail/'.$guide->id)}}">{{$guide->name}} {{$guide->surname}}</a></td>
                               
                            </tr>
                            @endforeach
                            
                          
                        
                        </tbody>
                    </table>
                </div>
    

</div>


<div class="col-md-10" id="main-print-table">





<div class="row">

      
    
<div class="col-md-4" id="print-summary-table">

    <div class="sb2-2-1">
        <h4>Summary</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                {{ $target_guide->name . " " . $target_guide->surname }}
            </th>
            <th>
                Total
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Scheduled Hours</td>
            <td id="scheduled-target-result">0.00</td>
           
        </tr>
    

        <tr>
            <td>Total Hours</td>
            <td id="shifts-target-result">0.00</td>
           
        </tr>

        <tr>
            <td>Total Off Day</td>
            <td id="off-day-target-result"></td>
           
        </tr>
    </tbody>
</table>
</div><!--end of sb-->

</div> 

<div class="col-md-3">
    <button style="font-size: 18px; font-weight: bold;" class="btn btn-default btn-block active print-button">PRINT SHIFTS <i class="icon-cz-floppy"></i></button>
</div>

</div><br>





                            <div class="box-inn-sp">
                           
                                <div class="tab-inn">
                                    <div class="table-responsive table-desi">
                                        <table class="table table-hover table-bordered" id="details-table">
                                            <thead>





                                                <tr>
                                                    <th>Date</th>
                                                    <th>Scheduled Shifts</th>
                                                    <th style="width: 80px;">In</th>
                                                    <th style="width: 80px;">Out</th>
                                                    <th style="width: 500px;"></th>
                                                    <th>Total</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>


                                              @php
                                                  $carbon = isset(request()->index_guide_search) ? \Carbon\Carbon::parse(explode("#", request()->index_guide_search)[0]) :  \Carbon\Carbon::now();
                                                  $current_month_name = $carbon->format('F Y');
                                              @endphp
                                             @for($i=1; $i<=$days_of_month; $i++)

                                               @php
                                                   $date_of_loop = $carbon->firstOfMonth()->addDays($i-1)->format('Y-m-d');
                                                   $get_meetings = \App\Meeting::where('guides', 'LIKE', '%"'.$target_guide->id.'"%')->where('date', $date_of_loop)->orderBy('clock_in', 'asc')->get()->groupBy('time');



                                                  
                                               @endphp

                                                <tr>
                                                    <td>

                                                            @if(!$get_meetings->count())
                                                              @if($target_guide->offday()->where("date", $date_of_loop)->where("status", 1)->count())
                                                              
                                                                  <button title="Remove Off Day" style="width: 15px; padding: 3px; height: 15px; border-radius: 50%;" class="set-off-day btn btn-danger btn-sm active" data-guide-id="{{$target_guide->id}}" data-date="{{$date_of_loop}}"></button>
                                                              
                                                              @else

                                                              
                                                                  <button  title="Add Off Day"  style="width: 15px; padding: 3px; height: 15px; border-radius: 50%;" class="set-off-day btn btn-default btn-sm active" data-guide-id="{{$target_guide->id}}" data-date="{{$date_of_loop}}"></button>
                                                             

                                                              @endif

                                                            @endif


                                                        {{$i}} {{$current_month_name}}  {{$date_of_loop}} 



                                                      
                                                    </td>
                                                    <td class="scheduled-tr"><a href="#">
                                                        @foreach ($get_meetings as $time => $meetings)

                                                       
                                                         
                                                             <span class="list-enq-name">{{$meetings[0]->clock_in->format('H:i') . " - ". $meetings[0]->clock_out->format('H:i')}}</span>
                                                             
                                                        @endforeach
                                                       
                                                    	
                                                    </a>
                                                    </td>
                                                    <td colspan="3" class="nop" style="padding: 10px 0; margin: 0;">
                                                        

                                                          <table class="inline-table">

                                                            @if(!$get_meetings->count())
                                                              @if($target_guide->offday()->where("date", $date_of_loop)->where("status", 1)->count())
                                                              <tr style="background-color: #e0e0ad;" class="set-off-day-tr">
                                                                  <td style="text-align:center;"><a href="#" style="font-size:16px;">Off Day</a></td>
                                                              </tr>
                                                              @endif
                                                            

                                                            @endif

                                                                   @php
                                                                        $total_clocked = 0;
                                                                    @endphp  
                                                             @foreach ($get_meetings as $time => $meetings)


                                                          
                                                             @forelse ($meetings[0]->shifts()->where('guide_id', $target_guide->id)->orderBy('time_in', 'asc')->get() as $shift)
                                                               @php


                                                                   if(!empty($shift->time_in) && !empty($shift->time_out)){
                                                                    if($shift->time_in->timestamp > 0 && $shift->time_out->timestamp > 0){
                                                                    $total_clocked = $total_clocked + ($shift->time_out->timestamp-$shift->time_in->timestamp);
                                                                   }
                                                                   }
                                                                   
                                                                   

                                                               @endphp


                                                                   <tr data-meeting-id="{{$meetings[0]->id}}" data-tr-shift-id="{{$shift->id}}"  class="@if(!$shift->is_approved) tr-purple @endif update-shift"  data-toggle="modal" data-target="#inoutmodal" data-id="{{$shift->id}}">


                                                                     @if(!empty($shift->time_in))
                                                                  <td style="padding: 3px 0 !important; width: 80px; text-align: center;">{{$shift->time_in->format('H:i')}}</td>

                                                                  @else
                                                                  <td style="padding: 3px 0 !important; width: 80px; text-align: center;">-</td>
                                                                  @endif


                                                                  @if(!empty($shift->time_out))

                                                                  <td style="padding: 3px 0 !important; width: 80px; text-align: center;">{{$shift->time_out->timestamp > 0 ? $shift->time_out->format('H:i') : '-'}}</td>
                                                                  @else
                                                                 <td style="padding: 3px 0 !important; width: 80px; text-align: center;">-</td>
                                                                  @endif

                                                                   <td style="padding: 3px 0 !important; width: 500px;"><span class="td-location"><span class="icon-cz-location"></span> {{$shift->meeting_point}}</span></td>
                                                              </tr>

                                                              @empty

                                                              @if(\Carbon\Carbon::now()->timestamp > $meetings[0]->clock_out->timezone('Europe/Paris')->timestamp)


                                                                 <tr data-target-guide-id="{{$target_guide->id}}" data-meeting-date="{{$meetings[0]->date}}" data-meeting-point="{{$meetings[0]->opt->meetingPoint}}" data-tr-shift-id="-1"  class="tr-danger create-shift"  data-toggle="modal" data-target="#inoutmodal-create" data-id="-1" data-meeting-id="{{$meetings[0]->id}}">


                                                                   
                                                                  <td style="padding: 3px 0 !important; width: 80px; text-align: center;">-</td>
                                                                 

                                                               

                                                                  
                                                                 <td style="padding: 3px 0 !important; width: 80px; text-align: center;">-</td>
                                                                  

                                                                   <td style="padding: 3px 0 !important; width: 500px;"><span class="td-location"><span class="icon-cz-location"></span> {{$meetings[0]->opt->meetingPoint}} </span></td>
                                                              </tr>

                                                              @endif

                                                              




                                                             @endforelse

                                                             @endforeach
                                                       

                                                           

                                                       

                                                      </table>

                                                    </td>

                                                    <td class="shift-tr">
                                                        

                                                        @php

                                                            $total_hours = (int)($total_clocked/3600);
                                                            $total_minutes = round((($total_clocked - $total_hours*3600)/60));
                                                        @endphp

                                                    {{$total_hours}}:{{strlen($total_minutes) > 1 ? $total_minutes : "0".$total_minutes}}

                                                </td>
                                                    
                                                   
                                                </tr>

                                                @endfor







                                   





                                     





                                               






                                                  


                                          


                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>




</div>


 


</div>
     
</div>











<div id="inoutmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Shift Details</h4>
      </div>
      <div class="modal-body">




        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="shift-delete-button"><span class="icon-cz-trash"></span> Delete</button>
        <button type="button" class="btn btn-default" id="shift-approve-button"> <span class="icon-cz-rocket"></span> Approve</button>
      </div>
    </div>

  </div>
</div>





<div id="inoutmodal-create" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create Shift</h4>
      </div>
      <div class="modal-body">




        
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-default" id="shift-create-button"> <span class="icon-cz-rocket"></span> Create Shift</button>
      </div>
    </div>

  </div>
</div>





@include('panel-partials.scripts', ['page' => 'guides-index'])
@include('panel-partials.datatable-scripts', ['page' => 'guides-index'])

<script>
    
    $(document).ready(function() {

        var total_off_day = $(".set-off-day-tr").length;
        $("#off-day-target-result").html("<span>"+total_off_day+" Day</span>");





     function floatToTimeFormat(str){

        var parts = str.split(".");
        var minutes = parts[1];
        var hours = parts[0];

        var minute_to_hour = minutes/60 | 0;

       


        var minute_to_minute = minutes%60;

        
        
       
       var last_hour = parseInt(hours) + minute_to_hour;

       minute_to_minute = minute_to_minute < 10 ? '0'+minute_to_minute : minute_to_minute;
       

       return last_hour+ ":"+minute_to_minute;

     }




        var scheduled_text;
        var total_scheduled_time = 0;
        var total_scheduled_hour = 0;
        var total_scheduled_minute = 0;

        $(".scheduled-tr").each(function(index, el) {
            if(scheduled_text = $(this).find("a").text().trim()){

               scheduled_text.split("\n").forEach( function(element, index2) {
                
                

                 
                 

                 if(element.trim()){
                
                var scheduled_parts = element.split('-');

                var endparts = scheduled_parts[1].split(":");
                var endTotalTimestamp = parseInt(endparts[0].trim())*3600 + parseInt(endparts[1].trim())*60;

                var startparts = scheduled_parts[0].split(":");
                var startTotalTimestamp = parseInt(startparts[0].trim())*3600 + parseInt(startparts[1].trim())*60;
                var diff = (endTotalTimestamp - startTotalTimestamp);

                 console.log(scheduled_parts[0]+"-"+scheduled_parts[1] +" "+ (diff/60).toFixed(2));

                //var diff = parseFloat(scheduled_parts[1].replace(':', '.').trim()) - parseFloat(scheduled_parts[0].replace(':', '.').trim());
                //console.log(diff);
                 
                total_scheduled_time = total_scheduled_time + diff;
                 }
                  
               

               });

               



            }
        });
        
        total_scheduled_hour = parseInt(total_scheduled_time/3600);
        total_scheduled_minute = parseInt(total_scheduled_time%3600)/60;
        total_scheduled_minute = total_scheduled_minute < 10 ? "0"+total_scheduled_minute : total_scheduled_minute;

        $("#scheduled-target-result").text(total_scheduled_hour+":"+total_scheduled_minute);



    
      var shifted_text;
      var total_shifted_time = 0;
      var total_shifted_hour = 0;
      var total_shifted_minute = 0;

     $(".shift-tr").each(function(index, el) {
        
        if($(this).text().trim() !== "0:00"){
          shifted_text = $(this).text().trim();

          var exploded_shifted_text = shifted_text.split(':');
          total_shifted_hour = total_shifted_hour + parseInt(exploded_shifted_text[0]);
          total_shifted_minute = total_shifted_minute + parseInt(exploded_shifted_text[1]);

          //total_shifted_time += parseFloat(shifted_text.replace(':', '.'));
        }


         
       

     });

     total_shifted_hour += parseInt(total_shifted_minute/60);
     total_shifted_minute = total_shifted_minute % 60;
     total_shifted_minute = (total_shifted_minute < 10) ? "0"+total_shifted_minute : total_shifted_minute;
     total_shifted_time = parseFloat(total_shifted_hour+"."+total_shifted_minute);
     console.log("--- "+total_shifted_time.toFixed(2));
     

     $("#shifts-target-result").text(floatToTimeFormat(total_shifted_time.toFixed(2)));

     $(".print-button").css("height",$(".print-button").closest(".col-md-3").prev(".col-md-4").height()+"px");


     $(document).on('click', '.print-button', function(event) {
         event.preventDefault();
         window.print();
     });
      
    });
</script>