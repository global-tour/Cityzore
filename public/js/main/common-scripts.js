$(function() {
    $('#addRemoveWishlist').on('click', function() {
        let $this = $(this);
        let wishlistType = $this.attr('data-type');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/addRemoveWishlist',
            data: {
                productID: $('#productID').val(),
                wishlistType: wishlistType
            },
            success: function(data) {
                if (!data.isLoggedIn) {
                    Materialize.toast(data.success, 4000, 'toast-alert');
                    return;
                }
                Materialize.toast(data.success, 4000, 'toast-success');
                if (wishlistType === 'add') {
                    $this.html(data.removeButton);
                    $this.attr('data-type', 'remove');
                } else {
                    $this.html(data.addButton);
                    $this.attr('data-type', 'add');
                }
            }
        });
    });
});
