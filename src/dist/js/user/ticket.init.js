let selectedFiles = [];
const attachment = $('#attachments');
const messageBody = $('#message-body');
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

$(window).on('resize', function () {
    const height = $('#message-body').innerHeight() + 256;
    $('.mj-chat-list').css({
        'max-height': `${$(window).innerHeight() - height}px`
    }).scrollTop($('.mj-chat-list')[0].scrollHeight);
});

$(document).ready(function () {
    $(window).trigger('resize');
});

messageBody.on('input', function () {
    $(this)
        .css({
            height: '1px'
        })
        .css({
            height: `${$(this)[0].scrollHeight}px`,
            'max-height': '300px'
        });

    $(window).trigger('resize');
});

attachment.on('change', function () {
    selectedFiles = $(this).prop('files');
});

$('#send-message').on('click', function () {
    const _btn = $(this);

    if (messageBody.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_message, 'warning', 3500);
    } else {
        modalProcessing.show();

        const ticket = $(this).data('ticket');
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        let params = new FormData();
        params.append('action', 'send-message');
        params.append('ticket', ticket);
        params.append('message', messageBody.val());
        params.append('token', $('#token').val());
        selectedFiles.forEach(function (element, index) {
            params.append(index + "", element);
        });

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: params,
            contentType: false,
            processData: false,
            success: function (response) {
                setTimeout(() => {
                    modalProcessing.hide();
                    modalSubmitting.show();
                }, 500);

                try {
                    const json = JSON.parse(response);
                    $('#token').val(json.response);
                    if (json.status == 200) {
                        const html = `
                        <i class="fe-check-circle d-block text-success mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_ticket_title'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_ticket_message_submitted, 'success', 2500);
                        setTimeout(() => {
                            window.location.href =`/user/ticket/${ticket}`;
                        }, 3000);
                    } else {
                        const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
                        `;
                        $('#submitting-alert').html(html);

                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                    }
                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                }
            }
        })
    }
});
