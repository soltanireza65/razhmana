const phone = $('#phone');
const countryCode = $('#country-code');
const otpInputs = $('.mj-otp-field input');

$('.mj-login-logo').css({
    height: `calc(100% - ${$('.mj-bottom-sheet').innerHeight()}px)`,
});

countryCode.select2({
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
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

phone.on('input', function () {
    if ($(this).val().substring(0, 1) == '0' || $(this).val().substring(0, 1) == '٠') {
        $(this).val($(this).val().substring(1));
    }
});

$('button[data-close]').on('click', function () {
    $('button[data-close]').addClass(('d-none'));
    $('button[data-next-step]').addClass(('d-none'));
    $('.mj-bottom-sheet').css({
        height: '', 'border-radius': '', top: '', margin: ''
    });
    $('div[data-step="2"]').fadeOut(300);
    $('div[data-step="1"]').delay(320).fadeIn(300);
});
let registerStatus = 'login';
$('button[data-next-step]').on('click', function () {
    if (phone.val().length > 5 && countryCode.val()) {
        const params = {
            action: 'login-user',
            mobile: `${countryCode.val()}${phone.val()}`,
            type: 'guest',
            token: $('#token_login').val()
        };

        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
                console.log(response)
                try {
                    const json = JSON.parse(response);
                    $('#token_login').val(json.response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.login_otp_sent, 'success', 2500);
                        $('div[data-step="1"]').fadeOut(300);
                        $('div[data-step="2"]').delay(320).fadeIn(300);
                    } else if (json.status == 201) {
                        sendNotice(lang_vars.alert_success, lang_vars.login_otp_sent, 'success', 3500);
                        $('div[data-step="1"]').fadeOut(300);
                        $('div[data-step="2"]').delay(320).fadeIn(300);
                        registerStatus = 'register';
                    } else {
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        });
    }

    // }
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
            'status': registerStatus,
            token: $('#token_otp').val(),
        };

        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
                console.log(response)
                try {
                    const json = JSON.parse(response);
                    $('#token_otp').val(json.response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.login_successfully, 'success', 2500);
                        setTimeout(() => {
                            let url = getCookie('login-back-url');
                            if (url) {
                                if (url == 'unknow') {
                                    let user_type = getCookie('user-type')
                                    if (user_type == 'businessman') {
                                        window.location.href = '/businessman';
                                    } else if (user_type == 'driver') {
                                        window.location.href = '/driver';
                                    } else {
                                        window.location.href = '/';
                                    }
                                } else {
                                    window.location.href = url;
                                }
                            } else {
                                window.location.href = '/';
                            }

                        }, 3000);

                    } else if (json.status == 201) {
                        sendNotice(lang_vars.alert_success, lang_vars.login_otp_sent, 'success', 2500);
                        $('div[data-step="2"]').fadeOut(300);
                        $('div[data-step="3"]').delay(320).fadeIn(300);
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

$('#register_new_user').click(function () {
    let otp = '';
    otpInputs.each(function (index, element) {
        otp += $(element).val();
    });
    if (otp.length == 6) {
        if ($('#user_name').val().trim() && $('#user_name').val().trim().length > 2) {
            if ($('#user_lname').val().trim() && $('#user_lname').val().trim().length > 2) {
                const params = {
                    action: 'register-new-user',
                    mobile: `${countryCode.val()}${phone.val()}`,
                    code: otp,
                    user_name: $('#user_name').val().trim(),
                    user_lname: $('#user_lname').val().trim(),
                    user_referral: $('#user_referral').val().trim(),
                    token: $('#token_register').val(),
                    mobileCode: countryCode.val(),
                    mobileNumber: phone.val(),
                };
                $.ajax({
                    url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
                        //console.log(response)
                        try {
                            const json = JSON.parse(response);
                            $('#token_login').val(json.response);
                            if (json.status == 200) {
                                sendNotice(lang_vars.alert_success, lang_vars.u_register_success, 'success', 2500);
                                window.location.href = '/';
                            } else {
                                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                            }
                        } catch (e) {
                            sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        }
                    }
                });
            }
        }
    }

});


/*otp auto read sms */

if ('OTPCredential' in window) {
    window.addEventListener('DOMContentLoaded', e => {
        const ac = new AbortController();
        navigator.credentials.get({
            otp: {transport: ['sms']},
            signal: ac.signal
        }).then(otp => {
            // otp.code;
            // let t1=otp.code;
            // let t2=t1.split("");
            console.log(otp)
            // $(".mj-otp-field :input").each(function (a,b) {
            //     // console.log(a,'a')
            //     // console.log($(b).val(t2[a]))
            // });

        }).catch(err => {

        });
        ac.abort();
    });
}
/*end otp read sms */
// Register an event listener for incoming SMS messages
navigator.serviceWorker.register('sw.js').then(registration => {
    navigator.serviceWorker.addEventListener('message', event => {
        const {data} = event;
        // Parse the received SMS message to extract the OTP value
        const otp = extractOTPFromSMS(data);
        // Use JavaScript to select the input field you want to autofill and set its value to the extracted OTP
        $('.mj-header-subtitle').html(data)
    });
});

function extractOTPFromSMS(sms) {
    // Use a regular expression or other parsing method to extract the OTP value from the SMS message
    const otpRegex = /\b\d{6}\b/g;
    const match = sms.match(otpRegex);
    if (match && match.length > 0) {
        return match[0];
    } else {
        return null;
    }
}

// $('#user_referral').on('input', function (ev) {
//     var $this = $(this);
//     var maxlength = $this.attr('max').length;
//     var value = $this.val();
//     if (value && value.length >= maxlength) {
//         $this.val(value.substr(0, maxlength));
//     }
// });