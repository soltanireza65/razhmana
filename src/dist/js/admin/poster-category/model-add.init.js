let temp_lang = JSON.parse(var_lang);

let xParent = $('#xParent').select2().select2('val');

$('.setSubmitBtn').click(function () {
    let id = $(this).attr('id');
    let token = $('#token').val().trim();
    let priority = $('#priority').val().trim();

    var title = $(".titleCategory").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();
    let title_save = "[" + title + "]";

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 1) {
            flag = false;
        }
    });

    let xParent = $('#xParent').select2().select2('val');


    if (flag && parseInt(xParent) > 0) {

        var BTN = Ladda.create(document.querySelector('#' + id));


        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (id == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'model-add',
            status: status,
            title: title_save,
            parent: xParent,
            priority: priority,
            token: token,

        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');

                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/model/edit/" + myArray[1]);
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
        if (parseInt(xParent) <= 0) {
            toastNotic(temp_lang.error, temp_lang.a_no_parent_error);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }

    }

});


$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});