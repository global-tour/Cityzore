@include('panel-partials.head', ['page' => 'coupon-index'])
@include('panel-partials.header', ['page' => 'coupon-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Coupons</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Coupons</h4>
                    <a href="{{url('/coupon/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Coupon Code</th>
                                <th>Discount for</th>
                                <th>Discount</th>
                                <th>Count of Using</th>
                                <th>Max Usability</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($coupons as $coupon)
                                <input type="hidden" class="couponID" value="{{$coupon->id}}">
                                <tr>
                                    @switch($coupon->type)
                                        @case(1)
                                        <td>Product - Option</td>
                                        <?php $discountFor = \App\Product::where('id', '=', $coupon->productID)->first()->title.' - '.\App\Option::findOrFail($coupon->lastSelect)->title ?>
                                        @break
                                        @case(2)
                                        <td>Location</td>
                                        <?php $discountFor = \App\Location::where('id', '=', $coupon->lastSelect)->first()->name ?>
                                        @break
                                        @case(3)
                                        <td>Attraction</td>
                                    <?php $discountFor = \App\Attraction::where('id', '=', $coupon->lastSelect)->first()->name ?>
                                        @break
                                        @case(4)
                                        <td>For User</td>
                                    <?php $discountFor = 'User' ?>
                                        @break
                                        @case(5)
                                        <td>All</td>
                                    <?php $discountFor = 'All' ?>
                                        @break
                                        @case(6)
                                        <td>New User Coupon</td>
                                    <?php $discountFor = \App\User::findOrFail($coupon->lastSelect)->name.' '.\App\User::findOrFail($coupon->lastSelect)->surname ?>
                                        @break
                                    @endswitch
                                        <td class="couponCode">{{$coupon->couponCode}}</td>
                                        <td>{{$discountFor}}</td>
                                        <td><span class="discount">{{$coupon->discount}}</span> <span class="discountType">{{$coupon->discountType}}</span></td>
                                        <td>{{$coupon->countOfUsing}}</td>
                                        <td class="maxUsability">{{$coupon->maxUsability}}</td>
                                        <td class="startingDate">{{$coupon->startingDate}}</td>
                                        <td class="endingDate">{{$coupon->endingDate}}</td>
                                        <td>
                                            <form method="POST" action="{{url('coupon/'.$coupon->id.'/delete')}}">
                                                @method('POST')
                                                @csrf
                                                {{ Form::button('<i class="icon-cz-trash" style="background: #ff0000!important;"></i>',['style="background:transparent;border:none;"', 'type' => 'submit', 'onclick' => 'return confirm("Are you sure?")'] )  }}
                                            </form>
                                        </td>
                                        <td>
                                            <a><span class="editCoupon"><i class="icon-cz-edit"></i></span></a>
                                            <a data-id="{{$coupon->id}}" class="updateCoupon" style="display:none;cursor:pointer;letter-spacing:2px;border-radius:3px;padding:6px 6px;color:white;background-color: #0e76a8">SAVE</a>
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


@include('panel-partials.scripts', ['page' => 'coupon-index'])
