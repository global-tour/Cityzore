@include('panel.product-partials.head', ['page' => 'product-edit'])
@include('panel.product-partials.header', ['page' => 'product-edit'])
<style>
    .justify-content-end {
        -webkit-box-pack: end !important;
        -ms-flex-pack: end !important;
        justify-content: flex-end !important;
    }

    .cropped-foto {
        border-radius: 30px;
        overflow: hidden;
        width: 120px;
        height: 120px;
        position: relative;
        display: block;
        z-index: 10;
    }

    .dz-image img {
        height: 120px;
        width: 192px;

    }

    .delete-image {
        margin-top: 4px;
        margin-right: 6px;
    }

    .home-image {
        margin-top: 4px;

    }

    .check-ok {
        border-radius: 50%;
        padding: 5px 10px;
        background-color: green;
        color: white;
        font-size: 12px;
        cursor: pointer;
    }

    .dropzone .dz-preview.dz-image-preview {
        text-align: center !important;
    }
</style>

@if(auth()->guard('supplier')->check())
    <input hidden value="supplier" id="userGuard">
@endif
<div style="margin-top: 100px;" class="container-fluid">
    <div class="row pull-right" style="margin-right: 1%;">
        <a href="/" role="button" class="btn btn-default btn-lg draftInfoModalClass" data-toggle="modal">Go to
            Dashboard</a>
    </div>
    <div class="">
        <h3>Edit {{$product->title}}</h3>
    </div>
</div>
<div class="col-lg-12">
    <div class="container" style="width: 80%">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle step1abutton">1</a>
                    <p class="hidden-xs">Product Details</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle step2abutton">2</a>
                    <p class="hidden-xs">Highlights</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle step3abutton">3</a>
                    <p class="hidden-xs">Pictures</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-4" type="button" class="btn btn-default btn-circle step4abutton">4</a>
                    <p class="hidden-xs">Options</p>
                </div>
            </div>
        </div>
        <form id="productEditForm"
              action="{{url('/product/'.(empty($product->copyOf) ? $product->id : $product->copyOf).'/edit')}}"
              enctype="multipart/form-data" method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="product_id" class="productId" value="{{$product->id}}">
            <input type="hidden" name="page_id" class="pageID" value="{{$pageID}}">
            <input type="hidden" name="productIsDraft" class="productIsDraft" value="{{$product->isDraft}}">
            <input type="hidden" name="isDraft" class="isDraft" value="0">
            <input type="hidden" name="whichStep" class="whichStep" value="0">
            <input type="hidden" name="redirectToDashboard" class="redirectToDashboard" value="0">
            <input type="hidden" name="userType" class="userType" value="{{$userType}}">
            <input type="hidden" name="comission" class="comission" value="{{$comission}}">
            <input type="hidden" name="optCount" class="optCount" value="0">
            <input type="hidden" name="userId" class="userId" value="{{$ownerId}}">
            <input type="hidden" name="whichPage" class="whichPage" value="product">
            <input type="hidden" name="oldTitle" class="oldTitle" value="{{$product->title}}">
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3 style="text-align: center;"> Product Details</h3>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Product Title</span><br>
                                    <span style="color:blue;font-size:13px!important;">Example :Paris: Louvre Skip-the-Line Ticket</span><br>
                                    <input id="title" name="title" type="text" class="validate form-control"
                                           value="{{$product->title}}" style="margin-top:2%; font-size:15px;">
                                    <span class="titleErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;font-size:15px!important;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Short Description</span><br>
                                    <span style="color:blue;font-size:13px!important;">This is the customer’s first introduction to your activity. It should outline what you’ll do and make customers want to learn more.
                                        Write 2 or 3 short sentences that summarize your activity in English. To get customers excited about what they’ll do,
                                        use action words like explore, see, or enjoy. Don’t write “we,” “our,” or your company’s name.
                                    </span>
                                    <input id="shortDesc" name="shortDesc" type="text" class="validate form-control"
                                           value="{{$product->shortDesc}}" style="margin-top:2%; font-size:15px;">
                                    <span class="shortDescErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;font-size:15px!important;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Full Description</span> <br>
                                    <span style="color:blue;font-size:13px!important;">In English, describe what the customer will do and see during your activity.
                                                What will they see first? Then what? How does the activity end?
                                                Don’t write “we,” “our,” your company’s name, or copy and paste text from an existing website.
                                    </span>
                                    <textarea name="fullDesc" id="fullDesc" class="materialize-textarea form-control">
                                         {!! html_entity_decode($product->fullDesc) !!}
                                        </textarea>
                                    <span class="fullDescErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;font-size:15px!important;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-8" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;padding-bottom: 5%;">Select Country</span>
                                    <br>
                                    <select class="select2 browser-default custom-select" name="location" id="location"
                                            style="width:100%!important;">
                                        @if (!is_null($product->country) && $product->country != 0)
                                            <option selected
                                                    value="{{$product->country}}">{{$product->countryName->countries_name}}</option>
                                        @endif
                                        @foreach($country as $c)
                                            <option value="{{$c->id}}">{{$c->countries_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="locationErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-8" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;padding-bottom: 5%;">Select City</span> <br>
                                    <select class="select2 browser-default custom-select" name="cities" id="cities"
                                            style="width:100%!important;">
                                        <option value="{{$product->city}}">{{$product->city}}</option>
                                    </select>
                                    <span class="locationErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;padding-bottom: 5%;">Select Attraction</span>
                                    <br>
                                    <select class="mdb-select" multiple searchable="Search here.." name="attractions[]"
                                            id="attractions">
                                        <option value="" disabled selected>Choose product attraction</option>
                                        @foreach($attractions as $attraction)
                                            <option value="{{$attraction->id}}" style="font-size: 15px;"
                                                    @foreach($productAttractions as $p) @if($attraction->id == $p) selected="selected" @endif @endforeach>{{$attraction->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="attractionErrorSpan col-md-12"
                                          style="display: none!important; color: #ff0000;">This field is required.</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="phoneNumberCount"
                               value="{{count(json_decode($product->countryCode, true))}}">
                        <div class="phoneNumberWrapper col-md-12"
                             style="border: 1px solid #e0e0e0;margin-top:15px;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                @foreach(json_decode($product->countryCode, true) as $i => $cCode)
                                    @if (count($countryIsoCodes) > 0)
                                        <input type="hidden" id="countryIsoCode{{$i+1}}"
                                               value="{{$countryIsoCodes[$i]}}">
                                    @endif
                                    <div class="form-group">
                                        <span style="font-size: 20px!important;padding-bottom: 5%;">Phone Number</span>
                                        <br>
                                        @if ($i == 0)
                                            <button data-value="{{count(json_decode($product->countryCode, true))}}"
                                                    id="addPhoneNumber" class="col-md-3 btn btn-primary pull-right"
                                                    type="button" style="margin-bottom:30px;padding: 0px 1rem;">Add New
                                                Phone Number
                                            </button>
                                        @else
                                            <button class="col-md-3 deletePhoneNumber btn btn-primary pull-right"
                                                    type="button" style="margin-bottom:30px;">Delete
                                            </button>
                                        @endif
                                        <div class="input-field col-md-9">
                                            <input type="hidden" name="countryCode[]" id="countryCode{{$i+1}}"
                                                   value="{{$cCode}}">
                                            <input autocomplete="off" type="tel" id="phoneNumber{{$i+1}}"
                                                   name="phoneNumber[]"
                                                   value="{{json_decode($product->phoneNumber, true)[$i]}}">
                                            <span id="valid-msg{{$i+1}}" class="hide">✓ Valid</span>
                                            <span id="error-msg{{$i+1}}" class="hide"></span>
                                        </div>
                                        <span class="countryCodeAndPhoneNumberErrorSpan col-md-12"
                                              style="display: none!important; color: #ff0000;">These fields are required.</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button style="margin-left: 10px;" class="btn btn-success btn-lg pull-right" type="submit">
                            Update
                        </button>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px;">Next
                        </button>
                        <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px; margin-right: 10px; display: none;">Previous
                        </button>
                    </div>
                </div>
            </div>

            <?php
            $highlights = implode('{}', explode('|', $product->highlights));
            $included = implode('{}', explode('|', $product->included));
            $notIncluded = implode('{}', explode('|', $product->notIncluded));
            $knowBeforeYouGo = implode('{}', explode('|', $product->knowBeforeYouGo));
            $tags = implode('{}', explode('|', $product->tags));
            ?>
            <div style="display: none!important;" class="row setup-content" id="step-2">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3 style="text-align: center;"> Highlights</h3>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Highlights</span> <br>
                                    <span style="color:blue;font-size:13px!important;">Example:Marvel at the centuries-old frescoes of the Sistine Chapel</span>
                                    <?php
                                    $highlights = implode('{}', explode('|', $product->highlights));
                                    $included = implode('{}', explode('|', $product->included));
                                    $notIncluded = implode('{}', explode('|', $product->notIncluded));
                                    $knowBeforeYouGo = implode('{}', explode('|', $product->knowBeforeYouGo));
                                    $tags = implode('{}', explode('|', $product->tags));

                                    ?>
                                    <input name="highlights" id="highlights" value="{{$highlights}}" type="text"
                                           class="tags form-control" style="margin-top: 10px;"/>
                                    <div id="suggestions-container" style=" float: left;margin: 10px;"></div>
                                </div>
                            </div>
                            <span class="highlightsErrorSpan col-md-12"
                                  style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Included</span> <br>
                                    <span style="color:blue;font-size:13px!important;">Example :Hotel pickup and drop-off</span>
                                    <input name="included" id="included" value="{{$included}}" type="text"
                                           class="tags form-control"/>
                                    <div id="suggestions-container" style=" float: left;margin: 10px;"></div>
                                </div>
                            </div>
                            <span class="includedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Not Included</span> <br>
                                    <span style="color:blue;font-size:13px!important;">Example :Food and drinks</span>
                                    <input name="notIncluded" id="notincluded" value="{{$notIncluded}}" type="text"
                                           class="tags form-control"/>
                                    <div id="suggestions-container" style=" float: left;margin: 10px;"></div>
                                </div>
                            </div>
                            <span class="notIncludedErrorSpan col-md-12"
                                  style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Know Before You Go</span> <br>
                                    <span style="color:blue;font-size:13px!important;">Example :This tour is not recommended for people with limited mobility.</span>
                                    <input name="knowBeforeYouGo" id="beforeyougo" value="{{$knowBeforeYouGo}}"
                                           type="text" class="tags form-control"/>
                                    <div id="suggestions-container" style=" float: left;margin: 10px;"></div>
                                </div>
                            </div>
                            <span class="beforeyougoErrorSpan col-md-12"
                                  style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="input-field col s12" style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Cancel Policy</span> <br>
                                    <input name="cancelPolicy" id="cancelPolicy" type="text"
                                           class="validate form-control valid" value="{{$product->cancelPolicy}}"
                                           style="font-size:15px;"/>
                                </div>
                            </div>
                            <span class="cancelPolicyErrorSpan col-md-12"
                                  style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="input-field col s12" style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Product Category</span> <br>
                                    <select class="browser-default custom-select" name="category" id="categoryId">
                                        <option selected value="option_select">Choose a Category</option>
                                        @foreach($category as $categories)
                                            <option
                                                value="{{ $categories->categoryName }}" {{$product->category == $categories->categoryName  ? 'selected' : ''}}>{{ $categories->categoryName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <span class="categoryIdErrorSpan col-md-12"
                                  style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="margin-top: 1%;border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Product Tags</span> <br>
                                    <span style="color:blue;font-size:13px!important;">Example :Eiffel Tower, Seine River Cruise</span>
                                    <input name="tags" id="tags_1" value="{{$tags}}" type="text"
                                           class="tags form-control col s12"
                                           placeholder="River Cruise, Eiffel Tower, Arc de Triomphe"/>
                                    <div id="suggestions-container"
                                         style="position: relative; float: left;margin: 10px;"></div>
                                </div>
                            </div>
                            <span class="tags_1ErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="col-md-12 col-sm-12 col-xs-12"
                                 style="margin-top: 1%;border: 1px solid #e0e0e0;margin-bottom: 15px;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Product Files</span> <br>
                                    @if (!is_null($product->productFiles))
                                        @foreach(json_decode($product->productFiles, true) as $file)
                                            <span class="productFileSpan" data-name="{{$file}}"
                                                  style="background-color: #0e76a8; color: #ffffff; padding: 5px; cursor: pointer;">{{$file}} x</span>
                                        @endforeach
                                    @endif
                                    <input style="margin-top: 10px;" type="file" name="product_files[]"
                                           id="productFiles" multiple>
                                </div>
                            </div>
                        </div>
                        <button style="margin-left: 10px;"
                                class="btn btn-danger btn-lg pull-right saveAsDraftBeforeSubmitButton">Save as Draft
                        </button>
                        <button style="margin-left: 10px;" class="btn btn-success btn-lg pull-right" type="submit">
                            Update
                        </button>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px;">Next
                        </button>
                        <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px; margin-right: 10px;">Previous
                        </button>
                    </div>
                </div>
            </div>
            <div style="display: none!important;" class="row setup-content" id="step-3">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3 style="text-align: center;"> Pictures</h3>
                        <div style="margin-top: 20px">
                            <ul class="nav nav-tabs " id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="new-system-btn" data-toggle="tab" href="#profile"
                                       role="tab" aria-controls="profile" aria-selected="true">Resize</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="old-system-btn" data-toggle="tab" href="#home" role="tab"
                                       aria-controls="home" aria-selected="false">Upload</a>
                                </li>

                                <li class="nav-item">

                                    <a class="nav-link galleryModal" id="profile-tab" data-toggle="modal"
                                       href="#galleryModal" role="tab" aria-controls="profile" aria-selected="false">Select
                                        Image(s) from Gallery</a>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group new-system" style="margin-top: 10px">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_content">
                                        <div class="row" align="center" style="margin: 4px">
                                            <a href="#" class="btn btn-primary add_photo_button">Yeni Fotoğraf Ekle</a>
                                            {{--                                            <button type="button" class="btn btn-primary add_photo_button">Yeni Fotoğraf Ekle</button>--}}
                                            <input type="hidden" id="upload-image-input" data-toggle="modal"
                                                   data-target="#uploadimageModal1">
                                            <input type="hidden" name="foto-slug" id="foto-slug"
                                                   value="foto slug degeri">
                                        </div>
                                        <span hidden style="font-size: 16px"
                                              class="col-md-12 col-sm-12 label label-danger"
                                              id="pictureSizeErrorSpan">Your photo size have to be smaller than 8 MB.</span>
                                        <div action="/gallery/uploadPhoto/product" class="dropzone1"
                                             id="my-awesome-dropzone11">
                                            @foreach($productImages as $productImg)
                                                <div
                                                    class="dz-preview dz-processing dz-image-preview dz-success dz-complete"
                                                    align="center">
                                                    <div class="dz-image"><img data-dz-thumbnail="" alt="{{$productImg->alt}}"
                                                                               src="{{Storage::disk('s3')->url('product-images-xs/' . $productImg->src)}}">
                                                    </div>
                                                    <div class="dz-success-mark">
                                                        <svg width="54px" height="54px" viewBox="0 0 54 54"
                                                             version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                             xmlns:xlink="http://www.w3.org/1999/xlink"
                                                             xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                                            <title>Check</title>
                                                            <defs></defs>
                                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                               fill-rule="evenodd" sketch:type="MSPage">
                                                                <path
                                                                    d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                                    id="Oval-2" stroke-opacity="0.198794158"
                                                                    stroke="#747474" fill-opacity="0.816519475"
                                                                    fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                                                            </g>
                                                        </svg>
                                                    </div>
{{--                                                    <a type="button" class="delete-image btn-danger btn-xs" href="javascript:void(0)"  value="1"  id="imageId-{{$productImg->id}}"><i--}}
{{--                                                            class="fa fa-trash-o" style="padding: 5px;margin: 5px"></i></a>--}}
                                                    <button onclick="return false" class="delete-image btn-danger btn-xs" value="1"
                                                            name="sari-antalya1" id="imageId-{{$productImg->id}}"><i
                                                            class="fa fa-trash-o"></i></button>
{{--                                                    <a href="javascript:void(0)" class="home-image {{$product->coverPhoto==$productImg->id ? 'btn-success':'btn-primary'}} btn-xs" value="0" val="{{$product->coverPhoto==$productImg->id ? 1:0}}"--}}
{{--                                                       name="sari-antalya1" id="homeId-{{$productImg->id}}" ><i--}}
{{--                                                            class="fa fa-home"  style="padding: 5px;margin: 5px"></i></a>--}}
                                                    <button onclick="return false" class="home-image {{$product->coverPhoto==$productImg->id ? 'btn-success':'btn-default'}} btn-xs" value="0"
                                                            name="sari-antalya1" id="homeId-{{$productImg->id}}"><i
                                                            class="fa fa-home"></i></button>
                                                </div>
                                            @endforeach
                                        </div>

                                        <br/>
                                        <br/>
                                        {{--                                    <span class="col-md-12 col-sm-12" style="background-color: #17a2b8; color: #fff;"--}}
                                        {{--                                          id="coverPhotoNameSpan">No Cover Photo Selected</span>--}}
                                        {{--                                    <span class="col-md-3 col-sm-3" id="dropzoneErrorSpan"--}}
                                        {{--                                          style="color: #FF0000; display: none;">You must at least upload one image and set one cover photo.</span>--}}
                                        <br/>
                                        <br/>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group old-system" style="margin-top: 10px">
                            <div class="x_panel">
                                <div class="x_content">
                                    <p>Drag multiple files to the box below for multi upload or click to select
                                        files.</p>
                                    <span hidden style="font-size: 16px" class="col-md-12 col-sm-12 label label-danger"
                                          id="pictureSizeErrorSpan">Your photo size have to be smaller than 8 MB.</span>
                                    <div action="/gallery/uploadPhoto/product" class="dropzone col-lg-12"
                                         id="my-awesome-dropzone" style="margin-bottom: 3%;">
                                        @foreach($productImages as $productImg)
                                            <div
                                                class="dz-preview dz-processing dz-image-preview dz-success dz-complete">
                                                <div class="dz-image"><img data-dz-thumbnail=""
                                                                           alt="{{$productImg->alt}}"
                                                                           src="{{Storage::disk('s3')->url('product-images-xs/' . $productImg->src)}}">
                                                </div>
                                                <div class="dz-details">
                                                    <div class="dz-size"><span
                                                            data-dz-size="">ID:<strong>{{$productImg->id}}</strong></span>
                                                    </div>
                                                    <div class="dz-filename"><span
                                                            data-dz-name="">{{$productImg->src}}</span>
                                                    </div>
                                                </div>
                                                <div class="dz-progress"><span class="dz-upload"
                                                                               data-dz-uploadprogress=""
                                                                               style="width: 100%;"></span></div>
                                                <div class="dz-error-message"><span data-dz-errormessage=""></span>
                                                </div>
                                                <div class="dz-success-mark">
                                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"><title>
                                                            Check</title>
                                                        <defs></defs>
                                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                           fill-rule="evenodd" sketch:type="MSPage">
                                                            <path
                                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                                id="Oval-2" stroke-opacity="0.198794158"
                                                                stroke="#747474" fill-opacity="0.816519475"
                                                                fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="dz-error-mark">
                                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"><title>
                                                            Error</title>
                                                        <defs></defs>
                                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                           fill-rule="evenodd" sketch:type="MSPage">
                                                            <g id="Check-+-Oval-2" sketch:type="MSLayerGroup"
                                                               stroke="#747474" stroke-opacity="0.198794158"
                                                               fill="#FFFFFF" fill-opacity="0.816519475">
                                                                <path
                                                                    d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                                    id="Oval-2" sketch:type="MSShapeGroup"></path>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <a class="dz-remove" href="javascript:undefined;" data-dz-remove=""
                                                   data-id="{{$productImg->id}}">Remove file</a><a
                                                    style="cursor:pointer;"
                                                    class="dz-cover home-image-dz setImageAsCoverPhoto {{$product->coverPhoto==$productImg->id ? 'check-ok':''}}"
                                                    val="0" value="{{$product->coverPhoto==$productImg->id ? 1:0}}" href="javascript:undefined;"
                                                    data-dz-cover="" data-id="{{$productImg->id}}"
                                                    data-name="{{$productImg->src}}">{{$product->coverPhoto==$productImg->id ? '✔':'Set as Cover Photo'}}</a></div>
                                        @endforeach
                                        {{--                                        @foreach($productImages as $productImg)--}}
                                        {{--                                            <div class="dz-image"--}}
                                        {{--                                                 style="position: relative;display: inline-block;vertical-align: top;margin: 16px;min-height: 100px;">--}}
                                        {{--                                                @if($productImg->isCoverPhoto == 0)--}}
                                        {{--                                                    <img--}}
                                        {{--                                                        src="{{Storage::disk('s3')->url('product-images-xs/' . $productImg->src)}}"--}}
                                        {{--                                                        style="border-radius: 20px; overflow: hidden; width: 120px; height: 120px; position: relative; display: block; z-index: 10;"/>--}}
                                        {{--                                                @else--}}
                                        {{--                                                    <img--}}
                                        {{--                                                        src="{{Storage::disk('s3')->url('product-images-xs/' . $productImg->src)}}"--}}
                                        {{--                                                        style="border:5px solid #0e76a8;border-radius: 20px; overflow: hidden; width: 120px; height: 120px; display: block; z-index: 10;"/>--}}
                                        {{--                                                @endif--}}
                                        {{--                                                <div class="text-center">--}}
                                        {{--                                                    <span>Image ID: {{$productImg->id}}</span>--}}
                                        {{--                                                </div>--}}
                                        {{--                                                <div class="text-center">--}}
                                        {{--                                                    <a data-name="{{$productImg->src}}" data-id="{{$productImg->id}}"--}}
                                        {{--                                                       style="margin-top:5px;cursor:pointer;"--}}
                                        {{--                                                       class="dz-remove removeImage" href="javascript:void(0);"--}}
                                        {{--                                                       data-dz-remove="">Remove file</a>--}}
                                        {{--                                                </div>--}}
                                        {{--                                                <div style="margin-top: 5px" class="text-center">--}}
                                        {{--                                                    @if($productImg->id != $product->coverPhoto)--}}
                                        {{--                                                        <a data-name="{{$productImg->src}}"--}}
                                        {{--                                                           data-id="{{$productImg->id}}" style="cursor:pointer;"--}}
                                        {{--                                                           class="dz-cover setImageAsCoverPhoto"--}}
                                        {{--                                                           href="javascript:void(0);" data-dz-cover="">Set as Cover--}}
                                        {{--                                                            Photo</a>--}}
                                        {{--                                                    @else--}}
                                        {{--                                                        <a data-name="{{$productImg->src}}"--}}
                                        {{--                                                           data-id="{{$productImg->id}}"--}}
                                        {{--                                                           style="border-radius:50%;padding:5px 10px;background-color:rgba(14,118,168,0.8);color:white;font-size:12px;cursor:pointer;"--}}
                                        {{--                                                           class="dz-cover setImageAsCoverPhoto"--}}
                                        {{--                                                           href="javascript:void(0);" data-dz-cover="">&#10004;</a>--}}
                                        {{--                                                    @endif--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        @endforeach--}}
                                    </div>
                                    <br/>
                                    <span class="col-md-12 col-sm-12" style="color: black;"
                                          id="coverPhotoNameSpan"></span>
                                    {{--                                    <span class="col-md-3 col-sm-3" id="dropzoneErrorSpan"--}}
                                    {{--                                          style="color: #FF0000; display: none;">You must at least upload one image and set one cover photo.</span>--}}
                                    <br/>
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                        </div>
                        <button style="margin-left: 10px;"
                                class="btn btn-danger btn-lg pull-right saveAsDraftBeforeSubmitButton">Save as Draft
                        </button>
                        <button style="margin-left: 10px;" class="btn btn-success btn-lg pull-right" type="submit">
                            Update
                        </button>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px;">Next
                        </button>
                        <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px; margin-right: 10px;">Previous
                        </button>
                    </div>
                </div>
            </div>
            <div style="display: none!important;" class="row setup-content" id="step-4">
                <div class="col-md-12">
                    <h3 style="text-align: center;">Options</h3>
                    <div class="row" style="border: 1px solid #e0e0e0;">
                        <div style="padding: 30px;">
                            <a href="#optionModal" role="button" class="btn btn-default btn-lg" data-toggle="modal"
                               style="margin-bottom: 20px; margin-top: 20px;">Create a New Option</a>
                            <span style="font-size: 18px!important;">or Select Existing Options:</span>
                            <style>
                                .select2-container {
                                    width: 100% !important;
                                }
                            </style>
                            <select class="s3 browser-default custom-select options" name="options[]" id="opt_select">
                                <option value="">Add an option</option>
                                @foreach($options as $option)
                                    <option value="{{$option->id}}">{{$option->title}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary addExistingOptionButton" style="margin-top: 2%;">Add</button>
                        </div>
                    </div>
                    <input type="hidden" class="productOptionCount" value="{{count($productOptions)}}">
{{--                    <input type="hidden" id="coverPhotoID" value="{{$product->coverPhoto}}">--}}
                    <div class="row editOptionsWrapper" style="margin-top: 1%;border: 1px solid #e0e0e0;">
                        <h4 style="margin-left: 15px;margin-top: 2%;margin-bottom: 2%;">Existing Options</h4>
                        <!-- New Option Part -->
                        <hr style="border-top: 1px solid #e0e0e0;">
                        <div class="navbar" style="margin-left: 15px;">
                            <div class="navbar-inner">
                                <ul class="nav nav-pills optionNavs">
                                    <?php
                                    $i = 1;
                                    $productOptionsCount = count($productOptions);
                                    foreach ($productOptions as $productOpt) {
                                        echo '<li ';
                                        if ($i == 1) {
                                            echo 'class="active"';
                                        }
                                        echo '><a class="optionPills" id="option' . $i . 'Tab" data-id="option' . $i . '" href="#option' . $i . '" data-toggle="tab" data-option-id="' . $productOpt['id'] . '">Option ' . $i . '</a></li>';
                                        $i++;
                                    }
                                    ?>
                                    <input type="hidden" class="selectedOption"
                                           value="@if(count($productOptions) > 0){{$productOptions[0]['id']}}@endif">
                                    <input type="hidden" class="selectedOptionStep" value="1">
                                    <input type="hidden" class="productOptionIds" name="productOptionIds"
                                           value="{{$product->options}}">
                                    <input type="hidden" class="saveOptionPart" name="saveOptionPart" value="1">
                                </ul>
                            </div>
                        </div>
                        <hr style="border-top:1px solid #e0e0e0;">
                        <div class="tab-content">
                            <div class="tab-pane fade in active editableOptionsTabPane" id="option1">
                                <div class="col-md-12">
                                    <ul class="nav nav-pills nav-stacked col-md-3">
                                        <li class="active"><a id="verticalStep1A" href="#verticalStep1"
                                                              data-toggle="pill">Title & Description</a></li>
                                        <li><a id="verticalStep2A" href="#verticalStep2" data-toggle="pill">Min & Max
                                                Person Count</a></li>
                                        <li><a id="verticalStep3A" href="#verticalStep3" data-toggle="pill">Cut of Time
                                                & Tour Duration</a></li>
                                        <li><a id="verticalStep4A" href="#verticalStep4" data-toggle="pill">Pricing & Ticket Type</a>
                                        </li>
                                        <li><a id="verticalStep5A" href="#verticalStep5"
                                               data-toggle="pill">Availability</a></li>
                                        <li><a id="verticalStep6A" href="#verticalStep6" data-toggle="pill">Meeting
                                                Point</a></li>
                                        <li><a id="verticalStep7A" href="#verticalStep7" data-toggle="pill">Contact
                                                Information Fields</a></li>
                                    </ul>
                                    <div class="col-md-9 col-xs-12 tab-content" style="border: 1px solid #e0e0e0;">
                                        <div class="tab-pane fade in active" id="verticalStep1">
                                            <div class="verticalStep1Form">
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input id="editableOptTitle" name="editableOptTitle" type="text"
                                                               class="validate form-control"
                                                               value="@if(count($productOptions) > 0){{$productOptions[0]['title']}}@endif">
                                                        <label for="editableOptTitle">Title</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <textarea id="editableOptDesc" name="editableOptDesc"
                                                                  type="text"
                                                                  class="materialize-textarea form-control">@if(count($productOptions) > 0){{$productOptions[0]['description']}}@endif</textarea>
                                                        <label for="editableOptDesc">Description</label>
                                                    </div>
                                                </div>
                                                <?php
                                                    if(count($productOptions) > 0) {
                                                        $included = implode('{}', explode('|', $productOptions[0]['included']));
                                                        $notIncluded = implode('{}', explode('|', $productOptions[0]['notIncluded']));
                                                        $knowBeforeYouGo = implode('{}', explode('|', $productOptions[0]['knowBeforeYouGo']));
                                                    }

                                                ?>
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input name="editableIncluded" id="editableIncluded" value="{{$included}}" type="text" class="tags form-control"/>
                                                        <label for="editableIncluded">What's Included</label>

                                                        <a id="includedcollapsetrigger" data-toggle="collapse" href="#includedcollapse" aria-expanded="false" aria-controls="includedcollapse">
                                                            Copy Paste Operation
                                                        </a>

                                                        <div class="collapse" id="includedcollapse">
                                                            <div class="form-group">
                                                                <span>Seperator: ⚈</span>
                                                                <textarea class="form-control" id="includedarea" rows="5"></textarea>
                                                            </div>
                                                            <button id="includedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input name="editableNotIncluded" id="editableNotIncluded" value="{{$notIncluded}}" type="text" class="tags form-control"/>
                                                        <label for="editableNotIncluded">What's Not Included</label>

                                                        <a id="notincludedcollapsetrigger" data-toggle="collapse" href="#notincludedcollapse" aria-expanded="false" aria-controls="notincludedcollapse">
                                                            Copy Paste Operation
                                                        </a>

                                                        <div class="collapse" id="notincludedcollapse">
                                                            <div class="form-group">
                                                                <span>Seperator: ⚈</span>
                                                                <textarea class="form-control" id="notincludedarea" rows="5"></textarea>
                                                            </div>
                                                            <button id="notincludedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input name="editableKnowBeforeYouGo" id="editableKnowBeforeYouGo" value="{{$knowBeforeYouGo}}" type="text" class="tags form-control"/>
                                                        <label for="editableKnowBeforeYouGo">Know Before You Go</label>

                                                        <a id="beforeyougocollapsetrigger" data-toggle="collapse" href="#beforeyougocollapse" aria-expanded="false" aria-controls="beforeyougocollapse">
                                                            Copy Paste Operation
                                                        </a>

                                                        <div class="collapse" id="beforeyougocollapse">
                                                            <div class="form-group">
                                                                <span>Seperator: ⚈</span>
                                                                <textarea class="form-control" id="beforeyougoarea" rows="5"></textarea>
                                                            </div>
                                                            <button id="beforeyougoprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="verticalStep2">
                                            <div class="verticalStep2Form">
                                                <input type="hidden" name="editable_skip_the_line"
                                                       value="{{!empty($productOptions[0]['isSkipTheLine']) ? $productOptions[0]['isSkipTheLine'] : ''}}">
                                                <input type="hidden" name="editable_is_free_cancellation"
                                                       value="{{!empty($productOptions[0]['isFreeCancellation']) ? $productOptions[0]['isFreeCancellation'] : ''}}">
                                                <input type="hidden" name="editable_guide_information"
                                                       value="{{!empty($productOptions[0]['guideInformation']) ? $productOptions[0]['guideInformation'] : ''}}">
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input id="editableMinPerson" name="editableMinPerson"
                                                               type="number" class="validate form-control" min="0"
                                                               value="0">
                                                        <label for="editableMinPerson">Min. Person Count</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-field col s12">
                                                        <input id="editableMaxPerson" name="editableMaxPerson"
                                                               type="number" class="validate form-control" min="0"
                                                               value="0">
                                                        <label for="editableMaxPerson">Max. Person Count</label>
                                                    </div>
                                                </div>


                                                <br>
                                                <hr>

                                                <div class="row" style="margin-bottom: 50px;">


                                                    <div class="form-group">


                                                        <div class="input-field col s12">

                                                            <div class="switch mar-bot-20">

                                                                <label>
                                                                    <span
                                                                        style="font-size: 12px; color: #000; padding: 0 0 0 30px;">Free Cancellation ?</span>
                                                                    <input type="checkbox"
                                                                           @if(!empty($productOptions[0]['isFreeCancellation']) && $productOptions[0]['isFreeCancellation'] == 1) checked
                                                                           @endif id="editable-is-free-cancellation">
                                                                    <span class="lever"></span>
                                                                </label>
                                                            </div>

                                                        </div>


                                                    </div>


                                                </div>


                                                <div class="row" style="margin-bottom: 50px;">


                                                    <div class="form-group">


                                                        <div class="input-field col s12">

                                                            <div class="switch mar-bot-20">

                                                                <label>
                                                                    <span
                                                                        style="font-size: 12px; color: #000; padding: 0 0 0 30px;">Skip The Line ?</span>
                                                                    <input type="checkbox"
                                                                           @if(!empty($productOptions[0]['isSkipTheLine']) && $productOptions[0]['isSkipTheLine'] == 1) checked
                                                                           @endif id="editable-skip-the-line">
                                                                    <span class="lever"></span>
                                                                </label>
                                                            </div>

                                                        </div>


                                                    </div>


                                                </div>

                                                <div class="row" style="padding: 0 30px;">
                                                    <div class="form-group">

                                                        <input type="checkbox"
                                                               class="filled-in editable_guide_information"
                                                               @if(!empty($productOptions[0]['guideInformation']) && in_array("Live Guide", json_decode($productOptions[0]['guideInformation'], true))) checked
                                                               @endif  id="editable-live-guide" value="Live Guide"/>
                                                        <label for="editable-live-guide">Live Guide</label>
                                                    </div>

                                                    <div class="form-group">

                                                        <input type="checkbox"
                                                               class="filled-in editable_guide_information"
                                                               @if(!empty($productOptions[0]['guideInformation']) && in_array("Audio Guide", json_decode($productOptions[0]['guideInformation'], true))) checked
                                                               @endif id="editable-audio-guide" value="Audio Guide"/>
                                                        <label for="editable-audio-guide">Audio Guide</label>
                                                    </div>

                                                    <div class="form-group">

                                                       <input type="checkbox" class="filled-in" id="editable-mobile-barcode" value="Mobile Barcode" />
                                                       <label for="editable-mobile-barcode">Mobile Barcode</label>
                                                   </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="verticalStep3">
                                            <div class="verticalStep3Form">
                                                <div class="form-group" style="height: 100px!important;">
                                                    <div class="input-field col-md-12 s12">
                                                        <div class="input-field col-md-3 s3">
                                                            <input id="editableOptCutTime" name="editableOptCutTime"
                                                                   type="number" class="validate form-control" min="0"
                                                                   value="0">
                                                            <label for="editableOptCutTime">Cut Of Time</label>
                                                        </div>
                                                        <div class="input-field col-md-9 s9">
                                                            <select class="browser-default custom-select"
                                                                    name="editableOptCutTimeDate"
                                                                    id="editableOptCutTimeDate">
                                                                <option selected value="">Please select...</option>
                                                                <option value="m">Minute(s)</option>
                                                                <option value="h">Hour(s)</option>
                                                                <option value="d">Day(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="height: 120px!important;">
                                                    <div class="input-field col-md-12 s12">
                                                        <div class="input-field col-md-3 s3">
                                                            <input id="editableOptTourDuration"
                                                                   name="editableOptTourDuration" type="number"
                                                                   class="validate form-control" min="0" value="0">
                                                            <label for="editableOptTourDuration">Tour Duration</label>
                                                        </div>
                                                        <div class="input-field col-md-9 s9">
                                                            <select class="browser-default custom-select"
                                                                    name="editableOptTourDurationDate"
                                                                    id="editableOptTourDurationDate">
                                                                <option selected value="">Please select...</option>
                                                                <option value="m">Minute(s)</option>
                                                                <option value="h">Hour(s)</option>
                                                                <option value="d">Day(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group" style="height: 120px!important;">
                                                    <div class="input-field col-md-12 s12">
                                                        <div class="input-field col-md-3 s3">
                                                            <input id="editableOptGuideTime" name="editableOptGuideTime"
                                                                   type="number" class="validate form-control" min="0"
                                                                   value="0">
                                                            <label for="editableOptGuideTime">Meeting Start Time</label>
                                                        </div>
                                                        <div class="input-field col-md-9 s9">
                                                            <select class="browser-default custom-select"
                                                                    name="editableOptGuideTimeType"
                                                                    id="editableOptGuideTimeType">
                                                                <option selected value="">Please select...</option>
                                                                <option value="m">Minute(s)</option>
                                                                <option value="h">Hour(s)</option>
                                                                <option value="d">Day(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group" style="height: 120px!important;">
                                                    <div class="input-field col-md-12 s12">
                                                        <div class="input-field col-md-3 s3">
                                                            <input id="editableCancelPolicyTime"
                                                                   name="editableCancelPolicyTime" type="number"
                                                                   class="validate form-control" min="0" value="0">
                                                            <label for="editableCancelPolicyTime">Cancel Policy
                                                                Time</label>
                                                        </div>
                                                        <div class="input-field col-md-9 s9">
                                                            <select class="browser-default custom-select"
                                                                    name="editableCancelPolicyTimeType"
                                                                    id="editableCancelPolicyTimeType">
                                                                <option selected value="">Please select...</option>
                                                                <option value="m">Minute(s)</option>
                                                                <option value="h">Hour(s)</option>
                                                                <option value="d">Day(s)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                        </div>
                                        <div class="tab-pane fade" id="verticalStep4">
                                            <div class="verticalStep4Form">
                                                <input type="hidden" name="editableComission" class="editableComission"
                                                       value="">
                                                <input type="hidden" name="editableUserType" class="editableUserType"
                                                       value="">
                                                <input type="hidden" name="editablePricingId" class="editablePricingId"
                                                       value="">
                                                <input type="hidden" name="editableTierIterator"
                                                       class="editableTierIterator" value="">
                                                <div class="priceForm">
                                                    <div class="col-md-12">
                                                        <div id="pricingAlertInfo" class="alert alert-info"
                                                             role="alert">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="verticalStep5">
                                            <div class="verticalStep5Form">
                                                <div class="col-md-12">
                                                    <div id="availabilityAlertInfo" class="alert alert-info"
                                                         role="alert">

                                                    </div>
                                                </div>
                                                <input type="hidden" id="editableMinDate" value="">
                                                <input type="hidden" id="editableMaxDate" value="">
                                                <input type="hidden" id="editableAvailabilityType" value="">
                                                <input type="hidden" id="editableAvailabilityId" value="">
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="verticalStep6">
                                            <div class="verticalStep6Form">
                                                <input type="hidden" id="editableOptMeetingPoint"
                                                       name="editableOptMeetingPoint" class="editableOptMeetingPoint"
                                                       value="">
                                                <input type="hidden" id="editableOptMeetingPointLat"
                                                       name="editableOptMeetingPointLat"
                                                       class="editableOptMeetingPointLat" value="">
                                                <input type="hidden" id="editableOptMeetingPointLong"
                                                       name="editableOptMeetingPointLong"
                                                       class="editableOptMeetingPointLong" value="">
                                                <div class="form-group" style="height: 30px!important;">
                                                    <input name="editableRadioMPorT" type="radio"
                                                           id="editableMeetingPointPin" value="Meeting Point"
                                                           checked="checked"/>
                                                    <label for="editableMeetingPointPin">Meeting Point</label>
                                                    <input name="editableRadioMPorT" type="radio"
                                                           id="editableMeetingPointDesc" value="Transfer"/>
                                                    <label for="editableMeetingPointDesc">Transfer</label>
                                                </div>

                                                <div class="form-group" id="editableMeetingPointPinDiv">
                                                    <div class="editablepac-card" id="editablepac-card">
                                                        <div>
                                                            <div id="title">
                                                                Autocomplete search
                                                            </div>
                                                            <div id="editabletype-selector"
                                                                 class="editablepac-controls">
                                                                <input type="radio" name="type"
                                                                       id="editablechangetype-all" checked="checked">
                                                                <label for="editablechangetype-all">All</label>

                                                                <input type="radio" name="type"
                                                                       id="editablechangetype-establishment">
                                                                <label for="editablechangetype-establishment">Establishments</label>

                                                                <input type="radio" name="type"
                                                                       id="editablechangetype-address">
                                                                <label
                                                                    for="editablechangetype-address">Addresses</label>

                                                                <input type="radio" name="type"
                                                                       id="editablechangetype-geocode">
                                                                <label for="editablechangetype-geocode">Geocodes</label>
                                                            </div>
                                                            <div id="editablestrict-bounds-selector"
                                                                 class="editablepac-controls">
                                                                <input type="checkbox" id="editableuse-strict-bounds"
                                                                       value="">
                                                                <label for="editableuse-strict-bounds">Strict
                                                                    Bounds</label>
                                                            </div>
                                                        </div>
                                                        <div id="editablepac-container"
                                                             style="z-index: 9999!important;">
                                                            <input id="editablepac-input" type="text"
                                                                   placeholder="Enter a location">
                                                        </div>
                                                    </div>
                                                    <div id="map2"
                                                         style="height: 600px; position: relative; overflow: hidden;"></div>
                                                    <div class="form-group">
                                                        <div class="input-field col s12">
                                                            <span
                                                                style="font-size: 16px!important;">Comment(Optional)</span>
                                                            <input name="editableMeetingComment"
                                                                   id="editableMeetingComment" type="text"
                                                                   class="tags form-control" value=" "/>
                                                        </div>
                                                    </div>
                                                    <div id="editableinfowindow-content">
                                                        <img src="" width="16" height="16" id="editableplace-icon">
                                                        <span id="editableplace-name" class="editabletitle"></span><br>
                                                        <span id="editableplace-address"></span>
                                                    </div>
                                                </div>
                                                <div id="editableMeetingPointDescDiv"
                                                     style="display: none; margin-top: 80px;" class="form-group">
                                                    <div class="input-field col-md-12 s12">
                                                        <input id="editableMeetingPointDescInput"
                                                               name="editableMeetingPointDescInput" type="text"
                                                               class="validate form-control" value=" ">
                                                        <label for="editableMeetingPointDescInput">Description</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="verticalStep7">


                                            <input type="hidden" id="editableOptAddresses" name="editableOptAddresses"
                                                   class="add" value="">
                                            <input type="hidden" id="editable_customer_mail_templates"
                                                   name="editable_customer_mail_templates" class="add" value="">


                                            <div id="map-area">


                                                <div class="pac-card3" id="pac-card3">
                                                    <div>
                                                        <div id="title3">
                                                            Autocomplete search
                                                        </div>
                                                        <div id="type-selector3" class="pac-controls3">
                                                            <input type="radio" name="type" id="changetype-all3"
                                                                   checked="checked">
                                                            <label for="changetype-all3">All</label>

                                                            <input type="radio" name="type"
                                                                   id="changetype-establishment3">
                                                            <label
                                                                for="changetype-establishment3">Establishments</label>

                                                            <input type="radio" name="type" id="changetype-address3">
                                                            <label for="changetype-address3">Addresses</label>

                                                            <input type="radio" name="type" id="changetype-geocode3">
                                                            <label for="changetype-geocode3">Geocodes</label>
                                                        </div>
                                                        <div id="strict-bounds-selector3" class="pac-controls3">
                                                            <input type="checkbox" id="use-strict-bounds3" value="">
                                                            <label for="use-strict-bounds3">Strict Bounds</label>
                                                        </div>
                                                    </div>
                                                    <div id="pac-container3" style="z-index: 9999!important;">
                                                        <input id="pac-input3" type="text"
                                                               placeholder="Enter a location" value="">
                                                    </div>
                                                </div>
                                                <div id="map3"></div>
                                                <div id="infowindow-content3">
                                                    <img src="" width="16" height="16" id="place-icon3">
                                                    <span id="place-name3" class="title3"></span><br>
                                                    <span id="place-address3"></span>
                                                </div>


                                                <div id="selected-address">


                                                </div>

                                            </div>
                                            <br><br>
                                            <hr>


                                            <div class="col-md-12"
                                                 style="margin-bottom: 25px;font-size: 16px!important;letter-spacing: 1px;">
                                                <label style="text-align: left"
                                                       class="col-md-8 col-xs-12 label label-info">If you would like to
                                                    delete a contact information field, <br><br>please leave it
                                                    blank.</label>
                                                <button type="button" style="right: 0;top:0;padding: 0 1rem;margin: 1%;"
                                                        class=" btn" id="addNewContactInformationLabel">Add New Contact
                                                    Box
                                                </button>
                                            </div>
                                            <h4>Contact Informations</h4>
                                            <div id="contactInformationDiv" class="form-group">
                                                <input id="contactInformationIteratorForEdit" hidden value="1">
                                            </div>
                                            <div class="">

                                            </div>
                                            <div class="col-md-6">
                                                <input class="col-md-12" value="0" type="checkbox"
                                                       id="contactForAllTravelersForEdit">
                                                <label for="contactForAllTravelersForEdit">Would you like to get
                                                    informations for all travelers?</label>
                                            </div>


                                            <div class="col-md-12" style="margin-top: 30px;">
                                                <h1>Mail Template For Customer</h1>

                                                <div class="form-group">

                                                    <ul class="nav nav-tabs">

                                                        <li class="active"><a data-toggle="tab"
                                                                              href="#editable-en">EN</a></li>
                                                        <li><a data-toggle="tab" href="#editable-fr">FR</a></li>
                                                        <li><a data-toggle="tab" href="#editable-tr">TR</a></li>
                                                        <li><a data-toggle="tab" href="#editable-ru">RU</a></li>
                                                        <li><a data-toggle="tab" href="#editable-es">ES</a></li>
                                                        <li><a data-toggle="tab" href="#editable-de">DE</a></li>
                                                        <li><a data-toggle="tab" href="#editable-it">IT</a></li>
                                                        <li><a data-toggle="tab" href="#editable-pt">PT</a></li>
                                                        <li><a data-toggle="tab" href="#editable-nd">ND</a></li>
                                                    </ul>

                                                    <div class="tab-content" id="editable-customer-tab-content-wrap">
                                                        <div id="editable-en" class="tab-pane fade in active">

                                                            <textarea name="en" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-fr" class="tab-pane fade">

                                                            <textarea name="fr" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-tr" class="tab-pane fade">

                                                            <textarea name="tr" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-ru" class="tab-pane fade">

                                                            <textarea name="ru" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-es" class="tab-pane fade">

                                                            <textarea name="es" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-de" class="tab-pane fade">

                                                            <textarea name="de" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-it" class="tab-pane fade">

                                                            <textarea name="it" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-pt" class="tab-pane fade">

                                                            <textarea name="pt" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                        <div id="editable-nd" class="tab-pane fade">

                                                            <textarea name="nd" value="" id="" cols="30" rows="10"
                                                                      style="height: 300px;"></textarea>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div><!--end of col-->


                                        </div>

                                        <div class="col-md-12 optionEditButtons"
                                             style="margin-top: 30px;margin-bottom: 3%;">
                                            <span style="margin-left: 10px;" class="btn btn-success btn-lg pull-right"
                                                  id="saveCurrentOption">Save</span>
                                            <span style="margin-left: 10px;" class="btn btn-success btn-lg pull-right"
                                                  id="deleteCurrentOption">Remove</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End New Option Part -->
                    <div class="col-md-12" style="margin-top: 50px;">
                        <button style="margin-left: 10px;"
                                class="btn btn-danger btn-lg pull-right saveAsDraftBeforeSubmitButton">Save as Draft
                        </button>
                        <button style="margin-left: 10px;" class="btn btn-success btn-lg pull-right" type="submit">
                            Update
                        </button>
                        <button class="btn btn-danger btn-lg pull-right" id="saveAsDraftButton">Save as Draft</button>
                        <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                                style="margin-bottom:30px; margin-right: 10px;">Previous
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <input style="display: none" type="file" id="inputImage" name="page_file1"
               class="form-control col-md-7 col-xs-12"
               accept="image/*">

    </div>
</div>
<div id="uploadimageModal1" class="modal" role="dialog">
    <div class="modal-dialog" style="width:700px">
        <div class="modal-content " style="width:700px">
            {{--                <div class="modal-header" style="width:750px" >--}}
            {{--                    <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
            {{--                    <h4 class="modal-title">Ürün Fotoğrafı Kırpma Ekranı</h4>--}}
            {{--                </div>--}}
            <div class="modal-body" style="width:700px;padding: 0">
                <div class="container cropper">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="img-container">
                                <img width="300" id="image" alt="Picture">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-9 docs-buttons">
                            <!-- <h3 class="page-header">Toolbar:</h3> -->

                            <!-- Show the cropped image in modal -->
                            <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true"
                                 aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">&times;
                                            </button>
                                            <h4 class="modal-title" id="getCroppedCanvasTitle">Cropped</h4>
                                        </div>
                                        <div class="modal-body"></div>
                                        <div class="modal-bodyy"></div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                Close
                                            </button>
                                            <a class="btn btn-primary" id="download" href="javascript:void(0);"
                                               download="cropped.png">Download</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.modal -->

                        </div><!-- /.docs-buttons -->

                        <!-- /.docs-toggles -->

                    </div>
                    <input type="hidden" name="responsive" checked>
                    <input type="hidden" name="restore" checked>
                    <input type="hidden" name="checkCrossOrigin" checked>
                    <input type="hidden" name="checkOrientation" checked>
                    <input type="hidden" name="modal" checked>
                    <input type="hidden" name="guides" checked>
                    <input type="hidden" name="center" checked>
                    <input type="hidden" name="highlight" checked>
                    <input type="hidden" name="background" checked>
                    <input type="hidden" name="autoCrop" checked>
                    <input type="hidden" name="movable" checked>
                    <input type="hidden" name="rotatable" checked>
                    <input type="hidden" name="scalable" checked>
                    <input type="hidden" name="zoomable" checked>
                    <input type="hidden" name="zoomOnTouch" checked>
                    <input type="hidden" name="zoomOnWheel" checked>
                    <input type="hidden" name="cropBoxMovable" checked>
                    <input type="hidden" name="cropBoxResizable" checked>
                    <input type="hidden" name="toggleDragModeOnDblclick" checked>
                    <input type="radio" class="sr-only" id="viewMode0" name="viewMode" value="0" checked>
                    <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success crop_image">Kaydet</button>
                <div class="docs-buttons" style="display: none">
                    <button id="docs-trig" type="button" data-method="getCroppedCanvas"
                            data-option="{ &quot;width&quot;: 600, &quot;height&quot;: 600 }">dene
                    </button>
                </div>

                <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal" aria-hidden="true">Kapat
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<!-- Gallery Modal -->
@include('layouts.modal-box.gallery-modal')

<!-- Option Modal -->
@include('layouts.modal-box.option-modal')

@include('layouts.modal-box.save-as-draft')

@include('panel.product-partials.edit-scripts')


