var swiper = new Swiper(".mySwiper", {
    loop: false,
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});
var swiper2 = new Swiper(".mySwiper2", {
    loop: false,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: swiper,
    },
});

let temp_lang = JSON.parse(var_lang);

// $('#sasad').bootstrapTable();
$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',
    $('#change_cargo_name_fa_IR').editable({
        type: 'text',
        pk: 1,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_name_fa_IR').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    }),
    $('#change_cargo_name_en_US').editable({
        type: 'text',
        pk: 101,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_name_en_US').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    }),
    $('#change_cargo_name_tr_Tr').editable({
        type: 'text',
        pk: 102,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_name_tr_Tr').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    }),
    $('#change_cargo_name_ru_RU').editable({
        type: 'text',
        pk: 103,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_name_ru_RU').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    }),
    $("#change_cargo_category").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 2,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_category,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_category').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_cargo_type").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 3,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_type,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_type').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_weight').editable({
        type: 'number',
        pk: 4,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_weight').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_volume').editable({
        type: 'number',
        pk: 5,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_volume').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_description_fa_IR').editable({
        type: 'textarea',
        pk: 6,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#change_cargo_description_fa_IR').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_description_en_US').editable({
        type: 'textarea',
        pk: 104,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#change_cargo_description_en_US').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_description_tr_Tr').editable({
        type: 'textarea',
        pk: 104,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#change_cargo_description_tr_Tr').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_description_ru_RU').editable({
        type: 'textarea',
        pk: 104,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#change_cargo_description_ru_RU').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_cargo_status").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 7,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_status,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_status').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_cargo_monetary_unit").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 8,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_currency,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_monetary_unit').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_recommended_price').editable({
        type: 'number',
        pk: 9,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_recommended_price').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    });

function f(type, newValue, refresh = false) {

    let CargoID = $('#CargoID').data('mj-cargo-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'cargo-in-info',
        value: newValue,
        type: type,
        cargoID: CargoID,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            const myArray = data.split(" ");

            if (myArray[0] == 'successful') {
                $('#token').val(myArray[1]);
                toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                if (refresh) {
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                }
            } else if (data == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });

}


/**
 * Show Request Images
 */


var swiper3 = new Swiper(".showImageReq", {
    slidesPerView: "auto",
    centeredSlides: true,
    spaceBetween: 30,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});

$('.showRequest').click(function () {
    let id = $(this).data('mj-request-id');
    let token = $('#token').val().trim();

    if (id > 0 && token.length > 0) {
        let _this = $(this);
        _this.find('i').addClass(' mdi-spin');

        let data = {
            action: 'get-request-in-image',
            id: id,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                _this.find('i').removeClass('mdi-spin');
                if (jQuery.parseJSON(data).status == 200) {
                    $('#token').val(jQuery.parseJSON(data).token);
                    $('#showImageReqID').html('');
                    getRequestImage(jQuery.parseJSON(data).images);

                    $('#requestImageModal').modal('show');
                } else {
                    $('#token').val(jQuery.parseJSON(data).token);
                    $('#requestImageModal').modal('hide');

                    $('#showImageReqID').html('');
                }

            }
        });

    }
});

function getRequestImage(images) {
    let temp = "";
    let tt = images;

    for (var i in tt) {
        temp = temp + '<div class="swiper-slide"><img src="' + tt[i] + '"></div>';
    }

    $('#showImageReqID').html(temp);
}


/**
 * Change Request Status
 */
$('.changeRequestSatus').click(function () {
    let id = $(this).data('mj-reguest-id');
    let status = $(this).data('mj-status');
    let token = $('#token').val().trim();

    // let statusREsult = "pending";
    // if (status == "pending") {
    //     statusREsult = "rejected";
    // }
    if (id > 0 && token.length > 0) {
        let _this = $(this);
        _this.find('i').addClass(' mdi-spin');

        let data = {
            action: 'change-request-in-status',
            requestId: id,
            status: status,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                _this.find('i').removeClass('mdi-spin');
                const myArray = data.split(" ");


                if (myArray[0] == 'successful') {
                    $('#token').val(myArray[1]);
                    if (status == "pending") {
                        // _this.find('i').removeClass('text-danger');
                        // _this.find('i').addClass('text-warning');
                        _this.tooltip().attr('data-bs-original-title', temp_lang.change_status_pending_to_rejected);
                        // _this.parent().parent().find('[data-mj-flag]').removeClass('badge-soft-danger');
                        // _this.parent().parent().find('[data-mj-flag]').addClass('badge-soft-warning');
                        // _this.parent().parent().find('[data-mj-flag]').text(temp_lang.pending);
                    } else {
                        // _this.find('i').removeClass('text-warning');
                        // _this.find('i').addClass('text-danger');
                        // _this.parent().parent().find('[data-mj-flag]').removeClass('badge-soft-warning');
                        // _this.parent().parent().find('[data-mj-flag]').addClass('badge-soft-danger');
                        // _this.parent().parent().find('[data-mj-flag]').text(temp_lang.rejected);
                        _this.tooltip().attr('data-bs-original-title', temp_lang.change_status_rejected_to_pending);
                    }
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                    // $(_this).data('mj-status', statusREsult);
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }

            }
        });

    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


/**
 * Change Extra Expenses Status
 */
$('.changeExtraExpensesStatus').click(function () {
    let _this = $(this);
    let idExtra = _this.data('mj-extra-id');
    let status = _this.data('mj-status');
    let token = $('#token').val().trim();

    if (idExtra > 0 && token.length > 0) {

        _this.find('i').addClass(' mdi-spin');


        let data = {
            action: 'change-extra-expenses-in-status',
            idExtra: idExtra,
            status: status,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                _this.find('i').removeClass('mdi-spin');

                const myArray = data.split(" ");

                if (myArray[0] == 'successful') {
                    $('#token').val(myArray[1]);
                    if (status == "pending") {
                        _this.parent().parent().find('.badgeExpenseStatus').removeClass();
                        _this.parent().parent().find('[data-mj-flag]').addClass('badge badge-soft-warning font-12 badgeExpenseStatus');
                        _this.parent().parent().find('[data-mj-flag]').text(temp_lang.pending);
                    } else if (status == "accepted") {
                        _this.parent().parent().find('.badgeExpenseStatus').removeClass();
                        _this.parent().parent().find('[data-mj-flag]').addClass('badge badge-soft-success font-12 badgeExpenseStatus');
                        _this.parent().parent().find('[data-mj-flag]').text(temp_lang.accepted);
                    } else if (status == "rejected") {
                        _this.parent().parent().find('.badgeExpenseStatus').removeClass();
                        _this.parent().parent().find('[data-mj-flag]').addClass('badge badge-soft-danger font-12 badgeExpenseStatus');
                        _this.parent().parent().find('[data-mj-flag]').text(temp_lang.rejected);
                    } else if (status == "canceled") {
                        _this.parent().parent().find('.badgeExpenseStatus').removeClass();
                        _this.parent().parent().find('[data-mj-flag]').addClass('badge badge-soft-secondary font-12 badgeExpenseStatus');
                        _this.parent().parent().find('[data-mj-flag]').text(temp_lang.canceled);
                    }

                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }

            }
        });

    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});

if ($('.btnSubmit').length > 0) {
    $('.btnSubmit').click(function () {
        let _this = $(this);
        let btn = _this.prop('id');
        let status = _this.data('mj-status');
        let token = $('#token').val().trim();
        let CargoID = $('#CargoID').data('mj-cargo-id');
        let BTNN = Ladda.create(document.querySelector('#' + btn));

        BTNN.start();
        let data = {
            action: 'change-cargo-in-status',
            CargoID: CargoID,
            status: status,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                const myArray = data.split(" ");


                BTNN.remove();

                if (myArray[0] == "successful") {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (myArray[0] == 'error') {
                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    });
}


if ($('.showCancelRequest').length > 0) {
    $('.showCancelRequest').click(function () {
        let id = $(this).data('mj-request-id');
        let value = $('#' + id).val();
        // $('#requestModalValue').text('');
        $('#requestModalValue').html(value);
        $('#requestmodal').modal('show');
    });
}


/**
 * Start Map
 * @type {ol.Map}
 */
$('.selectLocation').select2({
    dropdownParent: $('#cityModal')
}).select2('val');


$('.changeLocation').click(function () {
    let _this = $(this);
    // let id = _this.find('~span').prop('id');
    let type = _this.data('tj-type');
    _this.addClass(' mdi-spin');

    let data = {
        action: 'cargo-info-location',
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            _this.removeClass('mdi-spin');
            if (jQuery.parseJSON(data).status == 200) {
                $('#selectCountry').html('');
                $('#submitModal').attr('data-tj-type', type);
                getCountry(data, 'selectCountry');
                $('#cityModal').modal('show');
            } else {
                $('#modalDiv').modal('hide');
                $('#selectCountry').html('');
                $('#submitModal').attr('data-tj-type', '');
            }

        }
    });
});


$('#selectCountry').on('select2:select', function (e) {
    var country = e.params.data.id;
    $('#selectCity').html('');
    $('#selectCustoms').html('');
    let data = {
        action: 'cargo-in-location-city',
        country: country,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            if (jQuery.parseJSON(data).status == 200) {
                $('#selectCity').html('');
                $('#selectCustoms').html('');
                getCity(data, 'selectCity');
                getCustoms(data, 'selectCustoms');
            } else {
                $('#modalDiv').modal('hide');
                $('#selectCity').html('');
                $('#selectCustoms').html('');
            }

        }
    });

});


function getCountry(myVlaues, divID) {
    let temp = '<option value="0">' + temp_lang.a_select_country_2 + '</option>';
    let tt = jQuery.parseJSON(myVlaues).data;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}

function getCustoms(myVlaues, divID) {
    let temp = "";
    let tt = jQuery.parseJSON(myVlaues).datacity;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}

function getCity(myVlaues, divID) {
    let temp = "";
    let tt = jQuery.parseJSON(myVlaues).datacity;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}


$('#submitModal').click(function () {


    let BTNN = Ladda.create(document.querySelector('#submitModal'));
    let CargoID = $('#CargoID').data('mj-cargo-id');
    let token = $('#token').val().trim();

    // let value = $('#selecteModal').select2().select2('val');
    let country = $('#selectCountry').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');

    let city = $('#selectCity').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');

    let customs = $('#selectCustoms').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');


    BTNN.start();

    let data = {
        action: 'cargo-in-set-new-location',
        CargoID: CargoID,
        country: country,
        city: city,
        customs: customs,
        token: token,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            BTNN.remove();
            if (data == 'successful') {
                $(".btn").attr('disabled', true);
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            } else if (data == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }

        },
        error: function () {
            alert('error occured');
        }
    });


});


$('#StartDate').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#startDefault',
    altFormat: 'X',
    minDate: Date.now(),
    viewMode: 'year',
    toolbox: {
        submitButton: {
            enabled: true,
            onSubmit: function () {
                f('cargo_start_date', $('#startDefault').val(), true)
            }
        },


    },
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    },

});


if ($('#referTask').length > 0) {
    const select2 = $('#referTask[data-toggle="select2"]').select2({
        templateResult: function (idioma) {
            const title = idioma.text;
            // const symbol = (idioma.id) ? idioma.id : '';
            const symbol = (idioma.element && $(idioma.element).data('tj-category-status')) ? $(idioma.element).data('tj-category-status') : '';
            const color = (idioma.element && $(idioma.element).data('tj-category-color')) ? $(idioma.element).data('tj-category-color') : '';

            return $(`<span class="sd-asset-item"> &nbsp; ${title} &nbsp;<span class="text-${color} font-11">(${symbol})</span></span>`);
        },
        templateSelection: function (idioma) {
            const title = idioma.text;
            // const symbol = (idioma.id) ? idioma.id : '';
            const symbol = (idioma.element && $(idioma.element).data('tj-category-status')) ? $(idioma.element).data('tj-category-status') : '';
            const color = (idioma.element && $(idioma.element).data('tj-category-color')) ? $(idioma.element).data('tj-category-color') : '';
            return $(`<span class="sd-asset-item-selection"> &nbsp; ${title} &nbsp;<span class="text-${color} font-11">(${symbol})</span></span>`);
        }
    });

    select2.data('select2').$selection.css('height', 'auto');
    select2.data('select2').$selection.find('.sd-asset-item').css('line-height', '30px');
    select2.data('select2').$selection.find('.select2-selection__arrow').css('height', 'auto');

    $('#setAdmin').click(function () {
        let BTNN = Ladda.create(document.querySelector('#setAdmin'));
        let CargoID = $('#CargoID').data('mj-cargo-id');
        let token = $('#token').val().trim();
        let referTd = $('#referTask').select2().select2('val');
        if (referTd.length > 0) {
            BTNN.start();

            let data = {
                action: 'cargo-in-set-admin',
                CargoID: CargoID,
                referTd: referTd,
                token: token,
            };
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {

                    BTNN.remove();
                    if (data == 'successful') {
                        $(".btn").attr('disabled', true);
                        toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                        window.setTimeout(
                            function () {
                                location.reload();
                            },
                            2000
                        );
                    } else if (data == "empty") {
                        toastNotic(temp_lang.error, temp_lang.empty_input);
                    } else if (data == "token_error") {
                        toastNotic(temp_lang.error, temp_lang.token_error);
                    } else {
                        toastNotic(temp_lang.error, temp_lang.error_mag);
                    }

                }
            });
        } else {
            toastNotic(temp_lang.warning, temp_lang.a_set_admin_min_one, 'warning');
        }
    });

}

$('#btn-admin-desc').on('click', function () {
    let BTNN = Ladda.create(document.querySelector('#btn-admin-desc'));
    let CargoID = $('#CargoID').data('mj-cargo-id');
    let token = $('#token').val().trim();
    let desc = $('#admin-desc').val().trim();
    if (desc.length > 0) {
        BTNN.start();

        let data = {
            action: 'cargo-in-admin-add-desc',
            CargoID: CargoID,
            desc: desc,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTNN.remove();
                if (data == 'successful') {
                    $(".btn").attr('disabled', true);
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }

            }
        });
    } else {
        toastNotic(temp_lang.warning, temp_lang.empty_input, 'warning');
    }

});

$(document).on('click', '#checklist-btn', function () {
    $('.mj-admin-cargo-checklist').addClass('active')
});
$(document).on('click', '.mj-close-checklist', function () {
    $('.mj-admin-cargo-checklist').removeClass('active')
});