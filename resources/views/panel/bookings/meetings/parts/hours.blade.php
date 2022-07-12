            
               @php
               	sort($timesArray)
               @endphp

            @forelse ($timesArray as $time)
            	
            <span data-date="{{$currentDate}}" data-hour="{{$time}}" class="hour-item">{{$time == '00:00:00' ? 'Operating Hours' : $time}}</span>

              

              @empty

              <div class="alert alert-danger">No Hour For Selected Date</div>

            @endforelse
            
      