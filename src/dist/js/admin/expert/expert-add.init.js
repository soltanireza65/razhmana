let temp_lang = JSON.parse(var_lang);

$('.setSubmitBtn').click(function () {

    let btn = $(this).attr('id');
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
            action: 'expert-add',
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
                    $(".btn").attr('disabled', true);
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/expert/edit/" + myArray[1]);
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


$('#firstname').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_firstname').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_firstname').html('<b class="text-danger">' + len + '</b>');
    }
});
$('#lastname').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_lastname').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_lastname').html('<b class="text-danger">' + len + '</b>');
    }
});
$('#mobile').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 11) {
        $('#length_mobile').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_mobile').html('<b class="text-danger">' + len + '</b>');
    }
});