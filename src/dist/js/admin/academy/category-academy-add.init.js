let temp_lang = JSON.parse(var_lang);

let language = $('#language').select2().select2('val');
let child = $('#child').select2().select2('val');

let selectFilesPost = [];
Dropzone.options.uploadPost = {
    url: '/admin/category/academy/add',
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
    let token = $('#token').val().trim();
    let metaTitle = $('#xMetaTitle').val().trim();
    let metaDesc = $('#xMetaDesc').val().trim();
    let schema = $('#xSchema').val().trim();
    let priority = $('#xPriority').val().trim();
    let language = $('#language').select2().select2('val');
    let parent = $('#child').select2().select2('val');

    let BTNN = Ladda.create(document.querySelector('#' + btn));


    if (title.length > 2 && language.length > 3 && selectFilesPost[0]) {

        BTNN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'category-academy-add',
            status: status,
            title: title,
            lang: language,
            metaTitle: metaTitle,
            metaDesc: metaDesc,
            schema: schema,
            priority: priority,
            parent: parent,
            token: token,
        };
        let params = new FormData();
        params.append('action', 'category-academy-add');
        params.append('title', title);
        params.append('status', status);
        params.append('lang', language);
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

                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/academy/edit/" + myArray[1]);
                        },
                        2000
                    );
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

$('#xSchema').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xSchema').html('<b class="text-info">' + len + '</b>');
});

