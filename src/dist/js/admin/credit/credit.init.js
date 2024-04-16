let temp_lang = JSON.parse(var_lang);

$('.setSubmitBtn').click(function () {

    let btn = $(this).prop('id');
    let BTNN = Ladda.create(document.querySelector('#' + btn));
    let token = $('#token').val().trim();

    let creditID = $(this).data('mj-id');
    let lists = ["accepted", "rejected", "pending", "deleted"];

    if (creditID > 0 && token.length > 0 && jQuery.inArray(btn, lists) != -1)
        BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'change-card-bank-status',
        token: token,
        creditID: creditID,
        status: btn,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            // console.log(data);
            BTNN.remove();
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $(".btn").attr("disabled", false);

                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                let sss = temp_lang.accepted;
                if (btn == 'accepted') {
                    sss = temp_lang.accepted;
                } else if (btn == 'rejected') {
                    sss = temp_lang.rejected;
                } else if (btn == 'pending') {
                    sss = temp_lang.pending;
                } else {
                    sss = temp_lang.deleted;
                }
                $('#change_credit_status').html(sss);
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

$('#deleteBTN').click(function () {

    let BTNN = Ladda.create(document.querySelector('#deleteBTN'));
    let token = $('#token').val().trim();

    let creditID = $(this).data('mj-id');


    if (creditID > 0 && token.length > 0)
        BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'delete-card-bank',
        token: token,
        creditID: creditID,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            // console.log(data);
            BTNN.remove();

            if (data == 'successful') {
                $(".btn").attr("disabled", true);
                toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "info");
                window.setTimeout(
                    function () {
                        window.location.replace("/admin/credit");
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

$('#inquiryBTN').click(function () {
    let BTNN = Ladda.create(document.querySelector('#inquiryBTN'));
    const token = $('#token').val().trim();
    BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'inquiry-card-bank',
        token: token,
        creditNumber: $(this).data('tj-card'),
        creditID: $(this).data('mj-id'),
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();

            if (data == 'successful') {
                $(".btn").attr("disabled", true);
                toastNotic(temp_lang.successful, temp_lang.a_inquiry_success, "info");
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
            } else if (data == "inquiry_error") {
                toastNotic(temp_lang.warning, temp_lang.a_inquiry_error, 'warning');
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }

        }
    });
});