@include('panel-partials.head', ['page' => 'product-check'])
@include('panel-partials.header', ['page' => 'product-check'])


<div style="margin-top: 100px;" class="container-fluid">
    <div class="row pull-right" style="margin-right: 1%;">
        <a href="/" role="button" class="btn btn-default btn-lg draftInfoModalClass" data-toggle="modal">Go to Dashboard</a>
    </div>
</div>
<?php
    $attractionModel = new \App\Attraction();
    $attractionNames = $attractionModel->getAttractionNames($product->attractions);
    $editedAttractionNames = $attractionModel->getAttractionNames($editedProduct->attractions);
    $countryCodes = json_decode($product->countryCode, true);
    $phoneNumbers = json_decode($product->phoneNumber, true);
    $editedCountryCodes = json_decode($editedProduct->countryCode, true);
    $editedPhoneNumbers = json_decode($editedProduct->phoneNumber, true);
    $productFiles = json_decode($product->productFiles, true);
    $editedProductFiles = json_decode($editedProduct->productFiles, true);
?>
<input type="hidden" class="productID" value="{{$product->id}}">
<input type="hidden" class="editedProductID" value="{{$editedProduct->id}}">
<div class="container">
    <div class="col-md-12" style="margin-bottom: 300px;">
        <div class="col-md-6">
            <h3>Product</h3>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Product Title</u></span>
                <div>{{$product->title}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Short Description</u></span>
                <div>{{$product->shortDesc}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Full Description</u></span>
                <div>{!! $product->fullDesc !!}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Country</u></span>
                <div>{{$product->countryName->countries_name}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>City</u></span>
                <div>{{$product->city}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Attractions</u></span>
                <div>{{$attractionNames}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Phone Number</u></span>
                @foreach($countryCodes as $i => $countryCode)
                <div>+{{$countryCode}} {{$phoneNumbers[$i]}}</div>
                @endforeach
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Highlights</u></span>

                  @php
                    $highlights = json_decode($product->highlights, true);


                @endphp
             
             @if($highlights)
                @foreach ($highlights as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif 

                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Included</u></span>

                    @php
                    $included = json_decode($product->included, true);


                @endphp
             
             @if($included)
                @foreach ($included as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif 


                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Not Included</u></span>

                @php
                    $notIncluded = json_decode($product->notIncluded, true);


                @endphp
             
             @if($notIncluded)
                @foreach ($notIncluded as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif 


                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Know Before You Go</u></span>

                @php
                $knowBeforeYouGo = json_decode($product->knowBeforeYouGo, true);


                @endphp
             
             @if($knowBeforeYouGo)
                @foreach ($knowBeforeYouGo as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif 


               
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Cancel Policy</u></span>
                <div>{{$product->cancelPolicy}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Category</u></span>
                <div>{{$product->category}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Tags</u></span>

              @php
                    $tags = json_decode($product->tags, true);


                @endphp
             
             @if($tags)
                @foreach ($tags as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif



                
            </div>
            @if (!is_null($product->productFiles))
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Product Files</u></span>
                @foreach($productFiles as $file)
                <div>{{$file}}</div>
                @endforeach
            </div>
            @endif
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Pictures</u></span>
                @foreach($product->productGalleries()->get() as $image)
                    <div style="margin-bottom: 10px;">
                        <img src="{{Storage::disk('s3')->url('product-images-xs/' . $image->src)}}" />
                    </div>
                @endforeach
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Options</u></span>
                @foreach($product->options()->get() as $option)
                    <div style="margin-bottom: 10px;">{{$option->title}}</div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <h3>Edited Product</h3>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Product Title</u></span>
                <div>{{$editedProduct->title}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Short Description</u></span>
                <div>{{$editedProduct->shortDesc}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Full Description</u></span>
                <div>{!! $editedProduct->fullDesc !!}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Country</u></span>
                <div>{{$editedProduct->countryName->countries_name}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>City</u></span>
                <div>{{$editedProduct->city}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Attractions</u></span>
                <div>{{$editedAttractionNames}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Phone Number</u></span>
                @foreach($editedCountryCodes as $i => $countryCode)
                    <div>+{{$countryCode}} {{$editedPhoneNumbers[$i]}}</div>
                @endforeach
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Highlights</u></span>
                @php
                    $highlights = json_decode($editedProduct->highlights, true);


                @endphp
             
             @if($highlights)
                @foreach ($highlights as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif  
                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Included</u></span>


                 @php
                    $included = json_decode($editedProduct->included, true);


                @endphp
                   @if($included)
                  @foreach ($included as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
                @endif


                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Not Included</u></span>


                @php
                    $notIncluded = json_decode($editedProduct->notIncluded, true);


                @endphp
                 @if($notIncluded)
                  @foreach ($notIncluded as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
                @endif
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Know Before You Go</u></span>

                  @php
                    $knowBeforeYouGo = json_decode($editedProduct->knowBeforeYouGo, true);


                @endphp 
               @if($knowBeforeYouGo)
                  @foreach ($knowBeforeYouGo as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
              @endif  


                
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Cancel Policy</u></span>
                <div>{{$editedProduct->cancelPolicy}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Category</u></span>
                <div>{{$editedProduct->category}}</div>
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Tags</u></span>

                    @php
                    $tags = json_decode($editedProduct->tags, true);


                @endphp
                  @if($tags)
                  @foreach ($tags as $key => $value)
                   <div>{{ $value["value"] }}</div>
                @endforeach
                @endif


                
            </div>
            @if (!is_null($editedProduct->productFiles))
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Product Files</u></span>
                @foreach($editedProductFiles as $file)
                    <div>{{$file}}</div>
                @endforeach
            </div>
            @endif
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Pictures</u></span>
                @foreach($editedProduct->productGalleries()->get() as $image)
                    <div style="margin-bottom: 10px;">
                        <img src="{{Storage::disk('s3')->url('product-images-xs/' . $image->src)}}" />
                    </div>
                @endforeach
            </div>
            <div class="col-md-12" style="margin-top: 20px;">
                <span><u>Options</u></span>
                @foreach($editedProduct->options()->get() as $option)
                    <div style="margin-bottom: 10px;">{{$option->title}}</div>
                @endforeach
            </div>
            <div class="col-md-12">
                <button class="btn btn-primary" id="publishButton">Publish</button>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'product-check'])


