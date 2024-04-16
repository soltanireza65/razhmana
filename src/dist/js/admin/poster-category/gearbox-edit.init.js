let temp_lang = JSON.parse(var_lang);

let selectFiles_uploadImage = [];
Dropzone.options.uploadImage = {
    url: '/admin/category/gearbox/edit',
    method: 'post',
    acceptedFiles: 'image/*,.gif, .ico',
    uploadMultiple: false,
    autoProcessQueue: true,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,


    accept: function (file, done) {
        done();
        this.on('thumbnail', function (file, dataURL) {
        });
    },

    init: function () {
        this.on('success', async function (file) {
            if (file.accepted) {
                const content = await fileRead(file);

                const temp = new File([content], file.name, {
                    type: file.type,
                    webkitRelativePath: file.webkitRelativePath,
                    lastModifiedDate: file.lastModifiedDate,
                    lastModified: file.lastModified,
                });
                selectFiles_uploadImage.push(temp);
            }
        });
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_uploadImage.push(file);
        });
        this.on('removedfile', async function (file) {
            const index = findItemIndex(file);
            if (index > -1) {
                selectFiles_uploadImage.splice(index, 1);
            }
        });
    }
};

function fileRead(file) {
    return new Promise((resolve, reject) => {
        let reader = new FileReader();
        reader.onload = () => {
            resolve(reader.result);
        };

        reader.onerror = () => {
            reject(reader.result);
        };

        reader.readAsArrayBuffer(file)
        // reader['readAsText'](file);
    });
}

function findItemIndex(file) {
    let deletedIndex = -1;
    selectFiles_uploadImage.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$('.setSubmitBtn').click(function () {

    let title = $(".titleCategory").map(function () {

        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();
    let title_save = "[" + title + "]";
    let btn = $(this).prop('id');
    let id = $(this).data('tj-id');
    let imageD = $(this).attr('data-tj-image');
    let token = $('#token').val().trim();
    let priority = $('#priority').val().trim();


    let BTN = Ladda.create(document.querySelector('#' + btn));

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });

    let Image = '';
    if (selectFiles_uploadImage.length > 0) {
        Image = selectFiles_uploadImage[0].dataURL;
    }

    if (flag && parseInt(id) > 0) {

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }
        if (imageD == "empty" && !selectFiles_uploadImage[0]) {
            toastNotic(temp_lang.error, temp_lang.a_upload_image_binding);
        } else {
            $(".btn").attr('disabled', 'disabled');
            BTN.start();
            let params = new FormData();
            params.append('action', 'gearbox-edit');
            params.append('title', title_save);
            params.append('status', status);
            params.append('priority', priority);
            params.append('token', token);
            params.append('id', id);
            selectFiles_uploadImage.forEach(function (element, index) {
                params.append("image", element);
            });
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: params,
                contentType: false,
                processData: false,
                success: function (data) {

                    BTN.remove();
                    $(".setSubmitBtn").removeAttr('disabled');

                    if (data == 'successful') {
                        $('.setSubmitBtn').attr('data-tj-image', 'full');
                        toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                        if (Image.length > 20) {
                            $(".btn").attr('disabled', 'disabled');
                            window.setTimeout(
                                function () {
                                    location.reload();
                                },
                                2000
                            );
                        }
                    } else if (data == "empty") {
                        toastNotic(temp_lang.error, temp_lang.empty_input);
                    } else if (data == "token_error") {
                        toastNotic(temp_lang.error, temp_lang.token_error);
                    } else if (data == "error_upload_image") {
                        toastNotic(temp_lang.error, temp_lang.error_upload_image);
                    } else {
                        toastNotic(temp_lang.error, temp_lang.error_mag);
                    }
                }
            });
        }
    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


$('.titleCategory').keyup(function () {
    let len = $(this).val().trim().length;
    let idid = $(this).attr('data-id');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});

let len = $('.titleCategory');
len.each(function (index, element) {
    let lennn = $(element).val().trim().length;
    let idid = $(element).attr('data-id');
    if (lennn > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + lennn + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + lennn + '</b>');
    }
});
