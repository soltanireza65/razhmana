let temp_lang = JSON.parse(var_lang);

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

const btnSubmit = Ladda.create(document.querySelector('#submit'));

$('#submit').click(function () {
    let mail = $('#emailaddress').val().trim();
    let resullt_mail = validateEmail(mail);
    let check = $('#checkbox-signin').is(":checked");
    let pass = $('#password').val().trim();


    if (resullt_mail && pass.length > 7) {
        btnSubmit.start();
        sendRequest(mail, pass, check);

    } else {
        if (!resullt_mail && pass.length < 8) {
            toastNotic(temp_lang.error, temp_lang.email_pass_error);
        } else if (!resullt_mail) {
            toastNotic(temp_lang.error, temp_lang.email_error);
        } else {
            toastNotic(temp_lang.error, temp_lang.pass_error);
        }

    }
});

$("#password").on("keydown", function search(e) {
    if (e.keyCode == 13) {
        let mail = $('#emailaddress').val().trim();
        let resullt_mail = validateEmail(mail);
        let check = $('#checkbox-signin').is(":checked");
        let pass = $('#password').val().trim();


        if (resullt_mail && pass.length > 7) {
            btnSubmit.start();
            sendRequest(mail, pass, check);

        } else {
            if (!resullt_mail && pass.length < 8) {
                toastNotic(temp_lang.error, temp_lang.email_pass_error);
            } else if (!resullt_mail) {
                toastNotic(temp_lang.error, temp_lang.email_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.pass_error);
            }

        }
    }
});

function sendRequest(mail, pass, check) {
    let data = {
        action: 'login-admin',
        mail: mail,
        pass: pass,
        check: check,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
// console.log(data)
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