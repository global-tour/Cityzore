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
    <input type="hidden" name="tierIterator" class="tierIterator" value="{{$tierIterator}}">
    <input type="hidden" name="pricingID" class="pricingID" value="{{$pricing->id}}">
    <div class="priceForm">
        <div class="form-group">
            <div class="input-field col-md-12 s12">
                <input id="pricingTitle" name="pricingTitle" type="text" class="validate form-control" value="{{$pricing->title}}">
                <label for="pricingTitle">Title</label>
            </div>
        </div>
        <div class="perPersonDiv col-md-12">
            <!--Infant-->
            @if(!is_null($infantPrice))
            <div class="categoryDiv infantDiv col-md-12" data-sort="4" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="infant">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="infantLabel">Infant</label>
                    <button data-cat="infant" class="pull-right btn btn-primary infantRemove">Remove</button>
                </div>
                @for($i = 0; $i < $tierIterator; $i++)
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="infantMin" name="infantMin" type="number" class="validate form-control" value="{{$pricing->infantMin}}">
                        <label for="infantMin">Min Age</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="infantMax" name="infantMax" type="number" class="validate form-control" value="{{$pricing->infantMax}}">
                        <label for="infantMax">Max Age</label>
                        @endif
                    </div>
                    <div class="col-md-2 s2">
                        @if($i == 0)
                        <input id="ignoreinfant" type="checkbox" value="1" @if(!is_null($ignoredCategories) && in_array('infant', $ignoredCategories)) checked @endif>
                        <label for="ignoreinfant">Ignore this category</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson{{$i+1}}" name="minPerson{{$i+1}}" type="hidden" value="{{$minPerson[$i]}}" class="validate form-control minPerson">
                        <label id="minPersonLabel{{$i+1}}">{{$minPerson[$i]}} -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson{{$i+1}}" name="maxPerson{{$i+1}}" type="number" min="{{$minPerson[$i]}}" value="{{$maxPerson[$i]}}" class="validate form-control maxPerson">
                        <label id="maxPersonLabel{{$i+1}}">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price{{$i+1}}" name="price{{$i+1}}" type="number" min="0" value="{{$infantPrice[$i]}}" onkeyup="calculateComission('price{{$i+1}}', $(this));" step="any" class="validate form-control price">
                        <label for="price{{$i+1}}">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        @unless(Auth::guard('admin')->check())
                        <label for="price{{$i+1}}Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price{{$i+1}}Com" style="color: #ff0000;">{{$infantPriceCom[$i]}}</span>
                        </div>
                        @endunless
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                        @else
                        <button id="deleteTier{{$i+1}}" class="btn btn-primary deleteTier">X</button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            @endif
            <!--Child-->
            @if(!is_null($childPrice))
            <div class="categoryDiv childDiv col-md-12" data-sort="3" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="child">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="childLabel">Child</label>
                    <button data-cat="child" class="pull-right btn btn-primary childRemove">Remove</button>
                </div>
                @for($i = 0; $i < $tierIterator; $i++)
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="childMin" name="childMin" type="number" class="validate form-control" value="{{$pricing->childMin}}">
                        <label for="childMin">Min Age</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="childMax" name="childMax" type="number" class="validate form-control" value="{{$pricing->childMax}}">
                        <label for="childMax">Max Age</label>
                        @endif
                    </div>
                    <div class="col-md-2 s2">
                        @if($i == 0)
                        <input id="ignorechild" type="checkbox" value="1" @if(!is_null($ignoredCategories) && in_array('child', $ignoredCategories)) checked @endif>
                        <label for="ignorechild">Ignore this category</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson{{$i+1}}" name="minPerson{{$i+1}}" type="hidden" value="{{$minPerson[$i]}}" class="validate form-control minPerson">
                        <label id="minPersonLabel{{$i+1}}">{{$minPerson[$i]}} -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson{{$i+1}}" name="maxPerson{{$i+1}}" type="number" min="{{$minPerson[$i]}}" value="{{$maxPerson[$i]}}" class="validate form-control maxPerson">
                        <label id="maxPersonLabel{{$i+1}}">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price{{$i+1}}" name="price{{$i+1}}" type="number" min="0" value="{{$childPrice[$i]}}" onkeyup="calculateComission('price{{$i+1}}', $(this));" step="any" class="validate form-control price">
                        <label for="price{{$i+1}}">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        @unless(Auth::guard('admin')->check())
                        <label for="price{{$i+1}}Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price{{$i+1}}Com" style="color: #ff0000;">{{$childPriceCom[$i]}}</span>
                        </div>
                        @endunless
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                        @else
                        <button id="deleteTier{{$i+1}}" class="btn btn-primary deleteTier">X</button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            @endif
            <!--Youth-->
            @if(!is_null($youthPrice))
            <div class="categoryDiv youthDiv col-md-12" data-sort="2" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="youth">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="youthLabel">Youth</label>
                    <button data-cat="youth" class="pull-right btn btn-primary youthRemove">Remove</button>
                </div>
                @for($i = 0; $i < $tierIterator; $i++)
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="youthMin" name="youthMin" type="number" class="validate form-control" value="{{$pricing->youthMin}}">
                        <label for="youthMin">Min Age</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="youthMax" name="youthMax" type="number" class="validate form-control" value="{{$pricing->youthMax}}">
                        <label for="youthMax">Max Age</label>
                        @endif
                    </div>
                    <div class="col-md-2 s2">
                        @if($i == 0)
                        <input id="ignoreyouth" type="checkbox" value="1" @if(!is_null($ignoredCategories) && in_array('youth', $ignoredCategories)) checked @endif>
                        <label for="ignoreyouth">Ignore this category</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson{{$i+1}}" name="minPerson{{$i+1}}" type="hidden" value="{{$minPerson[$i]}}" class="validate form-control minPerson">
                        <label id="minPersonLabel{{$i+1}}">{{$minPerson[$i]}} -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson{{$i+1}}" name="maxPerson{{$i+1}}" type="number" min="{{$minPerson[$i]}}" value="{{$maxPerson[$i]}}" class="validate form-control maxPerson">
                        <label id="maxPersonLabel{{$i+1}}">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price{{$i+1}}" min="0" name="price{{$i+1}}" type="number" value="{{$youthPrice[$i]}}" onkeyup="calculateComission('price{{$i+1}}', $(this));" step="any" class="validate form-control price">
                        <label for="price{{$i+1}}">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        @unless(Auth::guard('admin')->check())
                        <label for="price{{$i+1}}Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price{{$i+1}}Com" style="color: #ff0000;">{{$youthPriceCom[$i]}}</span>
                        </div>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                        @else
                        <button id="deleteTier{{$i+1}}" class="btn btn-primary deleteTier">X</button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            @endif
            <!--Adult-->
            <div class="categoryDiv adultDiv col-md-12" data-sort="0" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="adult">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="adultLabel">Adult</label>
                    <button data-cat="adult" class="pull-right btn btn-primary adultRemove" style="display: none;">Remove</button>
                </div>
                @for($i = 0; $i < $tierIterator; $i++)
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="adultMin" name="adultMin" type="number" class="validate form-control" value="{{$pricing->adultMin}}">
                        <label for="adultMin">Min Age</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="adultMax" name="adultMax" type="number" class="validate form-control" value="{{$pricing->adultMax}}">
                        <label for="adultMax">Max Age</label>
                        @endif
                    </div>
                    <div class="col-md-2 s2">
                        @if($i == 0)
                        <input id="ignoreadult" type="checkbox" value="1" @if(!is_null($ignoredCategories) && in_array('adult', $ignoredCategories)) checked @endif>
                        <label for="ignoreadult">Ignore this category</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson{{$i+1}}" name="minPerson{{$i+1}}" type="hidden" value="{{$minPerson[$i]}}" class="validate form-control minPerson">
                        <label id="minPersonLabel{{$i+1}}">{{$minPerson[$i]}} -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson{{$i+1}}" name="maxPerson{{$i+1}}" type="number" min="{{$minPerson[$i]}}" value="{{$maxPerson[$i]}}" class="validate form-control maxPerson">
                        <label id="maxPersonLabel{{$i+1}}">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price{{$i+1}}" min="0" name="price{{$i+1}}" type="number" value="{{$adultPrice[$i]}}" onkeyup="calculateComission('price{{$i+1}}', $(this));" step="any" class="validate form-control price">
                        <label for="price{{$i+1}}">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        @unless(Auth::guard('admin')->check())
                        <label for="price{{$i+1}}Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price{{$i+1}}Com" style="color: #ff0000;">{{$adultPriceCom[$i]}}</span>
                        </div>
                        @endunless
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                        @else
                        <button id="deleteTier{{$i+1}}" class="btn btn-primary deleteTier">X</button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            <!--Eu Citizen-->
            @if(!is_null($euCitizenPrice))
            <div class="categoryDiv euCitizenDiv col-md-12" data-sort="1" style="border-style: solid; border-color: #e0e0e0; margin-top: 10px; margin-bottom: 10px;">
                <input type="hidden" class="priceCategory" value="euCitizen">
                <div class="col-md-12 price-title" style="margin-top: 10px;">
                    <label id="euCitizenLabel">EU Citizen</label>
                    <button data-cat="euCitizen" class="pull-right btn btn-primary euCitizenRemove">Remove</button>
                </div>
                @for($i = 0; $i < $tierIterator; $i++)
                <div class="col-md-12 categoryWrapper">
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="euCitizenMin" name="euCitizenMin" type="number" class="validate form-control" value="{{$pricing->euCitizenMin}}">
                        <label for="euCitizenMin">Min Age</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <input id="euCitizenMax" name="euCitizenMax" type="number" class="validate form-control" value="{{$pricing->euCitizenMax}}">
                        <label for="euCitizenMax">Max Age</label>
                        @endif
                    </div>
                    <div class="col-md-2 s2">
                        @if($i == 0)
                        <input id="ignoreeuCitizen" type="checkbox" value="1" @if(!is_null($ignoredCategories) && in_array('euCitizen', $ignoredCategories)) checked @endif>
                        <label for="ignoreeuCitizen">Ignore this category</label>
                        @endif
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="minPerson{{$i+1}}" name="minPerson{{$i+1}}" type="hidden" value="{{$minPerson[$i]}}" class="validate form-control minPerson">
                        <label id="minPersonLabel{{$i+1}}">{{$minPerson[$i]}} -</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="maxPerson{{$i+1}}" name="maxPerson{{$i+1}}" type="number" min="{{$minPerson[$i]}}" value="{{$maxPerson[$i]}}" class="validate form-control maxPerson">
                        <label id="maxPersonLabel{{$i+1}}">Max. Person</label>
                    </div>
                    <div class="input-field col-md-1 s1">
                        <input id="price{{$i+1}}" name="price{{$i+1}}" type="number" value="{{$euCitizenPrice[$i]}}" onkeyup="calculateComission('price{{$i+1}}', $(this));" step="any" class="validate form-control price">
                        <label for="price{{$i+1}}">Price</label>
                    </div>
                    <div class="col-md-2 s2">
                        <label for="price{{$i+1}}Com">Price You Earn</label>
                        <div>
                            <span class="priceCom" id="price{{$i+1}}Com" style="color: #ff0000;">{{$euCitizenPriceCom[$i]}}</span>
                        </div>
                    </div>
                    <div class="input-field col-md-1 s1">
                        @if($i == 0)
                        <button class="btn btn-primary addTier">Set up price tiers</button>
                        @else
                        <button id="deleteTier{{$i+1}}" class="btn btn-primary deleteTier">X</button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            @endif
        </div>
        <div class="col-md-4" style="margin-bottom: 20px; margin-top: 20px;">
            @php
            $sortArr = ["youth" => 2, "infant" => 4, "child" => 3, "euCitizen" => 1, "adult" => 0];
                
            @endphp
            <select class="browser-default custom-select" name="categorySelect" id="categorySelect">
                <option selected value="">Add age group</option>
                @foreach($availableCategories as $value)
                <option value="{{$value}}" data-sort="{{$sortArr[$value]}}">{{ucfirst($value)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <input data-form="save" type="submit" class="btn btn-primary" value="Save" id="priceButton">
        </div>
    </div>
</div>


@include('panel.pricing-partials.edit-scripts')


