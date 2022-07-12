@include('panel.pricing-partials.head')
@include('panel.pricing-partials.header')
@include('panel-partials.sidebar')
<style>
    .col-md-1{
        width: 10%;
    }
</style>


<div class="option-edit sb2-2 sb2-2-1">
    <h4>Create a New Pricing</h4>
    <hr>
    <input type="hidden" name="comission" class="comission" value="{{$comission}}">
    <input type="hidden" name="userType" class="userType" value="{{$userType}}">
    <input type="hidden" name="tierIterator" class="tierIterator" value="1">
    <div class="priceForm">
        <div class="form-group">
            <div class="input-field col-md-12 s12">
                <input id="pricingTitle" name="pricingTitle" type="text" class="validate form-control">
                <label for="pricingTitle">Title</label>
            </div>
        </div>
        <div class="perPersonDiv col-md-12">
            <!--Adult-->
            <div class="categoryDiv adultDiv col-md-12" data-sort="0" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="adult">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="adultLabel">Adult</label>
                    <button data-cat="adult" class="pull-right btn btn-primary adultRemove" style="display: none;">Remove</button>
                </div>
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        <input id="adultMin" name="adultMin" type="number" class="validate form-control">
                        <label for="adultMin">Min Age</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="adultMax" name="adultMax" type="number" class="validate form-control">
                        <label for="adultMax">Max Age</label>
                    </div>
                    <div class="col-md-2 s2">
                        <input id="ignoreadult" type="checkbox" value="1">
                        <label for="ignoreadult">Ignore this category</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson1" name="minPerson1" type="hidden" value="1" class="validate form-control minPerson">
                        <label id="minPersonLabel1">1 -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson1" name="maxPerson1" type="number" min="2" class="validate form-control maxPerson">
                        <label id="maxPersonLabel1">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price1" name="price1" min="0" type="number" onkeyup="calculateComission('price1', $(this));" step="any" class="validate form-control price">
                        <label for="price1">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        @unless(Auth::guard('admin')->check())
                        <label for="price1Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price1Com" style="color: #ff0000;"></span>
                        </div>
                        @endunless
                    </div>
                    <div class="input-field col-md-1 s1">
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="margin-bottom: 20px; margin-top: 20px;">
            <select class="browser-default custom-select" name="categorySelect" id="categorySelect">
                <option selected value="">Add age group</option>
                <option value="infant" data-sort="4">Infant</option>
                <option value="child" data-sort="3">Child</option>
                <option value="youth" data-sort="2">Youth</option>
                <option value="euCitizen" data-sort="1">EU Citizen</option>
            </select>
        </div>
        <div class="col-md-12">
            <input data-form="save" type="submit" class="btn btn-primary" value="Save" id="priceButton">
        </div>
    </div>
</div>


@include('panel.pricing-partials.scripts')

{{--@include('panel.pricing-partials.edit-scripts')--}}


