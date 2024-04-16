let temp_lang = JSON.parse(var_lang);

Dropzone.autoDiscover = false;
let selectFilesPost_attachmentsDropzone = [];
Dropzone.options.attachmentsDropzone = {
    url: '/task/add',
    method: 'post',
    acceptedFiles: 'image/*, application/pdf, application/x-zip-compressed',
    uploadMultiple: true,
    maxFiles: 10,
    autoProcessQueue: true,

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
                selectFilesPost_attachmentsDropzone.push(temp);
            }
        });

        this.on('uploadprogress', (file, progress, bytesSent) => {
            if (file.accepted) {
                if (file.upload.progress == 100) {
                    $(file['previewElement']).find('*[data-dz-progress]').removeClass('progress-bar-striped').addClass('bg-success');
                }
                $(file['previewElement']).find('*[data-dz-progress]').css("width", file.upload.progress + "%").text(`${Math.floor(file.upload.progress)}%`);
            }
        });

        this.on('removedfile', async function (file) {
            const index = selectFilesPost_attachmentsDropzone.indexOf(file.dataURL);
            if (index > -1) {
                selectFilesPost_attachmentsDropzone.splice(index, 1);
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
    selectFilesPost_attachmentsDropzone.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$(document).ready(function () {
    $('[data-plugin="dropzone"]').each(function () {
        let t = $(this).attr('action');
        let e = $(this).data('previewsContainer');
        let i = {url: t};
        e && (i.previewsContainer = e);
        let o = $(this).data('uploadPreviewTemplate');
        o && (i.previewTemplate = $(o).html());
        $(this).dropzone(i);
    });
});

$('#xTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_xTitle').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_xTitle').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#xDescription').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xDescription').html('<b class="text-info">' + len + '</b>');
});


$('#btnSend').click(function () {

    let title = $('#xTitle').val().trim();
    let desc = $('#xDescription').val().trim();
    let token = $('#token').val().trim();
    let taskId = $('#btnSend').data('tj-task-id');

    if (title.length > 2 && taskId > 0) {

        var BTN = Ladda.create(document.querySelector('#btnSend'));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let params = new FormData();
        params.append('action', 'task-info');
        params.append('title', title);
        params.append('desc', desc);
        params.append('taskId', taskId);
        params.append('token', token);

        selectFilesPost_attachmentsDropzone.forEach(function (element, index) {
            params.append(index + "", element);
        });

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: params,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data)
                $(".setSubmitBtn").removeAttr('disabled');
                BTN.remove();
                if (data == 'successful') {
                    $(".setSubmitBtn").attr('disabled', 'disabled');

                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.reload();
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
        if (title.length <= 2) {
            toastNotic(temp_lang.error, temp_lang.a_empty_title);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
})


$('.setSubmitBtn').click(function () {

    let btn = $(this).prop('id');
    let token = $('#token').val().trim();
    let taskId = $('#btnSend').data('tj-task-id');

    if (taskId > 0) {

        var BTN = Ladda.create(document.querySelector('#'+btn));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let data = {
            action: 'task-info-status',
            status: btn,
            taskId: taskId,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                console.log(data)
                BTN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.reload();
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
        if (title.length <= 2) {
            toastNotic(temp_lang.error, temp_lang.a_empty_title);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
})