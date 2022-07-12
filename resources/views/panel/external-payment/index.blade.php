@include('panel-partials.head', ['page' => 'external-payment-index'])
@include('panel-partials.header', ['page' => 'external-payment-index'])
@include('panel-partials.sidebar')
    <head>
        <style>
            select:disabled {
                color: rgba(23, 15, 15, 0.96);
            }
        </style>
    </head>

    <div class="sb2-2-2">
        <ul>
            <li><a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> External Payment</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <div class="box-inn-sp">
            <div class="inn-title">
                <h4>External Payment Links</h4>
                <a href="{{url('/external-payment/create')}}" class="btn btn-default pull-right">Add New</a>
            </div>
            <div class="bor">
                <div class="row" style="margin-top: 50px;overflow-x: auto;">
                    <table  id="datatable" class="table">
                        <thead>
                        <tr>
                            <th style="width: 300px;">Payment Link</th>
                            <th>Ref. Code</th>
                            <th style="width: 150px;">Booking Ref. Code</th>
                            <th>E-Mail</th>
                            <th>Message</th>
                            <th>Price</th>
                            <th>Paid</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($paymentLinks as $paymentLink)
                        <tr>
                            <td>{{$paymentLink->payment_link}}</td>
                            <td>@if(!is_null($paymentLink->referenceCode)) {{$paymentLink->referenceCode}} @else - @endif</td>
                            <td class="bookingRefCode" style="width: 15%">
                                <select id="bookings" disabled class="select2 browser-default custom-select ">
                                    <option {{is_null($paymentLink->bookingRefCode) ? 'selected':''}} value="">-</option>
                                    @foreach($bookingsToBeSelected as $booking)
                                        <option {{$paymentLink->bookingRefCode == $booking ? 'selected':''}} value={{$booking}}>{{$booking}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{$paymentLink->email}}</td>
                            <td class="message">{{$paymentLink->message}}</td>
                            @php
                                $currencySymbol = "";
                                switch ($paymentLink->currency) {
                                    case '978':
                                        $currencySymbol = "icon-cz-eur";
                                        break;
                                    case '949':
                                        $currencySymbol = "icon-cz-try";
                                        break;
                                    case '840':
                                        $currencySymbol = "icon-cz-usd";
                                        break;
                                    case '826':
                                        $currencySymbol = "icon-cz-gbp";
                                        break;

                                    default:
                                        $currencySymbol = "icon-cz-eur";
                                        break;
                                }
                            @endphp
                            <td><i class="{{$currencySymbol}}"></i> {{$paymentLink->price}}</td>
                            <td>
                                <p>
                                    @if($paymentLink->is_paid == '1')
                                    <span class="db-done">Paid</span>
                                    @else
                                    <span class="db-not-done">Not Paid</span>
                                    @endif
                                </p>
                            </td>
                            <td>{{date('d/m/Y H:i:s', strtotime($paymentLink->created_at))}}</td>
                            <td>
                                <button data-id="{{$paymentLink->id}}" class="btn btn-xs btn-primary btn-block resendEmail" @if($paymentLink->is_paid == 1) disabled @endif style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Re-send Email</button>
                                @if($paymentLink->is_paid == 1)
                                    <button class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;"><a href="{{url('external-payments-pdf/'.$paymentLink->id)}}" target="_blank">Download Invoice</a></button>
                                @endif
                                <button class="btn btn-xs btn-primary btn-block editExternalPayment" style="width: 120px; padding: 0px;margin-bottom: 3px; color: white; background-color: #54bb49; border-color: #8852E4; font-size: 10px; font-weight: bold;">Edit</button>
                                <button data-id="{{$paymentLink->id}}" class="btn btn-xs btn-primary btn-block updateExternalPayment" style="width: 120px; padding: 0px;margin-bottom: 3px; color: white; background-color: #5bc0de; border-color: #8852E4; font-size: 10px; font-weight: bold; display: none;">Update</button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@include('panel-partials.scripts', ['page' => 'external-payment-index'])
@include('panel-partials.datatable-scripts', ['page' => 'external-payment-index'])
