let temp_lang = JSON.parse(var_lang);


let selectFiles_jibitIcon = [];
// myAwesomeDropzone  id
Dropzone.options.jibitIcon = {
    url: 'settings-payment',
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
            }
            selectFiles_jibitIcon.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_jibitIcon.indexOf(file);
            if (index > -1) {
                selectFiles_jibitIcon.splice(index, 1); // 2nd parameter means remove one item only
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


$('#btnSubmit').on('click', function () {
    let btnSubmit = Ladda.create(document.querySelector('#btnSubmit'));


    let jibit_merchantid = $('#jibit_merchantid').val().trim();
    let jibit_status = $('#jibit_status').is(':checked');
    let jibit_icon = '';
    if (selectFiles_jibitIcon.length > 0) {
        jibit_icon = selectFiles_jibitIcon[0].dataURL;
    }


    btnSubmit.start();
    let data = {
        action: 'settings-payment',
        jibit_merchantid: jibit_merchantid,
        jibit_status: jibit_status,
        jibit_icon: jibit_icon,
        token: $('#token').val().trim(),
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            btnSubmit.remove();
            if (data == "successful") {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
                // window.setTimeout(
                //     function () {
                //         location.reload();
                //     },
                //     2000
                // );
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
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


});