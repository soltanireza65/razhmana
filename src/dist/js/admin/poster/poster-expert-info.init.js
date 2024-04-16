let temp_lang = JSON.parse(var_lang);

$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',
    $('#change_pe_address').editable({
        type: 'text',
        pk: 1,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_pe_address').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    }),
    $("#change_expert_id").editable({
        // prepend: "not selected",
        type: 'select',
        mode: "inline",
        pk: 2,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_experts,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_expert_id').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_pe_reason').editable({
        type: 'text',
        pk: 3,
        emptytext: temp_lang.a_empty,
        // url: '/api/adminAjax',
        // title: 'Enter username',
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_pe_reason').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }

        }
    });

function f(type, newValue, refresh = false) {

    let peId = $('[data-tj-id]').data('tj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'poster-expert-info',
        value: newValue,
        type: type,
        peId: peId,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            const myArray = data.split(" ");

            if (myArray[0] == 'successful') {
                // $(".btn").attr('disabled', 'disabled');
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

$('.btn-submit').on('click', function () {
    let peId = $('[data-tj-id]').data('tj-id');
    let btn = $(this).prop('id');
    if (jQuery.inArray(btn, ['accepted', 'rejected', 'completed', 'canceled']) != -1) {

        const BTN = Ladda.create(document.querySelector('#' + btn));
        BTN.start();
        $(".btn").attr('disabled', true);

        let data = {
            action: 'poster-expert-select-expert',
            peId: peId,
            status: btn,
            token: $('#token').val().trim(),
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").attr('disabled', false);

                if (data == "successful") {
                    $(".btn").attr('disabled', true);
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'info');
                    window.setTimeout(
                        function () {
                            window.location.reload();
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

})
$('.send-sms').on('click', function () {
    let peId = $('[data-tj-id]').data('tj-id');
    let btn = $(this).prop('id');

    const BTN = Ladda.create(document.querySelector('#' + btn));
    BTN.start();
    $(".btn").attr('disabled', true);

    let data = {
        action: 'poster-expert-sent-sms',
        peId: peId,
        token: $('#token').val().trim(),
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTN.remove();
            $(".btn").attr('disabled', false);

            if (data == "successful") {
                $(".btn").attr('disabled', true);
                toastNotic(temp_lang.successful, temp_lang.a_sent_address_sms, 'info');
            } else if (data == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });
});

