let temp_lang = JSON.parse(var_lang);

let selectFiles_uploadIcon = [];
Dropzone.options.uploadIcon = {
    url: '/admin/category/car/edit',
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
                selectFiles_uploadIcon.push(temp);
            }
        });
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_uploadIcon.push(file);
        });
        this.on('removedfile', async function (file) {
            const index = findItemIndex(file);
            if (index > -1) {
                selectFiles_uploadIcon.splice(index, 1);
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
    selectFiles_uploadIcon.map(function (element, index) {
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
    const BTN = Ladda.create(document.querySelector('#' + btn));


    if (flag && parseInt(id) != 0 && id.length > 0) {
        BTN.start();
        $(".btn").attr('disabled', 'disabled');
        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }
        let params = new FormData();
        params.append('action', 'category-car-edit');
        params.append('title', title_save);
        params.append('status', status);
        params.append('id', id);
        params.append('token', token);
        selectFiles_uploadIcon.forEach(function (element, index) {
            params.append("icon", element);
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
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "error_upload_icon") {
                    toastNotic(temp_lang.error, temp_lang.error_upload_icon);
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