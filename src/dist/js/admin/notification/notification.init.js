let temp_lang = JSON.parse(var_lang);

$('#noticTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_noticTitle').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_noticTitle').html('<b class="text-danger">' + len + '</b>');
    }
});


$('#noticSenderName').keyup(function () {
    var len1 = $(this).val().trim().length;
    if (len1 > 2) {
        $('#length_noticSenderName').html('<b class="text-success">' + len1 + '</b>');
    } else {
        $('#length_noticSenderName').html('<b class="text-danger">' + len1 + '</b>');
    }
});

if ($('#noticSenderText').length > 0) {
    $('#noticSenderText').keyup(function () {
        var len = $('#noticSenderText .ql-editor').text().length;
        if (len > 2) {
            $('#length_noticSenderText').html('<b class="text-success">' + len + '</b>');
        } else {
            $('#length_noticSenderText').html('<b class="text-danger">' + len + '</b>');
        }
    });

}


if ($('#noticSenderText').length > 0) {
    const quill = new Quill("#noticSenderText", {
        theme: "snow",
        modules: {
            toolbar: [
                [{header: [!1, 3, 4, 5, 6]}],
                ['underline', 'italic', 'bold'],
                [{color: []}, {background: []}],
                [{list: 'bullet'}, {list: 'ordered'}],
                ['video', 'image', 'link']
            ]
        }
    });
}


$('#sendNotic').on('click', function () {

    let title = $('#noticTitle').val().trim();
    let sender = $('#noticSenderName').val().trim();
    let text = $('#noticSenderText  .ql-editor').html().trim();
    let id = $('#sendNotic').data('user-id');
    let token = $('#tokenShow').val().trim();

    let BTNN = Ladda.create(document.querySelector('#sendNotic'));

    if (title.length > 2 && sender.length > 2 && text != "<p><br></p>" && text.length > 9) {

        BTNN.start();
        $(".btn").attr('disabled', 'disabled');
        let data = {
            action: 'notification-add',
            id: id,
            title: title,
            sender: sender,
            token: token,
            text: text
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTNN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    $('#noticTitle').val("");
                    $('#noticSenderName').val("");
                    $('#noticSenderText').val("");
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                } else if (data == "empty") {
                    toastNotic(temp_lang.error, temp_lang.empty_input);
                } else if (data == "token_error") {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });

    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});


$(document).on('click', '.showNotification', function () {
    let id = $(this).data('notification-id');
    let token = $('#tokenShow').val().trim();
    let _this = $(this);

    if (id > 0) {

        _this.find('i').addClass(' mdi-spin');

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
                console.log(data)
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
                } else if (result.status == -2) {
                    location.reload();
                } else {
                    $('#modalDiv').modal('hide');
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }

            }
        });
    }
});