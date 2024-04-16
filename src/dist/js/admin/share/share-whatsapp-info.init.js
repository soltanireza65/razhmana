let temp_lang = JSON.parse(var_lang);

$('.btnSubmit').click(function () {
    let _this = $(this);
    let id = _this.prop('id');
    let btNN = Ladda.create(document.querySelector('#' + id));
    let waID = $(this).data('tj-id');
    let status = $(this).data('tj-status');
    let token = $('#token').val().trim();

    if (id.length > 0) {
        btNN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'share-whatsapp-info-status',
            waID: waID,
            status: status,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                btNN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "info");
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});