@include('panel-partials.head', ['page' => 'coupon-create'])
@include('panel-partials.header', ['page' => 'coupon-create'])
@include('panel-partials.sidebar')


<div class="writeinfo"></div>
@csrf
<div class="sb2-2-2">
    <ul>
        <li><a href="{{url('/')}}"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Add New</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <div class="box-inn-sp">
        <div class="inn-title">
            <h4>Create Coupon</h4>
        </div>
        <div class="bor">
            <div class="form-group">
                <div class="input-field col-md-3 s3">
                    <select class="browser-default custom-select select2" name="couponType" id="couponTypeSelect">
                        <option data-foo="" selected>Choose A Coupon Type</option>
                        <option value="1">Product - Option Coupon</option>
                        <option value="2">Location Coupon</option>
                        <option value="3">Attraction Coupon</option>
                        <option value="4">Coupon for Users</option>
                        <option value="5">All</option>
                        <option value="6">Coupon for Specific User</option>
                    </select>
                </div>
                <div style="display: none" class="form-group input-field col-md-3 s3">
                    <select style="width: 100%!important;" class="browser-default custom-select select2" name="product" id="productSelect">
                    </select>
                </div>
                <div style="display: none" class="form-group input-field col-md-3 s3">
                    <select style="width: 100%!important;" class="browser-default custom-select select2" name="lastSelect" id="lastSelect">
                    </select>
                </div>
                <div style="display: none" id="discountType" class="form group input-field col-md-4 s4">
                    <p>
                        <input name="radio" type="radio" id="percentRadio" value="percent" checked/>
                        <label style="font-size: 1.1rem!important;" for="percentRadio">Percent</label>
                    </p>
                    <p>
                        <input name="radio" type="radio" id="netRateRadio" value="net rate"/>
                        <label style="font-size: 1.1rem!important;" for="netRateRadio">Net Rate</label>
                    </p>
                </div>
                <div style="display:none" id="maxUsabilityDiv" class="form-group input-field col-md-4 s4">
                    <div class="col-md-12">
                        <input type="text" id="maxUsability" placeholder="Count of using">
                    </div>
                    <div class="col-md-12">
                        <input type="text" id="discount" placeholder="Discount">
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="couponCode" placeholder="Coupon Name">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary" id="generateName">Generate Name</button>
                    </div>
                </div>
                <div style="display: none" id="startingAndEndingDateDiv" class="form-group input-field col-md-4 s4">
                    <input type="date" id="startingDate">
                    <input type="date" id="endingDate">
                </div>
            </div>
            <div class="row">
                <div class="input-field row col-md-12">
                    <button class="btn btn-primary" id="saveCoupon">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'coupon-create'])
