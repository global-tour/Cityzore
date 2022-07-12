               @foreach ($responseData['data'] as $key => $value)
               <li class="option-item"><input id="option{{$loop->iteration}}" type="checkbox" name="option[]" value="{{$value['referenceCode']}}" data-operating="{{$value['operating_hour']}}"><label for="option{{$loop->iteration}}">{{$value['title']}} 
                @if($value["time"] === "00:00:00")


                @php
                	if($value["operating_hour"] == '[{"hour":"00:00"}]'){

                    $option = \App\Option::findOrFail($value['id']);
                    $av = $option->avs()->first();

                    foreach(json_decode($av->daily, true) as $day){
                    if($day["day"] === $value["day"]){
                     
                     $value["operating_hour"] = json_encode([["hour" => $day["hourFrom"]."-".$day["hourTo"]]]);

                    	break;
                    }
                    }

 
                
                	}

                	if($value["operating_hour"] == '[{"hour":"00:00"}]'){
                	  $value["operating_hour"] = json_encode([["hour" => "!Warning: no time range has been set for this date"]]);
                	}
                	


                @endphp

                @foreach (json_decode($value["operating_hour"], true) as $hour_wrap)
                	<span style="display: block; font-size: 11px; border-radius: 5px; padding: 2px;">( {{$hour_wrap["hour"]}} )</span>
                @endforeach

                

                @endif
 

                </label></li>
                

               @endforeach
 
            