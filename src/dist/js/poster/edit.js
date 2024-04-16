Dropzone.autoDiscover = !1;

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

/** Start Type Poster  **/

// type-truck
// type-trailer
const typePoster = $('input[name="type-poster"]');
const statusPoster = $('input[name="status-poster"]');
const firstItemOne = $('#first-item-one-poster');
const SecondItemOne = $('#second-item-one-poster');
const SecondItemTwo = $('#second-item-two-poster');
const btnFirstNextItemOne = $('#btn-first-next-item-one-poster');
let myType='truck';

typePoster.each(function () {
    $(this).change(function () {
        if ($(this).prop('id') == "type-truck") {
            // firstItemOne.fadeOut(500);

            $('.mj-a-status-parent-poster').parent().fadeIn(500)
        } else {
            $('.mj-a-status-parent-poster').parent().fadeOut(500)
        }
    });
});

btnFirstNextItemOne.on('click', function () {
    if ($('input[name="type-poster"]:checked').prop('id') == "type-truck") {
        firstItemOne.fadeOut(500, function () {
            window.scrollTo(0, 0);
            SecondItemOne.fadeIn(500);
        });
        myType='truck';
        getBrands('truck')
        getProperty('truck')
    } else {
        firstItemOne.fadeOut(500, function () {
            window.scrollTo(0, 0);
            SecondItemTwo.fadeIn(500);
        });
        myType='trailer';
        getBrands('trailer')
        getProperty('trailer')
    }


    let statusRun = '';
    $('[name="status-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            statusRun = $(this).prop('id');
        }
    });

    if (statusRun == "status-new") {
        runTruck.parent().parent().addClass('d-none');
    } else {
        runTruck.parent().parent().removeClass('d-none');
    }

});


function getProperty(type) {
    const params = {
        action: 'get-property-poster',
        type: type,
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            const json = JSON.parse(response);
            // if (json.status == 200) {
            // modalXbrand.attr('data-tj-type', type)
            setHtmlProperty(json)
            // }
        }
    });
}
$("#close-brand-modal").on('click', function () {
    $('#choose-brand-modal').modal('hide')
})

$("#close-model-modal").on('click', function () {
    $('#choose-model-modal').modal('hide')
})


function setHtmlProperty(loop) {
    let myP = $('.mj-filter-option-list').data('tj-property').toString();
    let myParray = myP.split(',');
    let temp = '';

    for (let i = 0; i < loop.length; ++i) {
        let checkedP = '';
        for (let j = 0; j < myParray.length; j++) {
            if (loop[i].id == myParray[j]) {
                checkedP = 'checked';
            }
        }

        temp = temp + '<label> <input type="checkbox" ' + checkedP + ' data-option-id="' + loop[i].id + '"> <div class="mj-filter-option-item"> <div class="mj-filter-option-img"> <img src="' + loop[i].image + '" alt="option"> </div><span>' + loop[i].name + '</span></div></label>';
    }
    $('.mj-filter-option-list').html(temp);
}

/**
 * trailer
 */
const brandTrailer = $('#brand-trailer');
const modelBTrailer = $('#model-b-trailer');
const modelTrailer = $('#model-trailer');
const axisTrailer = $('#axis-trailer');
const priceTrailer = $('#price-trailer');
const currencyTrailer = $('#currency-trailer');
const cashTrailer = $('#cash-trailer');
const leasingTrailer = $('#leasing-trailer');
const installmentTrailer = $('#installment-trailer');

const btnSecondBackItemTwoPoster = $('#btn-second-back-item-two-poster')
const btnSecondNextItemTwoPoster = $('#btn-second-next-item-two-poster')


brandTrailer.click(function () {
    ModalBrand.modal('show');
    brandTrailer.parent().removeClass('border-danger');
    // $('#modal-x-model').val(exchangeSaleModel.prop('id'));
});

modelBTrailer.click(function () {
    modelBTrailer.parent().removeClass('border-danger');
    if (brandTrailer.attr('data-mj-value')) {
        ModalModel.modal('show');
    } else {
        brandTrailer.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_barnd, 'warning', 2500);
    }
});

modelTrailer.select2({
    // minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-7'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
modelTrailer.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


axisTrailer.select2({
    minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-1'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
axisTrailer.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

priceTrailer.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value)) {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    } else {
        $(this).val(addCommas(_value))
    }
});

$("[data-tj-check-1]").on('input', function () {
    let _this = $(this);
    $('#second-item-two-poster').find('[data-tj-check-1]').prop('checked', false);
    _this.prop('checked', true);
})

btnSecondBackItemTwoPoster.on('click', function () {
    SecondItemTwo.fadeOut(500, function () {
        window.scrollTo(0, 0);
        firstItemOne.fadeIn(500);
    });
});

btnSecondNextItemTwoPoster.on('click', function () {


    modelTrailer.parent().removeClass('border-danger');
    axisTrailer.parent().removeClass('border-danger');
    priceTrailer.parent().removeClass('border-danger');
    currencyTrailer.removeClass('border-danger');

    let statusRun = '';
    $('[name="status-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            statusRun = $(this).prop('id');
        }
    });

    if (modelTrailer.val().trim() == '') {
        modelTrailer.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_trailer, 'warning', 2500);
    } else if (axisTrailer.val().trim() == '') {
        axisTrailer.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_axis, 'warning', 2500);
        // } else if (priceTrailer.val().trim().length <= 0) {
        //     priceTrailer.parent().addClass('border-danger');
        //     sendNotice(lang_vars.alert_warning ,lang_vars.u_please_select_price_trailer, 'warning', 2500);
    } else if (currencyTrailer.val() == -1 || currencyTrailer.val() == '') {
        currencyTrailer.addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_monetary_unit_error, 'warning', 2500);
    } else {
        SecondItemTwo.fadeOut(500, function () {
            window.scrollTo(0, 0);
            fourthItemOnePoster.fadeIn(500);
        });
    }


});


/**
 * Start truck
 */
const brandTruck = $('#brand-truck');
const modelTruck = $('#model-truck');
const gearboxTruck = $('#gearbox-truck');
const colorTruck = $('#color-truck');
const colorInputTruck = $('#color-input-truck');
const fuelTruck = $('#fuel-truck');

const btnSecondBackItemOnePoster = $('#btn-second-back-item-one-poster');
const btnSecondNextItemOnePoster = $('#btn-second-next-item-one-poster');
const thirdItemOnePoster = $('#third-item-one-poster');


const runTruck = $('#run-truck');
const priceTruck = $('#price-truck');
const currencyTruck = $('#currency-truck');
const cashTruck = $('#cash-truck');
const leasingTruck = $('#leasing-truck');
const installmentTruck = $('#installment-truck');
const builtTruck = $('#built-truck');


const btnThirdBackItemOnePoster = $('#btn-third-back-item-one-poster');
const btnThirdNextItemOnePoster = $('#btn-third-next-item-one-poster');

let yearDifference = 0;

// start step 2
brandTruck.click(function () {
    ModalBrand.modal('show');
    brandTruck.parent().removeClass('border-danger');
    // $('#modal-x-model').val(exchangeSaleModel.prop('id'));
});

modelTruck.click(function () {
    modelTruck.parent().removeClass('border-danger');
    if (brandTruck.attr('data-mj-value')) {
        ModalModel.modal('show');
    } else {
        brandTruck.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_barnd, 'warning', 2500);
    }
});

gearboxTruck.select2({
    minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-4'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
gearboxTruck.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

fuelTruck.select2({
    minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-5'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
fuelTruck.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

const pickr = Pickr.create({
    el: '#color-truck',
    theme: 'nano', // or 'monolith', or 'nano'

    swatches: null,
    default: colorInputTruck.data('mj-value'),
    position: 'bottom-middle',
    autoReposition: true,
    components: {

        // Main components
        preview: false,
        opacity: false,
        hue: true,

        // Input / output Options
        interaction: {
            hex: false,
            rgba: false,
            hsla: false,
            hsva: false,
            cmyk: false,
            input: false,
            clear: false,
            save: true
        }
    },
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 1)',
        'rgba(156, 39, 176, 1)',
        'rgba(103, 58, 183, 1)',
        'rgba(63, 81, 181, 1)',
        'rgba(33, 150, 243, 1)',
        'rgba(3, 169, 244, 1)',
        'rgba(0, 188, 212, 1)',
        'rgba(0, 150, 136, 1)',
        'rgba(76, 175, 80, 1)',
        'rgba(139, 195, 74, 1)',
        'rgba(205, 220, 57, 1)',
        'rgba(255, 235, 59, 1)',
        'rgba(255, 193, 7, 1)'
    ],
    // useAsButton: false,
    // closeOnScroll: true,
    // comparison: true,
    // closeWithKey: 'Escape',
    i18n: {

        // Strings visible in the UI
        'ui:dialog': 'color picker dialog',
        'btn:toggle': 'toggle color picker dialog',
        'btn:swatch': 'color swatch',
        'btn:last-color': 'use previous color',
        'btn:save': lang_vars.b_select,
        'btn:cancel': 'Cancel',
        'btn:clear': 'Clear',

        // Strings used for aria-labels
        'aria:btn:save': 'save and close',
        'aria:btn:cancel': 'cancel and close',
        'aria:btn:clear': 'clear and close',
        'aria:input': 'color input field',
        'aria:palette': 'color selection area',
        'aria:hue': 'hue selection slider',
        'aria:opacity': 'selection slider'
    }
});
pickr.on('init', instance => {
    // console.log('Event: "init"', instance);
}).on('hide', instance => {
    // console.log('Event: "hide"', instance);
}).on('show', (color, instance) => {
    // console.log('Event: "show"', color, instance);
}).on('save', (color, instance) => {
    // console.log('Event: "save"', color, instance);
    pickr.hide();
}).on('clear', instance => {
    // console.log('Event: "clear"', instance);
}).on('change', (color, source, instance) => {
    // console.log('Event: "change"', color, source, instance);
}).on('changestop', (source, instance) => {
    // console.log('Event: "changestop"', source, instance);
}).on('cancel', instance => {
    // console.log('Event: "cancel"', instance);
}).on('swatchselect', (color, instance) => {
    // console.log('Event: "swatchselect"', color, instance);
});

colorInputTruck.on('click', function () {
    pickr.show()
});

builtTruck.on('input', function () {
    $('#built-text-truck').text(parseInt($(this).val().trim()) + yearDifference)
});

// $('.mj-a-built-truck').on('click', function () {
//
//     if ($(this).prop('id') == "jalali") {
//         $('#gregorian').removeClass('mj-a-active')
//         $('#jalali').addClass('mj-a-active')
//         yearDifference = 0;
//         $('#built-text-truck').text(parseInt(builtTruck.val().trim()))
//     } else {
//         $('#jalali').removeClass('mj-a-active')
//         $('#gregorian').addClass('mj-a-active')
//         yearDifference = 621;
//         $('#built-text-truck').text(parseInt(builtTruck.val().trim()) + 621)
//     }
// })


$('[name="u-jalali-gregorian"]').each(function () {
    $(this).change(function () {
        if ($(this).prop('id') == "jalali") {
            yearDifference = 0;
            $('#built-text-truck').text(parseInt(builtTruck.val().trim()))
        } else {
            yearDifference = 621;
            $('#built-text-truck').text(parseInt(builtTruck.val().trim()) + 621)
        }
    });
});


btnSecondBackItemOnePoster.on('click', function () {
    SecondItemOne.fadeOut(500, function () {
        window.scrollTo(0, 0);
        firstItemOne.fadeIn(500);
    });
});

btnSecondNextItemOnePoster.on('click', function () {

    brandTruck.parent().removeClass('border-danger');
    modelTruck.parent().removeClass('border-danger');
    gearboxTruck.parent().removeClass('border-danger');
    fuelTruck.parent().removeClass('border-danger');

    if (brandTruck.attr('data-mj-value') == '') {
        brandTruck.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_barnd, 'warning', 2500);
    } else if (modelTruck.attr('data-mj-value') == '') {
        modelTruck.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_model, 'warning', 2500);
    } else if (gearboxTruck.val().trim() == '') {
        gearboxTruck.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_gearbox, 'warning', 2500);
    } else if (fuelTruck.val().trim() == '') {
        fuelTruck.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_fuel, 'warning', 2500);
    } else {
        SecondItemOne.fadeOut(500, function () {
            window.scrollTo(0, 0);
            thirdItemOnePoster.fadeIn(500);
        });
    }
});

// start step 3
runTruck.on('input', function () {
    let max = runTruck.attr('max');
    let min = runTruck.attr('min');

    let _value = $(this).val().replaceAll(',', '');
    if (parseInt(_value) < max && parseInt(_value) >= min && _value.length != 0) {
        if (/\D/g.test(_value)) {
            // Filter non-digits from input value.
            _value = _value.replace(/\D/g, '');
            $(this).val(addCommas(_value))
        } else {
            $(this).val(addCommas(_value))
        }
    } else if (_value == '') {
        $(this).val('')
    } else {
        // _value = _value.toString();
        // _value = _value.substr(0, max.length);
        $(this).val(addCommas(1000000))
    }
});

priceTruck.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value)) {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    } else {
        $(this).val(addCommas(_value))
    }
});

$("[data-tj-check-2]").on('input', function () {
    let _this = $(this);
    $('#third-item-one-poster').find('[data-tj-check-2]').prop('checked', false);
    _this.prop('checked', true);
})

btnThirdBackItemOnePoster.on('click', function () {
    thirdItemOnePoster.fadeOut(500, function () {
        window.scrollTo(0, 0);
        SecondItemOne.fadeIn(500);
    });
});

btnThirdNextItemOnePoster.on('click', function () {

    runTruck.parent().removeClass('border-danger');
    priceTruck.parent().removeClass('border-danger');
    currencyTruck.removeClass('border-danger');

    let statusRun = '';
    $('[name="status-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            statusRun = $(this).prop('id');
        }
    });

    // if (statusRun != "status-new" && runTruck.val().trim().length <= 0) {
    //     runTruck.parent().addClass('border-danger');
    //     sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_run, 'warning', 2500);
    //     // } else if (priceTruck.val().trim().length <= 0) {
    //     //     priceTruck.parent().addClass('border-danger');
    //     //     sendNotice(lang_vars.alert_warning , lang_vars.u_please_select_price, 'warning', 2500);
    // } else
        if (currencyTruck.val() == -1 || currencyTruck.val() == '') {
        currencyTruck.addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.b_cargo_monetary_unit_error, 'warning', 2500);
    } else {
        thirdItemOnePoster.fadeOut(500, function () {
            window.scrollTo(0, 0);
            fourthItemOnePoster.fadeIn(500);
        });
    }

});


/**
 *  general 2
 */
const secondItemGeneralPoster = $('#second-item-general-poster')

const fourthItemOnePoster = $('#fourth-item-one-poster');
const btnFourthBackItemOnePoster = $('#btn-fourth-back-item-one-poster');
const btnFourthNextItemOnePoster = $('#btn-fourth-next-item-one-poster');


const mobile = $('#mobile');
const phone = $('#phone');
const clockFrom = $('#clock-from');
const clockTo = $('#clock-to');
const countries = $('#countries');
const cities = $('#cities');
const description = $('#description');
const btnSecondBackItemGeneralPoster = $('#btn-second-back-item-general-poster');
const btnSecondNextItemGeneralPoster = $('#btn-second-next-item-general-poster');
const thirdItemGeneralPoster = $('#third-item-general-poster');

const trailerImages = $('#trailerImages');
let imagesList = [];
const expert = $('#expert');
const btnThirdBackItemGeneralPoster = $('#btn-third-back-item-general-poster');
const btnThirdNextItemGeneralPoster = $('#btn-third-next-item-general-poster');


// select property
btnFourthBackItemOnePoster.on('click', function () {
    let typePster = '';
    $('[name="type-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            typePster = $(this).prop('id');
        }
    });


    if (typePster == 'type-truck') {
        fourthItemOnePoster.fadeOut(500, function () {
            window.scrollTo(0, 0);
            thirdItemOnePoster.fadeIn(500);
        });
    } else {
        fourthItemOnePoster.fadeOut(500, function () {
            window.scrollTo(0, 0);
            SecondItemTwo.fadeIn(500);
        });
    }

});

btnFourthNextItemOnePoster.on('click', function () {
    fourthItemOnePoster.fadeOut(500, function () {
        window.scrollTo(0, 0);
        secondItemGeneralPoster.fadeIn(500);
    });
});


// select country and phone
countries.select2({
    // minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-2'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
countries.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        cities.html('');

        const data = {
            action: 'get-cities-by-country',
            country: $(this).val().trim(),
            type: 'poster'
        };
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(data),
            success: function (response) {
                let html = '<option value=""></option>';
                const json = JSON.parse(response);

                json.response.forEach(function (item) {
                    html += `
                        <option value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
                });
                cities.html(html);
            }
        });

    }
});

function getCity() {
    cities.html('');
    let myCity = cities.data('tj-city');

    const data = {
        action: 'get-cities-by-country',
        country: countries.val().trim(),
        type: 'poster'
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(data),
        success: function (response) {
            let html = '<option value=""></option>';
            const json = JSON.parse(response);

            json.response.forEach(function (item) {
                let selected = '';
                if (myCity == item.CityId) {
                    selected = 'selected';
                }
                html += `
                        <option ${selected} value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
            });
            cities.html(html);
        }
    });
}

$(document).ready(function () {
    getCity()
});

cities.select2({
    // minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-3'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
cities.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


clockFrom.select2({
    minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-6'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
clockFrom.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

clockTo.select2({
    minimumResultsForSearch: Infinity,
    // placeholder: "Select a state",
    allowClear: false,
    dropdownParent: $('.mj-a-select-child-8'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    },
    templateSelection: function (data) {
        const title = data.text;
        return $(`
              <span class="mj-a-custom-select-item">
                  ${title}
              </span>
          `);
    }
});
clockTo.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val().trim() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


btnSecondBackItemGeneralPoster.on('click', function () {
    secondItemGeneralPoster.fadeOut(500, function () {
        window.scrollTo(0, 0);
        fourthItemOnePoster.fadeIn(500);
    });
});

btnSecondNextItemGeneralPoster.on('click', function () {

    countries.parent().removeClass('border-danger');
    cities.parent().removeClass('border-danger');
    mobile.parent().removeClass('border-danger');

    if (countries.val().trim() == '') {
        countries.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_country, 'warning', 2500);
    } else if (cities.val().trim() == '') {
        cities.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_city, 'warning', 2500);
    } else if (mobile.val().trim().length < 11) {
        mobile.parent().addClass('border-danger');
        sendNotice(lang_vars.alert_warning, lang_vars.u_please_select_mobile, 'warning', 2500);
    } else {
        secondItemGeneralPoster.fadeOut(500, function () {
            window.scrollTo(0, 0);
            thirdItemGeneralPoster.fadeIn(500);
        });
    }


});


// select images
Dropzone.options.trailerImages = {
    url: '/poster/add',
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
                imagesList.push(file.dataURL);
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
            const index = imagesList.indexOf(file.dataURL);
            if (index > -1) {
                imagesList.splice(index, 1);
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

function removeImage(element) {
    const index = defaultImages.indexOf($(element).data('image'));
    if (index > -1) {
        defaultImages.splice(index, 1);
        $('div[data-remove="' + $(element).data('image') + '"]').remove();
    }
}

btnThirdBackItemGeneralPoster.on('click', function () {
    thirdItemGeneralPoster.fadeOut(500, function () {
        window.scrollTo(0, 0);
        secondItemGeneralPoster.fadeIn(500);
    });
});

btnThirdNextItemGeneralPoster.on('click', function () {
    const _this = $(this);

    let typePster = '';
    $('[name="type-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            typePster = $(this).data('tj-id');
        }
    });


    let statusPster = '';
    $('[name="status-poster"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            statusPster = $(this).data('tj-id');
        }
    });


    let properties = [];
    $('.mj-filter-option-list input:checked').each(function () {
        properties.push($(this).attr('data-option-id'));
    });

    _this.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });
    _this.addClass('tj-a-loader-6');

    const params = {
        action: 'update-poster',
        id: $(this).data('tj-id'),
        type: typePster,
        ads_title: $('#ads-title').val().trim(),
        status: statusPster,
        brandTruck: brandTruck.attr('data-mj-value').trim(),
        brandTextTruck: brandTruck.text().trim(),
        modelTruck: modelTruck.attr('data-mj-value').trim(),
        modelTextTruck: modelTruck.text().trim(),
        gearboxTruck: gearboxTruck.val().trim(),
        fuelTruck: fuelTruck.val().trim(),
        colorOutTruck: pickr.getColor().toHEXA().toString(),
        builtTruck: $('#built-text-truck').text().trim(),
        runTruck: runTruck.val().trim().replaceAll(',', ''),
        priceTruck: priceTruck.val().trim().replaceAll(',', ''),
        currencyTruck: currencyTruck.val().trim(),
        cashTruck: $('#cash-truck').is(':checked'),
        leasingTruck: $('#leasing-truck').is(':checked'),
        installmentTruck: $('#installment-truck').is(':checked'),

        brandTrailer: brandTrailer.attr('data-mj-value').trim(),
        brandTextTrailer: brandTrailer.text().trim(),
        modelBTrailer: modelBTrailer.attr('data-mj-value').trim(),
        modelTextTrailer: modelBTrailer.text().trim(),
        modelTrailer: modelTrailer.val().trim(),
        axisTrailer: axisTrailer.val().trim(),
        priceTrailer: priceTrailer.val().trim().replaceAll(',', ''),
        currencyTrailer: currencyTrailer.val().trim(),
        cashTrailer: $('#cash-trailer').is(':checked'),
        leasingTrailer: $('#leasing-trailer').is(':checked'),
        installmentTrailer: $('#installment-trailer').is(':checked'),


        properties: properties,
        country: countries.val().trim(),
        city: cities.val().trim(),
        mobile: mobile.val().trim(),
        phone: phone.val().trim(),
        clockFrom: clockFrom.val().trim(),
        clockTo: clockTo.val().trim(),
        description: description.val().trim(),
        images: imagesList,
        defaultImages: defaultImages,
        token: $('#token').val().trim()
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            const json = JSON.parse(response);
            if (json.status == 200) {
                sendNotice(lang_vars.alert_success, lang_vars.u_alert_success_add_poster, 'success', 2500);

                setTimeout(() => {
                    window.location.href = '/poster/my-list/pending';
                }, 2500);
            } else {
                sendNotice(lang_vars.alert_warning, lang_vars.u_alert_warning_add_poster, 'warning', 2500);
                $('#token').val(json.response);
                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
                _this.removeClass('tj-a-loader-6');
            }
        }
    })
});


/**
 * Modal Brand
 */
const ModalBrand = $('#choose-brand-modal');
const searchBrand = $('#brand-search-table');
const addNewBrandParent = $('#add-new-brand-parent')
const submitModalBrand = $('#submit-modal-brand')
const variableOtherBrand = '<div class="col-4 mj-a-brand-item mj-a-another-brand"><input type="radio" name="brand-select" id="brand-cc-another"> <label for="brand-cc-another" class="mj-a-brand-modal-div"> <div class="fa-plus"></div><span class="my-1">'+lang_vars.u_other+'</span><span class="d-none">no thing other</span></label></div>';


$("#search-brand").on("keyup", function () {
    addNewBrandParent.addClass('mj-a-height-0');
    addNewBrandParent.removeClass('mj-a-height-active');
    submitModalBrand.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });

    var value = $(this).val().toLowerCase();
    let flag = false;
    $("#brand-search-table .mj-a-brand-item").filter(function () {

        if ($(this).text().toLowerCase().indexOf(value) > -1) {
            flag = true;
        }
        if (flag) {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        } else {
            $(this).toggle($(this).text().toLowerCase().indexOf('no thing') > -1)
        }
        // $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1 )
        // $(this).toggle($(this).text().toLowerCase().indexOf('no thing')  > -1)

    });
});

$('body').find('[name="brand-select"]').each(function () {

    $(document).on("change", '.mj-a-brand-item', function () {
        brandTruck.html('<span class="mj-a-black">' + lang_vars.u_select_brand + '</span>');
        brandTruck.attr('data-mj-value', '');
        brandTrailer.html('<span class="mj-a-black">' + lang_vars.u_select_brand + '</span>');
        brandTrailer.attr('data-mj-value', '');
        $('#add-new-brand').val('');
        $('body').find('[name="brand-select"]').each(function (i) {
            if ($(this).is(':checked') === true) {
                if ($(this).prop('id') == "brand-cc-another") {
                    addNewBrandParent.removeClass('mj-a-height-0');
                    addNewBrandParent.addClass('mj-a-height-active');
                    submitModalBrand.attr('disabled', true).css({
                        transition: 'all .3s',
                        opacity: .5
                    });
                } else {
                    submitModalBrand.attr('disabled', false).css({
                        transition: 'all .3s',
                        opacity: 1
                    });
                    addNewBrandParent.addClass('mj-a-height-0');
                    addNewBrandParent.removeClass('mj-a-height-active');
                }
            }
        });
    });
});


function getBrands(type) {
    const params = {
        action: 'get-brands-poster',
        type: type,
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            const json = JSON.parse(response);
            if (json.status == 200) {
                // modalXbrand.attr('data-tj-type', type)
                setHtmlBrand(json.data)
            }
        }
    });
}

function setHtmlBrand(loop) {
    let temp = '';
    for (let i = 0; i < loop.length; ++i) {
        // temp = temp + '<div class="mj-m-brand-item" data-brand-id="' + loop[i].id + '"><img src="' + loop[i].image + '" alt=""> <div class="mj-m-brand-name">' + loop[i].name + '</div></div>';
        temp = temp + '<div class="col-4 mj-a-brand-item"><input type="radio" name="brand-select" data-value-id="' + loop[i].id + '" id="brand-cc-' + loop[i].id + '"> <label for="brand-cc-' + loop[i].id + '" class="mj-a-brand-modal-div"><img src="' + loop[i].image + '"><span class="my-1">' + loop[i].name + '</span><i class="d-none">' + loop[i].name_en + '</i></label></div>';
    }
    // searchBrand.prepend(temp);
    searchBrand.html(temp + variableOtherBrand);
}

submitModalBrand.on('click', function () {

    let _this = $(this);
    let flag = null;
    $('body').find('[name="brand-select"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            flag = $(this).prop('id');
        }
    });

    if (flag) {

        if (flag == 'brand-cc-another') {
            if ($('#add-new-brand').val().trim().length > 0) {

                ModalBrand.modal('hide');
                if (myType=='truck'){
                    brandTruck.html($('#add-new-brand').val().trim());
                    brandTruck.attr('data-mj-value', 0);
                    modelTruck.attr('data-mj-value','')
                    modelTruck.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
                }else{
                    brandTrailer.html($('#add-new-brand').val().trim());
                    brandTrailer.attr('data-mj-value', 0);
                    modelBTrailer.attr('data-mj-value','')
                    modelBTrailer.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
                }

                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });


                addNewModelParent.removeClass('mj-a-height-active').addClass('mj-a-height-0');
                submitModalModel.attr('disabled', true).css({
                    transition: 'all .3s',
                    opacity: .5
                });


                getModels(0)
                $('#add-new-brand').val('');

            } else {
                _this.attr('disabled', true).css({
                    transition: 'all .3s',
                    opacity: .5
                });

                sendNotice(lang_vars['alert_warning'], lang_vars['u_please_input_brand_name'], 'warning', 2500);
            }
        } else {
            ModalBrand.modal('hide');
            let Name = $('#' + flag + ' + label > span ').text().trim();
            let Id = $('#' + flag).attr('data-value-id');

            if (myType=='truck'){
                brandTruck.html(Name);
                brandTruck.attr('data-mj-value', Id);
                modelTruck.attr('data-mj-value','')
                modelTruck.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
            }else{
                brandTrailer.html(Name);
                brandTrailer.attr('data-mj-value', Id);
                modelBTrailer.attr('data-mj-value','');
                modelBTrailer.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
            }

            addNewModelParent.removeClass('mj-a-height-active').addClass('mj-a-height-0');
            submitModalModel.attr('disabled', true).css({
                transition: 'all .3s',
                opacity: .5
            });

            getModels(Id)
        }
    } else {
        sendNotice(lang_vars['alert_warning'], lang_vars['u_please_select_brand_name'], 'warning', 2500);
    }
});
$(document).on('keyup focusout', '#add-new-brand', function () {
    if ($('#add-new-brand').val().trim().length > 0) {
        submitModalBrand.attr('disabled', false).css({
            transition: 'all .3s',
            opacity: 1
        });
    } else {

        submitModalBrand.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: .5
        });
    }
})


/**
 * Modal Model
 */
const ModalModel = $('#choose-model-modal');
const modelSelected = $('[name="model-select"]')
const addNewModelParent = $('#add-new-model-parent')
const modelSearchTable = $('#model-search-table')
const submitModalModel = $('#submit-modal-model')
let allModels = [];
const variableOtherModel = '<div class="col-12 mj-a-model-item"> <input type="radio" name="model-select" id="model-cc-another"> <label for="model-cc-another" class="mj-a-model-modal-div"><span class="my-1"> <i class="fa-plus"></i> ' + lang_vars.u_other + '</span><i class="d-none">no thing other</i></label></div>';
$(document).ready(function () {
    getAllModels()
});


$("#search-model").on("keyup", function () {
    addNewModelParent.addClass('mj-a-height-0');
    addNewModelParent.removeClass('mj-a-height-active');
    submitModalModel.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });

    var value = $(this).val().toLowerCase();
    let flag = false;
    $("#model-search-table .mj-a-model-item").filter(function () {

        if ($(this).text().toLowerCase().indexOf(value) > -1) {
            flag = true;
        }
        if (flag) {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        } else {
            $(this).toggle($(this).text().toLowerCase().indexOf('no thing') > -1)
            // modelSearchTable.html(variableOtherModel)
        }
        // $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1 )
        // $(this).toggle($(this).text().toLowerCase().indexOf('no thing')  > -1)

    });
});

$(document).find('[name="model-select"]').each(function () {
    $(document).on("change", '.mj-a-model-item', function () {

        modelTruck.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
        modelTruck.attr('data-mj-value', '');
        modelBTrailer.html('<span class="mj-a-black">' + lang_vars.u_select_model + '</span>');
        modelBTrailer.attr('data-mj-value', '');
        $('#add-new-model').val('');

        $('body').find('[name="model-select"]').each(function (i) {
            if ($(this).is(':checked') === true) {
                if ($(this).prop('id') == "model-cc-another") {
                    addNewModelParent.removeClass('mj-a-height-0');
                    addNewModelParent.addClass('mj-a-height-active');
                    submitModalModel.attr('disabled', true).css({
                        transition: 'all .3s',
                        opacity: .5
                    });
                } else {
                    submitModalModel.attr('disabled', false).css({
                        transition: 'all .3s',
                        opacity: 1
                    });
                    addNewModelParent.addClass('mj-a-height-0');
                    addNewModelParent.removeClass('mj-a-height-active');
                }
            }
        });
    });
});


function getAllModels() {
    const params = {
        action: 'get-models-poster',
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            const json = JSON.parse(response);
            if (json.status == 200) {
                allModels = json.data;
                getModels(modelTruck.attr('data-mj-value'))
            }
        }
    });
}

function getModels(brandId) {
    let template = [];
    for (let i = 0; i < allModels.length; i++) {
        // console.log(provinceId)
        if (allModels[i].brandId == brandId) {
            template.push(allModels[i]);
        }
    }
    setHtmlModel(template)
}

function setHtmlModel(loop) {
    let temp = '';
    for (let i = 0; i < loop.length; ++i) {
        temp = temp + '<div class="col-12 mj-a-model-item"><input type="radio" name="model-select" data-value-id="' + loop[i].id + '" id="model-cc-' + loop[i].id + '"><label for="model-cc-' + loop[i].id + '" class="mj-a-model-modal-div"> <span class="my-1">' + loop[i].name + '</span><i class="d-none">' + loop[i].name_en + '</i></label></div>';
    }
    modelSearchTable.html(temp + variableOtherModel);
}

submitModalModel.on('click', function () {

    let _this = $(this);
    let flag = null;
    $('body').find('[name="model-select"]').each(function (i) {
        if ($(this).is(':checked') === true) {
            flag = $(this).prop('id');
        }
    });

    if (flag) {

        if (flag == 'model-cc-another') {
            if ($('#add-new-model').val().trim().length > 0) {

                ModalModel.modal('hide');
                if (myType=='truck'){
                    modelTruck.html($('#add-new-model').val().trim());
                    modelTruck.attr('data-mj-value', 0);
                }else{
                    modelBTrailer.html($('#add-new-model').val().trim());
                    modelBTrailer.attr('data-mj-value', 0);
                }

                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });


                $('#add-new-model').val('');
                _this.attr('disabled', true).css({
                    transition: 'all .3s',
                    opacity: .5
                });

            } else {
                _this.attr('disabled', true).css({
                    transition: 'all .3s',
                    opacity: .5
                });

                sendNotice(lang_vars['alert_warning'], lang_vars['u_please_input_model_name'], 'warning', 2500);
            }
        } else {
            ModalModel.modal('hide');
            let Name = $('#' + flag + ' + label > span ').text().trim();
            let Id = $('#' + flag).attr('data-value-id');
            if (myType=='truck') {
                modelTruck.html(Name);
                modelTruck.attr('data-mj-value', Id);
            }else{
                modelBTrailer.html(Name);
                modelBTrailer.attr('data-mj-value', Id);
            }


            $('#add-new-model').val('');
            _this.attr('disabled', true).css({
                transition: 'all .3s',
                opacity: .5
            });
        }
    } else {
        sendNotice(lang_vars['alert_warning'], lang_vars['u_please_select_model_name'], 'warning', 2500);
    }
});

$(document).on('input focusout', '#add-new-model', function () {
    if ($('#add-new-model').val().trim().length > 0) {
        submitModalModel.attr('disabled', false).css({
            transition: 'all .3s',
            opacity: 1
        });
    } else {

        submitModalModel.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: .5
        });
    }
})