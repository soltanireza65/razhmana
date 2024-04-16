$('#install').click(function(){
    installApp();
});
$('#android').click(function(){
    window.location.href ='/android'
});
$('#ios').click(function(){
    window.location.href ='/ios'
});
function installApp() {
    const link = document.createElement('link');
    link.href = '/dist/json/manifest.webmanifest';
    link.rel = 'manifest';
    document.getElementsByTagName('head')[0].appendChild(link);
}
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/dist/js/site/service-worker.js')
}
$(document).ready(function ($) {
    // jQuery code is in here
    if (getCookie('install')=='true'){

    }else{
        $('#exampleModal').modal('show');
        setCookie('install' , 'true' , 30);
    }

});


$('#driver-login').click(function () {
    const user_type = getCookie('user-type');
    console.log(user_type)

    if (user_type ==='driver'){
        window.location.href ='/driver'
    }else if (user_type ==='guest'){
        changeUserTypeDriver('driver');
    }else if (user_type==='businessman'){
        sendNotice(lang_vars.user_type_error_title ,lang_vars.user_type_error_desc );
    }else{
        window.location.href ='/login'
    }

});
$('#businessman-login').click(function () {
    const user_type = getCookie('user-type');


    if (user_type ==='businessman'){
        window.location.href ='/businessman'
    }else if (user_type ==='guest'){
        changeUserTypeBusinessMan('businessman');
    }else if (user_type==='driver'){
        sendNotice(lang_vars.user_type_error_title ,lang_vars.user_type_error_desc );
    }else{
        window.location.href ='/login'
    }

});
$('#go-to-dashboard').click(function () {
    const user_type = getCookie('user-type');
    if (user_type){
        if (user_type ==='businessman'){
            window.location.href ='/businessman'
        }else if (user_type ==='driver'){
            window.location.href ='/driver'
        }
    }
})
function changeUserTypeDriver(type) {
    const params = {
        action: 'change-user-type',
        type:type,
        token:$('#token_change_user_type').val()
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
                    setCookie('user-type' , type , 365)
                    sendNotice(lang_vars.alert_success, lang_vars.alert_user_chage, 'success', 3500);
                    setTimeout(() => {
                        // window.location.replace(`/driver`);
                    }, 3000);
                } else {
                    $('#token_change_user_type').val(json.response);
                    // _btn.removeAttr('disabled').css({
                    //     opacity: 1
                    // });
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            } catch (e) {
                // _btn.removeAttr('disabled').css({
                //     opacity: 1/
                // });
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
            }
        }
    })
}
function changeUserTypeBusinessMan(type) {
    const params = {
        action: 'change-user-type',
        type:'businessman',
        token:$('#token_change_user_type').val()
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    setCookie('user-type' , type , 365)
                    sendNotice(lang_vars.alert_success, lang_vars.alert_user_chage, 'success', 2500);
                    setTimeout(() => {
                        // window.location.replace(`/businessman`);
                    }, 3000);
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