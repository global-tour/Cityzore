<style>
  table{
    border: solid 1px;
  }

  table td{
    border: solid 1px #000;
  }

  table td{
    font-size: 12px;
    text-align: center;
  }
</style>



<table>

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

      @php
        if(App\Meeting::where('option', $key)->where('time', $currentTime)->where('date', $currentDate)->exists()){

            $meeting = App\Meeting::where('option', $key)->where('time', $currentTime)->where('date', $currentDate)->first();
            $admins = App\Admin::whereIn('id', json_decode($meeting->guides, true))->get()->pluck('adminFullName')->toArray();
        }else{
           $admins = [];
        }
      @endphp


    <thead>
        <tr>

        <th colspan="4" style="background-color: white; color: black; font-size: 12px; font-weight: 700; padding: 20px; height: 20px;">{{$supplierID}} -{{$option->title}} - {{$currentDate}}



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

                 if($value[0]["operating_hour"] == '[{"hour":"00:00"}]'){
                    $value[0]["operating_hour"] = json_encode([["hour" => "!Warning: no time range has been set for this date"]]);
                  }



                @endphp



                             @foreach (json_decode($value[0]["operating_hour"], true) as $ohour)
                                - ( {{$ohour["hour"]}} )
                             @endforeach

                             @else
                             - {{$currentTime}}
                             @endif




       </th>

        <th style="background-color: #3a87fc; font-size:13px;">
            Total<br><span>{{$value[0]["totalPeople"]}}</span>
        </th>
        <th colspan="2" @if(count($admins)) style="background-color: #F5B041; font-size:12px;" @else style="background-color: #ff0000; font-size:13px;"  @endif>{!! count($admins) ? implode("<br>",$admins) : 'No Guide' !!}</th>


    </tr>
    <tr>
        <th style="background-color: #CCCCCC; font-size: 13px;">RefCode</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">Full Name</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">Phone</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">All Category</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">Ignored Category</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">Ticket</th>
        <th style="background-color: #CCCCCC; font-size: 13px;">Status</th>

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
                    <span>{{(string)$v2->phoneNumber}}</span>
                    @endforeach
            </td>


            <td>
                @php
                    $ignored = '';
                @endphp
                   @foreach ($v['bookingItems'] as $el)



                              @if(in_array($el->category, $ignoredCategories))

                               @php
                                   $ignored.= " ".$el->category
                               @endphp

                                {{$el->category}}: {{$el->count}}

                               @else
                              {{$el->category}}: {{$el->count}}

                               @php
                                $tickets += (int)$el->count;
                               @endphp

                               @endif

                             @endforeach

             </td>
             <td>{{$ignored}}</td>

            <td>{{$tickets}}</td>


            <td>
                            @if($v['check'])
                             Checkin
                             @else
                              Not Checkin
                             @endif
            </td>
        </tr>

        @endforeach

    </tbody>


      @endforeach
</table>
