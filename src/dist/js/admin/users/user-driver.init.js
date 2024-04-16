let temp_lang = JSON.parse(var_lang);

$('.btnChangeUSerStatus').click(function () {
    let status = $(this).attr('data-type');
    let userID = $(this).attr('data-user-id');
    let id = $(this).attr('id');
    let token = $('#token').val().trim();

    var list_status = ["inactive", "active", "suspend", "guest"];
    if (jQuery.inArray(status, list_status) != -1 && userID.length > 0) {

        var BTN = Ladda.create(document.querySelector('#' + id));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'change-driver-status',
            status: status,
            userID: userID,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');

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
    }
});


$('.deleteSession').click(function () {
    let user_Id = $(this).data('id-user');
    let number = $(this).attr('data-number');
    let expire = $(this).attr('data-expire');
    let token = $('#token').val().trim();


    if (user_Id > 0 && number.length > 0) {


        $("a.deleteSession").addClass('d-none');

        let data = {
            action: 'delete-businessman-session',
            userId: user_Id,
            number: number,
            expire: expire,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                if (data == 'successful') {
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


$('#giftSubmit').click(function () {
    let user_Id = $(this).data('mj-user-id');
    let title = $('#giftTitle').val();
    let count = $('#giftCount').val();
    let action = $('#giftAction option:selected').val();
    let token = $('#token').val().trim();
    var lists = ["add", "low"];

    if (user_Id > 0 && parseInt(count) != 0 && jQuery.inArray(action, lists) != -1 && token.length > 10) {
        let BTN = Ladda.create(document.querySelector('#giftSubmit'));
        BTN.start();


        let data = {
            action: 'change-driver-gift',
            userId: user_Id,
            count: count,
            actions: action,
            title: title,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTN.remove();
                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {

                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    let valueDefault = parseInt($('#giftValueID').text());
                    if (action == 'low') {
                        $('#giftValueID').text(valueDefault - parseInt(count))
                    } else {
                        $('#giftValueID').text(valueDefault + parseInt(count))
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


    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


$('#giftTitle').keyup(function () {
    var len1 = $(this).val().trim().length;
    if (len1 > 2) {
        $('#giftTitle_text').text(len1);
    } else {
        $('#giftTitle_text').text(len1);
    }
});


$('#scoreSubmit').click(function () {
    let user_Id = $(this).data('mj-user-id');
    let title = $('#scoreTitle').val();
    let count = $('#scoreCount').val();
    let action = $('#scoreAction option:selected').val();
    let token = $('#token').val().trim();
    var lists = ["add", "low"];

    if (user_Id > 0 && parseInt(count) != 0 && jQuery.inArray(action, lists) != -1 && token.length > 10) {
        let BTN = Ladda.create(document.querySelector('#scoreSubmit'));
        BTN.start();


        let data = {
            action: 'change-driver-score',
            userId: user_Id,
            count: count,
            actions: action,
            title: title,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTN.remove();
                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {

                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    let valueDefault = parseInt($('#scoreValueID').text());
                    if (action == 'low') {
                        $('#scoreValueID').text(valueDefault - parseInt(count))
                    } else {
                        $('#scoreValueID').text(valueDefault + parseInt(count))
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


    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});


$('#scoreTitle').keyup(function () {
    var len1 = $(this).val().trim().length;
    if (len1 > 2) {
        $('#scoreTitle_text').text(len1);
    } else {
        $('#scoreTitle_text').text(len1);
    }
});


$('.submit_change_authentication').click(function () {
    let _this = $(this);
    let option_slug = $(this).data('tj-option-slug');
    let status = $(this).data('tj-status');
    let userID = $('#UserID').data('mj-user-id');
    let btnType = $(this).data('tj-value');
    let newValue = _this.parent('div.input-group').find('.valueClass').val();
    let token = $('#token2').val().trim();
    let field_type = _this.parent('div.input-group').find('.valueClass').attr('type');
    if (field_type == "file") {
        newValue = _this.parent('div.input-group').find('.valueClass')[0].files[0]
    }
    let idBtn = $(this).prop('id');
    let BTN = Ladda.create(document.querySelector('#' + idBtn));
    BTN.start();

    let params = new FormData();
    params.append('action', 'authentication-driver-info');
    params.append('slug', option_slug);
    params.append('status', status);
    params.append('userID', userID);
    params.append('newValue', newValue);
    params.append('type', field_type);
    params.append('btnType', btnType);
    params.append('token', token);

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: params,
        contentType: false,
        processData: false,
        success: function (data) {

            BTN.remove();


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

});

$('.btnChangeAuth').click(function () {
    let status = $(this).data('tj-status');
    let userID = $(this).data('tj-user-id');
    let id = $(this).attr('id');
    let token = $('#token2').val().trim();

    var list_status = ["accepted", "rejected"];
    if (jQuery.inArray(status, list_status) != -1 && userID > 0) {

        var BTN = Ladda.create(document.querySelector('#' + id));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'change-user-auth-status',
            status: status,
            userID: userID,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');

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
    }
});

$('#btnInquiry').click(function () {
    let userID = $(this).data('tj-user-id');
    let token = $('#token2').val().trim();

    let BTN = Ladda.create(document.querySelector('#btnInquiry'));
    BTN.start();
    $(".btn").attr('disabled', 'disabled');

    let data = {
        action: 'request-inquiry-user',
        userID: userID,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTN.remove();
            $(".btn").removeAttr('disabled');

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

});