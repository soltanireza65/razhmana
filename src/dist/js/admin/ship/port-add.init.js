let temp_lang = JSON.parse(var_lang);

let rolesAdmin = $('#countriesC').select2().select2('val');
let rolesAdminn = $('#cityC').select2().select2('val');

$('.setSubmitBtn').click(function () {
    let id = $(this).attr('id');
    let token = $('#token').val().trim();
    let city = $('#cityC').select2().select2('val');
    let priority = $('#priority').val().trim();
    var title = $(".titleCategory").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();


    let title_save = "[" + title + "]";


    let flag = true;
    $.each(title, function (i, item) {
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });


    if (flag && city > 0) {

        let BTN = Ladda.create(document.querySelector('#' + id));

        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let status = "inactive";
        if (id == 'btnActive') {
            status = "active";
        }

        let data = {
            action: 'port-add',
            status: status,
            title: title_save,
            city: city,
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
                            // window.location.replace("/admin/category/port/edit/" + myArray[1]);
                            window.location.replace("/admin/category/port/add");

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
        if (!city || city <= 0) {
            toastNotic(temp_lang.error, temp_lang.a_select_city);
        } else if (!flag) {
            toastNotic(temp_lang.error, temp_lang.a_enter_title_city);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }

});

$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#countriesC').on('select2:select', function (e) {
    let id = e.params.data.id;

    $('#mj-loader').removeClass('d-none');
    $("#countriesC").attr('disabled', 'disabled');

    $("#cityC").empty();

    let data = {
        action: 'get-city-port-by-country-id',
        id: id,
        token: $('#token').val().trim(),
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            let temp = JSON.parse(data);
            $('#mj-loader').addClass('d-none');
            $("#countriesC").removeAttr('disabled');
            $("#cityC").select2({
                data: temp
            });

        }
    });
});


let data = {
    action: 'get-city-port-by-country-id',
    id: rolesAdmin,
    token: $('#token').val().trim(),
};

$.ajax({
    type: 'POST',
    url: '/api/adminAjax',
    data: JSON.stringify(data),
    success: function (data) {
        let temp = JSON.parse(data);
        $("#cityC").select2({
            data: temp
        });
    }
});
