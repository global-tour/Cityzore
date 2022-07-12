</body>
<script src="{{asset('js/jquery-latest.min.js')}}"></script>
<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/admin/bootstrap.min.js')}}"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/admin/custom.js')}}"></script>
<script src="{{asset('js/dropzone.js')}}"></script>
<script src="{{asset('js/admin/custom-cropper.js')}}"></script>
<script src="{{asset('foto/vendors/cropper/dist/cropper.min.js')}}"></script>
<script src="{{asset('foto/build/js/custom.min.js')}}"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('js/intl-tel-input/build/js/intlTelInput.js')}}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="{{asset('js/lodash.js')}}"></script>
<script src="{{ asset('js/wizard-form/tagify.min.js') }}"></script>
<script src="{{'../../keditor/build/keditor.min.js'}}"></script>
<script src="{{asset('../../keditor/src/lang/en.js')}}"></script>
<script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>

<?php use \App\Http\Controllers\VariableController; ?>

<script>
    KEDITOR.create('fullDesc', {
        buttonList: [
            ['fontSize'],
            ['bold', 'underline', 'italic'],
            ['fontColor'],
            ['link'],
            ['fullScreen', 'codeView'],
            ['preview', 'print'],
            ['undo', 'redo'],
        ],
        minHeight: '250px',
    });

    $('form').on('submit', function () {
        $('#fullDesc').val($('.keditor-editable').html());
    });

    $('.keditor-editable').on('keyup', function () {
        let navigationBar = $('.ke-navigation');
        let wordCount = $('.keditor-editable').html().split(" ").length;
        navigationBar.html('');
        if ((101 - wordCount) < 0) {
            navigationBar.append('<span style="color: #ff0000; font-weight: bolder">Explanation is sufficient.</span>');
        } else {
            navigationBar.append('You need  <span style="color: #ff0000; font-weight: bolder">' + (101 - wordCount) + '</span> words');
        }
    });

    let highlights = document.querySelector('#highlights');

    let included = document.querySelector('#included');
    let notIncluded = document.querySelector('#notincluded');
    let knowBeforeYouGo = document.querySelector('#beforeyougo');

    let editableIncluded = document.querySelector('#editableIncluded');
    let editableNotIncluded = document.querySelector('#editableNotIncluded');
    let editableKnowBeforeYouGo = document.querySelector('#editableKnowBeforeYouGo');

    let tags_1 = document.querySelector('#tags_1');
    let editableMeetingComment = document.querySelector('#editableMeetingComment');
    // let highlightsEditedProduct = document.querySelector('#highlightsEditedProduct');
    // let includedEditedProduct = document.querySelector('#includedEditedProduct');
    // let notIncludedEditedProduct = document.querySelector('#notincludedEditedProduct');
    // let beforeyougoEditedProduct = document.querySelector('#beforeyougoEditedProduct');
    // let tags_1EditedProduct = document.querySelector('#tags_1EditedProduct');

    let tagifyHighlights = new Tagify(highlights, {
        keepInvalidTags: true,
        backspace: "edit",
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.' + tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })

    function onDragEndHighlights(elm) {
        tagifyHighlights.updateValueByDOMTags()
    }


    let tagifyIncluded = new Tagify(included, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.' + tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })

    function onDragEndIncluded(elm) {
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded = new Tagify(notIncluded, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.' + tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })

    function onDragEndNotIncluded(elm) {
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo = new Tagify(knowBeforeYouGo, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.' + tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })

    function onDragEndKnowBeforeYouGo(elm) {
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }

    //

    let tagifyEditableIncluded = new Tagify(editableIncluded, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyEditableIncluded.DOM.scope, {
        selector: '.' + tagifyEditableIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndEditableIncluded
        }
    })

    function onDragEndEditableIncluded(elm) {
        tagifyEditableIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#includedprocess', function() {
        var mainText = $('#includedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyEditableIncluded.addTags(mainText);
        $('#includedarea').val('');
        $('#includedcollapsetrigger').click();
        return false;
    });

    let tagifyEditableNotIncluded = new Tagify(editableNotIncluded, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyEditableNotIncluded.DOM.scope, {
        selector: '.' + tagifyEditableNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndEditableNotIncluded
        }
    })

    function onDragEndEditableNotIncluded(elm) {
        tagifyEditableNotIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#notincludedprocess', function() {
        var mainText = $('#notincludedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyEditableNotIncluded.addTags(mainText);
        $('#notincludedarea').val('');
        $('#notincludedcollapsetrigger').click();
        return false;
    });

    let tagifyEditableKnowBeforeYouGo = new Tagify(editableKnowBeforeYouGo, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyEditableKnowBeforeYouGo.DOM.scope, {
        selector: '.' + tagifyEditableKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndEditableKnowBeforeYouGo
        }
    })

    function onDragEndEditableKnowBeforeYouGo(elm) {
        tagifyEditableKnowBeforeYouGo.updateValueByDOMTags()
    }

    $(document).on('click', '#beforeyougoprocess', function() {
        var mainText = $('#beforeyougoarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyEditableKnowBeforeYouGo.addTags(mainText);
        $('#beforeyougoarea').val('');
        $('#beforeyougocollapsetrigger').click();
        return false;
    });

    let tagifyTags_1 = new Tagify(tags_1, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyTags_1.DOM.scope, {
        selector: '.' + tagifyTags_1.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyTags_1
        }
    })

    function onDragEndtagifyTags_1(elm) {
        tagifyTags_1.updateValueByDOMTags()
    }


    let tagifyEditableMeetingComment = new Tagify(editableMeetingComment, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyEditableMeetingComment.DOM.scope, {
        selector: '.' + tagifyEditableMeetingComment.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyEditableMeetingComment
        }
    })

    function onDragEndtagifyEditableMeetingComment(elm) {
        tagifyEditableMeetingComment.updateValueByDOMTags()
    }

    // let tagifyHighlightsEditedProduct =  new Tagify(highlightsEditedProduct, {
    //     keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
    //     backspace           : "edit",
    //
    // });
    // DragSort(tagifyHighlightsEditedProduct.DOM.scope, {
    //     selector: '.'+tagifyHighlightsEditedProduct.settings.classNames.tag,
    //     callbacks: {
    //         dragEnd: onDragEndtagifyHighlightsEditedProduct
    //     }
    // })
    // function onDragEndtagifyHighlightsEditedProduct(elm){
    //     tagifyHighlightsEditedProduct.updateValueByDOMTags()
    // }

    // let tagifyIncludedEditedProduct =  new Tagify(includedEditedProduct, {
    //     keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
    //     backspace           : "edit",
    //
    // });
    // DragSort(tagifyIncludedEditedProduct.DOM.scope, {
    //     selector: '.'+tagifyIncludedEditedProduct.settings.classNames.tag,
    //     callbacks: {
    //         dragEnd: onDragEndtagifyIncludedEditedProduct
    //     }
    // })
    // function onDragEndtagifyIncludedEditedProduct(elm){
    //     tagifyIncludedEditedProduct.updateValueByDOMTags()
    // }
    //
    // let tagifyNotIncludedEditedProduct =  new Tagify(notIncludedEditedProduct, {
    //     keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
    //     backspace           : "edit",
    //
    // });
    // DragSort(tagifyNotIncludedEditedProduct.DOM.scope, {
    //     selector: '.'+tagifyNotIncludedEditedProduct.settings.classNames.tag,
    //     callbacks: {
    //         dragEnd: onDragEndtagifyNotIncludedEditedProduct
    //     }
    // })
    // function onDragEndtagifyNotIncludedEditedProduct(elm){
    //     tagifyNotIncludedEditedProduct.updateValueByDOMTags()
    // }

    // let tagifybeforeyougoEditedProduct =  new Tagify(beforeyougoEditedProduct, {
    //     keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
    //     backspace           : "edit",
    //
    // });
    // DragSort(tagifybeforeyougoEditedProduct.DOM.scope, {
    //     selector: '.'+tagifybeforeyougoEditedProduct.settings.classNames.tag,
    //     callbacks: {
    //         dragEnd: onDragEndtagifybeforeyougoEditedProduct
    //     }
    // })
    // function onDragEndtagifybeforeyougoEditedProduct(elm){
    //     tagifybeforeyougoEditedProduct.updateValueByDOMTags()
    // }

    // let tagifytags_1EditedProduct =  new Tagify(tags_1EditedProduct, {
    //     keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
    //     backspace           : "edit",
    //
    // });
    // DragSort(tagifytags_1EditedProduct.DOM.scope, {
    //     selector: '.'+tagifytags_1EditedProduct.settings.classNames.tag,
    //     callbacks: {
    //         dragEnd: onDragEndtagifytags_1EditedProduct
    //     }
    // })
    // function onDragEndtagifytags_1EditedProduct(elm){
    //     tagifytags_1EditedProduct.updateValueByDOMTags()
    // }

    let opt_included = document.querySelector('#opt_included');
    let opt_notIncluded = document.querySelector('#opt_notIncluded');
    let opt_knowBeforeYouGo = document.querySelector('#opt_knowBeforeYouGo');

    let tagifyOptIncluded = new Tagify(opt_included, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyOptIncluded.DOM.scope, {
        selector: '.' + tagifyOptIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndOptIncluded
        }
    })

    function onDragEndOptIncluded(elm) {
        tagifyOptIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#opt_includedprocess', function() {
        var mainText = $('#opt_includedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyOptIncluded.addTags(mainText);
        $('#opt_includedarea').val('');
        $('#opt_includedcollapsetrigger').click();
    });

    let tagifyOptNotIncluded = new Tagify(opt_notIncluded, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyOptNotIncluded.DOM.scope, {
        selector: '.' + tagifyOptNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndOptNotIncluded
        }
    })

    function onDragEndOptNotIncluded(elm) {
        tagifyOptNotIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#opt_notincludedprocess', function() {
        var mainText = $('#opt_notincludedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyOptNotIncluded.addTags(mainText);
        $('#opt_notincludedarea').val('');
        $('#opt_notincludedcollapsetrigger').click();
    });

    let tagifyOptKnowBeforeYouGo = new Tagify(opt_knowBeforeYouGo, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",

    });
    DragSort(tagifyOptKnowBeforeYouGo.DOM.scope, {
        selector: '.' + tagifyOptKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndOptKnowBeforeYouGo
        }
    })

    function onDragEndOptKnowBeforeYouGo(elm) {
        tagifyOptKnowBeforeYouGo.updateValueByDOMTags()
    }

    $(document).on('click', '#opt_beforeyougoprocess', function() {
        var mainText = $('#opt_beforeyougoarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyOptKnowBeforeYouGo.addTags(mainText);
        $('#opt_beforeyougoarea').val('');
        $('#opt_beforeyougocollapsetrigger').click();
    });

</script>

<script>

    // That object is necessary for firing remove minute hour function
    let editableGlobalItObject = {
        itInternal: 1,
        itListener: function (val) {
        },
        set it(val) {
            this.itInternal = val;
            this.itListener(val);
        },
        get it() {
            return this.itInternal;
        },
        registerListener: function (listener) {
            this.itListener = listener;
        }
    };

    $(window).on('load', function () {
        let productID = $('.productId').val();
        $.ajax({
            type: 'POST',
            url: '/getProductGalleryData',
            data: {
                productID: productID,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                let productGalleries = data.productGalleries;
                if (productGalleries.length <= 0) {
                    $('button[type="submit"]').attr('disabled', 'disabled');
                } else {
                    $('button[type="submit"]').removeAttr('disabled');
                }
            }
        });

        let navigationBar = $('.ke-navigation');
        navigationBar.html('');
        let wordCount = $('.keditor-editable').html().split(" ").length;
        if ((101 - wordCount) < 0) {
            navigationBar.append('<span style="color: #ff0000; font-weight: bolder">Explanation is sufficient.</span>');
        } else {
            navigationBar.append('You need  <span style="color: #ff0000; font-weight: bolder">' + (101 - wordCount) + '</span> words');
        }

        if ($('.productOptionCount').val() === '0') {
            $('.editOptionsWrapper').hide();
        }
    });


    $(function () {
        $("#ticketTypes").select2();

        $('#new-system-btn').parent().addClass('active');
        $('#new-system-btn').attr('aria-expanded',true);
        $('#meetingPointDesc').on('click', function () {
            $('#meetingPointPinDiv').hide();
            $('#meetingPointDescDiv').show();
        });
        $('#meetingPointPin').on('click', function () {
            $('#meetingPointPinDiv').show();
            $('#meetingPointDescDiv').hide();
        });

        $("#location,#categoryId,#opt_select").select2({
            matcher: matchCustom,
            templateResult: formatCustom
        });
    });

    function stringMatch(term, candidate) {
        return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
    }

    function matchCustom(params, data) {
        // If there are no search terms, return all of the data
        if ($.trim(params.term) === '') {
            return data;
        }
        // Do not display the item if there is no 'text' property
        if (typeof data.text === 'undefined') {
            return null;
        }
        // Match text of option
        if (stringMatch(params.term, data.text)) {
            return data;
        }
        // Match attribute "data-foo" of option
        if (stringMatch(params.term, $(data.element).attr('data-foo'))) {
            return data;
        }
        // Return `null` if the term should not be displayed
        return null;
    }

    function formatCustom(state) {
        let divBlock = '<div><div>' + state.text + '</div><div class="foo">';
        if (typeof $(state.element).attr('data-foo') !== 'undefined') {
            divBlock += $(state.element).attr('data-foo');
        }
        divBlock += '</div></div>';
        return $(divBlock);
    }

</script>

<script>
    // here, the index maps to the error code returned from getValidationError - see readme
    let errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

    $('#addPhoneNumber').on('click', function () {
        let dataValue = $(this).attr('data-value');
        let nextDataValue = parseInt(dataValue) + 1;
        let block = '';
        block += '<div class="form-group col-md-12">\n' +
            '<label for="phoneNumber">Phone Number</label>\n' +
            '<button class="col-md-3 deletePhoneNumber btn btn-primary pull-right" type="button" style="margin-bottom:30px;">Delete</button>\n' +
            '<div class="input-field col-md-9">\n' +
            '<input type="hidden" name="countryCode[]" id="countryCode' + nextDataValue + '">\n' +
            '<input autocomplete="false" type="tel" id="phoneNumber' + nextDataValue + '" name="phoneNumber[]">\n' +
            '<span id="valid-msg' + nextDataValue + '" class="hide">✓ Valid</span>\n' +
            '<span id="error-msg' + nextDataValue + '" class="hide"></span>\n' +
            '</div>\n' +
            '</div>';
        $('.phoneNumberWrapper').append(block);

        let input2 = document.querySelector("#phoneNumber" + nextDataValue),
            errorMsg2 = document.querySelector("#error-msg" + nextDataValue),
            validMsg2 = document.querySelector("#valid-msg" + nextDataValue);
        let iti2 = window.intlTelInput(input2);

        // on keyup / change flag: reset
        input2.addEventListener('change', reset2(input2, errorMsg2, validMsg2));
        input2.addEventListener('keyup', reset2(input2, errorMsg2, validMsg2));

        // on blur: validate
        input2.addEventListener('blur', function () {
            reset2(input2, errorMsg2, validMsg2);
            if (input2.value.trim()) {
                if (iti2.isValidNumber()) {
                    validMsg2.classList.remove("hide");
                } else {
                    input2.classList.add("error");
                    let errorCode2 = iti2.getValidationError();
                    errorMsg2.innerHTML = errorMap[errorCode2];
                    errorMsg2.classList.remove("hide");
                }
            }
        });
        $(this).attr('data-value', nextDataValue);
    });

    $('body').on('click', '.deletePhoneNumber', function () {
        let dataValue = parseInt($('#addPhoneNumber').attr('data-value'));
        $('#addPhoneNumber').attr('data-value', dataValue - 1);
        $(this).parent().remove();
    });

    let reset2 = function (input2, errorMsg2, validMsg2) {
        input2.classList.remove("error");
        errorMsg2.innerHTML = "";
        errorMsg2.classList.add("hide");
        validMsg2.classList.add("hide");
    };

    let phoneNumberCount = parseInt($('#phoneNumberCount').val());
    for (let i = 1; i < (phoneNumberCount + 1); i++) {
        let input = document.querySelector("#phoneNumber" + i),
            errorMsg = document.querySelector("#error-msg" + i),
            validMsg = document.querySelector("#valid-msg" + i);

        // initialise plugin
        let iti = window.intlTelInput(input);

        let reset = function () {
            input.classList.remove("error");
            errorMsg.innerHTML = "";
            errorMsg.classList.add("hide");
            validMsg.classList.add("hide");
        };
        // on blur: validate
        input.addEventListener('blur', function () {
            reset();
            if (input.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                } else {
                    input.classList.add("error");
                    let errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
        });
        // on keyup / change flag: reset
        input.addEventListener('change', reset);
        input.addEventListener('keyup', reset);

        // intl tel input
        // set country and phone number
        let countryCodeHiddenVal = $('#countryIsoCode' + i);
        if (countryCodeHiddenVal.val() !== '') {
            iti.setCountry(countryCodeHiddenVal.val());
        }
    }
</script>

<script>
    $(function () {
        $('.nextBtn').on('click', function () {
            let whichStep = $('.whichStep').val();
            let productId = $('.productId').val();
            let bladeName = $('#bladeName').val();
            let values = {};
            if ($('#userGuard').val() !== 'supplier') {
                if (whichStep === '0') {
                    let title = $('#title').val();
                    let shortDesc = $('#shortDesc').val();
                    $('#fullDesc').val($('.keditor-editable').html());
                    let fullDesc = $('#fullDesc').val();
                    let location = $('#location').val();
                    let cities = $('#cities').val();
                    let attractions = $('#attractions').val();
                    if (bladeName !== 'productCheck') {
                        let countryCodes = [];
                        $('input[name="countryCode[]"]').each(function (index, item) {
                            countryCodes.push(item.value);
                        });
                        let phoneNumbers = [];
                        $('input[name="phoneNumber[]"]').each(function (index, item) {
                            phoneNumbers.push(item.value);
                        });
                        values['countryCodes'] = countryCodes;
                        values['phoneNumbers'] = phoneNumbers;
                    }

                    values['title'] = title;
                    values['shortDesc'] = shortDesc;
                    values['fullDesc'] = fullDesc;
                    values['location'] = location;
                    values['cities'] = cities;
                    values['attractions'] = attractions;
                    callProductDraft(whichStep, values, productId);
                }
                if (whichStep === '1') {
                    let highlights = $('#highlights').val();
                    let included = $('#included').val();
                    let notIncluded = $('#notincluded').val();
                    let knowBeforeYouGo = $('#beforeyougo').val();
                    let cancelPolicy = $('#cancelPolicy').val();
                    let category = $('#categoryId').val();
                    let tags = $('#tags_1').val();
                    values['highlights'] = highlights;
                    values['included'] = included;
                    values['notIncluded'] = notIncluded;
                    values['knowBeforeYouGo'] = knowBeforeYouGo;
                    values['cancelPolicy'] = cancelPolicy;
                    values['category'] = category;
                    values['tags'] = tags;
                    callProductDraft(whichStep, values, productId);
                }
            }
        });
    });

    function validateUpdateProductStepByStep(whichStep, values) {
        if (whichStep === '0' && (values['title'] === '' || values['shortDesc'] === '' || values['fullDesc'] === '<p><br></p>'
            || values['cities'] === '' || values['attractions'] === '' || values['phoneNumbers'][0] === '')) {
            return false;
        }

        return !(whichStep === '1' && (values['highlights'] === '' || values['included'] === '' || values['notIncluded'] === ''
            || values['knowBeforeYouGo'] === '' || values['cancelPolicy'] === '' || values['tags'] === ''));
    }

    function callProductDraft(whichStep, values, productId) {
        if (validateUpdateProductStepByStep(whichStep, values)) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/updateProductStepByStep',
                data: {
                    whichStep: whichStep,
                    values: values,
                    productId: productId
                },
                success: function (data) {
                    /*if (whichStep === '1') {
                        $('.productId').val(data.product_id);
                    }*/
                }
            });
        }
    }
</script>

<script>
    $(function () {
        $('body').on('click', '.addTier', function () {
            let tierIterator = $('.tierIterator').val();
            if ($('.adultDiv #maxPerson' + tierIterator).val() === '') {
                Materialize.toast('You should type Max Person count!', 4000, 'toast-alert');
                return;
            }
            let existingMaxPerson = parseInt($('.adultDiv #maxPerson' + tierIterator).val()) + 1;
            let it = parseInt(tierIterator) + 1;
            let block = '';
            block +=
                '<div class="col-md-12 categoryWrapper">\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '    </div>\n' +
                '    <div class="col-md-2 s2">\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="minPerson' + it + '" name="minPerson' + it + '" type="hidden" value="' + existingMaxPerson + '" class="validate form-control minPerson">\n' +
                '        <label id="minPersonLabel' + it + '">' + existingMaxPerson + ' -</label>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="maxPerson' + it + '" name="maxPerson' + it + '" type="number" min="' + existingMaxPerson + '" class="validate form-control maxPerson">\n' +
                '        <label id="maxPersonLabel' + it + '">Max. Person</label>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="price' + it + '" name="price' + it + '" type="number" onkeyup="calculateComission(\'price' + it + '\', $(this));" step="any" class="validate form-control price">\n' +
                '        <label for="price' + it + '">Price</label>\n' +
                '    </div>\n' +
                '    <div class="col-md-2 s2">\n' +
                '        <label for="price' + it + 'Com">Price You Earn</label>\n' +
                '        <div>\n' +
                '            <span class="priceCom" id="price' + it + 'Com" style="color: #ff0000;"></span>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <button id="deleteTier' + it + '" class="btn btn-primary deleteTier">X</button>\n' +
                '    </div>\n' +
                '</div>';
            $('.categoryDiv').append(block);
            $('.tierIterator').val(it);
        });

        $('body').on('click', '.deleteTier', function () {
            let it = $('.tierIterator').val();
            $('.tierIterator').val(parseInt(it) - 1);
            $('body #deleteTier' + it).parent().parent().remove();
        });

        $('#categorySelect').on('change', function () {
            let value = $(this).val();
            let capitalized = value.charAt(0).toUpperCase() + value.slice(1);
            $('#categorySelect option[value="' + value + '"]').remove();
            $('.adultDiv').clone().appendTo('.perPersonDiv');
            $('.categoryDiv.adultDiv').eq(0).removeClass('adultDiv').addClass(value + 'Div');
            $('.' + value + 'Div #adultLabel').eq(0).removeAttr('id').attr('id', value + 'Label');
            $('.' + value + 'Div #adultMin').eq(0).removeAttr('id').attr('id', value + 'Min');
            $('.' + value + 'Div #adultMax').eq(0).removeAttr('id').attr('id', value + 'Max');
            $('.' + value + 'Div #ignoreadult').eq(0).removeAttr('id').attr('id', 'ignore' + value);
            $('.' + value + 'Div .adultRemove').eq(0).removeClass('adultRemove').addClass(value + 'Remove');
            $('.' + value + 'Div .priceCategory').eq(0).val(value);
            $('#' + value + 'Min').val('');
            $('#' + value + 'Max').val('');
            $('.' + value + 'Div #' + value + 'Label').html(capitalized);
            $('.' + value + 'Div .' + value + 'Remove').attr('data-cat', value);
            $('.' + value + 'Div .' + value + 'Remove').show();
        });

        $('body').on('keyup', '.maxPerson', function () {
            let id = $(this).attr('id');
            let value = $(this).val();
            $('.perPersonDiv #' + id).val(value);
        });

        $('body').on('click', '.infantRemove,.childRemove,.youthRemove,.adultRemove,.euCitizenRemove', function () {
            let cat = $(this).attr('data-cat');
            let capitalized = cat.charAt(0).toUpperCase() + cat.slice(1);
            $('#categorySelect').append('<option value="' + cat + '">' + capitalized + '</option>');
            $(this).parent().parent().remove();
        });

        function isMinMaxAgesValid() {
            let agesArr = [];
            let agesArr2 = [];
            let adultMax = $('#adultMax').val();
            let adultMaxInt = parseInt(adultMax);
            if (isNaN(adultMaxInt)) {
                return false;
            }
            let adultMin = $('#adultMin').val();
            let adultMinInt = parseInt(adultMin);
            if (isNaN(adultMinInt)) {
                return false;
            }
            if ((adultMax === '' && adultMin !== '') || (adultMax !== '' && adultMin === '')) {
                return false;
            }
            if (adultMin !== '') {
                agesArr.push(adultMinInt);
                agesArr2.push(adultMinInt);
            }
            let youthMax = $('#youthMax').val();
            let youthMaxInt = parseInt(youthMax);
            if ($('#youthMax')[0]) {
                if (isNaN(youthMaxInt)) {
                    return false;
                }
                if (youthMax !== '') {
                    agesArr.push(youthMaxInt);
                    agesArr2.push(youthMaxInt);
                }
            }
            let youthMin = $('#youthMin').val();
            let youthMinInt = parseInt(youthMin);
            if ($('#youthMin')[0]) {
                if (isNaN(youthMinInt)) {
                    return false;
                }
                if ((youthMax === '' && youthMin !== '') || (youthMax !== '' && youthMin === '')) {
                    return false;
                }
                if (youthMin !== '') {
                    agesArr.push(youthMinInt);
                    agesArr2.push(youthMinInt);
                }
            }
            let childMax = $('#childMax').val();
            let childMaxInt = parseInt(childMax);
            if ($('#childMax')[0]) {
                if (isNaN(childMaxInt)) {
                    return false;
                }
                if (childMax !== '') {
                    agesArr.push(childMaxInt);
                    agesArr2.push(childMaxInt);
                }
            }
            let childMin = $('#childMin').val();
            let childMinInt = parseInt(childMin);
            if ($('#childMin')[0]) {
                if (isNaN(childMinInt)) {
                    return false;
                }
                if ((childMax === '' && childMin !== '') || (childMax !== '' && childMin === '')) {
                    return false;
                }
                if (childMin !== '') {
                    agesArr.push(childMinInt);
                    agesArr2.push(childMinInt);
                }
            }
            let infantMax = $('#infantMax').val();
            let infantMaxInt = parseInt(infantMax);
            if ($('#infantMax')[0]) {
                if (isNaN(infantMaxInt)) {
                    return false;
                }
                if (infantMax !== '') {
                    agesArr.push(infantMaxInt);
                    agesArr2.push(infantMaxInt);
                }
            }
            let infantMin = $('#infantMin').val();
            let infantMinInt = parseInt(infantMin);
            if ($('#infantMin')[0]) {
                if (isNaN(infantMinInt)) {
                    return false;
                }
                if ((infantMax === '' && infantMin !== '') || (infantMax !== '' && infantMin === '')) {
                    return false;
                }
            }
            let sortedAgesArr = agesArr2.sort((a, b) => b - a);
            let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) !== index);
            if (findDuplicates(sortedAgesArr).length > 0) {
                return false;
            }
            return _.isEqual(agesArr, sortedAgesArr);
        }

        $('#price_button').on('click', function () {
            let title = $('#pricingTitle').val();
            if (title === '') {
                Materialize.toast('Title can\'t be blank!', 4000, 'toast-alert');
                return;
            }
            let adultMin = $('#adultMin').val();
            let adultMax = $('#adultMax').val();
            let youthMin = $('#youthMin').val();
            let youthMax = $('#youthMax').val();
            let childMin = $('#childMin').val();
            let childMax = $('#childMax').val();
            let infantMin = $('#infantMin').val();
            let infantMax = $('#infantMax').val();
            let euCitizenMin = $('#euCitizenMin').val();
            let euCitizenMax = $('#euCitizenMax').val();
            let categories = ["adult", "youth", "child", "infant", "euCitizen"];
            let ignoredCategories = [];
            $.each(categories, function (index, value) {
                if ($('input[id="ignore' + value + '"]:checked').val() === "1") {
                    ignoredCategories.push(value);
                }
            });
            let minPerson = [];
            let maxPerson = [];
            $('.adultDiv .minPerson').each(function (index, item) {
                minPerson.push(item.value);
            });
            $('.adultDiv .maxPerson').each(function (index, item) {
                maxPerson.push(item.value);
            });
            let infantPrice = [];
            $('.infantDiv .price').each(function (index, item) {
                infantPrice.push(item.value);
            });
            let infantPriceCom = [];
            $('.infantDiv .priceCom').each(function (index, item) {
                infantPriceCom.push(item.innerText);
            });
            let childPrice = [];
            $('.childDiv .price').each(function (index, item) {
                childPrice.push(item.value);
            });
            let childPriceCom = [];
            $('.childDiv .priceCom').each(function (index, item) {
                childPriceCom.push(item.innerText);
            });
            let youthPrice = [];
            $('.youthDiv .price').each(function (index, item) {
                youthPrice.push(item.value);
            });
            let youthPriceCom = [];
            $('.youthDiv .priceCom').each(function (index, item) {
                youthPriceCom.push(item.innerText);
            });
            let adultPrice = [];
            $('.adultDiv .price').each(function (index, item) {
                adultPrice.push(item.value);
            });
            let adultPriceCom = [];
            $('.adultDiv .priceCom').each(function (index, item) {
                adultPriceCom.push(item.innerText);
            });
            let euCitizenPrice = [];
            $('.euCitizenDiv .price').each(function (index, item) {
                euCitizenPrice.push(item.value);
            });
            let euCitizenPriceCom = [];
            $('.euCitizenDiv .priceCom').each(function (index, item) {
                euCitizenPriceCom.push(item.innerText);
            });
            let tierCount = $('.tierIterator').val();
            if (isMinMaxAgesValid()) {
                $.ajax({
                    method: 'POST',
                    url: '/pricing/store',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        title: title,
                        ignoredCategories: ignoredCategories,
                        adultMin: adultMin,
                        adultMax: adultMax,
                        adultPrice: adultPrice,
                        adultPriceCom: adultPriceCom,
                        youthMin: youthMin,
                        youthMax: youthMax,
                        youthPrice: youthPrice,
                        youthPriceCom: youthPriceCom,
                        childMin: childMin,
                        childMax: childMax,
                        childPrice: childPrice,
                        childPriceCom: childPriceCom,
                        infantMin: infantMin,
                        infantMax: infantMax,
                        infantPrice: infantPrice,
                        infantPriceCom: infantPriceCom,
                        euCitizenMin: euCitizenMin,
                        euCitizenMax: euCitizenMax,
                        euCitizenPrice: euCitizenPrice,
                        euCitizenPriceCom: euCitizenPriceCom,
                        minPerson: minPerson,
                        maxPerson: maxPerson,
                        tierCount: tierCount
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            Materialize.toast('Pricing is successfully added! You will be redirected to pricing list in 3 seconds', 4000, 'toast-success');
                            $('#pricings').append('<option id="pricings" name="pricings" value="' + data.pricing.id + '">' + data.pricing.title + '</option>');
                            $('#pricings').val(data.pricing.id);
                            Materialize.toast('Pricing is successfully added', 4000, 'toast-success');
                            $('#price_button').attr('disabled', true);
                            $('.nextBtnForOpt').click();
                            setTimeout(function () {
                                $('#price_button').attr('disabled', false);
                            }, 5000);
                        }
                    }
                });
            } else {
                Materialize.toast('Please fill age ranges correctly!', 4000, 'toast-alert');
            }
        });

    });

    function calculateComission(which, $this) {
        let category = $this.parent().parent().parent().find('.priceCategory').val();
        let userType = $('.userType').val();
        let price = $('body .' + category + 'Div #' + which).val();
        let comission = $('.comission').val();
        if (userType === 'supplier') {
            $('body .' + category + 'Div #' + which + 'Com').html((price - (price * (comission / 100))).toFixed(2));
        } else {
            $('body .' + category + 'Div #' + which + 'Com').html(price);
        }
    }
</script>

<script>
    let addedOption = null;
    let addedAvailability = null;

    // That object is necessary for firing remove minute hour function
    let globalItObject = {
        itInternal: 1,
        itListener: function (val) {
        },
        set it(val) {
            this.itInternal = val;
            this.itListener(val);
        },
        get it() {
            return this.itInternal;
        },
        registerListener: function (listener) {
            this.itListener = listener;
        }
    };

    // Add Hour/Minute Dynamically For Every Day
    function addMinHour(day, date, time) {
        let type = $('input[name="radioTime"]:checked').val();
        let block = '<div class="col-md-12 input-field s12 dynamicDiv">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="' + day + 'Hour" name="' + day + 'Hour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToFor' + day + ' col-md-2">\n' +
                '          <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="' + day + 'HourTo" name="' + day + 'HourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger">x</a></div></div>';
        }
        $('.' + day + 'Div' + date + time).append(block);
    }

    // Add Dates Dynamically For Every Availability
    function addDates(it) {
        let type = $('input[name="radioTime"]:checked').val();
        if (it >= 1) {
            if (!validateDateTimes(type, it, false)) {
                return;
            }
        }
        if (it >= 2) {
            if (!validateDates(it, false)) {
                return;
            }
        }
        let date = it + 1;
        $('.coll-body').hide({'easing': 'swing'});
        let block = '<li class="dynamicLi">\n' +
            '            <div class="collapsible-header coll-head"><i class="icon-cz-booking"></i>Date Range | <span id="dateRangeText' + date + '" class="dateRangeText"></span></div>\n' +
            '                 <div style="border-bottom: 0!important;" class="collapsible-body coll-body">\n' +
            '                     <div class="newDateDiv" style="height: 100%!important;">\n' +
            '                         <div class="newDateWrapper">\n';
        block += ' <div class="form-group">\n' +
            '           <input type="text" class="dateRange' + date + '" name="daterange[]" value="" />\n' +
            '      </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Monday</label>\n' +
            '          <div class="mondayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="mondayHour" name="mondayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForMonday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="mondayHourTo" name="mondayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'monday\', ' + date + ', 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'monday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '        </div>\n' +
            '          </div>\n' +
            '       </div>\n';
        block += '<div class="form-group">\n' +
            '              <label>Tuesday</label>\n' +
            '               <div class="tuesdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="tuesdayHour" name="tuesdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForTuesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="tuesdayHourTo" name="tuesdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'tuesday\', ' + date + ', 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'tuesday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '        </div>\n' +
            '         </div>\n' +
            '     </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Wednesday</label>\n' +
            '          <div class="wednesdayDiv' + date + '1">\n' +
            '              <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="wednesdayHour" name="wednesdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForWednesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="wednesdayHourTo" name="wednesdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '           <button onclick="addMinHour(\'wednesday\', ' + date + ', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'wednesday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '             </div>\n' +
            '             </div>\n' +
            '        </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Thursday</label>\n' +
            '          <div class="thursdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="thursdayHour" name="thursdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForThursday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="thursdayHourTo" name="thursdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '         <button onclick="addMinHour(\'thursday\', ' + date + ', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'thursday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '              </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Friday</label>\n' +
            '          <div class="fridayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="fridayHour" name="fridayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForFriday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="fridayHourTo" name="fridayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'friday\', ' + date + ', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'friday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Saturday</label>\n' +
            '          <div class="saturdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="saturdayHour" name="saturdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForSaturday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="saturdayHourTo" name="saturdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '           <button onclick="addMinHour(\'saturday\', ' + date + ', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '      </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'saturday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '                 </div>\n' +
            '                 </div>\n' +
            '            </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Sunday</label>\n' +
            '          <div class="sundayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="sundayHour" name="sundayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForSunday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="sundayHourTo" name="sundayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'sunday\', ' + date + ', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'sunday\', ' + date + ', false)" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '               </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="col-md-12">\n' +
            '          <button onclick="addDates(' + date + ');" data-id="' + date + '" class="addNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right">Add New Date Range</button>\n' +
            '          <button data-id="' + date + '" class="removeDateButton btn btn-primary">Remove Date Range</button>\n' +
            '     </div>\n';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n' +
            '    </li>';
        $('.collapsible').append(block);
        let collBody = $('#step5').find('.coll-body');
        collBody.eq(it).show({'easing': 'swing'});
        globalItObject.it = it + 1;
        $('button.addNewDateButton[data-id="' + it + '"]').attr('disabled', true);
        $('button.removeDateButton[data-id="' + it + '"]').attr('disabled', true);
        $('input.dateRange' + it).attr('disabled', true);
    }

    $(function () {

        $('input[name="radioTime"]').on('change', function () {
            let type = $('input[name="radioTime"]:checked').val();
            if (type === 'Operating Hours') {
                $('.addMinHourButtonDiv').hide();
                $('.hourDivToForMonday').show();
                $('.hourDivToForTuesday').show();
                $('.hourDivToForWednesday').show();
                $('.hourDivToForThursday').show();
                $('.hourDivToForFriday').show();
                $('.hourDivToForSaturday').show();
                $('.hourDivToForSunday').show();
            }
            if (type === 'Starting Time') {
                $('.addMinHourButtonDiv').show();
                $('.hourDivToForMonday').hide();
                $('.hourDivToForTuesday').hide();
                $('.hourDivToForWednesday').hide();
                $('.hourDivToForThursday').hide();
                $('.hourDivToForFriday').hide();
                $('.hourDivToForSaturday').hide();
                $('.hourDivToForSunday').hide();
            }
        });

        // Init first(static) daterangepicker
        $('.dateRange1').daterangepicker({
            'opens': 'center',
            'drops': 'down',
            'startDate': moment().format('DD/MM/YYYY'),
            'endDate': moment().add('1', 'months').format('DD/MM/YYYY'),
            "locale": {
                "format": "DD/MM/YYYY",
            }
        });

        $('.dateRangeText').html($('.dateRange1').val());

        $('input[name="daterange[]"]').on('change', function () {
            let dateRangeText = $(this).parent().parent().parent().parent().parent().find('.dateRangeText');
            dateRangeText.html($(this).val());
        });

        // Made a workaround for first remove minute hour function
        let weekDaysArr = ['.mondayDiv', '.tuesdayDiv', '.wednesdayDiv', '.thursdayDiv', '.fridayDiv', '.saturdayDiv', '.sundayDiv'];
        for (let b = 0; b < weekDaysArr.length; b++) {
            weekDaysArr[b] = weekDaysArr[b] + '11';
            $('.collapsible').on('click', weekDaysArr[b] + ' .removeMinHourButton', function () {
                $(this).parent().parent().remove();
            });
        }

        // That workaround is starting from second. Basicly it is observing the globalItObject object and if it changes it fires the function below when it changed
        globalItObject.registerListener(function () {
            if (globalItObject.it > 1) {
                let weekDaysArr = ['.mondayDiv', '.tuesdayDiv', '.wednesdayDiv', '.thursdayDiv', '.fridayDiv', '.saturdayDiv', '.sundayDiv'];
                for (let b = 0; b < weekDaysArr.length; b++) {
                    weekDaysArr[b] = weekDaysArr[b] + globalItObject.it + '1';
                    $('.collapsible').on('click', weekDaysArr[b] + ' .removeMinHourButton', function () {
                        $(this).parent().parent().remove();
                    });
                }

                let minDate = $('.dateRange' + (globalItObject.it - 1)).val().split('- ')[1];
                let minDateMoment = moment(minDate, "DD/MM/YYYY").add(1, 'd');
                let minDateString = moment(minDateMoment).format('DD/MM/YYYY');
                let maxDateMoment = moment(minDateString, "DD/MM/YYYY").add(1, 'months');
                let maxDateString = moment(maxDateMoment).format('DD/MM/YYYY');
                $('.dateRange' + globalItObject.it).daterangepicker({
                    'opens': 'center',
                    'drops': 'down',
                    'minDate': minDateString,
                    'startDate': minDateString,
                    'endDate': maxDateString,
                    "locale": {
                        "format": "DD/MM/YYYY",
                    }
                });
                $('#dateRangeText' + globalItObject.it).html($('.dateRange' + globalItObject.it).val());
            }
            $('input[name="daterange[]"]').on('change', function () {
                let dateRangeText = $(this).parent().parent().parent().parent().parent().find('.dateRangeText');
                dateRangeText.html($(this).val());
            });
        });

        $('.collapsible').on('click', '.removeDateButton', function () {
            $(this).parent().parent().parent().parent().parent().remove();
            $('button.addNewDateButton[data-id="' + (globalItObject.it - 1) + '"]').attr('disabled', false);
            $('button.removeDateButton[data-id="' + (globalItObject.it - 1) + '"]').attr('disabled', false);
            $('input.dateRange' + (globalItObject.it - 1)).attr('disabled', false);
            globalItObject.it = globalItObject.it - 1;
            let collBody = $('#step5').find('.coll-body');
            collBody.eq(globalItObject.it - 1).show({'easing': 'swing'});
        });

    });
</script>

<script>

    function hideShowPrevNextButtons() {
        let allNextBtnForOpt = $('.nextBtnForOpt');
        let allPrevBtnForOpt = $('.prevBtnForOpt');
        parseInt(allNextBtnForOpt.attr('data-step')) === 1 ? allPrevBtnForOpt.hide() : allPrevBtnForOpt.show();
        parseInt(allNextBtnForOpt.attr('data-step')) === 6 ? allNextBtnForOpt.hide() : allNextBtnForOpt.show();
    }

    $(document).ready(function () {
        $('.step1abutton').on('click', function () {
            $('.whichStep').val('0');
        });

        $('.step2abutton').on('click', function () {
            $('.whichStep').val('1');
        });

        $('.step3abutton').on('click', function () {
            $('.whichStep').val('2');
        });

        $('.step4abutton').on('click', function () {
            $('.whichStep').val('3');
        });

        let navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn'),
            allPrevBtn = $('.prevBtn');

        allWells.hide();

        navListItems.click(function (e) {
            e.preventDefault();
            let $target = $($(this).attr('href')),
                $item = $(this);

            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
            }
        });

        allNextBtn.click(function (e) {
            if (productFormValidation()) {
                let curStep = $(this).closest(".setup-content"),
                    curStepBtn = curStep.attr("id"),
                    nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                    curInputs = curStep.find("input[type='text'],input[type='url']"),
                    isValid = true;

                $(".form-group").removeClass("has-error");
                for (let i = 0; i < curInputs.length; i++) {
                    if (!curInputs[i].validity.valid) {
                        isValid = false;
                        $(curInputs[i]).closest(".form-group").addClass("has-error");
                    }
                }

                let step = parseInt($('.whichStep').val());
                $('.whichStep').val(step + 1);

                if (isValid) {
                    nextStepWizard.removeAttr('disabled').trigger('click');
                }
                if (parseInt($('.whichStep').val()) > 0) {
                    $('.draftInfoModalClass').attr('href', '#draftInfoModal');
                }
            }
            $("html").animate({scrollTop: 0}, "slow");
        });

        allPrevBtn.click(function (e) {
            let step = parseInt($('.whichStep').val());
            $('.whichStep').val(step - 1);
            let curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");
            prevStepWizard.removeAttr('disabled').trigger('click');
            $("html").animate({scrollTop: 0}, "slow");
        });

        let allNextBtnForOpt = $('.nextBtnForOpt');
        let allPrevBtnForOpt = $('.prevBtnForOpt');
        allNextBtnForOpt.click(function (e) {
            let step = $(this).attr('data-step');
            let nextStep = parseInt(step) + 1;
            let nextStepWizardForOpt = $('.option-setup-panel li a[href="#step' + nextStep + '"]');
            nextStepWizardForOpt.trigger('click');
            hideShowPrevNextButtons();
        });

        allPrevBtnForOpt.click(function (e) {
            let step = allNextBtnForOpt.attr('data-step');
            let prevStep = parseInt(step) - 1;
            let prevStepWizardForOpt = $('.option-setup-panel li a[href="#step' + prevStep + '"]');
            prevStepWizardForOpt.trigger('click');
            hideShowPrevNextButtons();
        });

        $('div.setup-panel div a.btn-primary').trigger('click');

        function productFormValidation() {
            $('input[name="countryCode[]"]').each(function (index, item) {
                item.value = $('.iti__active').eq(index).attr('data-dial-code');
            });
            $('#fullDesc').val($('.keditor-editable').html());
            @if (env('APP_ENV') == 'local')
                return true; //--> Please comment out that line while testing the product creation form
            @endif
            let whichStep = parseInt($('.whichStep').val());
            if (whichStep === 0) {
                if ($('#title').val() === '') {
                    $('.titleErrorSpan').show();
                } else {
                    $('.titleErrorSpan').hide();
                }
                if ($('#shortDesc').val() === '') {
                    $('.shortDescErrorSpan').show();
                } else {
                    $('.shortDescErrorSpan').hide();
                }
                if ($('#fullDesc').val() === '<p><br></p>') {
                    $('.fullDescErrorSpan').show();
                } else {
                    $('.fullDescErrorSpan').hide();
                }
                if ($('#location').val() === '') {
                    $('.locationErrorSpan').show();
                } else {
                    $('.locationErrorSpan').hide();
                }
                if ($('#attractions').val().length === 0) {
                    $('.attractionErrorSpan').show();
                } else {
                    $('.attractionErrorSpan').hide();
                }
                return !($('#title').val() === '' || $('#shortDesc').val() === '' || $('#fullDesc').val() === '<p><br></p>' || $('#location').val() === '' || $('#attractions').val().length === 0);
            }
            if (whichStep === 1) {
                if ($('#highlights').val() === '') {
                    $('.highlightsErrorSpan').show();
                } else {
                    $('.highlightsErrorSpan').hide();
                }
                if ($('#included').val() === '') {
                    $('.includedErrorSpan').show();
                } else {
                    $('.includedErrorSpan').hide();
                }
                if ($('#notincluded').val() === '') {
                    $('.notIncludedErrorSpan').show();
                } else {
                    $('.notIncludedErrorSpan').hide();
                }
                if ($('#beforeyougo').val() === '') {
                    $('.beforeyougoErrorSpan').show();
                } else {
                    $('.beforeyougoErrorSpan').hide();
                }
                if ($('#cancelPolicy').val() === '') {
                    $('.cancelPolicyErrorSpan').show();
                } else {
                    $('.cancelPolicyErrorSpan').hide();
                }
                if ($('#categoryId').val() === '') {
                    $('.categoryIdErrorSpan').show();
                } else {
                    $('.categoryIdErrorSpan').hide();
                }
                if ($('#tags_1').val() === '') {
                    $('.tags_1ErrorSpan').show();
                } else {
                    $('.tags_1ErrorSpan').hide();
                }
                return !($('#highlights').val() === '' || $('#included').val() === '' || $('#notincluded').val() === '' || $('#beforeyougo').val() === '' || $('#cancelPolicy').val() === '' || $('#categoryId').val() === '' || $('#tags_1').val() === '');
            }
            if (whichStep === 2) {
                if ($('.dz-image').children().length === 0) {
                    $('#dropzoneErrorSpan').show();
                    return false;
                } else {
                    $('#dropzoneErrorSpan').hide();
                    return true;
                }
            }
        }
    });

    $('#productEditForm').submit(function (e) {
        e.preventDefault();
        $('input[name="countryCode[]"]').each(function (index, item) {
            item.value = $('.iti__active').eq(index).attr('data-dial-code');
        });
        if ($('#opt_select').val() === 'null') {
            $('#opt_selectErrorSpan').show();
            return;
        }
        let title = $('#title').val();
        let oldTitle = $('.oldTitle').val();
        let productID = $('.productId').val();
        let serializeData = $('#productEditForm').serialize();

        if (title !== oldTitle) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/product/titleValidation',
                data: {
                    title: title,
                    productID: productID
                },
                success: function (data) {
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                        return;
                    }
                    $('#productEditForm')[0].submit();
                }
            });
        } else {


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/product/isFilledAll',
                data: serializeData,
                success: function (data) {
                    /*console.log(data);
                    return false;*/
                    if (data.status === "0") {
                        Materialize.toast(data.message, 4000, 'toast-alert');
                        return;
                    } else {
                        $('#productEditForm')[0].submit();
                    }

                }
            });


            //$('#productEditForm')[0].submit();
        }
    });

    $('#saveAsDraftButton').on('click', function (e) {
        e.preventDefault();
        $('.isDraft').val("1");
        $('#productEditForm')[0].submit();
    });

    $('#saveAsDraftModalButton').on('click', function (e) {
        e.preventDefault();
        $('.isDraft').val("1");
        $('.redirectToDashboard').val("1");
        $('#productEditForm')[0].submit();
    });

    $('.saveAsDraftBeforeSubmitButton').on('click', function (e) {
        e.preventDefault();
        $('.isDraft').val("1");
        $('.redirectToDashboard').val("1");
        $('#productEditForm')[0].submit();
    });

    $('.draftInfoModalClass').on('click', function () {
        if (parseInt($('.whichStep').val()) === 0) {
            window.location.href = '/';
        }
    });

    $('[data-step=2]').click(function () {
        return false;
    }).addClass("text-muted");
    $('[data-step=3]').click(function () {
        return false;
    }).addClass("text-muted");
    $('[data-step=4]').click(function () {
        return false;
    }).addClass("text-muted");
    $('[data-step=5]').click(function () {
        return false;
    }).addClass("text-muted");
    $('[data-step=6]').click(function () {
        return false;
    }).addClass("text-muted");
    $('[data-step=7]').click(function () {
        return false;
    }).addClass("text-muted");

    $('#step1Tab').on('click', function () {
        $('.nextBtnForOpt').attr('data-step', 1);
        hideShowPrevNextButtons();
        $('#price_button').hide();
        $('#opt_button').hide();
    });

    $('#step2Tab').on('click', function () {
        if ($('#opt_title').val() === '') {
            $('.opt_titleErrorSpan').show();
        } else {
            $('.opt_titleErrorSpan').hide();
        }
        if ($('#opt_desc').val() === '') {
            $('.opt_descErrorSpan').show();
        } else {
            $('.opt_descErrorSpan').hide();
        }
        if ($('#opt_full_desc').val() === '') {
            $('.opt_full_descErrorSpan').show();
        } else {
            $('.opt_full_descErrorSpan').hide();
        }
        if ($('#opt_included').val() === '') {
            $('.opt_includedErrorSpan').show();
        } else {
            $('.opt_includedErrorSpan').hide();
        }
        if ($('#opt_notIncluded').val() === '') {
            $('.opt_notIncludedErrorSpan').show();
        } else {
            $('.opt_notIncludedErrorSpan').hide();
        }
        if ($('#opt_knowBeforeYouGo').val() === '') {
            $('.opt_knowBeforeYouGoErrorSpan').show();
        } else {
            $('.opt_knowBeforeYouGoErrorSpan').hide();
        }
        if ($('#opt_title').val() !== '' && $('#opt_desc').val() !== '' && $('#opt_full_desc').val() !== '' && $('#opt_included').val() !== '' && $('#opt_notIncluded').val() !== '' && $('#opt_knowBeforeYouGo').val() !== '') {
            $('#step2Tab').removeClass('text-muted');
            $('#step2Tab').removeAttr('disabled');
            $('#step2Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 2);
            hideShowPrevNextButtons();
        }
        $('#av_button').hide();
        $('#price_button').hide();
        $('#opt_button').hide();
    });

    $('#step3Tab').on('click', function () {
        if (($('#minPerson').val() === '') || parseInt($('#minPerson').val()) > parseInt($('#maxPerson').val()) || parseInt($('#minPerson').val()) < 1) {
            $('.minMaxPersonErrorSpan').show();
        } else {
            $('.minMaxPersonErrorSpan').hide();
            $('#step3Tab').removeClass('text-muted');
            $('#step3Tab').removeAttr('disabled');
            $('#step3Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 3);
            hideShowPrevNextButtons();
        }
        $('#av_button').hide();
        $('#price_button').hide();
        $('#opt_button').hide();
    });

    $('#step4Tab').on('click', function () {
        if ($('#opt_cut_time').val() === '' || $('#opt_cut_time_date').val() === '') {
            $('.opt_cut_timeErrorSpan').show();
        } else {
            $('.opt_cut_timeErrorSpan').hide();
        }
        if ($('#opt_tour_duration').val() === '' || $('#opt_tour_duration_date').val() === '') {
            $('.opt_tour_durationErrorSpan').show();
        } else {
            $('.opt_tour_durationErrorSpan').hide();
        }
        if ($('#opt_cut_time').val() !== '' && $('#opt_cut_time_date').val() !== '' && $('#opt_tour_duration').val() !== '' && $('#opt_tour_duration_date').val() !== '') {
            $('#step4Tab').removeClass('text-muted');
            $('#step4Tab').removeAttr('disabled');
            $('#step4Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 4);
            hideShowPrevNextButtons();
        }
        $('#av_button').hide();
        $('#price_button').show();
        $('#opt_button').hide();
    });

    $('#step5Tab').on('click', function () {
        if ($('#pricings').val() === '') {
            $('.pricingsErrorSpan').show();
            if ($('#bladeName').val() !== 'productCheck') {
                Materialize.toast('You must save your pricing if you are creating a new one! <br> Or you must select an old one from select box', 4000, 'toast-alert');
            }
        } else {
            $('.pricingsErrorSpan').hide();
            $('#step5Tab').removeClass('text-muted');
            $('#step5Tab').removeAttr('disabled');
            $('#step5Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 5);
            hideShowPrevNextButtons();
            $('#av_button').show();
            $('.coll-body').show({'easing': 'swing'});
            $('#price_button').hide();
        }
        $('#opt_button').hide();
    });

    $('#step6Tab').on('click', function () {
        if ($('#availabilities').val() === '') {
            $('.availabilitiesErrorSpan').show();
        } else {
            let availabilities = [];
            $('select[name="availabilities[]"] option:selected').each(function () {
                availabilities.push($(this).val());
            });
            if (availabilities.length > 1) {
                if (availabilities.every((val, i, arr) => val === arr[0])) {
                    if ($('#bladeName').val() !== 'productCheck') {
                        Materialize.toast('Same availability can not be selected twice!', 4000, 'toast-alert');
                    }
                    return;
                }
                if (availabilities.includes('')) {
                    if ($('#bladeName').val() !== 'productCheck') {
                        Materialize.toast('You must select all availabilities!', 4000, 'toast-alert');
                    }
                    return;
                }
            }
            $('.availabilitiesErrorSpan').hide();
            $('#step6Tab').removeClass('text-muted');
            $('#step6Tab').removeAttr('disabled');
            $('#step6Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 6);
            hideShowPrevNextButtons();
            $('#opt_button').show();
            $('#av_button').hide();
            $('#price_button').hide();
        }
    });

    $('#step7Tab').on('click', function () {
        if ($('#pac-input').val() === '') {
            if ($('#bladeName').val() !== 'productCheck') {
                Materialize.toast('You should type a meeting point!', 4000, 'toast-alert');
            }
            //return;
        } else {
            $('#step7Tab').removeAttr('disabled');
            $('#step7Tab').tab('show');
            $('.nextBtnForOpt').attr('data-step', 7);
            hideShowPrevNextButtons();
            $('#opt_button').show();
            $('#av_button').hide();
            $('#price_button').hide();
        }
    });

    $('#pricings').on('change', function () {
        if ($('#pricings').val() === '') {
            $('.priceForm').show();
        } else {
            $('.priceForm').hide();
        }
    });

</script>

<script>
    $(document).ready(function () {
        $('.next').click(function () {
            let nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
            return false;
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            //update progress
            let step = $(e.target).data('step');
            let percent = (parseInt(step) / 6) * 100;

            $('.progress-bar').css({width: percent + '%'});
            $('.progress-bar').text("Step " + step + " of 6");
        });

        $('.first').click(function () {
            $('#myWizard a:first').tab('show');
        });

    });
</script>

<script>
    $(document).ready(function () {
        $('#limitlessTicket').on('change', function () {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#av_button').on('click', function () {
            let type = $('input[name="radioTime"]:checked').val();
            let name = $('#avName').val();
            let ticketType = $('#ticketType').val();
            let it = globalItObject.it;
            let isLimitless = $('#limitlessTicket').val();
            if (name === '') {
                if ($('#bladeName').val() !== 'productCheck') {
                    Materialize.toast('Availability Name can not be blank!', 4000, 'toast-alert');
                }
                return;
            }
            if (it >= 1) {
                if (!validateDateTimes(type, it, false)) {
                    return;
                }
            }
            if (it > 1) {
                if (!validateDates(it, false)) {
                    return;
                }
            }
            let monday = new Array(it);
            let tuesday = new Array(it);
            let wednesday = new Array(it);
            let thursday = new Array(it);
            let friday = new Array(it);
            let saturday = new Array(it);
            let sunday = new Array(it);
            let dateRanges = new Array(it);
            for (let x = 0; x < it; x++) {
                monday[x] = [];
                tuesday[x] = [];
                wednesday[x] = [];
                thursday[x] = [];
                friday[x] = [];
                saturday[x] = [];
                sunday[x] = [];
                dateRanges[x] = [];
                $('input[name="mondayHour' + (x + 1) + '[]"]').each(function (i) {
                    monday[x][i] = {};
                    if ($(this).val() !== '') {
                        monday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="mondayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            monday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="tuesdayHour' + (x + 1) + '[]"]').each(function (i) {
                    tuesday[x][i] = {};
                    if ($(this).val() !== '') {
                        tuesday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="tuesdayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            tuesday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="wednesdayHour' + (x + 1) + '[]"]').each(function (i) {
                    wednesday[x][i] = {};
                    if ($(this).val() !== '') {
                        wednesday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="wednesdayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            wednesday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="thursdayHour' + (x + 1) + '[]"]').each(function (i) {
                    thursday[x][i] = {};
                    if ($(this).val() !== '') {
                        thursday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="thursdayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            thursday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="fridayHour' + (x + 1) + '[]"]').each(function (i) {
                    friday[x][i] = {};
                    if ($(this).val() !== '') {
                        friday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="fridayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            friday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="saturdayHour' + (x + 1) + '[]"]').each(function (i) {
                    saturday[x][i] = {};
                    if ($(this).val() !== '') {
                        saturday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="saturdayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            saturday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="sundayHour' + (x + 1) + '[]"]').each(function (i) {
                    sunday[x][i] = {};
                    if ($(this).val() !== '') {
                        sunday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="sundayHourTo' + (x + 1) + '[]"').each(function (i) {
                        if ($(this).val() !== '') {
                            sunday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="daterange[]"]').each(function (i) {
                    dateRanges[i] = $(this).val();
                });
            }
            $.ajax({
                method: 'POST',
                url: '/av/store',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type,
                    name: name,
                    dateRanges: dateRanges,
                    monday: monday,
                    tuesday: tuesday,
                    wednesday: wednesday,
                    thursday: thursday,
                    friday: friday,
                    saturday: saturday,
                    sunday: sunday,
                    isLimitless: isLimitless,
                    ticketType: ticketType,
                },
                success: function (data) {
                    if (data.availability.availabilityType) {
                        let avCount = $('.avCount');
                        addedAvailability = data.availability;
                        $('.availabilities').append('<option value="' + data.availability.id + '">' + data.availability.name + '</option>');
                        $('.availabilities').eq(parseInt(avCount.val())).val(data.availability.id);
                        Materialize.toast('Availability is added successfully!', 4000, 'toast-success');
                        $('.dynamicLi').remove();
                        $('.dynamicDiv').remove();
                        if (isLimitless === '1') {
                            $('#limitlessTicket').click();
                        }
                        $('input[name="mondayHour1[]"]').val('');
                        $('input[name="tuesdayHour1[]"]').val('');
                        $('input[name="wednesdayHour1[]"]').val('');
                        $('input[name="thursdayHour1[]"]').val('');
                        $('input[name="fridayHour1[]"]').val('');
                        $('input[name="saturdayHour1[]"]').val('');
                        $('input[name="sundayHour1[]"]').val('');
                        $('#avName').val('');
                        globalItObject.it = 1;
                        $('button.addNewDateButton[data-id="' + globalItObject.it + '"]').attr('disabled', false);
                        $('input.dateRange' + globalItObject.it).attr('disabled', false);
                        $('.errorSpan').hide();
                        avCount.val(parseInt(avCount.val()) + 1);
                        $('.nextBtnForOpt').click();
                    } else {
                        if ($('#bladeName').val() !== 'productCheck') {
                            Materialize.toast(data.errors, 4000, 'toast-alert');
                        }
                    }
                },
                errors: function () {
                    if ($('#bladeName').val() !== 'productCheck') {
                        Materialize.toast('An error occured while adding Availability. Please check your data and try again!', 4000, 'toast-alert');
                    }
                }
            });
        });

        function refreshOptionModal() {
            $('.dynamicLi').remove();
            $('.dynamicDiv').remove();
            $('input[name="mondayHour1[]"]').val('');
            $('input[name="tuesdayHour1[]"]').val('');
            $('input[name="wednesdayHour1[]"]').val('');
            $('input[name="thursdayHour1[]"]').val('');
            $('input[name="fridayHour1[]"]').val('');
            $('input[name="saturdayHour1[]"]').val('');
            $('input[name="sundayHour1[]"]').val('');
            $('#avName').val('');
            globalItObject.it = 1;
            $('button.addNewDateButton[data-id="' + globalItObject.it + '"]').attr('disabled', false);
            $('.availabilities').val('');
            $('#radioMixedNo').click();
            $('.dynamicExtraAv').remove();
            $('#pricings').val('');
            $('.priceForm').show();
            $('#pricingTitle').val('');
            $('#opt_cut_time').val('');
            $('#opt_cut_time_date').val('');
            $('#opt_tour_duration').val('');
            $('#opt_tour_duration_date').val('');
            $('#pac-input').val('');
            $('#opt_meeting_point').val('');
            $('#opt_meeting_point_lat').val('');
            $('#opt_meeting_point_long').val('');
            $('#opt_title').val('');
            $('#opt_desc').val('');
            $('#opt_full_desc').val('');
            $('#minPerson').val('');
            $('#maxPerson').val('');
            $('#radioTime').click();
            $('.nextBtnForOpt').attr('data-step', 1);
            $('#step1Tab').click();
            $('#opt_button').hide();
            $('.errorSpan').hide();
            $('.avCount').val(0);
        }


        function setGuideInformation() {
            var json = [];
            $(".guide_information:checked").each(function () {
                json.push($(this).val());
            });

            if (json.length) {
                $("input[name='guide_information']").val(JSON.stringify(json));
            } else {
                $("input[name='guide_information']").val('');
            }
        }


        $(document).on('change', '#is-free-cancellation', function (event) {
            event.preventDefault();
            if ($(this).is(":checked")) {
                $("input[name='is_free_cancellation']").val("1");
            } else {
                $("input[name='is_free_cancellation']").val("0");
            }
        });


        $(document).on('change', '#skip-the-line', function (event) {
            event.preventDefault();
            if ($(this).is(":checked")) {
                $("input[name='skip_the_line']").val("1");
            } else {
                $("input[name='skip_the_line']").val("0");
            }
        });


        $(document).on('change', '#live-guide', function (event) {
            event.preventDefault();
            setGuideInformation();

        });


        $(document).on('change', '#audio-guide', function (event) {
            event.preventDefault();
            setGuideInformation();


        });


        function setCustomerTemplates() {
            var json = {};
            $("#customer-tab-content-wrap textarea").each(function (index, el) {

                json[$(this).attr("name")] = $(this).val();


            });

            $("input[name='customer_mail_templates']").val(JSON.stringify(json));
        }


        $(document).on('keyup', '#customer-tab-content-wrap textarea', function (event) {
            event.preventDefault();
            setCustomerTemplates();

        });


        $('.closeOptionModal').on('click', function () {
            refreshOptionModal();
        });
        $('#opt_button').click(function (e) {
            // Option Modal Last Step Validation
            let mpOrT = $('input[name="radioMPorT"]:checked').val();
            if (mpOrT === 'Meeting Point') {
                if ($('#pac-input').val() === '') {
                    $('.meetingPointErrorSpan').show();
                    return;
                }
            } else {
                if ($('#meetingPointDescInput').val() === '') {
                    $('.meetingPointDescErrorSpan').show();
                    return;
                }
            }
            //
            let customerMailTemplates = $('input[name="customer_mail_templates"]').val();
            let isFreeCancellation = $('input[name="is_free_cancellation"]').val();
            let isSkipTheLine = $('input[name="skip_the_line"]').val();
            let guideInformation = $('input[name="guide_information"]').val();
            let type = $('input[name="radioTime"]:checked').val();
            let minPerson = $('#minPerson').val();
            let maxPerson = $('#maxPerson').val();
            let desc = $('#opt_desc').val();
            let title = $('#opt_title').val();
            let meetingPoint = $('#opt_meeting_point').val();
            let meetingComment = $('#meetingComment').val();
            let meetingPointLat = $('.opt_meeting_point_lat').val();
            let meetingPointLong = $('.opt_meeting_point_long').val();
            let meetingPointDesc = $('#meetingPointDescInput').val();
            let addresses = $('input[name="addresses"]').val();
            let fullDesc = $('#opt_full_desc').val();
            let cutOfTime = $('#opt_cut_time').val();
            let cutOfTimeDate = $('#opt_cut_time_date').val();
            let tourDuration = $('#opt_tour_duration').val();
            let tourDurationDate = $('#opt_tour_duration_date').val();

            let guideTime = $('#opt_guide_time').val();
            let guideTimeType = $('#opt_guide_time_type').val();

            let cancelPolicyTime = $('#opt_cancel_policy_time').val();
            let cancelPolicyTimeType = $('#opt_cancel_policy_time_type').val();

            let pricings = $('#pricings').val();
            let isMixed = $('input[name="isMixed"]:checked').val();
            let productId = $('.productId').val();
            let availabilities = [];
            $('select[name="availabilities[]"] option:selected').each(function () {
                availabilities.push($(this).val());
            });
            let mobileBarcode = $('#mobile-barcode').is(":checked") ? 1 : 0;
            let included = $('#opt_included').val();
            let notIncluded = $('#opt_notIncluded').val();
            let knowBeforeYouGo = $('#opt_knowBeforeYouGo').val();
            let ticketTypes = $('#ticketTypes').val();

            let blockoutHours = [];
            $('.blockoutContainer .row').each(function() {
                if($(this).find('.months').val() || $(this).find('.days').val() || $(this).find('.hours').val())
                    blockoutHours.push({'months': $(this).find('.months').val(), 'days': $(this).find('.days').val(), 'hours': $(this).find('.hours').val()});
            });

            if (desc.length === 0) {
                if ($('#bladeName').val() !== 'productCheck') {
                    Materialize.toast('Option description can\'t be blank!', 4000, 'toast-alert');
                }
            } else {
                $.ajax({
                    method: 'POST',
                    url: '/option/store',
                    data: {
                        customerMailTemplates: customerMailTemplates,
                        isFreeCancellation: isFreeCancellation,
                        isSkipTheLine: isSkipTheLine,
                        guideInformation: guideInformation,
                        meetingComment: meetingComment,
                        type: type,
                        minPerson: minPerson,
                        maxPerson: maxPerson,
                        title: title,
                        description: desc,
                        meetingPoint: meetingPoint,
                        meetingPointLat: meetingPointLat,
                        meetingPointLong: meetingPointLong,
                        meetingPointDesc: meetingPointDesc,
                        addresses: addresses,
                        fullDesc: fullDesc,
                        cutOfTime: cutOfTime,
                        cutOfTimeDate: cutOfTimeDate,
                        tourDuration: tourDuration,
                        tourDurationDate: tourDurationDate,

                        guideTime: guideTime,
                        guideTimeType: guideTimeType,

                        cancelPolicyTime: cancelPolicyTime,
                        cancelPolicyTimeType: cancelPolicyTimeType,

                        pricings: pricings,
                        availabilities: availabilities,
                        isMixed: isMixed,
                        productId: productId,
                        mobileBarcode: mobileBarcode,
                        included: included,
                        notIncluded: notIncluded,
                        knowBeforeYouGo: knowBeforeYouGo,
                        ticketTypes: ticketTypes,
                        blockoutHours: blockoutHours,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        if (res.id) {
                            let optCount = $('.optCount');
                            addedOption = res;
                            $('.options').append('<option value="' + res.id + '">' + res.title + '</option>');
                            $('.options').eq(parseInt(optCount.val())).val(res.id).trigger('change');
                            ;
                            Materialize.toast('Option is added successfully!', 4000, 'toast-success');
                            refreshOptionModal();
                            $('.close').click();
                            optCount.val(parseInt(optCount.val()) + 1);
                            // New Option Part Scripts
                            let count = parseInt($('.productOptionCount').val()) + 1;
                            let block = '<li><a class="optionPills" data-id="option' + count + '" id="option' + count + 'Tab" href="#option' + count + '" data-toggle="tab" data-option-id="' + res.id + '">Option ' + count + '</a></li>';
                            $('.optionNavs').append(block);
                            $('.productOptionCount').val(count);
                            let productOptionIds = [];
                            if ($('.productOptionIds').val() !== '') {
                                productOptionIds = JSON.parse($('.productOptionIds').val());
                            }
                            productOptionIds.push(res.id);
                            $('.productOptionIds').val(JSON.stringify(productOptionIds));
                            if ($('.productOptionCount').val() !== '0') {
                                $('.editOptionsWrapper').show();
                            }
                        } else {
                            if ($('#bladeName').val() !== 'productCheck') {
                                Materialize.toast('Options can\'t have the same name. Please change name of this option!', 4000, 'toast-alert');
                            }
                        }
                    }
                });
            }
        });

        $('.addNewAvSelectBox').on('click', function () {
            let supplierId = $(this).data('supplier');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/availability/getAvailabilities',
                data: {
                    supplier_id: supplierId
                },
                success: function (data) {
                    let block = '<div class="form-group col-md-12 dynamicExtraAv">\n' +
                        '<select class="browser-default custom-select col-md-9 availabilities" name="availabilities[]" id="availabilities">\n' +
                        '<option value="" selected>Choose an Availability</option>\n';
                    $.each(data.availabilities, function (key, value) {
                        block += '<option value="' + value.id + '">' + value.name + '</option>\n';
                    });
                    block += '</select>\n' +
                        '<button class="btn btn-primary deleteNewAvSelectBox col-md-3">x</button>\n' +
                        '<span class="availabilitiesErrorSpan col s12" style="display: none!important; color: #ff0000;">You must choose an availability.</span>\n' +
                        '</div>';
                    $('.avPane').append(block);
                }
            });
        });

        $('body').on('click', '.deleteNewAvSelectBox', function () {
            $(this).parent().remove();
        });

        $('#radioMixedNo').on('click', function () {
            $('.addNewAvSelectBox').hide();
        });

        $('#radioMixedYes').on('click', function () {
            $('.addNewAvSelectBox').show();
        });

        $('#closeDraftModalButton').on('click', function () {
            window.location.href = '/';
        });

        $('.removeImage').on('click', function () {
            $this = $(this);
            let productId = $('.productId').val();
            let fileName = $(this).data('name');
            let fileID = $(this).data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/product/deletePhoto',
                data: {
                    productId: productId,
                    fileName: fileName,
                    fileID: fileID
                },
                success: function (data) {
                    if (data.success) {
                        $this.parent().parent().remove();
                    }
                }
            });
        });

        // $('.setImageAsCoverPhoto').on('click', function () {
        //
        //     var thisIs = $(this);
        //     let productId = $('.productId').val();
        //     let fileName = thisIs.data('name');
        //     let fileID = thisIs.data('id');
        //
        //
        //     if (thisIs.parent().hasClass('dz-error')) {
        //         thisIs.hide();
        //
        //     } else {
        //         $.ajax({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             type: 'POST',
        //             url: '/product/setAsCoverPhoto',
        //             data: {
        //                 fileID: fileID,
        //                 productId: productId,
        //                 fileName: fileName
        //             },
        //             success: function (data) {
        //                 $('.home-image-dz').each(function () {
        //                     $(this).attr('value', 0);
        //                     $(this).text('Set as Cover Photo');
        //                     $(this).removeClass('check-ok');
        //                 });
        //                 $('.home-image').each(function () {
        //                     var id_raw = $(this).attr('id');
        //                     var id = id_raw.split('-')[1];
        //                     if (fileID == id) {
        //                         $(this).removeClass('btn-default').addClass('btn-success');
        //                         $(this).attr('value', 1);
        //                     } else {
        //                         $(this).attr('value', 0);
        //                         $(this).removeClass('btn-success');
        //                         $(this).removeClass('btn-default');
        //                     }
        //
        //
        //                 });
        //                 thisIs.attr('value', 1);
        //                 thisIs.text('✔');
        //                 thisIs.addClass('check-ok');
        //
        //                 $('.coverPhotoNameSpan').html('Cover Photo: ' + data.fileName);
        //
        //             }
        //         });
        //     }
        //
        //
        // });

        $('.galleryModal').on('click', function () {
            getImageGallery(0);
        });

        $(document).on('change', '#galleryAttractions', function() {
            $('#galleryName').val('');
            getImageGallery(1);
        });

        $(document).on('click', '#galleryNameApply', function() {
            getImageGallery(1);
        });

        function getImageGallery(mode) {
            let category = $('#cities').val();
            let ownerId = $('.userId').val();
            let galleryAttraction = $('#galleryAttractions').val() ?? 1
            let galleryName = $('#galleryName').val() ?? ''
            console.log("gallery name: " + galleryName)
            $.ajax({
                method: 'POST',
                url: '/product/getImageGallery',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    category: category,
                    ownerId: ownerId,
                    galleryAttraction: galleryAttraction,
                    galleryName: galleryName
                },
                success: function (data) {
                    console.log(data);
                    let imagesOfCategory = data.imagesOfCategory;
                    let imagesUncategorized = data.imagesUncategorized;
                    let imageGalleryModalBody = $('#imageGalleryModalBody1');
                    if(mode == 0) {
                        imageGalleryModalBody.html('');
                        let block = '';
                        block += '<div class="col-md-12">';
                        block += '<ul class="nav nav-tabs" role="tablist">\n';
                        if (data.category) {
                            block += '<li role="presentation" class="active"><a href="#category" aria-controls="category" role="tab" data-toggle="tab">' + data.category + '</a></li>\n';
                        }
                        block += '<li role="presentation"><a href="#uncategorized" aria-controls="uncategorized" role="tab" data-toggle="tab">Uncategorized</a></li>\n';
                        block += '</ul>\n';


                        block += '<div class="tab-content" style="margin-top: 20px;">\n';
                        block += '<div role="tabpanel" class="tab-pane" id="uncategorized">\n';
                        let counter = 0;
                        imagesUncategorized.forEach(function (image, index) {
                            if (index % 3 === 0) {
                                block += '<div class="col-md-12">';
                            }
                            block += '<div class="col-md-4" style="padding-top: 20px; padding-bottom: 20px;">';
                            block += '<img data-id="' + image.id + '" style="cursor:pointer;" id="bigImageDiv" src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/' + image.src + '" />';
                            block += '</div>';
                            if (index % 3 === 2) {
                                block += '</div>\n';
                            } else if (imagesUncategorized.length == (index + 1)) {
                                block += '</div>\n';
                            }
                            counter++;
                        });


                        block += '</div>\n';

                        if (data.category != null) {
                            block += '<div role="tabpanel" class="tab-pane active" id="category">\n';

                            let attractions = data.attractions;
                            block += '<div>';
                            block += '<select id="galleryAttractions" class="select2 browser-default custom-select">';
                            for (var i = 0; i < attractions.length; i++) {
                                block += '<option value="' + attractions[i]["id"] + '">' + attractions[i]["name"] + '</option>';
                            }
                            block += '</select>';
                            block += '</div>';

                            block += `<div class="col-md-3" style="margin-top: 20px;">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-9">
                                    <input id="galleryName" name="galleryName" type="text" class="validate form-control">
                                </div>
                                <div class="col-md-3">
                                    <button id="galleryNameApply" class="btn btn-primary">Apply</button>
                                </div>
                            </div>`;

                            let counter = 0;
                            block += '<div id="onlyPhotoBlock">';
                            imagesOfCategory.forEach(function (image, index) {
                                if (index % 6 === 0) {
                                    block += '<div class="col-md-12">';
                                }
                                block += '<div class="col-md-2" style="padding-top: 20px; padding-bottom:20px;">';
                                block += '<img data-id="' + image.id + '" style="cursor:pointer;" id="bigImageDiv" src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/' + image.src + '" />';
                                block += '</div>';
                                if (index % 6 === 5) {
                                    block += '</div>\n';
                                } else if (imagesOfCategory.length == (index + 1)) {
                                    block += '</div>\n';
                                }
                                counter++;
                            });
                            block += '</div>';
                        }
                        block += '</div>';
                        block += '</div>';
                        imageGalleryModalBody.append(block);
                        $('#galleryAttractions').val(galleryAttraction)
                        $("#galleryAttractions").select2();
                    } else if(mode == 1) {
                        $('#onlyPhotoBlock').html('');
                        let block = '';
                        let counter = 0;

                        imagesOfCategory.forEach(function (image, index) {
                            if (index % 6 === 0) {
                                block += '<div class="col-md-12">';
                            }
                            block += '<div class="col-md-2" style="padding-top: 20px; padding-bottom:20px;">';
                            block += '<img data-id="' + image.id + '" style="cursor:pointer;" id="bigImageDiv" src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/' + image.src + '" />';
                            block += '</div>';
                            if (index % 6 === 5) {
                                block += '</div>\n';
                            } else if (imagesOfCategory.length == (index + 1)) {
                                block += '</div>\n';
                            }
                            counter++;
                        });

                        $('#onlyPhotoBlock').append(block);
                    }
                }
            });
        }

        $("#galleryModal").on('click', '#bigImageDiv', function () {
            $(this).toggleClass("selected");
        });

        $('.close.closeModal').on('click', function () {
            $('#galleryModal > .modal-body').html('');
        });

        $('#selectImagesForProductButton1').on('click', function () {
            let selectedImages = $('img.selected');
            let category = $('#cities').val();
            let attraction = $('#attractions').val();
            let imageIds = [];
            let productId = $('.productId').val();
            selectedImages.each(function (index, img) {
                imageIds.push(img.dataset.id);
            });
            $('#old-system-btn').click();
            $.ajax({
                method: 'POST',
                url: '/product/setImagesForProduct',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    category: category,
                    imageIds: imageIds,
                    productId: productId,
                    attractions: attraction
                },
                success: function (data) {
                    if (data.success) {
                        let dropzone = $('#my-awesome-dropzone');
                        let images = data.images;
                        let block = '';
                        images.forEach(function (image) {
                            block+='<div class="dz-preview dz-processing dz-image-preview">\n';
                            block+='<div class="dz-image">\n';
                            block+='<img data-dz-thumbnail="" alt="'+image.src+'" src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/'+ image.src + '">\n';
                            block+='</div>\n';
                            block+='<a class="dz-remove" href="javascript:undefined;" data-dz-remove="" data-id="'+image.id+'">Remove file</a>';
                            block+='<a style="cursor:pointer;" class="dz-cover home-image-dz setImageAsCoverPhoto " val="0" value="0" href="javascript:undefined;" data-dz-cover="" data-id="'+image.id+'" data-name="'+image.src+'">Set as Cover Photo</a>';
                            block+='</div>';

                        });
                        dropzone.append(block);
                        $('.closeGalleryModal').click();
                        $('button[type="submit"]').removeAttr('disabled');
                    } else {
                        if ($('#bladeName').val() !== 'productCheck') {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                        $('.closeGalleryModal').click();
                    }
                }
            });
        });

        $('body').on('click', '.removeImage', function () {
            let $this = $(this);
            let productId = $('.productId').val();
            let fileName = $(this).data('name');
            let fileID = $(this).data('id');
            let ownerId = $('.userId').val();
            let whichPage = $('.whichPage').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/product/deletePhoto',
                data: {
                    fileID: fileID,
                    productId: productId,
                    fileName: fileName,
                    ownerId: ownerId,
                    whichPage: whichPage
                },
                success: function (data) {
                    if (data.success) {
                        $this.parent().parent().remove();
                    }
                }
            });
        });

        $('body').on('click', '.setImageAsCoverPhoto', function () {


            var thisIs = $(this);
            let productId = $('.productId').val();
            let fileName = thisIs.data('name');
            let fileID = thisIs.data('id');


            if (thisIs.parent().hasClass('dz-error')) {
                thisIs.hide();

            } else {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/product/setAsCoverPhoto',
                    data: {
                        fileID: fileID,
                        productId: productId,
                        fileName: fileName
                    },
                    success: function (data) {
                        $('.home-image-dz').each(function () {
                            $(this).attr('value', 0);
                            $(this).text('Set as Cover Photo');
                            $(this).removeClass('check-ok');
                        });
                        $('.home-image').each(function () {
                            var id_raw = $(this).attr('id');
                            var id = id_raw.split('-')[1];
                            if (fileID == id) {
                                $(this).removeClass('btn-default').addClass('btn-success');
                                $(this).attr('value', 1);
                            } else {
                                $(this).attr('value', 0);
                                $(this).removeClass('btn-success');
                                $(this).removeClass('btn-default');
                            }


                        });
                        thisIs.attr('value', 1);
                        thisIs.text('✔');
                        thisIs.addClass('check-ok');

                        $('.coverPhotoNameSpan').html('Cover Photo: ' + data.fileName);

                    }
                });
            }



        });
        $('body').on('click', '.dz-remove', function () {
            var thisIs = $(this);
            var productId = $('.productId').val();
            var fileID = $(this).attr('data-id');
            var ownerId = $('.userId').val();
            var whichPage = "soft";

            if (fileID != undefined) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/product/deletePhoto',
                    data: {
                        productId: productId,
                        ownerId: ownerId,
                        whichPage: whichPage,
                        fileID: fileID
                    },
                    success: function (data) {
                        if (data.success) {
                            var cover_text = $('.coverPhotoNameSpan').text().split('Cover Photo: ')[1];
                            if (data.fileName == cover_text) {
                                $('.coverPhotoNameSpan').text('No Cover Photo Selected');
                            }
                            thisIs.parent().remove();

                            $('.delete-image').each(function () {
                                var id_raw = $(this).attr('id');
                                var id = id_raw.split('-')[1];
                                if (fileID == id) {
                                    $(this).parent().remove();
                                }
                            });

                        }

                    }
                });
            } else {
                thisIs.parent().remove();
                console.log('yeni yazılan: yüklenmediği halde silinen');
            }

        });


        /* New Option Scripts */

        var optionElements = [];

        $('body').on('click', '.optionPills', function () {
            let optionID = $(this).attr('data-option-id');
            let dataID = $(this).attr('data-id');
            $('.selectedOption').val(optionID);
            $('.editableOptionsTabPane').attr('id', dataID);
            $('.saveOptionPart').val('0');
            $('#verticalStep1A').click();
        });

        $('#deleteCurrentOption').on('click', function () {
            let productOptionIds = JSON.parse($('.productOptionIds').val());
            if (productOptionIds.length >= 2) {
                let optionID = $('.selectedOption').val();
                $("[data-option-id='" + optionID + "']").closest("li").remove();
                $('.optionPills').click();
                let leftOptionIds = jQuery.grep(productOptionIds, function (value) {
                    return value !== optionID;
                });
                $('.productOptionIds').val(JSON.stringify(leftOptionIds));

                $('#opt_select').find('option[value=' + optionID + ']').attr('disabled', false);
                $("#opt_select option:first-child").after(optionElements[optionID]);

                var count = $(".optionNavs li").length;
                console.log(count);

                $(".optionNavs li").each(function (index, el) {
                    $(this).find("a").text("Option " + (index + 1));
                    $(this).find("a").attr("data-id", "option" + (index + 1));
                    $(this).find("a").attr("id", "option" + (index + 1) + "Tab");
                    $(this).find("a").attr("href", "#option" + (index + 1));
                });


                $('.productOptionCount').val(parseInt(count));
                //$("#opt_select").prepend(optionElements[optionID]);  //*****************************************
                delete optionElements[optionID];
                $("#opt_select").select2();

            } else {
                if ($('#bladeName').val() !== 'productCheck') {
                    Materialize.toast('There should be at least one option!', 2000, 'toast-alert');
                }
                return;
            }
        });


        function editableSetGuideInformation() {
            var json = [];
            $(".editable_guide_information:checked").each(function () {
                json.push($(this).val());
            });

            if (json.length) {
                $("input[name='editable_guide_information']").val(JSON.stringify(json));
            } else {
                $("input[name='editable_guide_information']").val('');
            }
        }


        $(document).on('change', '#editable-is-free-cancellation', function (event) {
            event.preventDefault();
            if ($(this).is(":checked")) {
                $("input[name='editable_is_free_cancellation']").val("1");
            } else {
                $("input[name='editable_is_free_cancellation']").val("0");
            }
        });


        $(document).on('change', '#editable-skip-the-line', function (event) {
            event.preventDefault();
            if ($(this).is(":checked")) {
                $("input[name='editable_skip_the_line']").val("1");
            } else {
                $("input[name='editable_skip_the_line']").val("0");
            }
        });


        $(document).on('change', '#editable-live-guide', function (event) {
            event.preventDefault();
            editableSetGuideInformation();

        });


        $(document).on('change', '#editable-audio-guide', function (event) {
            event.preventDefault();
            editableSetGuideInformation();


        });


        function editableSetCustomerTemplates() {
            var json = {};
            $("#editable-customer-tab-content-wrap textarea").each(function (index, el) {

                json[$(this).attr("name")] = $(this).val();


            });

            $("input[name='editable_customer_mail_templates']").val(JSON.stringify(json));
        }


        $(document).on('keyup', '#editable-customer-tab-content-wrap textarea', function (event) {
            event.preventDefault();
            editableSetCustomerTemplates();

        });


        $('#saveCurrentOption').on('click', function () {
            saveOptionPart();
        });

        function saveOptionPart(autoNext = true) {
            let optionID = $('.selectedOption').val();
            let optionStep = $('.selectedOptionStep').val();
            let values = {};
            let addresses = $("#editableOptAddresses").val();
            let editable_customer_mail_templates = $("#editable_customer_mail_templates").val();

            if (optionStep === '1') {
                if ($('#editableOptTitle').val() === '') {
                    Materialize.toast('Title must be filled!', 2000, 'toast-alert');
                    return;
                }
                if ($('#editableOptDesc').val() === '') {
                    Materialize.toast('Description must be filled!', 2000, 'toast-alert');
                    return;
                }
                if ($('#editableIncluded').val() === '') {
                    Materialize.toast('Included must be filled!', 2000, 'toast-alert');
                    return;
                }
                if ($('#editableNotIncluded').val() === '') {
                    Materialize.toast('Not Included must be filled!', 2000, 'toast-alert');
                    return;
                }
                if ($('#editableKnowBeforeYouGo').val() === '') {
                    Materialize.toast('Know Before You Go must be filled!', 2000, 'toast-alert');
                    return;
                }
                values['title'] = $('#editableOptTitle').val();
                values['description'] = $('#editableOptDesc').val();
                values['included'] = $('#editableIncluded').val();
                values['notIncluded'] = $('#editableNotIncluded').val();
                values['knowBeforeYouGo'] = $('#editableKnowBeforeYouGo').val();
            }

            if (optionStep === '2') {
                if ($('#editableMinPerson').val() === '' || parseInt($('#editableMinPerson').val()) > parseInt($('#editableMaxPerson').val()) || parseInt($('#editableMinPerson').val()) < 1) {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Min Person must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                if ($('#editableMaxPerson').val() === '') {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Max Person must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                values['minPerson'] = $('#editableMinPerson').val();
                values['maxPerson'] = $('#editableMaxPerson').val();
                values['isFreeCancellation'] = $('input[name="editable_is_free_cancellation"]').val();
                values['isSkipTheLine'] = $('input[name="editable_skip_the_line"]').val();
                values['guideInformation'] = $('input[name="editable_guide_information"]').val();
                values['mobileBarcode'] = $('#editable-mobile-barcode').is(":checked") ? 1 : 0;
            }

            if (optionStep === '3') {
                if ($('#editableOptCutTime').val() === '') {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Cut of Time must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                if ($('#editableOptCutTimeDate').val() === '') {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Cut of Time Date must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                if ($('#editableOptTourDuration').val() === '') {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Tour Duration must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                if ($('#editableOptTourDurationDate').val() === '') {
                    if ($('#bladeName') !== 'productCheck') {
                        Materialize.toast('Tour Duration Date must be filled!', 2000, 'toast-alert');
                    }
                    return;
                }
                values['cutOfTime'] = $('#editableOptCutTime').val();
                values['cutOfTimeDate'] = $('#editableOptCutTimeDate').val();
                values['tourDuration'] = $('#editableOptTourDuration').val();
                values['tourDurationDate'] = $('#editableOptTourDurationDate').val();
                values['guideTime'] = $('#editableOptGuideTime').val();
                values['guideTimeType'] = $('#editableOptGuideTimeType').val();

                values['cancelPolicyTime'] = $('#editableCancelPolicyTime').val();
                values['cancelPolicyTimeType'] = $('#editableCancelPolicyTimeType').val();
            }

            if(optionStep === '4') {
                values['ticketTypes'] = $('#ticketTypesEditable').val();
                values['blockoutHours'] = [];
                $('.blockoutContainer .row').each(function() {
                    if($(this).find('.months').val() || $(this).find('.days').val() || $(this).find('.hours').val())
                        values['blockoutHours'].push({'months': $(this).find('.months').val(), 'days': $(this).find('.days').val(), 'hours': $(this).find('.hours').val()});
                });
            }

            if (optionStep === '6') {
                if ($('input[name="editableRadioMPorT"]:checked').val() === 'Meeting Point') {
                    values['meetingComment'] = $('#editableMeetingComment').val();
                    values['meetingPoint'] = $('#editableOptMeetingPoint').val();
                    values['meetingPointLat'] = $('#editableOptMeetingPointLat').val();
                    values['meetingPointLong'] = $('#editableOptMeetingPointLong').val();
                } else {
                    values['meetingPointDesc'] = $('#editableMeetingPointDescInput').val();
                }
            }

            if (optionStep === '7') {
                values['iterator'] = $('#contactInformationIteratorForEdit').val();
                let contactInformationFieldsTempArray = [];
                let contactInformationFieldsArray = [];
                let addresses;
                values['contactForAllTravelers'] = $('#contactForAllTravelersForEdit').is(":checked") ? 1 : 0;
                $('.contact-info-group').each(function () {
                    contactInformationFieldsTempArray.push({
                        'title': $(this).find('.contact-info-title').val().length > 0 ? $(this).find('.contact-info-title').val() : null,
                        'name': $(this).find('.contact-info-title').val().length > 0 ? $(this).find('.contact-info-title').val() : null,
                        'isRequired': $(this).find('.contact-info-checkbox').is(":checked") ? 1 : 0
                    });
                });
                contactInformationFieldsTempArray.forEach(function (e) {
                    if (e.title !== null) {
                        contactInformationFieldsArray.push(e);
                    }
                });

                values['contactInformationFieldsArray'] = contactInformationFieldsArray;


            }

            if (['1', '2', '3', '4', '6', '7'].includes(optionStep)) {
                $.ajax({
                    type: 'POST',
                    url: '/option/saveOptionPart',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        optionID: optionID,
                        optionStep: optionStep,
                        values: values,
                        addresses: addresses,
                        editable_customer_mail_templates: editable_customer_mail_templates
                    },
                    success: function (data) {

                        if (data.error) {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                            return false;
                        }


                        if (autoNext && ['1', '2', '3', '4', '6'].includes(optionStep)) {
                            let nextStep = parseInt(optionStep) + 1;
                            $('#verticalStep' + nextStep + 'A').click();
                        }
                        if ($('#bladeName').val() !== 'productCheck') {
                            Materialize.toast('Option is updated successfully!', 4000, 'toast-success');
                        }
                    }
                });
            }
        }


        jQuery.fn.toggleOption = function (show) {
            jQuery(this).toggle(show);
            if (show) {
                if (jQuery(this).parent('span.toggleOption').length)
                    jQuery(this).unwrap();
            } else {
                if (jQuery(this).parent('span.toggleOption').length == 0)
                    jQuery(this).wrap('<span class="toggleOption" style="display: none;" />');
            }
        };

        function IsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        var selectedIDS = IsJsonString($('.productOptionIds').val()) ? JSON.parse($('.productOptionIds').val()) : [];
        for (var i = 0; i < selectedIDS.length; i++) {
            console.log(selectedIDS[i]);
            $('#opt_select').find('option[value=' + selectedIDS[i] + ']').attr('disabled', true);
        }


        $('.addExistingOptionButton').on('click', function (e) {
            e.preventDefault();
            //var selectedIDS = JSON.parse($('.productOptionIds').val());

            //e.preventDefault();
            if ($('#opt_select').val() !== '') {
                let optionID = $('#opt_select').val();
                let count = parseInt($('.productOptionCount').val()) + 1;
                let block = '<li><a class="optionPills" id="option' + count + 'Tab" data-id="option' + count + '" href="#option' + count + '" data-toggle="tab" data-option-id="' + optionID + '">Option ' + count + '</a></li>';

                let productOptionIds = [];
                if ($('.productOptionIds').val() !== '' && $('.productOptionIds').val() !== '["null"]') {
                    productOptionIds = JSON.parse($('.productOptionIds').val());
                }

                if (productOptionIds.indexOf(optionID) !== -1) {
                    Materialize.toast('you have already chosen this option!', 4000, 'toast-alert');
                    return false;
                }

                $('.optionNavs').append(block);
                if ($('.optionPills').length == 1) {
                    $('.optionPills').eq(0).click();
                }

                $('.productOptionCount').val(count);

                productOptionIds.push(optionID);

                var cloneOption = $("#opt_select").find("option[value='" + optionID + "']").clone(true); // *************************************************************
                optionElements[optionID] = cloneOption;
                $("#opt_select").find("option[value='" + optionID + "']").remove();
                $("#opt_select").select2();

                $('.productOptionIds').val(JSON.stringify(productOptionIds));
                if ($('.productOptionCount').val() !== '0') {
                    $('.editOptionsWrapper').show();
                }
            } else {
                if ($('#bladeName').val() !== 'productCheck') {
                    Materialize.toast('You need to select an option!', 4000, 'toast-alert');
                }
                return;
            }
            /*for (var i = 0; i < selectedIDS.length; i++) {
                console.log(selectedIDS[i]);
                $('#opt_select').find('option[value='+selectedIDS[i]+']').attr('disabled', true);
            }*/
        });


        function reSetGuideAndSkip() {
            $("#editable-live-guide").prop("checked", false);
            $("#editable-audio-guide").prop("checked", false);
            $("#editable-skip-the-line").prop("checked", false);
            $("#editable-is-free-cancellation").prop("checked", false);
            var isFreeCancellation = $('input[name="editable_is_free_cancellation"]').val();
            var isSkipTheLineData = $('input[name="editable_skip_the_line"]').val();
            var guideInformationData = $('input[name="editable_guide_information"]').val();

            if (guideInformationData) {
                guideInformationData = JSON.parse(guideInformationData);


                if (guideInformationData.indexOf(("Live Guide")) !== -1) {
                    $("#editable-live-guide").prop("checked", true);
                }

                if (guideInformationData.indexOf(("Audio Guide")) !== -1) {
                    $("#editable-audio-guide").prop("checked", true);
                }
            }

            if (isSkipTheLineData == "1") {
                $("#editable-skip-the-line").prop("checked", true);
            }

            if (isFreeCancellation == "1") {
                $("#editable-is-free-cancellation").prop("checked", true);
            }

        }


        $('#verticalStep1A, #verticalStep2A, #verticalStep3A, #verticalStep4A, #verticalStep5A, #verticalStep6A, #verticalStep7A').on('click', function () {
            let id = $(this).attr('id');
            let part = parseInt(id.substring(12, 13));
            if ($('.saveOptionPart').val() === '1') {
                saveOptionPart(false);
            }
            $('.selectedOptionStep').val(part);
            setValuesToForm(part);
            $('.saveOptionPart').val('1');
        });

        function setValuesToForm(part) {
            let optionID = $('.selectedOption').val();
            $.ajax({
                type: 'POST',
                url: '/option/getOptionPart',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    optionID: optionID,
                    part: part
                },
                success: function (data) {
                    if (part === 1) {
                        $('#editableOptTitle').val(data.option.title);
                        $('#editableOptDesc').val(data.option.description);

                        tagifyEditableIncluded.removeAllTags();
                        let processDataIncludes = data.option.included;
                        if(processDataIncludes) {
                            processDataIncludes = processDataIncludes.split('|');
                            processDataIncludes = processDataIncludes.join('{}');
                            tagifyEditableIncluded.addTags(processDataIncludes);
                        }

                        tagifyEditableNotIncluded.removeAllTags();
                        let processDataNotIncluded = data.option.notIncluded;
                        if(processDataNotIncluded) {
                            processDataNotIncluded = processDataNotIncluded.split('|');
                            processDataNotIncluded = processDataNotIncluded.join('{}');
                            tagifyEditableNotIncluded.addTags(processDataNotIncluded);
                        }

                        tagifyEditableKnowBeforeYouGo.removeAllTags();
                        let processDataKnowBeforeYouGo = data.option.knowBeforeYouGo;
                        if(processDataKnowBeforeYouGo) {
                            processDataKnowBeforeYouGo = processDataKnowBeforeYouGo.split('|');
                            processDataKnowBeforeYouGo = processDataKnowBeforeYouGo.join('{}');
                            tagifyEditableKnowBeforeYouGo.addTags(processDataKnowBeforeYouGo);
                        }
                    }

                    if (part === 2) {
                        $('#editableMinPerson').val(data.option.minPerson);
                        $('#editableMaxPerson').val(data.option.maxPerson);
                        $('input[name="editable_is_free_cancellation"]').val(data.option.isFreeCancellation);
                        $('input[name="editable_skip_the_line"]').val(data.option.isSkipTheLine);
                        $('input[name="editable_guide_information"]').val(data.option.guideInformation);

                        if(data.option.mobileBarcode == 1) {
                            if(!$('#editable-mobile-barcode').is(':checked'))
                                $('#editable-mobile-barcode').click();
                        } else {
                            if($('#editable-mobile-barcode').is(':checked'))
                                $('#editable-mobile-barcode').click();
                        }
                        reSetGuideAndSkip();
                    }

                    if (part === 3) {
                        $('#editableOptCutTime').val(data.option.cutOfTime);
                        $('#editableOptCutTimeDate').val(data.option.cutOfTimeDate);
                        $('#editableOptTourDuration').val(data.option.tourDuration);
                        $('#editableOptTourDurationDate').val(data.option.tourDurationDate);

                        $('#editableOptGuideTime').val(data.option.guideTime);
                        $('#editableOptGuideTimeType').val(data.option.guideTimeType);

                        $('#editableCancelPolicyTime').val(data.option.cancelPolicyTime);
                        $('#editableCancelPolicyTimeType').val(data.option.cancelPolicyTimeType);
                    }

                    if (part === 4) {
                        $('#pricingAlertInfo').html('');
                        let pricing = data.pricing;

                        let block = '';
                        block += '<span class="col-md-12" style="font-size: 24px!important;">Pricing Name: ' + data.pricing.title + '</span>';
                        block += '<span class="col-md-12">If you want to edit this pricing please click edit button</span>';
                        block += '<a class="btn btn-primary pull-right" style="margin-top: 5%;" href="/pricing/' + data.pricing.id + '/edit" target="_blank">Edit</a>';

                        block += `
                        <div class="col-md-12">
                            <h4 style="margin-top: 20px;">Choose Ticket Type</h4>
                            <div class="form-group">
                                <select class="select2 browser-default custom-select" name="ticketTypesEditable" id="ticketTypesEditable">
                                    <option value="" selected>Not Selected</option> `;
                                    for(let i=0; i<data.ticketTypes.length; i++) {
                                        if(data.optionTicketTypes.includes(data.ticketTypes[i]["id"]))
                                            block += '<option value="'+data.ticketTypes[i]["id"]+'" selected>'+data.ticketTypes[i]["name"]+'</option>';
                                        else
                                            block += '<option value="'+data.ticketTypes[i]["id"]+'">'+data.ticketTypes[i]["name"]+'</option>';
                                    }
                            block += `
                                </select>
                            </div>
                        </div>`;

                        block += `
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4 style="margin-top: 40px;">Blockout Hours</h4>
                                    <hr>
                                    <div class="btn btn-primary addBlockoutBlock" style="background-color: #e34f2a;">+</div>
                                        <div class="blockoutContainer">`;
                                            for(let i=0; i<data.blockoutHours.length; i++) {
                                                block += `<div class="row">
                                                    <div class="col-md-2 s12">
                                                        <label>Months</label>
                                                            <select class="browser-default custom-select col-md-11 months" multiple>`;
                                                                @foreach(VariableController::returnMonths() as $key => $month)
                                                                    if(data.blockoutHours[i]["months"] && data.blockoutHours[i]["months"].includes('{{$key}}'))
                                                                        block += '<option value="{{$key}}" selected>{{$month}}</option>';
                                                                    else
                                                                        block += '<option value="{{$key}}">{{$month}}</option>';
                                                                @endforeach
                                                            block += `</select>
                                                    </div>
                                                    <div class="col-md-2 s12">
                                                        <label>Days</label>
                                                        <select class="browser-default custom-select col-md-11 days" multiple>`;
                                                            @foreach(VariableController::returnDays() as $day)
                                                                if(data.blockoutHours[i]["days"] && data.blockoutHours[i]["days"].includes('{{$day}}'))
                                                                    block += '<option value="{{$day}}" selected>{{$day}}</option>';
                                                                else
                                                                    block += '<option value="{{$day}}">{{$day}}</option>';
                                                            @endforeach
                                                        block += `</select>
                                                    </div>
                                                    <div class="col-md-2 s12">
                                                        <label>Hours</label>
                                                        <select class="browser-default custom-select col-md-11 hours" multiple>`;
                                                            if(data.blockoutHours[i]["hours"]) {
                                                                for(let j=0; j<data.blockoutHours[i]["hours"].length; j++) {
                                                                    block += '<option value="' + data.blockoutHours[i]["hours"][j] + '" selected>' + data.blockoutHours[i]["hours"][j] + '</option>';
                                                                }
                                                            }
                                                        block += `</select>
                                                    </div>
                                                    <div class="col-md-2 s12">
                                                        <div class="btn btn-primary addBlockoutHour" style="background-color: #1E8449";>+</div>
                                                        <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control blockoutHour" value="">
                                                    </div>
                                                    <div class="col-md-2 s12">
                                                        <div class="btn btn-primary removeBlockoutBlock" style="background-color: #FF3333";>x</div>
                                                    </div>
                                                </div>`;
                                            }
                                        block += `</div>
                                </div>
                            </div>`;

                        $('#pricingAlertInfo').append(block);
                        $('#ticketTypesEditable').select2();
                        $('.months').select2();
                        $('.days').select2();
                        $('.hours').select2();
                    }

                    if (part === 5) {
                        $('#availabilityAlertInfo').html('');
                        let availability = data.availability;
                        let minDate = data.minDate;
                        let maxDate = data.maxDate;

                        let block = '';
                        block += '<span class="col-md-12" style="font-size: 24px!important;">Availability Name: ' + data.availability.name + '</span>';
                        block += '<span class="col-md-12">If you want to edit this availability please click edit button</span>';
                        block += '<a class="btn btn-primary pull-right" style="margin-top: 5%;" href="/av/' + data.availability.id + '/edit" target="_blank">Edit</a>';

                        $('#availabilityAlertInfo').append(block);
                        $('#editableMinDate').val(minDate);
                        $('#editableMaxDate').val(maxDate);
                        $('#editableAvailabilityType').val(availability.availabilityType);
                        $('#editableAvailabilityId').val(availability.id);
                    }

                    if (part === 6) {
                        if (data.option.meetingComment !== null) {
                            let exploded = data.option.meetingComment.split('|');
                            let imploded = exploded.join('{}');
                            tagifyEditableMeetingComment.removeAllTags();
                            tagifyEditableMeetingComment.addTags(imploded);
                        }
                        $('#editableOptMeetingPoint').val(data.option.meetingPoint);
                        $('#editableOptMeetingPointLat').val(data.option.meetingPointLat);
                        $('#editableOptMeetingPointLong').val(data.option.meetingPointLong);

                        $('#editablepac-input').val(data.option.meetingPoint);
                        if (data.option.meetingPoint === null) {
                            $('#editableMeetingPointDesc').click();
                            $('#editableMeetingPointDescInput').val(data.option.meetingPointDesc);
                        }
                    }

                    if (part === 7) {
                        $("input[name='editable_customer_mail_templates']").val(data.option.customer_mail_templates);
                        $("input[name='editableOptAddresses']").val(data.option.addresses);
                        $("#selected-address").html('');
                        $("#editable-customer-tab-content-wrap textarea[name='en']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='fr']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='tr']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='es']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='ru']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='pt']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='nd']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='de']").val('');
                        $("#editable-customer-tab-content-wrap textarea[name='it']").val('');


                        if (data.option.customer_mail_templates && data.option.customer_mail_templates.trim()) {

                            var decoded_templates = JSON.parse(data.option.customer_mail_templates);

                            $("#editable-customer-tab-content-wrap textarea[name='en']").val(decoded_templates.en);
                            $("#editable-customer-tab-content-wrap textarea[name='fr']").val(decoded_templates.fr);
                            $("#editable-customer-tab-content-wrap textarea[name='tr']").val(decoded_templates.tr);
                            $("#editable-customer-tab-content-wrap textarea[name='es']").val(decoded_templates.es);
                            $("#editable-customer-tab-content-wrap textarea[name='ru']").val(decoded_templates.ru);
                            $("#editable-customer-tab-content-wrap textarea[name='pt']").val(decoded_templates.pt);
                            $("#editable-customer-tab-content-wrap textarea[name='nd']").val(decoded_templates.nd);
                            $("#editable-customer-tab-content-wrap textarea[name='de']").val(decoded_templates.de);
                            $("#editable-customer-tab-content-wrap textarea[name='it']").val(decoded_templates.it);


                        }


                        if (data.option.addresses && data.option.addresses.trim()) {


                            var decoded_address = JSON.parse(data.option.addresses);
                            var html = "";
                            decoded_address.forEach(function (element, index) {
                                html += `<div class="selected-item" data-address-title="${element.address_title}" data-address="${element.address}" data-address-lat="${element.address_lat}" data-address-lng="${element.address_lng}">


                               <div class="title-area"><input type="text" class="form-control address-title" value="${element.address_title}"></div>
                            <span>${element.address}: (${element.address_lat}) lng: (${element.address_lng}) </span>
                            <i class="delete-address-item pull-right icon-cz-trash"></i>
                          </div>`;
                            });

                            $("#selected-address").html(html);
                        }


                        $('#contactInformationDiv').html('');
                        let contactInformationFields = JSON.parse(data.option.contactInformationFields);
                        let contactForAllTraveler = data.option.contactForAllTravelers;
                        if (contactForAllTraveler === 1) {
                            $('#contactForAllTravelersForEdit').val(1);
                            $('#contactForAllTravelersForEdit').attr('checked', 'checked');
                        } else {
                            $('#contactForAllTravelersForEdit').val(0);
                        }
                        if (contactInformationFields === null) {
                            $('#contactInformationDiv').append(' ' +
                                '                   <div class="contact-info-group col-md-12">\n' +
                                '                        <div class="col-md-6 col-xs-12">\n' +
                                '                            <input class="contact-info-title" name="newContactInformation0" style="border: none!important;" id="newContactInformation0" placeholder="Add a name...">\n' +
                                '                        </div>\n' +
                                '                        <div class="col-md-6 col-xs-12">\n' +
                                '                            <input class="contact-info-checkbox" value="0" type="checkbox" id="isRequired0">\n' +
                                '                            <label for="isRequired0">is Required?</label>\n' +
                                '                        </div>\n' +
                                '                    </div>\n');
                        } else {
                            $('#contactInformationIteratorForEdit').val(contactInformationFields.length);
                            for (let i = 0; i < contactInformationFields.length; i++) {
                                $('#contactInformationDiv').append(' ' +
                                    '                   <div class="contact-info-group col-md-12">\n' +
                                    '                        <div class="col-md-6 col-xs-12">\n' +
                                    '                            <input value="' + contactInformationFields[i].title + '" class="contact-info-title" name="newContactInformation' + i + '" style="border: none!important;" id="newContactInformation' + i + '" placeholder="Add a name...">\n' +
                                    '                        </div>\n' +
                                    '                        <div class="col-md-6 col-xs-12">\n' +
                                    '                            <input class="contact-info-checkbox" value="0" type="checkbox" id="isRequired' + i + '">\n' +
                                    '                            <label for="isRequired' + i + '">is Required?</label>\n' +
                                    '                            <span style="margin-left: 10px;" class="btn btn-success btn-lg pull-right remove-row-field" onclick="removeRowField($(this))">Remove</span>\n' +
                                    '                        </div>\n' +
                                    '                    </div>\n');
                                if (contactInformationFields[i].isRequired === "1") {

                                    $('#isRequired' + i).val(1);
                                    $('#isRequired' + i).attr('checked', true);
                                } else {
                                    $('#isRequired' + i).val(0);
                                }
                            }
                        }
                    }
                }
            });
        }

        $('#editableMeetingPointDesc').on('click', function () {
            $('#editableMeetingPointPinDiv').hide();
            $('#editableMeetingPointDescDiv').show();
        });

        $('#editableMeetingPointPin').on('click', function () {
            $('#editableMeetingPointPinDiv').show();
            $('#editableMeetingPointDescDiv').hide();
        });

        ///////////////////////////////////////////

        $('.coll-body').show({'easing': 'swing'});

        $('#editableLimitlessTicket').on('change', function () {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        // Made a workaround for first remove minute hour function
        let editableWeekDaysArr = ['.editableMondayDiv', '.editableTuesdayDiv', '.editableWednesdayDiv', '.editableThursdayDiv', '.editableFridayDiv', '.editableSaturdayDiv', '.editableSundayDiv'];
        for (let b = 0; b < editableWeekDaysArr.length; b++) {
            editableWeekDaysArr[b] = editableWeekDaysArr[b] + '11';
            $('.collapsible.editableAvailability').on('click', editableWeekDaysArr[b] + ' .editableRemoveMinHourButton', function (e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        }

        // That workaround is starting from second. Basicly it is observing the globalItObject object and it fires the function below when it changed
        editableGlobalItObject.registerListener(function () {
            if (editableGlobalItObject.it > 1) {
                let editableWeekDaysArr = ['.editableMondayDiv', '.editableTuesdayDiv', '.editableWednesdayDiv', '.editableThursdayDiv', '.editableFridayDiv', '.editableSaturdayDiv', '.editableSundayDiv'];
                for (let b = 0; b < editableWeekDaysArr.length; b++) {
                    editableWeekDaysArr[b] = editableWeekDaysArr[b] + editableGlobalItObject.it + '1';
                    $('.collapsible.editableAvailability').on('click', editableWeekDaysArr[b] + ' .editableRemoveMinHourButton', function () {
                        $(this).parent().parent().remove();
                    });
                }

                let minDate = $('.editableDateRange' + (editableGlobalItObject.it - 1)).val().split('- ')[1];
                let minDateMoment = moment(minDate, "DD/MM/YYYY").add(1, 'd');
                let minDateString = moment(minDateMoment).format('DD/MM/YYYY');
                let maxDateMoment = moment(minDateString, "DD/MM/YYYY").add(1, 'months');
                let maxDateString = moment(maxDateMoment).format('DD/MM/YYYY');
                $('.editableDateRange' + editableGlobalItObject.it).daterangepicker({
                    'opens': 'center',
                    'drops': 'down',
                    'minDate': minDateString,
                    'startDate': minDateString,
                    'endDate': maxDateString,
                    "locale": {
                        "format": "DD/MM/YYYY",
                    }
                });
                $('#editableDateRangeText' + editableGlobalItObject.it).html($('.editableDateRange' + editableGlobalItObject.it).val());
            }

            $('input[name="editableDaterange[]"]').on('change', function () {
                let dateRangeText = $(this).parent().parent().parent().parent().parent().find('.editableDateRangeText');
                dateRangeText.html($(this).val());
            });
        });

        $('.collapsible.editableAvailability').on('click', '.editableRemoveDateButton', function (e) {
            e.preventDefault();
            $(this).parent().parent().parent().parent().parent().remove();
            $('button.editableAddNewDateButton[data-id="' + (editableGlobalItObject.it - 1) + '"]').attr('disabled', false);
            $('button.editableRemoveDateButton[data-id="' + (editableGlobalItObject.it - 1) + '"]').attr('disabled', false);
            $('input.editableDateRange' + (editableGlobalItObject.it - 1)).attr('disabled', false);
            editableGlobalItObject.it = editableGlobalItObject.it - 1;
            let collBody = $('.collapsible.editableAvailability').find('.coll-body');
            collBody.eq(editableGlobalItObject.it - 1).show({'easing': 'swing'});
        });

        function dayBlock(dayStr, dayStrCapitalized, date, type, hourFrom = '', hourTo = '', index = 0) {
            let block = '';
            block += '<div class="col-md-12 input-field s12">\n';
            block += '<div class="editableHourDivFrom col-md-2">\n' +
                '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editable' + dayStr + 'Hour" name="editable' + dayStr + 'Hour' + date + '[]" value="' + hourFrom + '">\n' +
                '     </div>';
            if (type === 'Operating Hours') {
                block += '<div class="editableHourDivToFor' + dayStrCapitalized + ' col-md-2">\n' +
                    '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editable' + dayStr + 'HourTo" name="editable' + dayStr + 'HourTo' + date + '[]" value="' + hourTo + '">\n' +
                    '     </div>';
            }
            if (index === 0) {
                if (type === 'Starting Time') {
                    block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                        '          <span onclick="editableAddMinHour(\'' + dayStr + '\', ' + date + ', 1)" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                        '     </div>\n';
                }
                block += '<div class="col-md-2">\n' +
                    '         <span onclick="copyToAllBelow(\'' + dayStr + '\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
                    '     </div>';

            } else {
                if (type === 'Starting Time') {
                    block += '<div class="editableRemoveDiv col-md-2"><a class="editableRemoveMinHourButton btn btn-danger">x</a></div>';
                }
            }
            block += '</div>\n';
            return block;
        }

        function initDateRanges(it, d) {
            let validFromTo = d['valid_from_to'];
            let validFrom = validFromTo.split(' - ')[0];
            let validTo = validFromTo.split(' - ')[1];
            let dateRangeIterator = parseInt(it) + 1;
            $('.editableDateRange' + dateRangeIterator).daterangepicker({
                'opens': 'center',
                'drops': 'down',
                'minDate': validFrom,
                'startDate': validFrom,
                'endDate': validTo,
                "locale": {
                    "format": "DD/MM/YYYY",
                }
            });
        }

    });

    // Add Hour/Minute Dynamically For Every Day
    function editableAddMinHour(day, date, time) {
        let dayStrCapitalized = capitalizeFirstLetter(day);
        let type = $('#editableAvailabilityType').val();
        let block = '<div class="col-md-12 input-field s12 editableDynamicDiv">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editable' + day + 'Hour" name="editable' + day + 'Hour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToFor' + day + ' col-md-2">\n' +
                '          <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editable' + day + 'HourTo" name="editable' + day + 'HourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableRemoveDiv col-md-2"><a class="editableRemoveMinHourButton btn btn-danger">x</a></div></div>';
        }
        $('.editable' + dayStrCapitalized + 'Div' + date + time).append(block);
    }

    // Add Dates Dynamically For Every Availability
    function editableAddDates(it) {
        let type = $('#editableAvailabilityType').val();
        if (it >= 1) {
            if (!validateDateTimes(type, it, true)) {
                return;
            }
        }
        if (it >= 2) {
            if (!validateDates(it, true)) {
                return;
            }
        }
        let date = it + 1;
        $('.coll-body').hide({'easing': 'swing'});
        let block = '<li class="editableDynamicLi">\n' +
            '            <div class="collapsible-header coll-head"><i class="icon-cz-booking"></i>Date Range | <span id="editableDateRangeText' + date + '" class="editableDateRangeText"></span></div>\n' +
            '                 <div style="border-bottom: 0!important;" class="collapsible-body coll-body">\n' +
            '                     <div class="editableNewDateDiv" style="height: 100%!important;">\n' +
            '                         <div class="editableNewDateWrapper">\n';
        block += ' <div class="form-group">\n' +
            '           <input type="text" class="editableDateRange' + date + '" name="editableDaterange[]" value="" />\n' +
            '      </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Monday</label>\n' +
            '          <div class="editableMondayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablemondayHour" name="editablemondayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForMonday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablemondayHourTo" name="editablemondayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '          <span onclick="editableAddMinHour(\'monday\', ' + date + ', 1)" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'monday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '        </div>\n' +
            '          </div>\n' +
            '       </div>\n';
        block += '<div class="form-group">\n' +
            '              <label>Tuesday</label>\n' +
            '               <div class="editableTuesdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editabletuesdayHour" name="editabletuesdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForTuesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editabletuesdayHourTo" name="editabletuesdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '          <span onclick="editableAddMinHour(\'tuesday\', ' + date + ', 1)" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'tuesday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '        </div>\n' +
            '         </div>\n' +
            '     </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Wednesday</label>\n' +
            '          <div class="editableWednesdayDiv' + date + '1">\n' +
            '              <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablewednesdayHour" name="editablewednesdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForWednesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablewednesdayHourTo" name="editablewednesdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '         <span onclick="editableAddMinHour(\'wednesday\', ' + date + ', 1);" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'wednesday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '             </div>\n' +
            '             </div>\n' +
            '        </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Thursday</label>\n' +
            '          <div class="editableThursdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablethursdayHour" name="editablethursdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForThursday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablethursdayHourTo" name="editablethursdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '         <span onclick="editableAddMinHour(\'thursday\', ' + date + ', 1);" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'thursday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '              </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Friday</label>\n' +
            '          <div class="editableFridayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablefridayHour" name="editablefridayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForFriday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablefridayHourTo" name="editablefridayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '          <span onclick="editableAddMinHour(\'friday\', ' + date + ', 1);" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'friday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Saturday</label>\n' +
            '          <div class="editableSaturdayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablesaturdayHour" name="editablesaturdayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForSaturday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablesaturdayHourTo" name="editablesaturdayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '           <span onclick="editableAddMinHour(\'saturday\', ' + date + ', 1);" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '      </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'saturday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '                 </div>\n' +
            '                 </div>\n' +
            '            </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Sunday</label>\n' +
            '          <div class="editableSundayDiv' + date + '1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="editableHourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="editablesundayHour" name="editablesundayHour' + date + '[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="editableHourDivToForSunday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="editablesundayHourTo" name="editablesundayHourTo' + date + '[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="editableAddMinHourButtonDiv col-md-2">\n' +
                '          <span onclick="editableAddMinHour(\'sunday\', ' + date + ', 1);" class="editableAddMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></span>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <span onclick="copyToAllBelow(\'sunday\', ' + date + ', true)" class="editableCopyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></span>\n' +
            '     </div>';
        block += '               </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="col-md-12">\n' +
            '          <span onclick="editableAddDates(' + date + ');" data-id="' + date + '" class="editableAddNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right">Add New Date Range</span>\n' +
            '          <button data-id="' + date + '" class="editableRemoveDateButton btn btn-primary">Remove Date Range</button>\n' +
            '     </div>\n';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n' +
            '    </li>';
        $('.collapsible.editableAvailability').append(block);
        let collBody = $('.collapsible.editableAvailability').find('.coll-body');
        collBody.eq(it).show({'easing': 'swing'});
        editableGlobalItObject.it = it + 1;
        $('button.editableAddNewDateButton[data-id="' + it + '"]').attr('disabled', true);
        $('button.editableRemoveDateButton[data-id="' + it + '"]').attr('disabled', true);
        $('input.editableDateRange' + it).attr('disabled', true);
    }

    function copyToAllBelow(day, date, editable = false) {
        let type = $('input[name="radioTime"]:checked').val();
        let editableText = '';
        let removeMinHourButtonText = 'removeMinHourButton';
        let dynamicDiv = 'dynamicDiv';
        let hourDivFrom = 'hourDivFrom';
        let removeDiv = 'removeDiv';
        if (editable) {
            type = $('#editableAvailabilityType').val();
            editableText = 'editable';
            removeMinHourButtonText = 'editableRemoveMinHourButton';
            dynamicDiv = 'editableDynamicDiv';
            hourDivFrom = 'editableHourDivFrom';
            removeDiv = 'editableRemoveDiv';
        }
        let weekDaysArr = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        let weekDayIndex = weekDaysArr.indexOf(day) + 1;
        let changableWeekDays = weekDaysArr.slice(weekDayIndex);
        changableWeekDays.forEach(function (changableWeekDay) {
            let block = '';
            $('.' + editableText + changableWeekDay + 'Div' + date + '1 .' + removeMinHourButtonText).click();
            let toBeCopied = $('input[name="' + editableText + day + 'Hour' + date + '[]"');
            $('input[name="' + editableText + changableWeekDay + 'Hour' + date + '[]"').first().val(toBeCopied.first().val());
            let toBeCopiedTo;
            if (type === 'Operating Hours') {
                toBeCopiedTo = $('input[name="' + editableText + day + 'HourTo' + date + '[]"');
                $('input[name="' + editableText + changableWeekDay + 'HourTo' + date + '[]"').first().val(toBeCopiedTo.first().val());
            }
            if (toBeCopied.length > 1) {
                for (let x = 1; x < toBeCopied.length; x++) {
                    block += '<div class="col-md-12 input-field s12 ' + dynamicDiv + '">\n';
                    block += '<div class="' + hourDivFrom + ' col-md-2">\n' +
                        '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="' + editableText + changableWeekDay + 'Hour" name="' + editableText + changableWeekDay + 'Hour' + date + '[]" value="' + toBeCopied.eq(x).val() + '">\n' +
                        '     </div>';
                    block += '<div class="' + removeDiv + ' col-md-2"><a class="' + removeMinHourButtonText + ' btn btn-danger">x</a></div></div>';
                }
            }
            if (editable) {
                let dayStrCapitalized = capitalizeFirstLetter(changableWeekDay);
                $('.editable' + dayStrCapitalized + 'Div' + date + '1').append(block);
            } else {
                $('.' + changableWeekDay + 'Div' + date + '1').append(block);
            }
        });
    }

    function validateSameHour(type, it, editable = false) {
        let editableText = '';
        if (editable) {
            editableText = 'editable';
        }
        let boolArr = [];
        for (let x = 1; x <= it; x++) {
            let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) != index);
            let mondayHours = $('input[name="' + editableText + 'mondayHour' + x + '[]"]');
            let mondays = [];
            mondayHours.each(function (index, item) {
                mondays.push(item.value);
            });
            let tuesdayHours = $('input[name="' + editableText + 'tuesdayHour' + x + '[]"]');
            let tuesdays = [];
            tuesdayHours.each(function (index, item) {
                tuesdays.push(item.value);
            });
            let wednesdayHours = $('input[name="' + editableText + 'wednesdayHour' + x + '[]"]');
            let wednesdays = [];
            wednesdayHours.each(function (index, item) {
                wednesdays.push(item.value);
            });
            let thursdayHours = $('input[name="' + editableText + 'thursdayHour' + x + '[]"]');
            let thursdays = [];
            thursdayHours.each(function (index, item) {
                thursdays.push(item.value);
            });
            let fridayHours = $('input[name="' + editableText + 'fridayHour' + x + '[]"]');
            let fridays = [];
            fridayHours.each(function (index, item) {
                fridays.push(item.value);
            });
            let saturdayHours = $('input[name="' + editableText + 'saturdayHour' + x + '[]"]');
            let saturdays = [];
            saturdayHours.each(function (index, item) {
                saturdays.push(item.value);
            });
            let sundayHours = $('input[name="' + editableText + 'sundayHour' + x + '[]"]');
            let sundays = [];
            sundayHours.each(function (index, item) {
                sundays.push(item.value);
            });

            boolArr.push(!(findDuplicates(mondays).length > 0 || findDuplicates(tuesdays).length > 0 ||
                findDuplicates(wednesdays).length > 0 || findDuplicates(thursdays).length > 0 ||
                findDuplicates(fridays).length > 0 || findDuplicates(saturdays).length > 0 ||
                findDuplicates(sundays).length > 0));
        }
        return !boolArr.includes(false);
    }

    function validateDateTimes(type, it, editable = false) {
        let editableText = '';
        if (editable) {
            editableText = 'editable';
        }
        if (type === 'Starting Time') {
            if (!validateSameHour(type, it, editable = false)) {
                Materialize.toast('You have same Hour & Minute! Please correct them!', 4000, 'toast-alert');
                return false;
            }
            if ($('input[name="' + editableText + 'mondayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'tuesdayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'wednesdayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'thursdayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'fridayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'saturdayHour' + it + '[]"]').val() === '' &&
                $('input[name="' + editableText + 'sundayHour' + it + '[]"]').val() === '') {
                Materialize.toast('You must fill at least one Hour and Minute!', 4000, 'toast-alert');
                return false;
            }
        } else {
            if (($('input[name="' + editableText + 'mondayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'mondayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'tuesdayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'tuesdayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'wednesdayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'wednesdayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'thursdayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'thursdayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'fridayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'fridayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'saturdayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'saturdayHourTo' + it + '[]"]').val() === '') &&
                ($('input[name="' + editableText + 'sundayHour' + it + '[]"]').val() === '' || $('input[name="' + editableText + 'sundayHourTo' + it + '[]"]').val() === '')) {
                Materialize.toast('You must fill at least one Hour and Minute!', 4000, 'toast-alert');
                return false;
            }
        }
        return true;
    }

    function validateDates(it, editable = false) {
        let dateRangeText = '.dateRange';
        if (editable) {
            dateRangeText = '.editableDateRange';
        }
        let endDateOld = moment($(dateRangeText + (it - 1)).val().split(' - ')[1], "DD/MM/YYYY").add(1, "d");
        let startDateNew = moment($(dateRangeText + it).val().split(' - ')[0], "DD/MM/YYYY");
        let a = endDateOld.format("DD/MM/YYYY");
        let b = startDateNew.format("DD/MM/YYYY");
        if (a !== b) {
            Materialize.toast('End date of old date picker and start date of new date picker must be consecutive!', 4000, 'toast-alert');
            return false;
        }
        return true;
    }

    //Capitalizes the first letter of string: monday -> Monday
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    $('.deleteOption').on('click', function () {
        let optId = $(this).attr('data-id');
        let optTitle = $(this).attr('data-title');
        let productId = $('.productId').val();
        let $this = $(this);
        let r = confirm("Would you really like to delete " + optTitle + "?");
        if (r) {
            $.ajax({
                method: 'POST',
                url: '/option/' + optId + '/detach',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    optId: optId,
                    productId: productId
                },
                success: function (data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, 'toast-success');
                        $this.parent().parent().remove();
                        let optionHtml = '<option value="' + optId + '">' + optTitle + '</option>';
                        $('#opt_select').append(optionHtml);
                    }
                }
            });
        } else {
            Materialize.toast("Don't worry! We didn't delete it :)", 4000, 'toast-warning');
        }
    });

    $('.editOption').on('click', function () {
        let optId = $(this).attr('data-id');
        let url = window.location.origin;
        window.location.href = url + '/option/' + optId + '/edit';
    });

    $('.productFileSpan').on('click', function () {
        let $this = $(this);
        let fileName = $this.attr('data-name');
        let productId = $('.productId').val();
        $.ajax({
            method: 'POST',
            url: '/product/deleteProductFile',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                productId: productId,
                fileName: fileName
            },
            success: function (data) {
                if (data.success) {
                    $this.remove();
                    Materialize.toast(data.success, 4000, 'toast-success');
                }
            }
        });
    });

    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">


    $(document).ready(function () {

        $(document).on('keyup', '#selected-address2 .address-title', function (event) {
            event.preventDefault();
            $(this).closest(".selected-item").attr("data-address-title", $(this).val());
            createAddressJson2();
            //console.log($(this).val());
        });

        $(document).on('keyup', '#selected-address .address-title', function (event) {
            event.preventDefault();
            $(this).closest(".selected-item").attr("data-address-title", $(this).val());
            createAddressJson();
            //console.log($(this).val());
        });


        $(document).on('click', '.delete-address-item', function (event) {
            event.preventDefault();
            $(this).closest(".selected-item").remove();
            createAddressJson();
            Materialize.toast('Address Removed From Address Area', 4000, 'toast-success');
        });

        $(document).on('click', '.delete-address-item2', function (event) {
            event.preventDefault();
            $(this).closest(".selected-item").remove();
            createAddressJson2();
            Materialize.toast('Address Removed From Address Area', 4000, 'toast-success');
        });
    });


    function createAddressJson() {
        var json = [];
        $("#selected-address .selected-item").each(function (index, el) {

            json.push({
                address_title: $(this).attr("data-address-title"),
                address: $(this).attr("data-address"),
                address_lat: $(this).attr("data-address-lat"),
                address_lng: $(this).attr("data-address-lng")
            });

        });
        if (json.length > 0) {
            $("input[name='editableOptAddresses']").val(JSON.stringify(json));
        } else {
            $("input[name='editableOptAddresses']").val("");
        }

    }

    function createAddressJson2() {
        var json = [];
        $("#selected-address2 .selected-item").each(function (index, el) {

            json.push({
                address_title: $(this).attr("data-address-title"),
                address: $(this).attr("data-address"),
                address_lat: $(this).attr("data-address-lat"),
                address_lng: $(this).attr("data-address-lng")
            });

        });
        if (json.length > 0) {
            $("input[name='addresses']").val(JSON.stringify(json));
        } else {
            $("input[name='addresses']").val("");
        }

    }

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var map2 = new google.maps.Map(document.getElementById('map2'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var card = document.getElementById('pac-card');
        var card2 = document.getElementById('editablepac-card');
        var input = document.getElementById('pac-input');
        var input2 = document.getElementById('editablepac-input');
        var types = document.getElementById('type-selector');
        var types2 = document.getElementById('editabletype-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');
        var strictBounds2 = document.getElementById('editablestrict-bounds-selector');

        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);
        map2.controls[google.maps.ControlPosition.TOP_RIGHT].push(card2);

        var autocomplete = new google.maps.places.Autocomplete(input);
        var autocomplete2 = new google.maps.places.Autocomplete(input2);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);
        autocomplete2.bindTo('bounds', map2);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);
        autocomplete2.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindow2 = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        var infowindowContent2 = document.getElementById('editableinfowindow-content');
        infowindow.setContent(infowindowContent);
        infowindow2.setContent(infowindowContent2);
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29)
        });
        var marker2 = new google.maps.Marker({
            map: map2,
            anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete.addListener('place_changed', function () {
            $('.pac-container.pac-logo').css('z-index', '1000!important');
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            $('.opt_meeting_point').val($('#pac-input').val());
            $('.opt_meeting_point_lat').val(place.geometry.location.lat());
            $('.opt_meeting_point_long').val(place.geometry.location.lng());

            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent.children['place-icon'].src = place.icon;
            infowindowContent.children['place-name'].textContent = place.name;
            infowindowContent.children['place-address'].textContent = address;
            infowindow.open(map, marker);
        });

        autocomplete2.addListener('place_changed', function () {
            $('.editablepac-container.pac-logo').css('z-index', '1000!important');
            infowindow2.close();
            marker2.setVisible(false);
            var place2 = autocomplete2.getPlace();
            $('.editableOptMeetingPoint').val($('#editablepac-input').val());
            $('.editableOptMeetingPointLat').val(place2.geometry.location.lat());
            $('.editableOptMeetingPointLong').val(place2.geometry.location.lng());

            if (!place2.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place2.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place2.geometry.viewport) {
                map2.fitBounds(place2.geometry.viewport);
            } else {
                map2.setCenter(place2.geometry.location);
                map2.setZoom(17);  // Why 17? Because it looks good.
            }
            marker2.setPosition(place2.geometry.location);
            marker2.setVisible(true);

            var address = '';
            if (place2.address_components) {
                address = [
                    (place2.address_components[0] && place2.address_components[0].short_name || ''),
                    (place2.address_components[1] && place2.address_components[1].short_name || ''),
                    (place2.address_components[2] && place2.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent2.children['editableplace-icon'].src = place2.icon;
            infowindowContent2.children['editableplace-name'].textContent = place2.name;
            infowindowContent2.children['editableplace-address'].textContent = address;
            infowindow2.open(map2, marker2);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function () {
                autocomplete.setTypes(types);
            });
        }

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener2(id, types2) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function () {
                autocomplete2.setTypes(types2);
            });
        }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);

        setupClickListener2('editablechangetype-all', []);
        setupClickListener2('editablechangetype-address', ['address']);
        setupClickListener2('editablechangetype-establishment', ['establishment']);
        setupClickListener2('editablechangetype-geocode', ['geocode']);

        document.getElementById('use-strict-bounds')
            .addEventListener('click', function () {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete.setOptions({strictBounds: this.checked});
            });

        document.getElementById('editableuse-strict-bounds')
            .addEventListener('click', function () {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete2.setOptions({strictBounds: this.checked});
            });


        var map3 = new google.maps.Map(document.getElementById('map3'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var card3 = document.getElementById('pac-card3');
        var input3 = document.getElementById('pac-input3');
        var types3 = document.getElementById('type-selector3');
        var strictBounds3 = document.getElementById('strict-bounds-selector3');

        map3.controls[google.maps.ControlPosition.TOP_RIGHT].push(card3);

        var autocomplete3 = new google.maps.places.Autocomplete(input3);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete3.bindTo('bounds', map3);

        // Set the data fields to return when the user selects a place.
        autocomplete3.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow3 = new google.maps.InfoWindow();
        var infowindowContent3 = document.getElementById('infowindow-content3');
        infowindow3.setContent(infowindowContent3);
        var marker3 = new google.maps.Marker({
            map: map3,
            anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete3.addListener('place_changed', function () {
            $('.pac-container3.pac-logo3').css('z-index', '1000!important');
            infowindow3.close();
            marker3.setVisible(false);
            var place3 = autocomplete3.getPlace();

            if (!place3.geometry) {
                place3 = {};
                place3.geometry = {};
                place3.geometry.location = {
                    lat: function () {
                        return "46.34567";
                    },
                    lng: function () {
                        return "38.334543";
                    }
                };
            }

            /* $('.opt_meeting_point').val($('#pac-input').val());
             $('.opt_meeting_point_lat').val(place.geometry.location.lat());
             $('.opt_meeting_point_long').val(place.geometry.location.lng());*/

            if (!place3.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place3.name + "'");
                return;
            }

            var address = $('#pac-input3').val();
            var html = `<div class="selected-item" data-address-title="Edit Address Title" data-address="${address}" data-address-lat="${place3.geometry.location.lat()}" data-address-lng="${place3.geometry.location.lng()}">

                        <div class="title-area"><input type="text" class="form-control address-title" value="Edit Address Title"></div>

                            <span>${address}: (${place3.geometry.location.lat()}) lng: (${place3.geometry.location.lng()}) </span>
                            <i class="delete-address-item pull-right icon-cz-trash"></i>
                        </div>`;

            $("#selected-address").append(html);
            createAddressJson();
            Materialize.toast('New Address set To Address Area', 4000, 'toast-success');

            // If the place has a geometry, then present it on a map.
            if (place3.geometry.viewport) {
                map3.fitBounds(place3.geometry.viewport);
            } else {
                map3.setCenter(place3.geometry.location);
                map3.setZoom(17);  // Why 17? Because it looks good.
            }
            marker3.setPosition(place3.geometry.location);
            marker3.setVisible(true);

            var address3 = '';
            if (place3.address_components) {
                address3 = [
                    (place3.address_components[0] && place3.address_components[0].short_name || ''),
                    (place3.address_components[1] && place3.address_components[1].short_name || ''),
                    (place3.address_components[2] && place3.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent3.children['place-icon'].src = place3.icon;
            infowindowContent3.children['place-name'].textContent = place3.name;
            infowindowContent3.children['place-address'].textContent = address3;
            infowindow3.open(map3, marker3);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener3(id, types) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function () {
                autocomplete3.setTypes(types);
            });
        }

        setupClickListener3('changetype-all3', []);
        setupClickListener3('changetype-address3', ['address']);
        setupClickListener3('changetype-establishment3', ['establishment']);
        setupClickListener3('changetype-geocode3', ['geocode']);

        document.getElementById('use-strict-bounds3')
            .addEventListener('click', function () {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete3.setOptions({strictBounds: this.checked});
            });


        var map4 = new google.maps.Map(document.getElementById('map4'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var card4 = document.getElementById('pac-card4');
        var input4 = document.getElementById('pac-input4');
        var types4 = document.getElementById('type-selector4');
        var strictBounds4 = document.getElementById('strict-bounds-selector4');

        map4.controls[google.maps.ControlPosition.TOP_RIGHT].push(card4);

        var autocomplete4 = new google.maps.places.Autocomplete(input4);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete4.bindTo('bounds', map4);

        // Set the data fields to return when the user selects a place.
        autocomplete4.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow4 = new google.maps.InfoWindow();
        var infowindowContent4 = document.getElementById('infowindow-content4');
        infowindow4.setContent(infowindowContent4);
        var marker4 = new google.maps.Marker({
            map: map4,
            anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete4.addListener('place_changed', function () {
            $('.pac-container4.pac-logo4').css('z-index', '1000!important');
            infowindow4.close();
            marker4.setVisible(false);
            var place4 = autocomplete4.getPlace();

            if (!place4.geometry) {
                place4 = {};
                place4.geometry = {};
                place4.geometry.location = {
                    lat: function () {
                        return "46.34567";
                    },
                    lng: function () {
                        return "38.334543";
                    }
                };
            }

            /* $('.opt_meeting_point').val($('#pac-input').val());
             $('.opt_meeting_point_lat').val(place.geometry.location.lat());
             $('.opt_meeting_point_long').val(place.geometry.location.lng());*/

            if (!place4.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place4.name + "'");
                return;
            }

            var address = $('#pac-input4').val();
            var html = `<div class="selected-item" data-address-title="Edit Address Title" data-address="${address}" data-address-lat="${place4.geometry.location.lat()}" data-address-lng="${place4.geometry.location.lng()}">

            <div class="title-area"><input type="text" class="form-control address-title" value="Edit Address Title"></div>
                            <span>${address}: (${place4.geometry.location.lat()}) lng: (${place4.geometry.location.lng()}) </span>
                            <i class="delete-address-item2 pull-right icon-cz-trash"></i>
                        </div>`;

            $("#selected-address2").append(html);
            createAddressJson2();
            Materialize.toast('New Address set To Address Area', 4000, 'toast-success');

            // If the place has a geometry, then present it on a map.
            if (place4.geometry.viewport) {
                map4.fitBounds(place4.geometry.viewport);
            } else {
                map4.setCenter(place4.geometry.location);
                map4.setZoom(17);  // Why 17? Because it looks good.
            }
            marker4.setPosition(place4.geometry.location);
            marker4.setVisible(true);

            var address4 = '';
            if (place4.address_components) {
                address4 = [
                    (place4.address_components[0] && place4.address_components[0].short_name || ''),
                    (place4.address_components[1] && place4.address_components[1].short_name || ''),
                    (place4.address_components[2] && place4.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent4.children['place-icon'].src = place4.icon;
            infowindowContent4.children['place-name'].textContent = place4.name;
            infowindowContent4.children['place-address'].textContent = address4;
            infowindow3.open(map4, marker4);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener4(id, types) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function () {
                autocomplete4.setTypes(types);
            });
        }

        setupClickListener4('changetype-all4', []);
        setupClickListener4('changetype-address4', ['address']);
        setupClickListener4('changetype-establishment4', ['establishment']);
        setupClickListener4('changetype-geocode4', ['geocode']);

        document.getElementById('use-strict-bounds4')
            .addEventListener('click', function () {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete4.setOptions({strictBounds: this.checked});
            });


    }

    $('#location').on('change', function () {
        let countryID = $(this).val();
        $('#cities').html('');
        $('.mdb-select').material_select('destroy');
        $('#attractions').val('');
        $('#attractions').html('');
        $('#attractions').append('<option value="" disabled selected>Choose product attraction</option>');
        $('.mdb-select').material_select();
        $.ajax({
            type: 'POST',
            url: '/product/create/getCities',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                countryID: countryID
            },
            success: function (data) {
                let cities = data.cities;
                $('#cities').append('<option value="">Choose a city</option>');
                for (let i = 0; i < cities.length; i++) {
                    $('#cities').append('<option value="' + cities[i] + '">' + cities[i] + '</option>');
                }
            }
        });
    });

    $('#cities').on('change', function () {
        let city = $('#cities').val();
        $.ajax({
            type: 'POST',
            url: '/getAttractionsByCity',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                city: city
            },
            success: function (data) {
                if (data.success) {
                    $('.mdb-select').material_select('destroy');
                    let block = '';
                    $('#attractions').html('');
                    $('#attractions').append('<option value="" disabled selected>Choose product attraction</option>');

                    let attractions = data.attractions;
                    attractions.forEach(function (item, index) {
                        block += '<option value="' + item.id + '">' + item.name + '</option>';
                    });
                    $('#attractions').append(block);
                    $('.mdb-select').material_select();
                }
            }
        });
    });

    $(document.body).on('dblclick', '.tag input', function () {
        $(this).removeAttr('readonly');
        $(this).focus();
        $(this).on('focusout', function () {
            $(this).attr('readonly', 'readonly');
        });
    });
</script>

<script>
    $('#addNewContactInformationLabel').on('click', function () {
        let iter = parseInt($('#contactInformationIteratorForEdit').val());
        $('#contactInformationDiv').append(' ' +
            '                   <div class="contact-info-group col-md-12">\n' +
            '                        <div class="col-md-6">\n' +
            '                            <input class="contact-info-title" name="newContactInformation' + iter + '" style="border: none!important;" id="newContactInformation' + iter + '" placeholder="Add a name...">\n' +
            '                        </div>\n' +
            '                        <div class="col-md-6">\n' +
            '                            <input class="contact-info-checkbox" name="isRequired' + iter + '" value="0" type="checkbox" id="isRequired' + iter + '">\n' +
            '                            <label for="isRequired' + iter + '">is Required?</label>\n' +
            '                            <span style="margin-left: 10px;" class="btn btn-success btn-lg pull-right remove-row-field" onclick="removeRowField($(this))">Remove</span>\n' +
            '                        </div>\n' +
            '                    </div>\n');
        let newIter = iter + 1;
        $('#contactInformationIteratorForEdit').val(newIter);
    });

    function removeRowField(el) {
        el.parent().parent().remove();
    }

</script>

<script>
    $(document).on('click', '.addBlockoutBlock', function() {
        let blockoutBlock = `<div class="row">
                                <div class="col-md-2 s12">
                                    <label>Months</label>
                                    <select class="browser-default custom-select col-md-11 months" multiple>
                                        @foreach(VariableController::returnMonths() as $key => $month)
                                            <option value="{{$key}}">{{$month}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 s12">
                                    <label>Days</label>
                                    <select class="browser-default custom-select col-md-11 days" multiple>
                                        @foreach(VariableController::returnDays() as $day)
                                            <option value="{{$day}}">{{$day}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 s12">
                                    <label>Hours</label>
                                    <select class="browser-default custom-select col-md-11 hours" multiple>

                                    </select>
                                </div>
                                <div class="col-md-2 s12">
                                    <div class="btn btn-primary addBlockoutHour" style="background-color: #1E8449";>+</div>
                                    <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control blockoutHour" value="">
                                </div>
                                <div class="col-md-2 s12">
                                    <div class="btn btn-primary removeBlockoutBlock" style="background-color: #FF3333";>x</div>
                                </div>
                             </div>`;
        $('.blockoutContainer').append(blockoutBlock);

        $('.months').select2();
        $('.days').select2();
        $('.hours').select2();
    });

    $(document).on('click', '.removeBlockoutBlock', function() {
        $(this).parent().parent().remove();
    });

    $(document).on('click', '.addBlockoutHour', function() {
        let hours = $(this).parent().prev().find('.hours').val();
        let hour = $(this).parent().find('.blockoutHour').val();
        if(hour) {
            $(this).parent().prev().find('.hours').append('<option value="' + hour + '" selected>' + hour + '</option>');
            $(this).parent().find('.blockoutHour').val('');
        }
    });
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIas7VCaChSga1sbY2J6TfgPVuDkMRmsk&language=en&libraries=places&callback=initMap"
    async defer></script>

{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAU3ap4LFLEeWvwOD0ycutnHRRkISzMq1Q&language=en&libraries=places&callback=initMap"
async defer></script> --}}
</html>
