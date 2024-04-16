Dropzone.autoDiscover = !1;
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

const receipt = $('#receipt');
let receiptFile = null;

// receipt.on('change', function () {
//     receiptFile = $(this).prop('files')[0];
// });

/*
let selectedFiles = [];
Dropzone.options.attachments = {
    url: '/driver/end-transportation',
    method: 'post',
    acceptedFiles: 'image/*',
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
                selectedFiles.push(file.dataURL);
            }
        });

        this.on('uploadprogress', (file, progress, bytesSent) => {
            if (file.accepted) {
                if (file.upload.progress == 100) {
                    $(file['previewElement']).find('*[data-dz-progress]').parent().parent().children('.mj-dropzone-progress').html(lang_vars.dropzone_progress_completed)
                    $(file['previewElement']).find('*[data-dz-progress]').removeClass('progress-bar-striped').addClass('bg-success');
                }
                $(file['previewElement']).find('*[data-dz-progress]').css("width", file.upload.progress + "%").text(`${Math.floor(file.upload.progress)}%`);
            }
        });

        this.on('removedfile', async function (file) {
            const index = selectedFiles.indexOf(file.dataURL);
            if (index > -1) {
                selectedFiles.splice(index, 1);
            }
            $('#mj-dropzone-progress').html()
        });
    }
};

$('[data-plugin="dropzone"]').each(function () {
    let t = $(this).attr('action');
    let e = $(this).data('previewsContainer');
    let i = {url: t};
    e && (i.previewsContainer = e);
    let o = $(this).data('uploadPreviewTemplate');
    o && (i.previewTemplate = $(o).html());
    $(this).dropzone(i);
});
*/
$('#submit-end').on('click', function () {
    modalProcessing.show();

    const _btn = $(this);
    _btn.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });

    let img_thumbnail = '';
    if (selectFilesPost.length > 0) {
        img_thumbnail = selectFilesPost[0].dataURL;
    }

    const rate = ($('input[name="rating"]:checked').length == 0) ? 0 : $('input[name="rating"]:checked').val();
    const cargo = $(this).data('cargo');
    let params = new FormData();
    params.append('action', 'end-transportation-in');
    params.append('cargo', $(this).data('cargo'));
    params.append('request', $(this).data('request'));
    params.append('receipt', img_thumbnail);
    params.append('images', JSON.stringify(selectedFiles));
    params.append('rate', rate.toString());
    params.append('token', $('#token').val());

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: params,
        contentType: false,
        processData: false,
        success: function (response) {
            setTimeout(() => {
                modalProcessing.hide();
                modalSubmitting.show();
            }, 500);

            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    const html = `
                        <i class="fe-check-circle d-block text-success mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['d_end_transportation_title'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_cargo_detail_title'])}</p>
                        `;
                    $('#submitting-alert').html(html);

                    sendNotice(lang_vars.alert_success, lang_vars.d_alert_end_transportation, 'success', 2500);
                    setTimeout(() => {
                        window.location.href =`/driver/cargo-in/${cargo}`;
                    }, 3000)
                } else if (json.status == 420) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['d_end_transportation_extra_error'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    $('#token').val(json.response);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                } else {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['d_end_transportation_title'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    $('#token').val(json.response);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {
                const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['d_end_transportation_title'])}</h6>
                        `;
                $('#submitting-alert').html(html);

                _btn.removeAttr('disabled').css({
                    opacity: 1
                });
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
});

function submitComplaint(element) {
    const _btn = $(element);
    _btn.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });

    const params = {
        action: 'submit-complaint',
        cargo: $(element).data('cargo'),
        request: $(element).data('request'),
        to: $(element).data('businessman'),
        token: $('#token-complaint').val(),
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    sendNotice(lang_vars.alert_success, lang_vars.d_alert_success_complaint, 'success', 2500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    $('#token-complaint').val(json.response);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {
                _btn.removeAttr('disabled').css({
                    opacity: 1
                });
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
}
//



// start Dropzone
let selectFilesPost = [];
Dropzone.options.uploadThumbnail = {
    url: 'product/add',
    method: 'post',
    acceptedFiles: 'image/*',
    maxFilesize: 10,
    maxThumbnailFilesize: 10,
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: lang_vars.delete,
    dictMaxFilesExceeded: lang_vars.dictMaxFilesExceeded,
    dictCancelUpload: lang_vars.cancel_upload,
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
            selectFilesPost.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFilesPost.indexOf(file);
            if (index > -1) {
                selectFilesPost.splice(index, 1); // 2nd parameter means remove one item only
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



Dropzone.autoDiscover = false;
let selectedFiles = [];
Dropzone.options.attachments = {
    url: '/product/add',
    method: 'post',
    acceptedFiles: 'image/*',
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
                selectedFiles.push(file.dataURL);
            }
        });

        this.on('uploadprogress', (file, progress, bytesSent) => {
            if (file.accepted) {
                if (file.upload.progress == 100) {
                    $(file['previewElement']).find('*[data-dz-progress]').parent().parent().children('.mj-dropzone-progress').html(lang_vars.dropzone_progress_completed)
                    $(file['previewElement']).find('*[data-dz-progress]').removeClass('progress-bar-striped').addClass('bg-success');
                }
                $(file['previewElement']).find('*[data-dz-progress]').css("width", file.upload.progress + "%").text(`${Math.floor(file.upload.progress)}%`);
            }
        });

        this.on('removedfile', async function (file) {
            const index = selectedFiles.indexOf(file.dataURL);
            if (index > -1) {
                selectedFiles.splice(index, 1);
            }
        });
    }
};


$('[data-plugin="dropzone"]').each(function () {
    let t = $(this).attr('action');
    let e = $(this).data('previewsContainer');
    let i = {url: t};
    e && (i.previewsContainer = e);
    let o = $(this).data('uploadPreviewTemplate');
    o && (i.previewTemplate = $(o).html());
    $(this).dropzone(i);
});
