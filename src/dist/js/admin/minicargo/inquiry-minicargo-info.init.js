let temp_lang = JSON.parse(var_lang);

$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',

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
    });


function f(type, newValue) {

    let inquiryId = $('#inquiryId').data('tj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'inquiry-minicargo-info',
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
        $(".btn").attr('disabled', true);

        let data = {
            action: 'inquiry-minicargo-info-statue',
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
                $(".btn").attr('disabled', false);

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
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


function f2(type, newValue) {

    let inquiryId = $('#inquiryId').data('tj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'inquiry-minicargo-info-desc-admin',
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


