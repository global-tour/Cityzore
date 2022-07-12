@include('panel.product-partials.head', ['page' => 'product-create'])
@include('panel.product-partials.header', ['page' => 'product-create'])
<style>
    .justify-content-end {
        -webkit-box-pack: end !important;
        -ms-flex-pack: end !important;
        justify-content: flex-end !important;
    }

    .cropped-foto{
        border-radius: 30px;
        overflow: hidden;
        width: 120px;
        height: 120px;
        position: relative;
        display: block;
        z-index: 10;
    }
    .dz-image img{
        height: 120px;
        width: 192px;

    }
    .delete-image{
        margin-top: 4px;
        margin-right: 6px;
    }
    .home-image{
        margin-top: 4px;

    }
    .check-ok{
        border-radius: 50%;
        padding: 5px 10px;
        background-color: green;
        color: white;
        font-size: 12px;
        cursor: pointer;
    }
    .dropzone .dz-preview.dz-image-preview {
        text-align: center!important;
    }
</style>

<div style="margin-top: 100px;" class="container-fluid">
    <div class="row pull-right" style="margin-right: 1%;">
        <a href="" role="button" class="btn btn-default btn-lg draftInfoModalClass" data-toggle="modal">Go to
            Dashboard</a>
    </div>
</div>
<div class="container">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-primary btn-circle step1abutton">1</a>
                <p class="hidden-xs">Product Details</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-default btn-circle step2abutton"
                   disabled="disabled">2</a>
                <p class="hidden-xs">Highlights</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-default btn-circle step3abutton"
                   disabled="disabled">3</a>
                <p class="hidden-xs">Pictures</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-4" type="button" class="btn btn-default btn-circle step4abutton"
                   disabled="disabled">4</a>
                <p class="hidden-xs">Options</p>
            </div>
        </div>
    </div>

    <form id="productStoreForm" method="POST" action="{{url('product/store')}}" enctype="multipart/form-data"
          class="form-horizontal form-label-left">
        <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
        <input type="hidden" name="product_id" class="productId" value="">
        <input type="hidden" name="isDraft" class="isDraft" value="0">
        <input type="hidden" name="whichStep" class="whichStep" value="0">
        <input type="hidden" name="redirectToDashboard" class="redirectToDashboard" value="0">
        <input type="hidden" name="userType" class="userType" value="{{$userType}}">
        <input type="hidden" name="comission" class="comission" value="{{$comission}}">
        <input type="hidden" name="optCount" class="optCount" value="0">
        <input type="hidden" name="userId" class="userId" value="{{$ownerId}}">
        <input type="hidden" name="whichPage" class="whichPage" value="product">
        <div class="row setup-content" id="step-1" style="display: none!important;">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <h3 style="text-align: center;"> Product Details</h3>
                    <div class="form-group" style="margin-top: 40px;">
                        <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Product Title</span><br>
                                <span style="color:blue;font-size:13px!important;">Example: Paris: Louvre Skip-the-Line Ticket</span><br>
                                <input id="title" name="title" type="text" class="validate form-control"
                                       style="margin-top:2%; font-size:15px;">
                                <span class="titleErrorSpan col-md-12"
                                      style="display: none!important; color: #ff0000;font-size:15px!important;">This field is required.</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-field col-lg-12" style="border: 1px solid #e0e0e0;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Short Description</span><br>
                                <span style="color:blue;font-size:13px!important;">This is the customer’s first introduction to your activity. It should outline what you’ll do and make customers want to learn more.
                                    Write 2 or 3 short sentences that summarize your activity in English. To get customers excited about what they’ll do,
                                    use action words like explore, see, or enjoy. Don’t write “we,” “our,” or your company’s name.
                                </span>
                                <input id="shortDesc" name="shortDesc" type="text" class="validate form-control"
                                       style="margin-top:2%; font-size:15px;">
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
                                <textarea title="Full Description" name="fullDesc" id="fullDesc"
                                          class="materialize-textarea form-control"></textarea>
                                <span class="fullDescErrorSpan col-md-12"
                                      style="display: none!important; color: #ff0000;font-size:15px!important;">This field is required.</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-field col-md-8" style="border: 1px solid #e0e0e0;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;padding-bottom: 5%;">Select Country</span> <br>
                                <select class="select2 browser-default custom-select" name="location" id="location"
                                        style="width:100% !important;">
                                    <option selected value="">Choose a Country</option>
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
                                        style="width:100% !important;"></select>
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
                                <select class="mdb-select" multiple name="attractions[]" id="attractions">
                                    <option value="" disabled selected>Choose product attraction</option>
                                </select>
                                <span class="attractionErrorSpan col-md-12"
                                      style="display: none!important; color: #ff0000;">This field is required.</span>
                            </div>
                        </div>
                    </div>
                    <div class="phoneNumberWrapper col-md-12"
                         style="border: 1px solid #e0e0e0;margin-top:15px;margin-bottom: 15px;">
                        <div class="row" style="padding: 30px;">
                            <div class="form-group">
                                <label for="phoneNumber">Phone Number</label>
                                <button data-value="1" id="addPhoneNumber" class="col-md-3 btn btn-primary pull-right"
                                        type="button" style="margin-bottom:30px;">Add New Phone Number
                                </button>
                                <div class="input-field col-md-9">
                                    <input type="hidden" name="countryCode[]" id="countryCode">
                                    <input autocomplete="off" type="tel" id="phoneNumber" name="phoneNumber[]">
                                    <span id="valid-msg" class="hide">✓ Valid</span>
                                    <span id="error-msg" class="hide"></span>
                                </div>
                                <span class="countryCodeAndPhoneNumberErrorSpan col-md-12"
                                      style="display: none!important; color: #ff0000;">These fields are required.</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" style="margin-bottom:30px;">
                        Next
                    </button>
                    <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                            style="margin-bottom:30px; margin-right: 10px; display:none;">Previous
                    </button>
                </div>
            </div>
        </div>
        <div style="display: none!important;" class="row setup-content" id="step-2">
            <div class="col-xs-12">
                <div class="col-md-12 col-lg-12">
                    <h3 style="text-align: center;"> Highlights</h3>
                    <div class="form-group" style="margin-top: 40px">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Highlights</span> <br>
                                <span style="color:blue;font-size:13px!important;">Example: Marvel at the centuries-old frescoes of the Sistine Chapel</span>
                                <input name="highlights" id="highlights" type="text" class="tags form-control"/>
                                <div id="suggestions-container" style=" float: left;margin: 10px;"></div>
                            </div>
                        </div>
                        <span class="highlightsErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">What's Included</span><br>
                                <span
                                    style="color:blue;font-size:13px!important;">Example: Hotel pickup and drop-off</span>
                                <input name="included" id="included" type="text" class="tags form-control"/>
                            </div>
                        </div>
                        <span class="includedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">What's Not Included</span><br>
                                <span style="color:blue;font-size:13px!important;">Example: Food and drinks</span>
                                <input name="notIncluded" id="notincluded" type="text" class="tags form-control"/>
                            </div>
                        </div>
                        <span class="notIncludedErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Know Before You Go</span><br>
                                <span style="color:blue;font-size:13px!important;">Example: This tour is not recommended for people with limited mobility.</span>
                                <input name="knowBeforeYouGo" id="beforeyougo" type="text" class="tags form-control"/>
                            </div>
                        </div>
                        <span class="beforeyougoErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="input-field col s12" style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Cancel Policy</span> <br>
                                <textarea name="cancelPolicy" id="cancelPolicy"
                                          class="materialize-textarea form-control"
                                          style="margin-top:2%; font-size:15px;"></textarea>
                            </div>
                        </div>
                        <span class="cancelPolicyErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="input-field col s12" style="border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <select class="select2 browser-default custom-select" name="category" id="categoryId"
                                        style="width:100% !important;">
                                    <option selected value="">Choose a Category</option>
                                    @foreach($category as $categories)
                                        <option
                                            value="{{$categories->categoryName}}">{{$categories->categoryName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <span class="categoryIdErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="margin-top: 1%;border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Product Tags</span> <br>
                                <span style="color:blue;font-size:13px!important;">Example :Eiffel Tower, Seine River Cruise</span>
                                <input name="tags" id="tags_1" type="text" class="tags form-control col s12"
                                       placeholder="Add Some Tags"/>
                                <div id="suggestions-container"
                                     style="position: relative; float: left;margin: 10px;"></div>
                            </div>
                        </div>
                        <span class="tags_1ErrorSpan col-md-12" style="display: none!important; color: #ff0000;">This field is required.</span>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12"
                             style="margin-top: 1%;border: 1px solid #e0e0e0;margin-bottom: 15px;">
                            <div class="row" style="padding: 30px;">
                                <span style="font-size: 20px!important;">Product Files</span> <br>
                                <input type="file" name="product_files[]" id="productFiles" multiple>
                            </div>
                        </div>
                    </div>
                    <button style="margin-left: 10px;"
                            class="btn btn-danger btn-lg pull-right saveAsDraftBeforeSubmitButton">Save as Draft
                    </button>
                    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" style="margin-bottom:30px;">
                        Next
                    </button>
                    <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                            style="margin-bottom:30px; margin-right: 10px;">Previous
                    </button>
                </div>
            </div>
        </div>


        <div class="row setup-content" id="step-3">

            <div class="col-xs-12">
                <div class="col-md-12">
                    <h3> Pictures</h3>


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
                                        <button class="btn btn-primary add_photo_button">Yeni Fotoğraf Ekle</button>
                                        <input type="hidden" id="upload-image-input" data-toggle="modal"
                                               data-target="#uploadimageModal1">
                                        <input type="hidden" name="foto-slug" id="foto-slug" value="foto slug degeri">
                                    </div>
                                    <span hidden style="font-size: 16px" class="col-md-12 col-sm-12 label label-danger"
                                          id="pictureSizeErrorSpan">Your photo size have to be smaller than 8 MB.</span>
                                    <div action="/gallery/uploadPhoto/product" class="dropzone1"
                                         id="my-awesome-dropzone11"></div>
                                    <br/>
                                    <br/>
                                    <span class="col-md-12 col-sm-12 coverPhotoNameSpan" style="background-color: #17a2b8; color: #fff;"
                                          id="coverPhotoNameSpan1">No Cover Photo Selected</span>
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
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_content">
                                    <p style="margin: 14px">Drag multiple files to the box below for multi upload or
                                        click to select files.</p>
                                    <span hidden style="font-size: 16px" class="col-md-12 col-sm-12 label label-danger"
                                          id="pictureSizeErrorSpan">Your photo size have to be smaller than 8 MB.</span>
                                    <div action="/gallery/uploadPhoto/product" class="dropzone"
                                         id="my-awesome-dropzone"></div>
                                    <br/>
                                    <span class="col-md-12 col-sm-12 coverPhotoNameSpan" style="background-color: #17a2b8; color: #fff;"
                                          id="coverPhotoNameSpan">No Cover Photo Selected</span>
                                    <span class="col-md-3 col-sm-3" id="dropzoneErrorSpan"
                                          style="color: #FF0000; display: none;">You must at least upload one image and set one cover photo.</span>
                                    <br/>
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button style="margin-left: 10px;"
                            class="btn btn-danger btn-lg pull-right saveAsDraftBeforeSubmitButton">Save as Draft
                    </button>
                    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" style="margin-bottom:30px;">
                        Next
                    </button>
                    <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                            style="margin-bottom:30px; margin-right: 10px;">Previous
                    </button>


                </div>
            </div>
        </div>
        <div style="display: none!important;" class="row setup-content" id="step-4">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <h3> Options</h3>
                    <a href="#optionModal" type="button" role="button" class="btn btn-default btn-lg"
                       data-toggle="modal" style="margin-bottom: 20px; margin-top: 20px;">Create a New Option</a>
                    <table style="height: 500px" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Option Name</th>
                            <th><a href="javascript:void(0);" class="addRow btn btn-info">+</a></th>
                        </tr>
                        </thead>
                        <tbody class="option" id="tbody-option">

                        </tbody>
                    </table>
                    <button style="margin-left: 10px;" class="btn btn-danger btn-lg pull-right" id="saveAsDraftButton">
                        Save as Draft
                    </button>
                    <button class="btn btn-success btn-lg pull-right" type="submit">Finish</button>
                    <button class="btn btn-primary prevBtn btn-lg pull-right" type="button"
                            style="margin-bottom:30px; margin-right: 10px;">Previouss
                    </button>

                </div>
            </div>
        </div>
    </form>


    <input style="display: none" type="file" id="inputImage" name="page_file" class="form-control col-md-7 col-xs-12"
           accept="image/*">
</div>


<div id="uploadimageModal2" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" style="margin-top: 50px;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeModal closeGalleryModal" data-dismiss="modal" aria-hidden="true"
                        style="opacity: 1!important;">x
                </button>
                <p>Select Image(s) from Gallery</p>
            </div>
            <div class="modal-body" style="height: 350px; overflow-y: scroll;" id="imageGalleryModalBody">

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="selectImagesForProductButton">Select Image(s)</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->


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

@include('panel.product-partials.scripts')
{{--<script>--}}
{{--    $(function () {--}}
{{--        $('#step-1').hide();--}}
{{--        $('#step-2').hide();--}}
{{--        $('#step-3').show();--}}
{{--    });--}}
{{--</script>--}}
