Dropzone.autoDiscover = !1;
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

let selectedFiles = [];
Dropzone.options.attachments = {
    url: '/driver/start-transportation-in',
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
            $('#mj-dropzone-progress').html ()
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

$('#submit-start').on('click', function () {
    modalProcessing.show();

    const _btn = $(this);
    _btn.fadeOut(300);
    const cargo = $(this).data('cargo');
    const params = {
        action: 'start-transportation-in',
        cargo: $(this).data('cargo'),
        request: $(this).data('request'),
        images: selectedFiles,
        token: $('#token').val()
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
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
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['d_start_transportation_title'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_cargo_detail_title'])}</p>
                        `;
                    $('#submitting-alert').html(html);

                    sendNotice(lang_vars.alert_success, lang_vars.d_alert_start_transportation, 'success', 2500);
                    setTimeout(() => {
                        window.location.href = `/driver/cargo-in/${cargo}`;
                    }, 3000)
                } else {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['d_start_transportation_title'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    $('#token').val(json.response);
                    _btn.fadeIn(300);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {
                const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['d_start_transportation_title'])}</h6>
                        `;
                $('#submitting-alert').html(html);

                _btn.fadeIn(300);
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
});

function submitComplaint(element) {
    const _btn = $(this);
    _btn.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });

    const params = {
        action: 'submit-complaint-in',
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