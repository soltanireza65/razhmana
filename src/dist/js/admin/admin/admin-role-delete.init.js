let temp_lang = JSON.parse(var_lang);

let category = $('#replacementRole').select2().select2('val');

$('#btnDelete').click(function () {
    var btnDelete = Ladda.create(document.querySelector('#btnDelete'));

    let id = $(this).attr("data-id");
    let replacementRole = $('#replacementRole').select2().select2('val');

    if (id.length > 0 && replacementRole && replacementRole.length > 0 && parseInt(replacementRole) > 0) {

        $("#btnDelete").attr('disabled', 'disabled');
        btnDelete.start();
        let data = {
            action: 'admin-role-delete',
            token: $('#token').val().trim(),
            replacementRole: replacementRole,
            id: id,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                console.log(data)
                btnDelete.remove();
                $("#btnDelete").attr('disabled', 'disabled');
                if (data == "successful") {
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/admin");
                        },
                        2000
                    );
                } else if (data == "end") {
                    toastNotic(temp_lang.warning, temp_lang.delete_but_error, 'warning');
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else if (data == "id_used") {
                    toastNotic(temp_lang.error, temp_lang.role_exist);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });

    } else {
        if (!replacementRole || replacementRole.length <= 0 || parseInt(replacementRole) <= 0) {
            toastNotic(temp_lang.error, temp_lang.replacement_role_error);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }

});