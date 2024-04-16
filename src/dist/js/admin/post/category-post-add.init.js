let temp_lang = JSON.parse(var_lang);

let language = $('#language').select2().select2('val');

$('.setSubmitBtn').click(function () {

    let title = $('#titleCategory').val().trim();
    let btn = $(this).attr('id');
    let token = $('#token').val().trim();
    let metaTitle = $('#xMetaTitle').val().trim();
    let metaDesc = $('#xMetaDesc').val().trim();
    let schema = $('#xSchema').val().trim();
    let priority = $('#xPriority').val().trim();
    let language = $('#language').select2().select2('val');

    var BTNN = Ladda.create(document.querySelector('#' + btn));

    if (title.length > 2 && language.length > 3) {

        BTNN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'category-post-add',
            status: status,
            title: title,
            lang: language,
            metaTitle: metaTitle,
            metaDesc: metaDesc,
            schema: schema,
            priority: priority,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTNN.remove();
                $(".btn").removeAttr('disabled');

                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/post/edit/" + myArray[1]);
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

$('#titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_titleCategory').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_titleCategory').html('<b class="text-danger">' + len + '</b>');
    }
});


$('#xMetaTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 70) {
        $('#length_xMetaTitle').html('<b class="text-info">' + len + '</b>');
    } else if (len == 70) {
        $('#length_xMetaTitle').html('<b class="text-success">' + len + '</b>');
    } else if (len > 70) {
        $('#length_xMetaTitle').html('<b class="text-warning">' + len + '</b>');
    }
});

$('#xMetaDesc').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 150) {
        $('#length_xMetaDesc').html('<b class="text-info">' + len + '</b>');
    } else if (len == 150) {
        $('#length_xMetaDesc').html('<b class="text-success">' + len + '</b>');
    } else if (len > 150) {
        $('#length_xMetaDesc').html('<b class="text-warning">' + len + '</b>');
    }
});

$('#xSchema').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xSchema').html('<b class="text-info">' + len + '</b>');
});