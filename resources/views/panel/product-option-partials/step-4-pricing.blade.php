<div class="tab-pane fade option-setup-content" id="step4">
    <h4>Choose an Old Pricing</h4>
    <div class="form-group">
        <select class="select2 browser-default custom-select" name="pricings" id="pricings">
            <option value="" selected>Choose a Pricing</option>
            @foreach($pricings as $pricing)
            <option value="{{$pricing->id}}">{{$pricing->title}}</option>
            @endforeach
        </select>
        <span class="pricingsErrorSpan errorSpan col s12" style="display: none!important; color: #ff0000;">You must choose a pricing.</span>
    </div>
    <hr style="border: 3px solid #eee;">
    <input type="hidden" name="tierIterator" class="tierIterator" value="1">
    <div class="col-md-12 text-center">
        <h4>Or Create a New Pricing</h4>
    </div>
    <div class="priceForm">
        <div class="form-group">
            <div class="input-field col-md-12 s12">
                <input id="pricingTitle" name="pricingTitle" type="text" class="validate form-control">
                <label for="pricingTitle">Title</label>
            </div>
        </div>
        <div class="perPersonDiv col-md-12">
            <!--Adult-->
            <div class="categoryDiv adultDiv col-md-12" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
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
                        <label id="maxPersonLabel1" style="top: 0px;">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price1" name="price1" min="0" type="number" onkeyup="calculateComission('price1', $(this));" step="any" class="validate form-control price" style="width: 50px;">
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
        <div class="col-md-12" style="margin-bottom: 20px; margin-top: 20px;">
            <select class="browser-default custom-select col-md-4" name="categorySelect" id="categorySelect">
                <option selected value="">Add age group</option>
                <option value="infant">Infant</option>
                <option value="child">Child</option>
                <option value="youth">Youth</option>
                <option value="euCitizen">EU Citizen</option>
            </select>
        </div>
    </div>
    <hr style="border: 3px solid #eee;">
    <h4 style="margin-top: 20px;">Choose Ticket Type</h4>
    <div class="form-group">
        <select class="select2 browser-default custom-select" name="ticketTypes" id="ticketTypes">
            <option value="" selected>Not Selected</option>
            @foreach($ticketTypes as $ticketType)
                <option value="{{$ticketType->id}}">{{$ticketType->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <h4 style="margin-top: 40px;">Blockout Hours</h4>
        <hr>
        <div class="btn btn-primary addBlockoutBlock" style="background-color: #e34f2a;">+</div>
        <div class="blockoutContainer">

        </div>
    </div>
</div>
