let temp_lang = JSON.parse(var_lang);

/**
 start settings_theme_admin
 */
var submit_theme = Ladda.create(document.querySelector('#submit_theme'));

let selectFiles_favicon_site = [];
// myAwesomeDropzone  id
Dropzone.options.faviconSite = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,
    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {
            // $('#avatarAdminUrl').val(file.dataURL);
        });
    },
    init: function () {
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_favicon_site.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_favicon_site.indexOf(file);
            if (index > -1) {
                selectFiles_favicon_site.splice(index, 1); // 2nd parameter means remove one item only
            }
            // console.log(selectFiles)
        });
        this.on('totaluploadprogress', function (progress) {
            $('#auth-uploader').css('width', progress + '%').html(progress + '%');
        });

        this.on('success', function (progress) {
            // console.log('completed');
            // window.location.reload();
        });
    }
};


let selectFiles_logo_sm = [];
// myAwesomeDropzone  id
Dropzone.options.logoSm = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,
    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {
            // $('#avatarAdminUrl').val(file.dataURL);
        });
    },
    init: function () {
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_logo_sm.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_logo_sm.indexOf(file);
            if (index > -1) {
                selectFiles_logo_sm.splice(index, 1); // 2nd parameter means remove one item only
            }
            // console.log(selectFiles)
        });
        this.on('totaluploadprogress', function (progress) {
            $('#auth-uploader').css('width', progress + '%').html(progress + '%');
        });

        this.on('success', function (progress) {
            // console.log('completed');
            // window.location.reload();
        });
    }
};


let selectFiles_logo_light = [];
// myAwesomeDropzone  id
Dropzone.options.logoLight = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,
    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {
            // $('#avatarAdminUrl').val(file.dataURL);
        });
    },
    init: function () {
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_logo_light.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_logo_light.indexOf(file);
            if (index > -1) {
                selectFiles_logo_light.splice(index, 1); // 2nd parameter means remove one item only
            }
            // console.log(selectFiles)
        });
        this.on('totaluploadprogress', function (progress) {
            $('#auth-uploader').css('width', progress + '%').html(progress + '%');
        });

        this.on('success', function (progress) {
            // console.log('completed');
            // window.location.reload();
        });
    }
};


let selectFiles_logo_dark = [];
// myAwesomeDropzone  id
Dropzone.options.logoDark = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: temp_lang.delete,
    dictMaxFilesExceeded: temp_lang.dictMaxFilesExceeded,
    dictCancelUpload: temp_lang.cancel_upload,
    accept: function (file, done) {
        done();

        this.on('thumbnail', function (file, dataURL) {
            // $('#avatarAdminUrl').val(file.dataURL);
        });
    },
    init: function () {
        this.on('addedfile', function (file) {
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
            selectFiles_logo_dark.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_logo_dark.indexOf(file);
            if (index > -1) {
                selectFiles_logo_dark.splice(index, 1); // 2nd parameter means remove one item only
            }
            // console.log(selectFiles)
        });
        this.on('totaluploadprogress', function (progress) {
            $('#auth-uploader').css('width', progress + '%').html(progress + '%');
        });

        this.on('success', function (progress) {
            // console.log('completed');
            // window.location.reload();
        });
    }
};


$("#submit_theme").on('click', function () {

    let faviconSite = '';
    if (selectFiles_favicon_site.length > 0) {
        faviconSite = selectFiles_favicon_site[0].dataURL;
    }

    let logoSm = '';
    if (selectFiles_logo_sm.length > 0) {
        logoSm = selectFiles_logo_sm[0].dataURL;
    }

    let logoLight = '';
    if (selectFiles_logo_light.length > 0) {
        logoLight = selectFiles_logo_light[0].dataURL;
    }

    let logoDark = '';
    if (selectFiles_logo_dark.length > 0) {
        logoDark = selectFiles_logo_dark[0].dataURL;
    }


    let token = $('#token').val().trim();

    $("#submit_main").attr('disabled', 'disabled');
    submit_theme.start();
    let data = {
        action: 'settings-theme',
        faviconSite: faviconSite,
        logoSm: logoSm,
        logoLight: logoLight,
        logoDark: logoDark,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            // console.log(data);
            submit_theme.remove();
            if (data = "successful") {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            } else if (data = "empty") {
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
 end settings_theme_admin
 */


/**
 start settings_theme_site
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


let selectedFiles_logoDarkUser = [];
Dropzone.options.logoDarkUser = {
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
                selectedFiles_logoDarkUser.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_logoDarkUser.indexOf(file);
            if (index > -1) {
                selectedFiles_logoDarkUser.splice(index, 1); // 2nd parameter means remove one item only
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


$('#submit_theme_site').click(function () {
    let token = $('#token').val().trim();
    var BTN = Ladda.create(document.querySelector('#submit_theme_site'));
    BTN.start();

    let params = new FormData();
    params.append('action', 'settings-theme-user');
    params.append('token', token);
    selectedFiles_logoLightUser.forEach(function (element, index) {
        params.append("logoLightUser", element);
    });
    selectedFiles_logoDarkUser.forEach(function (element, index) {
        params.append("logoDarkUser", element);

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
 end settings_theme_site
 */


/**
 start settings social
 */

var submit_social = Ladda.create(document.querySelector('#submit_social'));

$("#submit_social").on('click', function () {


    let whatsapp = $('#whatsapp').val().trim();
    let support_call = $('#support_call').val().trim();
    let support_call_2 = $('#support_call_2').val().trim();
    let token = $('#token').val().trim();

    $("#submit_main").attr('disabled', true);
    submit_social.start();
    let data = {
        action: 'settings-social',
        support_call: support_call,
        support_call_2: support_call_2,
        whatsapp: whatsapp,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            submit_social.remove();
            $("#submit_main").attr('disabled', false);
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $('#token').val(myArray[1]);
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else {
                toastNotic(temp_lang.warning, temp_lang.error_mag, 'warning');
            }


        }
    });
});

/**
 end settings social
 */


/**
 * Start manifest logo Setting
 */

let selectedFiles_manifest144 = [];
Dropzone.options.manifest144 = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/png',
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
                selectedFiles_manifest144.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_manifest144.indexOf(file);
            if (index > -1) {
                selectedFiles_manifest144.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};

let selectedFiles_manifest180 = [];
Dropzone.options.manifest180 = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/png',
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
                selectedFiles_manifest180.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_manifest180.indexOf(file);
            if (index > -1) {
                selectedFiles_manifest180.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};


let selectedFiles_manifest192 = [];
Dropzone.options.manifest192 = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/png',
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
                selectedFiles_manifest192.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_manifest192.indexOf(file);
            if (index > -1) {
                selectedFiles_manifest192.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};


let selectedFiles_manifest384 = [];
Dropzone.options.manifest384 = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/png',
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
                selectedFiles_manifest384.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_manifest384.indexOf(file);
            if (index > -1) {
                selectedFiles_manifest384.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};


let selectedFiles_manifest512 = [];
Dropzone.options.manifest512 = {
    url: 'settings-general',
    method: 'post',
    acceptedFiles: 'image/png',
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
                selectedFiles_manifest512.push(temp);
            }
        });
        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            // $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
        });
        this.on('removedfile', async function (file) {
            let index = selectedFiles_manifest512.indexOf(file);
            if (index > -1) {
                selectedFiles_manifest512.splice(index, 1); // 2nd parameter means remove one item only
            }
        });
    }
};


$('#submit_manifest').click(function () {
    let token = $('#token').val().trim();
    var BTN = Ladda.create(document.querySelector('#submit_manifest'));
    BTN.start();

    let params = new FormData();
    params.append('action', 'settings-manifest');
    params.append('token', token);
    selectedFiles_manifest144.forEach(function (element, index) {
        params.append("manifest144", element);
    });
    selectedFiles_manifest180.forEach(function (element, index) {
        params.append("manifest180", element);

    });
    selectedFiles_manifest192.forEach(function (element, index) {
        params.append("manifest192", element);

    });

    selectedFiles_manifest384.forEach(function (element, index) {
        params.append("manifest384", element);

    });

    selectedFiles_manifest512.forEach(function (element, index) {
        params.append("manifest512", element);

    });

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: params,
        contentType: false,
        processData: false,
        success: function (data) {
            console.table(data)
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
 * End manifest logo Setting
 */



/**
 start settings overall
 */



$("#submit_overall").on('click', function () {

    const submit_overall = Ladda.create(document.querySelector('#submit_overall'));
    let cargo_expire = $('#cargo_expire').val().trim();
    let cargo_distance = $('#cargo_distance').val().trim();
    let r_card_account = $('#r_card_account').val().trim();
    let r_card_iban = $('#r_card_iban').val().trim();
    let r_card_number = $('#r_card_number').val().trim();
    let r_card_number_name = $('#r_card_number_name').val().trim();
    let token = $('#tokenM').val().trim();

    $("#submit_overall").attr('disabled', 'disabled');
    submit_overall.start();
    let data = {
        action: 'settings-overall',
        cargo_expire: cargo_expire,
        cargo_distance: cargo_distance,
        r_card_account: r_card_account,
        r_card_iban: r_card_iban,
        r_card_number: r_card_number,
        r_card_number_name: r_card_number_name,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            console.log(data)
            submit_overall.remove();
            $("#submit_overall").removeAttr('disabled');
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $('#token').val(myArray[1]);
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else {
                toastNotic(temp_lang.warning, temp_lang.error_mag, 'warning');
            }


        }
    });
});

/**
 end settings overall
 */
