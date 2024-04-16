Dropzone.autoDiscover = !1;

const ticketSubject = $('#ticket-subject');
const ticketDepartment = $('#ticket-department');
const ticketMessage = $('#ticket-message');
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));
$(".mj-text-inputs-mal").keypress(function (e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if ((charCode > 31 && charCode < 48) || (charCode > 57 && charCode < 65) || (charCode > 90 && charCode < 97) || (charCode > 122 && charCode < 254)) {
        return false;
    }
    return true;
});
let selectedFiles = [];
Dropzone.options.ticektAttachments = {
    url: '/driver/support',
    method: 'post',
    acceptedFiles: 'image/*, application/pdf, application/x-zip-compressed', uploadMultiple: true,
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
                selectedFiles.push(temp);
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
            const index = findItemIndex(file);
            if (index > -1) {
                selectedFiles.splice(index, 1);
            }
            $('#mj-dropzone-progress').html()
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
    });
}

function findItemIndex(file) {
    let deletedIndex = -1;
    selectedFiles.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$('[data-plugin="dropzone"]').each(function () {
    let t = $(this).attr('action');
    let e = $(this).data('previewsContainer');
    let i = {url: t};
    e && (i.previewsContainer = e);
    let o = $(this).data('uploadPreviewTemplate');
    o && (i.previewTemplate = $(o).html());
    $(this).dropzone(i);
});

ticketDepartment.select2({
    dropdownParent: '.mj-custom-select',
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-12">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-12">
                ${title}
            </span>
        `);
    }
});

$('#submit-ticket').on('click', function () {
    const _btn = $(this);

    if (ticketSubject.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_subject, 'warning', 3500);
    } else if (ticketDepartment.val() == -1) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_department, 'warning', 3500);
    } else if (ticketMessage.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_message, 'warning', 3500);
    } else {
        modalProcessing.show();
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        let params = new FormData();
        params.append('action', 'submit-ticket');
        params.append('subject', ticketSubject.val());
        params.append('department', ticketDepartment.val());
        params.append('message', ticketMessage.val());
        params.append('token', $('#token').val());
        selectedFiles.forEach(function (element, index) {
            params.append(index + "", element);
        });

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
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_ticket_list_title'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_ticket_submitted, 'success', 2500);
                        setTimeout(() => {
                            window.location.href = '/user/ticket-list';
                        }, 3000);
                    } else {
                        const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
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
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_ticket'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        })
    }
});