let temp_lang = JSON.parse(var_lang);
if ($('#SetAdminID').length > 0) {
    $('#SetAdminID').click(function () {
        let token = $('#token').val().trim();
        let complaint = $('#complaintID').data('mj-complaint-id');
        var BTN = Ladda.create(document.querySelector('#SetAdminID'));

        if (complaint && complaint > 0) {
            BTN.start();
            let data = {
                action: 'complaint-in-set-admin',
                token: token,
                complaint: complaint,
            };

            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    BTN.remove();
                    $(".setSubmitBtn").removeAttr('disabled');

                    if (data == 'successful') {
                        $(".btn").attr('disabled', true);

                        toastNotic(temp_lang.successful, temp_lang.successful_admin_set, "success");
                        window.setTimeout(
                            function () {
                                location.reload();
                            },
                            2000
                        );
                    } else if (data == "before_set") {
                        toastNotic(temp_lang.warning, temp_lang.before_admin_set, 'info');
                        window.setTimeout(
                            function () {
                                location.reload();
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
}

if ($('#textAreaComplaintBTN').length > 0) {
    $('#textAreaComplaintBTN').click(function () {
        let token = $('#token').val().trim();
        let complaint = $('#complaintID').data('mj-complaint-id');
        let desc = $('#textAreaComplaint').val().trim();
        var BTN = Ladda.create(document.querySelector('#textAreaComplaintBTN'));

        if (complaint && complaint > 0 && token.length > 0 && desc.length > 2) {
            BTN.start();
            let data = {
                action: 'complaint-in-set-closed',
                token: token,
                desc: desc,
                complaint: complaint,
            };

            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    BTN.remove();
                    $(".setSubmitBtn").removeAttr('disabled');

                    if (data == 'successful') {
                        $(".btn").attr('disabled', 'disabled');

                        toastNotic(temp_lang.successful, temp_lang.successful_admin_set, "success");
                        window.setTimeout(
                            function () {
                                location.reload();
                            },
                            2000
                        );
                    } else if (data == "before_set") {
                        toastNotic(temp_lang.warning, temp_lang.before_admin_closed, 'info');
                        window.setTimeout(
                            function () {
                                location.reload();
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


    $('#textAreaComplaint').keyup(function () {
        var len = $('#textAreaComplaint').val().length;
        if (len > 2) {
            $('#length_textAreaComplaint').html('<b class="text-success">' + len + '</b>');
        } else {
            $('#length_textAreaComplaint').html('<b class="text-danger">' + len + '</b>');
        }
    });


}