const cargoName = $('#cargo-name');
const cargoType = $('#cargo-type');
const cargoPackingType = $('#cargo-packing-type');
const cargoCarType = $('#cargo-car-type');
const cargoContainerCount = $('#cargo-container-count');
const cargoStartDate = $('#cargo-start-date');
const cargoStartDateTimestamp = $('#cargo-start-date-ts');
const cargoWeight = $('#cargo-weight');
const cargoNeededCar = $('#cargo-needed-car');
const cargoVolume = $('#cargo-volume');
const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const sourcePort = $('#source-port');
const cargoDestination = $('#cargo-destination');
const destPort = $('#dest-port');
const cargoRecommendedPrice = $('#cargo-recommended-price');
const cargoMonetaryUnit = $('#cargo-monetary-unit');
const cargoDescription = $('#cargo-description');

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));


cargoName.on('input', function () {
    $(this).parent().removeClass('border-danger');
    if ($(this).val() == '') {
        $(this).parent().addClass('border-danger');
    }
});
cargoContainerCount.on('input', function () {
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
cargoPackingType.select2({
    dropdownParent: $('.mj-custom-select.cargo-packing-type'),
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
cargoPackingType.on('change', function () {
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
            action: 'get-ship-cities',
            country: $(this).val(),
            type: 'city'
        };

        const shipParams = {
            action: 'get-ship-cities',
            country: $(this).val(),
            type: 'port'
        };
        $('#cargo-origin').html('');
        $('#source-port').html('');
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
                        <option value="${item.city_id}">${item.city_name} ${item.CityNameEN}</option>
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
            data: JSON.stringify(shipParams),
            success: function (response) {
                try {
                    let html = '';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.port_id}">${item.port_name} ${item.port_nameEN}</option>
                        `;
                    });
                    sourcePort.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
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
            action: 'get-ship-cities',
            country: $(this).val(),
            type: 'city'
        };
        const customsParams = {
            action: 'get-ship-cities',
            country: $(this).val(),
            type: 'port'
        };
        $('#cargo-destination').html('');
        $('#dest-port').html('');
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
                        <option value="${item.city_id}">${item.city_name} ${item.CityNameEN}</option>
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
                        <option value="${item.port_id}">${item.port_name} ${item.port_nameENz}</option>
                        `;
                    });
                    destPort.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
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


sourcePort.select2({
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


sourcePort.on('change', function () {
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


destPort.select2({
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


destPort.on('change', function () {
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
    } else if (cargoPackingType.val() == -1) {
        cargoPackingType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_packaging_error'], 'warning', 2500);
    } else if (cargoStartDateTimestamp.val() == '' || cargoStartDate.val() == '') {
        cargoStartDateTimestamp.parent().addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_date_error'], 'warning', 2500);
    } else if (cargoCarType.val() == -1) {
        cargoCarType.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_container_error'], 'warning', 2500);
    } else if (cargoContainerCount.val() == 0) {
        cargoContainerCount.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_container_count_error'], 'warning', 2500);
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
    } else if (cargoOrigin.val() == -1 || cargoOrigin.val() === undefined || cargoOrigin.val() === null) {
        cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_error'], 'warning', 2500);
    } else if (sourcePort.val() == -1 || sourcePort.val() === undefined || sourcePort.val() === null) {
        sourcePort.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_inquiry_port_source_error'], 'warning', 2500);
    } else if (cargoDestination.val() == -1 || cargoDestination.val() === undefined || cargoDestination.val() === null) {
        cargoDestination.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_dest_error'], 'warning', 2500);
    } else if (destPort.val() == -1 || destPort.val() === undefined || destPort.val() === null) {
        destPort.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars['alert_warning'], lang_vars['b_inquiry_port_dest_error'], 'warning', 2500);
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
        action: 'inquiry-ship',
        name: cargoName.val().trim(),
        category: cargoType.val(),
        packing: cargoPackingType.val(),
        carType: cargoCarType.val(),
        cargoContainerCount: cargoContainerCount.val().replaceAll(',', ''),
        startDate: cargoStartDateTimestamp.val(),
        weight: cargoWeight.val().replaceAll(',', ''),
        volume: cargoVolume.val().replaceAll(',', ''),
        origin: cargoOrigin.val(),
        originCity: cargoOrigin.select2('data')[0].text,
        sourcePort: sourcePort.val(),
        destination: cargoDestination.val(),
        destinationCity: cargoDestination.select2('data')[0].text,
        destPort: destPort.val(),
        description: cargoDescription.val().trim(),
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
                        <img width="72px" src="/dist/images/icons/circle-check-solid-green.svg">
                        <h6>${lang_vars['b_tell_inquiry_ground']}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['u_inquiry_my_list'])}</p>
                        `;
                    $('#submitting-alert').html(html);

                    setTimeout(() => {
                        window.location.href ='/user/ship/inquiry-list';
                    }, 750000000);
                } else {
                    const html = `
                        <img width="72px" src="/dist/images/icons/fe-x-circle-red.svg">
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_ship'])}</h6>
                        `;
                    $('#token').val(json.response);
                    $('#submitting-alert').html(html);
                }

            } catch (e) {
                const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_ship'])}</h6>
                        `;
                $('#submitting-alert').html(html);
            }
        }
    })

});

/*

$('#cargo-container-count,#cargo-weight').on('input', function () {
    const _value = $(this).val().replaceAll(',', '');
    let value = 0;
    if (!isNaN(_value) && _value > 0) {
        console.log(111)
        value = new Intl.NumberFormat().format(_value);
        $(this).val(value);
    } else {

        let x= $(this).val().replace(/\D/g, '')
        x= new Intl.NumberFormat().format(x);
        $(this).val(x);
    }
});
*/


cargoVolume.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value))
    {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    }else{
        $(this).val(addCommas(_value))
    }
});

cargoWeight.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value))
    {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    }else{
        $(this).val(addCommas(_value))
    }
});

cargoContainerCount.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value))
    {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    }else{
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