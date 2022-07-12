@include('panel-partials.head', ['page' => 'finance-index'])
@include('panel-partials.header', ['page' => 'finance-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li>
            <a href="index.html"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li class="active-bre">
            <a href="#"> Finance</a>
        </li>
        <li class="page-back">
            <a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a>
        </li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <?php
    $allTimeTotal = 0;
    $allTimeNet = 0;
    $companyID = $supplier->id;
    $commissionerID = 0;
    $isPlatform = 0;
    if ($model == 'admin') {
        $companyID = '-1';
        $companyName = 'Paris Business & Travel';
    } else if ($model == 'commissioner') {
        $companyID = '0';
        $commissionerID = $supplier->id;
        $companyName = $supplier->companyName;
    } else if ($model == 'supplier') {
        $companyName = $supplier->companyName;
    }else if ($model == 'platform'){
        $companyName = $supplier->name;
        $isPlatform = 1;
    }
    $all_commission_rates = [];
    $options_checks = [];
    ?>
    <div class="inn-title">
        <h4>Finance Details of {{$companyName}}</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-9">
                @foreach($years as $year)
                    <div style="margin-top: 50px;" class="col-md-12">
                        <span style="padding: 20px; font-size: 24px!important;">Invoices {{$year}}
                            @if($year != date('Y'))
                            <button data-shown="0" class="btn btn-primary showHideButton">+</button>
                            @else
                            <button data-shown="1" class="btn btn-primary showHideButton">-</button>
                            @endif
                        </span>
                        <div class="col-md-12 yearsWrapper" @if($year != date('Y')) style="display:none;" @endif>
                            @php
                                $allTimeCreditorCom = 0;
                            @endphp

                            @foreach($financeMonthly as $monthly)
                                @if($year == $monthly['year'])
                                <?php
                                    $totalRate = 0;
                                    $netRate = 0;
                                    $bookings = $monthly['bookings'];
                                    $comTotal = 0;
                                    $creditTotal = 0;

                                    foreach($bookings as $b) {

                                        if(!$b->bookingOption)
                                            $bidd = '';

                                        if($companyID != 0){


                                        if(\App\Commission::whereDate('created_at', '<', \Carbon\Carbon::parse($b->created_at))->where('commissionerID', $commissionerID)->where('optionID', $b->bookingOption->id ?? $bidd)->exists()){



                                           $com  = \App\Commission::whereDate('created_at', '<', \Carbon\Carbon::parse($b->created_at))->where('commissionerID', $commissionerID)->where('optionID', $b->bookingOption->id ?? $bidd)->first();

                                           if(!in_array($b->optionRefCode, $options_checks)){

                                            $options_checks[] = $b->optionRefCode;

                                            $all_commission_rates[] = [
                                                'commission' => $com->commission,
                                                'option' => $b->optionRefCode
                                            ];

                                           }


                                              $totalRate = $totalRate + $b->totalPrice;
                                        if ($model == 'supplier') {
                                            $netRate = $netRate + $b->totalPrice - (($b->totalPrice) * ($com->commission) / 100);
                                        } else {
                                            $netRate = $netRate + ($b->totalPrice) * ($com->commission) / 100;
                                        }

                                        }else{

                                              $totalRate = $totalRate + $b->totalPrice;
                                        if ($model == 'supplier') {
                                            $netRate = $netRate + $b->totalPrice - (($b->totalPrice) * ($commissionRate) / 100);
                                        } else {
                                            $netRate = $netRate + ($b->totalPrice) * ($commissionRate) / 100;
                                        }


                                        }


                                        }else{
                                           $paymentMethod = \App\Invoice::where('bookingID', $b->id)->first()->paymentMethod;
                                            $cart = \App\Cart::where('referenceCode', $b->reservationRefCode)->first();
                                            $b->cart = $cart;
                                               $totalRate = $totalRate + $b->totalPrice;

                                              if (!empty($b->cart->tempCommission) && $b->cart->tempCommission > 0)
                                                {
                                                    $netRate += $b->cart->tempCommission;

                                                    if($paymentMethod == "COMMISSION"){
                                                        $comTotal += $b->cart->totalPrice - $b->cart->tempCommission;

                                                    }else{
                                                         $creditTotal +=  $b->cart->tempCommission;
                                                    }
                                                }
                                                else
                                                {
                                                    $netRate += $b->cart->totalCommission;

                                                     if($paymentMethod == "COMMISSION"){

                                                        $comTotal += $b->cart->totalPrice - $b->cart->totalCommission;

                                                    }else{
                                                        $creditTotal += $b->cart->totalCommission;

                                                    }
                                                }



                                        }








                                    }





                                              if($companyID != 0){
                                                $extraPayment = \App\ExternalPayment::where('is_paid', 1)->where('createdBy', $companyID)
                                    ->whereDate('updated_at','<=', \Carbon\Carbon::now()->lastOfMonth()->format('Y-m-d H:i:s'))
                                    ->get();

                                     $currency = \App\Currency::where('isActive', true)->get();
                                     $euro = $currency->where('currency', 'EUR')->first();

                                    foreach($extraPayment as $payment){
                                        $oran = $euro->value / $currency->where('currency', $payment->currency_code)->first()->value;
                                        $price = $payment->price * $oran;
                                        $totalRate += $price > 0 ? $price : 0;
                                        $netRate += $price > 0 ? $price : 0;
                                    }
                                }




                                    $allTimeTotal += $totalRate;
                                    $allTimeNet += $netRate;






                                ?>
                                <div class="col-md-12">
                                    <div class="payment_block" style="padding: 5px 8px; background: #f3f4f6; margin: 4px 0 6px 10px; border: 1px solid #b1b6bf;">
                                        <div class="head" style="border-bottom: 1px solid #ccc; padding-bottom: 2px; margin-bottom: 3px;">
                                            Invoice of {{$monthNames[$monthly['month']]}} {{$year}}
                                            <div class="pull-right">
                                                | <a target="_blank" href="{{url('finance-pdf/'.$monthly["month"].'/'.$monthly["year"].'/'.$totalRate.'/'.$companyID.'/'.$isPlatform. '/' . $commissionerID)}}?time={{time()}}"><b>PDF Invoice</b></a>
                                                | <a target="_blank" href="{{url('finance/exportToExcel?financeMonth='.$monthly["month"].'&financeYear='.$monthly["year"].'&companyID='.$companyID.'&commissioner='.$commissionerID)}}?time={{time()}}" style="color: #00be7f;"><b>Bookings as Excel File</b></a>
                                            </div>
                                        </div>
                                        <div class="body">


                                            Amount: € {{number_format($totalRate, 2)}}
                                            @if (in_array($model, ['supplier', 'commissioner']))
                                            @if($companyID == 0)
                                            | Net Rate: € {{number_format($creditTotal-$comTotal, 2)}}
                                            @else
                                            | Net Rate: € {{number_format($netRate, 2)}}
                                            @endif

                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                 @php
                                $allTimeCreditorCom += $creditTotal-$comTotal;
                            @endphp
                            @endforeach

                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-md-3">
                <div class="col-md-12" style="margin-top: 20px;">
                    Your Commission: <br>
                    general : % {{$supplier->commission}}
                    @foreach ($all_commission_rates as $rt)
                        <br>
                        option  {{$rt["option"]}} : % {{$rt["commission"]}}
                    @endforeach
                </div>
                <div class="col-md-12" style="margin-top: 20px;">
                    Total Amount: € {{number_format($allTimeTotal, 2)}}
                </div>
                @if (in_array($model, ['supplier', 'commissioner']))

                @if($companyID == 0)

                 <div class="col-md-12" style="margin-top: 20px;">
                    Your Earned: € {{number_format($allTimeCreditorCom, 2)}}
                </div>
                @else

                 <div class="col-md-12" style="margin-top: 20px;">
                    Your Earned: € {{number_format($allTimeNet, 2)}}
                </div>

                @endif


                @endif
                <div class="col-md-12">
                    <?php
                    if ($model == 'supplier') {
                        $firstUrlPart = 'supplier';
                    }
                    if ($model == 'commissioner') {
                        $firstUrlPart = 'user';
                    }
                    ?>
                    @if (in_array($model, ['supplier', 'commissioner']))
                    <a href="{{url('/'.$firstUrlPart.'/'.$supplier->id.'/edit')}}"><b>Edit Bank Details</b></a>
                    @endif
                    @if($model == 'admin')
                    <a href="{{url('/finance')}}" class="btn btn-primary" style="margin-top: 20px;">Search for Another Company</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'finance-index'])
@include('panel-partials.datatable-scripts', ['page' => 'finance-index'])

