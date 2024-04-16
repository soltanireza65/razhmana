const modalProcessing = new bootstrap.Modal($('#modal-processing'));

const firstName = $('#req-first-name');
const lastName = $('#req-last-name');
const idCard = $('#req-id-card');
const passport = $('#req-passport');

$(document).ready(function () {
    $('.mj-auth-modal-info').modal("show");
})

$('#submit_auth_required').on('click', function () {
    const _btn = $(this);
    let radio = $('input[name=req-choose-input]:checked').prop('id');
    let firstNameVal = firstName.val().trim();
    let lastNameVal = lastName.val().trim();
    let idCardVal = idCard.val().trim();
    let passportVal = passport.val().trim()
    let flag = false;
    let type = 'id-card';
    if (radio == "req-radio-id-card") {
        type = 'id-card';
        if (idCardVal.length == 10 && $.isNumeric(idCardVal)) {
            flag = true;
        }
    } else {
        type = 'passport';
        if (passportVal.length > 4) {
            flag = true;
        }
    }


    firstName.parent().removeClass('border-danger');
    lastName.parent().removeClass('border-danger');
    idCard.parent().removeClass('border-danger');
    passport.parent().removeClass('border-danger');

    if (firstNameVal.length > 2 && lastNameVal.length > 2 && flag) {
        modalProcessing.show();

        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'auth-required',
            fName: firstNameVal,
            lName: lastNameVal,
            idCard: idCardVal,
            passport: passportVal,
            type: type,
            token: $('#token').val().trim()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                setTimeout(() => {
                    modalProcessing.hide();

                    try {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        const json = JSON.parse(response);

                        if (json.status == 200) {
                            sendNotice(lang_vars.alert_success, lang_vars.alert_success_auth_required, 'success', 3500);
                            window.setTimeout(
                                function () {
                                    location.reload();
                                },
                                2500
                            );
                        } else if (json.status == 208) {
                            sendNotice(lang_vars.alert_success, lang_vars.alert_info_auth_required, 'info', 3500);
                        } else if (json.status == -2) {
                            sendNotice(lang_vars.alert_error, lang_vars.alert_error_auth_first_name, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else if (json.status == -3) {
                            sendNotice(lang_vars.alert_error, lang_vars.alert_error_auth_last_name, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else if (json.status == -3) {
                            sendNotice(lang_vars.alert_error, lang_vars.alert_info_auth_required, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else if (json.status == -4) {
                            sendNotice(lang_vars.alert_error, lang_vars.alert_error_auth_id_card, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else if (json.status == -5) {
                            sendNotice(lang_vars.alert_error, lang_vars.alert_error_auth_passport, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else if (json.status == -6) {
                            sendNotice(lang_vars.alert_error, lang_vars.u_alert_error_auth_id_card_invalid, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        } else {
                            sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                            window.setTimeout(
                                function () {
                                    $('.mj-auth-modal-info').modal("show");
                                },
                                3500
                            );
                        }
                    } catch (e) {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        window.setTimeout(
                            function () {
                                $('.mj-auth-modal-info').modal("show");
                            },
                            3500
                        );
                    }
                }, 500);
            }
        })


    } else {
        if (firstNameVal.length <= 2) {
            firstName.parent().addClass('border-danger');
        }
        if (lastNameVal.length <= 2) {
            lastName.parent().addClass('border-danger');
        }

        if (idCardVal.length != 10) {
            idCard.parent().addClass('border-danger');
        }
        if (passportVal.length <= 4) {
            passport.parent().addClass('border-danger');
        }

    }
});

$('input:radio[name="req-choose-input"]').change(function () {
    let radio = $('input[name=req-choose-input]:checked').prop('id');
    if (radio == "req-radio-id-card") {
        idCard.parents().eq(3).removeClass('d-none');
        passport.parents().eq(3).addClass('d-none');
    } else {
        passport.parents().eq(3).removeClass('d-none');
        idCard.parents().eq(3).addClass('d-none');
    }

});