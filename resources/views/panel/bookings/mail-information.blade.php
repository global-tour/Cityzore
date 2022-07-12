@include('panel-partials.head', ['page' => 'mail-information'])
@include('panel-partials.header', ['page' => 'mail-information'])
@include('panel-partials.sidebar')

<style>
    .inn-title .select-dropdown,  .inn-title .caret{
        display: none!important;
    }
    span.badge{
        float: initial!important;
    }
</style>
<section>
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Mail Information</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-1">
        <h2 style="margin-bottom: 1%;">Mail Information</h2>
        <table id="datatable" class="table">
            <thead>
            <tr>
                <th style="width: 8%">Booking Ref No</th>
                <th>To</th>
                <th style="width: 12%">Option</th>
                <th>Mail</th>
                <th style="width: 8%">Date</th>
                <th style="width: 8%">Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($mails as $mail)
                <tr>
                    <?php \App\Booking::where('id', $mail->bookingID)->exists() ?
                        $booking = \App\Booking::where('id', $mail->bookingID)->first() :
                        $booking = null; ?>
                    @if($booking!=null)
                        @if(!($booking->gygBookingReference == null))
                            <td>{{$booking->gygBookingReference}}</td>
                        @else
                            <td>{{$booking->bookingRefCode}}</td>
                        @endif
                    @else
                        <td>Not Found</td>
                    @endif
                    <td>{{$mail->to}}</td>
                    <td>
                        {{json_decode($mail->data)[0]->options}}
                    </td>
                    <td>
                        {!!html_entity_decode(json_decode($mail->data)[0]->template)!!}
                    </td>
                    <td>{{$mail->updated_at}}</td>
                    <td style="text-align: center">
                        @if($mail->status == 0)
                            <span class="badge" style="background-color: red; color: white;">Failed</span>
                        @else
                            <span class="badge" style="background-color: #0f9d58; color:white;">Sent</span>
                        @endif
                            <hr>
                        @if($mail->read_count > 0)
                                <span class="badge" style="background-color: #0f9d58; color:white; margin-bottom: 10px">Read<span > {{$mail->read_count}}</span></span>
                            @else
                                <span class="badge" style="background-color: #c97300; color: white;">Not Read</span>
                            @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>


@include('panel-partials.scripts', ['page' => 'mail-information'])
@include('panel-partials.datatable-scripts', ['page' => 'mail-information'])
