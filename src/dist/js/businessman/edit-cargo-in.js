Dropzone.autoDiscover = !1;
const cargoName = $('#cargo-name');
const cargoType = $('#cargo-type');
const cargoCarType = $('#cargo-car-type');
const cargoStartDate = $('#cargo-start-date');
const cargoStartDateTimestamp = $('#cargo-start-date-ts');
const cargoWeight = $('#cargo-weight');
const cargoNeededCar = $('#cargo-needed-car');
const cargoOriginCountry = $('#cargo-origin-country');
const cargoOrigin = $('#cargo-origin');
const cargoDestination = $('#cargo-destination');
const cargoRecommendedPrice = $('#cargo-recommended-price');
const cargoMonetaryUnit = $('#cargo-monetary-unit');
const cargoDescription = $('#cargo-description');
let selectedFiles = [];

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

Dropzone.options.cargoImages = {
    url: '/businessman/edit-cargo-in',
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


cargoName.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
});


cargoType.select2({
    dropdownParent: $('.mj-custom-select.cargo-type'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

cargoType.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


cargoCarType.select2({
    dropdownParent: $('.mj-custom-select.cargo-car-type'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

cargoCarType.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


let calendarSetting;
if (getCookie('language')) {
    let language = getCookie('language');
    if (language.substr(0, 2) === 'fa') {

        calendarSetting = {
            format: 'YYYY/MM/DD',
            altField: '#cargo-start-date-ts',
            altFormat: 'X',
            minDate: Date.now(),
            viewMode: 'year',
            navigator: {
                enabled: true,
                scroll: {
                    enabled: true
                },
                text: {
                    btnNextText: "<",
                    btnPrevText: ">"
                }
            },
            "toolbox": {
                "enabled": true,
                "calendarSwitch": {
                    "enabled": true,
                    "format": '',

                },
                "todayButton": {
                    "enabled": true,
                    "text": {
                        "fa": "برو به امروز",
                        "en": "select today"
                    }
                },
                "submitButton": {
                    "enabled": true,
                    "text": {
                        "fa": "انتخاب",
                        "en": "select"
                    }
                },
                "text": {
                    "btnToday": "امروز"
                }
            },
            onSelect: function (unixDate) {
                cargoStartDate.parent().removeClass('border-danger');
            }
        }
    } else {
        calendarSetting = {
            format: 'YYYY/MM/DD',
            altField: '#cargo-start-date-ts',
            altFormat: 'X',
            minDate: Date.now(),
            viewMode: 'year',
            calendarType: 'gregorian',
            navigator: {
                enabled: true,
                scroll: {
                    enabled: true
                },
                text: {
                    btnNextText: "next",
                    btnPrevText: "perv"
                }
            },
            "toolbox": {
                "enabled": true,
                "calendarSwitch": {
                    "enabled": true,
                    "format": ''
                },
                "todayButton": {
                    "enabled": true,
                    "text": {
                        "fa": "برو به امروز",
                        "en": "select today"
                    }
                },
                "submitButton": {
                    "enabled": true,
                    "text": {
                        "fa": "انتخاب",
                        "en": "select"
                    }
                },
                "text": {
                    "btnToday": "امروز"
                },

            },
            onSelect: function (unixDate) {
                cargoStartDate.parent().removeClass('border-danger');

            }
        }
    }

}
cargoStartDate.persianDatepicker(calendarSetting);
$(document).on('click', '.pwt-btn-calendar', function () {
    cargoStartDate.persianDatepicker(calendarSetting);
});

cargoWeight.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    } else {
        $(this).val(fixNumbers($(this).val()));
        if ($(this).val() <= 0) {
            $(this).val(1);
        } else if ($(this).val() > $(this).data('max')) {
            $(this).val($(this).data('max'));
        }
    }
});


cargoNeededCar.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
    // if ($(this).val().substring(0, 1) == '0') {
    //     $(this).val($(this).val().substring(1));
    // }
});


cargoOriginCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin-country'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    sorter: function (results) {
        var query = $('.select2-search__field').val().toLowerCase();
        return results.sort(function (a, b) {
            return a.text.toLowerCase().indexOf(query) -
                b.text.toLowerCase().indexOf(query);
        });
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

cargoOriginCountry.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities',
            country: $(this).val().trim(),
            city: 'city',
            type: 'ground'
        };

        $('#cargo-origin').html('');
        $('#cargo-destination').html('');
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(citiesParams),
            success: function (response) {
                try {
                    let html = '';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName} ${item.CityNameEN}</option>
                        `;
                    });
                    cargoOrigin.append(html);
                    cargoDestination.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });

    }
});


cargoOrigin.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    sorter: function (results) {
        var query = $('.select2-search__field').val().toLowerCase();
        return results.sort(function (a, b) {
            return a.text.toLowerCase().indexOf(query) -
                b.text.toLowerCase().indexOf(query);
        });
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

cargoOrigin.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


cargoDestination.select2({
    dropdownParent: $('.mj-custom-select.cargo-destination'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    sorter: function (results) {
        var query = $('.select2-search__field').val().toLowerCase();
        return results.sort(function (a, b) {
            return a.text.toLowerCase().indexOf(query) -
                b.text.toLowerCase().indexOf(query);
        });
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

cargoDestination.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

cargoRecommendedPrice.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value)) {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    } else {
        $(this).val(addCommas(_value))
    }
});

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


cargoMonetaryUnit.on('change', function () {
    $(this).removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).addClass('border-danger');
    }
});


function checkStep1() {
    if (cargoName.val().trim() == '' || cargoName.val().length < 2) {
        cargoName.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_name_error, 'warning', 2500);
    } else if (cargoType.val() == "") {
        cargoType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_type_error, 'warning', 2500);
    } else if (cargoCarType.val() == "") {
        cargoCarType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_car_type_error, 'warning', 2500);
    } else if (cargoStartDateTimestamp.val() == '' || cargoStartDate.val() == '') {
        cargoStartDateTimestamp.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_date_error, 'warning', 2500);
    } else {
        formStepsNum++;
        updateFormSteps();
        updateProgressbar();
    }
}


function checkStep2() {
    if (cargoWeight.val() <= 0 || cargoWeight.val() > 25 || cargoWeight.val() == '') {
        cargoWeight.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_weight_error, 'warning', 2500);
    } else if (cargoNeededCar.val() <= 0 || cargoNeededCar.val() == '') {
        cargoNeededCar.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_needed_car_error, 'warning', 2500);
    } else if (cargoOriginCountry.val() == "" || cargoOriginCountry.val() == null) {
        cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_select_country, 'warning', 2500);
    } else if (cargoOrigin.val() == "" || cargoOrigin.val() == null) {
        cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_city_error, 'warning', 2500);
    } else if (cargoDestination.val() == "" || cargoDestination.val() == null) {
        cargoDestination.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_dest_error, 'warning', 2500);
    } else {
        formStepsNum++;
        updateFormSteps();
        updateProgressbar();
    }
}


const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".mj-b-progress-step");

let formStepsNum = 0;

nextBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        if (formStepsNum == 0) {
            checkStep1();
        } else if (formStepsNum == 1) {
            checkStep2()
        }
        window.scrollTo(0, 0);
    });
});

prevBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        formStepsNum--;
        updateFormSteps();
        updateProgressbar();
    });
});

function updateFormSteps() {
    formSteps.forEach((formStep) => {
        formStep.classList.contains("form-step-active") &&
        formStep.classList.remove("form-step-active");
    });

    formSteps[formStepsNum].classList.add("form-step-active");
}

function updateProgressbar() {
    progressSteps.forEach((progressStep, idx) => {
        if (idx < formStepsNum + 1) {
            progressStep.classList.add("mj-b-progress-step-active");
        } else {
            progressStep.classList.remove("mj-b-progress-step-active");
        }
    });

    const progressActive = document.querySelectorAll(".mj-b-progress-step-active");

    progress.style.width =
        ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
}


function removeImage(element) {
    const index = defaultImages.indexOf($(element).data('image'));
    if (index > -1) {
        defaultImages.splice(index, 1);
        $('div[data-remove="' + $(element).data('image') + '"]').remove();
    }
}


$('#submit-cargo').on('click', function () {
    if (cargoRecommendedPrice.val() == '') {
        cargoRecommendedPrice.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_recommended_price_error'], 'warning', 2500);
    } else if (cargoMonetaryUnit.val() == -1 || cargoMonetaryUnit.val() == '') {
        cargoMonetaryUnit.addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_monetary_unit_error'], 'warning', 2500);
    } else {
        modalProcessing.show();
        const cargoId = $(this).data('cargo');

        const params = {
            action: 'edit-cargo-in',
            cargo: $(this).data('cargo'),
            name: cargoName.val().trim(),
            category: cargoType.val(),
            carType: cargoCarType.val(),
            startDate: cargoStartDateTimestamp.val(),
            weight: cargoWeight.val(),
            neededCar: cargoNeededCar.val(),
            origin: cargoOrigin.val(),
            destination: cargoDestination.val(),
            recommendedPrice: cargoRecommendedPrice.val().replaceAll(',', ''),
            currency: cargoMonetaryUnit.val(),
            description: cargoDescription.val().trim(),
            defaultImages: defaultImages,
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
                        <img width="72px" class="mb-3" src="/dist/images/icons/circle-check-solid-green.svg">
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['b_edit_cargo'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['b_title_cargo_detail'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        setTimeout(() => {
                            window.location.href = `/businessman/cargo-in-detail/${cargoId}`;
                        }, 2500);
                    } else {
                        const html = `
                        <img width="72px" class="mb-3" src="/dist/images/icons/fe-x-circle-red.svg">
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_edit_cargo'])}</h6>
                        `;
                        $('#token').val(json.response);
                        $('#submitting-alert').html(html);
                    }

                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_edit_cargo'])}</h6>
                        `;
                    $('#submitting-alert').html(html);
                }
            }
        })
    }
});


$('#cargo-weight').on('input', function () {
    const _value = $(this).val().replaceAll(',', '');
    let value = 0;
    if (!isNaN(_value) && _value > 0) {
        value = new Intl.NumberFormat().format(_value);
        $(this).val(value);
    } else {
        let x = $(this).val().replace(/\D/g, '')
        x = new Intl.NumberFormat().format(x);
        $(this).val(x);
    }
});