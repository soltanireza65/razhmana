$(document).ready(function () {

    $(".mj-search-btn").click(function () {
        $(".mj-trx-serach-form").toggleClass("searchopen")
        $(".mj-filter-dropdown").removeClass("filteropen")
    });
    $(".mj-filter-btn").click(function () {
        $(".mj-filter-dropdown").toggleClass("filteropen")
        $(".mj-trx-serach-form").removeClass("searchopen")
    });


    const loaderLottie = $('.mj-trx-list-load');
    const modalDetail = $('#show-modal');
    const txSearch = $('#tx-search');
    let transactionCount = 0;
    let transactionsAllCount = 0;
    let transactionsAllResponse = [];
    let flag = true;

    function getListTransactions() {
        let status = $('[data-tj-status]').data('tj-status')
        let currency = $('[data-tj-currency]').data('tj-currency')
        const params = {
            action: 'get-all-list-transactions',
            status: status,
            currency: currency,
        };
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                const json = JSON.parse(response);
                transactionsAllCount = json.response.length;
                transactionsAllResponse = json.response;

                if (transactionsAllCount > 0) {
                    setHtmlTransactions(json.response)
                } else {
                    flag = false;
                    setEmpty();
                }

            }
        });
    }

    function setHtmlTransactions(loop) {
        let temp = '';
        for (let i = 0; i < 10; i++) {
            if (transactionCount < transactionsAllCount) {

                let type = '<img src="/dist/images/wallet/deposit-trx.svg" alt="">';
                if (loop[transactionCount].TransactionType == 'withdraw') {
                    type = '<img src="/dist/images/wallet/withdraw-trx.svg" alt="">';
                }

                let status = 'trxpending';
                if (loop[transactionCount].TransactionColor == 'green') {
                    status = 'accept';
                } else if (loop[transactionCount].TransactionColor == 'red') {
                    status = 'abort';
                }

                let title = lang_vars.u_wallet_deposit
                if (loop[transactionCount].TransactionDestination) {
                    title = loop[transactionCount].TransactionDestination;
                }

                temp = temp + '<div data-tj-detail="' + loop[transactionCount].TransactionId + '" class="mj-trx-item"><a href="javascript:void(0);"> <div class="mj-trx-info"> <div class="mj-trx-status ' + status + '">' + type + '</div><div class="mj-trx-detail"><span class="mj-trx-account-name">' + title + '</span><div class="mj-trx-date d-flex align-items-center"><span class="trx-time">' + loop[transactionCount].TransactionTime1 + '</span><div class="mj-line-divider mx-1">|</div><span class="trx-date">' + loop[transactionCount].TransactionTime2 + '</span></div></div></div><div class="mj-trx-value"><span>' + loop[transactionCount].TransactionAmount + ' </span><span>' + loop[transactionCount].TransactionCurrency + '</span></div></a></div>';
                transactionCount += 1;
                flag = true;
            } else {
                flag = false;
                console.log(5555555555555)
            }
        }
        loaderLottie.addClass('d-none')
        $('.mj-transaction-items').append(temp);


    }

    function setEmpty() {
        const temp = '<div class="mj-trx-empty-item text-center">\n' +
            '    <img style="width: 90%" src="/dist/images/wallet/notrx.svg" alt="notrx">\n' +
            '</div>';
        $('.mj-transaction-items').html(temp);
    }

    getListTransactions()

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 30) {
            if (flag) {
                flag = false;
                loaderLottie.removeClass('d-none')
                setTimeout(() => {
                    console.log(transactionCount)
                    setHtmlTransactions(transactionsAllResponse);

                }, 1500)
            }
        }
    });

    $(document).on('click', ".mj-trx-item", function () {
        // viewDetail.on('click', function () {
        let _this = $(this);
        modalDetail.find('.modal-body').html('');

        let showId = _this.attr('data-tj-detail');
        let result = transactionsAllResponse.find(x => x.TransactionId == showId);

        let status = lang_vars.tx_status_unknown;
        if (result.TransactionStatus == 'completed') {
            status = lang_vars.tx_status_completed;
        } else if ($.inArray(result.TransactionStatus, ['pending', 'pending_deposit']) != -1) {
            status = lang_vars.tx_status_pending;
        } else if ($.inArray(result.TransactionStatus, ['rejected', 'rejected_deposit']) != -1) {
            status = lang_vars.tx_status_rejected;
        } else if (result.TransactionStatus == 'expired') {
            status = lang_vars.tx_status_expired;
        } else if (result.TransactionStatus == 'unpaid') {
            status = lang_vars.tx_status_unpaid;
        } else if (result.TransactionStatus == 'paid') {
            status = lang_vars.tx_status_paid;
        }

        let type = ''
        if (result.TransactionType == 'deposit') {
            if (result.TransactionDepositType == 'receipt') {
                type = lang_vars.tx_type_deposit_receipt;
            } else {
                type = lang_vars.tx_type_deposit_online;
            }
        } else {
            type = lang_vars.wallet_withdraw;
        }

        let temp1 = '';
        if (result.TransactionType == 'withdraw') {
            temp1 = '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.tx_destination + ' :</span> <span dir="ltr"> <?= $tx->' + result.TransactionDestination + '</span></div>' +
                '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.card_number + ' :</span> <span dir="ltr"> <?= $tx->' + result.TransactionCardNumber + '</span></div>';
        }

        let temp2 = '';
        if (result.TransactionDepositType == 'receipt') {
            temp2 = '<div class="mj-tx-item py-2"><span>' + lang_vars.tx_receipt + ' :</span><img class="d-block w-100 mt-2 mx-auto" src="' + result.TransactionReceipt + '" alt="receipt" style="max-width: 400px;"></div>';
        }
        let temp3 = (result.TransactionAuthority) ? result.TransactionAuthority : "-";
        let temp4 = (result.TransactionTrackingCode) ? result.TransactionTrackingCode : "-";
        const htmlDetail =
            '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.transaction_list_date + ' :</span><span dir="ltr">' + result.TransactionTime1 + ' | ' + result.TransactionTime2 + ' </span></div>' +
            '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.tx_authority + ' :</span><span dir="ltr">' + temp3 + '</span></div>' +
            '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.tx_tacking_code + ' :</span><span dir="ltr">' + temp4 + '</span></div>' +
            '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.tx_status + ' :</span><span dir="ltr">' + status + '</span></div>' +
            '<div class="mj-tx-item d-flex align-items-center justify-content-between py-2"><span>' + lang_vars.tx_type + ' :</span><span dir="ltr">' + type + '</span></div>' +
            temp1 + temp2;
        modalDetail.find('.modal-body').html(htmlDetail)
        modalDetail.modal('show');

    });

    // $(window).bind('scroll', function() {
    //     if($(window).scrollTop() >= $('.mj-transaction-items').offset().top + $('.mj-transaction-items').outerHeight() - window.innerHeight) {
    //         console.log('end reached');
    //     }
    // });

    txSearch.on('input', function () {
        let search = $(this).val().trim();

        $('.mj-transaction-items').html('')
        transactionCount = 0;
        transactionsAllCount = 0;
        transactionsAllResponse = [];
        flag = true;

        let status = $('[data-tj-status]').data('tj-status')
        let currency = $('[data-tj-currency]').data('tj-currency')
        const params = {
            action: 'get-all-list-transactions',
            status: status,
            currency: currency,
            search: search,
        };
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                const json = JSON.parse(response);

                transactionsAllCount = json.response.length;
                transactionsAllResponse = json.response;
                if (transactionsAllCount > 0) {
                    setHtmlTransactions(json.response)
                } else {
                    flag = false;
                    setEmpty();
                }
            }
        });
    });

    function find(arr, search) {
        var result = [];
        var res_index = [];

        $.each(arr, function (key0, value0) {
            $.each(value0, function (key, value) {
                value = (value != null) ? value.toString() : ''

                if (value.includes(search)) {
                    if ($.inArray(value0.TransactionId, res_index) !== -1) {

                    } else {
                        res_index.push(value0.TransactionId);
                        result.push(value0);
                    }
                }
            });
        });

        return result;
    }

});
