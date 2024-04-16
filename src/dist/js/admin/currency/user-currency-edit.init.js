let temp_lang = JSON.parse(var_lang);

$('#btnSubmit').click(function () {
    let balance = $(this).data('mj-balance-id');
    let balanceValue = $('#balanceValue').val().trim();
    let balanceFrozen = $('#balanceFrozen').val().trim();
    let token = $('#token').val().trim();

    if (parseInt(balance) >= 0) {
        const BTN = Ladda.create(document.querySelector('#btnSubmit'));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'user-balance-edit',
            balance: balance,
            balanceValue: balanceValue,
            balanceFrozen: balanceFrozen,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');

                // const myArray = data.split(" ");
                if (data == 'successful') {
                    // $('#token').val(myArray[1]);
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
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

