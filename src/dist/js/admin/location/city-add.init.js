let temp_lang = JSON.parse(var_lang);

let rolesAdmin = $('#countriesC').select2().select2('val');


$('.setSubmitBtn').click(function () {
    let id = $(this).attr('id');
    let token = $('#token').val().trim();
    let priority = $('#priority').val().trim();
    let country = $('#countriesC').select2().select2('val');
    let a_status_ground = $('#a_status_ground').is(':checked');
    let a_status_ship = $('#a_status_ship').is(':checked');
    let a_status_air = $('#a_status_air').is(':checked');
    let a_status_railroad = $('#a_status_railroad').is(':checked');
    let a_status_inventory = $('#a_status_inventory').is(':checked');
    let a_status_poster = $('#a_status_poster').is(':checked');
    let xInternationalName = $('#xInternationalName').val().trim();

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
        if (temp.toString().length < 2) {
            flag = false;
        }
    });


    if (flag && country > 0) {

        let BTN = Ladda.create(document.querySelector('#' + id));

        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let data = {
            action: 'city-add',
            title: title_save,
            country: country,
            status_ground: a_status_ground,
            status_ship: a_status_ship,
            status_air: a_status_air,
            status_railroad: a_status_railroad,
            status_inventory: a_status_inventory,
            status_poster: a_status_poster,
            priority: priority,
            InternationalName: xInternationalName,
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
                            // window.location.replace("/admin/city/edit/" + myArray[1]);
                            window.location.replace("/admin/city/add");

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

$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});
