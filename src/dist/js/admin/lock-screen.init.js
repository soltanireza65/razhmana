const temp_lang = JSON.parse(var_lang);
const btnSubmit = Ladda.create(document.querySelector('#submit'));
$('#submit').click(function () {
    let pass = $('#password').val().trim();


    if (pass.length > 7) {
        btnSubmit.start();
        sendRequest(pass);
    } else {
        toastNotic(temp_lang.error, temp_lang.pass_error);
    }
});

$("#password").on("keydown", function search(e) {
    if (e.keyCode == 13) {
        let pass = $(this).val().trim();
        if (pass.length > 7) {
            btnSubmit.start();
            sendRequest(pass);
        } else {
            toastNotic(temp_lang.error, temp_lang.pass_error);
        }
    }
});

function sendRequest(pass) {

    let data = {
        action: 'lock-screen-admin',
        pass: pass,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            btnSubmit.remove();
            if (data == "successful") {
                window.location.replace("/admin");
            } else if (data == "user_not_find") {
                toastNotic(temp_lang.error, temp_lang.admin_not_find);
            } else if (data == "admin_block_time") {
                toastNotic(temp_lang.error, temp_lang.admin_block_time);
            } else if (data == "admin_status_inactive") {
                toastNotic(temp_lang.error, temp_lang.admin_status_inactive);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });
}