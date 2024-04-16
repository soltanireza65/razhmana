let temp_lang = JSON.parse(var_lang);

$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',
    $("#change_inquiry_category").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 2,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_category,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_inquiry_category').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_type_id").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 2,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_type,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_type_id').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_inquiry_weight').editable({
        type: 'number',
        pk: 6,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_inquiry_weight').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_inquiry_volume').editable({
        type: 'number',
        pk: 7,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_inquiry_volume').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_inquiry_price').editable({
        type: 'number',
        pk: 8,
        emptytext: temp_lang.a_empty,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_inquiry_price').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_inquiry_currency").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 9,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_currency,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_inquiry_currency').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#add_inquiry_admin_description').editable({
        type: 'textarea',
        pk: 11,
        emptytext: temp_lang.a_add_new_description,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#add_inquiry_admin_description').data('tj-type');
            if (Value.length > 0) {
                f2(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
$('#change_freight_duration').editable({
    type: 'number',
    pk: 8,
    emptytext: temp_lang.a_empty,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_freight_duration').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
});


function f(type, newValue) {

    let inquiryId = $('#inquiryId').data('tj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'inquiry-inventory-info',
        value: newValue,
        type: type,
        inquiryId: inquiryId,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            console.log(data)
            if (data == 'successful') {
                // $(".btn").attr('disabled', 'disabled');
                toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
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


$('.btnSubmit').click(function () {

    let btn = $(this).prop('id');
    let inquiryId = $('#inquiryId').data('tj-id');
    let status = $(this).data('tj-status');
    let token = $('#token').val().trim();

    var BTN = Ladda.create(document.querySelector('#' + btn));

    if (parseInt(inquiryId) > 0) {
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'inquiry-inventory-info-statue',
            inquiryId: inquiryId,
            status: status,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTN.remove();
                $(".setSubmitBtn").removeAttr('disabled');

                if (data == 'successful') {
                    $(".btn").attr('disabled', 'disabled');

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
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


function f2(type, newValue) {

    let inquiryId = $('#inquiryId').data('tj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'inquiry-inventory-info-desc-admin',
        value: newValue,
        type: type,
        inquiryId: inquiryId,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            if (data == 'successful') {
                // $(".btn").attr('disabled', 'disabled');
                toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
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


$('.selectLocation').select2({
    dropdownParent: $('#cityModal')
}).select2('val');


$('.changeLocation').click(function () {
    let _this = $(this);
    // let id = _this.find('~span').prop('id');
    let type = _this.data('tj-type');
    _this.addClass(' mdi-spin');

    let data = {
        action: 'inquiry-info-location',
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            _this.removeClass('mdi-spin');
            if (jQuery.parseJSON(data).status == 200) {
                $('#selectCountry').html('');
                getCountry(data, 'selectCountry');
                $('#cityModal').modal('show');
            } else {
                $('#modalDiv').modal('hide');
                $('#selectCountry').html('');

            }

        }
    });
});


$('#selectCountry').on('select2:select', function (e) {
    var country = e.params.data.id;
    $('#selectCustoms').html('');
    let data = {
        action: 'inquiry-info-location-inventory',
        country: country,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            if (jQuery.parseJSON(data).status == 200) {
                $('#selectCity').html('');
                $('#selectInventory').html('');
                getCity(data, 'selectCity');
                getInventory(data, 'selectInventory');
            } else {
                $('#modalDiv').modal('hide');
                $('#selectCity').html('');
                $('#selectInventory').html('');
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

function getCity(myVlaues, divID) {
    let temp = "";
    let tt = jQuery.parseJSON(myVlaues).dataCity;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}

function getInventory(myVlaues, divID) {
    let temp = '';
    let tt = jQuery.parseJSON(myVlaues).dataInventory;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    temp = temp + '<option value="null">' + temp_lang.a_select_inventory_2 + '</option>';
    $('#' + divID).html(temp);
}


$('#submitModal').click(function () {


    let BTNN = Ladda.create(document.querySelector('#submitModal'));
    let inquiryId = $('#inquiryId').data('tj-id');
    let token = $('#token').val().trim();

    // let value = $('#selecteModal').select2().select2('val');
    let country = $('#selectCountry').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');

    let city = $('#selectCity').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');

    let inventory = $('#selectInventory').select2({
        dropdownParent: $('#cityModal')
    }).select2('val');

    BTNN.start();

    let data = {
        action: 'inquiry-inventory-set-new-location',
        inquiryId: inquiryId,
        country: country,
        city: city,
        inventory: inventory,
        token: token,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            BTNN.remove();
            if (data == 'successful') {
                $(".btn").attr('disabled', 'disabled');
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
            toastNotic(temp_lang.error, temp_lang.error_mag, 'warning');
        }
    });


});

