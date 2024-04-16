const table = $('#requests-table');

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

$('#request-search').on('input', function () {
    table.DataTable().search($(this).val()).draw()
});


$('a[data-detail]').on('click', function () {
    let template = $('#template-detail').html();
    template = template.replaceAll('#REQUEST#', $(this).data('request'));
    template = template.replaceAll('#CARGO#', $(this).data('cargo'));
    template = template.replaceAll('#DRIVER#', $(this).data('driver'));

    $('body').append(template);
    const _modal = new bootstrap.Modal($('#modal-detail'));
    _modal.show();

    $('button[data-btn-request]').on('click', function () {
        const cargoId = $(this).data('cargo');

        const params = {
            action: 'change-request-status',
            request: $(this).data('request'),
            cargo: $(this).data('cargo'),
            driver: $(this).data('driver'),
            status: $(this).data('status'),
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
                        sendNotice(lang_vars.alert_success, lang_vars.b_request_changed, 'success', 2500);
                        setTimeout(() => {
                            window.location.href =`/businessman/cargo-detail/${cargoId}`;
                        }, 3000)
                    } else if (json.status ==-30 ){
                        sendNotice(lang_vars.alert_error, lang_vars.alert_car_count_accepted, 'error', 3500);
                        $('#token-expenses').val(json.response);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }else {
                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        $('#token-expenses').val(json.response);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            }
        })
    });
});