@if($isCommissioner)
    <?php
        $cartIds = [];
    ?>
    @foreach($cart as $i => $c)
    <tbody>
        <?php
            array_push($cartIds, $c->id);
            $specials = $c->specials;
            $discount = 0;
            if ($totalCommission == 0) {
                $discount = $totalPriceWOSO - $totalPrice;
            }
        ?>
        <tr>
            <th scope="row">
                <img src="{{Storage::disk('s3')->url('product-images-xs/' . $images[$i])}}" alt="" style="border-radius: 25%; width:100px; height:100px;">
            </th>
            <th>
                <span style="font-size: 14px">{{App\Product::where('id', '=', $c->productID)->first()->title}}</span><br>
                <span style="font-size: 14px;@if($isCommissioner)display:none;@endif">
                    <p>
                        <i class="{{session()->get('currencyIcon')}}"></i>{{$c->totalPrice + $c->totalCommission}}
                    </p>
                </span>
                <span>
                    @foreach(json_decode($c->bookingItems, true) as $bi)
                        <span style="font-size: 12px">{{$bi['category']}} x {{$bi['count']}}</span>
                    @endforeach
                </span>
            </th>
        </tr>
        <input hidden id="translationArray" value="{{$translationArray}}">
        <td hidden><i class="{{session()->get('currencyIcon')}}"></i> <input class="totalPrice" style="height:auto;border:none;width: 25%" readonly value="{{$c->totalPrice}}"></td>
        <tr>
            <th>{{__('commission')}}</th>
            <td>
                <div class="col-lg-4 input-group number-spinner">

                    <input class="spinnerInputs form-control text-center totalCommission" data-id="{{$c->id}}" data-specials-discount="{{!empty($c->specials) ? json_decode($c->specials, true)['discount'] : null}}" data-specials-type ="{{!empty($c->specials) ? json_decode($c->specials,true)['discountType'] : null}}"  step="0.01" name="totalCommission" value="{{$c->totalCommission}}" max="{{$c->maxCommission}}" min="0" type="number"  style="border:none;text-align:center;width:100%;height: auto;">

                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">{{__('subtotal')}}</th>
            @if(is_null(session()->get('totalPriceWithDiscount')))
                <td><i class="col-md-2 {{session()->get('currencyIcon')}}"></i><input class="form-control text-center amountOfPayment" readonly value="{{$c->totalPrice}}"  style="border:none;text-align:center;width:50%;height: auto;background: none"></td>
            @endif
        </tr>
    </tbody>
    @endforeach
    <input type="hidden" id="cartIds" value="{{json_encode($cartIds)}}" >
    <tr>
        <th style="color: #ff0000;font-weight: bold" scope="row">{{__('totalPrice')}}</th>
        <td style="text-align:right;font-size:16px;color: #ff0000;font-weight: bold;"><i class="{{session()->get('currencyIcon')}}"></i> <input data-total-price="{{$totalPrice}}" style="font-size:16px!important;height:auto;border:none;width: 30%" readonly value="{{$totalPrice}}" id="totalPriceForAllCart"></td>
    </tr>
    <tr>
        <th style="color: #ff0000;font-weight: bold;width: 50%" scope="row">{{__('totalCommission')}}</th>
        <td style="text-align:right;font-size:16px;color: #ff0000;font-weight: bold;"><i class="{{session()->get('currencyIcon')}}"></i> <input data-total-commission="{{$totalCommission}}" style="font-size:16px!important;height:auto;border:none;width: 30%" readonly value="{{$totalCommission}}" id="totalCommissionForAllCart"></td>
    </tr>
@endif
