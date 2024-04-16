let temp_lang = JSON.parse(var_lang);

let catsryP = $('#categoryP').select2().select2('val');

$('#btnDelete').click(function () {

    var btnDelete = Ladda.create(document.querySelector('#btnDelete'));

    let categoryID = $(this).data('tj-category-id');
    let categoryReplace = $('#categoryP').select2().select2('val');
    let token = $('#token').val().trim();

    if (categoryID > 0 && categoryReplace != 0) {
        $(".btn").attr('disabled', 'disabled');
        btnDelete.start();

        let data = {
            action: 'category-post-delete',
            categoryID: categoryID,
            categoryReplace: categoryReplace,
            token: token,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                btnDelete.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "info");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/category/post");
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
    } else if (categoryReplace == 0) {
        toastNotic(temp_lang.error, temp_lang.select_replace_category_enter);
    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }
});