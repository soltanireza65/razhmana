let temp_lang = JSON.parse(var_lang);

let selectFiles_uploadImage = [];
Dropzone.options.uploadImage = {
    url: '/admin/category/brand/add',
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

$('#xType').select2({
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + temp_lang.u_no_result_found + "</span>");
        }
    },
});


$('.setSubmitBtn').click(function () {
    let id = $(this).attr('id');
    let token = $('#token').val().trim();
    var title = $(".titleCategory").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();
    let title_save = "[" + title + "]";
    let priority = $('#priority').val().trim();
    let xType = $('#xType').select2().select2('val');

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 2) {
            flag = false;
        }
    });


    if (flag && selectFiles_uploadImage[0] && xType.length > 0) {

        let BTN = Ladda.create(document.querySelector('#' + id));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (id == 'btnActive') {
            status = "active";
        }

        let params = new FormData();
        params.append('action', 'brand-add');
        params.append('title', title_save);
        params.append('status', status);
        params.append('priority', priority);
        params.append('type', xType);
        params.append('token', token);
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
                $(".btn").removeAttr('disabled');

                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    //
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/brand/edit/" + myArray[1]);
                        },
                        2000
                    );
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

    } else {
        if (selectFiles_uploadImage.length < 1) {
            toastNotic(temp_lang.error, temp_lang.a_upload_image_binding);
        } else if (xType.length <= 0) {
            toastNotic(temp_lang.error, temp_lang.a_type_2_no_select);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }

});


$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});
