$('.setSubmitBtn').click(function () {

    let btn = $(this).prop('id');
    let BTNN = Ladda.create(document.querySelector('#' + btn));
    let token = $('#token').val().trim();

    let employID = $(this).data('mj-id');
    let lists = ["completed", "process", "reject"];

    if (employID > 0 && token.length > 0 && jQuery.inArray(btn, lists) != -1)
        BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'change-employ-info-status',
        token: token,
        employID: employID,
        status: btn,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $(".btn").attr("disabled", true);
                toastNotic(lang_vars.successful, lang_vars.successful_update_mag, "success");
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            } else if (data == "empty") {
                toastNotic(lang_vars.error, lang_vars.empty_input);
            } else if (data == "token_error") {
                toastNotic(lang_vars.error, lang_vars.token_error);
            } else {
                toastNotic(lang_vars.error, lang_vars.error_mag);
            }
        }
    });


});

$('.btnDesc').click(function () {
    let btn = $(this).prop('id');
    let desc = $('#employ-desc').val().trim();
    let BTNN = Ladda.create(document.querySelector('#' + btn));
    let token = $('#token').val().trim();

    let employID = $(this).data('mj-id');

    if (employID > 0 && token.length > 0 && desc.length > 0) {
        BTNN.start();
        $(".btn").attr("disabled", true);
        let data = {
            action: 'add-desc-employ-info',
            token: token,
            employID: employID,
            desc: desc,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                console.log(data);
                BTNN.remove();
                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr("disabled", true);
                    toastNotic(lang_vars.successful, lang_vars.successful_update_mag, "success");
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (data == "empty") {
                    toastNotic(lang_vars.error, lang_vars.empty_input);
                } else if (data == "token_error") {
                    toastNotic(lang_vars.error, lang_vars.token_error);
                } else {
                    toastNotic(lang_vars.error, lang_vars.error_mag);
                }
            }
        });

    } else {
        toastNotic(lang_vars.error, lang_vars.empty_input);
    }
});


function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}


$('#btnDeleted').click(function () {

    let btn = $(this).prop('id');
    let BTNN = Ladda.create(document.querySelector('#' + btn));
    let token = $('#token').val().trim();

    let employID = $(this).data('mj-id');
    if (employID > 0 && token.length > 0)
        BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'delete-employ-info',
        token: token,
        employID: employID,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            console.log(data);
            BTNN.remove();
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $(".btn").attr("disabled", true);
                toastNotic(lang_vars.successful, lang_vars.successful_delete_mag, "info");
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            } else if (data == "empty") {
                toastNotic(lang_vars.error, lang_vars.empty_input);
            } else if (data == "token_error") {
                toastNotic(lang_vars.error, lang_vars.token_error);
            } else {
                toastNotic(lang_vars.error, lang_vars.error_mag);
            }
        }
    });
});

let data = {
    action: 'cargo-info-location',
};

$.ajax({
    type: 'POST',
    url: '/api/adminAjax',
    data: JSON.stringify(data),
    success: function (data) {
        getCountry(data, 'selectCountry');
    }
});

function getCountry(myVlaues, divID) {
    let temp = '<option value="0">' + lang_vars.a_select_country_2 + '</option>';
    let tt = jQuery.parseJSON(myVlaues).data;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}

function getCity(myVlaues, divID) {
    let temp = "";
    let tt = jQuery.parseJSON(myVlaues).datacity;
    for (var i in tt) {
        temp = temp + ' <option value="' + tt[i].value + '">' + tt[i].name + '</option>';
    }
    $('#' + divID).html(temp);
}

$('#selectCountry').on('select2:select', function (e) {
    var country = e.params.data.id;
    $('#selectCity').html('');
    let data = {
        action: 'cargo-location-city-and-customs',
        country: country,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            if (jQuery.parseJSON(data).status == 200) {
                $('#selectCity').html('');
                getCity(data, 'selectCity');
            } else {
                $('#selectCity').html('');
            }
        }
    });
});
let country = $('#selectCountry').select2({}).select2('val');
let city = $('#selectCity').select2({}).select2('val');

$('#change-location').click(function () {
    let country = $('#selectCountry').select2({}).select2('val');
    let city = $('#selectCity').select2({}).select2('val');
    if (city !=null){
        let data = {
            action: 'change-hire-location-to-new-state',
            hire_id : hire_id ,
            country: country,
            city: city,
            token : $('#token').val()
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

            }
        });
    }
})





