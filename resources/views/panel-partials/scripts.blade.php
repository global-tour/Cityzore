</div>
</div>
</div>
</div>
</body>
<!--========= Scripts ===========-->

<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/main/bootstrap.js')}}"></script>

<script src="{{asset('js/main/wow.min.js')}}"></script>
<script src="{{asset('js/main/materialize.min.js')}}"></script>
@if($page != 'bookings-index')<script src="{{asset('js/main/custom.js')}}"></script>@endif
<script src="{{asset('js/main/jquery-ui.js')}}"></script>
<script src="{{asset('js/admin/dashboard-scripts.js')}}"></script>
<script src="https://js.pusher.com/5.1/pusher.min.js"></script>
<script>


    //LEFT MOBILE MENU OPEN
    $(".atab-menu").on('click', function() {
        $(".sb2-1").css("left", "0");
        $(".btn-close-menu").css("display", "inline-block");
    });

    //LEFT MOBILE MENU CLOSE
    $(".btn-close-menu").on('click', function() {
        $(".sb2-1").css("left", "-350px");
        $(".btn-close-menu").css("display", "none");
    });
</script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    let pusher = new Pusher('2697e3c74dd4bfe53964', {
        cluster: 'eu',
        forceTLS: true
    });

    let channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
        let block = '';
        $.ajax({
            type: 'POST',
            url: '/registerNotification',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                notification: data
            },
            success: function(notification) {
                let id = notification.notification.id;
                block += "<div style=\"border-bottom: 1px #eeeeee solid\" class=\"notRead col-md-12\">\n";
                if (data.type == 'USER_REGISTER') {
                    block += "<span style=\"margin-left:-11px;\" class=\"icon col-md-2\"><i style=\"color: #d9534f\" class=\"icon-cz-user\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"label label-info title\">"+data.message+"</label><br>\n";
                } else if (data.type == 'COMPANY_REGISTER' || data.type == 'SUPPLIER_REGISTER') {
                    block += "<span style=\"margin-left:-11px;\" class=\"icon col-md-2\"><i style=\"color: #d9534f\" class=\"icon-cz-add-commission\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"label label-info title\">"+data.message+"</label><br>\n";
                } else if (data.type == "GYG_BOOKING") {
                    block += "<span style=\"margin-left:-14px;\" class=\"icon col-md-2\"><i style=\"color:#ff5533\" class=\"icon-cz-getyourguide\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"label label-warning title\">"+data.message+"</label><br>\n";
                } else if (data.type == "BOKUN_BOOKING") {
                    block += "<span style=\"margin-left:-16px;\" class=\"icon col-md-2\"><i style=\"color:rgb(0,94,158);font-size: 25px\" class=\"icon-cz-bokun\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"title label label-info\">"+data.message+"</label><br>\n";
                } else if (data.type == "CITYZORE_BOOKING") {
                    block += "<span style=\"margin-left:-20px;\" class=\"icon cityzore col-md-2\"><i class=\"icon-cz-cityzore\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"title label label-danger\">"+data.message+"</label><br>\n";
                } else if (data.type == "TICKET_ALERT") {
                    block += "<span style=\"margin-left:-20px;\" class=\"icon col-md-2\"><i class=\"icon-cz-ticket\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"title label label-ticket\">"+data.message+"</label><br>\n";
                } else if (data.type == "AVAILABILITY_EXPIRED") {
                    block += "<span style=\"margin-left:-20px;\" class=\"icon cityzore col-md-2\"><i class=\"icon-cz-availability\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"title label label-ticket\">"+data.message+"</label><br>\n";
                } else if (data.type == "NEW_COMMENT") {
                    block += "<span style=\"margin-left:-20px;\" class=\"icon col-md-2\"><i class=\"icon-cz-comment\"></i></span>\n" +
                        "<text style=\"float: right;\">\n" +
                        "<label class=\"title label label-ticket\">"+data.message+"</label><br>\n";
                }
                block += "<a class=\"deleteNotification\" data-id=\""+id+"\" style=\"float: right;\" role=\"button\"><i class=\"icon-cz-cancel\"></i></a>\n";
                block += "<button onclick='window.location.href=\"/notification/"+data.id+"/details\"'>Show</button>\n";
                block += "<span class=\"date\">a few seconds ago</span>\n";
                block += "</text>\n";
                block += "</div>";
                Materialize.toast(data.message, 4000, 'toast-success');
                $('#data').append(block);
            }
        });
    });

    $('#markAllAsRead').on('click', function() {
        $.ajax({
            type: 'POST',
            url: '/markAllAsRead',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(data) {
                $('.notification').removeClass('notRead');
            }
        });
    });

    $('.notRead').on('click', function() {
        let $this = $(this);
        let id = $this.attr('data-id');
        $.ajax({
            type: 'POST',
            url: '/markNotificationAsRead',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(data) {
                $this.removeClass('notRead');
            }
        });
    });

    $('.deleteNotification').on('click', function() {
        let $this = $(this);
        let id = $this.attr('data-id');
        let dataCount = $('#data').attr('data-count');
        $.ajax({
            type: 'POST',
            url: '/deleteNotification',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
            },
            success: function() {
                $this.parent().parent().remove();
                dataCount = dataCount - 1;
                $('#data').attr('data-count', dataCount);
                if (dataCount == 0) {
                    $('#data').html("<p style='font-size:12px!important;padding: 10px!important;'>There's no notifications !</p>");
                }
            }
        });
    });

    $('#deleteAllNotifications').on('click', function() {
        $.ajax({
            type: 'POST',
            url: '/deleteAllNotifications',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function() {
                $('#data').html('');
                $('#data').html("<p style='font-size:12px!important;padding: 10px!important;'>There's no notifications !</p>");
            }
        });
    });
</script>

@if($page == 'supplier-index')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>

    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $(function() {
            $('body').on('change', '.toggle-class', function() {
                let isActive = $(this).prop('checked') == true ? 1 : 0;
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/changeSupplierStatus',
                    data: {
                        'isActive': isActive,
                        'id': id
                    },
                    success: function(data) {
                        //
                    }
                });
            });
        });
    </script>

@elseif($page == 'on-goings')
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $(function() {
            $("#productSelect").select2({
                multiple: true
            });

            $("#optionSelect").select2({
                multiple: true
            });

            $('.select2-selection__rendered').css('height', '50px');
            $('.select2-selection__rendered').css('overflow-y', 'auto');

        });

        $('#productSelect').on('change', function() {
            $.ajax({
                type: "POST",
                url: "/booking/optionSelectMultiple",
                data: {
                    products: $('#productSelect').val(),
                    _token: '<?=csrf_token()?>'
                },
                success: function(data) {
                    let options = data.options;
                    let optionList = '';
                    for(let i=0; i<options.length; i++) {
                        optionList += '<option value=' + options[i]["id"] + '>' + options[i]["title"] + '</option>';
                    }
                    $('#optionSelect').html(optionList);
                }
            })
        });

        $(document).ready(function(){

            $("#remove-old-cart-items-from-on-goings").on("click", function (event) {
                const waitMeFireArea = $("#datatable_wrapper");
                if(confirm('This is already done automatically by the cron handler, it is not recommended to use if there is no data older than 1.5 hours in the on goins table, Do you still want to use it?')){
                    waitMeFireArea.waitMe({
                        effect : 'bounce',
                        text : 'This process may take some time, please wait',
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
                        url: '{{url("/command-delete-carts-manuel")}}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{csrf_token()}}'
                        },
                    })
                        .done(function(response) {
                         if(response.status){
                             Materialize.toast(response.data.message, 6000, 'toast-success');

                             setTimeout(function(){
                                 window.location.reload();
                             }, 4000);
                         }
                        })
                        .fail(function(XHR) {
                            console.log(JSON.parse(XHR.responseText).message);
                            Materialize.toast(JSON.parse(XHR.responseText).message, 6000, 'toast-alert');
                        })
                        .always(function() {
                            console.log("complete");
                            waitMeFireArea.waitMe("hide");
                        });
                }
            });
        })
    </script>



@elseif($page == 'admins-edit')

<script>
    $(document).ready(function() {

    $(document).on('click', '#create-chat-account-form-submit-button', function(event) {
        event.preventDefault();



        $.ajax({
            url: '{{url("admin/ajax")}}',
            type: 'POST',
            dataType: 'json',
            data: $("#register-chat-account-form").serialize()+"&_token={{csrf_token()}}",
        })
        .done(function(response) {
            if(response.status == "success"){
            Materialize.toast(response.message, 4000, 'toast-success');
            }else {
            Materialize.toast(response.message, 4000, 'toast-alert');
            }


        })
        .fail(function(xhr) {
            console.log(JSON.parse(xhr.responseText).message);
            Materialize.toast("An Error Occurred!", 4000, 'toast-alert');


        })
        .always(function() {
            console.log("complete");
        });

    });

    $(document).on('click', '.register-for-chat-for-admin-fire-button', function(event) {
        event.preventDefault();


         let $this = $(this);
         let user_id = $(this).data("user-id");
         let action = 'register_for_chat_for_admin_layout';
         let token = "{{csrf_token()}}";
         $("#create-chat-admin-modal .modal-content").empty();


         $.ajax({
             url: '{{url("admin/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {action: action, user_id: user_id, _token: token},
         })
         .done(function(response) {
            $("#create-chat-admin-modal .modal-content").html(response.view);
         })
         .fail(function() {
             console.log("error");
         })
         .always(function() {
             console.log("complete");
         });

    });



    });
</script>





@elseif($page == 'product-check')
    <script>
        $(function() {
            $('#publishButton').on('click', function() {
                $.ajax({
                    type: 'POST',
                    url: '/confirmSupplierProductEdit',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        productID: $('.productID').val(),
                        editedProductID: $('.editedProductID').val()
                    },
                    success: function(data) {
                        Materialize.toast('Confirmed successfully! You will be redirected to product list page in 3 seconds', 4000, 'toast-success');
                        window.location.href = '/product';
                    }
                });
            });
        });
    </script>
@elseif($page == 'supplier-details')
    <script>
        $('#newFile').on('click', function() {
            let block =
                '<div class="row s6">' +
                '<div class="input-field col s6">' +
                '<input type="text" class="validate title" name="title[]" value="">' +
                '<label for="bankName">{{__('title')}}</label>' +
                '</div>' +
                '<div class="input-field col s6">' +
                '<button type="button" class="deleteFiles waves-effect waves-light btn-danger" style="font-size:26px;padding:2px 20px;border:none;float: right">-</button>' +
                '<input type="file" class="fileName validate" name="fileName[]" >' +
                '</div>' +
                '</div>' ;
            $('#filesTable').append(block);
            $('#uploadFiles').show();
        });

        $('body').on('click', '.deleteFiles', function() {
            let id = $(this).attr('data-id');
            let $this = $(this);
            if (typeof(id) === undefined) {
                $this.parent().parent().html('');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/deleteLicense',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                    },
                    success: function(data) {
                        $this.parent().parent().html('');
                        Materialize.toast('You deleted your file !', 4000, 'toast-success');
                    },
                    error: function() {
                        Materialize.toast("There's an error! Please contact with us!", 4000, 'toast-alert');
                    }
                });
            }
        });

        $('.deleteFile').on('click', function() {
            let id = $(this).attr('data-id');
            let $this = $(this);
            let tr = $('#' + id);
            $.ajax({
                type: 'POST',
                url: '/supplier/deleteLicense',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    Materialize.toast('You deleted your license successfully !', 4000, 'toast-success');
                    tr.html('');
                }
            });
        });

        $('.editSuggestFile').on('click', function() {
            let $this = $(this);
            let id = $this.attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/supplier/editSuggestFile',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    Materialize.toast('You sent an email to company for edit suggestion.', 4000, 'toast-success');
                }
            });
        });

        $('.confirmCheck').on('click', function() {
            let value = $(this).prop('checked') == true ? 1 : 0;
            let id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/supplier/changeLicenseStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    value: value,
                    id: id,
                }
            });
        });
    </script>
@elseif($page == 'language-index')
    <script>
        $(function() {
            $('.toggle-class').change(function() {
                let isActive = $(this).prop('checked') === false ? 0 : 1;
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/language/isActive',
                    data: {
                        'isActive': isActive,
                        'id': id
                    },
                    success: function(data) {
                        //
                    },
                });
            });
        });
    </script>
@elseif($page == 'apilogs-index')
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $('#getAvailabilities, #reserve, #cancelReservation, #book, #cancelBooking, #notifyPush').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });
    </script>
@elseif($page == 'availability-index')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                    success: function() {
                        if (pageID !== '0') {
                            $('.edit').attr('href', $('.edit').attr('href')+'?page='+pageID);
                        }
                    }
                });
            }
        });

        $(document.body).on('click', '.paginate_button a', function() {
            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
            sessionStorage.setItem("lastPage", $('#pageID').val());
        });

        $('#publishedFilter, #notPublishedFilter').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });
    </script>
@elseif($page == 'pricings-index')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                    success: function() {
                        if (pageID !== '0') {
                            $('.edit').attr('href', $('.edit').attr('href')+'?page='+pageID);
                        }
                    }
                });
            }
        });

        $(document.body).on('click', '.paginate_button a', function() {
            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });
    </script>
@elseif($page == 'product-editpct')
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{'../../keditor/build/keditor.min.js'}}"></script>
    <script src="{{asset('../../keditor/src/lang/en.js')}}"></script>
    <script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
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

        let highlights = document.querySelector('#highlights');
        let included = document.querySelector('#included');
        let notIncluded = document.querySelector('#notincluded');
        let knowBeforeYouGo = document.querySelector('#beforeyougo');
        let tags_1 = document.querySelector('#tags_1');

            let tagifyHighlights =  new Tagify(highlights,{
        keepInvalidTags     : true,
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.'+tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })
    function onDragEndHighlights(elm){
        tagifyHighlights.updateValueByDOMTags()
    }



    let tagifyIncluded =  new Tagify(included, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.'+tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })
    function onDragEndIncluded(elm){
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded =  new Tagify(notIncluded, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.'+tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })
    function onDragEndNotIncluded(elm){
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo =  new Tagify(knowBeforeYouGo, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.'+tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })
    function onDragEndKnowBeforeYouGo(elm){
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }

    let tagifyTags_1 =  new Tagify(tags_1, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyTags_1.DOM.scope, {
        selector: '.'+tagifyTags_1.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyTags_1
        }
    })
    function onDragEndtagifyTags_1(elm){
        tagifyTags_1.updateValueByDOMTags()
    }



        $('#productPCTEditButton').on('click', function() {
            let productID = $('#productID').val();
            let pageID = $('#pageID').val();
            let title = $('#title').val();
            let shortDesc = $('#shortDesc').val();
            $('#fullDesc').val($('.keditor-editable').html()); // This line is for keditor to fill the #fullDesc field
            let fullDesc = $('#fullDesc').val();
            let highlights = $('#highlights').val();
            let included = $('#included').val();
            let notIncluded = $('#notincluded').val();
            let knowBeforeYouGo = $('#beforeyougo').val();
            let tags = $('#tags_1').val();
            $.ajax({
                type: 'POST',
                url: '/productPCT/'+productID+'/edit',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    title: title,
                    shortDesc: shortDesc,
                    fullDesc: fullDesc,
                    highlights: highlights,
                    included: included,
                    notIncluded: notIncluded,
                    knowBeforeYouGo: knowBeforeYouGo,
                    tags: tags
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast('Fields are updated successfully', 3000, 'toast-success');
                        setTimeout(function() {
                            window.location.href = "/productPCT?page="+pageID;
                        }, 3000);
                    }
                }
            });
        });
    </script>
@elseif($page == 'product-indexpct')
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });
    </script>
@elseif($page == 'product-editpctcom')
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{'../../keditor/build/keditor.min.js'}}"></script>
    <script src="{{asset('../../keditor/src/lang/en.js')}}"></script>
    <script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
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

        let highlights = document.querySelector('#highlights');
        let included = document.querySelector('#included');
        let notIncluded = document.querySelector('#notincluded');
        let knowBeforeYouGo = document.querySelector('#beforeyougo');
        let tags_1 = document.querySelector('#tags_1');





            let tagifyHighlights =  new Tagify(highlights,{
        keepInvalidTags     : true,
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.'+tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })
    function onDragEndHighlights(elm){
        tagifyHighlights.updateValueByDOMTags()
    }



    let tagifyIncluded =  new Tagify(included, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.'+tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })
    function onDragEndIncluded(elm){
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded =  new Tagify(notIncluded, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.'+tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })
    function onDragEndNotIncluded(elm){
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo =  new Tagify(knowBeforeYouGo, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.'+tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })
    function onDragEndKnowBeforeYouGo(elm){
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }

    let tagifyTags_1 =  new Tagify(tags_1, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyTags_1.DOM.scope, {
        selector: '.'+tagifyTags_1.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyTags_1
        }
    })
    function onDragEndtagifyTags_1(elm){
        tagifyTags_1.updateValueByDOMTags()
    }


/*        let tagifyHighlights = new Tagify(highlights, {
            keepInvalidTags: true,
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });
        let tagifyIncluded = new Tagify(included, {
            keepInvalidTags: true,
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });
        let tagifyNotIncluded = new Tagify(notIncluded, {
            keepInvalidTags: true,
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });
        let tagifyKnowBeforeYouGo = new Tagify(knowBeforeYouGo, {
            keepInvalidTags: true,
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });
        let tagifyTags_1 = new Tagify(tags_1, {
            keepInvalidTags: true,
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });*/





        $('#productPCTEditButton').on('click', function() {
            let productID = $('#productID').val();
            let pageID = $('#pageID').val();
            let title = $('#title').val();
            let shortDesc = $('#shortDesc').val();
            $('#fullDesc').val($('.keditor-editable').html()); // This line is for keditor to fill the #fullDesc field
            let fullDesc = $('#fullDesc').val();
            let highlights = $('#highlights').val();
            let included = $('#included').val();
            let notIncluded = $('#notincluded').val();
            let knowBeforeYouGo = $('#beforeyougo').val();
            let tags = $('#tags_1').val();
            $.ajax({
                type: 'POST',
                url: '/productPCTcom/'+productID+'/edit',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    title: title,
                    shortDesc: shortDesc,
                    fullDesc: fullDesc,
                    highlights: highlights,
                    included: included,
                    notIncluded: notIncluded,
                    knowBeforeYouGo: knowBeforeYouGo,
                    tags: tags
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast('Fields are updated successfully', 3000, 'toast-success');
                        setTimeout(function() {
                            window.location.href = "/productPCTcom?page="+pageID;
                        }, 3000);
                    }
                }
            });
        });
    </script>
@elseif($page == 'product-editctp')
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{'../../keditor/build/keditor.min.js'}}"></script>
    <script src="{{asset('../../keditor/src/lang/en.js')}}"></script>
    <script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
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

        let highlights = document.querySelector('#highlights');
        let included = document.querySelector('#included');
        let notIncluded = document.querySelector('#notincluded');
        let knowBeforeYouGo = document.querySelector('#beforeyougo');
        let tags_1 = document.querySelector('#tags_1');




            let tagifyHighlights =  new Tagify(highlights,{
        keepInvalidTags     : true,
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.'+tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })
    function onDragEndHighlights(elm){
        tagifyHighlights.updateValueByDOMTags()
    }



    let tagifyIncluded =  new Tagify(included, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.'+tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })
    function onDragEndIncluded(elm){
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded =  new Tagify(notIncluded, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.'+tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })
    function onDragEndNotIncluded(elm){
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo =  new Tagify(knowBeforeYouGo, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.'+tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })
    function onDragEndKnowBeforeYouGo(elm){
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }

    let tagifyTags_1 =  new Tagify(tags_1, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),

    });
    DragSort(tagifyTags_1.DOM.scope, {
        selector: '.'+tagifyTags_1.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndtagifyTags_1
        }
    })
    function onDragEndtagifyTags_1(elm){
        tagifyTags_1.updateValueByDOMTags()
    }





        $('#productPCTEditButton').on('click', function() {
            let productID = $('#productID').val();
            let pageID = $('#pageID').val();
            let title = $('#title').val();
            let shortDesc = $('#shortDesc').val();
            $('#fullDesc').val($('.keditor-editable').html()); // This line is for keditor to fill the #fullDesc field
            let fullDesc = $('#fullDesc').val();
            let highlights = $('#highlights').val();
            let included = $('#included').val();
            let notIncluded = $('#notincluded').val();
            let knowBeforeYouGo = $('#beforeyougo').val();
            let tags = $('#tags_1').val();
            $.ajax({
                type: 'POST',
                url: '/productCTP/'+productID+'/edit',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    title: title,
                    shortDesc: shortDesc,
                    fullDesc: fullDesc,
                    highlights: highlights,
                    included: included,
                    notIncluded: notIncluded,
                    knowBeforeYouGo: knowBeforeYouGo,
                    tags: tags
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast('Fields are updated successfully', 3000, 'toast-success');
                        setTimeout(function() {
                            window.location.href = "/productCTP?page="+pageID;
                        }, 3000);
                    }
                }
            });
        });
    </script>
@elseif($page == 'product-indexpctcom')
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });
    </script>

@elseif($page == 'product-indexctp')
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });
    </script>


@elseif($page == 'product-index')






    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>


   <script>

       $(document).ready(function() {

         $(document).on('click', '.paginate_button a', function() {
            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#cities").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#attractions").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });



                 $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        let block = '<option value="" disabled selected>Choose a City</option>';
                        for (let i = 0; i < cities.length; i++) {
                            block += '<option value="'+cities[i]+'">'+cities[i]+'</option>'
                        }
                        $('#cities').append(block);
                    }
                });
            });

            $('#cities').on('change', function() {
                let city = $('#cities').val();
                $.ajax({
                    type: 'POST',
                    url: '/getAttractionsByCity',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        city: city
                    },
                    success: function(data) {
                        if (data.success) {
                            $('.mdb-select').material_select('destroy');
                            let block = '';
                            $('#attractions').html('');
                            $('#attractions').append('<option value="" disabled selected>Choose an Attraction</option>');

                            let attractions = data.attractions;
                            attractions.forEach(function(item, index) {
                                block += '<option value="'+item.id+'">'+item.name+'</option>';
                            });
                            $('#attractions').append(block);
                            $('.mdb-select').material_select();
                        }
                    }
                });
            });




       });
   </script>










    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                    success: function() {
                        if (pageID !== '0') {
                            $('.edit').attr('href', $('.edit').attr('href')+'?page='+pageID);
                        }
                    }
                });
            }
        });

        $('#publishedFilter, #notPublishedFilter').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });

        $('.disabledDraft').on('click', function() {
            Materialize.toast('Please add location and title to your product firstly', 4000, 'toast-alert');
        });

        $('.disabledPublish').on('click', function() {
            Materialize.toast("Please check your options' status firstly.", 4000, 'toast-alert');
        });

        $('#sortDiv').sortable({
            items: '.sortableDiv',
            cursor: 'move',
            opacity: 0.5,
            containment: '#sortDiv',
            distance: 20,
            tolerance: 'pointer',
            start: function(event, ui) {
                let start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            change: function(event, ui) {
                //
            },
            update: function(event, ui) {
                let start_pos = ui.item.data('start_pos');
                let end_pos = ui.item.index();
            }
        });

        $('#doneOrderingButton').on('click', function() {
            let productId = $(this).attr('data-product-id');
            let sortableDiv = $('.sortableDiv');
            let orderArr = [];
            sortableDiv.each(function(index, item) {
                orderArr[index] = $(this).attr('data-id');
            });
            $.ajax({
                method: 'POST',
                url: '/product/orderImages',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    productId: productId,
                    orderArr: orderArr
                },
                success: function(data) {
                    if (data.success) {
                        $('.close').click();
                    }
                },
                errors: function() {
                    //
                }
            });
        });

        $('#exportToExcelButton').on('click', function() {
            let country = $('#countries').val();
            let city = $('#cities').val();
            let attraction = $('#attractions').val();
            let category = $('#categoryId').val();
            let supplier = $('#supplierId').val() === null ? '-1' : $('#supplierId').val();
            let published = $('#publishedFilter').val();
            let notPublished = $('#notPublishedFilter').val();
            let pendingApproval = $('#pendingApproval').is(':checked') ? '1' : '0';
            let orderBy = $('#orderBy').val();
            let specialOffer = $('#specialOffer').is(':checked') ? '1' : '0';

            window.location.href = '/product/exportToExcel?country=' + encodeURIComponent(country) + '&city=' +
                encodeURIComponent(city) + '&attraction=' + encodeURIComponent(attraction) + '&category=' + encodeURIComponent(category) + '&supplier=' + encodeURIComponent(supplier) + '&published=' + encodeURIComponent(published) + '&notPublished=' + encodeURIComponent(notPublished) + '&pendingApproval=' + encodeURIComponent(pendingApproval) + '&orderBy=' + encodeURIComponent(orderBy) + '&specialOffer=' + encodeURIComponent(specialOffer);
        });

        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $('body').on('click', '.orderModal', function() {
            let productId = $(this).attr('data-product-id');
            $.ajax({
                method: 'POST',
                url: '/product/getProductImages',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    productId: productId
                },
                success: function(data) {
                    $('#doneOrderingButton').attr('data-product-id', productId);
                    let productImages = data.productImages;
                    let imageGalleryModalBody = $('#sortDiv');
                    imageGalleryModalBody.html('');
                    let block = '';
                    productImages.forEach(function(item) {
                        block += '<div class="col-md-3 sortableDiv" style="margin-bottom: 10px;" data-id="'+item.id+'">';
                        block += '<img src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/'+item.src+'">';
                        block += '</div>';
                    });
                    imageGalleryModalBody.append(block);
                },
                errors: function() {
                    //
                }
            });
        });
    </script>



@elseif($page == 'customerlogs-index')

{{-- <script src="https://canvasjs.com/assets/script/jquery-ui.1.11.2.min.js"></script> --}}
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>

     <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/waitme/waitMe.min.js')}}"></script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

<script>

  window.onload = function () {


    $(document).on('change', 'select[name="month"]', function(event) {
        event.preventDefault();
        var date = $(this).val();




                $("#customer-datepicker").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


                 $.ajax({
                     url: '{{url('/customerlog/ajax')}}',
                     type: 'POST',
                     dataType: 'json',
                     data: {action: 'get_data_by_month', _token: "{{csrf_token()}}", date: date},
                 })
                 .done(function(response) {
                    var objArr = [];
                    var parsedData = response.data;

                    for (key in parsedData) {
                      objArr.push({ label: key, y: parsedData[key] });
                    }



          // Construct options first and then pass it as a parameter
            var options = {
                animationEnabled: true,
                title: {
                    text: "Customer Activities"
                },
                data: [{
                    type: "column", //change it to line, area, bar, pie, etc
                    legendText: false,
                    showInLegend: false,
                    dataPoints: objArr
                    }]
            };




                         $("#chartContainer").CanvasJSChart(options);






                 })
                 .fail(function(xhr) {
                     console.log(xhr);

                     $("#billing-image-wrapper").html('');
                 })
                 .always(function() {

                    $("#customer-datepicker").waitMe('hide');
                 });




    });



             $("#customer-datepicker").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


                 $.ajax({
                     url: '{{url('/customerlog/ajax')}}',
                     type: 'POST',
                     dataType: 'json',
                     data: {action: 'get_data_by_month', _token: "{{csrf_token()}}", date: "{{date('Y-m-d')}}"},
                 })
                 .done(function(response) {
                    var objArr = [];
                    var parsedData = response.data;

                    for (key in parsedData) {
                      objArr.push({ label: key, y: parsedData[key] });
                    }



                // Construct options first and then pass it as a parameter
            var options = {
                animationEnabled: true,
                title: {
                    text: "Customer Activities"
                },
                data: [{
                    type: "column", //change it to line, area, bar, pie, etc
                    legendText: false,
                    showInLegend: false,
                    dataPoints: objArr
                    }]
            };

                $("#resizable").resizable({
                    create: function (event, ui) {
                        //Create chart.
                        $("#chartContainer").CanvasJSChart(options);
                    },
                    resize: function (event, ui) {
                        //Update chart size according to its container size.
                        $("#chartContainer").CanvasJSChart().render();
                    }
                });




                 })
                 .fail(function(xhr) {
                     console.log(xhr);

                     $("#billing-image-wrapper").html('');
                 })
                 .always(function() {

                    $("#customer-datepicker").waitMe('hide');
                 });






















}
</script>




@elseif($page == 'option-index')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                    success: function() {
                        if (pageID !== '0') {
                            $('.edit').attr('href', $('.edit').attr('href')+'?page='+pageID);
                        }
                    }
                });
            }
        });

        $(document.body).on('click', '[data-opt-id]', function () {
            const optId = $(this).data('opt-id');
            let html = '';

            $.ajax({
                url: '/option/show-supplier',
                type: 'POST',
                dataType: 'json',
                data: {_token: '{{ csrf_token() }}' , optionId: optId },
                success: function (res) {

                    if(res.data.length){

                        $.each(res.data, function (k, v) {
                            html += `<tr>
                                        <td> ${k+1} </td>
                                        <td> ${v.contactName} ${v.contactSurname} </td>
                                        <td> <a href="mailto:${v.email}" >${v.email}</a> </td>
                                    </tr>`
                        })

                    }

                    $('#opt-supp-modal table tbody').html(html);
                }
            })
        });

        $(document.body).on('click', '.paginate_button a', function() {
            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });

        $('#publishedFilter, #notPublishedFilter').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });
    </script>
    <script>
        $(document.body).on('dblclick', '.commissionInput', function() {
            $(this).removeAttr('readonly');
            let button = $(this).parent().parent().find('.saveCommissionInput');
            button.show();
        });

        $('.disablePreview').on('click', function() {
            Materialize.toast('If you would like to view preview of this product, please attach an option firstly', 4000, 'toast-alert');
        });

        $(document.body).on('click','.saveCommissionInput', function() {
            let input = $(this).parent().parent().find('.commissionInput');
            let optionId = $(this).attr('data-option-id');
            let $this = $(this);
            if (input.val() !== '') {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/option/saveOptionCommission',
                    data: {
                        _token: '<?=csrf_token()?>',
                        commission: input.val(),
                        optionId: optionId
                    },
                    success: function(data) {
                        Materialize.toast('You have changed commission successfully', 4000, 'toast-success');
                        $this.hide();
                        input.attr('readonly', true);
                        if (typeof data.error !== 'undefined') {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            } else {
                Materialize.toast('Commission can not be blank!', 4000, 'toast-alert');
            }
        });

        $(function() {
            $('#showHideFilters').on('click', function() {
                let isShown = $(this).attr('data-shown');
                if (isShown === '1') {
                    $(this).attr('data-shown', '0');
                    $(this).html('Show');
                    $('.filters').hide();
                } else {
                    $(this).attr('data-shown', '1');
                    $(this).html('Hide');
                    $('.filters').show();
                }
            });

            $('body').on('change', '.toggle-class3', function() {
                let isSupplierPublished = $(this).prop('checked') === true ? 1 : 0;
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/supplierPublished',
                    data: {
                        'supplierPublished': isSupplierPublished,
                        'id': id
                    },
                    success: function(data) {
                        //
                    }
                });
            });

            $('body').on('change', '.toggle-class4', function() {
                let isPublished = $(this).prop('checked') === true ? 1 : 0;
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/option/changeOptionPublishedStatus',
                    data: {
                        'isPublished': isPublished,
                        'id': id
                    },
                    success: function(data) {
                        //
                    }
                });
            });
        });

        $('.disabledDraft').on('click', function() {
            Materialize.toast('Please add location and title to your product firstly', 4000, 'toast-alert');
        });

        $('.disabledPublish').on('click', function() {
            Materialize.toast("Please check your options' status firstly.", 4000, 'toast-alert');
        });

        $(document.body).on('click', '.apiButton', function() {
            let $this = $(this);
            let optionID = $this.attr('data-option-id');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '/option/setApiConnection',
                data: {
                    _token: '<?=csrf_token()?>',
                    optionID: optionID
                },
                success: function(data) {
                    if (data.success) {
                        if (data.value === 1) {
                            $('.spanDiv').html('<span class="label label-info" style="font-size: 18px!important;">Connected to API</span>');
                            $this.html('<i style="background: #dc3545!important;" class="icon-cz-connection"></i>');
                            $this.removeClass('disconnectedToApi');
                            $this.addClass('connectedToApi');
                            Materialize.toast(data.success, 4000, 'toast-success');
                        } else {
                            $('.spanDiv').html('<span class="label label-info" style="font-size: 18px!important;">Not Connected to API</span>');
                            $this.html('<i style="background: #28a745!important;" class="icon-cz-connection"></i>');
                            $this.removeClass('connectedToApi');
                            $this.addClass('disconnectedToApi');
                            Materialize.toast(data.success, 4000, 'toast-success');
                        }
                    } else {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.optionProduct').click(function() {
                $(this).parent().next('.more-info').slideToggle('slow');
            });
        });

        function showModal() {
            $('#myModal').modal('show');
        }
    </script>
@elseif($page == 'special-offers-create')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('/js/airdatepicker/datepicker.en.js')}}"></script>
    <script src="{{asset('/js/admin/special-offers-create-scripts.js')}}"></script>
@elseif($page == 'special-offers-index')
    <script>
        $('.changeSpecialOfferStatus').on('click', function() {
            let $this = $(this);
            let specialOfferID = $this.attr('data-offer-id');
            let dateType = $this.attr('data-date-type');
            let from = $this.parent().parent().find('#from').val();
            let to = $this.parent().parent().find('#to').val();
            let dayName = $this.parent().parent().find('#dayName').val();
            let day = $this.parent().parent().find('#day').val();
            let hour = $this.parent().parent().find('#hour').val();
            let obj = [];
            obj.push({id: specialOfferID, dateType: dateType, from: from, to: to, dayName: dayName, day: day, hour: hour});
            $.ajax({
                type: 'POST',
                url: '/special-offers/changeSpecialOfferStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    obj: obj,
                },
                success: function(data) {
                    let isActive = data.isActive;
                    if (isActive === 1) {
                        $this.text('');
                        $this.text('DEACTIVATE');
                        $this.css('background-color', '#e23464');
                    } else {
                        $this.text('');
                        $this.text('ACTIVATE');
                        $this.css('background-color', '#0f9d58');
                    }
                }
            });
        });

        $('.editSpecialOffer').on('click', function(e) {
            e.preventDefault();
            let $this = $(this);
            let $thisParent = $this.parent().parent().parent();
            let date = $thisParent.find('.date');
            let dateVal = date.text();
            let discount = $thisParent.find('.discount');
            let discountVal = discount.text();
            let minPersonCartTotal = $thisParent.find('.minPersonCartTotal');
            let minPersonCartTotalVal = minPersonCartTotal.text();

            dateVal = dateVal.split(" - ");
            dateVal[0] = dateVal[0].split('/');
            dateVal[0] = dateVal[0][2] + '-' + dateVal[0][1] + '-' + dateVal[0][0];
            dateVal[1] = dateVal[1].split('/');
            dateVal[1] = dateVal[1][2] + '-' + dateVal[1][1] + '-' + dateVal[1][0];

            date.html('');
            date.append("<input style='width: 45%; margin-right: 5px;' type='date' class='fromDateNewVal' value='"+dateVal[0]+"'>");
            date.append("<input style='width: 45%' type='date' class='toDateNewVal' value='"+dateVal[1]+"'>");

            discountVal = discountVal.split(" ");
            discount.html(discountVal[0] + " ");
            discount.append("<input style='width: 25%' type='text' class='discountNewVal' value='"+discountVal[1]+"'>");

            minPersonCartTotalVal = minPersonCartTotalVal.split(": ");
            minPersonCartTotal.html(minPersonCartTotalVal[0] + ": ");
            minPersonCartTotal.append("<input style='width: 25%' type='text' class='minPersonCartTotalNewVal' value='"+minPersonCartTotalVal[1]+"'>");

            $thisParent.find('.updateSpecialOffer').show();
            $(this).hide();
        });

        $('.updateSpecialOffer').on('click', function(e) {
            e.preventDefault();
            let $this = $(this);
            let $thisParent = $this.parent().parent().parent();
            let specialOfferID = $(this).attr('data-id');

            let date = $thisParent.find('.date');
            let fromDateNewVal = $thisParent.find('.fromDateNewVal').val();
            let toDateNewVal = $thisParent.find('.toDateNewVal').val();

            let discount = $thisParent.find('.discount');
            let discountNewVal = $thisParent.find('.discountNewVal').val();

            let minPersonCartTotal = $thisParent.find('.minPersonCartTotal');
            let minPersonCartTotalNewVal = $thisParent.find('.minPersonCartTotalNewVal').val();

            $.ajax({
                type: 'POST',
                url: '/special-offers/edit',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    fromDateNewVal: fromDateNewVal,
                    toDateNewVal: toDateNewVal,
                    discountNewVal: discountNewVal,
                    minPersonCartTotalNewVal: minPersonCartTotalNewVal,

                    specialOfferID: specialOfferID,
                    dateType: $thisParent.find('#dateType').val(),
                    from: $thisParent.find('#from').val(),
                    to: $thisParent.find('#to').val(),
                },
                success: function(data) {
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    } else if(data.success) {
                        $thisParent.find('.updateSpecialOffer').hide();
                        $thisParent.find('.editSpecialOffer').show();

                        date.html('');
                        date.append(data.fromDateInResponse + " - " + data.toDateInResponse);

                        discount.html(data.discountTypeInResponse + " ");
                        discount.append(discountNewVal);

                        minPersonCartTotal.html(data.minTypeInResponse + " ");
                        minPersonCartTotal.append(minPersonCartTotalNewVal);

                        $thisParent.find('#from').val(data.fromDateInResponse);
                        $thisParent.find('#to').val(data.toDateInResponse);

                        Materialize.toast('You updated the special offer successfully!', 4000, 'toast-success');
                    }else{
                        Materialize.toast("An Error Occurred!", 4000, 'toast-alert');
                    }
                }
            });
        });
    </script>
@elseif($page == 'commissioners-edit')
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script>
        $('#productSelect').on('change', function() {
            $('#commissionDiv').hide();
            let productSelect = $('#productSelect').val();
            let optionSelect = $('#optionSelect').html("<option selected>Choose an Option</option>\n");
            $.ajax({
                method: 'POST',
                url: '/booking/optionSelect',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    productSelect: productSelect
                },
                success: function(data) {
                    let options = data.option;
                    for (let i=0; i<options.length; i++) {
                        $('#optionSelect').append('<option data-foo="'+options[i].referenceCode+'" data-is-connected="'+options[i].connectedToApi+'" data-ref-code="'+options[i].referenceCode+'" value="'+options[i].id+'">'+options[i].title+'</option>');
                    }
                }
            });
        });

        $('#optionSelect').on('change', function() {
            let optionSelect = $('#optionSelect').val();
            let commissionerID = $('#commissionerID').val();
            $('#commission').val('');
            $.ajax({
                method: 'POST',
                url: '/commissioner/optionSelect',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    optionSelect: optionSelect,
                    commissionerID: commissionerID
                },
                success: function(data) {
                    if (data.commission) {
                        $('#commission').val(data.commission.commission);
                    }
                    $('#commissionDiv').show();
                }
            });
        });

        $('#saveCommissionButton').on('click', function() {
            let optionID = $('#optionSelect').val();
            let commissionerID = $('#commissionerID').val();
            let commission = $('#commission').val();
            $.ajax({
                method: 'POST',
                url: '/commissioner/saveOptionCommission',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    optionID: optionID,
                    commissionerID: commissionerID,
                    commission: commission
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, 'toast-success');
                    }
                }
            });
        });

        $(function() {
            $("#productSelect, #optionSelect").select2({
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
@elseif($page == 'bookings-create')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('/js/airdatepicker/datepicker.en.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker-here').datepicker({
                minDate: moment().toDate(),
            });
            $('#companySelect').on('change', function() {
                let companySelect = $('#companySelect').val();
                $('#timeContainer').html('');
                productSelect = $('#productSelect').html("<option selected>Choose an Product</option>\n");
                optionSelect = $('#optionSelect').html("<option selected>Choose an Option</option>");
                $.ajax({
                    method: 'POST',
                    url: '/booking/productSelect',
                    data: {
                        _token: '<?=csrf_token()?>',
                        companySelect: companySelect
                    },
                    success: function(data) {
                        let product = data.product;
                        for (let i=0; i<product.length; i++) {
                            $('#productSelect').append('<option data-ref-code="'+product[i].referenceCode+'" value="'+product[i].id+'">'+product[i].title+'</option>');
                        }
                    }
                });
            });
            $('#productSelect').on('change', function() {
                $('#timeContainer').html('');
                let productSelect = $('#productSelect').val();
                optionSelect = $('#optionSelect').html("<option selected>Choose an Option</option>");
                $.ajax({
                    method: 'POST',
                    url: '/booking/optionSelect',
                    data: {
                        _token: '<?=csrf_token()?>',
                        productSelect: productSelect
                    },
                    success: function(data) {
                        let option = data.option;
                        for (let i=0; i<option.length; i++) {
                            $('#optionSelect').append('<option data-ref-code="'+option[i].referenceCode+'" value="'+option[i].id+'">'+option[i].title+'</option>');
                        }
                    }
                });
            });
            $('#optionSelect').on('change', function() {
                $('#timeContainer').html('');
                let optionSelect = $('#optionSelect').val();
                let bookingDate = $('#bookingDate').val();
                $.ajax({
                    method: 'POST',
                    url: '/booking/bookingTime',
                    data: {
                        _token: '<?=csrf_token()?>',
                        optionSelect: optionSelect,
                        bookingDate: bookingDate,
                    },
                    success: function(data) {
                        $('#timeContainer').show();
                        let ticketsSt = data.ticketsSt;
                        let hourArrSt = data.hourArrSt;
                        let hourArrOp = data.hourArrOp;
                        let ticketsOp = data.ticketsOp;
                        let type = data.type;
                        let isLimitless = data.isLimitless;
                        let avNames = data.avNames;
                        for (let i=0;i<type.length;i++) {
                            $('#timeContainer').append('<select id="bookingTime'+[i]+'" class="browser-default custom-select bookingTime file-path validate" name="bookingTime'+[i]+'"></select>');
                            if (isLimitless[i] === 0) {
                                if (type[i] === 'Starting Time') {
                                    if (hourArrSt[i].length > 0) {
                                        for (let j=0; j<hourArrSt[i].length; j++) {
                                            $("#bookingTime"+i).append('<option data-ref-code="' + hourArrSt[i][j] + '" value="' + hourArrSt[i][j] + '">'+ avNames[i] + ' - ' + hourArrSt[i][j] + ' - ' + ticketsSt[i][j] + ' tickets left</option>');
                                        }
                                    } else {
                                        $('#timeContainer').hide();
                                        Materialize.toast('Tour is not available on selected date!', 4000, 'toast-alert');
                                    }
                                } else if (type[i] === 'Operating Hours') {
                                    if (hourArrOp[i].length > 0) {
                                        for (let k=0; k<hourArrOp[i].length; k++) {
                                            $("#bookingTime"+i).append('<option data-ref-code="' + hourArrOp[i][k] + '" value="' + hourArrOp[i][k] + '">'+ avNames[i]+ ' - ' + hourArrOp[i][k] + ' - ' + ticketsOp[i][k] + ' tickets left</option>');
                                        }
                                    } else {
                                        $('#timeContainer').hide();
                                        Materialize.toast('Tour is not available on selected date!', 4000, 'toast-alert');
                                    }
                                }
                            } else {
                                if (type[i] === 'Starting Time') {
                                    if (hourArrSt[i].length > 0) {
                                        for (let j=0; j<hourArrSt[i].length; j++) {
                                            $("#bookingTime"+i).append('<option data-ref-code="' + hourArrSt[i][j] + '" value="' + hourArrSt[i][j] + '">'+ avNames[i] + ' - ' + hourArrSt[i][j] + ' - Limitless tickets</option>');
                                        }
                                    } else {
                                        $('#timeContainer').hide();
                                        Materialize.toast('Tour is not available on selected date!', 4000, 'toast-alert');
                                    }
                                } else if (type[i] === 'Operating Hours') {
                                    if (hourArrOp[i].length > 0) {
                                        for (let k=0; k<hourArrOp[i].length; k++) {
                                            $("#bookingTime"+i).append('<option data-ref-code="' + hourArrOp[i][k] + '" value="' + hourArrOp[i][k] + '">'+ avNames[i]+ ' - ' + hourArrOp[i][k] + ' - Limitless tickets</option>');
                                        }
                                    } else {
                                        $('#timeContainer').hide();
                                        Materialize.toast('Tour is not available on selected date!', 4000, 'toast-alert');
                                    }
                                }
                            }
                        }
                    },
                });
            });

            $('#bookingDate').on('click', function() {
                $('#timeContainer').html('');
            });
        });
    </script>
@elseif($page == 'bookings-edit')
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('/js/airdatepicker/datepicker.en.js')}}"></script>

    <script type="text/javascript">
        $('#nextButton').on('click', function(e) {
            e.preventDefault();
            $('#menu1Tab').click();
        });
    </script>
@elseif($page == 'comment-index')
    <script>
        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $('#one, #two, #three, #four, #five, #confirmed, #notConfirmed').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });
    </script>
@elseif($page == 'bookings-index')
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
    <script src="{{asset('js/main/custom1.js')}}"></script>

{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>--}}
{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>--}}
{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>--}}

{{--    <script src="{{asset('js/bay-select2.js')}}"></script>--}}
    <script src="{{asset('js/select2.min.js')}}"></script>

    <script>

        $('#exportToExcelButton').on('click', function() {
            let from = $('.datepicker-from').val();
            let to = $('.datepicker-to').val();

            let cFrom = $('.c-datepicker-from').val();
            let cTo = $('.c-datepicker-to').val();


            var platforms=$('#selectInputs').val();

            let payment_supplier = "";
            @if(auth()->guard('admin')->check())
                payment_supplier = $('#payment_supplier').val();
            @elseif(auth()->guard('supplier')->check())
                payment_supplier = {{auth()->user()->id}}
            @endif

            let payment_affiliate = $('#payment_affiliate').val() ?? '';
            let paymentMethod = $('#paymentMethod').val() ?? '';
            let selectedOption = $('#optionSelect').val() ?? '';

            let approvedBookings = $('#bookingStatus').val().includes('approvedBookings') ? '1' : '0';
            let pendingBookings = $('#bookingStatus').val().includes('pendingBookings') ? '1' : '0';
            let cancelledBookings = $('#bookingStatus').val().includes('cancelledBookings') ? '1' : '0';

            let bookingNumber = $('#searchBooking').val() ?? '';
            let invoiceID = $('#searchInvoice').val() ?? '';
            let travelerName = $('#searchTraveler').val() ?? '';
            let commissionID = $('#commissioner').val() ?? '';
            window.location.href = '/booking/exportToExcel?payment_supplier=' + payment_supplier + '&payment_affiliate=' + payment_affiliate + '&from=' + from + '&to=' +
                to + '&cFrom=' + cFrom + '&cTo=' + cTo + '&platforms=' + platforms + '&paymentMethod=' + paymentMethod + '&approvedBookings=' + approvedBookings + '&pendingBookings=' + pendingBookings + '&cancelledBookings=' + cancelledBookings + '&selectedOption=' + selectedOption + '&bookingNumber=' + bookingNumber + '&invoiceID=' + invoiceID + '&travelerName=' + travelerName+ '&commissionID=' + commissionID;
        });

        $('#showHideFilters').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).attr('data-shown', '0');
                $(this).html('Show');
                $('.filters').hide();
            } else {
                $(this).attr('data-shown', '1');
                $(this).html('Hide');
                $('.filters').show();
            }
        });

        $('#gygBookings, #czBookings, #approvedBookings, #pendingBookings, #cancelledBookings, #bokunBookings, #viatorBookings, #musementBookings, #headoutBookings, #isangoBookings, #holibobBookings, #railbookersBookings, #raynaToursBookings').on('click', function() {
            let value = $(this).val();
            $(this).val(value === '1' ? '0' : '1');
            if (value === '1') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });

        let datepickerFrom = $('.datepicker-from').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: false,
            onShow: function() {
                $('.datepicker--nav').show();
                $('.-from-bottom-').show();
                $('.datepicker--nav-title').show();
                $('.datepicker--nav-action').show();
                $('.datepicker--pointer').show();
                $('.datepicker--content').show();
            },
            onSelect: function() {
                $('.datepicker--nav').hide();
                $('.-from-bottom-').hide();
                $('.datepicker--nav-title').hide();
                $('.datepicker--nav-action').hide();
                $('.datepicker--pointer').hide();
                $('.datepicker--content').hide();
            }
        });
        let datepickerTo = $('.datepicker-to').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: false,
            onShow: function() {
                $('.datepicker--nav').show();
                $('.-from-bottom-').show();
                $('.datepicker--nav-title').show();
                $('.datepicker--nav-action').show();
                $('.datepicker--pointer').show();
                $('.datepicker--content').show();
            },
            onSelect: function() {
                $('.datepicker--nav').hide();
                $('.-from-bottom-').hide();
                $('.datepicker--nav-title').hide();
                $('.datepicker--nav-action').hide();
                $('.datepicker--pointer').hide();
                $('.datepicker--content').hide();
            }
        });
        let cDatepickerFrom = $('.c-datepicker-from').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: false,
            onShow: function() {
                $('.datepicker--nav').show();
                $('.-from-bottom-').show();
                $('.datepicker--nav-title').show();
                $('.datepicker--nav-action').show();
                $('.datepicker--pointer').show();
                $('.datepicker--content').show();
            },
            onSelect: function() {
                $('.datepicker--nav').hide();
                $('.-from-bottom-').hide();
                $('.datepicker--nav-title').hide();
                $('.datepicker--nav-action').hide();
                $('.datepicker--pointer').hide();
                $('.datepicker--content').hide();
            }
        });

        let cDatepickerTo = $('.c-datepicker-to').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: false,
            onShow: function() {
                $('.datepicker--nav').show();
                $('.-from-bottom-').show();
                $('.datepicker--nav-title').show();
                $('.datepicker--nav-action').show();
                $('.datepicker--pointer').show();
                $('.datepicker--content').show();
            },
            onSelect: function() {
                $('.datepicker--nav').hide();
                $('.-from-bottom-').hide();
                $('.datepicker--nav-title').hide();
                $('.datepicker--nav-action').hide();
                $('.datepicker--pointer').hide();
                $('.datepicker--content').hide();
            }
        });


        $('body').on('dblclick', '.rCodeInput', function() {
            $(this).removeAttr('readonly');
            let button = $(this).parent().parent().find('.saveRCodeInput');
            button.show();
        });


        $('body').on('click', '.saveRCodeInput', function() {
            let input = $(this).parent().parent().find('.rCodeInput');
            let bookingId = $(this).attr('data-booking-id');
            let bookingStatus = $(this).attr('data-booking-status');
            let $this = $(this);
            if (input.val() !== '') {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/saveRCode',
                    data:{
                        _token: '<?=csrf_token()?>',
                        rCode: input.val(),
                        bookingId: bookingId
                    },
                    success: function(data) {
                        if (typeof data.success !== 'undefined') {
                            Materialize.toast(data.success, 4000, 'toast-success');
                            $this.hide();
                            input.attr('readonly', true);
                            if (bookingStatus === '4') {
                                let approvedButton = $this.parent().parent().parent().parent().find('.toggle-button1');
                                approvedButton.click();
                            }
                        }

                        if (typeof data.error !== 'undefined') {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            } else {
                Materialize.toast('R-Code can not be blank!', 4000, 'toast-alert');
            }
        });

        $('body').on('click', '.toggle-button1', function() {
            let id = $(this).data('id');
            let bookingID = $(this).attr('data-booking-id');
            let $this = $(this);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/booking/changeStatus/'+bookingID,
                data: {
                    id: id,
                    status: $this.attr("data-content"),
                    _token: '<?=csrf_token()?>'
                },
                success: function(data) {
                    $this.addClass('active2');
                    let parent = $this.parent();
                    let toggleButton2 = parent.find('.toggle-button2');
                    let toggleButton3 = parent.find('.toggle-button3');
                    $this.removeClass('pending');
                    toggleButton2.removeClass('pending');
                    toggleButton3.removeClass('canceled');
                    $this.parent().parent().parent().find('.monthContainer').removeAttr('class').addClass('monthContainer').addClass('active2');
                }
            });
        });

        $('body').on('click', '.toggle-button2', function() {
            let id = $(this).data('id');
            let bookingID = $(this).attr('data-booking-id');
            let $this = $(this);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/booking/changeStatus/'+bookingID,
                data: {
                    id: id,
                    status: $this.attr("data-content"),
                    _token: '<?=csrf_token()?>'
                },
                success: function(data) {
                    let parent = $this.parent();
                    let toggleButton1 = parent.find('.toggle-button1');
                    let toggleButton3 = parent.find('.toggle-button3');
                    toggleButton1.removeClass("active2");
                    $this.addClass("pending");
                    toggleButton3.removeClass("canceled");
                    $this.parent().parent().parent().find('.monthContainer').removeAttr('class').addClass('monthContainer').addClass('pending');
                }
            });
        });

        $('body').on('click', '.toggle-button3', function() {
            let id = $(this).data('id');
            let bookingID = $(this).attr('data-booking-id');
            let $this = $(this);

            if(confirm('Are you sure want to cancel?')){
                var cancelReason = prompt("please indicate your reason for cancellation", "");

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/booking/changeStatus/'+bookingID,
                    data: {
                        id: id,
                        status: $this.attr("data-content"),
                        cancelReason: cancelReason,
                        _token: '<?=csrf_token()?>'
                    },
                    success: function(data) {
                        if (data.success) {
                            let parent = $this.parent();
                            let toggleButton1 = parent.find('.toggle-button1');
                            let toggleButton2 = parent.find('.toggle-button2');
                            toggleButton1.removeClass("active2");
                            toggleButton2.removeClass("pending");
                            toggleButton1.fadeOut();
                            toggleButton2.fadeOut();
                            $this.addClass("canceled");
                            $this.parent().parent().parent().find('.monthContainer').removeAttr('class').addClass('monthContainer').addClass('canceled');
                        }
                        if (data.error) {
                            Materialize.toast('An error occured via sending request to Big Bus API!', 4000, 'toast-alert');
                        }
                    }
                });
            }
        });

        $(function() {
            $('.mdb-select').material_select();
            $("#productSelect").select2({
                matcher: matchCustom,
                templateResult: formatCustom,
                multiple: true
            });
            $('#productSelect').next().find('ul').css({'position': 'absolute', 'margin-top': '35px', 'display': 'block'});

            $("#optionSelect").select2({
                matcher: matchCustom,
                templateResult: formatCustom,
                multiple: true
            });
            $('#optionSelect').next().find('ul').css({'position': 'absolute', 'margin-top': '35px', 'display': 'block'});

            $("#restaurantSelect").select2({
                matcher: matchCustom,
                templateResult: formatCustom,
                multiple: true
            });
            $('#restaurantSelect').val([]);
            $('#restaurantSelect').next().find('ul').css({'position': 'absolute', 'margin-top': '35px', 'display': 'block'});
        });

        $('#productSelect').on('change', function() {
            $.ajax({
                type: "POST",
                url: "/booking/optionSelectMultiple",
                data: {
                    products: $('#productSelect').val(),
                    _token: '<?=csrf_token()?>'
                },
                success: function(data) {
                    let options = data.options;
                    let optionList = '';
                    for(let i=0; i<options.length; i++) {
                        optionList += '<option value=' + options[i]["referenceCode"] + '>' + options[i]["title"] + '</option>';
                    }
                    $('#optionSelect').html(optionList);
                }
            })

            $('#productSelect').next().parent().css('height', $('#productSelect').next().find('ul').height() + 20);
            $('#optionSelect').next().parent().css('height', '32px');
        });

        $('#optionSelect').on('change', function() {
            $('#optionSelect').next().parent().css('height', $('#optionSelect').next().find('ul').height() + 20);
        });

        $('#restaurantSelect').on('change', function() {
            $('#restaurantSelect').next().parent().css('height', $('#restaurantSelect').next().find('ul').height() + 20);
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

        $('#selectInputsArea').hover(function () {
            $('.sm-btn').fadeIn();
        });
        $('#selectInputsArea').mouseleave(function () {
            $('.sm-btn').fadeOut();
        });
        $('.sm-btn-success').on('click', function () {
            let token = "{{csrf_token()}}";
            $.ajax({
                url: '{{url("booking/ajax")}}',
                type: 'POST',
                dataType: 'json',
                data: {action: 'get_platforms', _token: token},
            }).done(function (response) {

                $('.mdb-select').material_select('destroy');
                $('#selectInputs').html('');
                var platforms = response.data;
                platforms.forEach(function (a) {
                    $('#selectInputs').append(' <option  value="' + a.id + '">' + a.name + '</option>');
                });
                $('.mdb-select').material_select();
            }).fail(function () {
                console.log("error");
            });
        });
        $('.sm-btn-danger').on('click', function () {
            let token = "{{csrf_token()}}";
            $.ajax({
                url: '{{url("booking/ajax")}}',
                type: 'POST',
                dataType: 'json',
                data: {action: 'get_platforms', _token: token},
            }).done(function (response) {

                $('.mdb-select').material_select('destroy');
                $('#selectInputs').html('');
                var platforms = response.data;
                platforms.forEach(function (a) {
                    $('#selectInputs').append(' <option selected  value="' + a.id + '">' + a.name + '</option>');
                })
                $('.mdb-select').material_select();
            }).fail(function () {
                console.log("error");
            });
        });

        function executeVisibilityChange() {
            if (document.visibilityState == "visible") {
                let pageID = 1;
                let maxPage = 1;
                $('#datatable_paginate .pagination').find('li').each(function () {
                    if($(this).hasClass('active'))
                        pageID = parseInt($(this).find('a').text());

                    if($(this).hasClass('next'))
                        maxPage = $(this).prev().find('a').text();
                });

                $('#datatable_paginate .pagination').find('li').each(function () {
                    if(parseInt($(this).find('a').text()) == pageID) {
                        if(pageID == 1) {
                            $('#datatable_previous').removeClass('disabled');
                            $('#datatable_next').click();
                            $('#datatable_previous').click();
                        }
                        else if(pageID == maxPage) {
                            $('#datatable_next').removeClass('disabled');
                            $('#datatable_previous').click();
                            $('#datatable_next').click();
                        }
                        else {
                            $('#datatable_previous').click();
                            $('#datatable_next').click();
                        }
                    }
                });
            }
        }

        document.addEventListener("visibilitychange", event => {
            executeVisibilityChange();
        })

    </script>
@elseif($page == 'bookings-v2-index')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/staterestore/1.1.1/js/dataTables.stateRestore.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.7/js/dataTables.scroller.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="{{ asset('keditor/build/keditor.min.js') }}"></script>
    <script src="{{ asset('js/admin/bookingsTable.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('js/waitme/waitMe.min.js')}}"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>
    @elseif($page == 'bulkmail')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script src="{{ asset('keditor/build/keditor.min.js') }}"  ></script>
    <script src="{{ asset('js/admin/columnSearchTable.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>

@elseif($page == 'meetings-index')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>

     <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/waitme/waitMe.min.js')}}"></script>


    <script>


        $(function() {

            var start= new Date();
            if(sessionStorage.hasOwnProperty('moveMeetingDate')){
                var dateTime=sessionStorage.getItem('moveMeetingDate');
                var dateTimeArr=dateTime.split(' ');
                var hour=dateTimeArr[1];
                var dateRaw=(dateTimeArr[0]).split('-');
                var dateDay=parseInt(dateRaw[2]);
                var dateMount=parseInt(dateRaw[1])-1;
                var dateYear=parseInt(dateRaw[0]);
                start.setDate(dateDay);
                start.setMonth(dateMount);
                start.setFullYear(dateYear);
            }

            if (sessionStorage.hasOwnProperty('moveMeetingDate')) {
                $('.hours .hour-item').each(function () {
                    var is = $(this);
                    if (is.attr('data-hour') == hour) {
                        is.click();
                    }
                });
            }

     /*       var key = 0;
            $(window).scroll(function(){
              var scroll = $(window).scrollTop();

             $("#movement-wrap").css("top", scroll+"px");
              if(scroll > 1000){
               if(!$("#main-container").hasClass('container-fluid')){
                $("#main-container").removeClass("container").addClass('container-fluid');

                $(".container-fluid #top-wrap").removeClass('col-md-12').addClass('col-md-4');
                $(".container-fluid #bottom-wrap").removeClass('col-md-12').addClass('col-md-8');



                $("#movement-wrap").css("position", "absolute");
                $("#movement-wrap").css("padding", "0 50px");
                $("#movement-wrap").css("overflow", "auto");
               }






              }

              if(scroll == 0){
                if($("#main-container").hasClass('container-fluid')){
                $("#main-container").removeClass("container-fluid").addClass('container');
                $(".container #top-wrap").removeClass('col-md-4').addClass('col-md-12');
                $(".container #bottom-wrap").removeClass('col-md-8').addClass('col-md-12');




                $("#movement-wrap").css("position", "static");
              }
              }
            });*/


            $(document).on('keyup', '.search-td', function(event) {
                event.preventDefault();
                $(".meet-table tr").removeClass('findit');
                $(this).css("background-color", "#FEF5E7 !important");

                if(event.keyCode === 13){
                 var inputData = $(this).val().trim();


                  if(inputData.length <= 2){
                    Materialize.toast('You Must Type at Least three Character', 4000, 'toast-alert');
                    return false;
                  }

                 var inputDataUpper = inputData.toUpperCase();
                 var inputDataLower = inputData.toLowerCase();
                 var findItems = [];

                 var allTd = $(".meet-table td");

                    allTd.each(function(index, el) {
                        var textLower = $(el).text().toLowerCase();
                        var textUpper = $(el).text().toUpperCase();


                        if(textLower.indexOf(inputDataLower)!== -1 || textUpper.indexOf(inputDataUpper) !== -1){
                            findItems.push(el);
                        }
                    });

                    if(findItems.length){
                        $(this).css("background-color", "#D1F2EB !important");
                        Materialize.toast(findItems.length + ' Item Match(es)', 4000, 'toast-success');
                    }else{
                        Materialize.toast(findItems.length + ' Item Match', 4000, 'toast-alert');
                        $(this).css("background-color", "#FADBD8 !important");
                    }

                    var avgTop=0;
                    findItems.forEach( function(element, index) {
                        avgTop = avgTop + $(element).closest("tr").offset().top;
                        $(element).closest('tr').addClass('findit').hide(200, function(){ $(this).show(800)});
                        console.log(avgTop);
                    });


                   if(findItems.length){
                    avgTop = (avgTop / findItems.length) | 0;


                    $('html, body').animate({
                        scrollTop: avgTop-100
                        }, 0, function(){
                            console.log("animate finished");
                        });

                   }


                }



            });


            $(document).on('click', '.check-status', function(event) {
                event.preventDefault();
               var $this = $(this);
               var data_id = $this.attr("data-check-id");

               if(!confirm("Are You Sure!")){
                return false;
               }

               $.ajax({
                   url: '{{url('meeting/ajax')}}',
                   type: 'POST',
                   dataType: 'json',
                   data: {action: 'check_or_not', _token: "{{csrf_token()}}", data_check_id:data_id},
               })
               .done(function(response) {
                   if(response.result){

                    if(response.status){

                        $this.removeClass('label-danger').addClass('label-success').text("Approved");

                    }else{
                        $this.removeClass('label-success').addClass('label-danger').text("Cancelled");
                    }



                   }
               })
               .fail(function() {
                   console.log("error");
               })
               .always(function() {
                   console.log("complete");
               });

            });




            $(document).on('click', '.turn-to-status', function(event) {
                event.preventDefault();

                var $this = $(this);
                var status = $(this).attr("data-status");
                var bookingId = $(this).attr("data-id");







               $.ajax({
                   url: '{{url("meeting/ajax")}}',
                   type: 'POST',
                   dataType: 'json',
                   data: {action: 'turn_to_status', _token:"{{csrf_token()}}", booking_id: bookingId},
               })
               .done(function(response) {

$("#turn-to-status .modal-body").html(response.view);



               })
               .fail(function() {
                alert("An Error Occured!");
                   console.log("error");
               })
               .always(function() {
                   console.log("complete");
               });




            });


            function checkGuides($parent, $getId){
                if($parent.find('.all-meeting-guides').find('ul').find("li").find('button[data-guide-id="'+$getId+'"]').length)
                    return true;

                    return false;

            }



            function scrollAnimate(target){

                var body = $("html, body");
body.stop().animate({scrollTop:$(target).offset().top-50}, 0, 'swing', function() {
                });

            }

         $("[data-toggle='tooltip']").tooltip();

            var minDate = $("#mindate").val();
            var maxDate = $("#maxdate").val();

            $('.meeting-datepicker').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: true,
            startDate:start,
            onShow: function() {
            },
            onSelect: function(date) {

              if($("#supplier-select").val() == ""){
                $("#supplier-select").css("border", "solid 2px red");
                Materialize.toast('you must choose at least one Supplier', 4000, 'toast-alert');
                return false;
              }
              $("#supplier-select").css("border", "solid 1px #f2f2f2");
              var supplierID = $("#supplier-select").val();


                $(".optionsWrapper").hide(0);
                $(".meetingsWrapper").hide(0);

                $(".hoursWrapper").show(600);



                 $(".hoursWrapper").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });

                if(date) {
                    $.ajax({
                        url: '{{url('meeting/ajax')}}',
                        type: 'POST',
                        dataType: 'json',
                        data: {action: 'get_meeting_hours', _token: "{{csrf_token()}}", date: date, supplierID: supplierID},
                    })
                    .done(function(response) {

                        $(".hoursWrapper .hours").html(response.view);

                        if (sessionStorage.hasOwnProperty('moveMeetingDate')) {
                            $('.hours .hour-item').each(function () {
                                var is = $(this);
                                if (is.attr('data-hour') == hour) {
                                    is.click();
                                }
                            });
                        }

                    })
                    .fail(function(xhr) {
                        console.log(xhr);
                        Materialize.toast(JSON.parse(xhr.responseText).error.message, 4000, 'toast-alert');
                        $(".hoursWrapper .hours").html('');
                    })
                    .always(function() {
                        $(".hoursWrapper").waitMe('hide');

                    });
                }




            },
            classes: "wider",

            //minDate: moment(minDate).isBefore(moment()) ? moment().toDate() : moment(minDate).toDate(),
            //maxDate: moment(maxDate).toDate()

           });


            $(document).on('change', '#supplier-select', function(event) {
                event.preventDefault();
                if(!$(this).val() == ""){
                    $("#supplier-select").css("border", "solid 1px #f2f2f2");
                }else{
                    $("#supplier-select").css("border", "solid 2px red");
                }
            });





      $(document).on('click', '.hour-item', function(event) {
          event.preventDefault();

          var date = $(this).attr("data-date");
          var time = $(this).attr("data-hour");
          var supplierID = $("#supplier-select").val();


          $(this).siblings('.hour-item').removeClass('clicked-hour');
          $(this).addClass('clicked-hour');

          $(".optionsWrapper").show(600, function(){
            //scrollAnimate(".optionsWrapper");

              $(".meetingsWrapper .meetings").html('');


                $(".optionsWrapper").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });



            $.ajax({
                url: '{{url('meeting/ajax')}}',
                type: 'POST',
                dataType: 'json',
                data: {action: 'get_meeting_options', _token: "{{csrf_token()}}", date: date, time: time, supplierID: supplierID},
            })
            .done(function(response) {
                $(".optionsWrapper .options ul").attr('data-date', date);
                $(".optionsWrapper .options ul").attr('data-hour', time);
                $(".optionsWrapper .options ul").html(response.view);
                 scrollAnimate(".optionsWrapper");
            })
            .fail(function(xhr) {

                console.log(xhr);
                     Materialize.toast(JSON.parse(xhr.responseText).error.message, 4000, 'toast-alert');
                     $(".optionsWrapper .options").html('');
            })
            .always(function() {

                  $(".optionsWrapper").waitMe('hide');
            });

          });



      });


        $(document).on('click', '#get-meeting-button', function(event) {
          event.preventDefault();

          if($(".options input:checked").length === 0){
             Materialize.toast('you must choose at least one option', 4000, 'toast-alert');
            return false;

          }


          $(".meetingsWrapper").show(0, function(){
             //scrollAnimate(".meetingsWrapper");




              $(".shaselect").each(function(index, el) {

                if($(this).val() != '0'){
                $(this).css("background-color", "#c9f3ef !important");
            }else{
                $(this).css("background-color", "#F5B7B1 !important");
            }

              });


            var supplierID = $("#supplier-select").val();
            var date = $(".optionsWrapper .options ul").attr("data-date");
            var hour = $(".optionsWrapper .options ul").attr("data-hour");
            var supplierID = $("#supplier-select").val();
            var options = $("#option-check-form").serializeArray();
            var optionsArray = [];

            options.forEach( function(element, index) {
               optionsArray.push(element.value);
            });

            console.log(optionsArray);


            $("#get-excel-button").attr({
                href: "{{url('meeting/excel')}}?date="+date+"&time="+hour+"&supplierID="+supplierID+"&options="+JSON.stringify(optionsArray)
            });

            $("#get-pdf-button").attr({
                href: "{{url('meeting/pdf')}}?date="+date+"&time="+hour+"&supplierID="+supplierID+"&options="+JSON.stringify(optionsArray)
            });






                $(".meetingsWrapper").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });

                $.ajax({
                    url: '{{url('meeting/ajax')}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'get_meetings', _token: "{{csrf_token()}}" , date:date, time: hour, options: optionsArray, supplierID: supplierID},
                })
                .done(function(response) {
                    console.log("success");
                    $(".meetingsWrapper .meetings").html(response.view);
                    scrollAnimate(".meetingsWrapper");
                })
                .fail(function(xhr) {

                    console.log(xhr);
                     Materialize.toast(JSON.parse(xhr.responseText).error.message, 4000, 'toast-alert');
                     $(".meetingsWrapper .meetings").html('');
                })
                .always(function() {
                    console.log("complete");

                      $(".meetingsWrapper").waitMe('hide');
                });






          });
      });


        $(document).on('change', '.shaselect.meet', function(event) {
            event.preventDefault();

            var $parent = $(this).closest('.meeting-wrap');

            var getId = $(this).val();
            var selectedText = $(this).find('option:selected').text();

            if(getId == 0) return false;


             if(!checkGuides($parent, getId)){
                var templateHtml = `<li>${selectedText} <button data-guide-id="${getId}" class="remove-guide-item icon-cz-cancel"><i class="icon-delete"></i></button></li>`;

              $parent.find('.all-meeting-guides').find('ul').prepend(templateHtml).find('li').eq(0).hide().show(400);


               var option = $parent.find('.all-meeting-guides').attr("data-option");
               var date = $parent.find('.all-meeting-guides').attr("data-date");
               var hour = $parent.find('.all-meeting-guides').attr("data-hour");
               var operating_hour = $parent.find('.all-meeting-guides').attr("data-operating-hour");
               var guides = [];


               $parent.find('.all-meeting-guides').find('ul').find('li').each(function(index, el) {
                   guides.push($(this).find("button").attr("data-guide-id"));
               });



             $.ajax({
                 url: '{{url('meeting/ajax')}}',
                 type: 'POST',
                 dataType: 'json',
                 data: {action: 'set_guides', _token:"{{csrf_token()}}", option: option, date: date, time: hour, guides: JSON.stringify(guides), operating_hour: operating_hour},
             })
             .done(function(response) {
                if(response.status === "success"){
                 Materialize.toast("Changes Has Been Done Successfully", 4000, 'toast-success');
                }else{
                Materialize.toast("There is An Error", 4000, 'toast-alert');
                }

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
                 Materialize.toast("there is an error", 4000, 'toast-alert');
             })
             .always(function() {
                 console.log("complete");
             });



             }else{

               Materialize.toast('you have already added this guide', 4000, 'toast-alert');
                    return;



             }









            if($(this).val() != '0'){
                $(this).css("background-color", "#c9f3ef !important");
            }else{
                $(this).css("background-color", "#F5B7B1 !important");
            }

        });


        $(document).on('click', '.remove-guide-item', function(event) {
            event.preventDefault();
            var $this = $(this);
            var $thisIndex = $(this).closest('li').index();
            var $thisCopy = $(this).closest("li").clone();
            console.log($thisIndex);

            $parent = $this.closest('.all-meeting-guides');
            $this.closest('li').remove();




               var option = $parent.attr("data-option");
               var date = $parent.attr("data-date");
               var hour = $parent.attr("data-hour");
               var operating_hour = $parent.attr("data-operating-hour");
               var guides = [];


               $parent.find('ul').find('li').each(function(index, el) {
                   guides.push($(this).find("button").attr("data-guide-id"));
               });



             $.ajax({
                 url: '{{url('meeting/ajax')}}',
                 type: 'POST',
                 dataType: 'json',
                 data: {action: 'set_guides', _token:"{{csrf_token()}}", option: option, date: date, time: hour, guides: JSON.stringify(guides), operating_hour: operating_hour},
             })
             .done(function(response) {
                if(response.status === "success"){
                 Materialize.toast("Changes Has Been Done Successfully", 4000, 'toast-success');

                }else{
                Materialize.toast(response.message, 4000, 'toast-alert');

                if($parent.find('ul').find("li").length == 0){
                    $parent.find('ul').prepend($thisCopy);
                }else if($parent.find('ul').find("li").length == $thisIndex){
                   $parent.find('ul').append($thisCopy);
                }else{
                  $parent.find('ul').find("li").eq($thisIndex).before($thisCopy);
                }




                }

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
                 Materialize.toast("there is an error", 4000, 'toast-alert');
             })
             .always(function() {
                 console.log("complete");
             });











        });

            if (sessionStorage.hasOwnProperty('moveMeetingDate')) {
                $('.datepicker--cell-day').each(function () {
                    var is = $(this);
                    if (is.attr('data-year') == dateYear && is.attr('data-month') == dateMount && is.attr('data-date') == dateDay) {
                        is.click();
                    }
                });
            }
        });
    </script>



    @elseif($page == 'guides-index')



<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/jquery.timepicker.js')}}"></script>
<script src="{{asset('js/waitme/waitMe.min.js')}}"></script>


 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCiMSJJWyJMpXHELXolLJgoZVcrv9ovaT0&language=en&libraries=places"
        async defer></script>
 {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIas7VCaChSga1sbY2J6TfgPVuDkMRmsk&language=en&libraries=places&callback=initMap"
        async defer></script>--}}

    <script>











        $(document).ready(function() {



            $(document).on('click', '.set-off-day', function(event) {
                event.preventDefault();


                if(!confirm("Are You Sure ?")){
                    return false;
                }


                var $this = $(this);
                var guide_id =  $this.data("guide-id");
                var off_date =  $this.data("date");


                    $this.closest('table').waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });



                $.ajax({
                    url: '{{url("guide/ajax")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: '{{csrf_token()}}', action: "set_day_off", guide_id: guide_id, off_date: off_date},
                })
                .done(function(response) {
                    if(response.status == "success"){
                        Materialize.toast('Status Change Successfully!', 4000, 'toast-success');

                        if(response.type == "add"){

                            var html =                       `<tr style="background-color: #e0e0ad;" class="set-off-day-tr">
                                                                  <td style="text-align:center;"><a href="#" style="font-size:16px;">Off Day</a></td>
                                                              </tr>`;
                          $this.removeClass("btn-default").addClass("btn-danger");

                          $this.closest("tr").find(".inline-table").prepend(html);


                             var total_off_day = $(".set-off-day-tr").length;
                           $("#off-day-target-result").html("<span>"+total_off_day+" Day</span>");
                        }else{
                         $this.removeClass("btn-danger").addClass("btn-default");
                          $this.closest("tr").find(".inline-table").html("");


                             var total_off_day = $(".set-off-day-tr").length;
                           $("#off-day-target-result").html("<span>"+total_off_day+" Day</span>");
                        }


                    }else{
                         Materialize.toast(response.message, 4000, 'toast-alert');
                    }

                })
                .fail(function() {
                    Materialize.toast('An Error Occured!', 4000, 'toast-alert');
                    console.log("error");
                })
                .always(function() {
                    $this.closest('table').waitMe("hide");
                    console.log("complete");
                });

            });





            $(document).on('click', '.shift-log-delete-button', function(event) {
                event.preventDefault();


                if(!confirm('Are You Sure')){
                    return false;
                }

                var $this = $(this);
                var log_id = $(this).attr("data-id");

                 $this.closest('tr').waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });



                   $.ajax({
                       url: '{{url("guide/ajax")}}',
                       type: 'POST',
                       dataType: 'json',
                       data: {_token: '{{csrf_token()}}', action: 'delete_log', data_id: log_id},
                   })
                   .done(function(response) {
                    if(response.status == "success"){
                         Materialize.toast('Log Removed Successfully', 4000, 'toast-success');
                         $this.closest('tr').fadeOut(400, function() {
                             $(this).remove();
                         });
                    }

                       console.log("success");
                   })
                   .fail(function() {
                       console.log("error");
                       Materialize.toast('An Error Occured!', 4000, 'toast-alert');
                   })
                   .always(function() {
                       console.log("complete");
                        $this.closest('tr').waitMe('hide');
                   });





            });





                $(document).on('click', '#shift-delete-button', function(event) {
                event.preventDefault();


                 if(!confirm('Are You Sure')){
                    return false;
                }

                 var shift_id = $("#inoutmodal").attr("data-shift-id");
                 if(shift_id){


                   $("#inoutmodal .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });

                   $.ajax({
                       url: '{{url("guide/ajax")}}',
                       type: 'POST',
                       dataType: 'json',
                       data: {_token: '{{csrf_token()}}', action: "delete_shift", data_id: shift_id},
                   })
                   .done(function(response) {
                    if(response.status == "success"){
                         Materialize.toast('Shift Deleted successfully', 4000, 'toast-success');
                         $("tr[data-tr-shift-id='"+shift_id+"']").fadeOut(400, function() {
                             $(this).remove();
                         });
                    }

                       console.log("success");
                   })
                   .fail(function() {
                       console.log("error");
                       Materialize.toast('An Error Occured!', 4000, 'toast-alert');
                   })
                   .always(function() {
                       console.log("complete");
                        $("#inoutmodal .modal-body").waitMe('hide');
                   });



                 }


            });






            $(document).on('click', '#shift-approve-button', function(event) {
                event.preventDefault();

                 var shift_id = $("#inoutmodal").attr("data-shift-id");
                 if(shift_id){


                   $("#inoutmodal .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });

                   $.ajax({
                       url: '{{url("guide/ajax")}}',
                       type: 'POST',
                       dataType: 'json',
                       data: {_token: '{{csrf_token()}}', action: "approve_or_not", data_id: shift_id},
                   })
                   .done(function(response) {
                    if(response.status == "success"){
                         Materialize.toast('Shift approved successfully', 4000, 'toast-success');
                         $("tr[data-tr-shift-id='"+shift_id+"']").removeClass('tr-purple');
                    }

                       console.log("success");
                   })
                   .fail(function() {
                       console.log("error");
                       Materialize.toast('An Error Occured!', 4000, 'toast-alert');
                   })
                   .always(function() {
                       console.log("complete");
                        $("#inoutmodal .modal-body").waitMe('hide');
                   });



                 }


            });








          $(document).on('click', '#shift-create-button', function(event) {
         event.preventDefault();
         var serialize_data = $("#shift-form-create").serialize();
         console.log(serialize_data);



           $("#inoutmodal-create .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


           $.ajax({
               url: '{{url("guide/ajax")}}',
               type: 'POST',
               dataType: 'json',
               data:serialize_data,
           })
           .done(function(response) {
            if(response.status == "success"){
                Materialize.toast('Shift has been Created successfully', 4000, 'toast-success');
                window.location.reload();
            }


               console.log("success");
           })
           .fail(function() {
               console.log("error");
               Materialize.toast('An Error Occured!', 4000, 'toast-alert');
           })
           .always(function() {
               console.log("complete");
               $("#inoutmodal .modal-body").waitMe('hide');
           });




     });





     $(document).on('click', '#shift-save-change-button', function(event) {
         event.preventDefault();
         var serialize_data = $("#shift-form-update").serialize();
         console.log(serialize_data);


           $("#inoutmodal .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


           $.ajax({
               url: '{{url("guide/ajax")}}',
               type: 'POST',
               dataType: 'json',
               data:serialize_data,
           })
           .done(function(response) {
            if(response.status == "success"){
                Materialize.toast('Shift has been updated successfully', 4000, 'toast-success');
                window.location.reload();
            }


               console.log("success");
           })
           .fail(function() {
               console.log("error");
               Materialize.toast('An Error Occured!', 4000, 'toast-alert');
           })
           .always(function() {
               console.log("complete");
               $("#inoutmodal .modal-body").waitMe('hide');
           });




     });






     $(document).on('change', 'select[name="index_guide_search"]', function(event) {
         event.preventDefault();


         $("#index-month-form").submit();
     });



       $(document).on('click', '#details-table .inline-table tr.update-shift', function(event) {
           event.preventDefault();

           var shift_id = $(this).attr("data-id");
           $("#inoutmodal").attr("data-shift-id", shift_id);



           $("#inoutmodal .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });



           $.ajax({
               url: '{{url('guide/ajax')}}',
               type: 'POST',
               dataType: 'json',
               data: {_token: '{{csrf_token()}}', action: "get_shift_modal", data_id: shift_id},
           })
           .done(function(response) {
               $("#inoutmodal .modal-body").html(response.view);
               console.log("success");
           })
           .fail(function() {
               console.log("error");
           })
           .always(function() {
               console.log("complete");
               $("#inoutmodal .modal-body").waitMe('hide');
           });

       });
















              $(document).on('click', '#details-table .inline-table tr.create-shift', function(event) {
           event.preventDefault();

           var meeting_id = $(this).attr("data-meeting-id");
           var meeting_point = $(this).attr("data-meeting-point");
           var meeting_date = $(this).attr("data-meeting-date");
           var guide_id = $(this).attr("data-target-guide-id");
           $("#inoutmodal-create").attr("data-meeting-id", meeting_id);
           $("#inoutmodal-create").attr("data-meeting-point", meeting_point);
           $("#inoutmodal-create").attr("data-meeting-date", meeting_date);
           $("#inoutmodal-create").attr("data-guide-id", guide_id);



           $("#inoutmodal-create .modal-body").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });



           $.ajax({
               url: '{{url('guide/ajax')}}',
               type: 'POST',
               dataType: 'json',
               data: {_token: '{{csrf_token()}}', action: "get_shift_modal_create", data_meeting_id: meeting_id, data_meeting_point: meeting_point, data_meeting_date: meeting_date, data_guide_id:guide_id},
           })
           .done(function(response) {
               $("#inoutmodal-create .modal-body").html(response.view);
               console.log("success");
           })
           .fail(function() {
               console.log("error");
           })
           .always(function() {
               console.log("complete");
               $("#inoutmodal-create .modal-body").waitMe('hide');
           });

       });







        });
    </script>




@elseif($page == 'guides-planning')
    <script src="{{asset('/calendar/lib/moment.js')}}"></script>
    <script src="{{asset('/calendar/lib/jquery.js')}}"></script>
    <script src="{{asset('/calendar/lib/jquery-ui.js')}}"></script>
    <script src="{{asset('/calendar/js/bootstrap.js')}}"></script>
    <script src="{{asset('/calendar/js/fullcalendar.js')}}"></script>
    <script src="{{asset('/calendar/js/lang-all.js')}}"></script>
    <script src="{{asset('/calendar/js/jquery.calendar.js')}}"></script>
    <script src="{{asset('/calendar/lib/spectrum/spectrum.js')}}"></script>

    <script src="{{asset('/calendar/lib/timepicker/jquery-ui-sliderAccess.js')}}"></script>
    <script src="{{asset('/calendar/lib/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>

    <script src="{{asset('/calendar/js/custom.js')}}"></script>

    <script src="{{asset('/calendar/js/g.map.js')}}"></script>
    <script src="{{asset('/calendar/js/gcal.js')}}"></script>
    <script src="http://maps.google.com/maps/api/js" defer></script>

    <!-- call calendar plugin -->
    <script type="text/javascript">
        $().FullCalendarExt({
            calendarSelector: '#calendar',
            lang: 'en',
            fc_extend: {
                nowIndicator: true
            }
        });

        $('.sb2').css('margin-top', '0px');

        /*
        $('#guide-selection').on('change', function(e) {
            let token = $('#cal_token').val();
            let user_id = $('#guide-selection').val();

            let elements = $('#calendar td.fc-day');
            let start = elements[0].getAttribute('data-date');
            let end = elements[elements.length-1].getAttribute('data-date');

            $.ajax({
                type: 'GET',
                url: '/guide/planning/cal_events',
                    data: {
                        token: token,
                        user_id: user_id,
                        start: start,
                        end: end,
                    },
                    success: function(data) {

                    }
            });

        });
        */

        function redirectToExporter() {
            let user_id = $('#guide-selection').val();
            window.location.href = "/guide/planning/exporter?user_id="+user_id;
        }
    </script>




@elseif($page == 'subuser-create')
    <script>
        $(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                if ($('input[name="roles[]"]:checked').length === 0) {
                    Materialize.toast('You should check at least one role!', 4000, 'toast-alert');
                    return;
                }
                $('#subUserForm')[0].submit();
            });
        });
    </script>
@elseif($page == 'updatehomebanner')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#homeBanner').on('change', function() {
            let value = document.getElementById('homeBanner').value;
            $('#homeBannerButton').val(value);
            readURL(this);
        });
    </script>
@elseif($page == 'blog-create' || $page == 'blog-edit')
    <script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
    <script src="{{'../../keditor/build/keditor.min.js'}}"></script>
    <script src="{{asset('../../keditor/src/lang/en.js')}}"></script>
    <script>
        KEDITOR.create('postContent', {
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic'],
                ['removeFormat'],
                ['fontColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'image', 'video'],
                ['fullScreen', 'codeView'],
                ['preview', 'print'],
            ],
            minHeight: '1000px',
            imageUploadUrl: '/blog/create/uploadImageForBlogPost',
            imageUploadHeader: {
                contentType: 'multipart/form-data',
                csrfToken: $('meta[name="csrf-token"]').attr('content'),
            }
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#coverPhoto').on('change', function() {
            let value = document.getElementById('coverPhoto').value;
            $('#coverPhotoButton').val(value);
            readURL(this);
        });

        $('form').on('submit', function(e) {
            e.preventDefault();
            let formType = $(this).data('type');
            let postTitle = $('#postTitle').val();
            let metaTitle = $('#metaTitle').val();
            let metaDescription = $('#metaDescription').val();
            let metaKeywords = $('#metaKeywords').val();
            if (postTitle === '') {
                Materialize.toast('Title shouldn\'t be blank!', 4000, 'toast-alert');
                return;
            }
            if (metaTitle === '') {
                Materialize.toast('Meta Title shouldn\'t be blank!', 4000, 'toast-alert');
                return;
            }
            if (metaDescription === '') {
                Materialize.toast('Meta Description shouldn\'t be blank!', 4000, 'toast-alert');
                return;
            }
            if (metaKeywords === '') {
                Materialize.toast('Meta Keywords shouldn\'t be blank!', 4000, 'toast-alert');
                return;
            }
            let coverPhoto = formType === 'create' ? $('#coverPhoto').val() : $('#blah').attr('src');
            if (coverPhoto === '') {
                Materialize.toast('You must upload a Cover Photo!', 4000, 'toast-alert');
                return;
            }
            $('#postContent').val($('.keditor-editable').html());
            let postContent = $('#postContent').val();
            if (postContent === '<p><br></p>') {
                Materialize.toast('Content shouldn\'t be blank!', 4000, 'toast-alert');
                return;
            }
            $('#blogForm')[0].submit();
        });
    </script>
@elseif($page == 'gallery-editcityphoto')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#coverPhoto').on('change', function() {
            let value = document.getElementById('coverPhoto').value;
            readURL(this);
        });
    </script>
@elseif($page == 'gallery-cityphoto')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(function() {
            $('#coverPhoto').on('change', function() {
                let value = document.getElementById('coverPhoto').value;
                $('#coverPhotoButton').val(value);
                readURL(this);
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });

            $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        for (let i = 0; i < cities.length; i++) {
                            $('#cities').append('<option value="'+cities[i]+'">'+cities[i]+'</option>');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'voucher-create')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('/js/airdatepicker/datepicker.en.js')}}"></script>
    <script>
        $('#productId').on('change', function() {
            let productId = $('#productId').val();
            $('#optionId').html('<option selected value="">Select Option</option>');
            if (productId !== '') {
                $.ajax({
                    method: 'POST',
                    url: '/voucher/getOptions',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        productId: productId
                    },
                    success: function(data) {
                        let options = data.options;
                        for (let i = 0; i < options.length; i++) {
                            $('#optionId').append('<option  data-foo="' + options[i].referenceCode + '"  data-is-connected="' + options[i].connectedToApi + '" data-ref-code="' + options[i].referenceCode + '" value="' + options[i].id + '">' + options[i].title + '</option>');
                        }
                    }
                });
            }
        });
    </script>
@elseif($page == 'finance-index')
    <script>
        $('.showHideButton').on('click', function() {
            let isShown = $(this).attr('data-shown');
            if (isShown === '1') {
                $(this).parent().parent().find('.yearsWrapper').hide();
                $(this).attr('data-shown', '0');
                $(this).text('+');
            } else {
                $(this).parent().parent().find('.yearsWrapper').show();
                $(this).attr('data-shown', '1');
                $(this).text('-');
            }
        });

        $('form#finance select').on('change', function () {
            $.each($('form#finance select').not($(this)[0]), (k, v) => {
                $(v).find('option[disabled]').prop('selected', true)
            })
        });

    </script>




@elseif($page == 'finance-bills')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>

     <link href="{{asset('js/waitme/waitMe.min.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/waitme/waitMe.min.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" integrity="sha512-k2GFCTbp9rQU412BStrcD/rlwv1PYec9SNrkbQlo6RZCf75l6KcC3UwDY8H5n5hl4v77IDtIPwOk9Dqjs/mMBQ==" crossorigin="anonymous"></script>

    <script>

                    $('.billing-datepicker').datepicker({
            dateFormat: 'yyyy-mm-dd',
            toggleSelected: true,


            onShow: function() {

            },
            onSelect: function(date) {
                console.log(date);








                 $("#billing-image-wrapper").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


                 $.ajax({
                     url: '{{url('finance/ajax')}}',
                     type: 'POST',
                     dataType: 'json',
                     data: {action: 'get_billing_images', _token: "{{csrf_token()}}", date: date},
                 })
                 .done(function(response) {
                     console.log(response);

                     $("#billing-image-wrapper").html(response.view);


                 })
                 .fail(function(xhr) {
                     console.log(xhr);
                    // Materialize.toast(JSON.parse(xhr.responseText).data.message, 4000, 'toast-alert');
                     $("#billing-image-wrapper").html('');
                 })
                 .always(function() {
                     $("#billing-image-wrapper").waitMe('hide');
                 });




            },
            classes: "wider",

            //minDate: moment(minDate).isBefore(moment()) ? moment().toDate() : moment(minDate).toDate(),
            //maxDate: moment(maxDate).toDate()

           });


    </script>

    <script>

    </script>






@elseif($page == 'voucher-edit')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('/js/airdatepicker/datepicker.en.js')}}"></script>
@elseif($page == 'external-payment-create')
    <script src="{{asset('/js/clipboard.min.js')}}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script>
        $(function() {
            let clipboard = new ClipboardJS('.copyToClipboard');
            clipboard.on('success', function(e) {
                Materialize.toast('Payment Link is copied!', 4000, 'toast-success');
                e.clearSelection();
            });

            clipboard.on('error', function(e) {
                Materialize.toast('Payment Link is not copied due to a reason. Please try again!', 4000, 'toast-alert');
            });

            $('#sendPaymentLinkButton').on('click', function() {
                let email = $('#email').val();
                let price = $('#price').val();
                let message = $('#message').val();
                let currency = $('#currency').val();
                let bookingRefCode = $('#bookings').val();
                let $this = $(this);
                $.ajax({
                    method: 'POST',
                    url: '/storePaymentLink',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        email: email,
                        price: price,
                        message: message,
                        currency: currency,
                        bookingRefCode: bookingRefCode,
                    },
                    success: function(data) {
                        if (data.success) {
                            $this.attr('disabled', true);
                            Materialize.toast(data.success, 4000, 'toast-success');
                            $('#paymentLink').val(data.payment_link);
                            $('#paymentLinkDiv').show();
                        }
                    }
                });
            });

            $("#bookings").select2({});
        });
    </script>

@elseif($page == 'external-payment-edit')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            const selectInputs2 = $('#bookings')

            $.ajax({
                url: '/external-booking-ref-code/'+ {{ $externalPayment->id }},
                type: 'POST',
                data: {
                    q: '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }).then(function (data) {
                var option;

                if(data.item.length) {

                $.each(data, function (key, val) {
                    option += `<option value="${val}" selected>${val}</option>`;
                });


                selectInputs2.append(option).trigger('change');
                }

                // manually trigger the `select2:select` event
                selectInputs2.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });

            });

            selectInputs2.select2({
                ajax: {
                    url: '/external-booking-ref-code/',
                    type: 'post',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    delay: 800,
                    processResults: function (data) {
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    text: item.mutatorRefCode,
                                    id: item.mutatorRefCode
                                }
                            })
                        }
                    }
                },
                cacheable: true,
                 placeholder: 'Search for a platform',
            })
        })
    </script>

@elseif($page == 'barcodes-create')
    <script>

        $(function() {

            $('#cruiseBarcodeImport').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);


                $.ajax({
                    url: '/barcodes-excel-import-ajax',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success:function (res){
                        Materialize.toast(res.message, 4000, 'toast-success');
                        $('#cruiseBarcodeImport')[0].reset();
                    },
                    error: function (err) {
                        Materialize.toast(err.responseJSON.message, 4000, 'toast-alert');
                    }

                })
            })


            // if($('#ticketTypeSelect').val() == 4){
            //      $("#importBarcodes").show();
            // }
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "4"){
            //       $("#importBarcodes").show();
            //     }else{
            //      $("#importBarcodes").hide();
            //     }
            // });
            //
            //
            //
            //
            //
            //
            //    if($('#ticketTypeSelect').val() == 6){
            //      $("#importPDFfORTriomphe").show();
            // }
            // if($('#ticketTypeSelect').val() == 20){
            //     $("#importPDFfORSainte").show();
            // }
            // if($('#ticketTypeSelect').val() == 25){
            //     $("#importGrevinBarcodes").show();
            // }
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "6"){
            //       $("#importPDFfORTriomphe").show();
            //     }else{
            //      $("#importPDFfORTriomphe").hide();
            //     }
            //     if($(this).val() == "20"){
            //         $("#importPDFfORSainte").show();
            //     }else{
            //         $("#importPDFfORSainte").hide();
            //     }
            //     if($(this).val() == "25"){
            //         $("#importGrevinBarcodes").show();
            //     }else{
            //         $("#importGrevinBarcodes").hide();
            //     }
            //     if($(this).val() == "27"){
            //         $("#importPompidouBarcodes").show();
            //     }else{
            //         $("#importPompidouBarcodes").hide();
            //     }
            //
            //     if($('#ticketTypeSelect').val() == 32 || $('#ticketTypeSelect').val() == 33){
            //         $('#barcodeModalforOrsay form #orsay-orangerie-ticket-type').val($('#ticketTypeSelect').val())
            //         $("#importOrsayBarcodes").show();
            //         if($('#ticketTypeSelect').val() == 32){
            //             $("#importOrsayBarcodes").html('Import Barcodes For Orsay')
            //         }else{
            //             $("#importOrsayBarcodes").html('Import Barcodes For Orangerie')
            //         }
            //     }else{
            //         $("#importOrsayBarcodes").hide();
            //     }
            // });
            // if($('#ticketTypeSelect').val() == 7){
            //  $("#importOrsayBarcodes").show();
            // }
            //
            // if($('#ticketTypeSelect').val() == 29){
            //     $("#importPicassoBarcodes").show();
            // }
            //
            // if($('#ticketTypeSelect').val() == 28){
            //     $("#importRodinBarcodes").show();
            // }
            //
            // if($('#ticketTypeSelect').val() == 30){
            //     $("#importMontparnasseAdultBarcodes").show();
            // }
            //
            // if($('#ticketTypeSelect').val() == 31){
            //     $("#importMontparnasseInfantBarcodes").show();
            // }
            //
            // $('.deactiveOnClick').on('click', function() {
            //     if($(this).parent().prev().find('input').val())
            //         $(this).addClass('disabled')
            // })
            //
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "7"){
            //       $("#importOperaBarcodes").show();
            //     }else{
            //      $("#importOperaBarcodes").hide();
            //     }
            // });
            //
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "29"){
            //         $("#importPicassoBarcodes").show();
            //     }else{
            //         $("#importPicassoBarcodes").hide();
            //     }
            // });
            //
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "28"){
            //         $("#importRodinBarcodes").show();
            //     }else{
            //         $("#importRodinBarcodes").hide();
            //     }
            // });
            //
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "30"){
            //         $("#importMontparnasseAdultBarcodes").show();
            //     }else{
            //         $("#importMontparnasseAdultBarcodes").hide();
            //     }
            // });
            //
            // $('#ticketTypeSelect').on('change', function() {
            //     let barcodeDiv = $('#barcodeDiv');
            //     barcodeDiv.html('');
            //
            //     if($(this).val() == "31"){
            //         $("#importMontparnasseInfantBarcodes").show();
            //     }else{
            //         $("#importMontparnasseInfantBarcodes").hide();
            //     }
            // });

            $('#barcodeCount').show();
            $('#barcodeCreateButton').show();
            $('#alertBox').show();
            let barcodeCreateButton = $('#barcodeCreateButton');
            let sendBarcodes = $('#sendBarcodes');
            barcodeCreateButton.on('click', function() {
                let barcodeDiv = $('#barcodeDiv');
                barcodeDiv.html('');
                let ticketType = $('#ticketTypeSelect').val();
                let barcodeCount = $('#barcodeCount').val();
                if (ticketType == '4' || ticketType == '6') {
                    block = '<table>' +
                        '<thead class="hidden-md hidden-sm hidden-xs">' +
                        '<tr>' +
                        '<th>Barcode</th>' +
                        '<th>Reservation Number</th>' +
                        '<th>Expiration Date (dd/mm/yyyy)</th>' +
                        '</tr>' +
                        '</thead>' ;
                } else if (ticketType == '7') {
                    block = '<table>' +
                        '<thead class="hidden-md hidden-sm hidden-xs">' +
                        '<tr>' +
                        '<th>Barcode</th>' +
                        '<th>Reservation Number</th>' +
                        '<th>Recode</th>' +
                        '<th>Contact</th>' +
                        '<th>Expiration Date (dd/mm/yyyy)</th>' +
                        '</tr>' +
                        '</thead>' ;
                } else if (ticketType == '16' || ticketType == '17' || ticketType == '18' || ticketType == '19' || ticketType == '20') {
                    block = '<table>' +
                        '<thead class="hidden-md hidden-sm hidden-xs">' +
                        '<tr>' +
                        '<th>Barcode</th>' +
                        '<th>Reservation Number</th>' +
                        '</tr>' +
                        '</thead>';
                } else {
                    block = '<table>' +
                        '<thead class="hidden-md hidden-sm hidden-xs">' +
                        '<tr>' +
                        '<th>Barcode</th>' +
                        '</tr>' +
                        '</thead>';
                }
                for (let i = 0; i < parseInt(barcodeCount); i++) {
                    if (ticketType === '4' || ticketType == '6') {
                        block += '<tbody><tr>' +
                            '<td><input id="barcode' + i + '" type="text" placeholder="Enter Your Barcode"></td>' +
                            '<td><input id="reservationNumber' + i + '" type="text" placeholder="Enter Your Reservation Code"></td>' +
                            '<td><input id="expirationDate' + i + '" type="text" placeholder="Expiration Date(dd/mm/yyyy)"></td>';
                    } else if (ticketType === '7') {
                        block += '<tbody><tr>' +
                            '<td><input id="barcode' + i + '" type="text" placeholder="Enter Your Barcode"></td>' +
                            '<td><input id="reservationNumber' + i + '" type="text" placeholder="Enter Your Reservation Code"></td>' +
                            '<td><input id="recode'+i+'" type="text" placeholder="Enter Your Recode"></td>' +
                            '<td><input id="contact'+i+'" type="text" placeholder="Enter Your Contact">' +
                            '<td><input id="expirationDate' + i + '" type="text" placeholder="Expiration Date(dd/mm/yyyy)"></td>';
                    } else if (ticketType == '16' || ticketType == '17' || ticketType == '18' || ticketType == '19' || ticketType == '20') {
                        block += '<tbody><tr>' +
                            '<td><input id="barcode'+i+'" type="text" placeholder="Enter Your Barcode"></td>' +
                            '<td><input id="reservationNumber'+i+'" type="text" placeholder="Enter Your Reservation Code"></td>';
                    } else {
                        block += '<tbody><tr>' +
                            '<td><input id="barcode' + i + '" type="text" placeholder="Enter Your Barcode"></td>';
                    }
                    block += '</tr></tbody>';
                }

                barcodeDiv.append(block);
            });

            sendBarcodes.on('click', function(data) {
                let barcodeDiv = $('#barcodeDiv');
                let ticketType = $('#ticketTypeSelect').val();
                let barcodeCount = $('#barcodeCount').val();
                let barcodes = [];
                let reservationNumbers = [];
                let expirationDates = [];
                let recodes = [];
                let contacts = [];
                for (let i = 0; i < parseInt(barcodeCount); i++) {
                    if ($('#barcode'+i).val().length != 0) {
                        barcodes.push($('#barcode'+i).val());
                        if (ticketType === '4') {
                            reservationNumbers.push($('#reservationNumber'+i).val());
                            expirationDates.push($('#expirationDate'+i).val());
                        } else if (ticketType === '6') {
                            if (dateValidation(i)) {
                                reservationNumbers.push($('#reservationNumber'+i).val());
                                expirationDates.push($('#expirationDate'+i).val());
                            } else {
                                Materialize.toast('Date Fields must be typed as dd/mm/yyyy', 4000, 'toast-alert');
                                return;
                            }
                        } else if (ticketType === '7') {
                            if (dateValidation(i, true, true)) {
                                reservationNumbers.push($('#reservationNumber'+i).val());
                                expirationDates.push($('#expirationDate'+i).val());
                                recodes.push($('#recode'+i).val());
                                contacts.push($('#contact'+i).val());
                            } else {
                                Materialize.toast('Date Fields must be type as dd/mm/yyyy', 4000, 'toast-alert');
                                return;
                            }
                        }
                    }
                }

                $.ajax({
                    type: 'POST',
                    url: '/barcodes/store',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        barcodes: barcodes,
                        reservationNumbers: reservationNumbers,
                        expirationDates: expirationDates,
                        ticketType: ticketType,
                        recodes: recodes,
                        contacts: contacts
                    },
                    success: function(data) {
                        if (data.oldBarcodes.length > 0) {
                            $('#barcodeDiv').html('');
                            let block = '<div class="col-md-12"><label style="color: white;!important;" class="row col-md-12 label-danger label"><span style="padding-bottom: 5px!important;">Recording is Successfuly !</span><br>These barcodes have been recorded before. Please check them again.</label></div>' +
                                '<div style="margin-top: 80px" class="row col-md-12">';
                            for (let i = 0; i < data.oldBarcodes.length; i++) {
                                block += '<div class="col-md-3">' +
                                    '<input style="padding-left:5px;border: 1px solid #ff0000" type="text" value="'+data.oldBarcodes[i]+'">' +
                                    '</div>';
                            }
                            block += '</div>';
                            $('#barcodeDiv').append(block);
                        } else {
                            $('#barcodeDiv').html('');
                            let block = '<div class="col-md-12"><label  style="color:white!important;" class="row col-md-12 label-success label">All barcodes has been recorded successfuly !</label></div>'
                            $('#barcodeDiv').append(block);
                        }
                    },
                    error: function(data) {
                        if (barcodes == null) {
                            Materialize.toast("Please enter least 1 barcode.", 4000, 'toast-alert');
                        } else {
                            Materialize.toast("There's an error. Please try again later", 4000, 'toast-alert');
                        }
                    }
                });
            });

            function dateValidation(i, isRecodes=false, isContacts=false) {
                let currentED = $('#expirationDate'+i).val();
                let currentEDSplitted = currentED.split('/');

                return currentEDSplitted.length === 3;
            }

            let deleteAllButton = $('#deleteButton');
            deleteAllButton.on('click', function() {
                $('#barcodeDiv').html('');
            });
        });
    </script>
@elseif($page == 'multiple-tickets')
    <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script>
        $(function() {

            let datepickerFrom = $('.datepicker-from').datepicker({
                dateFormat: 'yyyy-mm-dd',
                toggleSelected: false,
                onShow: function() {
                    $('.datepicker--nav').show();
                    $('.-from-bottom-').show();
                    $('.datepicker--nav-title').show();
                    $('.datepicker--nav-action').show();
                    $('.datepicker--pointer').show();
                    $('.datepicker--content').show();
                },
                onSelect: function() {
                    $('.datepicker--nav').hide();
                    $('.-from-bottom-').hide();
                    $('.datepicker--nav-title').hide();
                    $('.datepicker--nav-action').hide();
                    $('.datepicker--pointer').hide();
                    $('.datepicker--content').hide();
                }
            });

        $(document).on('submit', '#multiple-ticket-form', function(event) {
            $(this).find(".downloadTicketsButton").attr("type", "button");


            var attr = $(".downloadTicketsButton").attr('data-usable');

            if (typeof attr !== typeof undefined && attr !== false) {
             $(this).find(".downloadTicketsButton").removeAttr("data-usable");
             $(".ticket-fuse").show();
             $(".downloadTicketsButton").removeClass("btn-primary").addClass("btn-danger active").css("opacity", "0.4").append("<i class='icon-cz-skull'></i>");
              return true;
            }else{
                $(".ticket-fuse").show();
                $(".downloadTicketsButton").removeClass("btn-primary").addClass("btn-danger active").css("opacity", "0.4").append("<i class='icon-cz-skull'></i>");
                 event.preventDefault();
            }

        });



        $(document).on('click', '.ticket-fuse', function(event) {
            event.preventDefault();

             if(!confirm("Are You Sure")){
                return false;
             }


            $(".downloadTicketsButton").attr("data-usable", "true");
            $(".downloadTicketsButton").attr("type", "submit");
            $(".downloadTicketsButton").removeClass("btn-danger").addClass("btn-primary active").css("opacity", "1").find("i").remove();
            $(this).hide();


        });




            function ajaxBarcode(ticketType, barcodeCount) {
                let downloadTicketsButton = $('.downloadTicketsButton');
                let barcodeDiv = $('#barcodeDiv');
                barcodeDiv.html('');
                $.ajax({
                    type: 'POST',
                    url: '/getUsableBarcodeCount',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ticketType: ticketType,
                        barcodeCount: barcodeCount,
                    },
                    success: function(data) {
                        if (barcodeCount <= data.usableBarcodeCount) {
                            if (data.usableBarcodeCount > 0) {
                                barcodeDiv.append('<span>There are ' + data.usableBarcodeCount + ' barcodes left</span>');
                            } else {
                                barcodeDiv.append('<span>You have no barcode for this ticket type. Add new barcodes first to create multiple tickets.</span>');
                                downloadTicketsButton.attr('disabled', true);
                            }
                        } else {
                            barcodeDiv.append("<span>There's no available ticket</span>");
                            downloadTicketsButton.attr('disabled', true);
                        }
                    }
                });
            }

            $('#barcodeCount').on('keyup', function() {
                let barcodeCount = $('#barcodeCount').val();
                downloadTicketsButton.attr('disabled', false);
                let ticketType = $('#ticketTypeSelect').val();

                ajaxBarcode(ticketType, barcodeCount);
            });

            $('#ticketTypeSelect').on('change', function(data) {
                let barcodeCount = $('#barcodeCount').val();
                let downloadTicketsButton = $('.downloadTicketsButton');
                downloadTicketsButton.attr('disabled', false);
                let ticketType = $(this).val();

                ajaxBarcode(ticketType, barcodeCount);
            });

            let barcodeCount = $('#barcodeCount');
            let ticketType = $('#ticketTypeSelect').val();
            $('#barcodeCount').show();
            $('#barcodeCreateButton').show();
            $('#alertBox').show();
            let barcodeDiv = $('#barcodeDiv');
            let barcodeCreateButton = $('#barcodeCreateButton');
            let sendBarcodes = $('#sendBarcodes');
            barcodeCreateButton.on('click', function() {
                let barcodeCount = $('#barcodeCount').val();
                $.ajax({
                    type: 'POST',
                    url: '/barcode/multiple-ticket',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ticketType: ticketType,
                        barcodeCount: barcodeCount,
                    },
                    success: function(data) {
                        if (data.oldBarcodes) {
                            Materialize.toast("Please check your Barcodes. All of them have to be unique."  , 4000, 'toast-alert');
                        } else {
                            Materialize.toast("You added " + barcodes.length + " barcodes successfully" , 4000, 'toast-success');
                        }
                    },
                    error: function(data) {
                        if (barcodes == null) {
                            Materialize.toast("Please enter least 1 barcode.", 4000, 'toast-alert');
                        } else {
                            Materialize.toast("There's an error. Please try again later", 4000, 'toast-alert');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'external-payment-index')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/admin/externalPaymentTable.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {
            $("#bookings").select2({
            });

            $(document).on('click', '.resendEmail', function() {
                let id = $(this).attr('data-id');
                let $this = $(this);
                $.ajax({
                    method: 'POST',
                    url: '/resendEmail',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function(data) {
                        if (data.success) {
                            Materialize.toast(data.success, 4000, 'toast-success');
                        }
                        $this.attr('disabled', true);
                    }
                });
            });

            $('.editExternalPayment').on('click', function() {
                let $this = $(this);
                let $thisParent = $this.parent().parent();
                let bookingRefCode = $thisParent.find('#bookings');
                let message = $thisParent.find('.message');
                let messageVal = message.text();
                bookingRefCode.removeAttr('disabled');;

                message.html("");
                message.append("<textarea style='width: 100%; height: 100px;' class='messageNewVal'>"+messageVal+"</textarea>");

                $thisParent.find('.updateExternalPayment').show();
                $(this).hide();
            });

            $('.updateExternalPayment').on('click', function() {
                let $this = $(this);
                let $thisParent = $this.parent().parent();
                let externalPaymentID = $(this).attr('data-id');

                let bookingRefCode = $thisParent.find('#bookings');
                let bookingRefCodeNewVal = bookingRefCode.val();

                let message = $thisParent.find('.message');
                let messageNewVal = $thisParent.find('.messageNewVal').val();
                $.ajax({
                    type: 'POST',
                    url: '/external-payment/edit',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bookingRefCodeNewVal: bookingRefCodeNewVal,
                        messageNewVal: messageNewVal,
                        externalPaymentID: externalPaymentID,
                    },
                    success: function(data) {
                        if (data.error) {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        } else if(data.success) {
                            $thisParent.find('.updateExternalPayment').hide();
                            $thisParent.find('.editExternalPayment').show();

                            bookingRefCode.attr('disabled',true);
                            message.html('');
                            message.append(messageNewVal);
                            Materialize.toast('You updated the external payment successfully!', 4000, 'toast-success');
                        }else{
                            Materialize.toast("An Error Occurred!", 4000, 'toast-alert');
                        }
                    }
                });
            });
        });
    </script>

@elseif($page == 'paymentlogs-index')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/admin/paymentLogsTable.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

@elseif($page == 'ticket-types-index')
    <script>
        $(function() {
            $('.usableAsTicketButton').on('click', function() {
                let $this = $(this);
                let ticketTypeID = $this.attr('data-tickettype-id');
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/ticket-type/setUsableAsTicket',
                    data: {
                        _token: '<?=csrf_token()?>',
                        ticketTypeID: ticketTypeID
                    },
                    success: function(data) {
                        if (data.success) {
                            if (data.value === 1) {
                                $this.html('Usable');
                                $this.removeClass('notUsableAsTicket');
                                $this.addClass('usableAsTicket');
                                Materialize.toast(data.success, 4000, 'toast-success');
                            } else {
                                $this.html('Not Usable');
                                $this.removeClass('usableAsTicket');
                                $this.addClass('notUsableAsTicket');
                                Materialize.toast(data.success, 4000, 'toast-success');
                            }
                        } else {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'restaurants-index')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script>
        $('#supplierSelect').on('select2:opening select2:closing', function( event ) {
            let $searchfield = $(this).parent().find('.select2-search__field');
            $searchfield.prop('disabled', true);
        }).select2({
            placeholder: 'Select an option',

        });

        $('#supplierSelect').on("select2:unselect", function(e) {
            let optionID = e.params.data.id;
            $.ajax({
                type: 'POST',
                url: '/supplier/removeOption',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    optionID: optionID,
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, 'toast-success');
                    }
                }
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
            // Do not display the item if there is no 'text'     property
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

        $('body').on('click', '.modalOpen', function() {
            $('#supplierSelect').html('');
            let supplierID = $(this).attr('data-supplier-id');
            $.ajax({
                type: 'POST',
                url: '/supplier/supplierid',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    supplierID: supplierID
                },
                success: function(data) {
                    let options = data.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].rCodeID === null || options[i].rCodeID === parseInt(supplierID)) {
                            let block = '';
                            block += '<option ';
                            if (options[i].rCodeID === parseInt(supplierID)) {
                                block += 'selected ';
                            }
                            block += 'value="'+options[i].id+'">'+options[i].title+'</option>';
                            $('#supplierSelect').append(block);
                        }
                    }
                }
            });
        });

        $('body').on('click', '#sendRestaurants', function() {
            let supplierID = $('.modalOpen').attr('data-supplier-id');
            let options = $('#supplierSelect').val();
            $.ajax({
                type: 'POST',
                url: '/supplier/sendRCode',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    supplierID: supplierID,
                    options: options,
                },
                success: function() {
                    Materialize.toast("It's successful", 4000, 'toast-success');
                }
            });
        });
    </script>
@elseif($page == 'supplier-create' || $page == 'supplier-edit')
    <script>

        $(function() {
            $('#isRestaurant').on('change', function() {
                $(this).val($(this).prop('checked') ? 1: 0);
            });

            $('#isAsMobileUser').on('change', function() {
                $(this).val($(this).prop('checked') ? 1: 0);
            });
        });

        $('#location').on('change', function() {
            let countryID = $(this).val();
            $('#cities').html('');
            $.ajax({
                type: 'POST',
                url: '/product/create/getCities',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    countryID: countryID
                },
                success: function(data) {
                    let cities = data.cities;
                    for (let i = 0; i < cities.length; i++) {
                        $('#cities').append('<option value="'+cities[i]+'">'+cities[i]+'</option>');
                    }
                }
            });
        });

        $('#paymentDetailSaveButton').on('click', function(e) {
            e.preventDefault();
            let supplierID = $('#supplierID').val();
            $.ajax({
                type: 'POST',
                url: '/supplier/sendVerificationEmail',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    supplierID: supplierID
                },
                success: function(data) {
                    //
                }
            });
            $('#verificationAlertDiv').show();
        });

        $('#submitVerificationCodeButton').on('click', function(e) {
            e.preventDefault();
            let supplierID = $('#supplierID').val();
            let verificationCode = $('#verificationCode').val();
            $.ajax({
                type: 'POST',
                url: '/supplier/submitVerificationEmail',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    supplierID: supplierID,
                    verificationCode: verificationCode
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, 'toast-success');
                        $('#paymentDetailsForm')[0].submit();
                    }
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    }
                }
            });
        });
    </script>
    <script>
        $('#newFile').on('click', function() {
            let block =
                '<div class="row s6">' +
                '<div class="input-field col s6">' +
                '<input type="text" class="validate title" name="title[]" value="">' +
                '<label for="bankName">{{__('title')}}</label>' +
                '</div>' +
                '<div class="input-field col s6">' +
                '<button type="button" class="deleteFiles waves-effect waves-light btn-danger" style="font-size:26px;padding:2px 20px;border:none;float: right">-</button>' +
                '<input type="file" class="fileName validate" name="fileName[]" >' +
                '</div>' +
                '</div>' ;
            $('#filesTable').append(block);
            $('#uploadFiles').show();
        });

        $('body').on('click', '.deleteFiles', function() {
            let id = $(this).attr('data-id');
            let $this = $(this);
            if (typeof(id) === undefined) {
                $this.parent().parent().html('');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/deleteLicense',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                    },
                    success: function(data) {
                        $this.parent().parent().html('');
                        Materialize.toast('You deleted your file!', 4000, 'toast-success');
                    },
                    error: function() {
                        Materialize.toast("There's an error! Please contact with us!", 4000, 'toast-alert');
                    }
                });
            }
        });

        $('.deleteFile').on('click', function() {
            let id = $(this).attr('data-id');
            let $this = $(this);
            let tr = $('#' + id);
            $.ajax({
                type: 'POST',
                url: '/supplier/deleteLicense',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    Materialize.toast('You deleted your license successfully !', 4000, 'toast-success');
                    tr.html('');
                }
            });
        });

        $('.editSuggestFile').on('click', function() {
            let $this = $(this);
            let id = $this.attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/supplier/editSuggestFile',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    Materialize.toast('You sent an email to company for edit suggestion.', 4000, 'toast-success');
                }
            });
        });

        $('.confirmCheck').on('click', function() {
            let value = $(this).prop('checked') == true ? 1 : 0;
            let id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/supplier/changeLicenseStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    value: value,
                    id: id,
                }
            });
        });
    </script>
@elseif($page == 'users-create' || $page == 'users-edit')
    <script>
        $(function() {

            function generate_unique(){
                return "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx".replace(/x/g, function(){
                    var r = Math.random()*16 | 0;
                    return r.toString(16);
                });
            }


        $(document).on('click', '#generate-unique', function(event) {
            event.preventDefault();
            $("#affiliate_unique").val(generate_unique());

        });


            $('#isCommissioner').on('change', function() {
                $(this).val($(this).prop('checked') ? 1: 0);
                if ($(this).val() === "1") {
                    $('#commissionDiv').show();
                } else {
                    $('#commissionDiv').hide();
                }
            });
        });
    </script>
@elseif($page == 'commissioners-index')
    <script>
        $('.commissionerCommission').on('dblclick', function() {
            $(this).removeAttr('readonly');
            let button = $(this).parent().parent().find('.saveCommissionerCommission');
            button.show();
        });

        $('.saveCommissionerCommission').on('click', function() {
            let input = $(this).parent().parent().find('.commissionerCommission');
            let commissionerId = $(this).attr('data-commissioner-id');
            let $this = $(this);
            if (input.val() !== '') {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/commission/saveCommission',
                    data: {
                        _token: '<?=csrf_token()?>',
                        commission: input.val(),
                        commissionerId: commissionerId
                    },
                    success: function(data) {
                        if (typeof data.success !== 'undefined') {
                            Materialize.toast(data.success, 4000, 'toast-success');
                            $this.hide();
                            input.attr('readonly', true);
                        }

                        if (typeof data.error !== 'undefined') {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            } else {
                Materialize.toast('Commission can not be blank!', 4000, 'toast-alert');
            }
        });

        $('.toggle-class').change(function() {
            let isActive = $(this).prop('checked') === false ? 0 : 1;
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/commissioner/setStatus',
                data: {
                    'isActive': isActive,
                    'id': id
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, 'toast-success');
                    } else if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    }
                },
            });
        });

        $('.commissionerCommissionType').on('dblclick', function() {
            let commissionType = $(this).val();
            let thisDiv = $(this).parent();
            $(this).hide();
            if(thisDiv.find('select').length == 0) {
                let block = `<select>
                                <option value="percentage">Percentage</option>
                                <option value="money">Money</option>
                            </select>`;
                thisDiv.append(block);
            }
            if(commissionType == 'percentage') thisDiv.find('select').val('percentage');
            else if(commissionType == 'money') thisDiv.find('select').val('money');
            thisDiv.find('select').show();

            let button = thisDiv.parent().find('.saveCommissionerCommissionType');
            button.show();
        });

        $('.saveCommissionerCommissionType').on('click', function() {
            let select = $(this).parent().parent().find('select');
            let commissionerId = $(this).attr('data-commissioner-id');
            let $this = $(this);

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '/commission/saveCommissionType',
                data: {
                    _token: '<?=csrf_token()?>',
                    commissionType: select.val(),
                    commissionerId: commissionerId
                },
                success: function(data) {
                    if (typeof data.success !== 'undefined') {
                        Materialize.toast(data.success, 4000, 'toast-success');
                        $this.hide();
                        let selectDiv = select.parent();
                        let selectVal = select.val();
                        select.hide();
                        selectDiv.find('input').val(selectVal);
                        selectDiv.find('input').show();
                    }

                    if (typeof data.error !== 'undefined') {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    }
                }
            });
        });
    </script>
@elseif($page == 'commissioners-details')
    <script>
        $('.deleteFile').on('click', function() {
            let $this = $(this);
            let id = $this.attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/deleteLicenses',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    $this.parent().parent().html('');
                    Materialize.toast('You deleted the file successfully !', 4000, 'toast-success');
                }
            });
        });

        $('.editSuggestFile').on('click', function() {
            let $this = $(this);
            let id = $this.attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/editSuggestFile',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function() {
                    Materialize.toast('You sent an email to company for edit suggestion.', 4000, 'toast-success');
                }
            });
        });

        $('.confirmCheck').on('click', function() {
            let value = $(this).prop('checked') == true ? 1: 0;
            let id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/changeLicenseStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    value: value,
                    id: id,
                }
            });
        });
    </script>
@elseif($page == 'bookings-index-for-restaurant')
    <script>
        $('.rCodeInput').on('dblclick', function() {
            $(this).removeAttr('readonly');
            let button = $(this).parent().parent().find('.saveRCodeInput');
            button.show();
        });

        $('.saveRCodeInput').on('click', function() {
            let input = $(this).parent().parent().find('.rCodeInput');
            let bookingId = $(this).attr('data-booking-id');
            let bookingStatus = $(this).attr('data-booking-status');
            let $this = $(this);
            let currentURL = (document.URL);
            let hash = currentURL.split('bookings-restaurant/')[1];
            if (input.val() !== '') {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/saveRCodeAsRestaurant',
                    data: {
                        _token: '<?=csrf_token()?>',
                        'rCode': input.val(),
                        'bookingId': bookingId,
                        'hash': hash
                    },
                    success: function(data) {
                        if (typeof data.success !== 'undefined') {
                            Materialize.toast(data.success, 4000, 'toast-success');
                            $this.hide();
                            input.attr('readonly', true);
                        }

                        if (typeof data.error !== 'undefined') {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            } else {
                Materialize.toast('R-Code can not be blank!', 4000, 'toast-alert');
            }
        });
    </script>
@elseif($page == 'barcodes-index')
    <script>

        $(function() {
            $('body').on('change', '.toggle-class-isUsed', function() {
                let isUsed = $(this).prop('checked') == true ? 1: 0;
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/barcodes/changeIsUsedStatus',
                    data: {
                        'isUsed': isUsed,
                        'id': id
                    },
                    success: function(data) {
                        //
                    }
                });
            });
        });
    </script>
@elseif($page == 'attraction-index')
    <script>

        // Status Button Actions
        $('body').on('click', '#setStatusButton', function() {
            let $this = $(this);
            let attractionID = $this.data('attraction-id');
            $.ajax({
                type: 'POST',
                url: '/attraction/setStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    attractionID: attractionID
                },
                success: function(data) {
                    if (data.success) {
                        $this.html(data.buttonText);
                        $this.attr('data-is-active', data.isActive);
                    }
                }
            });
        });
    </script>

    @elseif($page == 'attraction-indexpct')
    <script>

        // Status Button Actions
        $('body').on('click', '#setStatusButton', function() {
            let $this = $(this);
            let attractionID = $this.data('attraction-id');
            $.ajax({
                type: 'POST',
                url: '/attraction/setStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    attractionID: attractionID
                },
                success: function(data) {
                    if (data.success) {
                        $this.html(data.buttonText);
                        $this.attr('data-is-active', data.isActive);
                    }
                }
            });
        });
    </script>

    @elseif($page == 'attraction-indexpctcom')
    <script>

        // Status Button Actions
        $('body').on('click', '#setStatusButton', function() {
            let $this = $(this);
            let attractionID = $this.data('attraction-id');
            $.ajax({
                type: 'POST',
                url: '/attraction/setStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    attractionID: attractionID
                },
                success: function(data) {
                    if (data.success) {
                        $this.html(data.buttonText);
                        $this.attr('data-is-active', data.isActive);
                    }
                }
            });
        });
    </script>

    @elseif($page == 'attraction-indexctp')
    <script>

        // Status Button Actions
        $('body').on('click', '#setStatusButton', function() {
            let $this = $(this);
            let attractionID = $this.data('attraction-id');
            $.ajax({
                type: 'POST',
                url: '/attraction/setStatus',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    attractionID: attractionID
                },
                success: function(data) {
                    if (data.success) {
                        $this.html(data.buttonText);
                        $this.attr('data-is-active', data.isActive);
                    }
                }
            });
        });
    </script>
@elseif($page == 'attraction-create')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{asset('/keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('/keditor/src/lang/en.js')}}"></script>
    <script>
        KEDITOR.create('description', {
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic'],
                ['removeFormat'],
                ['fontColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'video'],
                ['fullScreen', 'codeView'],
                ['preview', 'print'],
            ],
            minHeight: '250px',
        });

        $('form').on('submit', function() {
            $('#description').val($('.keditor-editable').html());
        });
    </script>
    <script>
        let tagAttraction = document.querySelector('#tagAttraction');
        let tagifyTagAttraction =  new Tagify(tagAttraction, {
            keepInvalidTags: true, // do not remove invalid tags (but keep them marked as invalid)
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });

        $(function() {
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });

            $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        for (let i = 0; i < cities.length; i++) {
                            $('#cities').append('<option value="'+cities[i]+'">'+cities[i]+'</option>');
                        }
                    }
                });
            });

            $('#bindCityButton').on('click', function(e) {
                e.preventDefault();
                let city = $('#cities').val();
                let attractionID = $('#attractionID').val();
                if (city === null) {
                    Materialize.toast('You have to choose a city before binding!', 4000, 'toast-alert');
                } else {
                    let block = '';
                    block += '<span class="col-md-1" id="bindedCity" style="cursor: pointer; text-align: center; background-color: #075175; margin: 20px; padding: 10px; color: #ffffff;">\n';
                    block += city;
                    block += '<span data-city="'+data.city+'" style="cursor: pointer;" class="pull-right" id="deleteBindedCity">X</span>\n';
                    block += '</span>';
                    block += '<input type="hidden" name="bindedCities[]" value="'+city+'">\n';
                    $('#countries').val('');
                    $('#cities').html('');
                    $('#citiesSpan').append(block);
                }
            });

            $('body').on('click', '#deleteBindedCity', function(e) {
                e.preventDefault();
                let city = $(this).attr('data-city');
                let attractionID = $('#attractionID').val();
                $(this).parent().remove();
            });
        });

        $('#tagAttraction').on('change', function() {
            $('tag div').replaceWith('<div>'+$(this.val())+'</div>');
        });

    </script>
@elseif($page == 'attraction-edit')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{asset('/keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('/keditor/src/lang/en.js')}}"></script>
    <script>
        KEDITOR.create('description', {
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic'],
                ['removeFormat'],
                ['fontColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'video'],
                ['fullScreen', 'codeView'],
                ['preview', 'print'],
            ],
            minHeight: '250px',
        });

        $('form').on('submit', function() {
            $('#description').val($('.keditor-editable').html());
        });
    </script>
    <script>
        let tagAttraction = document.querySelector('#tagAttraction');
        let tagifyTagAttraction =  new Tagify(tagAttraction, {
            keepInvalidTags: true, // do not remove invalid tags (but keep them marked as invalid)
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });

        $(function() {
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });

            $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        for (let i = 0; i < cities.length; i++) {
                            $('#cities').append('<option value="'+cities[i]+'">'+cities[i]+'</option>');
                        }
                    }
                });
            });

            $('#bindCityButton').on('click', function(e) {
                e.preventDefault();
                let city = $('#cities').val();
                let attractionID = $('#attractionID').val();
                if (city === null) {
                    Materialize.toast('You have to choose a city before binding!', 4000, 'toast-alert');
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/attraction/bindCity',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            attractionID: attractionID,
                            city: city
                        },
                        success: function(data) {
                            if (data.success) {
                                let block = '';
                                block += '<span class="col-md-1" id="bindedCity" style="cursor: pointer; text-align: center; background-color: #075175; margin: 20px; padding: 10px; color: #ffffff;">\n';
                                block += data.city;
                                block += '<span data-city="'+data.city+'" style="cursor: pointer;" class="pull-right" id="deleteBindedCity">X</span>\n';
                                block += '</span>';
                                $('#countries').val('');
                                $('#cities').html('');
                                $('#citiesSpan').append(block);
                            } else if (data.error) {
                                Materialize.toast('This city is binded before! Please try another city.', 4000, 'toast-alert');
                            } else {
                                Materialize.toast('An error is occured! Please consult the Software Development Team', 4000, 'toast-alert');
                            }
                        }
                    });
                }
            });

            $('body').on('click', '#deleteBindedCity', function(e) {
                e.preventDefault();
                let $this = $(this);
                let city = $this.attr('data-city');
                let attractionID = $('#attractionID').val();
                $.ajax({
                    type: 'POST',
                    url: '/attraction/deleteCity',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        attractionID: attractionID,
                        city: city
                    },
                    success: function(data) {
                        if (data.success) {
                            $this.parent().remove();
                        } else {
                            Materialize.toast('An error is occured! Please consult the Software Development Team', 4000, 'toast-alert');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'gallery-index')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script>

        $(function() {
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#cities").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#attractions").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#suppliers").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });

            $('.bigImageDiv').on('click', function() {
                let href = $(this).attr('data-href');
                window.location.href = href;
            });

            $('.photoClose').on('click', function() {
                let id = $(this).attr('data-id');
                window.location.href = '/gallery/' + id + '/delete';
            });

            $('.photoEdit').on('click', function() {
                let id = $(this).attr('data-id');
                let alt = $(this).attr('data-alt');
                let name = $(this).attr('data-name');
                let titleSuffix = $(this).attr('data-title-suffix');
                let city = $(this).attr('data-city');
                let country = $(this).attr('data-country');
                let attractions = JSON.parse($(this).attr('data-attractions') ? $(this).attr('data-attractions') : '[""]');
                let uploadedBy = $(this).attr('data-uploaded-by') ? $(this).attr('data-uploaded-by') : '-';
                alt = alt.replace(titleSuffix, '');
                name = name.replace(titleSuffix, '');
                $('#imageAlt').val(alt);
                $('#imageName').val(name);
                $('#updateImageButton').attr('data-id', id);
                $('#countryCityName').text((country ? country : '*') + '/' + city);

                $('#attraction').val(attractions).trigger('change');
                $('#attraction').select2();
                $('#uploadedBy').text(uploadedBy);
            });

            $('#updateImageButton').on('click', function() {
                let imageAltId = $('#imageAlt');
                let imageNameId = $('#imageName');
                let alt = imageAltId.val();
                let name = imageNameId.val();
                let id = $(this).attr('data-id');
                $.ajax({
                    method: 'POST',
                    url: '/gallery/updateImage',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        'id': id,
                        'name': name,
                        'alt': alt,
                        'attractions': $('#attraction').val()
                    },
                    success: function(data) {
                        if (data.success) {
                            Materialize.toast(data.success, 4000, 'toast-success');
                            $('.closeModal').click();
                            let alt = data.alt;
                            alt = alt.replace(data.titleSuffix, '');
                            let name = data.name;
                            name = name.replace(data.titleSuffix, '');
                            $('.photoEdit[data-id='+data.id+']').attr('data-alt', alt);
                            $('.photoEdit[data-id='+data.id+']').attr('data-name', name);
                            $('.photoEdit[data-id='+data.id+']').attr('data-attractions', data.attractions);
                        }
                        if (data.error) {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    }
                });
            });

            let selectCity = 0;
            let selectAttraction = 0;

            $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        let block = '<option value="" selected>Choose a City</option>';
                        for (let i = 0; i < cities.length; i++) {
                            block += '<option value="'+cities[i]+'">'+cities[i]+'</option>'
                        }
                        $('#cities').append(block);
                        @if($city)
                            if(selectCity == 0) {
                                $('#cities').val('{{$city}}').trigger('change');
                                selectCity = 1;
                            }
                        @endif
                    }
                });
            });

            $('#cities').on('change', function() {
                let city = $('#cities').val();
                $.ajax({
                    type: 'POST',
                    url: '/getAttractionsByCity',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        city: city
                    },
                    success: function(data) {
                        if (data.success) {
                            $('.mdb-select').material_select('destroy');
                            let block = '';
                            $('#attractions').html('');
                            $('#attractions').append('<option value="" selected>Choose an Attraction</option>');

                            let attractions = data.attractions;
                            attractions.forEach(function(item, index) {
                                block += '<option value="'+item.id+'">'+item.name+'</option>';
                            });
                            $('#attractions').append(block);
                            $('.mdb-select').material_select();
                            @if($attraction)
                                if(selectAttraction == 0) {
                                    $('#attractions').val({{$attraction}}).trigger('change');
                                    selectAttraction = 1;
                                }
                            @endif
                        }
                    }
                });
            });

            $('#showHideFilters').on('click', function() {
                let isShown = $(this).attr('data-shown');
                if (isShown === '1') {
                    $(this).attr('data-shown', '0');
                    $(this).html('Show');
                    $('.filters').hide();
                } else {
                    $(this).attr('data-shown', '1');
                    $(this).html('Hide');
                    $('.filters').show();
                }
            });

            $('#applyFiltersButton').on('click', function() {
                let country = $('#countries').val();
                let city = $('#cities').val();
                let attraction = $('#attractions').val();
                let galleryID = $('#galleryID').val();
                let supplierID = $('#suppliers').val();
                let galleryName = $('#galleryName').val();
                if (galleryID !== '') {
                    window.location.href = '?galleryID=' + galleryID;
                } else if (galleryName !== '') {
                    window.location.href = '?galleryName=' + galleryName;
                } else if (supplierID !== '') {
                    window.location.href = '?supplierID=' + supplierID;
                } else if (attraction !== '') {
                    window.location.href = '?country='+country+'&city='+city+'&attraction='+attraction;
                } else {
                    if (country !== '') {
                        if (city === '') {
                            Materialize.toast('You have to choose a city to apply filters!', 4000, 'toast-alert');
                        } else {
                            window.location.href = '?country=' + country + '&city=' + city;
                        }
                    } else {
                        Materialize.toast('You have not chosen any filter!', 4000, 'toast-alert');
                        return false;
                    }
                }
            });

            $('#clearFiltersButton').on('click', function() {
                let locationHref = $(location).attr('href');
                if (locationHref.includes('country=') || locationHref.includes('city=') || locationHref.includes('attraction=') || locationHref.includes('galleryID=') || locationHref.includes('galleryName=') || locationHref.includes('supplierID=')) {
                    window.location.href = '/gallery';
                } else {
                    Materialize.toast('You have no filter to clear!', 4000, 'toast-alert');
                }
            });

            @if($country)
                $('#countries').val({{$country}}).trigger('change');
            @endif

            @if($supplierID)
                $('#suppliers').val({{$supplierID}}).trigger('change');
            @endif

            @if($galleryID)
                $('#galleryID').val({{$galleryID}});
            @endif

            @if($galleryName)
                $('#galleryName').val(@json($galleryName));
            @endif
        });
    </script>
@elseif($page == 'language-edit')
    <script>

        $(function() {
            $('#languageUpdateSubmitButton').on('click', function(e) {
                e.preventDefault();
                let keyValueInputs = $('.keyValueInputs');
                let validationArr = [];
                keyValueInputs.each(function(key, element) {
                    if (element.value === '') {
                        validationArr.push('true');
                        return;
                    } else {
                        validationArr.push('false');
                    }
                });
                if (!validationArr.includes('true')) {
                    $('.languageUpdateForm')[0].submit();
                } else {
                    Materialize.toast('Translation can not be blank!', 4000, 'toast-alert');
                }
            });

            $('#searchInput').on('keyup', function() {
                let languageID = $('#languageID').val();
                let value = $(this).val();
                if (value !== '') {
                    $('.pageButton, .dotButton').hide();
                } else {
                    $('.pageButton, .dotButton').show();
                }
                $.ajax({
                    method: 'POST',
                    url: '/language/search',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        languageID: languageID,
                        value: value
                    },
                    success: function(data) {
                        if (data.success) {
                            $('#keyValuesRow').html('');
                            $('#nextButton').attr('data-step', 0);
                            $('#prevButton').attr('disabled', 'disabled');
                            let block = '';
                            let langArr = data.langArr;
                            for (let key in langArr) {
                                let newStr = langArr[key].replace(/"/g, '\'');
                                block += '<div class="col-md-12">\n' +
                                    '<div class="col-md-3">\n' +
                                    '<label for="list-title">'+key+'</label>\n' +
                                    '</div>\n' +
                                    '<div class="col-md-9">\n' +
                                    '<input type="text" name="'+key+'" value="'+newStr+'" class="validate keyValueInputs">\n' +
                                    '</div>\n' +
                                    '</div>';
                            }
                            $('#keyValuesRow').append(block);
                            if (data.isEnd) {
                                $('#nextButton').attr('disabled', true);
                            } else {
                                $('#nextButton').removeAttr('disabled');
                            }
                        }
                    }
                });
            });

            function paginationActions(step, newStep, dataDirection, type) {
                if (dataDirection === 'prev') {
                    newStep = step - 1;
                }
                let value = $('#searchInput').val();
                let languageID = $('#languageID').val();
                let pageCount = parseInt($('#pageCount').val());
                let keyValues = {};
                let keyValueInputs = $('.keyValueInputs');
                keyValueInputs.each(function(key, element) {
                    keyValues[element.name] = element.value;
                });

                $.ajax({
                    method: 'POST',
                    url: '/language/nextPrevPage',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        step: newStep,
                        languageID: languageID,
                        value: value,
                        keyValues: keyValues
                    },
                    success: function(data) {
                        if (data.success) {
                            $('#keyValuesRow').html('');
                            $('#nextButton').attr('data-step', newStep);
                            let block = '';
                            let langArr = data.langArr;
                            for (let key in langArr) {
                                let newStr = langArr[key].replace(/"/g, '\'');
                                block += '<div class="col-md-12">\n' +
                                    '<div class="col-md-3">\n' +
                                    '<label for="list-title">'+key+'</label>\n' +
                                    '</div>\n' +
                                    '<div class="col-md-9">\n' +
                                    '<input type="text" name="'+key+'" value="'+newStr+'" class="validate keyValueInputs">\n' +
                                    '</div>\n' +
                                    '</div>';
                            }
                            $('#keyValuesRow').append(block);

                            if (newStep !== 0) {
                                $('#prevButton').removeAttr('disabled');
                            } else {
                                $('#prevButton').attr('disabled', true);
                            }

                            if (data.isEnd) {
                                $('#nextButton').attr('disabled', true);
                            } else {
                                $('#nextButton').removeAttr('disabled');
                            }

                            let oldButton = $('.pageButton[data-step="'+step+'"]');
                            oldButton.removeClass('activeButton');
                            oldButton.addClass('disabledButton');

                            if (newStep <= 3) {
                                $('.willBeAddedDot').hide();
                                $('.twoAndThree').show();
                                $('.nextAfterFive').hide();
                                if (newStep === 0 && type === 'pageclick') {
                                    $('.beforeFive').html(newStep + 4);
                                    $('.fiveButton').html(newStep + 5);
                                    $('.beforeFive').attr('data-step', newStep + 3);
                                    $('.fiveButton').attr('data-step', newStep + 4);
                                }
                            }
                            if (newStep === 4) {
                                if (dataDirection === 'prev') {
                                    $('.willBeAddedDot').hide();
                                    $('.twoAndThree').show();
                                    $('.nextAfterFive').hide();
                                    $('.beforeFive').html(newStep);
                                    $('.fiveButton').html(newStep + 1);
                                    $('.beforeFive').attr('data-step', newStep - 1);
                                    $('.fiveButton').attr('data-step', newStep);
                                }
                            }
                            if (newStep === 5 || newStep === (pageCount-1)) {
                                $('.willBeAddedDot').show();
                                $('.twoAndThree').hide();
                                $('.nextAfterFive').show();
                            }

                            if (newStep > 4) {
                                if (pageCount > (step + 3)) {
                                    $('.beforeFive').attr('data-step', step);
                                    if (dataDirection === 'prev') {
                                        $('.beforeFive').html(newStep);
                                        $('.fiveButton').html(newStep + 1);
                                        $('.nextAfterFive').html(newStep + 2);
                                        $('.beforeFive').attr('data-step', newStep - 1);
                                        $('.fiveButton').attr('data-step', newStep);
                                        $('.nextAfterFive').attr('data-step', newStep + 1);
                                    } else {
                                        $('.beforeFive').html(newStep);
                                        $('.fiveButton').html(newStep + 1);
                                        $('.nextAfterFive').html(newStep + 2);
                                        $('.beforeFive').attr('data-step', newStep - 1);
                                        $('.fiveButton').attr('data-step', newStep);
                                        $('.nextAfterFive').attr('data-step', newStep+1);
                                    }
                                }
                                if (type === 'pageclick') {
                                    if (pageCount === (newStep + 1)) {
                                        $('.beforeFive').html(newStep - 2);
                                        $('.fiveButton').html(newStep - 1);
                                        $('.nextAfterFive').html(newStep);
                                        $('.beforeFive').attr('data-step', newStep - 3);
                                        $('.fiveButton').attr('data-step', newStep - 2);
                                        $('.nextAfterFive').attr('data-step', newStep - 1);
                                    }
                                }
                            }
                            if (dataDirection === 'prev') {
                                if (pageCount <= (step + 3)) {
                                    $('.willBeRemovedDot').hide();
                                } else {
                                    $('.willBeRemovedDot').show();
                                }
                            }
                            if (dataDirection === 'next') {
                                if (pageCount <= (step + 4)) {
                                    $('.willBeRemovedDot').hide();
                                } else {
                                    $('.willBeRemovedDot').show();
                                }
                            }
                            let newButton = $('.pageButton[data-step="'+newStep+'"]');
                            newButton.addClass('activeButton');
                            newButton.removeClass('disabledButton');
                            Materialize.toast('Translations are saved!', 4000, 'toast-success');
                        }
                    }
                });
            }

            $('#nextButton, #prevButton').on('click', function(e) {
                e.preventDefault();
                let step = parseInt($('#nextButton').attr('data-step'));
                let dataDirection = $(this).attr('data-direction');
                let newStep = step + 1;
                paginationActions(step, newStep, dataDirection, 'nextprev');
            });

            $('.pageButton').on('click', function(e) {
                e.preventDefault();
                let activeButton = $('.pageButton.activeButton');
                activeButton.removeClass('activeButton');
                activeButton.addClass('disabledButton');
                let step = parseInt($(this).attr('data-step')) - 1;
                let newStep = step + 1;
                paginationActions(step, newStep, 'next', 'pageclick');
            });
        });
    </script>
@elseif($page == 'gallery-create')
    <script src="{{asset('js/dropzone.js')}}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>

   <script>
       $('body').on('click', '.dz-remove', function() {
           var thisIs=$(this);
            var fileID=$(this).attr('data-id');
            var whichPage = "soft";



           if (fileID!=undefined){
               $.ajax({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   type: 'POST',
                   url: '/product/deletePhoto',
                   data: {
                       whichPage: whichPage,
                       fileID:fileID
                   },
                   success: function (data) {
                       if(data.success){
                           thisIs.parent().remove();
                       }

                   }
               });
           }else{
               thisIs.parent().remove();
           }

       });

       $(document).ready(function() {
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

            $("#countries").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#cities").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
            $("#attractions").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });



                 $('#countries').on('change', function() {
                let countryID = $(this).val();
                $('#cities').html('');
                $.ajax({
                    type: 'POST',
                    url: '/product/create/getCities',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        countryID: countryID
                    },
                    success: function(data) {
                        let cities = data.cities;
                        let block = '<option value="" disabled selected>Choose a City</option>';
                        for (let i = 0; i < cities.length; i++) {
                            block += '<option value="'+cities[i]+'">'+cities[i]+'</option>'
                        }
                        $('#cities').append(block);
                    }
                });
            });

            $('#cities').on('change', function() {
                let city = $('#cities').val();
                $.ajax({
                    type: 'POST',
                    url: '/getAttractionsByCity',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        city: city
                    },
                    success: function(data) {
                        if (data.success) {
                            $('.mdb-select').material_select('destroy');
                            let block = '';
                            $('#attractions').html('');
                            $('#attractions').append('<option value="" disabled selected>Choose an Attraction</option>');

                            let attractions = data.attractions;
                            attractions.forEach(function(item, index) {
                                block += '<option value="'+item.id+'">'+item.name+'</option>';
                            });
                            $('#attractions').append(block);
                            $('.mdb-select').material_select();
                        }
                    }
                });
            });




       });
   </script>




@elseif($page == 'coupon-index')
    <script>
        $('.editCoupon').on('click', function() {
            let $this = $(this);
            let $thisParent = $this.parent().parent().parent();
            let couponCode = $thisParent.find('.couponCode');
            let couponID = $thisParent.find('.couponID').val();
            let couponCodeVal = $thisParent.find('.couponCode').text();
            let discount = $thisParent.find('.discount');
            let discountVal = $thisParent.find('.discount').text();
            let maxUsability = $thisParent.find('.maxUsability');
            let maxUsabilityVal = $thisParent.find('.maxUsability').text();
            let startingDate = $thisParent.find('.startingDate');
            let startingDateVal = $thisParent.find('.startingDate').text();
            let endingDate = $thisParent.find('.endingDate');
            let endingDateVal = $thisParent.find('.endingDate').text();
            couponCode.html('');
            couponCode.append("<input style='width: 75%' class='couponCodeNewVal' type='text' value='"+couponCodeVal+"'>");
            discount.html('');
            discount.append("<input style='width: 25%' type='text' class='discountNewVal' value='"+discountVal+"'>");
            maxUsability.html('');
            maxUsability.append("<input style='width: 25%' type='text' class='maxUsabilityNewVal' value='"+maxUsabilityVal+"'>");
            startingDate.html('');
            startingDate.append("<input type='date' class='startingDateNewVal' value='"+startingDateVal+"'>");
            endingDate.html('');
            endingDate.append("<input type='date' class='endingDateNewVal' value='"+endingDateVal+"'>");
            let updateCoupon = $thisParent.find('.updateCoupon').show();
            $(this).hide();
        });

        $('.updateCoupon').on('click', function() {
            let $this = $(this);
            let $thisParent = $this.parent().parent();
            let couponID = $(this).attr('data-id');
            let couponCode = $thisParent.find('.couponCode');
            let couponCodeNewVal = $('.couponCodeNewVal').val();
            let discount = $thisParent.find('.discount');
            let discountNewVal = $('.discountNewVal').val();
            let maxUsability = $thisParent.find('.maxUsability');
            let maxUsabilityNewVal = $('.maxUsabilityNewVal').val();
            let startingDate = $thisParent.find('.startingDate');
            let startingDateNewVal = $('.startingDateNewVal').val();
            let endingDate = $thisParent.find('.endingDate');
            let endingDateNewVal = $('.endingDateNewVal').val();
            $.ajax({
                type: 'POST',
                url: '/coupon/edit',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    couponID: couponID,
                    couponCodeNewVal: couponCodeNewVal,
                    discountNewVal: discountNewVal,
                    maxUsabilityNewVal: maxUsabilityNewVal,
                    startingDateNewVal: startingDateNewVal,
                    endingDateNewVal: endingDateNewVal
                },
                success: function(data) {
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    } else if(data.success) {
                        $('.updateCoupon').hide();
                        $('.editCoupon').show();
                        couponCode.html('');
                        couponCode.append(couponCodeNewVal);
                        discount.html('');
                        discount.append(discountNewVal);
                        maxUsability.html('');
                        maxUsability.append(maxUsabilityNewVal);
                        startingDate.html('');
                        startingDate.append(startingDateNewVal);
                        endingDate.html('');
                        endingDate.append(endingDateNewVal);
                        Materialize.toast('You updated '+couponCodeNewVal+' successfully!', 4000, 'toast-success');
                    }else{
                        Materialize.toast("An Error Occurred!", 4000, 'toast-alert');
                    }
                }
            });
        });
    </script>
@elseif($page == 'coupon-create')
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script>
        $(function() {
            $("#lastSelect,#productSelect,#couponTypeSelect").select2({
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

        $('#couponTypeSelect').on('change', function() {
            let couponTypeSelect = $(this).val();
            let lastSelect = $('#lastSelect');
            let productSelect = $('#productSelect');
            lastSelect.parent().css('display', 'none');
            productSelect.parent().css('display', 'none');
            lastSelect.on('change', function() {
                $('#discountType').show();
                $('#maxUsabilityDiv').show();
                $('#startingAndEndingDateDiv').show();
            });

            if (couponTypeSelect === '1') {
                productSelect.parent().show();
                productSelect.html("<option value=''>Choose a Product</option>@foreach($product as $p)<option data-foo='{{$p->referenceCode}}' value='{{$p->id}}'>{{$p->title}}</option>@endforeach");
                productSelect.on('change', function() {
                    lastSelect.append('');
                    let productID = productSelect.val();
                    $.ajax({
                        method: 'POST',
                        url: '/coupon/optionSelect',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            productID: productID
                        },
                        success: function(data) {
                            lastSelect.parent().show();
                            lastSelect.html('<option value="" disabled selected>Choose an Option</option>');
                            let options = data.options;
                            for (let i = 0; i <options.length; i++) {
                                lastSelect.append('<option data-foo="'+options[i].referenceCode+'" value="'+options[i].id+'">'+options[i].title+'</option>');
                            }
                        }
                    });
                });
            } else if (couponTypeSelect === '2') {
                lastSelect.parent().show();
                lastSelect.html("<option value=''>Choose a Location</option>@foreach($countries as $c)<option value='{{$c->id}}'>{{$c->countries_name}}</option>@endforeach");
            } else if (couponTypeSelect === '3') {
                lastSelect.parent().show();
                lastSelect.html("<option value=''>Choose an Attraction</option>@foreach($attraction as $a)<option value='{{$a->id}}'>{{$a->name}}</option>@endforeach");
            } else if (couponTypeSelect === '4') {
                $('#discountType').show();
                $('#maxUsabilityDiv').show();
                $('#startingAndEndingDateDiv').show();
            } else if (couponTypeSelect === '6') {
                lastSelect.parent().show();
                lastSelect.html("<option value=''>Choose a User</option>@foreach($users as $u)<option value='{{$u->id}}'>{{$u->name}} {{$u->surname}}</option>@endforeach");
            } else {
                $('#discountType').show();
                $('#maxUsabilityDiv').show();
                $('#startingAndEndingDateDiv').show();
            }
        });

        $('#generateName').on('click', function() {
            let couponCode = generateString();
            $('#couponCode').val(couponCode);
        });

        function generateString(length=8) {
            let result = '';
            let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        $('#saveCoupon').on('click', function() {
            let maxUsability = $('#maxUsability').val();
            let startingDate = $('#startingDate').val();
            let endingDate = $('#endingDate').val();
            let discount = $('#discount').val();
            let lastSelect = $('#lastSelect').val();
            let discountTypeVal = $('input[name="radio"]:checked').val();
            let productID = $('#productSelect').val();
            let couponType = $('#couponTypeSelect').val();
            let couponCode = $('#couponCode').val();
            $.ajax({
                type: 'POST',
                url: '/coupon/couponSaved',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    maxUsability: maxUsability,
                    startingDate: startingDate,
                    endingDate: endingDate,
                    discount: discount,
                    lastSelect: lastSelect,
                    discountTypeVal: discountTypeVal,
                    productID: productID,
                    couponType: couponType,
                    couponCode: couponCode,
                },
                success: function(data) {
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    } else {
                        Materialize.toast('Your coupon has been created successfully', 4000, 'toast-success');
                    }
                }
            });
        });

        $('#percentRadio').on('click', function() {
            $(this).attr('checked', 'checked');
            $('#netRateRadio').removeAttr('checked');
        });

        $('#netRateRadio').on('click', function() {
            $(this).attr('checked', 'checked');
            $('#percentRadio').removeAttr('checked');
        });
    </script>
@elseif($page == 'producttranslations')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                });
            }
        });

        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let productID = $(this).attr('data-product-id');
                let platform = $(this).attr('data-platform');
                window.location.href = '/general-config/translateProduct/' + productID + '/' + langID + '?page='+pageID+'&platform='+platform;
            });
        });
    </script>
@elseif($page == 'translateproductforall')
    <script src="{{asset('../keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('../keditor/src/lang/en.js')}}"></script>
     <script src="{{ asset('js/wizard-form/tagify.min.js') }}"></script>
    <script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.2/autosize.min.js" integrity="sha512-Fv9UOVSqZqj4FDYBbHkvdMFOEopbT/GvdTQfuWUwnlOC6KR49PnxOVMhNG8LzqyDf+tYivRqIWVxGdgsBWOmjg==" crossorigin="anonymous"></script>
    <script>
        $(function() {

            getLangBlock();

            function getLangBlock() {
                $.ajax({
                    type: 'POST',
                    url: '/general-config/getProductTranslation',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        languageID: $('#languageID').val(),
                        productID: $('#productID').val()
                    },
                    success: function(data) {
                        productOperations(data);
                        optionOperations(data);
                    }
                });
            }

            function optionOperations(data) {
                $('.optionWrapper').html('');
                let block = '';
                let notTranslatedOptions = data.notTranslatedOptions;
                let translatedOptions = data.translatedOptions;
                let iterator = 0;
                if (notTranslatedOptions.length > 0) {
                    notTranslatedOptions.forEach(function(item, index) {
                        block +=
                            '<div class="card">\n' +
                            '    <div class="card-header" id="heading'+iterator+'">\n' +
                            '        <h5 class="mb-0">\n' +
                            '            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse'+iterator+'" aria-expanded="false" aria-controls="collapse'+iterator+'">\n' +
                            '                Translate '+item.option.title+' \n' +
                            '            </button>\n' +
                            '            This product\'s option not translated yet.\n' +
                            '        </h5>\n' +
                            '    </div>\n' +
                            '    <div id="collapse'+iterator+'" class="collapse" aria-labelledby="heading'+iterator+'" data-parent="#accordion">\n' +
                            '        <div class="card-body">\n' +
                            '            <div class="row">\n' +
                            '                <input type="hidden" id="optionID" value="'+item.option.id+'">\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Title</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <input readonly class="form-control" type="text" value="'+item.option.title+'" name="titleEnglish" id="titleEnglish">\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <input class="form-control" type="text" value="" name="title" id="title">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Description</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea readonly class="materialize-textarea form-control" name="descriptionEnglish" id="descriptionEnglish">'+item.option.description+'</textarea>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea class="materialize-textarea form-control" name="description" id="description"></textarea>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Meeting Comment</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea readonly class="materialize-textarea form-control" name="meetingCommentEnglish" id="meetingCommentEnglish">'+item.option.meetingComment+'</textarea>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea class="materialize-textarea form-control" name="meetingComment" id="meetingComment"></textarea>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <button class="btn btn-primary saveOptionTranslation">Save Translation</button>\n' +
                            '                </div>\n' +
                            '            </div>\n' +
                            '        </div>\n' +
                            '    </div>\n' +
                            '</div>\n';
                        iterator++;
                    });
                }
                if (translatedOptions.length > 0) {
                    translatedOptions.forEach(function(item, index) {
                        block +=
                            '<div class="card">\n' +
                            '    <div class="card-header" id="heading'+iterator+'">\n' +
                            '        <h5 class="mb-0">\n' +
                            '            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse'+iterator+'" aria-expanded="false" aria-controls="collapse'+iterator+'">\n' +
                            '                Update Translation of '+item.option.title+' \n' +
                            '            </button>\n' +
                            '            This product\'s option translated but you can update it.\n' +
                            '        </h5>\n' +
                            '    </div>\n' +
                            '    <div id="collapse'+iterator+'" class="collapse" aria-labelledby="heading'+iterator+'" data-parent="#accordion">\n' +
                            '        <div class="card-body">\n' +
                            '            <div class="row">\n' +
                            '                <input type="hidden" id="optionID" value="'+item.option.id+'">\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Title</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <input readonly class="form-control" type="text" value="'+item.option.title+'" name="titleEnglish" id="titleEnglish">\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <input class="form-control" type="text" value="'+item.translatedOption.title+'" name="title" id="title">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Description</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea readonly class="materialize-textarea form-control" name="descriptionEnglish" id="descriptionEnglish">'+item.option.description+'</textarea>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea class="materialize-textarea form-control" name="description" id="description">'+item.translatedOption.description+'</textarea>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px;">\n' +
                            '                    <div class="col-md-2">\n' +
                            '                        <label>Meeting Comment</label>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea readonly class="materialize-textarea form-control" name="meetingCommentEnglish" id="meetingCommentEnglish">'+item.option.meetingComment+'</textarea>\n' +
                            '                    </div>\n' +
                            '                    <div class="col-md-5">\n' +
                            '                        <textarea class="materialize-textarea form-control" name="meetingComment" id="meetingComment">'+item.translatedOption.meetingComment+'</textarea>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="col-md-12" style="margin-top: 50px; margin-bottom: 50px;">\n' +
                            '                    <button class="btn btn-primary saveOptionTranslation">Save Translation</button>\n' +
                            '                </div>\n' +
                            '            </div>\n' +
                            '        </div>\n' +
                            '    </div>\n' +
                            '</div>\n';
                        iterator++;
                    });
                }
                $('.optionWrapper').append(block);
            }

            function productOperations(data) {
                let languageToTranslate = data.languageToTranslate.name;
                let product = data.product;
                let productTranslation = data.productTranslation;
                let ptTitle = productTranslation ? productTranslation.title : '';
                let ptShortDesc = productTranslation ? productTranslation.shortDesc : '';
                let ptFullDesc = productTranslation ? productTranslation.fullDesc : '';

                let ptHighlights = productTranslation ? productTranslation.highlights : '';
                let ptIncluded = productTranslation ? productTranslation.included : '';
                let ptNotIncluded = productTranslation ? productTranslation.notIncluded : '';
                let ptKnowBeforeYouGo = productTranslation ? productTranslation.knowBeforeYouGo : '';

                ptHighlights = ptHighlights.replace(/\|/g, "{}");
                ptIncluded = ptIncluded.replace(/\|/g, "{}");
                ptNotIncluded = ptNotIncluded.replace(/\|/g, "{}");
                ptKnowBeforeYouGo = ptKnowBeforeYouGo.replace(/\|/g, "{}");

                product.highlights = product.highlights.replace(/\|/g, " |\n");
                product.included = product.included.replace(/\|/g, " |\n");
                product.notIncluded = product.notIncluded.replace(/\|/g, " |\n");
                product.knowBeforeYouGo = product.knowBeforeYouGo.replace(/\|/g, " |\n");



                let ptCategory = productTranslation ? productTranslation.category : '';
                let ptCancelPolicy = productTranslation ? productTranslation.cancelPolicy : '';
                let block = '';
                block +=
                    `    <div class="sb2-2-add-blog sb2-2-1">
                            <h2>Translate Product from English to ${languageToTranslate}</h2>
                            <div class="row">

                              <ul class="nav nav-tabs nav-tabs-sub" style="margin-top: 40px;">
                                <li class="active"><a data-toggle="tab" href="#titlee">Title</a></li>
                                <li><a data-toggle="tab" href="#short-description">Short Description</a></li>
                                <li><a data-toggle="tab" href="#full-description">Full Description</a></li>
                                <li><a data-toggle="tab" href="#highlightss">Highlights</a></li>
                                <li><a data-toggle="tab" href="#includedd">Included</a></li>
                                <li><a data-toggle="tab" href="#not-included">Not Included</a></li>
                                <li><a data-toggle="tab" href="#know-before-you-go">Know Before You Go</a></li>
                                <li><a data-toggle="tab" href="#categoryy">Category</a></li>
                                <li><a data-toggle="tab" href="#cancel-policy">Cancel Policy</a></li>
                              </ul>





                           <div class="tab-content">

                          <div id="titlee" class="tab-pane fade in active">

                             <div class="col-md-12" style="margin-top: 50px;">

                                <div class="col-md-12">
                                    <input readonly class="form-control" type="text" value="${product.title}" name="titleEnglish" id="titleEnglish">
                                </div>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" value="${ptTitle}" name="title" id="title">
                                </div>
                            </div>

                          </div>




                           <div id="short-description" class="tab-pane fade">
                              <div class="col-md-12" style="margin-top: 50px;">

                                <div class="col-md-12">
                                    <textarea readonly class="materialize-textarea form-control" name="shortDescEnglish" id="shortDescEnglish">${product.shortDesc}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="materialize-textarea form-control" name="shortDesc" id="shortDesc">${ptShortDesc}</textarea>
                                </div>
                            </div>
                           </div>




                           <div id="full-description" class="tab-pane fade">

                                     <div class="col-md-12" style="margin-top: 50px;">

                            <div class="col-md-12">
                                <span readonly class="materialize-textarea form-control" name="fullDescEnglish" id="fullDescEnglish" style="height: 100%;">${product.fullDesc}</span>
                            </div>
                            <div class="col-md-12">
                                <textarea title="Full Description" name="fullDesc" id="fullDesc" class="materialize-textarea form-control">${ptFullDesc}</textarea>
                            </div>
                        </div>

                           </div>


                             <div id="highlightss" class="tab-pane fade">



                              <div class="col-md-12" style="margin-top: 50px;">

                            <div class="col-md-12">
                                <textarea readonly class="materialize-textarea form-control" name="highlightsEnglish" id="highlightsEnglish">${product.highlights}</textarea>
                            </div>
                            <div class="col-md-12">
                                <textarea class="materialize-textarea" name="highlights" id="highlights">${ptHighlights}</textarea>
                            </div>
                        </div>
                             </div>




                              <div id="includedd" class="tab-pane fade">



                                <div class="col-md-12" style="margin-top: 50px;">

                                <div class="col-md-12">
                                    <textarea readonly class="materialize-textarea form-control" name="includedEnglish" id="includedEnglish">${product.included}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="materialize-textarea" name="included" id="included">${ptIncluded}</textarea>
                                </div>
                            </div>

                              </div>




                              <div id="not-included" class="tab-pane fade">


                             <div class="col-md-12" style="margin-top: 50px;">

                            <div class="col-md-12">
                                <textarea readonly class="materialize-textarea form-control" name="notIncludedEnglish" id="notIncludedEnglish">${product.notIncluded}</textarea>
                            </div>
                            <div class="col-md-12">
                                <textarea class="materialize-textarea" name="notIncluded" id="notIncluded">${ptNotIncluded}</textarea>
                            </div>
                        </div>
                              </div>




                              <div id="know-before-you-go" class="tab-pane fade">

                             <div class="col-md-12" style="margin-top: 50px;">

                            <div class="col-md-12">
                                <textarea readonly class="materialize-textarea form-control" name="knowBeforeYouGoEnglish" id="knowBeforeYouGoEnglish">${product.knowBeforeYouGo}</textarea>
                            </div>
                            <div class="col-md-12">
                                <textarea class="materialize-textarea" name="knowBeforeYouGo" id="knowBeforeYouGo">${ptKnowBeforeYouGo}</textarea>
                            </div>
                        </div>
                              </div>



                              <div id="categoryy" class="tab-pane fade">

                                         <div class="col-md-12" style="margin-top: 50px;">

                            <div class="col-md-12">
                                <input readonly class="form-control" type="text" value="${product.category}" name="categoryEnglish" id="categoryEnglish">
                            </div>
                            <div class="col-md-12">
                                <input class="form-control" type="text" value="${ptCategory}" name="category" id="category">
                            </div>
                        </div>
                              </div>


                              <div id="cancel-policy" class="tab-pane fade">

                                  <div class="col-md-12" style="margin-top: 50px;">

                                <div class="col-md-12">
                                    <textarea readonly class="materialize-textarea form-control" name="cancelPolicyEnglish" id="cancelPolicyEnglish">${product.cancelPolicy}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="materialize-textarea form-control" name="cancelPolicy" id="cancelPolicy">${ptCancelPolicy}</textarea>
                                </div>
                            </div>
                              </div>



                           </div>



                          </div>


                             <div class="row">
                            <div class="input-field col s12">
                                <input id="saveTranslationButton" type="submit" class="btn btn-large bnt-primary" value="Save Translation">
                            </div>
                        </div>

                          </div>


                            `;






                let langCode = $('#languageCode').val();
                $('#'+langCode).append(block);





                let highlights = document.querySelector('#highlights');
                let included = document.querySelector('#included');
                let notIncluded = document.querySelector('#notIncluded');
                let knowBeforeYouGo = document.querySelector('#knowBeforeYouGo');

             /*   autosize(document.querySelector('#highlightsEnglish'));
                autosize(document.querySelector('#includedEnglish'));
                autosize(document.querySelector('#notIncludedEnglish'));
                autosize(document.querySelector('#knowBeforeYouGoEnglish'));*/



                       let tagifyHighlights =  new Tagify(highlights,{
        keepInvalidTags     : true,
        backspace           : "edit",
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.'+tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })
    function onDragEndHighlights(elm){
        tagifyHighlights.updateValueByDOMTags()
    }



    let tagifyIncluded =  new Tagify(included, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.'+tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })
    function onDragEndIncluded(elm){
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded =  new Tagify(notIncluded, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.'+tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })
    function onDragEndNotIncluded(elm){
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo =  new Tagify(knowBeforeYouGo, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.'+tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })
    function onDragEndKnowBeforeYouGo(elm){
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }




                KEDITOR.create('fullDesc', {
                    buttonList: [
                        ['fontSize'],
                        ['bold', 'underline', 'italic'],
                        ['fontColor'],
                        ['link'],
                        ['fullScreen', 'codeView'],
                        ['undo', 'redo'],
                    ],
                    minHeight: '250px',
                });
            }

            $('body').on('click', '.saveOptionTranslation', function() {
                let title = $(this).parent().parent().find('#title').val();
                let description = $(this).parent().parent().find('#description').val();
                let meetingComment = $(this).parent().parent().find('#meetingComment').val();
                let optionID = $(this).parent().parent().find('#optionID').val();
                $.ajax({
                    type: 'POST',
                    url: '/general-config/saveOptionTranslationForAll',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        languageID: $('#languageID').val(),
                        optionID: optionID,
                        title: title,
                        description: description,
                        meetingComment: meetingComment
                    },
                    success: function(data) {
                        if (data.successful) {
                            Materialize.toast(data.successful, 4000, 'toast-success');
                            $('.collapse').collapse('hide');
                        }
                    }
                });
            });

            $('.languageTab').on('click', function() {
                $('#'+$('#languageCode').val()).html('');
                $('#languageID').val($(this).attr('data-lang-id'));
                $('#languageCode').val($(this).attr('data-lang-code'));
                getLangBlock();
            });

            $('body').on('click', '#saveTranslationButton', function() {
                $('#fullDesc').val($('.keditor-editable').html());
                $.ajax({
                    type: 'POST',
                    url: '/general-config/saveProductTranslationForAll',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        languageID: $('#languageID').val(),
                        productID: $('#productID').val(),
                        title: $('#title').val(),
                        shortDesc: $('#shortDesc').val(),
                        fullDesc: $('#fullDesc').val(),
                        highlights: $('#highlights').val(),
                        included: $('#included').val(),
                        notIncluded: $('#notIncluded').val(),
                        knowBeforeYouGo: $('#knowBeforeYouGo').val(),
                        category: $('#category').val(),
                        cancelPolicy: $('#cancelPolicy').val()
                    },
                    success: function(data) {
                        if (data.successful) {
                            Materialize.toast(data.successful, 4000, 'toast-success');
                        }
                    }
                });
            });

        });
    </script>
@elseif($page == 'translateproduct')
    <script src="{{asset('../keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('../keditor/src/lang/en.js')}}"></script>
    <script src="{{ asset('js/wizard-form/tagify.min.js') }}"></script>
    <script src="https://unpkg.com/@yaireo/dragsort@1.0.8/dist/dragsort.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.2/autosize.min.js" integrity="sha512-Fv9UOVSqZqj4FDYBbHkvdMFOEopbT/GvdTQfuWUwnlOC6KR49PnxOVMhNG8LzqyDf+tYivRqIWVxGdgsBWOmjg==" crossorigin="anonymous"></script>
    <script>
        KEDITOR.create('fullDesc', {
            buttonList: [
                ['fontSize'],
                ['bold', 'underline', 'italic'],
                ['fontColor'],
                ['link'],
                ['fullScreen', 'codeView'],
                ['undo', 'redo'],
            ],
            minHeight: '250px',
        });

        $('form').on('submit', function() {
            $('#fullDesc').val($('.keditor-editable').html());
        });




        let highlights = document.querySelector('#highlights');
        let included = document.querySelector('#included');
        let notIncluded = document.querySelector('#notIncluded');
        let knowBeforeYouGo = document.querySelector('#knowBeforeYouGo');

        /*autosize(document.querySelector('#highlightsEnglish'));
        autosize(document.querySelector('#includedEnglish'));
        autosize(document.querySelector('#notIncludedEnglish'));
        autosize(document.querySelector('#knowBeforeYouGoEnglish'));*/


         let tagifyHighlights =  new Tagify(highlights,{
        keepInvalidTags     : true,
        backspace           : "edit",
        //

    });
    DragSort(tagifyHighlights.DOM.scope, {
        selector: '.'+tagifyHighlights.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndHighlights
        }
    })
    function onDragEndHighlights(elm){
        tagifyHighlights.updateValueByDOMTags()
    }



    let tagifyIncluded =  new Tagify(included, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyIncluded.DOM.scope, {
        selector: '.'+tagifyIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndIncluded
        }
    })
    function onDragEndIncluded(elm){
        tagifyIncluded.updateValueByDOMTags()
    }

    let tagifyNotIncluded =  new Tagify(notIncluded, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyNotIncluded.DOM.scope, {
        selector: '.'+tagifyNotIncluded.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndNotIncluded
        }
    })
    function onDragEndNotIncluded(elm){
        tagifyNotIncluded.updateValueByDOMTags()
    }

    let tagifyKnowBeforeYouGo =  new Tagify(knowBeforeYouGo, {
        keepInvalidTags     : true,         // do not remove invalid tags (but keep them marked as invalid)
        backspace           : "edit",

    });
    DragSort(tagifyKnowBeforeYouGo.DOM.scope, {
        selector: '.'+tagifyKnowBeforeYouGo.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEndKnowBeforeYouGo
        }
    })
    function onDragEndKnowBeforeYouGo(elm){
        tagifyKnowBeforeYouGo.updateValueByDOMTags()
    }




    </script>


@elseif($page == 'blog-index')

<script>

    $(document).ready(function() {


        $(document).on('click', '[data-delete]', function (e){
            e.preventDefault();
            if(confirm('Are you sure?')){
                location.reload = $(this).data('delete')
            }
        })


        // turn to draft status

    $(document).on('click', '.turn_draft', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-default",
            removeClassName: "btn-success active",
            statusText: "Draft"

        };
         envanter[0] = {
            addClassName: "btn-success active",
            removeClassName: "btn-default",
            statusText: "Confirmed"

        };



       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_draft', data_id: data_id, _token:"{{csrf_token()}}", model:"cz"},
       })
       .done(function(response) {

        if(response.is_draft == "1"){

            $this.closest('td').next("td").find("label").removeClass('btn-success active turn_action').addClass('active btn-danger turn_action').text("Not Published");
        }

        $this.removeClass(envanter[response.is_draft].removeClassName).addClass(envanter[response.is_draft].addClassName).text(envanter[response.is_draft].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });



     // turn to activate status
        $(document).on('click', '.turn_action', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-success active turn_action",
            removeClassName: "active btn-danger turn_action",
            statusText: "Published"

        };
         envanter[0] = {
            addClassName: "active btn-danger turn_action",
            removeClassName: "btn-success active turn_action",
            statusText: "Not Published"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_action', data_id: data_id, _token:"{{csrf_token()}}", model:"cz"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_action].removeClassName).addClass(envanter[response.is_action].addClassName).text(envanter[response.is_action].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });




    });

</script>



@elseif($page == 'blog-index-pct')

<script>

    $(document).ready(function() {




        // turn to draft status

    $(document).on('click', '.turn_draft', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-default",
            removeClassName: "btn-success active",
            statusText: "Draft"

        };
         envanter[0] = {
            addClassName: "btn-success active",
            removeClassName: "btn-default",
            statusText: "Confirmed"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_draft', data_id: data_id, _token:"{{csrf_token()}}", model:"pct"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_draft].removeClassName).addClass(envanter[response.is_draft].addClassName).text(envanter[response.is_draft].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });



     // turn to activate status
        $(document).on('click', '.turn_action', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-success active turn_action",
            removeClassName: "active btn-danger turn_action",
            statusText: "Published"

        };
         envanter[0] = {
            addClassName: "active btn-danger turn_action",
            removeClassName: "btn-success active turn_action",
            statusText: "Not Published"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_action', data_id: data_id, _token:"{{csrf_token()}}", model:"pct"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_action].removeClassName).addClass(envanter[response.is_action].addClassName).text(envanter[response.is_action].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });




    });

</script>


@elseif($page == 'blog-index-pctcom')

<script>

    $(document).ready(function() {




        // turn to draft status

    $(document).on('click', '.turn_draft', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-default",
            removeClassName: "btn-success active",
            statusText: "Draft"

        };
         envanter[0] = {
            addClassName: "btn-success active",
            removeClassName: "btn-default",
            statusText: "Confirmed"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_draft', data_id: data_id, _token:"{{csrf_token()}}", model:"pctcom"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_draft].removeClassName).addClass(envanter[response.is_draft].addClassName).text(envanter[response.is_draft].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });



     // turn to activate status
        $(document).on('click', '.turn_action', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-success active turn_action",
            removeClassName: "active btn-danger turn_action",
            statusText: "Published"

        };
         envanter[0] = {
            addClassName: "active btn-danger turn_action",
            removeClassName: "btn-success active turn_action",
            statusText: "Not Published"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_action', data_id: data_id, _token:"{{csrf_token()}}", model:"pctcom"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_action].removeClassName).addClass(envanter[response.is_action].addClassName).text(envanter[response.is_action].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });




    });

</script>



@elseif($page == 'blog-index-ctp')

<script>

    $(document).ready(function() {




        // turn to draft status

    $(document).on('click', '.turn_draft', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-default",
            removeClassName: "btn-success active",
            statusText: "Draft"

        };
         envanter[0] = {
            addClassName: "btn-success active",
            removeClassName: "btn-default",
            statusText: "Confirmed"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_draft', data_id: data_id, _token:"{{csrf_token()}}", model:"ctp"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_draft].removeClassName).addClass(envanter[response.is_draft].addClassName).text(envanter[response.is_draft].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });



     // turn to activate status
        $(document).on('click', '.turn_action', function(event) {
        event.preventDefault();
        var $this = $(this);
        var data_id = $(this).attr("data-id");
        var envanter = [];
        envanter[1] = {
            addClassName: "btn-success active turn_action",
            removeClassName: "active btn-danger turn_action",
            statusText: "Published"

        };
         envanter[0] = {
            addClassName: "active btn-danger turn_action",
            removeClassName: "btn-success active turn_action",
            statusText: "Not Published"

        };


       $.ajax({
           url: '{{url("blog/ajax")}}',
           type: 'POST',
           dataType: 'json',
           data: {action: 'turn_action', data_id: data_id, _token:"{{csrf_token()}}", model:"ctp"},
       })
       .done(function(response) {

        $this.removeClass(envanter[response.is_action].removeClassName).addClass(envanter[response.is_action].addClassName).text(envanter[response.is_action].statusText);


       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

    });




    });

</script>




@elseif($page == 'prodmetatagstrans')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                });
            }
        });

        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let productID = $(this).attr('data-product-id');
                let platform = $(this).attr('data-platform');
                window.location.href = '/general-config/translateProductMetaTags/' + productID + '/' + langID + '?page=' + pageID + '&platform=' + platform;
            });
        });
    </script>








@elseif($page == 'pagemetatagstrans')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                });
            }
        });

        $(function() {






            $('.languageID').on('change', function() {
                let dtPageID = $('#pageID').val();
                let langID = $(this).val();
                let pageID = $(this).attr('data-page-id');
                let platform = $(this).attr('data-platform');
                window.location.href = '/general-config/translatePageMetaTags/' + pageID + '/' + langID + '?page=' + dtPageID+"&platform="+platform;


            });
        });
    </script>
@elseif($page == 'optiontranslations')
    <script>
        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                });
            }
        });

        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let optionID = $(this).attr('data-option-id');
                window.location.href = '/general-config/translateOption/' + optionID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'routetranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let optionID = $(this).attr('data-route-id');
                window.location.href = '/general-config/translateRoute/' + optionID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'blogtranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let langID = $(this).val();
                let blogID = $(this).attr('data-blog-id');
                let platform = $(this).attr('data-platform');
                window.location.href = '/general-config/translateBlog/' + blogID + '/' + langID + '?platform=' + platform;
            });
        });
    </script>
@elseif($page == 'translateblog')
    <script src="{{asset('/keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('/keditor/src/lang/en.js')}}"></script>
    <script>
        KEDITOR.create('postContent', {
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic'],
                ['removeFormat'],
                ['fontColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'image', 'video'],
                ['fullScreen', 'codeView'],
                ['preview', 'print'],
            ],
            minHeight: '250px',
            imageUploadUrl: '/blog/create/uploadImageForBlogPost',
            imageUploadHeader: {
                contentType: 'multipart/form-data',
                csrfToken: $('meta[name="csrf-token"]').attr('content'),
            }
        });

        $('form').on('submit', function() {
            $('#postContent').val($('.keditor-editable').html());
        });
    </script>
@elseif($page == 'blogmetatagstrans')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let langID = $(this).val();
                let blogID = $(this).attr('data-blog-id');
                let platform = $(this).attr('data-platform');
                window.location.href = '/general-config/translateBlogMetaTags/' + blogID + '/' + langID + '?platform=' + platform;
            });
        });
    </script>
@elseif($page == 'countrytranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let countryID = $(this).attr('data-country-id');
                window.location.href = '/general-config/translateCountry/' + countryID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'citytranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let cityID = $(this).attr('data-city-id');
                window.location.href = '/general-config/translateCity/' + cityID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'faqtranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let cityID = $(this).attr('data-city-id');
                window.location.href = '/general-config/translateFAQ/' + cityID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'categorytranslations')
    <script>
        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let categoryID = $(this).attr('data-category-id');
                window.location.href = '/general-config/translateCategory/' + categoryID + '/' + langID + '?page=' + pageID;
            });
        });
    </script>
@elseif($page == 'attractiontranslations')
    <script>

        $('#datatable').on('DOMSubtreeModified', function() {
            let pageID = $('#pageID').val();
            let isRun = $('#isRun').val();
            if (isRun === '0') {
                $('#isRun').val('1');
                $.ajax({
                    type: 'POST',
                    url: '/pageIDForDataTable',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pageID: pageID,
                    },
                });
            }
        });


        $(function() {
            $('.languageID').on('change', function() {
                let pageID = $('#pageID').val();
                let langID = $(this).val();
                let platform = $(this).attr('data-platform');
                let attractionID = $(this).attr('data-attraction-id');
                window.location.href = '/general-config/translateAttraction/' + attractionID + '/' + langID + '?page=' + pageID+"&platform="+platform;
            });
        });
    </script>
@elseif($page == 'translateattraction')
    <script src="{{asset('js/wizard-form/tagify.min.js')}}"></script>
    <script src="{{asset('/keditor/build/keditor.min.js')}}"></script>
    <script src="{{asset('/keditor/src/lang/en.js')}}"></script>
    <script>
        KEDITOR.create('description', {
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic'],
                ['removeFormat'],
                ['fontColor'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'table'],
                ['link', 'video'],
                ['fullScreen', 'codeView'],
                ['preview', 'print'],

            ],
            minHeight: '250px',
            maxHeight : '450px',
            bgColor: "#f2f2f2",
        });

        $('form').on('submit', function() {
            $('#description').val($('.keditor-editable').html());
        });
    </script>
    <script>
        let tagsEnglish = document.querySelector('#tagsEnglish');
        let tagifyTagEnglish =  new Tagify(tagsEnglish, {
            keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });

        let tagsTranslation = document.querySelector('#tagsTranslation');
        let tagifyTagsTranslation =  new Tagify(tagsTranslation, {
            keepInvalidTags: true,         // do not remove invalid tags (but keep them marked as invalid)
            backspace: "edit",
            placeholder: "type something",
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join('|'),
        });
    </script>

    <script>

        $(document).ready(function() {

            $('.nav-tabs li').click(function(event) {
                if($(this).find("a").attr("href") == "#descriptions"){
                    console.log("test");
                   $("#sticky-full-description").css("position", "fixed").css("bottom", "40px").css("right","40px").css("width",($(".tab-content").outerWidth()/2 | 0)+"px");

                }

            });
        });
    </script>
@elseif($page == 'change-meta-tags')
    <script>
        $(function() {
            $('#title, #description, #keywords').on('keydown', function(e) {
                let charCount = $(this).val().length;
                if (charCount > 250 && (e.keyCode !== 8 && e.keyCode !== 46)) {
                    e.preventDefault();
                }
            });

            $('#title, #description, #keywords').on('keyup', function(e) {
                let charCount = $(this).val().length;
                if (charCount <= 250) {
                    $(this).parent().find('#charCounter').html(255 - parseInt(charCount));
                } else {
                    Materialize.toast('You reached character limit!', 4000, 'toast-alert');
                }
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
                let title = $('#title').val().length;
                if (title > 251) {
                    Materialize.toast('Title length is longer than 255 characters!', 4000, 'toast-alert');
                    return;
                }
                let description = $('#description').val().length;
                if (description > 251) {
                    Materialize.toast('Description length is longer than 255 characters!', 4000, 'toast-alert');
                    return;
                }
                let keywords = $('#keywords').val().length;
                if (keywords > 251) {
                    Materialize.toast('Keywords length is longer than 255 characters!', 4000, 'toast-alert');
                    return;
                }
                $('#changePageMetaTags').submit();
            });
        });
    </script>
@elseif($page == 'product-sort')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script>
        $(function() {
            $("#productSelect").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });

            $("#pageSelect").select2({
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
            // Do not display the item if there is no 'text'     property
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
        $('#pageSelect').on('change', function() {
            $('#productSelect').html('');
            $('#products').html('');
            let pageID = $(this).val();
            $.ajax({
                type: 'POST',
                url: '/general-config/pageSelect',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    pageID: pageID,
                },
                success: function(data) {
                    let block = '<option>Choose a Product</option>';
                    let product = data.finalProducts;
                    for (let i = 0; i < product.length; i++) {
                        block += '<option value='+product[i].id+' data-is-selected="0" data-foo='+product[i].referenceCode+'>'+product[i].title+'</option>';
                    }
                    $('#productSelect').append(block);
                }
            });
        });

        $('#productSelect').on('change', function() {
            let productID = $(this).val();
            let pageID = $('#pageSelect').val();
            let productsArray = [];
            $('.sortable').each(function() {
                productsArray.push($(this).attr('data-product-id'));
            });
            let isProductInArray = productsArray.includes($('#productSelect').val());
            if (isProductInArray == true) {
                Materialize.toast('The product you selected has been previously selected.', 4000, 'toast-alert');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/general-config/productSelect',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        productID: productID,
                        pageID: pageID,
                    },
                    success: function(data) {
                        if (data.SAME_PRODUCT) {
                            Materialize.toast('The product you selected has been previously selected.', 4000, 'toast-alert');
                        } else {
                            $('#products').append('<li class="sortable" data-product-id="'+data.product.id+'">'+data.product.title+'<a class="unsetSortedProduct" style="float:right;font-size: 14px">X</a></li>');
                        }
                    }
                });
            }
        });
    </script>
    <script>
        $(function() {
            $("#products").sortable({
                revert: true,
                start: function(event, ui) {
                },
                change: function(event, ui) {
                    //
                },
                update: function(event, ui) {
                    let position = ui.item.index();
                }
            });
            $("#draggable").draggable({
                connectToSortable: "#sortable",
                helper: "clone",
                revert: "invalid"
            });
            $("li").disableSelection();
        });

        $('#sendButton').on('click', function() {
            let sortedProducts = [];
            let pageID = $('#pageSelect').val();
            $('.sortable').each(function() {
                sortedProducts.push($(this).attr('data-product-id'));
            });
            $.ajax({
                type: 'POST',
                url: '/general-config/sendProductSort',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    pageID: pageID,
                    sortedProducts: sortedProducts
                },
                success: function(data) {
                    let page = data.page;
                    Materialize.toast('You has been changed product sorting for '+page.name+' successfully!', 4000, 'toast-success');
                }
            });
        });

        $('#pageSelect').on('change', function() {
            let pageID = $(this).val();
            $('#products').html('');
            $.ajax({
                type: 'POST',
                url: '/general-config/getProductSortForAPage',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    pageID: pageID,
                },
                success: function(data) {
                    let sortedProducts = data.sortedProducts;
                    let block = '';
                    for (let i = 0; i < sortedProducts.length; i++) {
                        block += '<li class="sortable" data-product-id="'+sortedProducts[i].id+'">'+sortedProducts[i].title+'<a data-product-id='+sortedProducts[i].id+' class="unsetSortedProduct" style="float:right;font-size: 14px">X</a></li>';
                    }
                    $('#products').append(block);
                }
            });
        });

        $('body').on('click', '.unsetSortedProduct', function() {
            let $this = $(this);
            let productID = $this.attr('data-product-id');
            let pageID = $('#pageSelect').val();
            $.ajax({
                type: 'POST',
                url: '/general-config/unsetSortedProduct',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    productID: productID,
                    pageID: pageID
                },
                success: function(data) {
                    let page = data.page;
                    $this.parent().remove();
                    Materialize.toast('You has been deleted product successfully from '+page.name+' sort list !', 4000, 'toast-success');
                }
            });
        });
    </script>
@elseif($page == 'faq-create')
    <script>
        $('#addFaqCategoryButton').on('click', function() {
            $('#addNewCategoryIterator').val(0);
            $('#faqCategoryDiv').html('');
            $('#faqQuestionAnswerDiv').html('');

            let block = '';
            block += '<div class="form-group">\n' +
                '<div class="input-field col-md-6">\n' +
                '<input type="text" id="faqCategoryName" placeholder="FAQ Category Name">\n' +
                '</div>\n' +
                '<div class="form-group">' +
                '<div class="input-field col-md-4">' +
                '<button class="btn btn-primary" id="saveCategory">Save Category</button>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-2 input-field" id="addNewFaqQuestionAnswerDiv"></div>' +
                '</div>';
            if ($('#addNewCategoryIterator').val() !== '1') {
                $('#faqCategoryDiv').append(block);
            }
            $('#addNewCategoryIterator').val(1);
        });

        $('#chooseFromOldCategories').on('click', function() {
            $('#addNewCategoryIterator').val(0);
            $('#faqCategoryDiv').html('');
            $('#faqQuestionAnswerDiv').html('');
            let block = '';
            $.ajax({
                method: 'POST',
                url: '/getOldFaqCategories',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(data) {
                    let faqCategories = data.faqCategories;
                    let optionBlock = '<option disabled selected>Choose a Category</option>';
                    faqCategories.forEach(function(faqCategory) {
                        optionBlock += '<option value="'+faqCategory.id+'">'+faqCategory.name+'</option>';
                    });
                    block += '<div class="form-group">' +
                        '<select id="faqCategorySelect" class="custom-select browser-default select2 select2-hidden-accessible">\n' +
                        optionBlock+
                        '</select>' +
                        '</div>' +
                        '<div class="col-md-2 input-field" id="addNewFaqQuestionAnswerDiv"></div>' +
                        '</div>';
                    if ($('#addNewCategoryIterator').val() !== '1') {
                        $('#faqCategoryDiv').append(block);
                    }
                    $('#addNewCategoryIterator').val(1);
                }
            });
        });

        $('body').on('click', '#saveCategory', function() {
            let name = $('#faqCategoryName').val();
            $.ajax({
                method: 'POST',
                url: '/addFaqCategory',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: name,
                },
                success: function(data) {
                    let plusBlock = '<div class="row"><div class="col-md-6"><button id="addNewFaqQuestionAnswer" class="btn btn-primary">+</button></div><div class="col-md-2"><button id="saveFaqQuestionAnswer" class="btn-primary btn">Save</button></div>';
                    let block = '';
                    $('#faqCategoryID').val(data.faqCategory.id);
                    block += '<div class="form-group">' +
                        '<div class="input-field col-md-12">' +
                        '<input type="text" class="faqQuestion" placeholder="FAQ Question...">' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<div class="input-field col-md-12">' +
                        '<textarea type="text" class="faqAnswer">FAQ Answer...</textarea>' +
                        '</div>' +
                        '</div>';
                    if (data.error) {
                        Materialize.toast(data.error, 4000, 'toast-error');
                    } else {
                        $('#addNewFaqQuestionAnswerDiv').append(plusBlock);
                        $('#faqQuestionAnswerDiv').append(block);
                    }
                }
            });
        });

        $('body').on('change', '#faqCategorySelect', function() {
            $('#faqCategoryID').val($('body #faqCategorySelect').val());
            let plusBlock = '<div class="row"><div class="col-md-6"><button id="addNewFaqQuestionAnswer" class="btn btn-primary">+</button></div><div class="col-md-2"><button id="saveFaqQuestionAnswer" class="btn-primary btn">Save</button></div></div>';
            let block = '';
            block += '<div class="form-group">' +
                '<div class="input-field col-md-12">' +
                '<input type="text" class="faqQuestion" placeholder="FAQ Question...">' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="input-field col-md-12">' +
                '<textarea type="text" class="faqAnswer">FAQ Answer...</textarea>' +
                '</div>' +
                '</div>' +
                '<div>';
            $('#faqQuestionAnswerDiv').append(block);
            $('body #addNewFaqQuestionAnswerDiv').append(plusBlock);
        });

        $('body').on('click', '#addNewFaqQuestionAnswer', function() {
            let block = '';
            block += '<div class="form-group">' +
                '<div class="input-field col-md-12">' +
                '<input type="text" class="faqQuestion" placeholder="FAQ Question...">' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="input-field col-md-12">' +
                '<textarea type="text" class="faqAnswer">FAQ Answer...</textarea>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('#faqQuestionAnswerDiv').append(block);
        });

        $('body').on('click', '#saveFaqQuestionAnswer', function() {
            let faqCategoryID = $('#faqCategoryID').val();
            let faqAnswer = $(".faqAnswer");
            let faqQuestion = $('.faqQuestion');
            let faqQuestionAnswerArray = [];
            for (let i = 0; i < faqAnswer.length; i++) {
                faqQuestionAnswerArray.push({ 'question': $(faqQuestion[i]).val(), 'answer': $(faqAnswer[i]).val() });
            }
            $.ajax({
                method: 'POST',
                url: '/saveFaqQuestionAnswer',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    faqCategoryID: faqCategoryID,
                    faqQuestionAnswerArray: faqQuestionAnswerArray,
                },
                success: function(data) {
                    Materialize.toast(data.success, 4000, 'toast-success');
                }
            });
        });
    </script>
    @elseif($page=='statistic')
    <script src="{{asset('custom/apexcharts-bundle/dist/apexcharts.js')}}"></script>
    <script src="{{asset('custom/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('custom/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('custom/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

    <script>
        var optResponsive = [
            {
                breakpoint: 1600,
                options: {
                    chart: {
                        width: '100%',
                        height: 700
                    },
                    legend: {
                        position: 'bottom',
                        show: true,
                        showForSingleSeries: false,
                        showForNullSeries: true,
                        showForZeroSeries: true,
                        horizontalAlign: 'left',
                        floating: false,
                        fontSize: '13px',
                        fontFamily: 'Helvetica, Arial',
                        fontWeight: 350,
                        formatter: function (value, ops) {
                            return ops.w.globals.series[ops.seriesIndex] + ": " + value
                        },
                        inverseOrder: false,
                        width: '100%',
                        height: 300,
                        tooltipHoverFormatter: undefined,
                        customLegendItems: [],
                        offsetX: 0,
                        offsetY: 0,
                        labels: {
                            colors: undefined,
                            useSeriesColors: false
                        },
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 12,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 5,
                            vertical: 2
                        },
                        onItemClick: {
                            toggleDataSeries: true
                        },
                        onItemHover: {
                            highlightDataSeries: true
                        },
                    },
                }
            },
            {
                breakpoint: 1290,
                options: {
                    chart: {
                        width: '100%',
                        height: 700
                    },
                    legend: {
                        position: 'bottom',
                        show: true,
                        showForSingleSeries: false,
                        showForNullSeries: true,
                        showForZeroSeries: true,
                        horizontalAlign: 'left',
                        floating: false,
                        fontSize: '13px',
                        fontFamily: 'Helvetica, Arial',
                        fontWeight: 350,
                        formatter: function (value, ops) {
                            return ops.w.globals.series[ops.seriesIndex] + ": " + value
                        },
                        inverseOrder: false,
                        width: '100%',
                        height: 300,
                        tooltipHoverFormatter: undefined,
                        customLegendItems: [],
                        offsetX: 0,
                        offsetY: 0,
                        labels: {
                            colors: undefined,
                            useSeriesColors: false
                        },
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 12,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 0,
                            vertical: 2
                        },
                        onItemClick: {
                            toggleDataSeries: true
                        },
                        onItemHover: {
                            highlightDataSeries: true
                        },
                    },
                }
            },
            {
                breakpoint: 1190,
                options: {
                    chart: {
                        width: '100%',
                        height: 700
                    },
                    legend: {
                        position: 'bottom',
                        show: true,
                        showForSingleSeries: false,
                        showForNullSeries: true,
                        showForZeroSeries: true,
                        horizontalAlign: 'left',
                        floating: false,
                        fontSize: '13px',
                        fontFamily: 'Helvetica, Arial',
                        fontWeight: 350,
                        formatter: function (value, ops) {
                            return ops.w.globals.series[ops.seriesIndex] + ": " + value
                        },
                        inverseOrder: false,
                        width: '100%',
                        height: 300,
                        tooltipHoverFormatter: undefined,
                        customLegendItems: [],
                        offsetX: 0,
                        offsetY: 0,
                        labels: {
                            colors: undefined,
                            useSeriesColors: false
                        },
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 12,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 80,
                            vertical: 2
                        },
                        onItemClick: {
                            toggleDataSeries: true
                        },
                        onItemHover: {
                            highlightDataSeries: true
                        },
                    },
                }
            },
            {
                breakpoint: 700,
                options: {
                    chart: {
                        width: '100%',
                        height: 700
                    },
                    legend: {
                        position: 'bottom',
                        show: true,
                        showForSingleSeries: false,
                        showForNullSeries: true,
                        showForZeroSeries: true,
                        horizontalAlign: 'left',
                        floating: false,
                        fontSize: '12px',
                        fontFamily: 'Helvetica, Arial',
                        fontWeight: 300,
                        formatter: function (value, ops) {
                            return ops.w.globals.series[ops.seriesIndex] + ": " + value
                        },
                        inverseOrder: false,
                        width: '100%',
                        height: 300,
                        tooltipHoverFormatter: undefined,
                        customLegendItems: [],
                        offsetX: 0,
                        offsetY: 0,
                        labels: {
                            colors: undefined,
                            useSeriesColors: false
                        },
                        markers: {
                            width: 12,
                            height: 12,
                            strokeWidth: 0,
                            strokeColor: '#fff',
                            fillColors: undefined,
                            radius: 12,
                            customHTML: undefined,
                            onClick: undefined,
                            offsetX: 0,
                            offsetY: 0
                        },
                        itemMargin: {
                            horizontal: 25,
                            vertical: 1
                        },
                        onItemClick: {
                            toggleDataSeries: true
                        },
                        onItemHover: {
                            highlightDataSeries: true
                        },
                    },
                }
            }];

        var optColData = @json($statsArr["hour"]["data"]);
        var optColCategories = @json($statsArr["hour"]["categories"]);
        var optionsColumn = {
            series: [{
                name: 'Bookings Created',
                data: optColData
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },

            xaxis: {
                categories: optColCategories,
                position: 'bottom',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: false,
                }

            },
            title: {
                text: 'Hour - Last 30 days',
                floating: true,
                offsetY: 0,
                align: 'center',
                style: {
                    fontSize: '15px',
                    fontWeight: 'bold',
                    color: '#04278f'
                }
            }
        };

        var optMonData = @json($statsArr["language"]["data"]);
        var optMonCategories = @json($statsArr["language"]["categories"]);
        var optionsMonochrome = {
            series: optMonData,
            chart: {
                width: '100%',
                type: 'pie',
            },
            labels: optMonCategories,
            theme: {
                monochrome: {
                    enabled: true
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -5
                    }
                }
            },
            title: {
                text: "Language - Last 30 days",
                align: 'center',
                style: {
                    fontSize: '15px',
                    fontWeight: 'bold',
                    color: '#04278f'
                }
            },
            dataLabels: {
                formatter(val, opts) {
                    const name = opts.w.globals.labels[opts.seriesIndex]
                    return [name, val.toFixed(1) + '%']
                }
            },
            legend: {
                show: false
            }
        };
        var optMon2Data = @json($statsArr["deviceType"]["data"]);
        var optMon2Categories = @json($statsArr["deviceType"]["categories"]);
        var optionsMonochrome2 = {
            series: optMon2Data,
            chart: {
                width: '100%',
                type: 'pie',
            },
            labels: optMon2Categories,
            theme: {
                monochrome: {
                    enabled: true
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -5
                    }
                }
            },
            title: {
                text: "Device Type - Last 30 days",
                align: 'center',
                style: {
                    fontSize: '15px',
                    fontWeight: 'bold',
                    color: '#04278f'
                }
            },
            dataLabels: {
                formatter(val, opts) {
                    const name = opts.w.globals.labels[opts.seriesIndex]
                    return [name, val.toFixed(1) + '%']
                }
            },
            legend: {
                show: false
            }
        };

        $(document).ready(function () {

            var chart;
            var chartOpt;
            var chartOptCancel;
            var d = new Date();
            var date = d.getFullYear() + '-' + (d.getMonth()) + '-' + d.getDate();
            start_date = date;
            finish_date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
            getReadyStatistic(start_date, finish_date)

            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('.apex-date-span').html('<img style="margin-bottom: -188px" src="/img/loading1.gif"  alt="">');
                    getUpdateStatistic(start.format('Y-M-D'), end.format('Y-M-D'), "chart");
                }
            );

            $('#daterange-btn-opt').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('.apex-date-span-opt').html('<img style="margin-bottom: -28px" src="/img/loading1.gif"  alt="">');
                    getUpdateStatistic(start.format('Y-M-D'), end.format('Y-M-D'), "chart-opt");
                }
            );

            $('#daterange-btn-opt-cancel').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('.apex-date-span-opt-cancel').html('<img style="margin-bottom: -28px" src="/img/loading1.gif"  alt="">');
                    getUpdateStatistic(start.format('Y-M-D'), end.format('Y-M-D'), "chart-opt-cancel");
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true
            })

            var chartColumn = new ApexCharts(document.querySelector("#chart-column"), optionsColumn);
            chartColumn.render();

            var chartMonochrome = new ApexCharts(document.querySelector("#chart-monochrome"), optionsMonochrome);
            chartMonochrome.render();

            var chartMonochrome2 = new ApexCharts(document.querySelector("#chart-monochrome2"), optionsMonochrome2);
            chartMonochrome2.render();
        });

        function getReadyStatistic(start_date, finish_date) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/statistic/ready',
                data: {
                    startDate: start_date,
                    finishDate: finish_date,
                },
                success: function (data) {
                    var options1 = {
                        title: {
                            text: "Booking Order Avarage Days",
                            align: 'center',
                            margin: 0,
                            offsetX: 0,
                            offsetY: 0,
                            floating: false,
                            style: {
                                fontSize: '18px',
                                fontWeight: 'bold',
                                fontFamily: undefined,
                                color: '#04278f'
                            },
                        },
                        series: [{
                            name: 'Order Avg Days',
                            data: data.order_diff_avg
                        },
                            {
                                name: 'No Show',
                                data: data.order_diff_noShow
                            }
                        ],
                        chart: {
                            height: 350,
                            type: 'area'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        noData: {
                            text: 'Loading...'
                        },
                        xaxis: {
                            type: 'date',
                            categories: data.order_diff_date
                        },
                        tooltip: {
                            x: {
                                format: 'dd/MM/yy HH:mm'
                            },
                        },
                    };
                    var optionsOpt = {
                        series: data.opt_value,
                        title: {
                            text: "Option Pie",
                            align: 'center',
                            margin: 0,
                            offsetX: 0,
                            offsetY: 0,
                            floating: false,
                            style: {
                                fontSize: '18px',
                                fontWeight: 'bold',
                                fontFamily: undefined,
                                color: '#04278f'
                            },
                        },
                        subtitle: {
                            text: "This Table shown success options values",
                            align: 'center',
                            margin: 15,
                            offsetX: 0,
                            offsetY: 25,
                            floating: false,
                            style: {
                                fontSize: '12px',
                                fontWeight: 'normal',
                                fontFamily: undefined,
                                color: '#9699a2'
                            },
                        },
                        chart: {
                            width: '100%',
                            type: 'pie',
                            dropShadow: {
                                enabled: false,
                                enabledOnSeries: undefined,
                                top: 0,
                                left: 0,
                                blur: 3,
                                color: 'rgba(4,41,122,0.94)',
                                opacity: 0.35
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 2000,
                                animateGradually: {
                                    enabled: true,
                                    delay: 150
                                },
                                dynamicAnimation: {
                                    enabled: true,
                                    speed: 350
                                }
                            },
                        },
                        shared: true,
                        labels: data.opt_label,
                        legend: {
                            position: 'bottom',
                            show: true,
                            showForSingleSeries: false,
                            showForNullSeries: true,
                            showForZeroSeries: true,
                            horizontalAlign: 'left',
                            floating: false,
                            fontSize: '14px',
                            fontFamily: 'Helvetica, Arial',
                            fontWeight: 400,
                            formatter: function (value, ops) {
                                return ops.w.globals.series[ops.seriesIndex] + ": " + value
                            },
                            inverseOrder: false,
                            width: '80%',
                            height: 250,
                            tooltipHoverFormatter: undefined,
                            customLegendItems: [],
                            offsetX: 0,
                            offsetY: 0,
                            labels: {
                                colors: undefined,
                                useSeriesColors: false
                            },
                            markers: {
                                width: 12,
                                height: 12,
                                strokeWidth: 0,
                                strokeColor: '#fff',
                                fillColors: undefined,
                                radius: 12,
                                customHTML: undefined,
                                onClick: undefined,
                                offsetX: 0,
                                offsetY: 0
                            },
                            itemMargin: {
                                horizontal: 40,
                                vertical: 3
                            },
                            onItemClick: {
                                toggleDataSeries: true
                            },
                            onItemHover: {
                                highlightDataSeries: true
                            },
                        },
                        responsive: optResponsive,
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0,
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'lighten',
                                    value: 0.15,
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'darken',
                                    value: 0.35,
                                }
                            },
                        },
                    };
                    var optionsOptCancel = {
                        series: data.opt_value_c,
                        title: {
                            text: "Cancelled Option Pie",
                            align: 'center',
                            margin: 0,
                            offsetX: 0,
                            offsetY: 0,
                            floating: false,
                            style: {
                                fontSize: '18px',
                                fontWeight: 'bold',
                                fontFamily: undefined,
                                color: '#04278f'
                            },
                        },
                        subtitle: {
                            text: "This Table shown failed options values",
                            align: 'center',
                            margin: 15,
                            offsetX: 0,
                            offsetY: 25,
                            floating: false,
                            style: {
                                fontSize: '12px',
                                fontWeight: 'normal',
                                fontFamily: undefined,
                                color: '#9699a2'
                            },
                        },
                        chart: {
                            width: '100%',
                            type: 'pie',
                            dropShadow: {
                                enabled: false,
                                enabledOnSeries: undefined,
                                top: 0,
                                left: 0,
                                blur: 3,
                                color: 'rgba(4,41,122,0.94)',
                                opacity: 0.35
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 2000,
                                animateGradually: {
                                    enabled: true,
                                    delay: 150
                                },
                                dynamicAnimation: {
                                    enabled: true,
                                    speed: 350
                                }
                            },
                        },
                        shared: true,
                        legend: {
                            position: 'bottom',
                            show: true,
                            showForSingleSeries: false,
                            showForNullSeries: true,
                            showForZeroSeries: true,
                            horizontalAlign: 'left',
                            floating: false,
                            fontSize: '14px',
                            fontFamily: 'Helvetica, Arial',
                            fontWeight: 400,
                            formatter: function (value, ops) {
                                return ops.w.globals.series[ops.seriesIndex] + ": " + value
                            },
                            inverseOrder: false,
                            width: '80%',
                            height: 250,
                            tooltipHoverFormatter: undefined,
                            customLegendItems: [],
                            offsetX: 0,
                            offsetY: 0,
                            labels: {
                                colors: undefined,
                                useSeriesColors: false
                            },
                            markers: {
                                width: 12,
                                height: 12,
                                strokeWidth: 0,
                                strokeColor: '#fff',
                                fillColors: undefined,
                                radius: 12,
                                customHTML: undefined,
                                onClick: undefined,
                                offsetX: 0,
                                offsetY: 0
                            },
                            itemMargin: {
                                horizontal: 50,
                                vertical: 3
                            },
                            onItemClick: {
                                toggleDataSeries: true
                            },
                            onItemHover: {
                                highlightDataSeries: true
                            },
                        },
                        labels: data.opt_label_c,
                        responsive: optResponsive,
                        states: {
                            normal: {
                                filter: {
                                    type: 'none',
                                    value: 0,
                                }
                            },
                            hover: {
                                filter: {
                                    type: 'lighten',
                                    value: 0.15,
                                }
                            },
                            active: {
                                allowMultipleDataPointsSelection: false,
                                filter: {
                                    type: 'darken',
                                    value: 0.35,
                                }
                            },
                        },

                    };

                    chart = new ApexCharts(document.querySelector("#chart"), options1);
                    chartOpt = new ApexCharts(document.querySelector("#chart-opt"), optionsOpt);
                    chartOptCancel = new ApexCharts(document.querySelector("#chart-opt-cancel"), optionsOptCancel);


                    chart.render();
                    chartOpt.render();
                    chartOptCancel.render();
                    $('#daterange-btn-opt-cancel').text(start_date + ' ⇿ ' + finish_date);
                    $('.apex-date-span-opt-cancel').html('');
                    $('.apex-date-span').html('<span>' + start_date + ' <i class="icon-cz-add-time"></i> ' + finish_date + '</span>');
                    $('#daterange-btn-opt').text(start_date + ' ⇿ ' + finish_date);
                    $('.apex-date-span-opt').html('');
                }
            });
        }

        function getUpdateStatistic(start_date, finish_date, chart_name) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/statistic/upload/' + chart_name,
                data: {
                    startDate: start_date,
                    finishDate: finish_date,
                },
                success: function (data) {

                    switch (chart_name) {
                        case 'chart':
                            chart.destroy();
                            $('#chart').children().remove();

                            var options1 = {
                                series: [{
                                    name: 'Order Avg Days',
                                    data: data.order_diff_avg
                                },
                                    {
                                        name: 'No Show',
                                        data: data.order_diff_noShow
                                    }
                                ],
                                chart: {
                                    height: 350,
                                    type: 'area'
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                noData: {
                                    text: 'Loading...'
                                },
                                xaxis: {
                                    type: 'date',
                                    categories: data.order_diff_date
                                },
                                tooltip: {
                                    x: {
                                        format: 'dd/MM/yy HH:mm'
                                    },
                                },
                            };
                            chart = new ApexCharts(document.querySelector("#chart"), options1);

                            chart.render();
                            $('.apex-date-span').html('<span>' + start_date + ' <i class="icon-cz-add-time"></i> ' + finish_date + '</span>');
                            break;
                        case 'chart-opt':
                            chartOpt.destroy();
                            $('#chart-opt').children().remove();

                            var optionsOpt = {
                                series: data.opt_value,
                                title: {
                                    text: "Option Pie",
                                    align: 'center',
                                    margin: 0,
                                    offsetX: 0,
                                    offsetY: 0,
                                    floating: false,
                                    style: {
                                        fontSize: '18px',
                                        fontWeight: 'bold',
                                        fontFamily: undefined,
                                        color: '#04278f'
                                    },
                                },
                                subtitle: {
                                    text: "This Table shown success options values",
                                    align: 'center',
                                    margin: 15,
                                    offsetX: 0,
                                    offsetY: 25,
                                    floating: false,
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 'normal',
                                        fontFamily: undefined,
                                        color: '#9699a2'
                                    },
                                },
                                chart: {
                                    id: 'optionsOpt',
                                    width: '100%',
                                    type: 'pie',
                                    dropShadow: {
                                        enabled: false,
                                        enabledOnSeries: undefined,
                                        top: 0,
                                        left: 0,
                                        blur: 3,
                                        color: 'rgba(4,41,122,0.94)',
                                        opacity: 0.35
                                    },
                                    animations: {
                                        enabled: true,
                                        easing: 'easeinout',
                                        speed: 2000,
                                        animateGradually: {
                                            enabled: true,
                                            delay: 150
                                        },
                                        dynamicAnimation: {
                                            enabled: true,
                                            speed: 350
                                        }
                                    },
                                },
                                shared: true,
                                labels: data.opt_label,
                                legend: {
                                    position: 'bottom',
                                    show: true,
                                    showForSingleSeries: false,
                                    showForNullSeries: true,
                                    showForZeroSeries: true,
                                    horizontalAlign: 'left',
                                    floating: false,
                                    fontSize: '14px',
                                    fontFamily: 'Helvetica, Arial',
                                    fontWeight: 400,
                                    formatter: function (value, ops) {
                                        return ops.w.globals.series[ops.seriesIndex] + ": " + value
                                    },
                                    inverseOrder: false,
                                    width: '80%',
                                    height: 250,
                                    tooltipHoverFormatter: undefined,
                                    customLegendItems: [],
                                    offsetX: 0,
                                    offsetY: 0,
                                    labels: {
                                        colors: undefined,
                                        useSeriesColors: false
                                    },
                                    markers: {
                                        width: 12,
                                        height: 12,
                                        strokeWidth: 0,
                                        strokeColor: '#fff',
                                        fillColors: undefined,
                                        radius: 12,
                                        customHTML: undefined,
                                        onClick: undefined,
                                        offsetX: 0,
                                        offsetY: 0
                                    },
                                    itemMargin: {
                                        horizontal: 40,
                                        vertical: 3
                                    },
                                    onItemClick: {
                                        toggleDataSeries: true
                                    },
                                    onItemHover: {
                                        highlightDataSeries: true
                                    },
                                },
                                responsive: optResponsive,
                                states: {
                                    normal: {
                                        filter: {
                                            type: 'none',
                                            value: 0,
                                        }
                                    },
                                    hover: {
                                        filter: {
                                            type: 'lighten',
                                            value: 0.15,
                                        }
                                    },
                                    active: {
                                        allowMultipleDataPointsSelection: false,
                                        filter: {
                                            type: 'darken',
                                            value: 0.35,
                                        }
                                    },
                                },
                            };
                            chartOpt = new ApexCharts(document.querySelector("#chart-opt"), optionsOpt);

                            chartOpt.render();
                            $('#daterange-btn-opt').text(start_date + ' ⇿ ' + finish_date);
                            $('.apex-date-span-opt').html('');
                            break;
                        case 'chart-opt-cancel':
                            chartOptCancel.destroy();
                            $('#chart-opt-cancel').children().remove();
                            var optionsOptCancel = {
                                series: data.opt_value_c,
                                title: {
                                    text: "Cancelled Option Pie",
                                    align: 'center',
                                    margin: 0,
                                    offsetX: 0,
                                    offsetY: 0,
                                    floating: false,
                                    style: {
                                        fontSize: '18px',
                                        fontWeight: 'bold',
                                        fontFamily: undefined,
                                        color: '#04278f'
                                    },
                                },
                                subtitle: {
                                    text: "This Table shown failed options values",
                                    align: 'center',
                                    margin: 15,
                                    offsetX: 0,
                                    offsetY: 25,
                                    floating: false,
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 'normal',
                                        fontFamily: undefined,
                                        color: '#9699a2'
                                    },
                                },
                                chart: {
                                    width: '100%',
                                    type: 'pie',
                                    dropShadow: {
                                        enabled: false,
                                        enabledOnSeries: undefined,
                                        top: 0,
                                        left: 0,
                                        blur: 3,
                                        color: 'rgba(4,41,122,0.94)',
                                        opacity: 0.35
                                    },
                                    animations: {
                                        enabled: true,
                                        easing: 'easeinout',
                                        speed: 2000,
                                        animateGradually: {
                                            enabled: true,
                                            delay: 150
                                        },
                                        dynamicAnimation: {
                                            enabled: true,
                                            speed: 350
                                        }
                                    },
                                },
                                shared: true,
                                legend: {
                                    position: 'bottom',
                                    show: true,
                                    showForSingleSeries: false,
                                    showForNullSeries: true,
                                    showForZeroSeries: true,
                                    horizontalAlign: 'left',
                                    floating: false,
                                    fontSize: '14px',
                                    fontFamily: 'Helvetica, Arial',
                                    fontWeight: 400,
                                    formatter: function (value, ops) {
                                        return ops.w.globals.series[ops.seriesIndex] + ": " + value
                                    },
                                    inverseOrder: false,
                                    width: '80%',
                                    height: 250,
                                    tooltipHoverFormatter: undefined,
                                    customLegendItems: [],
                                    offsetX: 0,
                                    offsetY: 0,
                                    labels: {
                                        colors: undefined,
                                        useSeriesColors: false
                                    },
                                    markers: {
                                        width: 12,
                                        height: 12,
                                        strokeWidth: 0,
                                        strokeColor: '#fff',
                                        fillColors: undefined,
                                        radius: 12,
                                        customHTML: undefined,
                                        onClick: undefined,
                                        offsetX: 0,
                                        offsetY: 0
                                    },
                                    itemMargin: {
                                        horizontal: 50,
                                        vertical: 3
                                    },
                                    onItemClick: {
                                        toggleDataSeries: true
                                    },
                                    onItemHover: {
                                        highlightDataSeries: true
                                    },
                                },
                                labels: data.opt_label_c,
                                responsive: optResponsive,
                                states: {
                                    normal: {
                                        filter: {
                                            type: 'none',
                                            value: 0,
                                        }
                                    },
                                    hover: {
                                        filter: {
                                            type: 'lighten',
                                            value: 0.15,
                                        }
                                    },
                                    active: {
                                        allowMultipleDataPointsSelection: false,
                                        filter: {
                                            type: 'darken',
                                            value: 0.35,
                                        }
                                    },
                                },

                            };

                            chartOptCancel = new ApexCharts(document.querySelector("#chart-opt-cancel"), optionsOptCancel);
                            chartOptCancel.render();
                            $('#daterange-btn-opt-cancel').text(start_date + ' ⇿ ' + finish_date);
                            $('.apex-date-span-opt-cancel').html('');
                            break;
                    }
                }
            });

        }

    </script>
    @elseif($page=='platforms-index')
    <script>
        $(document).ready(function () {
            $('.statusBtn').on('click', function () {
                var t=$(this)
                var status=parseInt(t.attr('name')) ? 0:1;
                console.log('status:'+status);
                var platformID=t.attr('id');
                let token = "{{csrf_token()}}";
                $.ajax({
                    url: '{{url("booking/ajax")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'platform_status',platformID:platformID,platform_status:status, _token: token},
                }).done(function (response) {
                    if(response.saveStatus){
                        if(parseInt(status))
                        {
                            t.css('background-color','green');
                            t.text('Active');
                            t.attr('name','1');
                        }else{
                            t.css('background-color','red');
                            t.text('Passive');
                            t.attr('name','0');

                        }

                    }



                }).fail(function () {
                    console.log("error");
                });
            });
        });
    </script>
    @elseif($page=='mails')
        <link href="{{asset('js/airdatepicker/datepicker.css')}}" rel="stylesheet" type="text/css">
        <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
        <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
        <script>
            let datepickerFrom = $('.datepicker-from').datepicker({
                dateFormat: 'yyyy-mm-dd',
                toggleSelected: false,
                onShow: function() {
                    $('.datepicker--nav').show();
                    $('.-from-bottom-').show();
                    $('.datepicker--nav-title').show();
                    $('.datepicker--nav-action').show();
                    $('.datepicker--pointer').show();
                    $('.datepicker--content').show();
                },
                onSelect: function() {
                    $('.datepicker--nav').hide();
                    $('.-from-bottom-').hide();
                    $('.datepicker--nav-title').hide();
                    $('.datepicker--nav-action').hide();
                    $('.datepicker--pointer').hide();
                    $('.datepicker--content').hide();
                }
            });
            let datepickerTo = $('.datepicker-to').datepicker({
                dateFormat: 'yyyy-mm-dd',
                toggleSelected: false,
                onShow: function() {
                    $('.datepicker--nav').show();
                    $('.-from-bottom-').show();
                    $('.datepicker--nav-title').show();
                    $('.datepicker--nav-action').show();
                    $('.datepicker--pointer').show();
                    $('.datepicker--content').show();
                },
                onSelect: function() {
                    $('.datepicker--nav').hide();
                    $('.-from-bottom-').hide();
                    $('.datepicker--nav-title').hide();
                    $('.datepicker--nav-action').hide();
                    $('.datepicker--pointer').hide();
                    $('.datepicker--content').hide();
                }
            });
        </script>

@endif
<script>
    $(document).ready(function () {
            var location_raw = (window.location.href).replace('#','');
            var location = location_raw.substr(location_raw.length - 1) == "/" ? location_raw.substr(0, location_raw.length - 1) :  location_raw;
            $('.sb2-13 .collapsible li a').each(function () {
                if (location == $(this).attr('href')) {
                    $(this).addClass('menu-active');
                    $(this).closest('.collapsible-body').fadeIn(1000);
                }
            });
    });
</script>
