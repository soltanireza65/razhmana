let temp_lang = JSON.parse(var_lang);

$('#btn_fa_IR').click(function () {
    var BTN = Ladda.create(document.querySelector('#btn_fa_IR'));
    BTN.start();
    let values = $('#textarea_fa_IR').val().trim();
    let token = $('#token').val().trim();
    if (token) {
        let data = {
            action: 'language-fa-ir',
            values: values,
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
                } else if (myArray[0] == 'error') {
                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        toastNotic(temp_lang.error, temp_lang.token_error);
    }
});

$('#btn_en_US').click(function () {
    var BTN = Ladda.create(document.querySelector('#btn_en_US'));
    BTN.start();
    let values = $('#textarea_en_US').val().trim();
    let token = $('#token').val().trim();
    if (token) {
        let data = {
            action: 'language-en-US',
            values: values,
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
                } else if (myArray[0] == 'error') {
                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        toastNotic(temp_lang.error, temp_lang.token_error);
    }
});


$('#btn_tr_Tr').click(function () {
    var BTN = Ladda.create(document.querySelector('#btn_tr_Tr'));
    BTN.start();
    let values = $('#textarea_tr_Tr').val().trim();
    let token = $('#token').val().trim();
    if (token) {
        let data = {
            action: 'language-tr-Tr',
            values: values,
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
                } else if (myArray[0] == 'error') {
                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        toastNotic(temp_lang.error, temp_lang.token_error);
    }
});

$('#btn_ru_RU').click(function () {
    var BTN = Ladda.create(document.querySelector('#btn_ru_RU'));
    BTN.start();
    let values = $('#textarea_ru_RU').val().trim();
    let token = $('#token').val().trim();
    if (token) {
        let data = {
            action: 'language-ru-RU',
            values: values,
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
                } else if (myArray[0] == 'error') {
                    $('#token').val(myArray[1]);
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    } else {
        toastNotic(temp_lang.error, temp_lang.token_error);
    }
});

