@include('panel-partials.head', ['page' => 'guides-index'])
@include('panel-partials.header', ['page' => 'guides-index'])
@include('panel-partials.sidebar')


<div class="container-fluid" id="main-container">


    <div class="sb2-2-3">

        <h1>All Guides Movements</h1>


        <div class="row" style="margin-bottom: 10px;">

            <div class="col-md-3 col-md-offset-9">


                <form action="{{url()->current()}}" id="index-month-form">
                    <select name="index_guide_search" id="" class="form-control shaselect">
                        <optgroup label="All Months" selected>

                            <option value="all">All Months</option>

                        </optgroup>
                        <optgroup label="past">

                            @for ($i=$to_past; $i>0; $i--)
                                <option
                                    @if(request()->index_guide_search == \Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')) selected
                                    @endif data-start="{{\Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')}}"
                                    data-end="{{\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')}}"
                                    value="{{\Carbon\Carbon::now()->copy()->subMonths($i)->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->copy()->subMonths($i)->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->copy()->subMonths($i)->format('F')}}</option>
                            @endfor

                        </optgroup>

                        <optgroup label="current">
                            <option
                                @if(request()->index_guide_search == \Carbon\Carbon::now()->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d')) selected
                                @endif value="{{\Carbon\Carbon::now()->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->format('F')}}</option>
                        </optgroup>


                        <optgroup label="future">
                            @for ($i=1; $i<=($to_future+1); $i++)
                                <option
                                    @if(request()->index_guide_search == \Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')."#".\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')) selected
                                    @endif data-start="{{\Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')}}"
                                    data-end="{{\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')}}"
                                    value="{{\Carbon\Carbon::now()->copy()->addMonths($i)->firstOfMonth()->format('Y-m-d')}}#{{\Carbon\Carbon::now()->copy()->addMonths($i)->lastOfMonth()->format('Y-m-d')}}">{{\Carbon\Carbon::now()->copy()->addMonths($i)->format('F')}}</option>
                            @endfor

                        </optgroup>
                    </select>
                </form>
            </div>
        </div>

        <div class="row">


            <div class="col-md-12">
                <div class="box-inn-sp">

                    <div class="tab-inn">
                        <div class="table-responsive table-desi">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>

                                    <th>Email</th>
                                    <th>Scheduled Hours</th>
                                    {{--<th>CH</th>
                                    <th>OT</th>--}}
                                    <th>Total Hours</th>
                                    <th>Settings</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($guides as $guide)


                                    @php
                                        $all_hours = 0;
                                        $all_minute = 0;
                                        $all_hours_for_shifts = 0;
                                        $all_minute_for_shifts = 0;

                                       $all_timestamp = 0;
                                       $all_timestamp_for_shifts = 0;

                                        $meeting_before_clock = [];
                                       foreach($all_meetings as $meeting){
                                         if(in_array($guide->id, json_decode($meeting->guides, true))  && !in_array(\Carbon\Carbon::parse($meeting->date." ".$meeting->time)->timestamp, $meeting_before_clock)){
                                              $meeting_before_clock[] = \Carbon\Carbon::parse($meeting->date." ".$meeting->time)->timestamp;
                                              $all_timestamp = $all_timestamp + $meeting->clock_out->diffInSeconds($meeting->clock_in);



                                            foreach($meeting->shifts()->where('guide_id', $guide->id)->get() as $shift){

                                                if(!empty($shift->time_out))
                                            $all_timestamp_for_shifts = $all_timestamp_for_shifts + $shift->time_out->diffInSeconds($shift->time_in);


                                            }



                                            }

                                       }

                                       $all_hours = (int)($all_timestamp / 3600);
                                       $all_minute = (int)(($all_timestamp % 3600) / 60);

                                       $all_hours_for_shifts = (int)($all_timestamp_for_shifts / 3600);
                                       $all_minute_for_shifts = (ceil(($all_timestamp_for_shifts % 3600) / 60));





                                    @endphp


                                    <tr>
                                        <td><span class="list-img">{{$guide->id}}</span>
                                        </td>
                                        <td><a href="{{url('guide/detail/'.$guide->id)}}"><span
                                                    class="list-enq-name">{{$guide->name}} {{$guide->surname}}</span>
                                                {{--<span class="list-enq-city">Illunois, United States</span>--}}
                                            </a>
                                        </td>

                                        <td>{{$guide->email}}</td>
                                        <td>{{$all_hours}}
                                            :{{strlen($all_minute) == 1 ? '0'.$all_minute : $all_minute}}</td>
                                        {{--<td>
                                           16.30
                                        </td>
                                        <td>
                                            5.00
                                        </td>--}}
                                        <td>
                                            {{$all_hours_for_shifts}}
                                            :{{strlen($all_minute_for_shifts) == 1 ? '0'.$all_minute_for_shifts : $all_minute_for_shifts}}
                                        </td>
                                        <td>

                                            <a href="{{url('guide/detail/'.$guide->id)}}"><i
                                                    class="icon-cz-edit"></i></a>
                                            {{--<a href=""><i class="icon-cz-trash" style="background: #CD6155;"></i></a>--}}
                                        </td>
                                    </tr>
                                @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>


    </div>

</div>


@include('panel-partials.scripts', ['page' => 'guides-index'])
@include('panel-partials.datatable-scripts', ['page' => 'guides-index'])
