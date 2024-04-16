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

/*
price.on('input', function () {
    // const value = new Intl.NumberFormat().format($(this).val().replaceAll(',', ''));
    // if (isNaN(value.replaceAll(',', ''))) {
    //     $(this).val(0)
    // } else {
    //     $(this).val(value)
    // }
    const _value = $(this).val().replaceAll(',', '');
    let value = 0;
    if (!isNaN(_value) && _value > 0) {
        value = new Intl.NumberFormat().format(_value);
        $(this).val(value);
    } else {
        let x= $(this).val().replace(/\D/g, '')
         x= new Intl.NumberFormat().format(x);
        $(this).val(x);
    }
});
*/


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

    // if (carsList.val() == -1) {
    //     sendNotice(lang_vars.alert_warning, lang_vars.d_alert_send_request_car, 'warning', 3500);
    // } else
    if (price.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_send_request_price, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'send-request',
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

$('#submit-car').on('click', function () {
    const _btn = $(this);

    if (carName.val().trim() == "") {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_name, 'warning', 3500);
    } else if (carType.val().trim() == -1 || carType.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_type, 'warning', 3500);
    } else if (plaqueType.val().trim() == '' || plaqueType.val().trim() == -1) {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_plaque_type, 'warning', 3500);
    } else if (plaqueNumber.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.d_alert_car_plaque, 'warning', 3500);
    } else {
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
            images: [],
            token: $('#token-new-car').val().trim()
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

$('#btn-add-car').click(function () {
    $('#add-car-model').modal('show');
})


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

// console.log("Test A ٠١٢٣", "=>", strPToE("tert  ٠"));
// console.log("Test B ٦٥٤", "=>",  strPToE("Test B ٦٥٤"));
// console.log("Test C ٧٨٩", "=>",  strPToE("Test C ٧٨٩"));
//
// // Demonstrate converting "English" to "Persian"
// console.log("Test A 0123", "=>", strEToP("Test A 0123"));
// console.log("Test B 654", "=>",  strEToP("Test B 654"));
// console.log("Test C 789", "=>",  strEToP("Test C 789"));

// function faTOen($string) {
//     return strtr($string, array('۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9', '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'));
// }


// const e2p = s => s.replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d])
// const e2a = s => s.replace(/\d/g, d => '٠١٢٣٤٥٦٧٨٩'[d])
//
// const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
// const a2e = s => s.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
//
// const p2a = s => s.replace(/[۰-۹]/g, d => '٠١٢٣٤٥٦٧٨٩'['۰۱۲۳۴۵۶۷۸۹'.indexOf(d)])
// const a2p = s => s.replace(/[٠-٩]/g, d => '۰۱۲۳۴۵۶۷۸۹'['٠١٢٣٤٥٦٧٨٩'.indexOf(d)])
//
// console.log(e2p("asdf1234")) // asdf۱۲۳۴
// console.log(e2a("asdf1234")) // asdf١٢٣٤
// console.log(p2e("asdf۱۲۳۴")) // asdf1234
// console.log(a2e("asdf١٢٣٤")) // asdf1234
// console.log(p2a("asdf۱۲۳۴")) // asdf١٢٣٤
// console.log(a2p("asdf١٢٣٤")) // asdf۱۲۳۴
// console.log(p2e(a2e('٢')))