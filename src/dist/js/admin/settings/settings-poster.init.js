let temp_lang = JSON.parse(var_lang);


/**
 start settings_poster_all
 */

// Dropzone.autoDiscover = false;
let selectedFiles_logoLightUser = [];
Dropzone.options.logoLightUser = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/svg+xml',
    uploadMultiple: false,
    maxFiles: 1,
    autoProcessQueue: true,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,

    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {

        });
    },

    init: function () {
        this.on('success', async function (file) {
            if (file.accepted) {
                const content = await fileRead(file);

                const temp = new File([content], file.name, {
                    type: file.type,
                    webkitRelativePath: file.webkitRelativePath,
                    lastModifiedDate: file.lastModifiedDate,
                    lastModified: file.lastModified,
                });
                selectedFiles_logoLightUser.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);
            //
            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_logoLightUser.indexOf(file);
            if (index > -1) {
                selectedFiles_logoLightUser.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};

function fileRead(file) {
    return new Promise((resolve, reject) => {
        let reader = new FileReader();
        reader.onload = () => {
            resolve(reader.result);
        };

        reader.onerror = () => {
            reject(reader.result);
        };

        reader.readAsArrayBuffer(file)
        // reader['readAsText'](file);
    });
}

// function findItemIndex(file) {
//     let deletedIndex = -1;
//     selectedFiles.map(function (element, index) {
//         if (element.name == file.name) {
//             deletedIndex = index;
//             return index;
//         }
//     });
//     return deletedIndex;
// }
$('#submit_poster_all').click(function () {
    let token = $('#token').val().trim();
    var BTN = Ladda.create(document.querySelector('#submit_poster_all'));
    BTN.start();

    let params = new FormData();
    params.append('action', 'settings-poster-all');
    params.append('token', token);
    selectedFiles_logoLightUser.forEach(function (element, index) {
        params.append("logoLightUser", element);
    });
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: params,
        contentType: false,
        processData: false,
        success: function (data) {
            BTN.remove();
            if (data == 'successful') {
                $("#submit_theme_site").attr('disabled', 'disabled');

                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                window.setTimeout(
                    function () {
                        location.reload();
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

});
/**
 end settings_poster_all
 */


/**
 start settings price time
 */
$("#submit_price_time").on('click', function () {

    const BTNN = Ladda.create(document.querySelector('#submit_price_time'));
    let poster_expire_time = $('#poster_expire_time').val().trim();
    let poster_immediate_time = $('#poster_immediate_time').val().trim();

    let poster_immediate_price_toman = $('#poster_immediate_price_toman').val().trim();
    let poster_immediate_price_dollar = $('#poster_immediate_price_dollar').val().trim();
    let poster_immediate_price_euro = $('#poster_immediate_price_euro').val().trim();

    let poster_ladder_price_toman = $('#poster_ladder_price_toman').val().trim();
    let poster_ladder_price_dollar = $('#poster_ladder_price_dollar').val().trim();
    let poster_ladder_price_euro = $('#poster_ladder_price_euro').val().trim();

    let poster_expert_time = $('#poster_expert_time').val().trim();
    let poster_expert_price_toman = $('#poster_expert_price_toman').val().trim();
    let poster_expert_price_dollar = $('#poster_expert_price_dollar').val().trim();
    let poster_expert_price_euro = $('#poster_expert_price_euro').val().trim();

    let token = $('#token').val().trim();

    $("#submit_price_time").attr('disabled', true);
    BTNN.start();
    let data = {
        action: 'settings-price-time',
        poster_expire_time: poster_expire_time,
        poster_immediate_time: poster_immediate_time,
        poster_immediate_price_toman: poster_immediate_price_toman,
        poster_immediate_price_dollar: poster_immediate_price_dollar,
        poster_immediate_price_euro: poster_immediate_price_euro,
        poster_ladder_price_toman: poster_ladder_price_toman,
        poster_ladder_price_dollar: poster_ladder_price_dollar,
        poster_ladder_price_euro: poster_ladder_price_euro,
        poster_expert_time: poster_expert_time,
        poster_expert_price_toman: poster_expert_price_toman,
        poster_expert_price_dollar: poster_expert_price_dollar,
        poster_expert_price_euro: poster_expert_price_euro,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();
            $("#submit_price_time").attr('disabled', false);
            if (data == 'successful') {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else {
                toastNotic(temp_lang.warning, temp_lang.error_mag, 'warning');
            }
        }
    });
});

/**
 end settings price time
 */
