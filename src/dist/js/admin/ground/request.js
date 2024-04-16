$(document).ready(function () {
    if ($('#orders-table').is('[data-tj-col]')) {
        let vars = $('#orders-table').data('tj-col');
        let address = $('#orders-table').data('tj-address');

        vars = vars.split(",");
        let output = [];

        for (let i = 0; i < vars.length; i++) {
            output.push({data: vars[i]})
        }
        // $('#orders-table tfoot th').each(function () {
        //     var title = $(this).text();
        //     $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        // });
        let table = $('#orders-table').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '../../../api/datatable/' + address,
            },
            'columns': output,
            oLanguage: {
                sUrl: "/dist/libs/datatables.net-i18/fa.json",
            },
            responsive: true,

        });


        // $('#first-name').on( 'keyup', function () {
        //     table
        //         .columns( 2 )
        //         .search( this.value )
        //         .draw();
        // } );


        // $('#first-name').on( 'keyup', function () {
        //     table
        //         .columns( 2 )
        //         .search( this.value )
        //         .draw();
        // } );
        //
        // $('#last-name').on( 'keyup', function () {
        //     table
        //         .columns( 3 )
        //         .search( this.value )
        //         .draw();
        // } );

        // $("#orders-table input").each(function(){
        //     let  id=$(this).prop('id');
        //     let col=$(this).data('tj-col');
        //     $('#'+id).on( 'keyup change clear', function () {
        //         table.columns( col ).search( this.value ).draw();
        //         console.log(table.columns(col).search(this.value).draw())
        //     } );
        // });

        $("#orders-table input[type='text']").each(function () {
            let id = $(this).prop('id');
            let col = $(this).data('tj-col');
            $('#' + id).on('keyup change clear', function () {
                table.columns(col).search(this.value).draw();
            });
        });
        $('[type="radio"]').on('click', function () {
            let myarray = [];
            $('[type="radio"]').each(function (i) {
                if ($(this).is(':checked') === true) {
                    myarray.push($(this).prop('id'))
                }
            });
            table.columns(6).search(myarray.toString()).draw();
        });

        // $(document).on('load',function () {
        let myarray = [];
        $('[type="radio"]').each(function (i) {
            if ($(this).is(':checked') === true) {
                myarray.push($(this).prop('id'))
            }
        });
        table.columns(6).search(myarray.toString()).draw();
        // });

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

