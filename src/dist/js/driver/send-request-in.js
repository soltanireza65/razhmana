const carsList = $('#cars-list');
const price = $('#price');

carsList.select2({
    dropdownParent: $('.mj-custom-select.cars-list'),
    minimumResultsForSearch: -1,
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    placeholder: {
        id: '-1',
        text: '<img width="20px" src="/dist/images/icons/truck-blue.svg" class="me-1"  /> ' + lang_vars.d_my_cars_choose_car_type
    },
    templateResult: function (data) {
        const title = data.text;
        const image = (data.element && $(data.element).data('image')) ? $(data.element).data('image') : '';

        return $(`
            <span class="mj-custom-select-item">
                ${(image) ? `<img width="20px" src="${image}" class="me-1" alt="" />` : ''}
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

price.on('input', function () {
    let _value = charPtoE($(this).val()).replaceAll(',', '');
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

$('#submit-request').on('click', function () {
    const _btn = $(this);

    /*   if (carsList.val() == -1) {
           sendNotice(lang_vars.alert_warning, lang_vars.d_alert_send_request_car, 'warning', 3500);
       } else */
    if (price.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_send_request_price, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'send-request-in',
            cargo: $(this).data('cargo'),
            car: -1,
            price: price.val().replaceAll(',', ''),
            token: $('#token').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.d_alert_send_request, 'success', 2500);
                        setTimeout(() => {
                            window.location.href = '/driver/my-requests';
                        }, 3000);
                    } else {
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        $('#token').val(json.response);
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
});


/***
 * add new car
 * Start Modal
 */
const plaqueType = $('#plaque-type');
const carType = $('#car-type');
const carName = $('#car-name');
const plaqueNumber = $('#plaque-number')

const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

carType.select2({
    dropdownParent: $('.mj-custom-select.car-type'),
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    placeholder: {
        id: '-1',
        text: '<img width="20px" src="/dist/images/icons/truck-blue.svg" class="me-1"  /> ' + lang_vars.d_my_cars_choose_car_type
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




// One time setup
var persian = "٠١٢٣٤٥٦٧٨٩";
var mapPtoE = Object.create(null);
var mapEtoP = Object.create(null);
persian.split("").forEach(function (glyph, index) {
    mapPtoE[glyph] = index;
    mapEtoP[index] = glyph;
});

// Convert one char "Persion" => "English"
function charPtoE(ch) {
    return (mapPtoE[ch] !== undefined) ? mapPtoE[ch] : ch
    // return mapPtoE[ch] || ch;
}

// Convert one char "English" => "Persion"
function charEtoP(ch) {
    return mapEtoP[ch] || ch;
}

// Convert the "Persian" digits in a string to "English"
function strPToE(s) {
    return s.replace(/[٠١٢٣٤٥٦٧٨٩]/g, charPtoE);
}

// Convert the "English" digits in a string to "Persian"
function strEToP(s) {
    return s.replace(/\d/g, charEtoP);
}