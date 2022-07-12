@include('panel-partials.head', ['page' => 'commissioners-edit'])
@include('panel-partials.header', ['page' => 'commissioners-edit'])
@include('panel-partials.sidebar')


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
            <h4>Edit Commissions</h4>
        </div>
        <div class="row">
            <div style="height: 300px;" class="col-md-12">
                <div class="form-group">
                    <input type="hidden" id="commissionerID" value="{{$commissioner->id}}">
                    <div class="col-md-12">
                        <div class="form-group input-field col-md-offset-1 col-md-4 s4">
                            <select class="browser-default custom-select select2" name="product" id="productSelect">
                                <option data-foo="" selected>Choose a Product</option>
                                @foreach($products as $product)
                                    <option data-foo="{{$product->referenceCode}}" value="{{$product->id}}">{{$product->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group input-field col-md-4 s4">
                            <select class="browser-default custom-select select2" name="option" id="optionSelect"></select>
                        </div>
                    </div>
                    <div class="col-md-12" style="display: none;" id="commissionDiv">
                        <div class="col-md-offset-1 col-md-4">
                            <label for="commission">Commission</label>
                            <input id="commission" type="number" step="0.1" class="form-control" name="commission" value="" required autofocus>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" id="saveCommissionButton">Save Commission</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'commissioners-edit'])
