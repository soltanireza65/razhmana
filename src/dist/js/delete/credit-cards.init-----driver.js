const table = $('#credit-cards-table');

table.DataTable({
    oLanguage: {
        sUrl: "/dist/libs/datatables.net-i18/fa.json",
    },
    searching: true,
    info: false,
    lengthChange: false,
    paging: false,
    ordering: false,
});

table.on('init.dt', function () {
    if ($('.hidden-search').length > 0) {
        $('.dataTables_filter').parent().html('');
    }
});

$('#card-search').on('input', function () {
    table.DataTable().search($(this).val()).draw()
});


$('button[data-load-more]').on('click', function () {
    const _btn = $(this);
    _btn.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: 0.5
    });

    const page = parseInt($(this).attr('data-page'));

    const params = {
        action: 'load-more-credit-cards',
        page: page + 1,
        status: status
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            if (response != '') {
                _btn.attr('data-page', page + 1).removeAttr('disabled').css({
                    opacity: 1
                });
                $('#credit-cards-list').append(response);
            } else {
                _btn.fadeOut(300);
            }
        }
    });
});

function cardDetail(element) {
    const number = $(element).data('number');
    const account = $(element).data('account');
    const iban = $(element).data('iban');
    const card = $(element).data('card');

    let template = $('#template-card-detail').html();
    template = template.replace('#NUMBER#', number);
    template = template.replace('#ACCOUNT#', account);
    template = template.replace('#IBAN#', iban);

    $('body').append(template);
    const modal = new bootstrap.Modal($('#card-detail-modal'));
    modal.show();

    $('#card-detail-modal').on('hide.bs.modal', function () {
        $(this).remove();
    });

    $('#delete-credit-card').on('click', function () {
        modal.hide();
        let verifyTemplate = $('#template-verify-delete').html();
        verifyTemplate = verifyTemplate.replace('#ACCOUNT#', account);
        verifyTemplate = verifyTemplate.replace('#CARD#', card);

        $('body').append(verifyTemplate);
        const verifyModal = new bootstrap.Modal($('#delete-card-modal'));
        verifyModal.show();

        $('#delete-card-modal').on('hide.bs.modal', function () {
            $(this).remove();
        });

        $('button[data-verify-delete]').on('click', function () {
            const _btn = $(this);
            _btn.attr('disabled', true).css({
                transition: 'all .3s',
                opacity: 1
            });

            const params = {
                action: 'delete-credit-card',
                card: $(this).data('card'),
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
                            sendNotice(lang_vars.alert_success, lang_vars.alert_success_delete_credit_card, 'success', 2500);
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        } else {
                            _btn.removeAttr('disabled').css({
                                opacity: 1
                            });
                            $('#token').val(json.response);
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
        })
    })
}

