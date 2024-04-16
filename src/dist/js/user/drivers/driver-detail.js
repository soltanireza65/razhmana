$('#change-role-status').click(function () {
    let cv_id = $(this).data('cv-id')
    let role_status = $(this).data('cv-role-status')
    let token = $('#token2').val().trim();
    let data = {
        action: 'change-driver-cv-status',
        cv_id: cv_id,
        role_status: role_status,
        token: token,
    };
    $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '/api/ajax',
        data: JSON.stringify(data),
        success: function (data) {


            if (data == 'successful') {
                sendNotice(lang_vars.successful, lang_vars.successful_update_mag, "success");

            } else if (data == "empty") {
                sendNotice(lang_vars.error, lang_vars.empty_input);
            } else if (data == "token_error") {
                sendNotice(lang_vars.error, lang_vars.token_error);
            } else {
                sendNotice(lang_vars.error, lang_vars.error_mag);
            }
            $(this).prop('disabled', false);

            location.reload();

        }
    });
})