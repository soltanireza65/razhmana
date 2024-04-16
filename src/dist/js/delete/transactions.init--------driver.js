const table = $('#transactions-table');

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

$('#tx-search').on('input', function () {
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
        action: 'load-more-transactions',
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
                $('#transactions-list').append(response);
            } else {
                _btn.fadeOut(300);
            }
        }
    });
});