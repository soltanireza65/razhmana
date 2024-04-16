let sUrl = "/dist/libs/datatables.net-i18/fa.json";
if (getCookie('language')) {
    let language = getCookie('language');
    console.log(language.substr(0, 2))
    sUrl = "/dist/libs/datatables.net-i18/" + language.substr(0, 2) + ".json";
}

$('#notification-table, #group-notifications-table').DataTable({
    oLanguage: {
        sUrl: sUrl
    },
    searching: false,
    info: false,
    lengthChange: false,
    paging: false,
    ordering: false,
});


$('div[data-notification-id]').click(function () {
    const params = {
        action: 'change-notification-status',
        notification_id: $(this).data('notification-id'),
     };
    let notification_link = $(this).data('notification-link')
    // console.log(notification_link)
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    window.location.href=notification_link
                } else {

                }
            } catch (e) {

            }
        }
    })
})