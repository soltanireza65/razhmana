let temp_lang = JSON.parse(var_lang);

$('#btnDelete').click(function () {
    let btnDelete = Ladda.create(document.querySelector('#btnDelete'));
    let id = $(this).attr('data-post-id');
    let token = $('#token').val().trim();

    if (id.length > 0) {
        btnDelete.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'post-delete',
            id: id,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                btnDelete.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "info");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/post");
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