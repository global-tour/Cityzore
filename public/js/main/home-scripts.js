$(window).on('load', function getCartItems() {
    let fullUrl = window.location.href;
    if (fullUrl.indexOf('admin') < 0) {
        setTimeout(function() {
            getCartItems();
        }, 1000);

        function getCartItems() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/getCartItems',
                data: {},
                success: function(data) {
                    let currenciesAsStr = {
                        '1': '$',
                        '2': '€',
                        '3': '£',
                        '4': '₺',
                        '5': 'ƒ',
                        '6': 'C$',
                        '7': '₽',
                        '8': 'AED',
                        '9': '₻',
                        '10': '¥',
                        '11': '₹',
                        '12': 'CSK',
                        '13': '₼',
                        '14': '원',
                        '15': 'QAR',
                        '16': '฿'
                    };
                    let currencySymbol = currenciesAsStr[$('#sessionCurrency').val()];
                    if (data.success) {
                        let carts = data.carts;
                        let images = data.images;
                        let optionNames = data.optionNames;
                        let baseUrl = window.location.origin;
                        let block = '';
                        let trashUrl = baseUrl + '/img/icon/trash.png';
                        carts.forEach(function(cart, index) {
                            let imageSrc = 'https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/' + images[index];
                            let deleteCartUrl = baseUrl + '/deleteItemFromCart/' + cart.id;
                            block += '<tbody class="cartItem tbody">\n' +
                                '            <tr>\n' +
                                '                <td rowspan="3" style="vertical-align: middle; padding: 3px;">\n' +
                                '                    <img id="image" src="' + imageSrc + '" alt="" style=" width:80px; height: 80px; border-radius: 25%; margin-right: 10px;">\n' +
                                '                </td>\n' +
                                '                <td style="padding: 3px;"><span style="font-weight: bold; color: #2d5d73;">' + optionNames[index] + '</span></td>\n' +
                                '                <td class="trash" rowspan="3" style="padding: 3px; vertical-align: middle;"><a href="' + deleteCartUrl + '"><img src="' + trashUrl + '" class="info-icon" style="width:20px;" /></a></td>\n' +
                                '            </tr>\n' +
                                '            <tr>\n' +
                                '                <td style="border: none; padding: 3px;">\n';
                            block += '<span>';
                            let bookingItems = JSON.parse(cart.bookingItems);
                            bookingItems.forEach(function(bookingItem) {
                                block += data.translationArray[bookingItem.category] + ' x ' + bookingItem.count + '<br>\n';
                            });
                            block += '</span>';
                            block += '</td>';
                            block += '            </tr>\n' +
                                '            <tr>\n' +
                                '                <td style="border: none; padding: 3px;">' + currencySymbol + ' ' + cart.totalPrice + '</td>\n' +
                                '            </tr>\n' +
                                '            </tbody>';
                        });
                        $('#cartTable').append(block);
                        $('.mm1-com-cart').show();
                        $('.cartCount').fadeIn();
                        $('.cartCount').html(data.carts.length);

                    } else {
                        $('.cartCount').hide();
                        $('#cartTable').append('<span id="emptyCart">Your cart is empty.</span>');
                    }
                }
            });
        }
    }

});

$(document).on('click', '.hamburger-menu', function () {
    $(this).toggleClass('open-menu')
    $(this).parent().find('.mobile-menu-content').toggleClass('open-menu-content')

    if ($(this).hasClass('open-menu')) {
        $('body').css({
            overflow: 'hidden'
        });
    }else{
        $('body').css({
            overflow: 'unset'
        })
    }
})

$(document).on('click', '.mobile-menu .mobile-menu-content .hasSubmenu', function () {
    $('.mobile-menu .mobile-menu-content .hasSubmenu').not(this).removeClass('active')
    if ($(this).hasClass('active')) {
        $(this).removeClass('active')
    }else{
        $(this).addClass('active')
    }
})
