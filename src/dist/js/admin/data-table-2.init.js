$(document).ready(function () {
    if ($('#orders-table').is('[data-tj-col]')) {
        let vars = $('#orders-table').data('tj-col');
        let address = $('#orders-table').data('tj-address');

        if ($('#orders-table').is('[data-tj-where]')) {
            address = address + "/" + $('#orders-table').data('tj-where');
        }
        vars = vars.split(",");
        let output = [];

        for (let i = 0; i < vars.length; i++) {
            output.push({data: vars[i]})
        }
        let table = $('#orders-table').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '../../../../api/datatable/' + address,
            },
            'columns': output,
            oLanguage: {
                sUrl: "/dist/libs/datatables.net-i18/fa.json",
            },
            responsive: true,
        });

        if ($('#orders-table input').is('[data-tj-col]')) {
            $("#orders-table input").each(function () {
                let id = $(this).prop('id');
                let col = $(this).data('tj-col');
                $('#' + id).on('keyup change clear', function () {
                    table.columns(col).search(this.value).draw();
                });
            });
        }
        $('[data-bs-toggle="tooltip"]').tooltip()

    } else {
        $("#orders-table").DataTable({
            oLanguage: {
                sUrl: "/dist/libs/datatables.net-i18/fa.json",
            },
            responsive: true,
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                $(".dataTables_paginate > .pagination .page-link").addClass("shadow-none");
            }
        }), $(".dataTables_length select").addClass("form-select form-select-sm"), $(".dataTables_length select").removeClass("custom-select custom-select-sm"), $(".dataTables_length label").addClass("form-label")
    }
});