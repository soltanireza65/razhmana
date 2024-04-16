let temp_lang = JSON.parse(var_lang);

let selectFiles_uploadIcon = [];
// myAwesomeDropzone  id
Dropzone.options.uploadIcon = {
    url: 'category-cargo-edit',
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
            selectFiles_uploadIcon.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_uploadIcon.indexOf(file);
            if (index > -1) {
                selectFiles_uploadIcon.splice(index, 1); // 2nd parameter means remove one item only
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


let selectFiles_uploadImage = [];
// myAwesomeDropzone  id
Dropzone.options.uploadImage = {
    url: 'category-cargo-edit',
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
            selectFiles_uploadImage.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_uploadImage.indexOf(file);
            if (index > -1) {
                selectFiles_uploadImage.splice(index, 1); // 2nd parameter means remove one item only
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


$('.setSubmitBtn').click(function () {


    let title = $(".titleCategory").map(function () {

        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();


    let title_save = "[" + title + "]";
    let btn = $(this).attr('id');
    let id = $('.setSubmitBtn').attr('data-id');


    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });


    let token = $('#token').val().trim();


    var BTN = Ladda.create(document.querySelector('#' + btn));



    let Icon = '';
    if (selectFiles_uploadIcon.length > 0) {
        Icon = selectFiles_uploadIcon[0].dataURL;
    }


    let Image= '';
    if (selectFiles_uploadImage.length > 0) {
        Image = selectFiles_uploadImage[0].dataURL;
    }
// console.log(Icon);
// console.log(Image);
    let color = $('#assetColor').val().trim();


    if (flag == true && parseInt(id) != 0 && id.length > 0) {
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'category-cargo-edit',
            status: status,
            id: id,
            title: title_save,
            token: token,
            color: color,
            icon: Icon,
            image: Image,
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

                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
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
                } else if (data == "error_upload_icon") {
                    toastNotic(temp_lang.error, temp_lang.error_upload_icon);
                }else if (data == "error_upload_image") {
                    toastNotic(temp_lang.error, temp_lang.error_upload_image);
                }else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });

    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});


$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});


var len = $('.titleCategory');
len.each(function (index, element) {
    let lennn = $(element).val().trim().length;
    var idid = $(element).attr('data-id');
    if (lennn > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + lennn + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + lennn + '</b>');
    }
});
