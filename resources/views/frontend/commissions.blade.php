@include('frontend-partials.head', ['page' => 'commissions'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>
<section>
    <div class="db">
    @include('layouts.profile-sidebar.sidebar-left')
        <div class="col-lg-9">
            <div class="db-2-com db-2-main" style="background-color: white;">

                <h4 style="height:50px;">
                    {{__('commissions')}}
                    <span style="float:right;padding-left: 3%">
                        {{__('totalPayment')}}: <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor($totalPayment)}}
                    </span>
                    <span style="float: right">
                        {{__('totalCommission')}}: <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor($totalCommission)}}
                    </span>
                </h4>
                    @foreach($bookings as $date => $bookingg)
                    <div class="db-2-main-com-table">
                         <table id="datatable" class="responsive-table">
                        <thead class="hidden-md hidden-sm hidden-xs" style="background-color: #ccc;" data-toggle="tooltip" data-placement="left" data-original-title="test">
                        <tr>
                            <th>{{__('bookingDate')}} <br> <span style="color: #922B21; font-size: 12px; text-decoration: underline;">{{$date}}</span></th>
                            <th>{{__('travelDate')}}</th>
                            <th>{{__('bookingRef')}}</th>
                            <th>{{__('productRef')}}</th>
                            <th>{{__('optionName')}}</th>
                            <th>{{__('commission')}}  <span style="color: #922B21;">(<i class="{{session()->get('currencyIcon')}}"></i>{{number_format((float)$priceArray[$date]['totalCommission'], 2, '.', '')}} )</th>
                            <th>{{__('net')}} <span style="color: #922B21;"> (<i class="{{session()->get('currencyIcon')}}"></i>{{number_format((float)$priceArray[$date]['totalPayment'], 2, '.', '')}}</span> )</th>
                            <th><a href="{{url($langCodeForUrl.'/print-invoice-commissioner-bymonth/'.$date)}}">{{__('invoice')}} </a></th>
                        </tr>
                        </thead>
                        <tbody>

                         @foreach ($bookingg as $booking)


                            <tr @if(!is_null($booking->affiliateID)) class="affiliated"  @endif>
                                <td>{{date('d-F-Y', strtotime($booking->created_at))}}</td>
                                <td>{{$booking->date}}</td>
                                <td style="font-size: 17px;">
                                    <?php
                                    $explodedBookingRefCode = explode('-', $booking->bookingRefCode);
                                    ?>
                                    {{end($explodedBookingRefCode)}}
                                </td>
                                <td style="font-size: 17px;">
                                    {{$explodedBookingRefCode[0]}}
                                </td>
                                <td style="">
                                    <?php
                                    $option = App\Option::where('referenceCode', $booking->optionRefCode)->first();
                                    ?>
                                    {{$option->title}}
                                </td>
                                <td style="">
                                    <?php
                                    $cart = App\Cart::where('referenceCode', $booking->reservationRefCode)->first();
                                    ?>
                                    @if(is_null($booking->affiliateID))
                                    <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor($cart->tempCommission ? $cart->tempCommission : $cart->totalCommission, $booking->currencyID)}}
                                    @else
                                    <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateAffiliateCommission($cart->tempTotalPrice ? $cart->tempTotalPrice : $cart->totalPrice, $cart->optionID)}}
                                    @endif
                                </td>
                                <td>
                                    <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitorFromCart($booking->reservationRefCode, $booking->currencyID)}}
                                </td>
                                <td><a href="{{url($langCodeForUrl.'/print-invoice-commissioner/'.$booking->id.'/'.$totalCommission)}}" target="_blank">{{__('invoiceUpper')}}</a></td>
                            </tr>
                            @endforeach

                                </tbody>
                                 </table>
                                 </div>
                        @endforeach


            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'commissions'])
