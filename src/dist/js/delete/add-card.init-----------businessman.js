const bank = $('#bank-name');
const cart = $('input[name="cart-number[]"]');
const account = $('#account-number');
const iban = $('#cart-iban');

$('#submit-card').on('click', function () {
    const _btn = $(this);

    let cartNumber = '';
    cart.each(function (index, element) {
        cartNumber += $(element).val();
    });

    if (bank.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_name_required, 'warning', 3500);
    } else if (cartNumber == '' || cartNumber.length != 16) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_card_required, 'warning', 3500);
    } else if (account.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_account_required, 'warning', 3500);
    } else if (iban.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_iban_required, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: .5
        });

        const params = {
            action: 'new-credit-card',
            bank: bank.val(),
            cart: cartNumber,
            account: account.val(),
            iban: "(IR)" + iban.val(),
            token: $('#token').val()
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.alert_success_submit_credit_card, 'success', 2500);
                        setTimeout(() => {
                            window.location.href =`/businessman/credit-cards`;
                        }, 3000);
                    } else {
                        $('#token').val(json.response);
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                } catch (e) {
                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        })
    }
});