let temp_lang = JSON.parse(var_lang);

$('.setSubmitBtn').click(function () {

    let x = new Map();
    $('.adppppppp').map(function (key, element) {
        let i = {};
        $(element).find('input').map(function (key, value) {
            switch ($(this).data('permission')) {
                case 'insert':
                    i.insert = $(this).is(':checked') ? 'yes' : 'no';
                    break;

                case 'edit':
                    i.edit = $(this).is(':checked') ? 'yes' : 'no';
                    break;

                case 'delete':
                    i.delete = $(this).is(':checked') ? 'yes' : 'no';
                    break;

                default:
                    i.show = $(this).is(':checked') ? 'yes' : 'no';
            }
            x.set($(this).attr('data-slug-id'), i);
        });
    });
    // console.log(x);
    let permission = Object.fromEntries(x);

    let btn = $(this).attr('id');

    // let title = $('.nameRole').val().trim();
    var title = $(".nameRole").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();

    let title_save = "[" + title + "]";

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });


    if (flag == true) {

        $(".setSubmitBtn").attr('disabled', 'disabled');
        var BTNN = Ladda.create(document.querySelector('#' + btn));
        BTNN.start();

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }


        let data = {
            action: 'admin-role-add',
            status: status,
            title: title_save,
            token: $('#token').val().trim(),
            permission: JSON.stringify(permission),
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTNN.remove();
                $(".setSubmitBtn").removeAttr('disabled');

                if (data == 'successful') {
                    $(".setSubmitBtn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/admin");
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


$('.nameRole').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-slug');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});