let temp_lang = JSON.parse(var_lang);

let xParent = $('#xParent').select2().select2('val');

$('.setSubmitBtn').click(function () {

    let title = $(".titleCategory").map(function () {

        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();
    let title_save = "[" + title + "]";
    let btn = $(this).prop('id');
    let id = $(this).data('tj-id');

    let token = $('#token').val().trim();
    let priority = $('#priority').val().trim();
    let xParent = $('#xParent').select2().select2('val');

    let BTN = Ladda.create(document.querySelector('#' + btn));

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 1) {
            flag = false;
        }
    });


    if (flag && parseInt(id) > 0 && parseInt(xParent) > 0) {

        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (btn == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'model-edit',
            status: status,
            id: id,
            title: title_save,
            token: token,
            parent: xParent,
            priority: priority,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTN.remove();
                $(".setSubmitBtn").removeAttr('disabled');

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
                } else if (data == "error_upload_image") {
                    toastNotic(temp_lang.error, temp_lang.error_upload_image);
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });

    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});


$('.titleCategory').keyup(function () {
    let len = $(this).val().trim().length;
    let idid = $(this).attr('data-id');
    if (len > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});

let len = $('.titleCategory');
len.each(function (index, element) {
    let lennn = $(element).val().trim().length;
    let idid = $(element).attr('data-id');
    if (lennn > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + lennn + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + lennn + '</b>');
    }
});