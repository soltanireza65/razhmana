let temp_lang = JSON.parse(var_lang);

let rolesAdmin = $('#countriesC').select2().select2('val');

$('.setSubmitBtn').click(function () {

    let title = $(".titleCategory").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();
    let title_save = "[" + title + "]";
    let btn = $(this).attr('id');
    let id = $('.setSubmitBtn').data('id');
    let country = $('#countriesC').select2().select2('val');
    let priority = $('#priority').val().trim();
    let a_status_ground = $('#a_status_ground').is(':checked');
    let a_status_ship = $('#a_status_ship').is(':checked');
    let a_status_air = $('#a_status_air').is(':checked');
    let a_status_railroad = $('#a_status_railroad').is(':checked');
    let a_status_inventory = $('#a_status_inventory').is(':checked');
    let a_status_poster = $('#a_status_poster').is(':checked');
    let token = $('#token').val().trim();
    let xInternationalName = $('#xInternationalName').val().trim();
    let long = $('#xlong').val().trim();
    let lat = $('#xlat').val().trim();
    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 2) {
            flag = false;
        }
    });

    const BTN = Ladda.create(document.querySelector('#' + btn));
    if (flag && id != 0 && country > 0) {
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'city-edit',
            id: id,
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
            lat: lat,
            long: long,
            token: token,
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

var len = $('.titleCategory');
len.each(function (index, element) {
    let lennn = $(element).val().trim().length;
    var idid = $(element).attr('data-id');
    if (lennn > 1) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + lennn + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + lennn + '</b>');
    }
});
