
$('.showNotification').click(function () {
    let id = $(this).attr('data-notification-id');
    let token = $('#token').val().trim();

    let _this = $(this);
    _this.find('i').addClass(' mdi-spin');

    if (id.length > 0 && token) {

        let data = {
            action: 'group-notification-show',
            id: id,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                _this.find('i').removeClass('mdi-spin');

                if (data.length > 0) {
                    $("#modalTitleG").html('');
                    $("#modalDescG").html('');
                    $("#modalSenderG").html('');
                    $("#modalDateG").html('');

                    $("#modalTitleG").html(JSON.parse(data).title);
                    $("#modalDescG").html(JSON.parse(data).message);
                    $("#modalSenderG").html(JSON.parse(data).sender);
                    $("#modalDateG").html(JSON.parse(data).time);
                    $("#token").val(JSON.parse(data).token);
                    $('#modalGroupDiv').modal('show');
                } else if (data == "token_error") {
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        1000
                    );
                }else {
                    $('#modalDiv').modal('hide');
                }

            }
        });
    }
});