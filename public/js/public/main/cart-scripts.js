$('.spinnerButtons').on('click', function() {
    let btn = $(this),
        oldValue = btn.closest('.number-spinner').find('input').val().trim(),
        newVal = 0;

    if (btn.attr('data-dir') === 'up') {
        newVal = parseInt(oldValue) + 1;
    } else {
        if (oldValue > 1) {
            newVal = parseInt(oldValue) - 1;
        } else {
            newVal = 1;
        }
    }
    btn.closest('.number-spinner').find('input').val(newVal);
    $('#ticketCount').val(parseInt($('.adultInput').val()) + parseInt($('#youngInput').val()) + parseInt($('#childInput').val()) + parseInt($('#infantInput').val()) + parseInt($('#euCitizenInput').val()));
    getAvailableDates().then(selectDate);
});

$('#checkOut').on('click', function() {
    let langCode = $('#sessionLocale').val();
    let langCodeForUrl = langCode === 'en' ? '' : '/' + langCode;
    let checkoutUrl = $('#checkoutUrl').val();
    $.ajax({
        method: 'GET',
        url: '/checkout',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(data) {
        
         if(data.status == "0"){
            
            var disabled_items = data.disabled_items;

            disabled_items.forEach( function(element, index) {
                $("div[data-cart-id='"+element+"']").css("background", "aqua");
            });
            Materialize.toast(data.message, 10000, 'toast-alert');
            return false;
         }


            Materialize.toast(data.success, 5000, 'toast-success');
            setTimeout(function() {
                window.location.href = langCodeForUrl + "/" + checkoutUrl;
            }, 2500);
        },
        error: function(t) {
            
            Materialize.toast(t.error, 5000, 'toast-alert');
        }
    });
});

$('.updateCart').on('click', function() {
    let cartID = $(this).attr('data-cart-id');
    let adultCount = $('#ADULT'+cartID).val();
    let youthCount = $('#YOUTH'+cartID).val();
    let childCount = $('#CHILD'+cartID).val();
    let infantCount = $('#INFANT'+cartID).val();
    let euCitizenCount = $('#EU_CITIZEN'+cartID).val();
    $.ajax({
        type: 'POST',
        url: '/cartUpdate',
        data: {
            "_token": $('meta[name="csrf-token"]').attr('content'),
            cartID: cartID,
            adultCount: adultCount,
            youthCount: youthCount,
            childCount: childCount,
            infantCount: infantCount,
            euCitizenCount: euCitizenCount
        },
        success: function(data) {
            if (data.ticket) {
                Materialize.toast("There's no available ticket, please check your ticket counts!", 4000, 'toast-alert');
            } else {
                Materialize.toast('Your cart is updated successfully!', 4000, 'toast-success');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
        }
    });

});
