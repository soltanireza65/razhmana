(function () {

    const bank = $('#account-name');
    const cart = $('input[name="cart-number[]"]');
    const account = $('#account-number');
    const iban = $('#cart-iban');
    const token = $('#token');
    const btnSubmit = $('#submit-card');
    const CreditDelete = $('.mj-accounts-operations');
    const btnDelete = $('#btn-delete');
    const cardContainer = document.getElementsByClassName('mj-credit-card-num')[0]
    CardIdDeleted = 0;

    const modalDelete = $('#delete-card-modal');

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
            _btn.addClass('tj-a-loader-3');

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
                            setTimeout(() => {
                                _btn.delay(1500).removeClass('tj-a-loader-3');
                                _btn.removeAttr('disabled').css({
                                    opacity: 1
                                });
                            }, 1500);
                            sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        }
                    } catch (e) {
                        setTimeout(() => {
                            _btn.delay(1500).removeClass('tj-a-loader-3');
                            _btn.removeAttr('disabled').css({
                                opacity: 1
                            });
                        }, 1500);
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


    $(".mj-account-card-title").click(function () {
        $(this).parent().children('.mj-accounts-operations').toggleClass('deleteacntshowed')
        $(this).toggleClass('opened')
        $(this).parent().children('.mj-accounts-detail').toggleClass('showed')
    })


    CreditDelete.on('click', function () {
        const _this = $(this);
        modalDelete.modal('show')
        let CardName = _this.parent().find('[data-tj-credit-title]').html();
        let CardId = _this.parent().find('[data-tj-credit-title]').data('tj-credit-title');
        CardIdDeleted=CardId
        modalDelete.find('.modal-body').children().eq(2).text(CardName)
    });



    btnDelete.on('click', function () {
        const _this = $(this);


        if (CardIdDeleted !=0) {
            _this.attr('disabled', true).css({
                transition: 'all .3s',
                opacity: .5
            });
            _this.addClass('tj-a-loader-3');

            const params = {
                action: 'delete-credit-card',
                card: CardIdDeleted,
                token: $('#token').val().trim()
            };
            $.ajax({
                url: '/api/ajax',
                type: 'POST',
                data: JSON.stringify(params),
                success: function (response) {

                    setTimeout(() => {
                        _this.removeAttr('disabled').css({
                            opacity: 1
                        });
                        _this.removeClass('tj-a-loader-3');
                    }, 3000);


                    try {
                        const json = JSON.parse(response);
                        if (json.status == 200) {
                            sendNotice(lang_vars.alert_success, lang_vars.alert_success_delete_credit_card, 'success', 2500);
                            CardIdDeleted = 0;
                            // modalDelete.find('.modal-body').children().eq(2).text('')
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        } else {
                            // _this.removeAttr('disabled').css({
                            //     opacity: 1
                            // });
                            $('#token').val(json.response);
                            sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        }
                    } catch (e) {
                        // _this.removeAttr('disabled').css({
                        //     opacity: 1
                        // });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    }
                }
            })
        }else{
            sendNotice(lang_vars.alert_error, lang_vars.u_no_card_bank_accepted, 'error', 3500);
        }
    })


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
                $(this).attr('src' , '/dist/images/wallet/up-arrow.svg')
            }else{
                $(".mj-wallet-rial-balance-withdraw ").removeClass("balanceopen")
                $(this).attr('src' , '/dist/images/wallet/down-arrow.svg')
            }
        });
    });
})();