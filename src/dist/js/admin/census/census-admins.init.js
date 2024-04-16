$('#dateStart').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#startDefault',
    altFormat: 'X',
    // minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    }
});

$('#dateEnd').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#endDefault',
    altFormat: 'X',
    // minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    },
});


$('#btnSubmit').click(function () {
    var BTNN = Ladda.create(document.querySelector('#btnSubmit'));
    let start = $('#startDefault').val(),
        end = $('#endDefault').val();
    BTNN.start();
    window.setTimeout(
        function () {
            window.location.replace("/admin/census/admins/" + start + "/" + end);
        },
        500
    );

});