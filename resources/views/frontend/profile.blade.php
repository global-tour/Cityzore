@include('frontend-partials.head', ['page' => 'profile'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
?>

<section>
    <div class="db">
        @include('layouts.profile-sidebar.sidebar-left')
        <div class="col-lg-9">
            <div class="db-2-com db-2-main" style="background-color: white;">
                <h4>{{__('travelBooking')}}</h4>
                <div class="db-2-main-com db-2-main-com-table">
                    <table id="" class="responsive-table">
                        <thead class="hidden-md hidden-sm hidden-xs">
                        <tr>
                            <th>{{__('date')}}</th>
                            <th>{{__('tour')}}</th>
                            <th>{{__('bookingRef')}}</th>
                            <th>{{__('companyInfos')}}</th>
                            <th>{{__('more')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $booking)
                            <tr @if(!is_null($booking->affiliateID)) style="background: #EBDEF0;" @endif>
                                <td>
                                    <div class="date"
                                         style="background-color: #253d5214; text-align: center; width: 115px;">
                                        <p class="month"
                                           style="background-color: #f23434; color: white;">{{date('F', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</p>
                                        <p class="day"><strong
                                                style="color: #f23434; font-size:25px;">{{date('d', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</strong><br>{{date('D', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}
                                        </p>
                                        <p class="years" style="color: black">
                                            <strong>{{date('Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']))}}</strong>
                                        </p>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12" style="text-align: center">
                                                <p>{{__('time')}}</p>
                                                @foreach(json_decode($booking->hour, true) as $dateTime)
                                                    <p><strong>{{$dateTime['hour']}}</strong></p>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size: 17px;">
                                    @if(!(\App\Product::where('referenceCode', '=', explode('-', $booking->bookingRefCode)[0])->first()==null))
                                        <a href="{{asset(\App\Product::where('referenceCode', '=', explode('-', $booking->bookingRefCode)[0])->first()->url)}}"
                                           target="_blank">
                                            <strong>{{\App\Product::where('referenceCode', '=', explode('-', $booking->bookingRefCode)[0])->first()->title}}</strong>
                                        </a>
                                    @endif
                                    @if(!(\App\Option::where('referenceCode', '=', $booking->optionRefCode)->first()==null))
                                        <p><strong>{{__('option')}}
                                                :</strong>{{\App\Option::where('referenceCode', '=', $booking->optionRefCode)->first()->title}}
                                        </p>
                                    @endif
                                    <p><strong>{{__('participants')}}:</strong>
                                        @foreach(json_decode($booking->bookingItems, true) as $bookingItem)
                                            {{$bookingItem['category']}}: {{$bookingItem['count']}}
                                        @endforeach</p>
                                    <p><strong>{{__('leadTraveler')}}:</strong>
                                        @foreach(json_decode($booking->travelers, true) as $traveler)
                                            {{$traveler['firstName']}} {{$traveler['lastName']}}
                                    </p>
                                    <p><strong>{{__('phoneNumber')}}:</strong>{{$traveler['phoneNumber']}}</p>
                                    <p><strong>{{__('email')}}:</strong>{{$traveler['email']}}</p>

                                    @endforeach
                                </td>
                                <td style="text-align: center;">
                                    <p><strong>{{__('pricing')}}: <i
                                                class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor($booking->totalPrice, $booking->currencyID)}}
                                        </strong></p>
                                    <p><strong>{{explode('-', $booking->bookingRefCode)[3]}}</strong></p>
                                    <p><strong>{{__('bookedOn')}}
                                            :</strong><br>{{date('d F Y  H:i:s', strtotime($booking->created_at))}}</p>
                                    @if($booking->status == 4)
                                        <p><span style="padding:5px 10px;font-size: 14px"
                                                 class="alert alert-warning">{{__('pending')}}</span></p>
                                    @elseif($booking->status == 0)
                                        <p><span style="padding:5px 10px;font-size: 14px"
                                                 class="alert alert-success">{{__('success')}}</span></p>
                                    @elseif($booking->status == 1 || $booking->status == 2 || $booking->status == 3)
                                        <p><span style="padding:5px 10px;font-size: 14px"
                                                 class="alert alert-danger">{{__('canceled')}}</span></p>
                                    @elseif($booking->status == 5)
                                        <p><span style="padding:5px 10px;font-size: 14px"
                                                 class="alert alert-warning">{{__('pendingForConfirmation')}}</span></p>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($booking->companyID === -1)
                                        <p><strong>Paris Business and Travel</strong></p>
                                        <p><a href="tel:+33184208801"><strong>+33184208801</strong></a></p>
                                        <p>
                                            <a href="mailto:contact@cityzore.com"><strong>contact@cityzore.com</strong></a>
                                        </p>
                                    @else
                                        <p>
                                            <strong>{{\App\Supplier::where('id', '=', $booking->companyID)->first()->companyName}}</strong>
                                        </p>
                                        <p>
                                            <a href="tel:+905443741994"><strong>{{\App\Supplier::where('id', '=', $booking->companyID)->first()->phoneNumber}}</strong></a>
                                        </p>
                                        <p>
                                            <a href="mailto:{{\App\Supplier::where('id', '=', $booking->companyID)->first()->email}}"><strong>{{\App\Supplier::where('id', '=', $booking->companyID)->first()->email}}</strong></a>
                                        </p>
                                    @endif
                                    <p><strong>{{__('paymentMethod')}}:</strong></p>
                                    <p>{{\App\Invoice::where('bookingID', $booking->id)->first()->paymentMethod ?? ''}}</p>
                                </td>
                                <td>

                                    @if($booking->status != 4)
                                        <a href="{{url($langCodeForUrl.'/print-pdf-frontend/'.$cryptRelated->encrypt($booking->id))}}"
                                           target="_blank">
                                            <button type="submit" class="btn btn-xs btn-primary btn-block"
                                                    style="margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourVoucher')}}</button>
                                        </a>
                                    @endif


                                    <a href="{{url($langCodeForUrl.'/print-invoice-frontend/'.$cryptRelated->encrypt($booking->id))}}"
                                       target="_blank">
                                        <button type="submit" class="btn btn-xs btn-primary btn-block"
                                                style="margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourInvoice')}}</button>
                                    </a>

                                    @if($booking->cancel_policy_status && !in_array($booking->status, [2,3]))
                                        <a href="javascript:;"
                                           data-booking="{{ $cryptRelated->encrypt($booking->id) }}">
                                            <button type="submit" class="btn btn-xs btn-secondary btn-block"
                                                    style="margin-bottom: 3px; color: #fff; background-color: #dc3545; border-color: #dc3545; font-size: 12px; font-weight: bold;">
                                                Cancel
                                            </button>
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'profile'])
