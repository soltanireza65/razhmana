$(document).ready(function () {


    $('#reject-request').click(function () {
        let request_id = $(this).data('request-id')
        let params = {
            action: 'change-exchange-request-status',
            request_id: request_id,
            request_status: 'rejected',
            token: $('#token').val()
        }

        sendAjaxRequest('POST', '/api/adminAjax', params, false)
            .then(response => {
                if (response.status == 200) {
                    toastNotic(lang_vars.alert_success, lang_vars.alert_success_operations, 'success');
                } else {
                    toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
            });
    })

    $('#accept-request').click(function () {
        let request_id = $(this).data('request-id')
        let params = {
            action: 'change-exchange-request-status',
            request_id: request_id,
            request_status: 'accepted',
            token: $('#token').val()
        }

        sendAjaxRequest('POST', '/api/adminAjax', params, false)
            .then(response => {
                if (response.status == 200) {
                    toastNotic(lang_vars.alert_success, lang_vars.alert_success_operations, 'success');
                } else {
                    toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
            });

    })

    $('#submit-desciption').click(function () {
        let admin_description = $('#admin-description').val();
        let request_id = $(this).data('request-id')

        let params = {
            action: 'submit-exchange-request-description',
            request_id: request_id,
            admin_description: admin_description,
            token: $('#token').val()
        }

        sendAjaxRequest('POST', '/api/adminAjax', params, false)
            .then(response => {
                if (response.status == 200) {
                    toastNotic(lang_vars.alert_success, lang_vars.alert_success_operations, 'success');
                } else {
                    toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
                }
               setTimeout(function () {
                   window.location.reload()
               } , 3000)
            })
            .catch(error => {
                console.error(error);
                toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
                setTimeout(function () {
                    window.location.reload()
                } , 3000)
            });
    })

});