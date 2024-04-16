let temp_lang = JSON.parse(var_lang);

Dropzone.autoDiscover = false;

let selectedFiles = [];
Dropzone.options.attachmentsDropzone = {
    url: 'ticket',
    method: 'post',
    acceptedFiles: 'image/*, application/pdf, application/x-zip-compressed',
    uploadMultiple: true,
    maxFiles: 10,
    autoProcessQueue: true,


    previewTemplate: $('#uploadPreviewTemplate').innerHTML,
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
                selectedFiles.push(temp);
            }
        });


        this.on('uploadprogress', (file, progress, bytesSent) => {
            // console.log(file);
            // console.log(file.upload.progress);
            // console.log(file.upload.bytesSent);

            $(file['previewElement']).find('*[data-dzc-id]').css("width", file.upload.progress + "%");
            // $('#'+ $(file.previewElement).find("[data-dzc-id]").attr('id')).html(file.upload.bytesSent);
           if (file.upload.progress==100){
               $(file['previewElement']).find('*[data-dzc-id]').removeClass("bg-info");
               $(file['previewElement']).find('*[data-dzc-id]').addClass("bg-success");
           }
        });

        this.on('removedfile', async function (file) {
            const index = findItemIndex(file);
            if (index > -1) {
                selectedFiles.splice(index, 1);
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

function findItemIndex(file) {
    let deletedIndex = -1;
    selectedFiles.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$(document).ready(function () {
    if ($('#ticket-message').length > 0) {
        const quill = new Quill("#ticket-message", {
            theme: "snow",
            modules: {
                toolbar: [
                    [{header: [!1, 3, 4, 5, 6]}],
                    ['underline', 'italic', 'bold'],
                    [{color: []}, {background: []}],
                    [{list: 'bullet'}, {list: 'ordered'}],
                    ['video', 'image', 'link']
                ]
            }
        });
    }
    $('#ticket-status').select2();

    $('[data-plugin="dropzone"]').each(function () {
        let t = $(this).attr('action');
        let e = $(this).data('previewsContainer');
        let i = {url: t};
        e && (i.previewsContainer = e);
        let o = $(this).data('uploadPreviewTemplate');
        o && (i.previewTemplate = $(o).html());
        $(this).dropzone(i);
    });
});

$('#BtnSendMassage').click(function () {


    let userID = $('#ticketID').data('user-id');
    let roomID = $('#ticketID').data('room-id');
    let massage = $('#ticket-message .ql-editor').html().trim();
    var massageText = $('#ticket-message .ql-editor').text().length;
    var BtnSendMassage = Ladda.create(document.querySelector('#BtnSendMassage'));
    let token = $('#token').val().trim();


    if (massage.length > 9 && massageText > 2 && userID > 0 && roomID > 0) {

        $("#BtnSendMassage").attr('disabled', 'disabled');
        BtnSendMassage.start();

        let params = new FormData();
        params.append('action', 'set-ticket-exist-room');
        params.append('userID', userID);
        params.append('roomID', roomID);
        params.append('massage', massage);
        params.append('token', token);
        selectedFiles.forEach(function (element, index) {
            params.append(index + "", element);
        });

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: params,
            contentType: false,
            processData: false,
            success: function (data) {

                BtnSendMassage.remove();

                $(".setSubmitBtn").removeAttr('disabled');

                if (data == 'successful') {
                    $("#BtnSendMassage").attr('disabled', 'disabled');
                    $("#BtnSendMassage").val("");

                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
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


    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }


});


if ($('#BtnCloseTicket').length > 0) {
    $('#BtnCloseTicket').click(function () {
        let roomID = $('#ticketID').data('room-id');
        let token = $('#token').val().trim();
        var BtnCloseTicket = Ladda.create(document.querySelector('#BtnCloseTicket'));
        $("#BtnCloseTicket").attr('disabled', 'disabled');

        BtnCloseTicket.start();

        let data = {
            action: 'close-room',
            roomID: roomID,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BtnCloseTicket.remove();
                $("#BtnCloseTicket").removeAttr('disabled');
                if (data == 'successful') {
                    $("#BtnCloseTicket").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
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
}

if ($('#ticket-message').length > 0) {
    $('#ticket-message').keyup(function () {
        var len = $('#ticket-message .ql-editor').text().length;
        if (len > 2) {
            $('#length_ticketMessage').html('<b class="text-success">' + len + '</b>');
        } else {
            $('#length_ticketMessage').html('<b class="text-danger">' + len + '</b>');
        }
    });
}








