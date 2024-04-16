let temp_lang = JSON.parse(var_lang);

let language = $('#language').select2().select2('val');

$('.setSubmitBtn').click(function () {

    let title = $('#titleCategory').val().trim();
    let btn = $(this).attr('id');
    let cat_id = $('#titleCategory').attr('data-id');
    let token = $('#token').val().trim();
    let metaTitle = $('#xMetaTitle').val().trim();
    let metaDesc = $('#xMetaDesc').val().trim();
    let schema = $('#xSchema').val().trim();
    let priority = $('#xPriority').val().trim();
    let language = $('#language').select2().select2('val');

    var BTNN = Ladda.create(document.querySelector('#' + btn));

    if (title.length > 2) {

        BTNN.start();
        $(".btn").attr('disabled', 'disabled');
        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'category-post-edit',
            status: status,
            title: title,
            language: language,
            cat_id: cat_id,
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

                if (data == 'successful') {
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
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

var len0 = $('#titleCategory').val().trim().length;
if (len0 > 2) {
    $('#length_titleCategory').html('<b class="text-success">' + len0 + '</b>');
} else {
    $('#length_titleCategory').html('<b class="text-danger">' + len0 + '</b>');
}


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
var len1 = $('#xMetaTitle').val().trim().length;
if (len1 > 0 && len1 < 70) {
    $('#length_xMetaTitle').html('<b class="text-info">' + len1 + '</b>');
} else if (len1 == 70) {
    $('#length_xMetaTitle').html('<b class="text-success">' + len1 + '</b>');
} else if (len1 > 70) {
    $('#length_xMetaTitle').html('<b class="text-warning">' + len1 + '</b>');
}

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
var len2 = $('#xMetaDesc').val().trim().length;
if (len2 > 0 && len2 < 150) {
    $('#length_xMetaDesc').html('<b class="text-info">' + len2 + '</b>');
} else if (len2 == 150) {
    $('#length_xMetaDesc').html('<b class="text-success">' + len2 + '</b>');
} else if (len2 > 150) {
    $('#length_xMetaDesc').html('<b class="text-warning">' + len2 + '</b>');
}

$('#xSchema').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xSchema').html('<b class="text-info">' + len + '</b>');
});
var len3 = $('#xSchema').val().trim().length;
$('#length_xSchema').html('<b class="text-info">' + len3 + '</b>');