const processingModal = new bootstrap.Modal($('#modal-processing'));
const resultModal = new bootstrap.Modal($('#modal-submitted'));

$('#user-avatar').on('change', function () {
    processingModal.show();

    let params = new FormData();
    params.append('action', 'change-avatar');
    params.append('avatar', $(this).prop('files')[0]);
    params.append('token', $('#token-avatar').val());

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: params,
        contentType: false,
        processData: false,
        success: function (response) {
            setTimeout(() => {
                processingModal.hide();
                resultModal.show();
            }, 500);

            try {
                const json = JSON.parse(response);
                if (json.status == 200) {
                    const html = `
                        <i class="fe-check-circle d-block text-success mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13">${lang_vars['b_success_processing'].replaceAll('#ACTION#', lang_vars['upload_avatar'])}</h6>
                        `;
                    $('#submitting-alert').html(html);

                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
                } else {
                    const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['upload_avatar'])}</h6>
                        `;
                    $('#token-avatar').val(json.response);
                    $('#submitting-alert').html(html);
                }
            } catch (e) {
                const html = `
                        <i class="fe-x-circle d-block text-danger mb-3" style="font-size: 72px;"></i>
                        <h6 class="mj-font-13 mb-0">${lang_vars['b_error_processing'].replaceAll('#ACTION#', lang_vars['upload_avatar'])}</h6>
                        `;
                $('#submitting-alert').html(html);
            }
        }
    });
});
$('#logout').on('click' , function () {
    let params = {
        action : 'logout-user'
    }
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            response = JSON.parse(response);
            if (response.status == 200){
                window.location.href = '/login';
            }

        }
    });
});