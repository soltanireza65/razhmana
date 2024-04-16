const commodityCategory = $('#commodity-category');
const commodityWeightSlug = $('#commodity-weight-slug');
const btnCommodityAdd = $('#btn-commodity-add');
const commodityList = $('#commodity-list');
const commodityListEmpty = $('#commodity-list-empty');

const nameModal = $('#commodity-name');
const categoryModal = $('#commodity-category');
const cargo_arrangement = $('#cargo_arrangement');

const weightModal = $('#commodity-weight');
const weightSlugModal = $('#commodity-weight-slug');
const volumeModal = $('#commodity-volume');
const btnAdd = $('#btn-add');
const btnClose = $('#btn_close');


const cargoDescription = $('#cargo-description');
const cargoStartDate = $('#cargo-start-date');
const cargoStartDateTimestamp = $('#cargo-start-date-ts');

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

const nextBtns = document.querySelectorAll(".btn-next");
const prevBtns = document.querySelectorAll(".btn-prev");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".mj-b-progress-step");
let formStepsNum = 0;

let flag = 'add';
let _index = 0;

commodityCategory.select2({
    dropdownParent: $('.commodity-category'),
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
commodityCategory.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

commodityWeightSlug.select2({
    dropdownParent: $('.commodity-weight-parent'),
    minimumResultsForSearch: Infinity,
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
commodityWeightSlug.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

btnAdd.on('click', function () {
    $('#addModal').modal("show");
    flag = 'add';
});

btnCommodityAdd.on('click', function () {

    nameModal.removeClass('border-danger');
    $('.commodity-category .select2-selection.select2-selection--single').removeClass('border-danger');
    weightModal.removeClass('border-danger');
    volumeModal.removeClass('border-danger');

    let name = nameModal.val().trim();
    let category = $('#select2-commodity-category-container').text();
    let categoryId = categoryModal.val();
    let weight = weightModal.val().trim();
    let weightSlug = weightSlugModal.val();
    let volume = volumeModal.val().trim();

    if (name == "") {
        $('#commodity-name').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_enter_name_commodity, 'warning', 2500);
    } else if (category == "") {
        $('.commodity-category .select2-selection.select2-selection--single').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_select_category_commodity, 'warning', 2500);
    } else if (weight == "") {
        $('#commodity-weight').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_enter_weight, 'warning', 2500);
    } else if (volume == "") {
        $('#commodity-volume').addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_enter_volume, 'warning', 2500);
    } else {
        if (flag === 'add') {
            $('#addModal').modal("hide");
            addHtml(name, category, categoryId, weight, weightSlug, volume);
            checkBody();
            resetModal()
        } else if (flag === 'edit') {
            $('#addModal').modal("hide");
            addHtml(name, category, categoryId, weight, weightSlug, volume, _index);
            checkBody()
            resetModal()
        }

    }
})

function addHtml(name, category, categoryId, weight, weightSlug, volume, index = null) {
    let html = '<div class="mj-custom-cargo-card" data-tj-row>' +
        '<div class=" mj-custom-cargo-maintext">' +
        ' <div class="mj-custom-cargo-cat" data-tj-category="' + categoryId + '">' + category + '</div>' +
        '<div class="mj-custom-cargo-name">' +
        ' <span data-tj-name>' + name + '</span>' +
        '</div>' +
        ' <div class="mj-more-balance-text mt-1 px-1">' +

        '<div class="mj-custom-cargo-item">' +
        '<span>' + lang_vars.a_weight + ':</span>' +
        ' <span class="mj-custom-cargo-item-val" ><span data-tj-weight>' + weight + '</span> <span id="custom-weight" data-tj-weightSlug >' + weightSlug + '</span></span>' +
        '</div>' +
        ' <div class="mj-custom-cargo-item">' +
        ' <span>' + lang_vars.a_volume + ':</span>' +
        ' <span class="mj-custom-cargo-item-val"><span data-tj-volume >' + volume + '</span> <span id="custom-volume">' + lang_vars.u_km3 + '</span></span>' +
        ' </div>' +
        '<div class="mj-custom-item-operations">' +
        ' <button class="edit-custom btn-edit">' + lang_vars.edit + '</button>' +
        ' <button class="delete-custom btn-delete">' + lang_vars.u_delete + '</button>' +
        '  </div>' +
        '</div>' +
        '</div>' +
        '<img class="show-balance-more" src="/dist/images/wallet/down-arrow.svg" alt="">' +
        ' </div>';
    if (index === null) {
        commodityList.append(html)
    } else {
        deleteHtml(_index);
        if (_index === 0) {
            commodityList.prepend(html);
        } else {
            $("#commodity-list > div:nth-child(" + (_index) + ")").after(html);
        }
    }
}

function deleteHtml(index) {
    $('[data-tj-row]').eq(index).remove();
}

function checkBody() {
    if (commodityList.html().trim() == '') {
        commodityListEmpty.removeClass('d-none')
    } else {
        commodityListEmpty.addClass('d-none')
    }
}

function resetModal() {
    nameModal.val('');
    categoryModal.val(null).trigger("change")
    weightModal.val('');
    // weightSlugModal.val();
    volumeModal.val('');
}

$(document).on("click", ".btn-edit", function () {
    let _this = $(this);
    let e = _this.parents('[data-tj-row]');

    openModelByValues(
        e.find('[data-tj-name]').text(),
        e.find('[data-tj-category]').attr('data-tj-category'),

        e.find('[data-tj-weight]').text(),
        e.find('[data-tj-weightSlug]').text(),
        e.find('[data-tj-volume]').text()
    );

    flag = 'edit'
    _index = e.index('[data-tj-row]');

    $('#addModal').modal("show");
});

$(document).on("click", ".btn-delete", function () {
    let _this = $(this);
    let e = _this.parents('[data-tj-row]');
    deleteHtml(e.index('[data-tj-row]'))
});

function openModelByValues(name, category, weight, weightSlug, volume) {
    nameModal.val(name);
    // categoryModal.val(null).trigger("change")
    weightModal.val(weight);
    weightSlugModal.val();
    volumeModal.val(volume);
    categoryModal.val(category).trigger('change');

}

btnClose.on('click', function () {
    resetModal();
    $('#addModal').modal("hide");
})

/**********************************************************************************************************************/


nextBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        if (formStepsNum == 0) {
            checkStep1();
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

function checkStep1() {
    let x = new Map();
    commodityList.map(function (key, element) {
        let i = {};
        $(element).find('[data-tj-row]').map(function (key, value) {
            i.name = $(value).find('[data-tj-name]').text();
            i.category = $(value).find('[data-tj-category]').attr('data-tj-category');
            i.weight = $(value).find('[data-tj-weight]').text();
            i.weightslug = $(value).find('[data-tj-weightslug]').text();
            i.volume = $(value).find('[data-tj-volume]').text();
            x.set(key, i)
        });
    });
    let permission = Object.fromEntries(x);

    if (Object.keys(permission).length <= 0) {
        sendNotice(lang_vars.alert_warning, lang_vars.u_enter_hscode_commodity, 'warning', 2500);
    } else {
        formStepsNum++;
        updateFormSteps();
        updateProgressbar();
    }
}

$('#submit-cargo').on('click', function () {


    if (cargoStartDateTimestamp.val() !== '' &&
        cargoOrigin.val() &&
        cargoDestination.val()
    ) {
        // run code

        let x = new Map();
        commodityList.find('[data-tj-row]').map(function (keyI, element) {
            let i = {};
            $(element).map(function (key, value) {
                i.name = $(this).find('[data-tj-name]').text();
                i.category = $(this).find('[data-tj-category]').attr('data-tj-category');
                i.weight = $(this).find('[data-tj-weight]').text();
                i.weightslug = $(this).find('[data-tj-weightslug]').text();
                i.volume = $(this).find('[data-tj-volume]').text();
                x.set(keyI, i)
            });
        });
        let commodity = Object.fromEntries(x);


        modalProcessing.show();
        const params = {
            action: 'inquiry-minicargo',
            startDate: cargoStartDateTimestamp.val(),
            source_city_id:cargoOrigin.val(),
            dest_city_id:cargoDestination.val(),
            cargo_arrangement:cargo_arrangement.is('checked'),
            description: cargoDescription.val().trim(),
            commodity: JSON.stringify(commodity),
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
                       <img width="72px" src="/dist/images/icons/circle-check-solid-green.svg">
                       <h6>${lang_vars['b_tell_inquiry_ground']}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['u_inquiry_my_list'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        setTimeout(() => {
                            window.location.href = '/user/minicargo/inquiry-list';
                        }, 7500);
                    } else {
                        const html = `
                        <img width="72px" src="/dist/images/icons/fe-x-circle-red.svg">
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_minicargo'])}</h6>
                        `;
                        $('#token').val(json.response);
                        $('#submitting-alert').html(html);
                    }

                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['b_inquiry_air'])}</h6>
                        `;
                    $('#submitting-alert').html(html);
                }
            }
        })
        // end ajax
    } else {
        if (cargoOrigin.val() == "" || cargoOrigin.val() == null) {
            cargoOrigin.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
            sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_city_error'], 'warning', 2500);
        } else if (cargoDestination.val() == "" || cargoDestination.val() == null) {
            cargoDestination.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
            sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_dest_error'], 'warning', 2500);
        } else if (cargoStartDateTimestamp.val() == '' || cargoStartDate.val() == '') {
            cargoStartDateTimestamp.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_date_error, 'warning', 2500);
        } else {
            sendNotice(lang_vars.alert_warning, lang_vars.login_unknown_error, 'warning', 2500);
        }
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

$(document).on("click", ".show-balance-more", function () {
    if ($(this).attr('src') == '/dist/images/wallet/down-arrow.svg') {
        $(this).parent().addClass("balanceopen")
        $(this).attr('src', '/dist/images/wallet/up-arrow.svg')
    } else {
        $(this).parent().removeClass("balanceopen")
        $(this).attr('src', '/dist/images/wallet/down-arrow.svg')
    }
});


const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const cargoDestination = $('#cargo-destination');


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
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
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
