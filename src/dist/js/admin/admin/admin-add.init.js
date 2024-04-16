let rolesAdmin = $('#rolesAdmin').select2().select2('val');

let temp_lang = JSON.parse(var_lang);

let selectFiles = [];

// myAwesomeDropzone  id
Dropzone.options.avatarAdmin = {
    url: 'admin-add',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,
    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {
            // $('#avatarAdminUrl').val(file.dataURL);
        });
    },
    init: function () {
        this.on('addedfile', function (file) {
            // if (this.files[1] != null) {
            //     this.removeFile(this.files[0]);
            //     $('#avatarAdminUrl').val('');
            // }
            selectFiles.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles.indexOf(file);
            if (index > -1) {
                selectFiles.splice(index, 1); // 2nd parameter means remove one item only
            }
            // console.log(selectFiles)
        });
        this.on('totaluploadprogress', function (progress) {
            $('#auth-uploader').css('width', progress + '%').html(progress + '%');
        });

        this.on('success', function (progress) {
            console.log('completed');
            // window.location.reload();
        });
    }
};

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validatePhone(number) {
    const re = /^0([9])\d{9}$/;
    return re.test(String(number).toLowerCase());
}

function validatePassword(password) {
    if (password.length > 7) {
        var output = password.split('');
        let flagU = false;
        let flagL = false;
        let flagN = false;
        $.each(output, function (i, e) {
            // var isNumber = Number.isInteger(e);
            var isNumber = /^[0-9]{1}/.test(e);
            if (isNumber) {
                flagN = true;
            } else {
                if (/^[a-z]{1}/.test(e)) {
                    flagU = true;
                }
                if (/^[A-Z]{1}/.test(e)) {
                    flagL = true;
                }
            }
        });
        if (flagU && flagL && flagN) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

$('.setSubmitBtn').click(function () {

    let name = $('#nameAdmin').val().trim();
    let nickname = $('#nicknameAdmin').val().trim();
    let email = $('#emailAdmin').val().trim();
    let password = $('#passwordAdmin').val().trim();
    let mobile = $('#mobileAdmin').val().trim();
    // let avatar = $('#avatarAdminUrl').val().trim();
    let avatar = '';
    if (selectFiles.length > 0) {
        avatar = selectFiles[0].dataURL;
    }

    let role = $('#rolesAdmin').val();
    let btn = $(this).attr('id');
    let BTNN = Ladda.create(document.querySelector('#' + btn));

    if (name.length > 2 && nickname.length > 2 && validatePhone(mobile) && validateEmail(email) && validatePassword(password) && avatar.length > 20) {

        $(".btn").attr('disabled', 'disabled');
        BTNN.start();
        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'admin-add',
            status: status,
            name: name,
            nickname: nickname,
            email: email,
            password: password,
            mobile: mobile,
            avatar: avatar,
            role: role,
            token: $('#token').val().trim(),
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTNN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    // $(".setSubmitBtn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            // window.location.replace("/admin/admin");
                        },
                        2000
                    );
                } else if (data == "email_exist") {
                    toastNotic(temp_lang.error, temp_lang.email_exist);
                } else if (data == "phone_exist") {
                    toastNotic(temp_lang.error, temp_lang.phone_exist);
                } else if (data == "avatar_error") {
                    toastNotic(temp_lang.error, temp_lang.avatar_error);
                } else if (data == "email_pass_error") {
                    toastNotic(temp_lang.error, temp_lang.email_pass_error);
                } else if (data == "pass_error") {
                    toastNotic(temp_lang.error, temp_lang.pass_error);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else if (data == "mobile_invalid") {
                    toastNotic(temp_lang.error, temp_lang.mobile_invalid);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });

    } else {
        if (validateEmail(email) == false) {
            toastNotic(temp_lang.error, temp_lang.email_error);
        }
        if (password.length < 8) {
            toastNotic(temp_lang.error, temp_lang.pass_error);
        }
        if (name.length < 3) {
            toastNotic(temp_lang.error, temp_lang.name_error);
        }
        if (nickname.length < 3) {
            toastNotic(temp_lang.error, temp_lang.nickname_error);
        }
        if (avatar.length < 50) {
            toastNotic(temp_lang.error, temp_lang.avatar_error);
        }
        if (mobile.length != 11) {
            toastNotic(temp_lang.error, temp_lang.mobile_invalid);
        }
        // toastNotic(temp_lang.warning, temp_lang.empty_input, 'warning');
    }
});


$('#nameAdmin').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_nameAdmin').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_nameAdmin').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#nicknameAdmin').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_nicknameAdmin').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_nicknameAdmin').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#mobileAdmin').keyup(function () {
    var len = validatePhone($(this).val().trim());
    if (len) {
        $('#length_mobileAdmin').html('<b class="text-success">' + temp_lang.validate_phone + '</b>');
    } else {
        $('#length_mobileAdmin').html('<b class="text-danger">' + temp_lang.invalidate_phone + '</b>');
    }
});

$('#emailAdmin').keyup(function () {
    var len = validateEmail($(this).val().trim());
    if (len) {
        $('#length_emailAdmin').html('<b class="text-success">' + temp_lang.validate_email + '</b>');
    } else {
        $('#length_emailAdmin').html('<b class="text-danger">' + temp_lang.invalidate_email + '</b>');
    }
});

$('#passwordAdmin').keyup(function () {
    var len = validatePassword($(this).val().trim());
    if (len) {
        $('#length_passwordAdmin').html('<b class="text-success">' + temp_lang.validate_password + '</b>');
    } else {
        $('#length_passwordAdmin').html('<b class="text-danger">' + temp_lang.invalidate_password + '</b>');
    }
});