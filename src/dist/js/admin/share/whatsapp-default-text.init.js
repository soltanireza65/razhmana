let temp_lang = JSON.parse(var_lang);

$('.repeater').repeater({
    initEmpty: false,
    show: function () {
        $(this).slideDown();
    },
    hide: function (deleteElement) {
        // if (confirm('Are you sure you want to delete this element?')) {
        $(this).slideUp(deleteElement);
        // }
    },
    isFirstItemUndeletable: true
});

$("#submit_whatsapp_default").on('click', function () {
    var BTNN = Ladda.create(document.querySelector('#submit_whatsapp_default'));
    BTNN.start();
    let token = $('#token').val().trim();
    let repp = $(".repeater").repeaterVal();
    let params = new FormData();
    params.append('action', 'whatsapp-default-text');
    params.append('token', token);

    $(repp['group-a']).each(function (index, element) {
        params.append(index + "", JSON.stringify({
            title: element.whatsAppDefaultTitle,
            desc: element.whatsAppDefaultDesc
        }));
    });

    $.ajax({
        url: '/api/adminAjax',
        type: 'POST',
        data: params,
        contentType: false,
        processData: false,
        success: function (response) {
            BTNN.remove();
            if (response = "successful") {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else if (response == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (response == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.warning, temp_lang.error_mag);
            }
        }
    });


});