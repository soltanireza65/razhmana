let temp_lang = JSON.parse(var_lang);

$('.setSubmitBtn').click(function () {

    let btn = $(this).attr('id');
    let id = $(this).data('tj-id');
    let firstname = $('#firstname').val().trim();
    let lastname = $('#lastname').val().trim();
    let mobile = $('#mobile').val().trim();
    let address = $('#address').val().trim();
    let description = $('#description').val().trim();
    let token = $('#token').val().trim();


    if (firstname.length > 2 && lastname.length > 0 && mobile.length >= 11) {

        var BTN = Ladda.create(document.querySelector('#' + btn));
        $(".btn").attr('disabled', true);
        BTN.start();


        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'expert-edit',
            id: id,
            firstname: firstname,
            lastname: lastname,
            mobile: mobile,
            address: address,
            description: description,
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
                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         window.location.reload();
                    //     },
                    //     2000
                    // );
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


$('#firstname').keyup(function () {
    let len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_firstname').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_firstname').html('<b class="text-danger">' + len + '</b>');
    }
});
let len0 = $('#firstname').val().trim().length;
if (len0 > 2) {
    $('#length_firstname').html('<b class="text-success">' + len0 + '</b>');
} else {
    $('#length_firstname').html('<b class="text-danger">' + len0 + '</b>');
}

$('#lastname').keyup(function () {
    let len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_lastname').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_lastname').html('<b class="text-danger">' + len + '</b>');
    }
});
let len1 = $('#lastname').val().trim().length;
if (len1 > 2) {
    $('#length_lastname').html('<b class="text-success">' + len1 + '</b>');
} else {
    $('#length_lastname').html('<b class="text-danger">' + len1 + '</b>');
}


$('#mobile').keyup(function () {
    let len = $(this).val().trim().length;
    if (len > 11) {
        $('#length_mobile').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_mobile').html('<b class="text-danger">' + len + '</b>');
    }
});
let len2 = $('#mobile').val().trim().length;
if (len2 > 11) {
    $('#length_mobile').html('<b class="text-success">' + len2 + '</b>');
} else {
    $('#length_mobile').html('<b class="text-danger">' + len2 + '</b>');
}



