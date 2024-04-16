let temp_lang = JSON.parse(var_lang);

$('#btnDelete').click(function () {
    let btnDelete = Ladda.create(document.querySelector('#btnDelete'));
    let portID = $(this).data('port-id');
    let name = $('#port_name').text();
    let token = $('#token').val().trim();

    if (portID > 0) {
        btnDelete.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'port-delete',
            portID: portID,
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
                            window.location.replace("/admin/category/port");
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