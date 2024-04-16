let temp_lang = JSON.parse(var_lang);

// Create a new instance of ladda for the specified button



let selectFiles = [];

// myAwesomeDropzone  id
Dropzone.options.avatarAdmin = {
    url: 'myaccount',
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
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            //     $('#avatarAdminUrl').val('');
            }
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
            // console.log('completed');
            // window.location.reload();
        });
    }
};


$('#btnActive').click(function () {
    let token = $('#token').val().trim();
    let nickname = $('#nicknameAdmin').val().trim();
    let password = $('#passwordAdmin').val().trim();
    // let avatar = $('#avatarAdminUrl').val().trim();
    let avatar = '';
    if (selectFiles.length > 0) {
        avatar = selectFiles[0].dataURL;
    }
    const btnActive = Ladda.create(document.querySelector('#btnActive'));

    if (nickname.length > 2 && password.length > 7 && token.length > 7) {

        $("#btnActive").attr('disabled', 'disabled');

        btnActive.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'myaccount-edit',
            nickname: nickname,
            password: password,
            avatar: avatar,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                btnActive.remove();
                $(".btn").removeAttr('disabled');
                if (data == 'successful') {
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
                } else if (data == "avatar_error") {
                    toastNotic(temp_lang.error, temp_lang.avatar_error);
                } else if (data == "pass_error") {
                    toastNotic(temp_lang.error, temp_lang.pass_error);
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                }else if (data == "token_error") {
                    // toastNotic(temp_lang.error, temp_lang.token_error);
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        if (password.length < 8) {
            toastNotic(temp_lang.warning, temp_lang.pass_error, 'warning');
        }
        if (nickname.length < 3) {
            toastNotic(temp_lang.warning, temp_lang.nickname_error, 'warning');
        }
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

var nickname = $('#nicknameAdmin').val().trim().length;
if (nickname > 2) {
    $('#length_nicknameAdmin').html('<b class="text-success">' + nickname + '</b>');
} else {
    $('#length_nicknameAdmin').html('<b class="text-danger">' + nickname + '</b>');
}


$('#passwordAdmin').keyup(function () {
    var lenp = $(this).val().trim().length;
    if (lenp > 7) {
        $('#length_passwordAdmin').html('<b class="text-success">' + lenp + '</b>');
    } else {
        $('#length_passwordAdmin').html('<b class="text-danger">' + lenp + '</b>');
    }
});
var Lpass = $('#passwordAdmin').val().trim().length;
if (Lpass > 7) {
    $('#length_passwordAdmin').html('<b class="text-success">' + Lpass + '</b>');
} else {
    $('#length_passwordAdmin').html('<b class="text-danger">' + Lpass + '</b>');
}