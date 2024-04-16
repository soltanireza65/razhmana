Dropzone.autoDiscover = false;


let department = $('#departemant-select');
department.select2({
    placeholder: lang_vars.d_Support_departemant_select,
})
let ticket_title = $('#ticket_title');
let ticket_desc = $('#ticket_description');
///////////
let selected_files = [];
var ticket_attaments_dropzone = new Dropzone('#my-support-dz', {
    url: '/driver/support',
    previewTemplate: document.querySelector('#preview-template').innerHTML,
    parallelUploads: 5,
    acceptedFiles: "image/*",
    autoQueue: true,
    addRemoveLinks: true,
    autoProcessQueue: true,
    maxFiles: 5,
    dictCancelUpload: lang_vars.b_cargo_cancel,
    dictRemoveFile: lang_vars.u_delete,
    dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
    thumbnail: function (file, dataUrl) {
        if (file.previewElement) {
            file.previewElement.classList.remove("dz-file-preview");
            var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
            for (var i = 0; i < images.length; i++) {
                var thumbnailElement = images[i];
                thumbnailElement.alt = file.name;
                thumbnailElement.src = dataUrl;
            }
            setTimeout(function () {
                file.previewElement.classList.add("dz-image-preview");
            }, 1);
        }
    },
    init: function () {
        this.on("maxfilesexceeded", function (file) {
            this.removeFile(file);
            $("#support-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
        });
        this.on("complete", function (file) {
            if (!file.type.match('image.*')) {
                this.removeFile(file);
                $("#support-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                return false;
            }
        });
        this.on('success', async function (file) {
            if (file.accepted) {
                selected_files.push(file.dataURL);
            }
        });
        this.on('removedfile', async function (file) {
            const index = selected_files.indexOf(file.dataURL);
            if (index > -1) {
                selected_files.splice(index, 1);
            }
        });
    }
});


$('#submit_ticket').click(function () {
    if (ticket_title.val().trim().length > 3) {

        if (ticket_desc.val().trim().length > 20) {

            if (department.val() == -1) {
                sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_department, 'warning', 3500);
            } else {

                let params = {
                    action: 'submit-ticket',
                    ticket_title: ticket_title.val(),
                    ticket_desc: ticket_desc.val(),
                    selected_files: selected_files,
                    department: department.val(),
                    token: $('#token').val()
                }
                console.log(params)

                $.ajax({
                    url: '/api/ajax',
                    type: 'POST',
                    data: JSON.stringify(params),
                    success: function (response) {
                        try {
                            const json = JSON.parse(response);
                            if (json.status == 200) {

                                sendNotice(lang_vars.alert_success, lang_vars.d_alert_ticket_submitted, 'success', 2500);
                                setTimeout(function () {
                                    window.location.reload()
                                } , 2500)
                            } else {
                                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                            }
                        } catch (e) {
                            sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                        }
                    }
                })
            }
        } else {
            sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_message, 'warning', 3500);
        }
    } else {
        sendNotice(lang_vars.alert_warning, lang_vars.alert_ticket_subject, 'warning', 3500);
    }


})
