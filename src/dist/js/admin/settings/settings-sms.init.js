let temp_lang = JSON.parse(var_lang);
$('#btnActive').on('click', function () {
    const btnActive = Ladda.create(document.querySelector('#btnActive'));
    let sms_panel = $('input[name=sms_panel]:checked').attr('id');
    let ghasedak_api = $('#ghasedak_api').val().trim();
    let ghasedak_sender_number = $('#ghasedak_sender_number').val().trim();
    let ghasedak_price_low = $('#ghasedak_price_low').val().trim();
    let ghasedak_admins_mobile = $('#ghasedak_admins_mobile').val().trim();
    let ghasedak_template_low_price = $('#ghasedak_template_low_price').val().trim();
    btnActive.start();
    let data = {
        action: 'settings-sms',
        sms_panel: sms_panel,
        ghasedak_api: ghasedak_api,
        ghasedak_sender_number: ghasedak_sender_number,
        ghasedak_price_low: ghasedak_price_low,
        ghasedak_admins_mobile: ghasedak_admins_mobile,
        ghasedak_template_low_price: ghasedak_template_low_price,
        token: $('#token').val().trim(),
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            btnActive.remove();
            if (data == "successful") {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
                // $("#submit_main").removeAttr('disabled');
                // window.setTimeout(
                //     function () {
                //         location.reload();
                //     },
                //     2000
                // );
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
                $("#submit_main").removeAttr('disabled');
            }
        }
    });
});
