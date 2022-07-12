@include('panel-partials.head', ['page' => 'special-offers-index'])
@include('panel-partials.header', ['page' => 'special-offers-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Special Offers</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Special Offers</h4>
                    <a href="{{url('/special-offers/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: hidden;">
                        <table id="datatable" class="responsive-table">
                            <thead>
                            <tr>
                                <th>Product & Option Title</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Discount</th>
                                <th>Min. Person/Cart Total</th>
                                <th>Used/Maximum Usability</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offersAsRows as $offer)
                                <tr>
                                    <td>{{$offer['productTitle']}} - {{$offer['optionTitle']}}</td>
                                    <td>{{$offer['type']}}</td>
                                    <td class="date">{{$offer['date']}}</td>
                                    <td class="discount">{{$offer['discount']}}</td>
                                    <td class="minPersonCartTotal">{{$offer['minPersonCartTotal']}}</td>
                                    <td>{{$offer['used']}}/{{$offer['maximumUsability']}}</td>
                                    <td>
                                        @if($offer['isActive'] == 1)
                                            <button data-date-type={{$offer['dateType']}} data-offer-id="{{$offer['id']}}" style="padding:8px 16px;font-size:12px;letter-spacing:1px;border: none;background-color:#e23464; color: white" class="changeSpecialOfferStatus" type="button">DEACTIVATE</button>
                                        @else
                                            <button data-date-type={{$offer['dateType']}} data-offer-id="{{$offer['id']}}" style="padding:8px 16px;font-size:12px;letter-spacing:1px;border: none;background-color: #0f9d58;color: white" class="changeSpecialOfferStatus" type="button">ACTIVATE</button>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{url('special-offers/deleteOldSpecialOffer')}}">
                                            @csrf
                                            <input type="hidden" id="productID" name="productID" value="{{$offer['productID']}}">
                                            <input type="hidden" id="optionID" name="optionID" value="{{$offer['optionID']}}">
                                            <input type="hidden" id="dateType" name="dateType" value="{{$offer['dateType']}}">
                                            <input type="hidden" id="from" name="from" value="{{$offer['from']}}">
                                            <input type="hidden" id="to" name="to" value="{{$offer['to']}}">
                                            <input type="hidden" id="dayName" name="dayName" value="{{$offer['dayName']}}">
                                            <input type="hidden" id="day" name="day" value="{{$offer['day']}}">
                                            <input type="hidden" id="hour" name="hour" value="{{$offer['hour']}}">
                                            <input type="hidden" id="requestType" name="requestType" value="normal">
                                            <button type="submit" style="margin-bottom: 7px;">
                                                <i style="font-size:18px;background-color:#dd2c00;padding: 5px 2px;" class="icon-cz-trash"></i>
                                            </button>
                                            @if($offer['dateType'] == "dateRange")
                                                <button class="editSpecialOffer">
                                                    <i style="font-size:18px;background-color:#54bb49;padding: 5px 2px;" class="icon-cz-edit"></i>
                                                </button>
                                                <button data-id="{{$offer['id']}}" class="updateSpecialOffer" style="display: none;">
                                                    <i style="font-size:18px;background-color:#5bc0de;padding: 5px 2px;" class="icon-cz-rocket"></i>
                                                </button>
                                            @endif
                                        </form>
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


@include('panel-partials.scripts', ['page' => 'special-offers-index'])
