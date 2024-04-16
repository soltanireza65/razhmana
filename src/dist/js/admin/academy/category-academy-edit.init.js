let temp_lang = JSON.parse(var_lang);

let language = $('#language').select2().select2('val');

let child = $('#child').select2().select2('val');

let selectFilesPost = [];
Dropzone.options.uploadPost = {
    url: '/admin/category/academy/edit',
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
                selectFilesPost.push(temp);
            }
        });
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFilesPost.push(file);
        });
        this.on('removedfile', async function (file) {
            const index = findItemIndex(file);
            if (index > -1) {
                selectFilesPost.splice(index, 1);
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
    selectFilesPost.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$('.setSubmitBtn').click(function () {

    let title = $('#titleCategory').val().trim();
    let btn = $(this).attr('id');
    let cat_id = $('#titleCategory').attr('data-id');
    let token = $('#token').val().trim();
    let metaTitle = $('#xMetaTitle').val().trim();
    let metaDesc = $('#xMetaDesc').val().trim();
    let schema = $('#xSchema').val().trim();
    let priority = $('#xPriority').val().trim();
    let language = $('#language').select2().select2('val');
    let parent = $('#child').select2().select2('val');

    let BTNN = Ladda.create(document.querySelector('#' + btn));

    let img_thumbnail = '';
    if (selectFilesPost.length > 0) {
        img_thumbnail = selectFilesPost[0].dataURL;
    }

    if (title.length > 2) {

        BTNN.start();
        $(".btn").attr('disabled', 'disabled');
        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'category-academy-edit',
            status: status,
            title: title,
            language: language,
            cat_id: cat_id,
            metaTitle: metaTitle,
            metaDesc: metaDesc,
            schema: schema,
            priority: priority,
            parent: parent,
            img: img_thumbnail,
            token: token,
        };
        let params = new FormData();
        params.append('action', 'category-academy-edit');
        params.append('status', status);
        params.append('title', title);
        params.append('language', language);
        params.append('cat_id', cat_id);
        params.append('metaTitle', metaTitle);
        params.append('metaDesc', metaDesc);
        params.append('schema', schema);
        params.append('priority', priority);
        params.append('parent', parent);
        params.append('token', token);
        selectFilesPost.forEach(function (element, index) {
            params.append("img", element);
        });
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: params,
            contentType: false,
            processData: false,
            success: function (data) {

                BTNN.remove();
                $(".btn").removeAttr('disabled');

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


$('#titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_titleCategory').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_titleCategory').html('<b class="text-danger">' + len + '</b>');
    }
});

var len0 = $('#titleCategory').val().trim().length;
if (len0 > 2) {
    $('#length_titleCategory').html('<b class="text-success">' + len0 + '</b>');
} else {
    $('#length_titleCategory').html('<b class="text-danger">' + len0 + '</b>');
}


$('#xMetaTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 70) {
        $('#length_xMetaTitle').html('<b class="text-info">' + len + '</b>');
    } else if (len == 70) {
        $('#length_xMetaTitle').html('<b class="text-success">' + len + '</b>');
    } else if (len > 70) {
        $('#length_xMetaTitle').html('<b class="text-warning">' + len + '</b>');
    }
});
var len1 = $('#xMetaTitle').val().trim().length;
if (len1 > 0 && len1 < 70) {
    $('#length_xMetaTitle').html('<b class="text-info">' + len1 + '</b>');
} else if (len1 == 70) {
    $('#length_xMetaTitle').html('<b class="text-success">' + len1 + '</b>');
} else if (len1 > 70) {
    $('#length_xMetaTitle').html('<b class="text-warning">' + len1 + '</b>');
}

$('#xMetaDesc').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 150) {
        $('#length_xMetaDesc').html('<b class="text-info">' + len + '</b>');
    } else if (len == 150) {
        $('#length_xMetaDesc').html('<b class="text-success">' + len + '</b>');
    } else if (len > 150) {
        $('#length_xMetaDesc').html('<b class="text-warning">' + len + '</b>');
    }
});
var len2 = $('#xMetaDesc').val().trim().length;
if (len2 > 0 && len2 < 150) {
    $('#length_xMetaDesc').html('<b class="text-info">' + len2 + '</b>');
} else if (len2 == 150) {
    $('#length_xMetaDesc').html('<b class="text-success">' + len2 + '</b>');
} else if (len2 > 150) {
    $('#length_xMetaDesc').html('<b class="text-warning">' + len2 + '</b>');
}

$('#xSchema').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xSchema').html('<b class="text-info">' + len + '</b>');
});
var len3 = $('#xSchema').val().trim().length;
$('#length_xSchema').html('<b class="text-info">' + len3 + '</b>');