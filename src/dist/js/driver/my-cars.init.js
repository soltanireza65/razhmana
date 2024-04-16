const plaqueType = $('#plaque-type');
const carType = $('#car-type');
const carName = $('#car-name');
const plaqueNumber = $('#plaque-number')
let selectedFiles = [];
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

Dropzone.autoDiscover = false;
Dropzone.options.carImages = {
    url: '/driver/my-cars',
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

carType.select2({
    dropdownParent: $('.mj-custom-select.car-type'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    placeholder: {
        id: '-1',
        text: '<img width="20px" src="/dist/images/icons/truck-blue.svg" class="me-1"  /> '+lang_vars.d_my_cars_choose_car_type
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;
        const image = (data.element && $(data.element).data('image')) ? $(data.element).data('image') : '';

        return $(`
            <span class="mj-custom-select-item">
                ${(image) ? `<img width="20px" src="${image}" class="me-1" alt="" />` : ''}
                ${title}
            </span>
        `);
    }
});

plaqueType.select2({
    dropdownParent: $('.mj-custom-select.plaque-type'),
    minimumResultsForSearch: -1,
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item d-block">
                ${title}
            </span>
        `);
    }
});



$('#submit-car').on('click', function () {
    const _btn = $(this);

    if (carName.val().trim() == "") {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_name, 'warning', 3500);
    } else if (carType.val().trim() == -1 || carType.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_type, 'warning', 3500);
    }
    /*else if (plaqueType.val().trim() == '' || plaqueType.val().trim() == -1) {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_plaque_type, 'warning', 3500);
    } else if (plaqueNumber.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_plaque, 'warning', 3500);
    }*/
    else {
        modalProcessing.show();

        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'new-car',
            car: carType.val().trim(),
            type: plaqueType.val().trim(),
            name: carName.val().trim(),
            plaque: plaqueNumber.val().trim(),
            images: selectedFiles,
            token: $('#token').val().trim()
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
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['submit_car'])}</h6>
                        `;
                        $('#submitting-alert').html(html);

                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_new_car, 'success', 2500);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000)
                    } else {
                        const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_car'])}</h6>
                        `;
                        $('#submitting-alert').html(html);

                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });

                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['submit_car'])}</h6>
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