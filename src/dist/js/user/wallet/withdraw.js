// open & close
$(".mj-account-card-title").click(function () {
    $(this).toggleClass('opened')
    $(this).parent().children('.mj-withdraw-input').toggleClass('showed')
})


// add Card
const bank = $('#account-name');
const cart = $('input[name="cart-number[]"]');
const account = $('#account-number');
const iban = $('#cart-iban');
const token = $('#token');
const btnSubmit = $('#submit-card');
const cardContainer = document.getElementsByClassName('mj-credit-card-num')[0]
btnSubmit.on('click', function () {
    const _btn = $(this);

    let cartNumber = '';
    cart.each(function (index, element) {
        cartNumber += $(element).val().trim();
    });
    if (bank.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_name_required, 'warning', 3500);
    } else if (cartNumber == '' || cartNumber.length != 16) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_card_required, 'warning', 3500);
    } else if (account.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_account_required, 'warning', 3500);
    } else if (iban.val().trim() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_bank_iban_required, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: .5
        });
        const params = {
            action: 'new-credit-card',
            bank: bank.val().trim(),
            cart: cartNumber,
            account: account.val().trim(),
            iban: iban.val().trim(),
            currency: btnSubmit.data('tj-currency'),
            token: token.val().trim(),
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
                            // window.location.replace(`/user/credit-cards`);
                            window.location.reload();
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
cardContainer.onkeyup = function (e) {
    var target = e.srcElement || e.target;
    var maxLength = parseInt(target.attributes["maxlength"].value, 10);
    var myLength = target.value.length;
    if (myLength >= maxLength) {
        var next = target;
        while (next = next.nextElementSibling) {
            if (next == null)
                break;
            if (next.tagName.toLowerCase() === "input") {
                next.focus();
                break;
            }
        }
    } else if (myLength === 0) {
        var previous = target;
        while (previous = previous.previousElementSibling) {
            if (previous == null)
                break;
            if (previous.tagName.toLowerCase() === "input") {
                previous.focus();
                break;
            }
        }
    }
}


//
const btnWithdraw = $('button.mj-withdraw-main-btn[type=button]');
const withdrawPrice = $('input[name="withdraw-input"]');
withdrawPrice.on('input', function () {
    let _value = $(this).val().replaceAll(',', '');
    if (/\D/g.test(_value)) {
        // Filter non-digits from input value.
        _value = _value.replace(/\D/g, '');
        $(this).val(addCommas(_value))
    } else {
        $(this).val(addCommas(_value))
    }
});

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


btnWithdraw.on('click', function () {
    const _this = $(this);
    let withdrawAmount = _this.parent().find('input[name="withdraw-input"]').val().replaceAll(',', '').trim()
    withdrawAmount = parseInt(withdrawAmount);
    let withdrawCurrency = _this.data('tj-currency')
    let withdrawDestination = _this.data('tj-credit')


    if (withdrawAmount == '' || isNaN(withdrawAmount) || withdrawAmount == 0) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_withdraw_amount, 'warning', 3500);
    } else if (withdrawCurrency == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_withdraw_destination, 'warning', 3500);
    } else if (withdrawDestination == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_withdraw_destination, 'warning', 3500);
    } else {
        _this.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });
        _this.addClass('tj-a-loader-2');

        const params = {
            action: 'withdraw-request',
            amount: withdrawAmount,
            currency: withdrawCurrency,
            destination: withdrawDestination,
            token: $('#token').val().trim(),
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {




                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        sendNotice(lang_vars.alert_success, lang_vars.alert_success_withdraw, 'success', 2500);
                        setTimeout(() => {
                            _this.delay(1500).removeClass('tj-a-loader-2');
                            _this.removeAttr('disabled').css({
                                opacity: 1
                            });
                            window.location.href = `/user/wallet`;
                        }, 3000);
                    } else if (json.status == -20) {
                        setTimeout(() => {
                            _this.delay(1500).removeClass('tj-a-loader-2');
                            _this.removeAttr('disabled').css({
                                opacity: 1
                            });
                        }, 1500);
                        $('#token').val(json.response);
                        sendNotice(lang_vars.alert_error, lang_vars.withdraw_amount_low, 'error', 5000);
                    } else if (json.status == -30) {
                        setTimeout(() => {
                            _this.delay(1500).removeClass('tj-a-loader-2');
                            _this.removeAttr('disabled').css({
                                opacity: 1
                            });
                        }, 1500);
                        $('#token').val(json.response);
                        sendNotice(lang_vars.alert_error, lang_vars.u_no_card_bank_accepted, 'error', 5000);
                    } else {
                        setTimeout(() => {
                            _this.delay(1500).removeClass('tj-a-loader-2');
                            _this.removeAttr('disabled').css({
                                opacity: 1
                            });
                        }, 1500);
                        $('#token').val(json.response);
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                    }
                } catch (e) {
                    setTimeout(() => {
                        _this.delay(1500).removeClass('tj-a-loader-2');
                        _this.removeAttr('disabled').css({
                            opacity: 1
                        });
                    }, 1500);
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                }
            }
        })
    }


});


$(document).ready(function () {
    $(".mj-search-btn").click(function () {
        $(".mj-trx-serach-form").toggleClass("searchopen")
    });
    $(".mj-filter-btn").click(function () {
        $(".mj-filter-dropdown").toggleClass("filteropen")
    });
    $("#show-balance-more").click(function () {
        if ($(this).attr('src') == '/dist/images/wallet/down-arrow.svg') {
            $(".mj-wallet-rial-balance-withdraw ").addClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/up-arrow.svg')
        } else {
            $(".mj-wallet-rial-balance-withdraw ").removeClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/down-arrow.svg')
        }
    });
});