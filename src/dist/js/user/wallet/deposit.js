$(document).ready(function () {


    // *************** action card
    /*   $(".mj-search-btn").click(function () {
           $(".mj-trx-serach-form").toggleClass("searchopen")
       });
       $(".mj-filter-btn").click(function () {
           $(".mj-filter-dropdown").toggleClass("filteropen")
       });*/
    $("#show-balance-more").click(function () {
        if ($(this).attr('src') == '/dist/images/wallet/down-arrow.svg') {
            $(".mj-wallet-rial-balance-withdraw ").addClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/up-arrow.svg')
        } else {
            $(".mj-wallet-rial-balance-withdraw ").removeClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/down-arrow.svg')
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


    // ************************  deposit Online
    const depositOnline = $('#deposit-online');
    const submitOnline = $('#submit-online');

    depositOnline.on('input', function () {
        let _value = $(this).val().replaceAll(',', '');
        if (/\D/g.test(_value)) {
            // Filter non-digits from input value.
            _value = _value.replace(/\D/g, '');
            $(this).val(addCommas(_value))
        } else {
            $(this).val(addCommas(_value))
        }
    });


    submitOnline.on('click', function () {
        const _btn = $(this);

        if (depositOnline.val() == '') {
            depositOnline.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_amount, 'warning', 3500);
        } else {

            // modalProcessing.show();
            //
            // _btn.attr('disabled', true).css({
            //     transition: 'all .3s',
            //     opacity: 0.5
            // });

            // const params = {
            // action: 'deposit-online',
            // amount: depositOnline.val().replaceAll(',', '').trim(),
            // currency: submitOnline.data('tj-currency'),
            // token: $('#token').val().trim()
            // };
            // $.ajax({
            //     url: '/api/ajax',
            //     type: 'POST',
            //     data: JSON.stringify(params),
            //     success: function (response) {
            //         // setTimeout(() => {
            //         //     // modalProcessing.hide();
            //         //     // modalSubmitting.show();
            //         // }, 500);
            //         console.log(response)
            //         try {
            //             const json = JSON.parse(response);
            //             if (json.status == 200) {
            //                 const html = `
            //                 <i class="fe-check-circle d-block text-success mb-3" style="font-size: 72px;"></i>
            //                 <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
            //                 <p class="mj-font-11 mb-0">${lang_vars['redirecting'].replaceAll('#PAGE#', lang_vars['d_transactions_title'])}</p>
            //                 `;
            //                 $('#submitting-alert').html(html);
            //
            //                 sendNotice(lang_vars.alert_success, lang_vars.alert_success_wallet_deposit_receipt, 'success', 2500);
            //                 setTimeout(() => {
            //                     window.location.replace(`/user/transactions`);
            //                 }, 3000)
            //             } else {
            //                 const html = `
            //                 <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
            //                 <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
            //                 `;
            //                 $('#submitting-alert').html(html);
            //
            //                 _btn.removeAttr('disabled').css({
            //                     opacity: 1
            //                 });
            //                 $('#token').val(json.response);
            //                 sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
            //             }
            //         } catch (e) {
            //             const html = `
            //                 <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
            //                 <h6 class="mj-font-13">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['wallet_deposit_submit_receipt'])}</h6>
            //                 `;
            //             $('#submitting-alert').html(html);
            //
            //             // _btn.removeAttr('disabled').css({
            //             //     opacity: 1
            //             // });
            //             sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
            //         }
            //     }
            // })/**/
            let amount = parseInt(depositOnline.val().replaceAll(',', '').trim()) * 10;
            // window.location.href = '/start-payment/' + amount;
            window.open('/start-payment/' + amount, '_blank')
        }
    })


    // ************************ deposit Receipt
    const uploadReceiptDiv = $('#upload-receipt-div')
    const respectImg = $('#respect-img');
    const receiptInput = $('#file-input');
    const submitRespect = $('#submit-respect');
    const amountOffline = $('#amount-offline');
    const txAuthority = $('#authority-offline');
    let receiptFile = null;
    let currency = submitRespect.data('tj-currency');

    //upload img
    uploadReceiptDiv.on('click', function (e) {
        console.log(receiptInput)
        e.preventDefault();
        receiptInput[0].click();
    })
    respectImg.on('click', function (e) {
        console.log(33)
        e.preventDefault();
        receiptInput[0].click();
    })
    receiptInput.on('change', function () {
        read()
        receiptFile = $(this).prop('files')[0];
    })

    function read() {
        var preview = document.querySelector('img#respect-img');
        var file = document.querySelector('input#file-input[type=file]').files[0];
        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
        }
    }

    amountOffline.on('input', function () {
        let _value = $(this).val().replaceAll(',', '');
        if (/\D/g.test(_value)) {
            // Filter non-digits from input value.
            _value = _value.replace(/\D/g, '');
            $(this).val(addCommas(_value))
        } else {
            $(this).val(addCommas(_value))
        }
    });

    submitRespect.on('click', function () {
        const _this = $(this);

        amountOffline.parent().removeClass('border-danger');
        txAuthority.parent().removeClass('border-danger');
        uploadReceiptDiv.children().removeClass('border-danger');

        if (amountOffline.val().trim() == '' || amountOffline.val().trim() == 0) {
            amountOffline.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_amount, 'warning', 3500);
        } else if (currency == '' || currency == 0) {
            sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_monetary_unit, 'warning', 3500);
        } else if (txAuthority.val().trim() == '') {
            txAuthority.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_authority, 'warning', 3500);
        } else if (receiptFile == null || receiptFile == undefined) {
            uploadReceiptDiv.children().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars.alert_wallet_deposit_receipt, 'warning', 3500);
        } else {
            _this.addClass('tj-a-loader-4');
            _this.attr('disabled', true).css({
                transition: 'all .3s',
                opacity: 0.5
            });

            let params = new FormData();
            params.append('action', 'deposit-with-receipt');
            params.append('amount', amountOffline.val().trim().replaceAll(',', ''));
            params.append('currency', currency);
            params.append('authority', txAuthority.val().trim());
            params.append('receipt', receiptFile);
            params.append('token', $('#token').val().trim());

            $.ajax({
                url: '/api/ajax',
                type: 'POST',
                data: params,
                contentType: false,
                processData: false,
                success: function (response) {

                    const json = JSON.parse(response);
                    if (json.status == 200) {

                        sendNotice(lang_vars.alert_success, lang_vars.alert_success_wallet_deposit_receipt, 'success', 2500);
                        setTimeout(() => {
                            window.location.href = `/user/wallet`;
                        }, 3000)
                    } else {
                        // _btn.removeAttr('disabled').css({
                        //     opacity: 1
                        // });
                        setTimeout(() => {
                            _this.removeAttr('tj-a-loader-4');
                            _this.removeAttr('disabled').css({
                                opacity: 1
                            });
                        }, 500);

                        $('#token').val(json.response);
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 5000);
                    }

                }
            })
        }
    })


});

// $('#icondemo').filestyle({
//
//     iconName : 'glyphicon glyphicon-file',
//
//     buttonText : 'Select File',
//
//     buttonName : 'btn-warning'
//
// });