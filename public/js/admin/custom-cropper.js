
$(document).on("click", ".home-image", function () {

    var thisIs = $(this);
    var id_raw = thisIs.attr('id');
    var id = id_raw.split('-')[1];
    var productId = $('.productId').val();



    if (id != "undefined") {
        if (parseInt(thisIs.attr('value')) == 0) {

            $('.home-image').removeClass('btn-success').addClass('btn-default');
            $('.home-image').attr('value',0);
            thisIs.removeClass('btn-default').addClass('btn-success');
            thisIs.attr('value',1);

            $('.home-image-dz').attr('value',0);
            $('.home-image-dz').text('Set as Cover Photo');
            $('.home-image-dz').removeClass('check-ok');

            $('.home-image-dz').each(function (){

                if(id==$(this).attr('data-id')){
                    $(this).attr('value', 1);
                    $(this).text('✔');
                    $(this).addClass('check-ok');
                }
            });
            var name = thisIs.attr('name');


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/product/setAsCoverPhoto',
                data: {
                    productId: productId,
                    fileName: name,
                    fileID: id
                },
                success: function (data) {
                    $('.coverPhotoNameSpan').html('Cover Photo: ' + data.fileName);
                }
            });
        }

    } else {
        Materialize.toast('You can not choice not loaded photo', 4000, 'toast-alert');

    }

});
$(document).on("click", ".delete-image", function () {
    var thiss = $(this);
    if (parseInt(thiss.val())) {
        var id_raw = thiss.attr('id');
        var id = id_raw.split('-')[1];
        var file_name = thiss.attr('name');


        productId = $('.productId').val();
        ownerId = $('.userId').val();
        whichPage = "soft"
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/product/deletePhoto',
            data: {
                fileID: id,
                productId: productId,
                fileName: file_name,
                ownerId: ownerId,
                whichPage: whichPage
            },
            success: function (data) {
                thiss.parent().remove();

                var cover_text=$('.coverPhotoNameSpan').text().split('Cover Photo: ')[1];
                if(data.fileName==cover_text){
                    $('.coverPhotoNameSpan').text('No Cover Photo Selected');
                }
                $('.step4abutton').attr("disabled", true);

                $('.home-image-dz').each(function (){

                    if(id==$(this).attr('data-id')){

                        $(this).parent().remove();
                    }
                });
            }
        });
    } else {
        thiss.parent().remove();

    }
});


$(document).ready(function () {
    "use strict";

// ---------------------------------------------------------------------------------------------------------------

    var file_name = "error";
    var g_file = null;
    $('#myModal').modal();
    $('.old-system').hide();
    $('.new-system').show();
    $('.dz-message').hide();
    $('#old-system-btn').click(function () {
        $('.old-system').show();
        $('.new-system').hide();
        $('.dz-message').show();

    });

    $('#new-system-btn').click(function () {
        $('.old-system').hide();
        $('.new-system').show();
        $('.dz-message').hide();


    });


    $('.add_photo_button').click(function () {
        $('#inputImage').trigger("click");
    });


    $('.btn-modal2').click(function () {
        $('#myModal').modal('show')
        // $('#uploadimageModal2').show();

    });

    $('.crop_image').click(function () {
        var method = 'getCroppedCanvas';
        var secondOption = null;
        var option = null;
        var foto;
        var result;

        option = {width: 800, height: 500};
        result = $image.cropper(method, option, secondOption);

        if (result) {
            foto = result.toDataURL();


            // $('.btn-cancel').trigger("click");
            // $('.dropzone1').html('<img height="200" src="' + foto + '" alt="">');
            // var template='<img class="cropped-foto" src="' + foto + '" alt="">'


            $.ajax({
                url: "/gallery/uploadPhoto/product",
                type: "POST",
                data: {
                    "_token": $('.csrfToken').val(),
                    "fileName": g_file.name,
                    "productId": $('.productId').val(),
                    "attractions": $('#attractions').val(),
                    "location": $('#cities').val(),
                    "ownerID": $('.ownerID').val(),
                    "type": g_file.type,
                    "isAjax": true,
                    "img_data": foto
                },
                success: function (data) {
                    var class_name;
                    var class_temp;
                    var file_name;
                    var home_html="";
                    var home_val;
                    var del_val;
                    var productId;
                    var img_id;
                    if (data.success) {
                        class_name = "dz-success";
                        class_temp = '<div class="dz-success-mark">    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">      <title>Check</title>      <defs></defs>      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>      </g>    </svg>  </div>'
                        file_name = data.file_name;
                        del_val = 1;
                        img_id = data.imageID;

                        productId = $('.productId').val();
                        home_html='<button onclick="return false" class="home-image btn-default btn-xs" value="' + 0 + '" name="' + file_name + '" id="homeId-' + data.imageID + '" ><i class="fa fa-home"></i></button>'


                    } else {
                        class_name = "dz-error";
                        class_temp = '<div class="dz-error-mark">    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">      <title>Error</title>      <defs></defs>      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">        <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">          <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>        </g>      </g>    </svg>  </div>'
                        file_name = null;
                        del_val = 0;
                        Materialize.toast(data.error, 4000, 'toast-alert');

                    }


                    var template = '<div class="dz-preview dz-processing dz-image-preview ' + class_name + ' dz-complete" align="center">' +
                        '<div class="dz-image">' +
                        '<img data-dz-thumbnail=""  alt="2eyy.jpg" src="' + foto + '">' +
                        '</div>' + class_temp +
                        // '<button class="delete-image btn-danger btn-xs" onclick="$(this).parent().remove()" ><i class="fa fa-trash-o"></i></button>' +
                        '<button onclick="return false" class="delete-image btn-danger btn-xs" value="' + del_val + '" name="' + file_name + '" id="imageId-' + data.imageID + '" ><i class="fa fa-trash-o"></i></button>' +
                        home_html +
                        '</div>';


                    $('.dropzone1').append(template);

                }
            });


            $('.btn-cancel').click();


        }



        $('#uploadimageModal').show();
    });

    $('#inputImage').on('change', function (e) {


        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onloadend = () => {
            // log to console
            // logs data:<type>;base64,wL2dvYWwgbW9yZ...
            var raw_file = reader.result
        };
        reader.readAsDataURL(file);
        g_file = file;
        console.log(file.type);
        if(!(file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/webp")){
            console.log(' Mime Type:'+file.type);
            Materialize.toast('You can not upload this('+file.type+') type file', 4000, 'toast-alert');
            return false;
        }



        options['aspectRatio'] = 800 / 500;
        $image.cropper('destroy').cropper(options);
        $('#upload-image-input').click();

    });

    var $image = $('#image');
    var $download = $('#download');
    var $dataX = $('#dataX');
    var $dataY = $('#dataY');
    var $dataHeight = $('#dataHeight');
    var $dataWidth = $('#dataWidth');
    var $dataRotate = $('#dataRotate');
    var $dataScaleX = $('#dataScaleX');
    var $dataScaleY = $('#dataScaleY');
    var options = {
        // aspectRatio: 16 / 9,
        aspectRatio: 800 / 500,
        preview: '.img-preview',
        crop: function (e) {
            $dataX.val(Math.round(e.x));
            $dataY.val(Math.round(e.y));
            // $dataHeight.val(Math.round(e.height));
            // $dataWidth.val(Math.round(e.width));
            $dataHeight.val(500);
            $dataWidth.val(800);
            $dataRotate.val(e.rotate);
            $dataScaleX.val(e.scaleX);
            $dataScaleY.val(e.scaleY);
        }
    };


    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();


    // Cropper
    $image.on({
        'build.cropper': function (e) {
            console.log(e.type);
        },
        'built.cropper': function (e) {
            console.log(e.type);
        },
        'cropstart.cropper': function (e) {
            console.log(e.type, e.action);
        },
        'cropmove.cropper': function (e) {
            console.log(e.type, e.action);
        },
        'cropend.cropper': function (e) {
            console.log(e.type, e.action);
        },
        'crop.cropper': function (e) {
            console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
        },
        'zoom.cropper': function (e) {
            console.log(e.type, e.ratio);
        }
    }).cropper(options);


    // Buttons
    if (!$.isFunction(document.createElement('canvas').getContext)) {
        $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
    }

    if (typeof document.createElement('cropper').style.transition === 'undefined') {
        $('button[data-method="rotate"]').prop('disabled', true);
        $('button[data-method="scale"]').prop('disabled', true);
    }


    // // Download
    // if (typeof $download[0].download === 'undefined') {
    //     $download.addClass('disabled');
    // }


    // Options
    $('.docs-toggles').on('change', 'input', function () {
        var $this = $(this);
        var name = $this.attr('name');
        var type = $this.prop('type');
        var cropBoxData;
        var canvasData;

        if (!$image.data('cropper')) {
            return;
        }

        if (type === 'checkbox') {
            options[name] = $this.prop('checked');
            cropBoxData = $image.cropper('getCropBoxData');
            canvasData = $image.cropper('getCanvasData');

            options.built = function () {
                $image.cropper('setCropBoxData', cropBoxData);
                $image.cropper('setCanvasData', canvasData);
            };
        } else if (type === 'radio') {
            options[name] = $this.val();
        }

        $image.cropper('destroy').cropper(options);
    });


    // Methods
    $('.docs-buttons').on('click', '[data-method]', function () {

        var $this = $(this);
        var data = $this.data();
        var $target;
        var result;
        var result1;
        var foto;
        var foto1;

        if ($this.prop('disabled') || $this.hasClass('disabled')) {
            return;
        }

        if ($image.data('cropper') && data.method) {
            data = $.extend({}, data); // Clone a new one
            if (typeof data.target !== 'undefined') {
                $target = $(data.target);
                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());

                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }

            result = $image.cropper(data.method, data.option, data.secondOption);

            // data.option = {aspectRatio: 7 / 11, width: 504, height: 792}
            // result1 = $image.cropper(data.method, data.option, data.secondOption);

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    $(this).data('option', -data.option);
                    break;

                case 'getCroppedCanvas':
                    if (result) {
                        alert('get cropped canvasa giriyor')

                        foto = result.toDataURL();
                        // foto1 = result1.toDataURL();

                        id = $('.proId').attr('id');
                        $('.changedImage').html('<img height="200" src="' + foto + '" alt="">');
                        $('#photos_img_data').val(foto);

                        $('#uploadimageModal').hide();

                    }

                    break;
            }

            if ($.isPlainObject(result) && $target) {
                try {
                    $target.val(JSON.stringify(result));
                } catch (e) {
                    console.log(e.message);
                }
            }

        }
    });

    // Keyboard
    $(document.body).on('keydown', function (e) {
        if (!$image.data('cropper') || this.scrollTop > 300) {
            return;
        }

        switch (e.which) {
            case 37:
                e.preventDefault();
                $image.cropper('move', -1, 0);
                break;

            case 38:
                e.preventDefault();
                $image.cropper('move', 0, -1);
                break;

            case 39:
                e.preventDefault();
                $image.cropper('move', 1, 0);
                break;

            case 40:
                e.preventDefault();
                $image.cropper('move', 0, 1);
                break;
        }
    });

    // Import image
    var $inputImage = $('#inputImage');
    var URL = window.URL || window.webkitURL;
    var blobURL;

    if (URL) {
        $inputImage.change(function () {
            var files = this.files;
            var file;

            if (!$image.data('cropper')) {
                return;
            }

            if (files && files.length) {
                file = files[0];

                if (/^image\/\w+$/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    $image.one('built.cropper', function () {

                        // Revoke when load complete
                        URL.revokeObjectURL(blobURL);
                    }).cropper('reset').cropper('replace', blobURL);
                    $inputImage.val('');
                } else {
                    window.alert('Please choose an image file.');
                }
            }
        });
    } else {
        $inputImage.prop('disabled', true).parent().addClass('disabled');
    }

    $('.crop_image').click(function (event) {

        // $(this).attr('disabled', true);

    });



});
