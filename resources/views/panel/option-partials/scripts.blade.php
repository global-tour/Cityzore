</div>
</div>
</body>
<script src="{{asset('js/jquery-latest.min.js')}}"></script>
<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/admin/bootstrap.min.js')}}"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/admin/custom.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/3.22.1/tagify.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
<script src="{{asset('/js/waitme/waitMe.min.js')}}" defer></script>
<script src="{{ asset('js/wizard-form/tagify.min.js') }}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>

 <script type="text/javascript">



     $(document).ready(function() {
        $("#ticketTypes").select2();
        $('.months').select2();
        $('.days').select2();
        $('.hours').select2();

     $(document).on('click', '#save-tootbus-information-button', function(event) {
         event.preventDefault();



         var serialize_form_data = $("#tootbus-connection-form").serialize();

         if($("input[name='tootbus_product_id']").val().trim() === ""){
            Materialize.toast('Product ID Required!', 4000, 'toast-alert');
         }

                    $("#tootbus-modal .modal-body").waitMe({
                            effect : 'bounce',
                            text : '',
                            bg : 'rgba(255,255,255,0.7)',
                            color : '#f4364f',
                            maxSize : '',
                            waitTime : -1,
                            textPos : 'vertical',
                            fontSize : '',
                            source : '',
                            onClose : function() {}
                        });


         $.ajax({
             url: '{{url("option/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: serialize_form_data,
         })
         .done(function(response) {

            if(response.status == "1"){

             if($("#tootbus-modal-trigger-button").hasClass("btn-danger")){
                $("#tootbus-modal-trigger-button").removeClass("btn-danger").addClass("btn-success").text("Connected");
             }
              $("input[name='tootbus_id']").val(response.tootbus_id);
              $(".delete-button-area").html('<button data-id="'+response.tootbus_id+'" type="button" class="btn btn-danger active" id="delete-tootbus-information-button">Disconnect</button>');


                var decoded_tootbus_body = JSON.parse(response.tootbus_body);
                if(!decoded_tootbus_body["shortDescription"]){
                 decoded_tootbus_body["shortDescription"] = '';
                }

                if(!decoded_tootbus_body["title"]){
                 decoded_tootbus_body["title"] = decoded_tootbus_body["internalName"];
                }
                 if(!decoded_tootbus_body["galleryImages"]){
                    decoded_tootbus_body["galleryImages"] = [];
                }
                var html = '';
                html += '<p class="availability-type"><b>Type:</b><br>'+decoded_tootbus_body["availabilityType"]+'</p>';
                html += '<p class="title"><b>Title:</b><br>'+decoded_tootbus_body["title"]+'</p>';
                html += '<p class="short-description"><b>Short Description:</b><br>'+decoded_tootbus_body["shortDescription"]+'</p>';
                html += '<p class="units"><b>Active Units:</b><br>'+response.units.join(" - ")+'</p>';

                html += '<div class="row">';



                decoded_tootbus_body["galleryImages"].forEach(function(img, index){
                 html+= '<div class="col-md-4 col-sm-4 col-xs-12" style="margin-top: 10px;"><img style="width: 100%; height: 100px;" src="'+img["url"]+'"></div>';

                });


                 html += '</div>';




              $("#tootbus-option-data").append(html);



              Materialize.toast(response.message, 4000, 'toast-success');

            }else{
             Materialize.toast(response.message, 4000, 'toast-alert');
            }
         })
         .fail(function(XHR) {
             console.log(JSON.parse(XHR.responseText).message);
              Materialize.toast(JSON.parse(XHR.responseText).message, 6000, 'toast-alert');
         })
         .always(function() {
             console.log("complete");
             $("#tootbus-modal .modal-body").waitMe("hide");
         });




     });


      $(document).on('click', '#delete-tootbus-information-button', function(event) {
         event.preventDefault();
         var $this = $(this);
         var data_id = $this.attr("data-id");
         if(!confirm("Are You Sure You Want to Disconnect Api for This Option ?")){
          return false;
         }

             $("#tootbus-modal .modal-body").waitMe({
                            effect : 'bounce',
                            text : '',
                            bg : 'rgba(255,255,255,0.7)',
                            color : '#f4364f',
                            maxSize : '',
                            waitTime : -1,
                            textPos : 'vertical',
                            fontSize : '',
                            source : '',
                            onClose : function() {}
                        });

         $.ajax({
             url: '{{url("option/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {_token: '{{csrf_token()}}', action: "delete_tootbus_connection", data_id: data_id},
         })
         .done(function(response) {
            if(response.status == "1"){
              $this.remove();
              if($("#tootbus-modal-trigger-button").hasClass("btn-success")){
                $("#tootbus-modal-trigger-button").removeClass("btn-success").addClass("btn-danger").text("Not Connected");
             }
              $("input[name='tootbus_id']").val("");
              $("#tootbus-option-data").html("");

              Materialize.toast(response.message, 4000, 'toast-success');

            }else{
            Materialize.toast('An Error Occurred!', 4000, 'toast-alert');
            }

         })
         .fail(function() {
            Materialize.toast('An Error Occurred!', 4000, 'toast-alert');
             console.log("error");
         })
         .always(function() {
             console.log("complete");
             $("#tootbus-modal .modal-body").waitMe("hide");
         });




     });



     });
 </script>

 <script>
    let included = document.querySelector('#included');
    let notIncluded = document.querySelector('#notincluded');
    let knowBeforeYouGo = document.querySelector('#beforeyougo');

    let tagifyIncluded = new Tagify(included, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",
        //originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.' + tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyIncluded
        }
    })

    function onDragEndtagifyIncluded(elm) {
        tagifyIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#includedprocess', function() {
        var mainText = $('#includedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyIncluded.addTags(mainText);
        $('#includedarea').val('');
        $('#includedcollapsetrigger').click();
    });

    let tagifyNotIncluded = new Tagify(notIncluded, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",
        //originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.' + tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyNotIncluded
        }
    })

    function onDragEndtagifyNotIncluded(elm) {
        tagifyNotIncluded.updateValueByDOMTags()
    }

    $(document).on('click', '#notincludedprocess', function() {
        var mainText = $('#notincludedarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyNotIncluded.addTags(mainText);
        $('#notincludedarea').val('');
        $('#notincludedcollapsetrigger').click();
    });

    let tagifyKnowBeforeYouGo = new Tagify(knowBeforeYouGo, {
        keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace: "edit",
        //originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.' + tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyKnowBeforeYouGo
        }
    })

    function onDragEndtagifyKnowBeforeYouGo(elm) {
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }

    $(document).on('click', '#beforeyougoprocess', function() {
        var mainText = $('#beforeyougoarea').val();
        mainText = mainText.split('⚈');
        mainText = mainText.join('{}');
        tagifyKnowBeforeYouGo.addTags(mainText);
        $('#beforeyougoarea').val('');
        $('#beforeyougocollapsetrigger').click();
    });
</script>



<script>
    let meetingComment = document.querySelector('input[name=meetingComment]');
    let tagifyMeetingComment =  new Tagify(meetingComment, {
        keepInvalidTags     : true,
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    });
    DragSort(tagifyMeetingComment.DOM.scope, {
        selector: '.'+tagifyMeetingComment.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndmeetingComment
        }
    })
    function onDragEndmeetingComment(elm){
        tagifyMeetingComment.updateValueByDOMTags()
    }
</script>

<script>

    function checkEmptySelectForAv() {
        var append = true;
        $('.av-select').each(function (key, item) {
            if (item.value === "")
            {
                append = false;
            }
        });


        if (append) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/availability/getAvailabilities',
                data: {
                    //
                },
                success: function(data) {
                    let block = '<div class="form-group col-md-12 dynamicExtraAv">\n' +
                        '<select class="browser-default custom-select col-md-11 availabilities av-select" name="availabilities[]" id="availabilities">\n' +
                        '<option value="" selected>Choose an Availability</option>\n';
                    $.each( data.availabilities, function(key, value) {
                        block += '<option value="'+value.id+'">'+value.name+'</option>\n';
                    });
                    block += '</select>\n' +
                        '<button class="btn btn-primary deleteNewAvSelectBox col-md-1">x</button>\n' +
                        '<span class="availabilitiesErrorSpan col s12" style="display: none!important; color: #ff0000;">You must choose an availability.</span>\n' +
                        '</div>';
                    $('.select-av').append(block);
                }
            });
        }
        else {
            swal("Choose an Availability");
        }
    }

    $(function() {

        function setGuideInformation () {
            var json = [];
            $(".guide_information:checked").each(function(){
               json.push($(this).val());
            });

            if(json.length){
              $("input[name='guide_information']").val(JSON.stringify(json));
            }else{
              $("input[name='guide_information']").val('');
            }
        }



     $(document).on('change', '#is-free-cancellation', function(event) {
         event.preventDefault();
         if($(this).is(":checked")){
            $("input[name='is_free_cancellation']").val("1");
         }else{
            $("input[name='is_free_cancellation']").val("0");
         }
     });



     $(document).on('change', '#skip-the-line', function(event) {
         event.preventDefault();
         if($(this).is(":checked")){
            $("input[name='skip_the_line']").val("1");
         }else{
            $("input[name='skip_the_line']").val("0");
         }
     });


        $(document).on('change', '#live-guide', function(event) {
         event.preventDefault();
         setGuideInformation();

     });


           $(document).on('change', '#audio-guide', function(event) {
         event.preventDefault();
         setGuideInformation();


     });





        $('#meetingPointDesc').on('click', function() {
            $('#meetingPointPinDiv').hide();
            $('#meetingPointDescDiv').show();
        });
        $('#meetingPointPin').on('click', function() {
            $('#meetingPointPinDiv').show();
            $('#meetingPointDescDiv').hide();
        });

        let allNextBtnForOpt = $('.nextBtnForOpt');
        let allPrevBtnForOpt = $('.prevBtnForOpt');
        allNextBtnForOpt.click(function(e) {
            let step = $(this).attr('data-step');
            let nextStep = parseInt(step) + 1;
            let nextStepWizardForOpt = $('.option-setup-panel li a[href="#step'+nextStep+'"]');
            nextStepWizardForOpt.trigger('click');
            hideShowPrevNextButtons();
        });

        allPrevBtnForOpt.click(function(e) {
            let step = allNextBtnForOpt.attr('data-step');
            let prevStep = parseInt(step) - 1;
            let prevStepWizardForOpt = $('.option-setup-panel li a[href="#step'+prevStep+'"]');
            prevStepWizardForOpt.trigger('click');
            hideShowPrevNextButtons();
        });

        function hideShowPrevNextButtons() {
            let allNextBtnForOpt = $('.nextBtnForOpt');
            let allPrevBtnForOpt = $('.prevBtnForOpt');
            let saveUpdateBtnForOpt = $('.saveUpdateBtnForOpt');
            parseInt(allNextBtnForOpt.attr('data-step')) === 1 ? allPrevBtnForOpt.hide() : allPrevBtnForOpt.show();
            parseInt(allNextBtnForOpt.attr('data-step')) === 5 ? allNextBtnForOpt.hide() : allNextBtnForOpt.show();
            parseInt(allNextBtnForOpt.attr('data-step')) === 5 ? saveUpdateBtnForOpt.show() : saveUpdateBtnForOpt.hide();
        }

        $('[data-step=2]').click(function(){return false;}).addClass("text-muted");
        $('[data-step=3]').click(function(){return false;}).addClass("text-muted");
        $('[data-step=4]').click(function(){return false;}).addClass("text-muted");
        $('[data-step=5]').click(function(){return false;}).addClass("text-muted");

        $('#step1Tab').on('click', function() {
            $('.nextBtnForOpt').attr('data-step', 1);
            hideShowPrevNextButtons();
        });

        let step2 = $('#step2Tab');
        let step3 = $('#step3Tab');
        let step4 = $('#step4Tab');
        let step5 = $('#step5Tab');

        step2.on('click', function() {
            let optTitle = $('#opt_title');
            let optTitleErrorSpan = $('.opt_titleErrorSpan');
            let optDesc = $('#opt_desc');
            let optDescErrorSpan = $('.opt_descErrorSpan');
            let optFullDesc = $('#opt_full_desc');
            let optFullDescErrorSpan = $('.opt_full_descErrorSpan');
            if (optTitle.val() === '') {
                optTitleErrorSpan.show();
            } else {
                optTitleErrorSpan.hide();
            }
            if (optDesc.val() === '') {
                optDescErrorSpan.show();
            } else {
                optDescErrorSpan.hide();
            }
            if (optFullDesc.val() === '') {
                optFullDescErrorSpan.show();
            } else {
                optFullDescErrorSpan.hide();
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

            if (optTitle.val() !== '' && optDesc.val() !== '' && optFullDesc.val() !== '' && $('#included').val() !== '' && $('#notincluded').val() !== '' && $('#beforeyougo').val() !== '') {
                step2.removeClass('text-muted');
                step2.removeAttr('disabled');
                step2.tab('show');
                $('.nextBtnForOpt').attr('data-step', 2);
                hideShowPrevNextButtons();
            }
        });

        step3.on('click', function() {
            let minPerson = $('#minPerson');
            let maxPerson = $('#maxPerson');
            let minMaxPersonErrorSpan = $('.minMaxPersonErrorSpan');
            if ((minPerson.val() === '') || parseInt(minPerson.val()) > parseInt(maxPerson.val()) || parseInt(minPerson.val()) < 1) {
                minMaxPersonErrorSpan.show();
            } else {
                minMaxPersonErrorSpan.hide();
                step3.removeClass('text-muted');
                step3.removeAttr('disabled');
                step3.tab('show');
                $('.nextBtnForOpt').attr('data-step', 3);
                hideShowPrevNextButtons();
            }
        });

        step4.on('click', function() {
            let optCutTime = $('#opt_cut_time');
            let optCutTimeDate = $('#opt_cut_time_date');
            let optCutTimeErrorSpan = $('.opt_cut_timeErrorSpan');
            let optTourDuration = $('#opt_tour_duration');
            let optTourDurationDate = $('#opt_tour_duration_date');
            let optTourDurationErrorSpan = $('.opt_tour_durationErrorSpan');

            let optGuideTime = $('#opt_guide_time');
            let optGuideTimeType = $('#opt_guide_time_type');


            let cancelPolicyTime = $('#opt_cancel_policy_time');
            let cancelPolicyTimeType = $('#opt_cancel_policy_time_type');
            let optCancelPolicyErrorSpan = $('.opt_cancel_policy_ErrorSpan');


            let pacInput = $('#pac-input');
            let meetingPointErrorSpan = $('.meetingPointErrorSpan');
            let meetingPointDescInput = $('#meetingPointDescInput');
            let meetingPointDescErrorSpan = $('.meetingPointDescErrorSpan');
            let mpOrT = $('input[name="radioMPorT"]:checked').val();
            let isMPorTValid = false;
            if (mpOrT === 'Meeting Point') {
                if (pacInput.val() === '') {
                    meetingPointErrorSpan.show();
                } else {
                    isMPorTValid = true;
                    meetingPointErrorSpan.hide();
                }
            } else {
                if (meetingPointDescInput.val() === '') {
                    meetingPointDescErrorSpan.show();
                } else {
                    isMPorTValid = true;
                    meetingPointDescErrorSpan.hide();
                }
            }
            if (optCutTime.val() === '' || optCutTimeDate.val() === '') {
                optCutTimeErrorSpan.show();
            } else {
                optCutTimeErrorSpan.hide();
            }
            if (optTourDuration.val() === '' || optTourDurationDate.val() === '') {
                optTourDurationErrorSpan.show();
            } else {
                optTourDurationErrorSpan.hide();
            }


            if (cancelPolicyTime.val() === '' || cancelPolicyTimeType.val() === '') {
                optCancelPolicyErrorSpan.show();
            } else {
                optCancelPolicyErrorSpan.hide();
            }


            if (isMPorTValid && optCutTime.val() !== '' && optCutTimeDate.val() !== '' && optTourDuration.val() !== '' && optTourDurationDate.val() !== '' && cancelPolicyTime.val() !== '' && cancelPolicyTimeType.val() ) {
                step4.removeClass('text-muted');
                step4.removeAttr('disabled');
                step4.tab('show');
                $('.nextBtnForOpt').attr('data-step', 4);
                hideShowPrevNextButtons();
            }
        });

        step5.on('click', function() {
            if ($('#pricings').val() !== '' && $('#availabilities').val() !== '') {
                step5.removeClass('text-muted');
                step5.removeAttr('disabled');
                step5.tab('show');
                $('.nextBtnForOpt').attr('data-step', 5);
                hideShowPrevNextButtons();
            }
        });

        function setCustomerTemplates(){
            var json = {};
            $("#mail textarea").each(function(index, el) {
                json[$(this).attr("name")] = $(this).val();
            });
            $("input[name='customer_mail_templates']").val(JSON.stringify(json));

            json = {};
            $("#whatsapp textarea").each(function(index, el) {
                json[$(this).attr("name")] = $(this).val();
            });
            $("input[name='customer_whatsapp_templates']").val(JSON.stringify(json));
        }


        $(document).on('keyup', '#customer-tab-content-wrap textarea', function(event) {
            event.preventDefault();
            setCustomerTemplates();

        });

        $('#radioMixedNo').on('click', function() {
            $('.addNewAvSelectBox').hide();
        });

        $('#radioMixedYes').on('click', function() {
            $('.addNewAvSelectBox').show();
        });

        $('.addNewAvSelectBox').on('click', function() {
            checkEmptySelectForAv();
        });

        $('body').on('click', '.deleteNewAvSelectBox', function() {
            $(this).parent().remove();
        });

        $('.saveUpdateBtnForOpt').click(function(e) {
            let action = $('.saveUpdateBtnForOpt').attr('data-action');
            let url = action === 'save' ? 'Store' : 'Update';
            let addedOrUpdated = action === 'save' ? 'added' : 'updated';
            let selectedAvailabilities = $('select[name="availabilities[]"] option:selected');
            // Option Form Last Step Validation
            if ($('#pricings').val() === '') {
                $('.pricingsErrorSpan').show();
                return;
            }
            if ($('#availabilities').val() === '') {
                $('.availabilitiesErrorSpan').show();
                return;
            } else {
                let availabilities = [];
                selectedAvailabilities.each(function() {
                    if ($(this).val() !== "")
                    {
                        availabilities.push($(this).val());
                    }
                });
                if (availabilities.length > 1) {
                    if (availabilities.every( (val, i, arr) => val === arr[0] )) {
                        Materialize.toast('Same availability can not be selected twice!', 4000, 'toast-alert');
                        return;
                    }
                    if (availabilities.includes('')) {
                        Materialize.toast('You must select all availabilities!', 4000, 'toast-alert');
                        return;
                    }
                }
            }
            //

            let customerMailTemplates = $('input[name="customer_mail_templates"]').val();
            let customerWhatsAppTemplates = $('input[name="customer_whatsapp_templates"]').val();
            let isFreeCancellation = $('input[name="is_free_cancellation"]').val();
            let isSkipTheLine = $('input[name="skip_the_line"]').val();
            let guideInformation = $('input[name="guide_information"]').val();
            let pageID = $('input[name="pageID"]').val();
            let optionId = $('#optionId').val();
            let title = $('#opt_title').val();
            let desc = $('#opt_desc').val();
            let fullDesc = $('#opt_full_desc').val();
            let minPerson = $('#minPerson').val();
            let maxPerson = $('#maxPerson').val();
            let meetingPoint = $('#opt_meeting_point').val();
            let meetingComment = $('input[name=meetingComment]').val();
            let meetingPointLat = $('.opt_meeting_point_lat').val();
            let meetingPointLong = $('.opt_meeting_point_long').val();
            let meetingPointDesc = $('#meetingPointDescInput').val();
            let addresses = $('input[name=addresses]').val();
            let cutOfTime =  $('#opt_cut_time').val();
            let cutOfTimeDate =  $('#opt_cut_time_date').val();
            let tourDuration = $('#opt_tour_duration').val();
            let tourDurationDate = $('#opt_tour_duration_date').val();
             let guideTime = $('#opt_guide_time').val();
             let guideTimeType = $('#opt_guide_time_type').val();

             let cancelPolicyTime = $('#opt_cancel_policy_time').val();
             let cancelPolicyTimeType = $('#opt_cancel_policy_time_type').val();



            let pricings = $('#pricings').val();
            let isMixed = $('input[name="isMixed"]:checked').val();
            let iterator = $('#contactInformationIterator').val();
            let contactInformationFieldsTempArray = [];
            let contactInformationFieldsArray = [];
            let contactForAllTravelers = $('#contactForAllTravelers').is(":checked") ? 1 : 0;
            $('.contact-info-group').each(function() {
                contactInformationFieldsTempArray.push({
                    'title': $(this).find('.contact-info-title').val().length > 0 ? $(this).find('.contact-info-title').val() : null,
                    'name': $(this).find('.contact-info-title').val().length > 0 ? $(this).find('.contact-info-title').val() : null,
                    'isRequired': $(this).find('.contact-info-checkbox').is(":checked") ? 1: 0
                });
            });

            contactInformationFieldsTempArray.forEach(function(e) {
                if (e.title !== null) {
                    contactInformationFieldsArray.push(e);
                }
            });

            let availabilities = [];
            selectedAvailabilities.each(function() {
                if ($(this).val() !== "")
                {
                    availabilities.push($(this).val());
                }
            });

            let mobileBarcode = $('#mobile-barcode').is(":checked") ? 1 : 0;
            let included = $('#included').val();
            let notIncluded = $('#notincluded').val();
            let knowBeforeYouGo = $('#beforeyougo').val();
            let ticketTypes = $('#ticketTypes').val();

            let blockoutHours = [];
            $('.blockoutContainer .row').each(function() {
                if($(this).find('.months').val() || $(this).find('.days').val() || $(this).find('.hours').val())
                    blockoutHours.push({'months': $(this).find('.months').val(), 'days': $(this).find('.days').val(), 'hours': $(this).find('.hours').val()});
            });

            if (confirm("Are you sure for updating this option?")) {
                $.ajax({
                    method: 'POST',
                    url: '/option/option'+url,
                    data: {
                        customerMailTemplates: customerMailTemplates,
                        customerWhatsAppTemplates: customerWhatsAppTemplates,
                        isFreeCancellation: isFreeCancellation,
                        isSkipTheLine: isSkipTheLine,
                        guideInformation: guideInformation,
                        pageID: pageID,
                        optionId: optionId,
                        title: title,
                        description: desc,
                        fullDesc: fullDesc,
                        minPerson: minPerson,
                        maxPerson: maxPerson,
                        meetingPoint: meetingPoint,
                        meetingComment: meetingComment,
                        meetingPointLat: meetingPointLat,
                        meetingPointLong: meetingPointLong,
                        meetingPointDesc: meetingPointDesc,
                        addresses: addresses,
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
                        iterator: iterator,
                        contactInformationFieldsArray: contactInformationFieldsArray.length > 0 ? contactInformationFieldsArray : null,
                        contactForAllTravelers: contactForAllTravelers,
                        mobileBarcode: mobileBarcode,
                        included: included,
                        notIncluded: notIncluded,
                        knowBeforeYouGo: knowBeforeYouGo,
                        ticketTypes: ticketTypes,
                        blockoutHours: blockoutHours,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.id) {
                            let urlParams = new URLSearchParams(window.location.search);
                            let pageID = urlParams.get('page') ?? 1;
                            //let pageID = res.pageID;
                            Materialize.toast('Option is '+addedOrUpdated+' successfully! You will be redirected to dashboard in 3 seconds', 4000, 'toast-success');
                            window.location.href = '/option?page='+pageID;
                        } else {
                            Materialize.toast('Options can\'t have the same name. Please change name of this option!', 4000, 'toast-alert');
                        }
                    },
                    errors: function() {
                        Materialize.toast('Options can\'t have the same name. Please change name of this option!', 4000, 'toast-alert');
                    }
                });
            } else {
                Materialize.toast(title+' is not updated yet! If you want to update it please click to "Yes" button.', 4000, 'toast-alert');
            }
        });
    });
</script>
<script>

    $(document).ready(function() {

        $(document).on('keyup', '#selected-address .address-title', function(event) {
            event.preventDefault();
            $(this).closest(".selected-item").attr("data-address-title", $(this).val());
            createAddressJson();
            //console.log($(this).val());
        });


        $(document).on('click', '.delete-address-item', function(event) {
            event.preventDefault();
            $(this).closest(".selected-item").remove();
            createAddressJson();
            Materialize.toast('Address Removed From Address Area', 4000, 'toast-success');
        });
    });

    function createAddressJson(){
      var json = [];
    $("#selected-address .selected-item").each(function(index, el) {

     json.push({address_title: $(this).attr("data-address-title"), address: $(this).attr("data-address"), address_lat: $(this).attr("data-address-lat"), address_lng: $(this).attr("data-address-lng")});

    });
    if(json.length > 0){
        $("input[name='addresses']").val(JSON.stringify(json));
    }else{
         $("input[name='addresses']").val("");
    }

    }
    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');

        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete.addListener('place_changed', function() {
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

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function() {
                autocomplete.setTypes(types);
            });
        }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);

        document.getElementById('use-strict-bounds')
            .addEventListener('click', function() {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete.setOptions({strictBounds: this.checked});
            });























       var map2 = new google.maps.Map(document.getElementById('map2'), {
            center: {lat: 48.858093, lng: 2.294694},
            zoom: 13
        });
        var card2 = document.getElementById('pac-card2');
        var input2 = document.getElementById('pac-input2');
        var types2 = document.getElementById('type-selector2');
        var strictBounds2 = document.getElementById('strict-bounds-selector2');

        map2.controls[google.maps.ControlPosition.TOP_RIGHT].push(card2);

        var autocomplete2 = new google.maps.places.Autocomplete(input2);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete2.bindTo('bounds', map2);

        // Set the data fields to return when the user selects a place.
        autocomplete2.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow2 = new google.maps.InfoWindow();
        var infowindowContent2 = document.getElementById('infowindow-content2');
        infowindow2.setContent(infowindowContent2);
        var marker2 = new google.maps.Marker({
            map: map2,
            anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete2.addListener('place_changed', function() {
            $('.pac-container2.pac-logo2').css('z-index', '1000!important');
            infowindow2.close();
            marker2.setVisible(false);
            var place2 = autocomplete2.getPlace();

            if(!place2.geometry){
                place2 = {};
                place2.geometry = {};
                place2.geometry.location = {
                    lat: function(){ return "46.34567"; },
                    lng: function(){ return "38.334543"; }
                };
            }

           /* $('.opt_meeting_point').val($('#pac-input').val());
            $('.opt_meeting_point_lat').val(place.geometry.location.lat());
            $('.opt_meeting_point_long').val(place.geometry.location.lng());*/

            if (!place2.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place2.name + "'");
                return;
            }

            var address = $('#pac-input2').val();
            var html = `<div class="selected-item" data-address-title="Edit Address Title" data-address="${address}" data-address-lat="${place2.geometry.location.lat()}" data-address-lng="${place2.geometry.location.lng()}">
                          <div class="title-area"><input type="text" class="form-control address-title" value="Edit Address Title"></div>
                            <span>${address}: (${place2.geometry.location.lat()}) lng: (${place2.geometry.location.lng()}) </span>
                            <i class="delete-address-item pull-right icon-cz-trash"></i>
                        </div>`;

            $("#selected-address").append(html);
            createAddressJson();
            Materialize.toast('New Address Set To Address Area', 4000, 'toast-success');

            // If the place has a geometry, then present it on a map.
            if (place2.geometry.viewport) {
                map2.fitBounds(place2.geometry.viewport);
            } else {
                map2.setCenter(place2.geometry.location);
                map2.setZoom(17);  // Why 17? Because it looks good.
            }
            marker2.setPosition(place2.geometry.location);
            marker2.setVisible(true);

            var address2 = '';
            if (place2.address_components) {
                address2 = [
                    (place2.address_components[0] && place2.address_components[0].short_name || ''),
                    (place2.address_components[1] && place2.address_components[1].short_name || ''),
                    (place2.address_components[2] && place2.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent2.children['place-icon'].src = place2.icon;
            infowindowContent2.children['place-name'].textContent = place2.name;
            infowindowContent2.children['place-address'].textContent = address2;
            infowindow2.open(map2, marker2);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener2(id, types) {
            var radioButton = document.getElementById(id);
            radioButton.addEventListener('click', function() {
                autocomplete2.setTypes(types);
            });
        }

        setupClickListener2('changetype-all2', []);
        setupClickListener2('changetype-address2', ['address']);
        setupClickListener2('changetype-establishment2', ['establishment']);
        setupClickListener2('changetype-geocode2', ['geocode']);

        document.getElementById('use-strict-bounds2')
            .addEventListener('click', function() {
                console.log('Checkbox clicked! New state=' + this.checked);
                autocomplete2.setOptions({strictBounds: this.checked});
            });






    }
</script>

<script>
    $('#addNewContactInformationLabel').on('click', function() {
        let iter = parseInt($('#contactInformationIterator').val());
        $('#contactInformationDiv').append(' ' +
            '                   <div class="contact-info-group col-md-12">\n' +
            '                        <div class="col-md-6">\n' +
            '                            <input class="contact-info-title" name="newContactInformation'+iter+'" style="border: none!important;" id="newContactInformation'+iter+'" placeholder="Add a name...">\n' +
            '                        </div>\n' +
            '                        <div class="col-md-6">\n' +
            '                            <input class="contact-info-checkbox"  value="0" type="checkbox" id="isRequired'+iter+'">\n' +
            '                            <label for="isRequired'+iter+'">is Required?</label>\n' +
            '                            <button class="btn btn-primary remove-row btn-lg pull-right" type="button" onclick="$(this).parent().parent().remove();" style="">Remove</button>' +
            '                        </div>\n' +
            '                    </div>\n');
        let newIter = iter + 1;
        $('#contactInformationIterator').val(newIter);
    });
</script>

<script>
    <?php use \App\Http\Controllers\VariableController; ?>
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

<script>
    $(document).on('click', '.addFileBlock', function() {
        let fileBlock = `<div class="row" style="margin-bottom: 5px; margin-top: 5px;">
                            <div class="col-md-4 s12" style="padding-top: 7px;">
                                <input type="file" class="optionFile" name="file[]">
                            </div>
                            <div class="col-md-2 s12">
                                <div class="btn btn-primary removeFileBlock" style="background-color: #FF3333";>x</div>
                            </div>
                        </div>`;
        $('.filesContainer').append(fileBlock);
    });

    $(document).on('click', '.removeFileBlock', function() {
        $(this).parent().parent().remove();
    });

    $(document).on('click', '.removeUploadedFileBlock', function() {
            let optionFileID = $(this).parent().prev().find('input').attr('data-id');
            let button = $(this);

            $.ajax({
                url: '{{url("/delete-option-file")}}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',
                    optionFileID: optionFileID
                },
            })
            .done(function(response) {
                if(response.status) {
                    button.parent().parent().remove();
                    Materialize.toast(response.success, 4000, 'toast-success');
                } else {
                    Materialize.toast('An Error Occurred!', 4000, 'toast-alert');
                }
            })
            .fail(function(XHR) {
                console.log(JSON.parse(XHR.responseText).message);
                Materialize.toast(JSON.parse(XHR.responseText).message, 6000, 'toast-alert');
            });
    });

    $(document).on("submit","#option-files-form",function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ url('/save-option-files') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function (response) {
                if (response.status == "success") {
                    Materialize.toast(response.message, 4000, 'toast-success');

                    $('.filesContainer .row').each(function() {
                        let row = $(this);
                        if(row.find('input').attr('type') == 'file')
                            row.remove();
                    });

                    let newBlocks = '';
                    let optionFiles = response.optionFiles;
                    for(let i=0; i<optionFiles.length; i++) {
                        newBlocks += `<div class="row" style="margin-bottom: 5px; margin-top: 5px;">
                            <div class="col-md-4 s12" style="padding-top: 7px;">`;
                                newBlocks += '<input type="text" disabled value="' + optionFiles[i]["fileName"] + '" data-id="' + optionFiles[i]["id"] + '"/>';
                            newBlocks += `</div>
                            <div class="col-md-2 s12">
                                <div class="btn btn-primary removeUploadedFileBlock"
                                     style="background-color: #FF3333" ;>x
                            </div>
                        </div></div>`;
                    }
                    $('.filesContainer').append(newBlocks);
                } else {
                    Materialize.toast('An Error Occured', 4000, 'toast-alert');
                }
            })
            .fail(function () {
                Materialize.toast('Uploading Failed', 4000, 'toast-alert');
            });
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCiMSJJWyJMpXHELXolLJgoZVcrv9ovaT0&language=en&libraries=places&callback=initMap"
        async defer></script>


{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAU3ap4LFLEeWvwOD0ycutnHRRkISzMq1Q&language=en&libraries=places&callback=initMap"
async defer></script> --}}
