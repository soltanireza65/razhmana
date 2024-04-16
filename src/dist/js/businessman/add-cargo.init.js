Dropzone.autoDiscover = !1;
const cargoName = $('#cargo-name');
const cargoType = $('#cargo-type');
const cargoCarType = $('#cargo-car-type');
const cargoStartDate = $('#cargo-start-date');
const cargoStartDateTimestamp = $('#cargo-start-date-ts');
const cargoWeight = $('#cargo-weight');
const cargoNeededCar = $('#cargo-needed-car');
// const cargoVolume = $('#cargo-volume');
const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const cargoCustomsOfOrigin = $('#cargo-customs-of-origin');
const cargoDestination = $('#cargo-destination');
const cargoDestinationCustoms = $('#cargo-destination-customs');
const cargoRecommendedPrice = $('#cargo-recommended-price');
const cargoMonetaryUnit = $('#cargo-monetary-unit');
const cargoDescription = $('#cargo-description');
const greenStreet = $('#green-street');
let selectedFiles = [];

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

Dropzone.options.cargoImages = {
    url: '/businessman/add-cargo',
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
    if ($(this).val() == 18) {
        cargoWeight.attr('data-max', 9998)
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
        } else if (parseInt($(this).val().replace(',', '')) > $(this).attr('data-max')) {
            $(this).val($(this).attr('data-max'));
        }
    }
});


cargoNeededCar.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
    if ($(this).val().substring(0, 1) == '0') {
        $(this).val($(this).val().substring(1));
    }

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
        // console.log($(this).val().trim())

        const customsParams = {
            action: 'get-cities',
            country: $(this).val().trim(),
            city: 'customs',
        };
        if ($(this).val().trim() == 1) {
            greenStreet.parents().eq(1).removeClass('d-none')
        } else {
            greenStreet.parents().eq(1).addClass('d-none')
            greenStreet.prop('checked', false)
            cargoCustomsOfOrigin.attr('disabled', false)
        }
        $('#cargo-origin').html('');
        $('#cargo-customs-of-origin').html('');
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
                        <option value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
                    });
                    cargoOrigin.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(customsParams),
            success: function (response) {
                try {
                    let html = '';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
                    cargoCustomsOfOrigin.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});

cargoDestCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-dest-country'),
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

cargoDestCountry.on('change', function () {
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

        const customsParams = {
            action: 'get-cities',
            country: $(this).val().trim(),
            city: 'customs',
        };
        $('#cargo-destination').html('');
        $('#cargo-destination-customs').html('');
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
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestination.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(customsParams),
            success: function (response) {
                try {
                    let html = '';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestinationCustoms.append(html);
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


cargoCustomsOfOrigin.select2({
    dropdownParent: $('.mj-custom-select.cargo-customs-of-origin'),
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


cargoCustomsOfOrigin.on('change', function () {
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


cargoDestinationCustoms.select2({
    dropdownParent: $('.mj-custom-select.cargo-destination-customs'),
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


cargoDestinationCustoms.on('change', function () {
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


// cargoRecommendedPrice.on('input', function () {
//     $(this).parent().removeClass('border-danger');
//     let x= $(this).val().replace(/\D/g, '')
//     const value = new Intl.NumberFormat().format(x);
//     if (isNaN(value.replaceAll(',', ''))) {
//         $(this).val(0);
//         $(this).parent().addClass('border-danger');
//     } else if (value == 0) {
//         $(this).parent().addClass('border-danger');
//     } else {
//         $(this).val(value);
//     }
// });


cargoMonetaryUnit.on('change', function () {
    $(this).removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).addClass('border-danger');
    }
});


function checkStep1() {
    if (cargoName.val().trim() == '' || cargoName.val().length < 2) {
        cargoName.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
    } else if (cargoType.val() == "") {
        cargoType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_type_error'], 'warning', 2500);
    } else if (cargoCarType.val() == "") {
        cargoCarType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_car_type_error'], 'warning', 2500);
    } else if (cargoStartDateTimestamp.val() == '' || cargoStartDate.val() == '') {
        cargoStartDateTimestamp.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_date_error'], 'warning', 2500);
    } else {
        formStepsNum++;
        updateFormSteps();
        updateProgressbar();
    }
}


function checkStep2() {
    if (cargoWeight.val() <= 0 || cargoWeight.val() > 25 || cargoWeight.val() == '') {
        cargoWeight.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_weight_error'], 'warning', 2500);
    } else if (cargoNeededCar.val() <= 0 || cargoNeededCar.val() == '') {
        cargoNeededCar.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_needed_car_error'], 'warning', 2500);
    } else if (cargoOrigin.val() == "" || cargoOrigin.val() == null) {
        cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_error'], 'warning', 2500);
    } else if ((cargoOriginCountry.val().trim() == 1 && !greenStreet.is(':checked') && cargoCustomsOfOrigin.val() == "") ||
        (cargoOriginCountry.val().trim() != 1 && cargoCustomsOfOrigin.val() == "")) {
        cargoCustomsOfOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_customs_error'], 'warning', 2500);
    } else if (cargoDestination.val() == "" || cargoDestination.val() == null) {
        cargoDestination.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_dest_error'], 'warning', 2500);
    } else if (cargoDestinationCustoms.val() == "" || cargoDestinationCustoms.val() == null) {
        cargoDestinationCustoms.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_dest_customs_error'], 'warning', 2500);
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


$('#submit-cargo').on('click', function () {
    if (cargoRecommendedPrice.val() == '') {
        cargoRecommendedPrice.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_recommended_price_error'], 'warning', 2500);
    } else if (cargoMonetaryUnit.val() == -1 || cargoMonetaryUnit.val() == '') {
        cargoMonetaryUnit.addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_monetary_unit_error'], 'warning', 2500);
    } else {
        modalProcessing.show();
        const params = {
            action: 'submit-cargo',
            name: cargoName.val().trim(),
            category: cargoType.val(),
            carType: cargoCarType.val(),
            startDate: cargoStartDateTimestamp.val(),
            weight: cargoWeight.val(),
            volume: 0,
            neededCar: cargoNeededCar.val(),
            origin: cargoOrigin.val(),
            originCity: cargoOrigin.select2('data')[0].text,
            customsOfOrigin: cargoCustomsOfOrigin.val(),
            destination: cargoDestination.val(),
            destinationCity: cargoDestination.select2('data')[0].text,
            destinationCustoms: cargoDestinationCustoms.val(),
            recommendedPrice: cargoRecommendedPrice.val().replaceAll(',', ''),
            currency: cargoMonetaryUnit.val(),
            description: cargoDescription.val().trim(),
            greenStreet: greenStreet.is(':checked'),
            images: selectedFiles,
            token: $('#token').val().trim()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                console.log(response)
                setTimeout(() => {
                    modalProcessing.hide();
                    modalSubmitting.show();
                }, 500);

                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        const html = `
                        <img width="72px" class="mb-3" src="/dist/images/icons/circle-check-solid-green.svg">
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['b_submit_cargo'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['b_my_cargoes'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        setTimeout(() => {
                            window.location.href = '/businessman/my-cargoes';
                        }, 2500);
                    } else {
                        const html = `
                         <img width="72px" class="mb-3" src="/dist/images/icons/fe-x-circle-red.svg">
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_submit_cargo'])}</h6>
                        `;
                        $('#token').val(json.response);
                        $('#submitting-alert').html(html);
                    }

                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_submit_cargo'])}</h6>
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

let tempGreenName = '';
greenStreet.change(function () {
    if (greenStreet.is(':checked')) {
        cargoCustomsOfOrigin.attr('disabled', true)
        tempGreenName = cargoCustomsOfOrigin.parent().find('.mj-custom-select-item').text();
        cargoCustomsOfOrigin.parent().find('.mj-custom-select-item').text(lang_vars.u_green_street_customs)
    } else {
        cargoCustomsOfOrigin.attr('disabled', false)
        cargoCustomsOfOrigin.parent().find('.mj-custom-select-item').text(tempGreenName)
    }
});



