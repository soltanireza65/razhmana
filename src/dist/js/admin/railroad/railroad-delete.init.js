let temp_lang = JSON.parse(var_lang);

$('#btnDelete').click(function () {
    let btnDelete = Ladda.create(document.querySelector('#btnDelete'));
    let railroadID = $(this).data('railroad-id');
    let name = $('#railroad_name').text();
    let token = $('#token').val().trim();

    if (railroadID > 0) {
        btnDelete.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'railroad-delete',
            railroadID: railroadID,
            name: name,
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
                    toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/railroad");
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