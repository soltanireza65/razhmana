if ($('#delete-car-modal').length > 0) {
    const modal = new bootstrap.Modal($('#delete-car-modal'));

    $('#delete-car').on('click', function () {
        const _btn = $(this);
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'delete-car',
            car: $(this).data('car'),
            token: $('#token').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                modal.hide();
                try {
                    const json = JSON.parse(response);
                    $('#token').val(json.response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_delete_car, 'success', 2500);
                        setTimeout(() => {
                            window.location.href ='/driver/my-cars';
                        }, 3000)
                    } else {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                } catch (e) {
                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        })
    });
}

new Swiper('.gallery-swiper', {
    slidesPerView: "1.15",
    centeredSlides: true,
    spaceBetween: 15,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    }
});

