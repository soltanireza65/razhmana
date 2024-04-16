const phone = $('#phone');
const countryCode = $('#country-code');
const otpInputs = $('.mj-otp-field input');

$('.mj-login-logo').css({
    height: `calc(100% - ${$('.mj-bottom-sheet').innerHeight()}px)`,
});

countryCode.select2({
    dropdownParent: $('.mj-input-box'),
    templateResult: function (data) {
        const title = data.text;
        const image = (data.element && $(data.element).data('image')) ? $(data.element).data('image') : '';

        return $(`
            <span class="mj-country-code-item">
                <img src="${image}" alt="${title}">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;
        const image = (data.element && $(data.element).data('image')) ? $(data.element).data('image') : '';

        return $(`
            <span class="mj-country-code-item">
                <img src="${image}" alt="${title}">
                ${title}
            </span>
        `);
    }
});
//
// phone.on('input', function () {
//     if ($(this).val().substring(0, 1) == '0') {
//         $(this).val($(this).val().substring(1));
//     }
// });

// phone.on('click', function () {
//     $('button[data-close]').removeClass(('d-none'));
//     // $('button[data-next-step]').removeClass(('d-none'));
//     // $('.mj-bottom-sheet').css({
//     //     height: `${$(window).height()}px`,
//     //     'border-radius': 0,
//     //     top: 0
//     // });
// });
//
// phone.focusin(function () {
//     $('button[data-close]').removeClass(('d-none'));
//     $('button[data-next-step]').removeClass(('d-none'));
//     $('.mj-bottom-sheet').css({
//         height: `${$(window).innerHeight() - 230}px`,
//         'border-radius': 0,
//         top: 0,
//         margin: 'auto 0 0'
//     });
// });

// phone.focusout(function () {
//     $('button[data-close]').trigger('click');
// });

$('button[data-close]').on('click', function () {
    $('button[data-close]').addClass(('d-none'));
    $('button[data-next-step]').addClass(('d-none'));
    $('.mj-bottom-sheet').css({
        height: '',
        'border-radius': '',
        top: '',
        margin: ''
    });
    $('div[data-step="2"]').fadeOut(300);
    $('div[data-step="1"]').delay(320).fadeIn(300);
});

$('button[data-next-step]').on('click', function () {
    if (phone.val().length != 10) {
        sendNotice(lang_vars.alert_warning, lang_vars.login_enter_phone_number, 'warning', 3500);
    } else {
        const params = {
            action: 'login-user',
            mobile: `${countryCode.val()}${phone.val()}`,
            type: 'driver',
            token: $('#token_login').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    $('#token_login').val(json.response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_info, lang_vars.login_otp_sent, 'info', 2500);
                        $('div[data-step="1"]').fadeOut(300);
                        $('div[data-step="2"]').delay(320).fadeIn(300);
                    } else if (json.status == 301) {
                        sendNotice(lang_vars.alert_warning, lang_vars.login_redirect_businessman, 'warning', 3500);
                    } else {
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        });
    }
});

$('button[data-prev-step]').on('click', function () {
    $('div[data-step="2"]').fadeOut(300);
    $('div[data-step="1"]').delay(320).fadeIn(300);
    otpInputs.each(function (index, element) {
        $(element).val('');
    })
    $('#phone').val('')
});


otpInputs.each(function (index, element) {
    $(element).attr('data-index', index);
    $(element).bind('paste', handleOnPaste);
    $(element).on('keyup', handleOTP);
});

async function handleOnPaste(e) {
    const data = await navigator.clipboard.readText();
    const value = data.split('');
    if (value.length == otpInputs.length) {
        otpInputs.each(function (index, element) {
            $(element).val(value[index]);
        });
        submit();
    }
}

function handleOTP(e) {
    const input = $(e.target);
    let value = input.val();
    input.val('');
    input.val(value ? value[0] : '');

    let fieldIndex = input.attr('data-index');
    if (value.length > 0 && fieldIndex < otpInputs.length - 1) {
        input.next().focus();
    }

    if (e.key == 'Backspace' && fieldIndex > 0) {
        input.prev().focus();
        fieldIndex--;
    }

    if (fieldIndex == otpInputs.length - 1) {
        submit();
    }
}

function submit() {
    let otp = '';
    otpInputs.each(function (index, element) {
        otp += $(element).val();
    });

    if (otp.length == 6) {
        const params = {
            action: 'verify-login',
            code: otp,
            token: $('#token_otp').val(),
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                console.log(response)
                try {
                    const json = JSON.parse(response);
                    $('#token_otp').val(json.response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.login_successfully, 'success', 2500);
                        setTimeout(() => {
                            window.location.href ='/driver';
                        }, 3000);
                    } else {
                        sendNotice(lang_vars.alert_error, lang_vars.login_otp_wrong, 'error', 3500);
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        });
    } else {
        sendNotice(lang_vars.alert_warning, lang_vars.login_enter_otp, 'warning', 3500);
    }
}