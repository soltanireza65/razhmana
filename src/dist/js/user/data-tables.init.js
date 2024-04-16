const table = $('#requests-table');
let sUrl = "/dist/libs/datatables.net-i18/fa.json" ;
if (getCookie('language')){
    let language  =  getCookie('language');
    console.log(language.substr(0, 2) )
      sUrl = "/dist/libs/datatables.net-i18/" + language.substr(0, 2) + ".json" ;

}

table.DataTable({
    oLanguage: {
        sUrl: sUrl,
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

