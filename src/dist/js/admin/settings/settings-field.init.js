let temp_lang = JSON.parse(var_lang);

// $('#sasad').bootstrapTable();
$.fn.editable.defaults.mode = 'inline';
$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',
    $('#auth_company').editable({
        type: 'number',
        emptytext: temp_lang.a_empty,
        pk: 1,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#auth_company').data('tj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    })
    , $('#auth_manager').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 2,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_manager').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_address').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 3,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_address').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_phone').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 4,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_phone').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_fox').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 5,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_fox').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_mail').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 6,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_mail').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_site').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 7,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_site').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_id-card-image').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 8,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_id-card-image').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_passport-image').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 9,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_passport-image').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_birthday-city').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 10,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_birthday-city').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_birthday-date').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 11,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_birthday-date').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_phone-national').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 12,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_phone-national').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_insurance-type').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 13,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_insurance-type').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_insurance-number').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 14,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_insurance-number').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
})
    , $('#auth_car-card-image').editable({
    type: 'number',
    emptytext: temp_lang.a_empty,
    pk: 15,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#auth_car-card-image').data('tj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }
});

function f(slug, rate) {

    let token = $('#token').val().trim();

    let data = {
        action: 'settings-field-rate',
        rate: rate,
        slug: slug,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            if (data == 'successful') {
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

}