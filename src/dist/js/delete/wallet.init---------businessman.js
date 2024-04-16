const depositAmount = $('#deposit-amount');
const currency = $('#currency-unit');
const txAuthority = $('#tx-authority');
const receipt = $('#receipt');
let receiptFile = null;
const modalProcessing = new bootstrap.Modal($('#modal-processing'));
const modalSubmitting = new bootstrap.Modal($('#modal-submitted'));

depositAmount.on('input', function () {
    const value = new Intl.NumberFormat().format(fixNumbers($(this).val()).replaceAll(',', ''));
    if (isNaN(value.replaceAll(',', ''))) {
        $(this).val(0)
    } else {
        $(this).val(value)
    }
});

receipt.on('change', function () {
    receiptFile = $(this).prop('files')[0];
});

$('#submit-receipt').on('click', function () {
    const _btn = $(this);

    if (depositAmount.val() == '' || depositAmount.val() == 0) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_amount, 'warning', 3500);
    } else if (currency.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_monetary_unit, 'warning', 3500);
    } else if (txAuthority.val() == '') {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_authority, 'warning', 3500);
    } else if (receiptFile == null || receiptFile == undefined) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_receipt, 'warning', 3500);
    } else {
        modalProcessing.show();

        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        let params = new FormData();
        params.append('action', 'deposit-with-receipt');
        params.append('amount', depositAmount.val().replaceAll(',', ''));
        params.append('currency', currency.val());
        params.append('authority', txAuthority.val());
        params.append('receipt', receiptFile);
        params.append('token', $('#token-receipt').val());

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: params,
            contentType: false,
            processData: false,
            success: function (response) {
                setTimeout(() => {
                    modalProcessing.hide();
                    modalSubmitting.show();
                }, 500);

                try {
                    const json = JSON.parse(response);
                    if (json.status == 200) {
                        const html = `
                        <i class="fe-check-circle d-block text-success mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
                        <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_transactions_title'])}</p>
                        `;
                        $('#submitting-alert').html(html);

                        sendNotice(lang_vars.alert_success, lang_vars.alert_success_wallet_deposit_receipt, 'success', 2500);
                        setTimeout(() => {
                            window.location.href `/businessman/transactions`;
                        }, 3000)
                    } else {
                        const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
                        `;
                        $('#submitting-alert').html(html);

                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        $('#token').val(json.response);
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                    }
                } catch (e) {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                }
            }
        })
    }
});


const withdrawAmount = $('#withdraw-amount');
const withdrawDestination = $('#withdraw-dest');


withdrawAmount.on('input', function () {
    const value = new Intl.NumberFormat().format(fixNumbers($(this).val()).replaceAll(',', ''));
    if (isNaN(value.replaceAll(',', ''))) {
        $(this).val(0)
    } else {
        $(this).val(value)
    }
});


withdrawDestination.select2({
    dropdownParent: $('.mj-custom-select'),
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item">
                ${title}
            </span>
        `);
    }
});


$('a[data-select-all]').on('click', function () {
    withdrawAmount.val($(this).find('span:first-child').text().replaceAll(',', ''));
    withdrawAmount.trigger('input');
});


$('#submit-withdraw').on('click', function () {
    const _btn = $(this);

    if (withdrawAmount.val() == '' || withdrawAmount.val() == 0) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_withdraw_amount, 'warning', 3500);
    } else if (withdrawDestination.val() == '' || withdrawDestination.val() == -1) {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_withdraw_destination, 'warning', 3500);
    } else {
        _btn.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: 0.5
        });

        const params = {
            action: 'withdraw-request',
            amount: withdrawAmount.val().replaceAll(',', ''),
            destination: withdrawDestination.val(),
            currency: $(this).data('currency'),
            token: $('#token-withdraw').val()
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
                            window.location.href = `/businessman/transactions`;
                        }, 3000);
                    } else {
                        $('#token').val(json.response);
                        _btn.removeAttr('disabled').css({
                            opacity: 1
                        });
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                    }
                } catch (e) {
                    _btn.removeAttr('disabled').css({
                        opacity: 1
                    });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                }
            }
        })
    }
});
