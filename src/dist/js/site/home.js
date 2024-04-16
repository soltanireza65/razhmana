// $(window).on('load', function() {
//   $(".mj-preloader").fadeOut("slow").remove(2000)
// });
var swiper = new Swiper(".mj-brands-slider", {
    slidesPerView: 5,
    centeredSlides: true,
    spaceBetween: 10,
    grabCursor: true,
    autoplay: true,
    loop: true,

});

langInputs = $('.mj-lang-items input[type=radio]')
langInputsSubmit = $('.mj-lang-submit')
langInputsSubmit.hide()
langInputs.on('change', function () {
    langInputsSubmit.show(500)
})
$(window).on('load', function () {

    $(".mj-preloader").fadeOut(300)

    if (getCookie('language-modal')) {

    } else {
        $('#language-modal').modal('show');
    }

});
$('.mj-d-cargo-item-link2').click(function () {
    $('.mj-cargo-owner-modal-info').modal("show")
})


$('#driver-login').click(function () {
    const user_type = getCookie('user-type');
    if (user_type === 'driver') {
        window.location.href = '/driver'
    } else if (user_type === 'guest') {
        changeUserTypeDriver('driver');
    } else if (user_type === 'businessman') {
        sendNotice(lang_vars.user_type_error_title, lang_vars.user_type_error_desc);
    } else {
        window.location.href = '/login'
    }

});
$('#businessman-login').click(function () {
    const user_type = getCookie('user-type');
    if (user_type === 'businessman') {
        window.location.href = '/businessman'
    } else if (user_type === 'guest') {
        changeUserTypeBusinessMan('businessman');
    } else if (user_type === 'driver') {
        sendNotice(lang_vars.user_type_error_title, lang_vars.user_type_error_desc);
    } else {
        window.location.href = '/login'
    }

});
$('#go-to-dashboard').click(function () {
    const user_type = getCookie('user-type');
    if (user_type) {
        if (user_type === 'businessman') {
            window.location.href = '/businessman'
        } else if (user_type === 'driver') {
            window.location.href = '/driver'
        }
    }
})

function changeUserTypeDriver(type) {
    const params = {
        action: 'change-user-type',
        type: type,
        token: $('#token_change_user_type').val()
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            console.log(response);
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    setCookie('user-type', type)
                    sendNotice(lang_vars.alert_success, lang_vars.alert_user_chage, 'success', 3500);
                    setTimeout(() => {
                        window.location.href = `/driver`;
                    }, 1000);
                } else {
                    $('#token_change_user_type').val(json.response);

                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {

                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
}

function changeUserTypeBusinessMan(type) {
    console.log(type)
    const params = {
        action: 'change-user-type',
        type: 'businessman',
        token: $('#token_change_user_type').val()
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    setCookie('user-type', type)
                    sendNotice(lang_vars.alert_success, lang_vars.alert_user_chage, 'success', 2500);
                    setTimeout(() => {
                        window.location.href = `/businessman`;
                    }, 1000);
                } else {
                    $('#token_change_user_type').val(json.response);

                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {

                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
}

$('#transportation').click(function () {
    let userType = getCookie('user-type');
    if (userType) {
        if (userType == 'driver') {
            window.location.href = '/driver'
        } else if (userType == 'businessman') {
            window.location.href = '/businessman'
        } else {

            $('#select-user-type-modal').modal('show')
        }
    } else {
        setCookie('login-back-url', 'unknow')
        window.location.href = '/login'
    }
})


$(document).on('click', '.iframe-item', async function () {
    let ad_id = $(this).data('id');
    $('#poster-detail').attr('src', '/poster/detail/' + ad_id)
    await load_iframe();
});

function load_iframe() {
    document.getElementById('poster-detail').onload = function () {
        $('#exampleModaliframe').modal('show')
    };
}

$('#exampleModaliframe').on('shown.bs.modal', function (e) {
    window.location.hash = "detail";
});

$('#exampleModaliframe').on('hidden.bs.modal', '.mj-p-poster-item-content', function () {
    location.hash = ''
})

$(window).on('hashchange', function (event) {
    if (window.location.hash != "#detail") {
        $('#exampleModaliframe').modal('hide');
    }
});
