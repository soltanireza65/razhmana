let temp_lang = JSON.parse(var_lang);

$(document).ready(function () {
    $(document).on('click', '.showNotification', function () {
        let id = $(this).attr('data-notification-id');
        let token = $('#tokenShow').val().trim();
        let _this = $(this);
        _this.find('i').addClass(' mdi-spin');

        if (id.length > 0) {

            let data = {
                action: 'notification-show',
                id: id,
                token: token,
            };
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    _this.find('i').removeClass('mdi-spin');
                    let result = JSON.parse(data);
                    if (result.status == 200) {
                        $("#modalTitleG").html('');
                        $("#modalDescG").html('');
                        $("#modalSenderG").html('');
                        $("#modalDateG").html('');


                        $("#modalTitleG").html(JSON.parse(data).title);
                        $("#modalDescG").html(JSON.parse(data).message);
                        $("#modalSenderG").html(JSON.parse(data).sender);
                        $("#modalDateG").html(JSON.parse(data).time);
                        $('#modalGroupDiv').modal('show');
                    }else if (result.status == -2) {
                        location.reload();
                    } else {
                        $('#modalDiv').modal('hide');
                        toastNotic(temp_lang.error, temp_lang.error_mag);
                    }

                }
            });
        }
    });
});
