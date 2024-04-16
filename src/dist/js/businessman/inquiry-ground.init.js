const cargoName = $('#cargo-name');
const cargoType = $('#cargo-type');
const cargoCarType = $('#cargo-car-type');
const cargoStartDate = $('#cargo-start-date');
const cargoStartDateTimestamp = $('#cargo-start-date-ts');
const cargoWeight = $('#cargo-weight');
const cargoNeededCar = $('#cargo-needed-car');
const cargoVolume = $('#cargo-volume');
const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const cargoCustomsOfOrigin = $('#cargo-customs-of-origin');
const cargoDestination = $('#cargo-destination');
const cargoDestinationCustoms = $('#cargo-destination-customs');
const cargoRecommendedPrice = $('#cargo-recommended-price');
const cargoMonetaryUnit = $('#cargo-monetary-unit');
const cargoDescription = $('#cargo-description');
let selectedFiles = [];

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

cargoName.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
});


cargoType.select2({
    dropdownParent: $('.mj-custom-select.cargo-type'),
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
        }
    }
});


cargoNeededCar.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
});


cargoOriginCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin-country'),
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
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities',
            country: $(this).val(),
            city: 'city',
            type: 'ground'
        };

        const customsParams = {
            action: 'get-cities',
            country: $(this).val(),
            city: 'customs',
        };
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
                        <option value="${item.CityId}">${item.CityName} ${item.CityNameEN}</option>
                        `;
                    });
                    cargoOrigin.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, login_unknown_error, 'error', 2500);
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
                        <option value="${item.CityId}">${item.CityName} ${item.CityNameEN}</option>
                        `;
                    });
                    cargoCustomsOfOrigin.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});

cargoDestCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-dest-country'),
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
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities',
            country: $(this).val(),
            city: 'city',
            type: 'ground'
        };

        const customsParams = {
            action: 'get-cities',
            country: $(this).val(),
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
                        <option value="${item.CityId}">${item.CityName} ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestination.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, login_unknown_error, 'error', 2500);
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
                        <option value="${item.CityId}">${item.CityName} ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestinationCustoms.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});


cargoOrigin.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin'),
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
    $(this).parent().removeClass('border-danger');
    const value = new Intl.NumberFormat().format(fixNumbers($(this).val()).replaceAll(',', ''));
    if (isNaN(value.replaceAll(',', ''))) {
        $(this).val(0);
        $(this).parent().addClass('border-danger');
    } else if (value == 0) {
        $(this).parent().addClass('border-danger');
    } else {
        $(this).val(value);
    }
});


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
    } else if (cargoType.val() == -1) {
        cargoType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_type_error'], 'warning', 2500);
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
    if (cargoNeededCar.val() <= 0 || cargoNeededCar.val() == '') {
        cargoNeededCar.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_needed_car_error'], 'warning', 2500);
    } else if (cargoOrigin.val() == -1) {
        cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_error'], 'warning', 2500);
    } else if (cargoCustomsOfOrigin.val() == -1) {
        cargoCustomsOfOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_customs_error'], 'warning', 2500);
    } else if (cargoDestination.val() == -1) {
        cargoDestination.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_dest_error'], 'warning', 2500);
    } else if (cargoDestinationCustoms.val() == -1) {
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

    modalProcessing.show();
    const params = {
        action: 'inquiry-ground',
        name: cargoName.val(),
        category: cargoType.val(),
        carType: cargoCarType.val(),
        startDate: cargoStartDateTimestamp.val(),
        weight: cargoWeight.val(),
        volume: cargoVolume.val(),
        origin: cargoOrigin.val(),
        originCity: cargoOrigin.select2('data')[0].text,
        customsOfOrigin: cargoCustomsOfOrigin.val(),
        destination: cargoDestination.val(),
        destinationCity: cargoDestination.select2('data')[0].text,
        destinationCustoms: cargoDestinationCustoms.val(),
        description: cargoDescription.val(),
        token: $('#token').val()
    };
    console.log(params);
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
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_ground'])}</h6>
                        <h6>${lang_vars['b_tell_inquiry_ground']}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['u_home_page'])}</p>
                        `;
                    $('#submitting-alert').html(html);

                    setTimeout(() => {
                        window.location.href ='/';
                    }, 7500);
                } else {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_ground'])}</h6>
                        `;
                    $('#token').val(json.response);
                    $('#submitting-alert').html(html);
                }

            } catch (e) {
                const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_ground'])}</h6>
                        `;
                $('#submitting-alert').html(html);
            }
        }
    })

});
