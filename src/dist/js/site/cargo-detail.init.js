const title = $('#title');
const price = $('#price');
const unit = $('#currency-unit');
const reason = $('#reason');

price.on('input', function () {
    const value = new Intl.NumberFormat().format($(this).val().replaceAll(',', ''));
    if (isNaN(value.replaceAll(',', ''))) {
        $(this).val(0)
    } else {
        $(this).val(value)
    }
});

new Swiper('.gallery-swiper', {
    slidesPerView: "1.15",
    centeredSlides: true,
    spaceBetween: 15,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    }
});

$('#submit-expenses').on('click', function () {
    const _btn = $(this);

    if (title.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_expense_title, 'warning', 3500);
    } else if (price.val() == '' || price.val() <= 0) {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_expense_amount, 'warning', 3500);
    } else if (unit.val() == undefined || unit.val() == '' || unit.val() == null) {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_expense_currency, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'new-extra-expenses',
            cargo: $(this).data('cargo'),
            request: $(this).data('request'),
            title: title.val(),
            amount: price.val().replaceAll(',', ''),
            unit: unit.val(),
            token: $('#token').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_extra_expanses_submitted, 'success', 2500);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000)
                    } else {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        $('#token').val(json.response);
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
    }
});

$('#submit-cancel').on('click', function () {
    const _btn = $(this);

    if (reason.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_cancel_request_reason, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'cancel-request',
            request: $(this).data('request'),
            reason: reason.val(),
            token: $('#token-cancel').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_success_cancel_request_reason, 'success', 2500);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        $('#token').val(json.response);
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
        to: $(element).data('businessman'),
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
                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    $('#token-complaint').val(json.response);
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
        copyToClipboard($('#share-data').data('title') +  $('#share-data').data('share-setting') +"\n"+$('#share-data').data('source')+"\n"+$('#share-data').data('dest')+"\n"+$('#share-data').data('weight') )
        function copyToClipboard(text) {
            /* Create a textarea element to hold the text to be copied */
            var textarea = document.createElement("textarea");
            textarea.value = text; // Set the text to be copied as the parameter
            document.body.appendChild(textarea); // Append the textarea element to the DOM
            textarea.select(); // Select the text in the textarea
            document.execCommand("copy"); // Execute the copy command
            document.body.removeChild(textarea); // Remove the textarea element from the DOM
            $('.mj-share-tooltip').fadeIn(300);
            setTimeout(function () {
                $('.mj-share-tooltip').fadeOut(200);
            },2000)
        }
    }
});

$(document).on("click", ".mj-cargo-owner-call", function () {
    $('.mj-cargo-owner-modal-info').modal("show")
})

