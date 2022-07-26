<!--========= Scripts ===========-->
<!--Start of Tawk.to Script
<script type="text/javascript">
    let Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function() {
        let s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5d1764e722d70e36c2a35da7/1dvb6fm7i';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>
End of Tawk.to Script-->

<script src="{{asset('/js/jquery-latest.min.js')}}"></script>
<script src="{{asset('/js/main/bootstrap.js')}}" defer></script>
<script src="{{asset('/js/main/wow.min.js')}}" defer></script>
<script src="{{asset('/js/main/materialize.min.js')}}" defer></script>
<script src="{{asset('/js/main/custom.js')}}" defer></script>
<script src="{{asset('/js/main/jquery-ui.js')}}"></script>
<script src="{{asset('/js/main/home-scripts.js')}}" defer></script>
<script src="{{asset('/js/waitme/waitMe.min.js')}}" defer></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<style>

    .pagination > li > a, .pagination > li > span{
        color: #1d6db2;
    }
    .pagination .active {
        line-height: 16.6px;
    }
    .pagination > li > a, .pagination > li > span{
        line-height: 17px;
    }
    .pagination > li > a, .pagination > li > span{
        padding: 8px 16px;
    }
    .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus{
        background-color: #1d6db2!important;
        border-color: #1d6db2!important;
    }
</style>
<script>

    $('#select-search').on('keyup', function() {

        if ($(this).val() === 'hotjar') {
            let currentUrl = window.location.href;
            let url = new URL(currentUrl);
            url.searchParams.set("hotjar", "true"); // setting your param
            let newUrl = url.href;
            window.history.pushState("", "", newUrl);
        }

    });
</script>

<script>
    $(window).on('load', function uniqueIDForCart() {
        let fullUrl = window.location.href;
        if (fullUrl.indexOf('admin') < 0) {
            let u = new Uint32Array(1);
            window.crypto.getRandomValues(u);
            let uniqueID = u.toString(10).toUpperCase();
            $.ajax({
                type: 'GET',
                url: '/uniqueIDForCart',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    uniqueID: uniqueID,
                },
                success: function(data) {
                    //
                }
            });
        }
    });

    $(function() {
        let body = $('body');
        body.on('click', '.currencyCodes', function() {
            let currencyID = $(this).attr('data-cur-code');
            $.ajax({
                type: 'POST',
                url: '/setCurrencyCode',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    currencyID: currencyID,
                },
                success: function(data) {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        Materialize.toast(data.error, 4000, 'toast-alert');
                    }
                }
            });
        });

        body.on('click', '.languageCodes', function() {
            let lang = $(this).attr('data-lang-code');
            let isProductPage = $('.isProductPage').val();
            let isAttractionPage = $('.isAttractionPage').val();
            let isBlogDetailPage = $('.isBlogDetailPage').val();
            let pathName = '';
            if (!isProductPage) {
                pathName = window.location.pathname;
            } else {
                pathName = window.location.pathname;
                pathName = pathName.split('/');
                pathName = pathName.slice(-2);
                pathName = pathName.join('/');
            }
            let sessionLocale = $('#sessionLocale').val();
            $.ajax({
                type: 'GET',
                url: '/setLocale',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    lang: lang,
                    isProductPage: isProductPage,
                    isAttractionPage: isAttractionPage,
                    isBlogDetailPage: isBlogDetailPage,
                    currentUrl: pathName
                },
                success: function(data) {
                    if (data.success) {

                        if(data.isErrorPage){
                            window.location.href = window.location.origin +"/"+ data.url;

                        }



                        if (!isProductPage) {
                            if (lang !== 'en') {
                                let newUrl = '';
                                if (pathName === '/') {
                                    newUrl = pathName + lang;
                                } else {
                                    if (sessionLocale === 'en') {
                                        newUrl = '/' + lang + pathName;
                                    } else {
                                        newUrl = pathName.replace(sessionLocale, lang);
                                    }
                                }
                                if (data.isRedirectable) {
                                    window.location.href = window.location.origin + '/' + lang + '/' + data.url;
                                } else {
                                    window.location.href = window.location.origin + newUrl;
                                }
                            } else {
                                if (data.isRedirectable) {
                                    window.location.href = window.location.origin + '/' + data.url;
                                } else {
                                    let newUrl = pathName.replace($('#sessionLocale').val(), '').replace('/', '');
                                    window.location.href = window.location.origin + newUrl;
                                }
                            }
                        } else {
                            if (lang !== 'en') {
                                window.location.href = window.location.origin + '/' + lang + '/' + data.url;
                            } else {
                                window.location.href = window.location.origin + '/' + data.url;
                            }
                        }
                    }
                }
            });
        });

        $('#loginSubmitButton').on('click', function(e) {
            e.preventDefault();
            let password = $('#password').val();
            let email = $('#email').val();
            let guard = $("input[name='guard']:checked").val();
            let loginModalFormAlertDiv = $('#loginModalFormAlertDiv');
            $.ajax({
                type: 'POST',
                url: '/validateLogin',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    guard: guard,
                    password: password,
                    email: email
                },
                success: function(data) {
                    if (data.success) {
                        $('#loginModalForm').submit();
                    } else {
                        loginModalFormAlertDiv.html(data.error);
                        loginModalFormAlertDiv.show();
                    }
                }
            });
        });










        $('.datepicker-from').datepicker({
            dateFormat: 'dd/mm/yy',
            autoClose: true,
            minDate: moment().toDate(),
            position: 'bottom right'
        });

        $('.datepicker-to').datepicker({
            dateFormat: 'dd/mm/yy',
            autoClose: true,
            minDate: moment().toDate(),
            position: 'bottom right'
        });

        $('.searchInput').on('focus, click', function() {
            let suggestionsContainer = $(this).closest(".input-field").find('.suggestions-container');
            let suggestionItem = suggestionsContainer.find('.suggestion-item');
            if (suggestionItem.length > 0) {
                suggestionsContainer.show();
            }
        });


        $('.search-field').on('keyup', function(e) {
            let keyValues = [37, 38, 39, 40];
            let which = e.which;
            let width = screen.width;
            $('#suggestionIndex').val('-1');
            let value = $(this).val();
            let suggestionsContainer = width > 520 ? $(this).parent().find(".suggestions-container") : $(this).parent().find(".mobile-suggestions-container");
            fillBody(suggestionsContainer, value)
        });

        function fillBody (suggestionsContainer, val) {

            $.ajax({
                type: 'POST',
                url: '/searchVarious',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    value: val
                },
                success: function(response) {

                    let block = '';
                    suggestionsContainer.html(block);

                    response.data.forEach(function (item) {
                        if(item.searchableModel === 'Product' || item.searchableModel === 'Attraction'){
                            block += `<a href="${item.url}" class="suggestion-item">
                                                <div class="suggestion-icon"  style="background: url('${item.cover}') center center no-repeat;background-size:cover"></div>
                                                ${item.searchableTitle}
                                            </a>`
                        }else {
                            block += `<a class="suggestion-item" href="/s?q=${item.searchableTitle}&m=${item.searchableModel}">
                                                <div class="icon-cz-location-1 suggestion-icon"></div>
                                                ${item.searchableTitle}
                                            </a>`
                        }

                    })

                    suggestionsContainer.append(block);
                    suggestionsContainer.show()
                },
                error: function (response){
                    suggestionsContainer.html(`<b style="display: block; text-align: center">${response.responseJSON.message}</b>`)
                    suggestionsContainer.show()
                }
            });
        }

        $('#mobile-search-button').on('click', function (){
            $('.mobile-search-overlay').addClass('active-overlay')
            $('.mobile-search-overlay .mobile-search-area input').focus()
            $('body').css({
                overflow: 'hidden'
            })
        })

        $('.mobile-search-overlay .mobile-search-area input').on('keyup', function() {
            if($(this).val().length > 0){
                $(this).parent().find('.clear-input').css({
                    display: 'flex'
                })
            }else{
                $(this).parent().find('.clear-input').css({
                    display: 'none'
                })
                $('.mobile-suggestions-container').html('')
            }
        })

        $('.clear-input').on('click', function () {
            $('.mobile-search-overlay .mobile-search-area input').val('')
            $('.mobile-search-overlay .mobile-search-area input').focus()
            $(this).css({
                display: 'none'
            })
            $('.mobile-suggestions-container').html('')
        })



        $('.overlay-close').on('click', function () {
            $('.mobile-search-overlay').removeClass('active-overlay')
            $('body').css({
                overflow: 'visible'
            })
        })


        $(document).on('keyup', function(e) {
            let suggestionIndex = $('#suggestionIndex');
            let suggestionsContainer = $('.suggestions-container');
            if (suggestionsContainer) {
                switch(e.which) {
                    case 38: // up
                        if (suggestionIndex.val() !== '-1' && suggestionIndex.val() !== '0') {
                            upDownOperations(-1);
                        }
                        break;
                    case 40: // down
                        let items = $('.suggestion-item');
                        if (parseInt(suggestionIndex.val()) + 1 !== items.length) {
                            upDownOperations(+1);
                        }
                        break;
                    default: return;
                }
            }
            e.preventDefault();
        });

        function upDownOperations(iterator) {
            let suggestionIndex = $('#suggestionIndex');
            let items = $('.suggestion-item');
            let nextEl = items.eq(parseInt(suggestionIndex.val()) + iterator);
            nextEl.css('background-color', '#1593ff');
            nextEl.css('color', '#fff');
            $('#select-search').val(nextEl.attr('data-value'));
            $('#searchType').val(nextEl.attr('data-type'));
            if (nextEl.attr('data-type') === 'product') {
                $('#productUrl').val(nextEl.attr('data-url'));
            }
            let lastEl = items.eq(parseInt(suggestionIndex.val()));
            lastEl.css('background-color', '#fff');
            lastEl.css('color', '#333');
            suggestionIndex.val(parseInt(suggestionIndex.val()) + iterator);
        }

        $(document).on('click', function(e) {
            let container = $('.suggestions-container');
            let searchInput = $('#select-search');
            let searchInputAnother = $('#select-search-another');
            if ((!container.is(e.target) && container.has(e.target).length === 0) && (!searchInput.is(e.target) && searchInput.has(e.target).length === 0) && (!searchInputAnother.is(e.target) && searchInputAnother.has(e.target).length === 0)) {
                container.hide();
            }
        });

        $('body').on('click', '.suggestion-item', function() {
            $(this).parent().parent().find('.search-button').attr('disabled', 'disabled')
        });

        $('.tour-form-one').on('submit', function(e) {
            e.preventDefault();

            let search = $(this).find('.searchInput');
            if (search.val() === '') {
                search.addClass('searchInputNotValid');
                setTimeout(setInputValid, 500);
            } else {
                let dataType = $('#searchType').val();
                if (dataType !== 'product') {
                    $('.tour-form-one')[0].submit();
                } else {
                    window.location.href = $('#productUrl').val();
                }
            }
        });


        $('.tour-form-two').on('submit', function(e) {
            e.preventDefault();

            let search = $(this).find('.searchInput');
            if (search.val() === '') {
                search.addClass('searchInputNotValid');
                setTimeout(setInputValid, 500);
            } else {
                let dataType = $('#searchType').val();
                if (dataType !== 'product') {
                    $('.tour-form-two')[0].submit();
                } else {
                    window.location.href = $('#productUrl').val();
                }
            }
        });

        let setInputValid = function() {
            $('.searchInput').removeClass('searchInputNotValid');
        };















    });
</script>

@if ($page == 'product')
    <script type="text/javascript" src="{{asset('js/sticky-sidebar/ResizeSensor.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/sticky-sidebar/theia-sticky-sidebar.js')}}" defer></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.min.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.de.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.en.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.es.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.fr.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.it.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.nl.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.pt.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/airdatepicker/datepicker.tr.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/main/product-scripts.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/main/common-scripts.js')}}" defer></script>
    <script type="text/javascript" src="{{asset('js/lightbox.min.js')}}" defer></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">// <![CDATA[
        function ShowHide(divId)
        {
            if(document.getElementById(divId).style.display == 'none')
            {
                document.getElementById(divId).style.display='block';
            }
            else
            {
                document.getElementById(divId).style.display = 'none';
            }
        }
        // ]]></script>
    <script>
        $(document).ready(function(){

            $('.optionMovable:first').addClass('movable-active');
            $("#know-before-you-go-wrap p").each(function(index, el) {
                if(index  > 3){
                    $(this).css("display", "none");
                    $(this).addClass("read-more-p");
                }

            });

            if($("#know-before-you-go-wrap p").length > 4 && $("#know-before-you-go-read-more").length == 0){
                $("#know-before-you-go-wrap").append("<b id='know-before-you-go-read-more' data-step='read' style='text-align:center; display:block; width:100px; background-color:#1d6db212; color:#000; cursor:pointer; padding:0 10px 0 10px; margin-bottom: 20px;'> {{__('Read More...')}} </b>");
            }else{
                $("#know-before-you-go-read-more").attr("data-step", "read");

            }



            // know before you go read more if row greater than 3

            function makeReadMoreForKnowBeforeYouGo(){

                $("#know-before-you-go-wrap p.read-more-p").slideToggle(200, function(){
                    $("#know-before-you-go-read-more").attr("data-step", "read");
                    $("#know-before-you-go-read-more").text("{{__('Read More...')}}");
                });


            }



            function notMakeReadMoreForKnowBeforeYouGo(){
                $("#know-before-you-go-wrap p.read-more-p").slideToggle(200, function(){
                    $("#know-before-you-go-read-more").attr("data-step", "hide");
                    $("#know-before-you-go-read-more").text("{{__('Hide')}}");
                });
            }

            $(document).on('click', '#know-before-you-go-read-more', function(event) {
                event.preventDefault();
                var step = $(this).attr("data-step");

                if(step == 'read'){
                    notMakeReadMoreForKnowBeforeYouGo();
                }else{
                    makeReadMoreForKnowBeforeYouGo();
                }

            });






            var maxLength = 500;
            $(".show-read-more").each(function(){
                var myStr = $(this).text();
                if($.trim(myStr).length > maxLength){
                    var newStr = myStr.substring(0, maxLength);
                    var removedStr = myStr.substring(maxLength, $.trim(myStr).length);
                    $(this).empty().html(newStr);
                    $(this).append(' <a href="javascript:void(0);" class="read-more">read more...</a>');
                    $(this).append('<span class="more-text">' + removedStr + '</span>');
                }
            });
            $(".read-more").click(function(){
                $(this).siblings(".more-text").contents().unwrap();
                $(this).remove();
            });
        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr', {action: 'register'}).then(function(token) {
                $('#recaptchaToken').val(token);
                $('#recaptchaAction').val('register');
            });
        });
    </script>

    <script>
        $(function() {

            $(".product-right-book button").click(function(){

                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#box_style_1").offset().top +100
                }, 1200);


            });

            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true
            });

            $(".all-images").click(function(event) {
                $(".gallery figure").eq(0).find("a").click();

            });

            $(".commentDisplay").slice(0, 4).show(); // select the first 4

            $("#load").click(function(e) { // click event for load more
                e.preventDefault();
                $(".commentDisplay:hidden").slice(0, 4).show(); // select next 4 hidden divs and show them
            });

            $('#sendComment').on('click', function() {
                if ($('input[name="rating"]:checked').val() === undefined) {
                    $('#ratingErrorSpan').show();
                }
            });

            $('input[name="rating"]').on('click', function() {
                $('#sendComment').removeAttr('type');
                $('#sendComment').attr('type', 'submit');
            });
        });
    </script>
    <script>
        document.getElementById('book-focus').onclick = function() {
            /*document.getElementById('sidebar').scrollIntoView();*/
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#box_style_1").offset().top +200
            }, 1200);
        };
    </script>


@elseif($page == 'cart')
    <script src="{{asset('js/main/cart-scripts.js')}}" defer></script>
@elseif($page == 'profile')
    <script src="{{asset('js/admin/jquery.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/jquery.dataTables.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/dataTables.bootstrap.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/buttons.bootstrap.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/buttons.flash.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/buttons.html5.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/buttons.print.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/dataTables.scroller.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/dataTables.buttons.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/jszip.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/pdfmake.min.js')}}" defer></script>
    <script src="{{asset('js/datatables/vfs_fonts.js')}}" defer></script>
    <script src="{{asset('js/main/profile-scripts.js')}}" defer></script>
    <script>
        $(document).ready(function () {
            $('body').on('click', '[data-booking]', function () {
                if(confirm('{{ __('cancelByUser') }}')){
                    $.ajax({
                        url: '/cancel-by-user',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).data('booking')
                        },
                        success: function (response) {
                            alert(response.message);
                            setTimeout(() => {
                                location.reload()
                            }, 1300)
                        },
                        error: function (error) {
                            console.log(error)
                        }
                    })
                }
            })
        })
    </script>
@elseif($page == 'commissions')
    <script>
        $(document).ready(function() {
            $("[data-toggle='tootip']").tooltip();

            $("thead").click(function(event) {
                if(!$(this).next("tbody").is(':visible'))
                    $(this).next("tbody").show(400);
                else
                    $(this).next("tbody").hide(200);
            });
        });
    </script>

@elseif($page == 'home')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
    <script>

        $('#nav-tabContent div:first').addClass('active in');

        $('#nav-tab a:first').addClass('active');

        $(".nav .nav-link").on("click", function() {
            $(".nav").find(".active").removeClass("active");
            $(this).addClass("active");
        });




        $(document).on('click','.add-to-wishlist', function() {
            let $this = $(this);
            let productID = $this.attr("data-product-id");
            let wishlistType = $this.attr('data-type');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/addRemoveWishlist',
                data: {
                    productID: productID,
                    wishlistType: wishlistType
                },
                success: function(data) {
                    if (!data.isLoggedIn) {
                        Materialize.toast(data.success, 4000, 'toast-alert');
                        return;
                    }
                    Materialize.toast(data.success, 4000, 'toast-success');
                    if (wishlistType === 'add') {
                        $this.css("color","#ff0000");
                        $this.attr('data-type', 'remove');
                    } else {
                        $this.css("color","#fff");
                        $this.attr('data-type', 'add');
                    }
                }
            });
        });



    </script>
@elseif(in_array($page, ['all-products', 'attractions']))
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
    <script>
        $(window).on('load', function() {
            // console.log('burda')
            makeFilterCall();
        });

        $('.datepicker-from').datepicker({
            dateFormat: 'dd/mm/yy',
            autoClose: true,
            minDate: moment().toDate()
        });

        $('.datepicker-to').datepicker({
            dateFormat: 'dd/mm/yy',
            autoClose: true,
            minDate: moment().toDate()
        });

        $('#checkAvailability').on('click', function() {
            $('#productsDiv').hide();
            $('#productsDiv').html('');
            $('#loadingDiv').show();
            makeFilterCall();
        });

        function fillProducts(products) {
            let block = '';
            let langCode = $('#sessionLocale').val();
            let langCodeForUrl = langCode === 'en' ? '' : '/' + langCode;
            let i = 0;
            products.forEach(function(product) {
                i = i+1;
                let item = product.item;
                let productTranslation = product.productTranslation;
                let misc = product.misc;
                block += '<div class="hot-page2-alp-r-list">';
                block += '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 hot-page2-alp-r-list-re-sp">';
                if (productTranslation) {
                    block += '<a href="'+langCodeForUrl+'/'+productTranslation.url+'">';
                } else {
                    block += '<a href="'+langCodeForUrl+'/'+item.url+'">';
                }
                if (misc.offerPercentage !== 0) {
                    block += '<div class="band2">';
                    block += '<div class="ribbon ribbon--orange" style="margin-top: 0px">% '+Math.round(misc.offerPercentage)+'</div>';
                    block += '</div>';
                }
                block += '<div class="hot-page2-hli-1" style="border-radius: 10px;">';
                block += '<img ';
                if (misc.isThereCoverPhoto) {
                    block += 'src="{{Storage::disk('s3')->url('product-images-xs/')}}'+misc.coverPhoto+'"';
                } else {
                    block += 'src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"';
                }
                block += ' alt="" style="border-radius: 5%;padding: 5%;min-height: 155px;max-height: 185px;">';
                block += '</div>';
                block += '</a>';
                block += '</div>';
                block += '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="border-right: 1px solid #dedede;">';
                block += '<div class="trav-list-bod">';
                if (productTranslation) {
                    block += '<a href="'+langCodeForUrl+'/'+productTranslation.url+'"><h2 style="font-size: 17px;">';
                    block += productTranslation.title;
                } else {
                    block += '<a href="'+langCodeForUrl+'/'+item.url+'"><h2 style="font-size: 17px;">';
                    block += item.title;
                }
                block += '</h2></a>';
                block += '<div class="dir-rat-star" style="font-size: 15px;">';
                block += '<div class="rating" style="direction: ltr;width:100%;">';
                if (item.rate) {
                    let $rate = item.rate;


                    if((parseInt($rate) + 0.5) >= $rate && parseInt($rate) != $rate){

                        for (let $i=1; $i <= 5; $i++) {
                            if(($i < $rate)){
                                // tam yıldız
                                block+= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';

                            }else if(($i > $rate) && (Math.ceil($rate) == $i)){
                                block+= '<i class="icon-cz-star-half" style="color: #ffad0c; font-size: 15px;"></i>';
                                // yarım yıldız
                            }
                            else{
                                block+= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
                                // boş yıldız
                            }
                        }

                    }else if((parseInt($rate) + 0.5) < $rate){

                        for (let $i=1; $i <= 5; $i++) {

                            if($i <= Math.ceil($rate)){
                                block+= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';
                            }else{
                                block+= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
                            }

                        }

                    }else{

                        for (let $i=1; $i <= 5; $i++) {
                            if($rate >= $i){
                                block+= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';
                            }else{
                                block+= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
                            }
                        }

                    }

                    block += '<label style="font-size: 13px;vertical-align: text-bottom; float:right;color: #1A2B50; padding-left: 3px;">'+$rate+'/5 </label>';


                    /*   for (let i=0; i<item.rate; i++) {
                           block += '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';
                       }
                       block += '<label style="font-size: 13px;vertical-align: text-bottom; float:left;color: #1A2B50;">'+(rate)+'/'+5+' |</label>';*/
                } else {
                    block += '<div style="font-size: 13px;vertical-align: text-bottom;">No reviews yet</div>';
                }
                block += '</div>';
                block += '</div>';
                block += '<div>';
                if (productTranslation) {
                    block += productTranslation.shortDesc.substr(0, 180);
                } else {
                    block += item.shortDesc.substr(0, 180);
                }
                block += '...</div>';
                block += '</div>';
                block += '</div>';
                block += '<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">';
                block += '<div class="hot-page2-alp-ri-p3 tour-alp-ri-p3">';
                block += '<span class="hot-list-p3-1">Prices Starting</span>';
                block += '<span class="hot-list-p3-2" style="font-size: 17px;">';
                if (misc.isCommissioner) {
                    if (misc.specialOffer !== 0) {
                        block += '<span class="special-offer-price" style="font-size: 17px;"><i class="'+misc.currencyIcon+'"></i>'+misc.specialOfferPrice+'</span><br>';
                        block += '<span class="strikeout" style="font-size: 17px;"><i class="'+misc.currencyIcon+'"></i>'+misc.normalPrice+'</span>';
                    } else {
                        block += '<i class="'+misc.currencyIcon+'"></i>' + misc.normalPrice;
                    }
                    block += '<i class="'+misc.currencyIcon+'"></i>'+misc.commissionerEarns+' COM';
                } else {
                    if (misc.specialOffer !== 0) {
                        block += '<span class="special-offer-price" style="font-size: 17px;"><i class="'+misc.currencyIcon+'"></i>'+misc.specialOfferPrice+'</span>';
                        block += '<span class="strikeout" style="font-size: 17px;"><i class="'+misc.currencyIcon+'"></i>'+misc.normalPrice+'</span>';
                    } else {
                        block += '<span style="font-size: 18px;"><i class="'+misc.currencyIcon+'"></i>'+misc.normalPrice+'</span>';
                    }
                }
                block += '</span>';
                block += '<span class="hot-list-p3-4">';
                if (productTranslation) {
                    block += '<a id="'+i+'" href="'+langCodeForUrl+'/'+productTranslation.url+'" class="hot-page2-alp-quot-btn">Book Now</a>';
                } else {
                    block += '<a id="'+i+'" href="'+langCodeForUrl+'/'+item.url+'" class="hot-page2-alp-quot-btn">Book Now</a>';
                }
                block += '</span>';
                block += '</div>';
                block += '</div>';
                block += '</div>';
            });
            return block;
        }

        function checkboxOperations($this, attributeName, hiddenId) {
            let oldValue = $this.val();
            $this.val(oldValue === '0' ? '1' : '0');
            let filterName = $this.attr(attributeName);
            let filters = $(hiddenId).val();
            if (oldValue === '0') {
                if (filters !== '') {
                    $(hiddenId).val(filters + ' | '  + filterName);
                } else {
                    $(hiddenId).val(filterName);
                }
            } else {
                let filtersAsArr = filters.split(' | ');
                let filterLeft = filtersAsArr.filter(c => c !== filterName);
                filterLeft = filterLeft.join(' | ');
                $(hiddenId).val(filterLeft);
            }
            $('#productsDiv').hide();
            $('#productsDiv').html('');
            $('#loadingDiv').show();
        }

        $('.categoriesCheckBox').on('click', function() {
            checkboxOperations($(this), 'data-cat-name', '#categories');
            makeFilterCall();
        });

        $('.attractionsCheckBox').on('click', function() {
            checkboxOperations($(this), 'data-attraction-id', '#attractions');
            makeFilterCall();
        });

        $('.pricesCheckBox').on('click', function() {
            checkboxOperations($(this), 'data-price', '#prices');
            makeFilterCall();
        });

        function changePage(page)
        {
            $('#page').val(page);
            $('#productsDiv').hide();
            $('#productsDiv').html('');
            $('#loadingDiv').show();
            makeFilterCall();
        }

        function makeFilterCall() {
            let q = $('#q').val();
            let searchDateFrom = $('#searchDateFrom').val();
            let searchDateTo = $('#searchDateTo').val();
            let searchType = $('#searchType').val();
            let from = $('.datepicker-from').val();
            let to = $('.datepicker-to').val();
            let fromFormatted = '';
            let toFormatted = '';
            let page = $('#page').val();
            if (from !== '' && to !== '') {
                fromFormatted = moment(from, 'DD/MM/YYYY').format('YYYY-MM-DD');
                toFormatted = moment(to, 'DD/MM/YYYY').format('YYYY-MM-DD');
            }
            let categories = $('#categories').val();
            let attractions = $('#attractions').val();
            let prices = $('#prices').val();
            let sortType = $('#selectedSortType').val();
            $.ajax({
                type: 'POST',
                url: '/all-products/checkAvailability',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    from: fromFormatted,
                    to: toFormatted,
                    categories: categories,
                    attractions: attractions,
                    page: page,
                    prices: prices,
                    sortType: sortType,
                    q: q,
                    searchDateFrom: searchDateFrom,
                    searchDateTo: searchDateTo,
                    searchType: searchType
                },
                success: function(data) {
                    if (data.successful) {
                        let products = data.products;
                        let block = fillProducts(products);
                        $('#productsDiv').append(block);
                        $('#loadingDiv').hide();
                        $('#productsDiv').show();
                        $('#productCount').html(data.totalProduct);
                        $('#paginator').html(data.paginator);
                    }
                    if (data.failed) {
                        $('#loadingDiv').hide();
                        let attractions = data.attractions;
                        let block = '';
                        block += '<div class="col-md-12"><label style="font-size: 14px;" class="label label-danger">'+ data.failed +'</label></div>';
                        block += '<div class="text-center col-md-12">';
                        attractions.forEach(function(attraction) {
                            if (attraction.isActive === 1) {
                                block += '<a href="'+attraction.onFailUrl+'"><div style="padding-left: 10px;padding-right: 10px;margin-top: 10px;" class="col-md-4"><div style="border: 1px solid #7a8088"><div style="width:100%; height:auto"><img style="width:100%;height:250px;object-fit: cover;" src="{{Storage::disk('s3')->url('attraction-images/')}}'+attraction.image+'" alt="'+attraction.onFailName+'"></div><h5>'+attraction.onFailName+'</h4></div></div>';
                            }
                        });
                        block += '</div></a>';
                        $('#productsDiv').append(block);
                        $('#productsDiv').show();
                        $('#paginator').html(data);
                    }
                }
            });
        }

        $('.sortType').on('click', function() {
            $('#productsDiv').hide();
            $('#productsDiv').html('');
            $('#loadingDiv').show();
            let sort = $(this).attr('data-sort');
            $('#selectedSortType').val(sort);
            let buttonHtml = {
                'recommended': 'Recommended &#9660;',
                'priceAsc': 'Price (Low to High) &#9660;',
                'priceDesc': 'Price (High to Low) &#9660;',
                'ratingDesc': 'Rating (High to Low) &#9660;',
            };
            $('.sortButton').html(buttonHtml[sort]);
            makeFilterCall();
        });

    </script>
@elseif($page == 'wishlists')
    <script src="{{asset('js/main/common-scripts.js')}}" defer></script>

@elseif ($page == 'attractions')



@elseif ($page == 'paginate-attractions')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>


    <script type="text/javascript">

        $(document).ready(function() {




            setTimeout(function () {
                var kelle = $('.select-wrapper');// $('.select-wrapper');
                $.each(kelle, function (i, t) {
                    t.addEventListener('click', e => e.stopPropagation());
                });
            }, 500)
            $('.mdb-select').material_select();



            $('.datepicker-from').datepicker({
                dateFormat: 'dd/mm/yyyy',
                autoClose: true,
                minDate: moment().toDate()
            });

            $('.datepicker-to').datepicker({
                dateFormat: 'dd/mm/yyyy',
                autoClose: true,
                minDate: moment().toDate()
            });


            $(document).on('change', 'select[name="sort"]', function(event) {
                event.preventDefault();
                $("#filter-form").submit();
            });

            $(document).on('change', 'select.form-check-input', function(event) {
                event.preventDefault();
                $("#filter-form").submit();
            });


            $(document).on('click', '#reset-form', function(event) {
                event.preventDefault();


                $(".form-check-input").prop("checked", false);
                $('select[name="sort"]').prop("selected", false);
                $('select[name="sort"]').val("");
                $('input[name="from_date"]').val("");
                $('input[name="to_date"]').val("");

                var documentURL = document.URL;
                var mainURL = documentURL.split('?')[0];
                console.log(mainURL);
                window.location = mainURL;

            });

        });
    </script>


@elseif($page == 'faq')
    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>
@elseif($page == 'checkout')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js" defer></script>

    <script>
        $("#checkoutEmail").on('focus', function(event) {
            event.preventDefault();
            $(this).removeAttr("style");
        });


        $('#placeOrder').on('click', function() {

            $(this).waitMe({
                effect : 'bounce',
                text : '',
                bg : 'rgba(255,255,255,0.7)',
                color : "#0e76a8",
                maxSize : 30,
                waitTime : -1,
                textPos : 'vertical',
                fontSize : '',
                source : '',
                onClose : function() {}
            })

            $(this).attr('disabled', 'disabled')

            let countryCode = $('#countryCode').val();
            if (countryCode === null || typeof countryCode === 'undefined' || countryCode === '') {
                Materialize.toast('Please select your country code before payment!', 3000, 'toast-alert');
                $('.select2').addClass('country-code-empty-border');
                $(this).waitMe('hide')
                $(this).attr('disabled', false)
                return false;
            }
            if ($('#checkoutEmail').val() != $('#checkoutEmail2').val()) {
                $(this).waitMe('hide')
                $(this).attr('disabled', false)
                Materialize.toast('E-Mail fields must match!', 3000, 'toast-alert');
                return false;
            }

            @php
                $isAdmin = auth()->check() && !is_null(auth()->guard('web')->user()->ccEmail) && auth()->guard('web')->user()->commission == null;
            @endphp
            @if($isAdmin || (auth()->guard('web')->check() && auth()->guard('web')->user()->id == 21) || (auth()->guard('web')->check() && auth()->guard('web')->user()->id == 466))
            let platform = $('#platformID').val();
            if(!platform || platform == "") {
                $(this).waitMe('hide')
                $(this).attr('disabled', false)
                Materialize.toast('Platform field is required!', 3000, 'toast-alert');
                return false;
            }
            @endif

            $(this).attr('disabled', false)

            let name = $('#firstName').val();
            let surname = $('#lastName').val();
            let email = $('#checkoutEmail').val();
            let country_code = $('#countryCode').val();
            let phone_number = $('#phone').val();
            let client_id = $('#clientid').val();
            // $.ajax({
            //     type: 'POST',
            //     url: '/booking/record',
            //     data: {
            //         _token: $('meta[name="csrf-token"]').attr('content'),
            //         name: name,
            //         surname: surname,
            //         email: email,
            //         country_code: country_code,
            //         phone_number: phone_number,
            //         client_id: client_id
            //     }
            // });
        });
    </script>
    <script>
        $(function() {
            $("#countryCode").select2({
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

    @if(session()->get('userLanguage') != 'en')
        <script>
            var sgment = '/{{session()->get('userLanguage')}}/bookit';
        </script>
    @else

        <script>
            var sgment = '/bookit';
        </script>

    @endif

    <script>
        $('#comissionRadio').on('click', function() {
            let comissionRadio = $('#comissionRadio');
            let creditCardRadio = $('#creditCardRadio');
            comissionRadio.val('1');
            comissionRadio.attr('checked', 'checked');
            creditCardRadio.val('0');
            creditCardRadio.removeAttr('checked');
            let url = window.location.origin;
            $('#checkoutForm').attr('action', url + sgment);
        });

        $('#creditCardRadio').on('click', function() {
            let comissionRadio = $('#comissionRadio');
            let creditCardRadio = $('#creditCardRadio');
            creditCardRadio.val('1');
            creditCardRadio.attr('checked', 'checked');
            comissionRadio.val('0');
            comissionRadio.removeAttr('checked');
            let url = window.location.origin;
            $('#checkoutForm').attr('action', url + '/credit-card-details');
        });

        $('#wantCoupon').on('click', function() {
            $('#couponDiv').fadeIn();
        });

        $('#couponUseIt').on('click', function(data) {
            let couponCode = $('#couponInput').val();
            let amountOfPayment = $('#amountOfPayment');
            let totalPrice = $('#totalPrice');
            let totalDiscount = $('#totalDiscount');
            let usedCouponsDiv = $('#usedCouponsDiv');
            let usedCoupons = $('#usedCoupons');
            let couponIDHidden = $('#couponIDHidden');
            $.ajax({
                type: 'POST',
                url: '/useCoupon',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    couponCode: couponCode,
                },
                success: function(data) {
                    if(data.couponResponse) {
                        console.log(data.couponResponse);
                        if(data.couponResponse.indexOf(true) !== -1){
                            Materialize.toast('The coupon discount is invalid because the current discount is more than the coupon discount!', 4000, 'toast-alert');
                            return false;
                        }
                    }
                    let coupon = data.coupon;
                    if (data.failed || (data.user === null && coupon.type === 4) ||
                        (data.country !== data.lastSelect  && coupon.type === 2) ||
                        (data.attractions.includes(String(data.lastSelect)) === false && coupon.type === 3) ||
                        ((data.productID !== coupon.productID || data.optionID !== data.lastSelect) && coupon.type === 1)) {
                        Materialize.toast(data.failed, 4000, 'toast-alert');
                    } else {
                        Materialize.toast(data.success, 4000, 'toast-success');
                        if (coupon.discountType === 'percent') {
                            amountOfPayment.html((parseFloat(totalPrice.val()) - (parseFloat(totalPrice.val()) * parseFloat(coupon.discount) / 100)).toFixed(2));
                            totalDiscount.html((parseFloat(totalPrice.val()) * parseFloat(coupon.discount) / 100).toFixed(2));
                            $('#amount').val((parseFloat(totalPrice.val()) - (parseFloat(totalPrice.val()) * parseFloat(coupon.discount) / 100)).toFixed(2));
                            totalPrice.val((parseFloat(totalPrice.val()) - (parseFloat(totalPrice.val()) * parseFloat(coupon.discount) / 100)).toFixed(2));
                        } else if (coupon.discountType === 'net rate' && coupon.discount < totalPrice) {
                            amountOfPayment.html((parseFloat(totalPrice.val()) - parseFloat(coupon.discount)).toFixed(2));
                            totalDiscount.html(coupon.discount);
                            $('#amount').val((parseFloat(totalPrice.val()) - parseFloat(coupon.discount)).toFixed(2));
                            totalPrice.val((parseFloat(totalPrice.val()) - parseFloat(coupon.discount)).toFixed(2));
                        }
                        usedCouponsDiv.show();
                        usedCoupons.show();
                        usedCoupons.text(coupon.couponCode);
                        usedCoupons.attr('data-id', coupon.id);
                        couponIDHidden.val(coupon.id);
                    }
                },
                error: function(data) {
                    Materialize.toast(data.failed, 4000, 'toast-alert');
                }
            });
        });

        $('#deleteCoupon').on('click', function() {
            let couponID = $('#usedCoupons').attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/deleteUsedCoupon',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    couponID: couponID,
                },
                success: function(data) {
                    $('#usedCoupons').html('');
                    $('#usedCoupons').parent().parent().hide();
                    $('#totalPrice').val({{ $totalPriceWOSO }});
                    $('#totalDiscount').html('{{ isset($discount) ? $discount : 0 }}');
                    $('#amountOfPayment').html('{{ $totalPrice }}');
                    $('#amount').val(parseFloat($('#amountOfPayment').text()));
                }
            });
        });

    </script>
    <script>
        $(document).ready(function() {
            let translationArray = JSON.parse($('#translationArray').val());

            let amount = $('#amount');
            $('.totalCommission').on('change', function() {
                let cartID = $(this).attr('data-id');
                let amountOfPaymentDiv = $(this).parent().parent().parent().parent().find('td .amountOfPayment');
                let maxCommission = $(this).attr('max');
                let totalPrice = $(this).parent().parent().parent().parent().find('.totalPrice').val();
                let totalCommission = $(this).val();
                let totalPriceForAllCart = 0;
                amountOfPaymentDiv.val('');
                amountOfPaymentDiv.val(totalPrice);
                amountOfPaymentDiv.val((totalPrice - (maxCommission - totalCommission)).toFixed(2));
                let a = $('input[name="totalCommission"]');
                let b = ($('.amountOfPayment'));
                let totalCommissionForAllCart = 0;

                if (totalCommission < 0) {
                    Materialize.toast(translationArray['checkYourCommission0'], 4000, 'toast-alert');
                    $(this).val(0);
                    amountOfPaymentDiv.val('');
                    amountOfPaymentDiv.val((totalPrice - (maxCommission)).toFixed(2));
                }

                if ((parseFloat(maxCommission) < parseFloat(totalCommission))) {
                    Materialize.toast(translationArray['checkYourCommission1']+maxCommission, 4000, 'toast-alert');
                    $(this).val(maxCommission);
                    amountOfPaymentDiv.val('');
                    amountOfPaymentDiv.val(totalPrice);
                }

                a.each(function(index, item) {totalCommissionForAllCart += parseFloat(item.value);});
                b.each(function(index, item) {totalPriceForAllCart += parseFloat(item.value);});

                $('#totalCommissionForAllCart').val((totalCommissionForAllCart).toFixed(2));
                $('#totalPriceForAllCart').val((totalPriceForAllCart).toFixed(2));

                amount.val(totalPriceForAllCart);

                $.ajax({
                    type: 'POST',
                    url: '/checkout/newValuesForCart',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        cartID: cartID,
                        totalCommission: totalCommission,
                        totalPrice: amountOfPaymentDiv.val(),
                    }
                });

            });

            $('#firstName, #lastName, #checkoutEmail, #hotel, #phone, #comment, #country, #city, #streetLine').on('keyup', function() {
                let name = $(this).attr('id');
                let value = $(this).val();
                if (name !== 'phone') {
                    $('#' + name + 'Hidden').val(value);
                } else {
                    $('#' + name + 'Hidden').val($('#countryCode').val() + value);
                }
            });

            $('#shareCartButton').on('click', function(e) {
                e.preventDefault();
                $('#shareInputs').show();
            });

            $('#emailCheck, #whatsappCheck').on('click', function() {
                let oldVal = $(this).val();
                $(this).val(oldVal === '1' ? '0' : '1');
            });

            $('#shareButton').on('click', function(e) {
                e.preventDefault();
                let cartIds = $('#cartIds').val();
                let firstName = $('#firstName').val();
                let lastName = $('#lastName').val();
                let countryCode = $('#countryCode').val();
                let phoneNumber = $('#phone').val();
                let email = $('#checkoutEmail').val();
                let emailCheck = $('#emailCheck').val();
                let whatsappCheck = $('#whatsappCheck').val();
                if (emailCheck === '0' && whatsappCheck === '0') {
                    Materialize.toast(translationArray['cartShareFailed0'], 4000, 'toast-alert');
                } else {
                    if (email === '' || countryCode === '' || phoneNumber === '') {
                        Materialize.toast(translationArray['cartShareFailed1'], 4000, 'toast-alert');
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: '/shareCart',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                cartIds: cartIds,
                                firstName: firstName,
                                lastName: lastName,
                                email: email,
                                phoneNumber: countryCode + phoneNumber,
                                emailCheck: emailCheck,
                                whatsappCheck: whatsappCheck
                            },
                            success: function(data) {
                                if (data.success) {
                                    $('#shareButton').addClass('disabled');
                                    if (emailCheck === '1') {
                                        Materialize.toast(translationArray['cartShareSuccess0'] + email, 4000, 'toast-success');
                                    }
                                    if (whatsappCheck === '1') {
                                        let phoneNumberForWhatsApp = countryCode + phoneNumber;
                                        phoneNumberForWhatsApp = phoneNumberForWhatsApp.replace('+', '');
                                        phoneNumberForWhatsApp = phoneNumberForWhatsApp.replace(' ', '');
                                        let text = translationArray['cartShareResponse'] + encodeURIComponent(data.link);
                                        $('#whatsappLink').append('<a id="whatsappButton" style="border: 1px solid;padding: 10px;color: #1ebea5;" target="_blank" href="https://api.whatsapp.com/send?phone='+phoneNumberForWhatsApp+'&text='+text+'&source=&data=">'+translationArray['cartShareLink']+'</a>');
                                        $('#whatsappLink').show();
                                    }
                                } else {
                                    Materialize.toast(data.error, 4000, 'toast-alert');
                                }
                            }
                        });
                    }
                }
            });

            $('body').on('click', '#whatsappButton', function() {
                Materialize.toast(translationArray['redirectHomePage'], 8000, 'toast-success');
                setTimeout(function() {
                    window.location.href = "/";
                }, 8000);
            });

        });

        $("#termsCheck").on('click', function() {
            let placeOrder = $('#placeOrder');
            if ($("#termsCheck").is(':checked')) {
                $("#placeOrder").removeAttr("disabled");
            } else {
                placeOrder.attr('disabled', 'disabled');
            }
        });

        $("#registerCheck").on('click', function() {
            if ($("#registerCheck").is(':checked')) {
                $("#registerCheck").val("1");
            } else {
                $("#registerCheck").val("0");
            }
        });
    </script>
@elseif($page == 'register')
    <script src="https://www.google.com/recaptcha/api.js?render=6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr', {action: 'register'}).then(function(token) {
                $('#recaptchaToken').val(token);
                $('#recaptchaAction').val('register');
            });
        });
    </script>
    <script>
        $(function() {
            function validateFields(id, string, count, op, id2 = null) {
                let operators = {
                    '>': function(a, b, c) { return a.val().length > b; },
                    '<': function(a, b, c) { return a.val().length < b; },
                    'blank': function(a, b, c) { return a.val().length === 0; },
                    '@': function(a, b, c) { return !a.val().includes('@'); },
                    'eq': function(a, b, c) { return a.val() !== c.val(); }
                };
                let message = '';
                switch (op) {
                    case '>':
                        message = string + ' can not be longer than ' + count + ' characters!';
                        break;
                    case 'blank':
                        message = string + ' should contain at least 1 character';
                        break;
                    case '@':
                        message = string + ' should contain @';
                        break;
                    case 'eq':
                        message = string + ' you typed must be equal with Confirm Password field!';
                        break;
                    case '<':
                        message = string + ' should be at least '  + count + ' characters!';
                        break;
                }
                if (operators[op](id, count, id2)) {
                    Materialize.toast(message, 5000, 'toast-alert');
                    return false;
                }
                return true;
            }

            $('#formButton').on('click', function(e) {
                e.preventDefault();
                let name = $('#name');
                let surname = $('#surname');
                let email = $('#emailRegister');
                let password = $('#password');
                let passwordConfirm = $('#password-confirm');
                let phoneNumber = $('#phoneNumber');
                let address = $('#address');
                if (!validateFields(name, 'Name', 0, 'blank')) {
                    return;
                }
                if (!validateFields(name, 'Name', 255, '>')) {
                    return;
                }
                if (!validateFields(surname, 'Surname', 0, 'blank')) {
                    return;
                }
                if (!validateFields(surname, 'Surname', 255, '>')) {
                    return;
                }
                if (!validateFields(email, 'E-Mail', 0, 'blank')) {
                    return;
                }
                if (!validateFields(email, 'E-Mail', 255, '>')) {
                    return;
                }
                if (!validateFields(email, 'E-Mail', 0, '@')) {
                    return;
                }
                if (!validateFields(password, 'Password', 8, '<')) {
                    return;
                }
                if (!validateFields(password, 'Password', 0, 'eq', passwordConfirm)) {
                    return;
                }
                if (!validateFields(phoneNumber, 'Phone Number', 50, '>')) {
                    return;
                }
                if (!validateFields(address, 'Address', 255, '>')) {
                    return;
                }
                $('#registerForm').submit();
            });
        });
    </script>
@elseif($page == 'check-booking')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('#invoiceUrl').hover(function() {
                $(this).css('text-decoration', 'none');
            });
            $('#voucherUrl').hover(function() {
                $(this).css('text-decoration', 'none');
            });

            $('#checkBookingButton').on('click', function() {
                if($('#bookingRefCode').val() == "" || $('#bookingRefCode').val() == null) {
                    Materialize.toast('{{__('bookingReferenceCodeFieldIsRequired')}}', 5000, 'toast-alert');
                    return false;
                }

                if($('#checkLevel').val() == 1) {
                    $.ajax({
                        url: '/send-confirmation-code',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            bookingRefCode: $('#bookingRefCode').val()
                        },

                        beforeSend: function () {
                            $('#cover-spin').show();
                        },
                        complete: function () {
                            $('#cover-spin').hide();
                        },
                    })
                        .done(function(data) {
                            $('#conCodeBlock').show();
                            $('#conCodeInformation').text(data.message);

                            $('#checkLevel').val(2);
                            $('#tryAnotherCodeButton').parent().show();
                        })
                        .fail(function(xhr, status, error) {
                            $('#conCodeBlock').hide();

                            var err = JSON.parse(xhr.responseText);
                            Materialize.toast(err.error, 5000, 'toast-alert');
                        })
                } else if($('#checkLevel').val() == 2) {
                    if($('#confirmationCode').val() == "" || $('#confirmationCode').val() == null) {
                        Materialize.toast('{{__('confirmationCodeFieldIsRequired')}}', 5000, 'toast-alert');
                        return false;
                    }

                    checkBookingAjax("conCodeMatters");
                }
            });

            $('#cancelBookingButton').on('click', function() {
                Swal.fire({
                    title: '{{__('areYouSure')}}',
                    text: "{{__('theBookingWillBeCancelled')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#87a1ad',
                    confirmButtonText: '{{__('cancelBooking')}}',
                    cancelButtonText: '{{__('back')}}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/cancel-booking',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                        })
                            .done(function(data) {
                                Swal.fire(
                                    '{{__('cancelled')}}',
                                    data.message,
                                    'success'
                                )
                                checkBookingAjax("conCodeNotMatter");
                            })
                            .fail(function(xhr, status, error) {
                                var err = JSON.parse(xhr.responseText);
                                Materialize.toast(err.error, 5000, 'toast-alert');
                            })
                    }
                })
            });

            $('#tryAnotherCodeButton').on('click', function() {
                $('#checkLevel').val(1);
                $('#tryAnotherCodeButton').parent().hide();

                $('#conCodeBlock').hide();
                $('#confirmationCode').val("");

                cleanFields();

                $.ajax({
                    url: '/reset-bkn-session',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                })
                    .done(function(data) {
                        console.log(data);
                    })
                    .fail(function(xhr, status, error) {
                        var err = JSON.parse(xhr.responseText);
                        console.log(err.error);
                    })
            });

            function checkBookingAjax(conCodeStatus) {
                $.ajax({
                    url: '/check-booking',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bookingRefCode: $('#bookingRefCode').val(),
                        confirmationCode: $('#confirmationCode').val(),
                        conCodeStatus: conCodeStatus
                    },
                })
                    .done(function(data) {
                        $('#bookingDetailsCard').show();

                        let booking = data.booking;
                        $('#bookingTitle').text(booking.title);
                        $('#bookingDateTime').text(booking.datetime);
                        $('#totalPrice').text(booking.totalPrice);
                        $('#bookingItems').text(booking.bookingItems);

                        if(booking.invoiceUrl == "#") {
                            $('#invoiceUrl').hide();
                            $('#voucherUrl').hide();
                            $('#cancelBookingButton').hide();
                            $('#vouInvInformation').text('{{__('theBookingIsCancelled')}}');
                            if(booking.extraFiles.length) {
                                $('#extra-files').css({display: 'flex'});

                                $.each(booking.extraFiles, function (k,v) {
                                    $(`<a href="${v.base}" target="_blank">${v.name}</a>`).appendTo($('#extra-files'))
                                })
                            }
                        } else {
                            $('#invoiceUrl').show();
                            if(booking.voucherUrl != "#") {
                                $('#voucherUrl').show();
                                $('#cancelBookingButton').hide();
                                $('#vouInvInformation').text('{{__('downloadVoucherAndInvoice')}}');
                            } else {
                                $('#voucherUrl').hide();
                                $('#cancelBookingButton').show();
                                $('#vouInvInformation').text('{{__('downloadInvoice')}}');
                            }
                        }
                        $('#invoiceUrl').attr("href", booking.invoiceUrl);
                        $('#voucherUrl').attr("href", booking.voucherUrl);

                        $('#confirmationCode').val("");
                        window.location = "#bookingDetailsCard";
                    })
                    .fail(function(xhr, status, error) {
                        cleanFields();

                        var err = JSON.parse(xhr.responseText);
                        Materialize.toast(err.error, 5000, 'toast-alert');
                    })
            }

            function cleanFields() {
                $('#bookingDetailsCard').hide();

                $('#bookingTitle').text("");
                $('#bookingDateTime').text("");
                $('#totalPrice').text("");
                $('#bookingItems').text("");
                $('#vouInvInformation').text("");
                $('#invoiceUrl').attr("href", "#");
                $('#voucherUrl').attr("href", "#");
            }
        });
    </script>
@elseif($page == 'email')
    <script>
        $(function() {
            $('#sendEmailForPasswordReset').on('click', function() {
                let email = $('#emailForResetPassword').val();
                $.ajax({
                    type: 'POST',
                    url: '/sendResetPasswordEmail',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        email: email,
                    },
                    success: function(data) {
                        if (data.success) {
                            Materialize.toast(data.success, 5000, 'toast-success');
                        } else {
                            Materialize.toast(data.error, 5000, 'toast-alert');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'reset')
    <script>
        $(function() {
            $('#sendPasswordForPasswordReset').on('click', function() {
                let password = $('#passwordForResetPassword').val();
                let passwordConfirmation = $('#passwordConfirmationForResetPassword').val();
                let translationArray = JSON.parse($('#translationArray').val());
                if (password.length < 8 || passwordConfirmation.length < 8) {
                    Materialize.toast(translationArray['passwordCharacterLength'], 3000, 'toast-alert');
                    return;
                }
                if (password !== passwordConfirmation) {
                    Materialize.toast(translationArray["passwordConfirmationEquality"], 3000, 'toast-alert');
                    return;
                }
                $('#sendPasswordForPasswordReset').attr('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: '/sendNewPassword',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        email: $('#hiddenEmailForPasswordReset').val(),
                        token: $('#hiddenTokenForPasswordReset').val(),
                        password: password,
                    },
                    success: function(data) {
                        if (data.success) {
                            Materialize.toast(data.success, 5000, 'toast-success');
                        } else {
                            Materialize.toast(data.error, 5000, 'toast-alert');
                            $('#sendPasswordForPasswordReset').removeAttr('disabled');
                        }
                    }
                });
            });
        });
    </script>
@elseif($page == 'become-a-supplier')

    <script src="https://www.google.com/recaptcha/api.js?render=6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr', {action: 'register'}).then(function(token) {
                $('#recaptchaToken').val(token);
                $('#recaptchaAction').val('register');
            });
        });
    </script>

    <script>
        $('#location').on('change', function() {
            let countryID = $(this).val();
            $('#cities').html('');
            $.ajax({
                type: 'POST',
                url: '/getCities',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    countryID: countryID
                },
                success: function(data) {
                    let cities = data.cities;
                    for (let i = 0; i < cities.length; i++) {
                        $('#cities').append(
                            '<option value="'+cities[i]+'">'+cities[i]+'</option>'
                        );
                    }
                }
            });
        });
    </script>
@elseif($page == 'become-a-commissioner')

    <script src="https://www.google.com/recaptcha/api.js?render=6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeV48kUAAAAAEBarIb7y3KiRw0452xW-5DK3YTr', {action: 'register'}).then(function(token) {
                $('#recaptchaToken').val(token);
                $('#recaptchaAction').val('register');
            });
        });
    </script>


    <script>
        $(function() {
            $('.saveCommissionerButton').on('click', function(e) {
                e.preventDefault();
                let password = $('#password').val();
                let confirmation = $('#password-confirm').val();
                let translationArray = JSON.parse($('#translationArray').val());
                if (password !== confirmation) {
                    Materialize.toast(translationArray['passwordCharacterLength'], 5000, 'toast-alert');
                    return;
                }
                if (password.length < 8) {
                    Materialize.toast(translationArray['passwordConfirmationEquality'], 5000, 'toast-alert');
                    return;
                }
                $('#becomeACommissionerForm').submit();
            });
        });
    </script>
@elseif($page == 'credit-card-details' || $page == 'external-payment-details')
    <script>

        $(function() {
            // Validation of credit card number using luhn algorithm
            $('input[name="pan"]').on('keyup', function() {
                $this = $(this);
                if ($this.val().length === 16) {
                    // card number
                    let cardNum = $this.val();
                    // get the last number
                    let cardLastNum = parseInt(cardNum.substr(cardNum.length - 1));
                    // remove the last number
                    let cardNumMin = cardNum.slice(0,-1);
                    // reverse
                    let cardNumReverse = cardNumMin.split("").reverse().join("");
                    // set to array
                    let cardNumArray = cardNumReverse.split("");

                    // for each odd number multiply
                    let a = 2;
                    let temp = null;
                    let cardNumOdsMultiplied = [];
                    for (let i = 0; i < cardNumArray.length; i++) {
                        if (a%2 === 0) {
                            temp = cardNumArray[i] * 2;
                            // if the number is greater than 9 substract by 9
                            if (temp > 9) {
                                temp = (temp -9);
                            }
                            cardNumOdsMultiplied.push(temp);
                        } else {
                            cardNumOdsMultiplied.push(parseInt(cardNumArray[i]));
                        }
                        a++;
                    }

                    let cardNumTelly = 0;
                    for (let i = 0; i < cardNumOdsMultiplied.length; i++) {
                        cardNumTelly += cardNumOdsMultiplied[i];
                    }

                    cardNumTelly = cardNumTelly + cardLastNum;

                    if (cardNumTelly % 10 === 0) {
                        // number is a valid card number
                    } else {
                        let translationArray = JSON.parse($('#translationArray').val());
                        // number is not valid
                        Materialize.toast(translationArray['checkYourCreditCardDetails'], 5000, 'toast-alert');
                    }
                }
            });

            if($('#deviceType').length) {
                let screenWidth = screen.width;
                let deviceType = "";
                if(screenWidth <= 480)
                    deviceType = "Mobile";
                else if(screenWidth > 480 && screenWidth <= 1024)
                    deviceType = "Tablet";
                else if(screenWidth > 1024)
                    deviceType = "Desktop";
                $('#deviceType').val(deviceType)
            }
        });
    </script>
@elseif($page == 'blog')
    <script>
        //Blog post animation
        // $('.post-module').hover(function() {
        //     $(this).find('.description').stop().animate({
        //         height: "toggle",
        //         opacity: "toggle"
        //     }, 300);
        //     $(this).find('.date').stop().animate({
        //         opacity: "toggle",
        //     }, 200);
        //     $(this).find('.author').stop().animate({
        //         opacity: "toggle",
        //     }, 200);
        // });
    </script>
@elseif($page == 'licence-files')
    <script>
        $(function() {


            $('#newFile').on('click', function() {
                let block =
                    '<div class="row s6">' +
                    '<div class="input-field col s6">' +
                    '<input type="text" class="validate title" name="title[]" value="">' +
                    '<label for="bankName">{{__("title")}}</label>' +
                    '</div>' +
                    '<div class="input-field col s6">' +
                    '<button type="button" class="deleteFiles waves-effect waves-light btn-danger" style="font-size:26px;padding:2px 20px;border:none;float: right">-</button>' +
                    '<input type="file" class="fileName validate" name="fileName[]" >' +
                    '</div>' +
                    '</div>' ;
                $('#licenceFilesContainer').append(block);
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
                            Materialize.toast(data.success, 4000, 'toast-success');
                        },
                        error: function(data) {
                            Materialize.toast(data.error, 4000, 'toast-alert');
                        }
                    });
                }
            });
        });
    </script>
    @endif
    </body>
    </html>
