new Swiper('.gallery-swiper', {
    slidesPerView: "1.15",
    centeredSlides: true,
    spaceBetween: 15,
    // freeMode: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    }
});

const swiperSliders = $('div[data-swiper]');
swiperSliders.each(function (index, element) {
    new Swiper(`#${$(element).prop('id')}`, {
        slidesPerView: "1.15",
        centeredSlides: true,
        spaceBetween: 15,
        // freeMode: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
    });
});

$('button[data-btn-expenses]').on('click', function () {
    const params = {
        action: 'change-extra-expense-status',
        expense: $(this).data('expense'),
        cargo: $(this).data('cargo'),
        request: $(this).data('request'),
        driver: $(this).data('driver'),
        status: $(this).data('status'),
        token: $('#token-expenses').val(),
    };


    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {

            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    sendNotice(lang_vars.alert_success, lang_vars.b_extra_expenses_changed, 'success', 2500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000)
                } else {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    $('#token-expenses').val(json.response);
                }
            } catch (e) {
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        }
    })
});


$('input[data-rate]').on('click', function () {
    const _parent = $(this).parent();
    const rate = (_parent.find('input:checked').length == 0) ? 0 : _parent.find('input:checked').val();

    const params = {
        action: 'submit-rate-to-request',
        cargo: _parent.data('cargo'),
        request: _parent.data('request'),
        rate: rate,
        token: $('#token-rate').val(),
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    sendNotice(lang_vars.alert_success, lang_vars.b_rate_submitted, 'success', 2500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000)
                } else {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    $('#token-expenses').val(json.response);
                }
            } catch (e) {
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        }
    })
});


const reason = $('#reason');

reason.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val().trim() == '' || $(this).val().length < 3) {
        $(this).parent().addClass('border-danger');
    }
});

$('#submit-cancel').on('click', function () {
    if (reason.val().trim() == '' || reason.val().length < 3) {
        reason.parent().addClass('border-danger');
    } else {
        const params = {
            action: 'cancel-cargo',
            cargo: $(this).data('cargo'),
            reason: reason.val(),
            token: $('#token-cancel').val(),
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.b_cargo_cancel_notice_success, 'success', 2500);
                        setTimeout(() => {
                            window.location.href ='/businessman/my-cargoes';
                        }, 3000)
                    } else {
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        $('#token-expenses').val(json.response);
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            }
        })
    }
});

function submitComplaint(element) {
    const _btn = $(this);
    _btn.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });

    const params = {
        action: 'submit-complaint',
        cargo: $(element).data('cargo'),
        request: $(element).data('request'),
        to: $(element).data('driver'),
        token: $('#token-complaint').val(),
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    sendNotice(lang_vars.alert_success, lang_vars.d_alert_success_complaint, 'success', 2500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    $('#token-complaint').val(json.response);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
                _btn.removeAttr('disabled').css({
                    opacity: 1
                });
            } catch (e) {
                _btn.removeAttr('disabled').css({
                    opacity: 1
                });
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
}

const shareButton =   document.getElementById('share');
shareButton.addEventListener('click', event => {
    console.log($('#share-data').data('link'))
    console.log($('#share-data').data('share-setting'))
    console.log($('#share-data').data('title'))
    if (navigator.share) {
        navigator.share({
            title: $('#share-data').data('title') +  $('#share-data').data('share-setting'),
            text: $('#share-data').data('title') +  $('#share-data').data('share-setting') +"\n"+$('#share-data').data('source')+"\n"+$('#share-data').data('dest')+"\n"+$('#share-data').data('weight'),
            url:  $('#share-data').data('link')
        }).then(() => {
            // console.log('Thanks for sharing!');
        })
            .catch(console.error);
    } else {
        // shareDialog.classList.add('is-open');
    }
});

$(document).on("click", ".mj-d-cargo-item-link2", function () {
    $('.mj-cargo-owner-modal-info').modal("show")
})

