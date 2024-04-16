

$("#num").keypress(function (e){
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 65 || charCode > 90)) {
        return false;
    }
    return true;
});

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
    // $('#ticket-status').select2();
    let departments = $('#departments').select2().select2('val');

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
    let title = $('#TitleSendMassage').val().trim();
    let userID = $('#ticketID').data('user-id');
    let massage = $('#ticket-message .ql-editor').html().trim();
    var massageText = $('#ticket-message .ql-editor').text().length;
    let departments = $('#departments').select2().select2('val');
    let token = $('#token').val().trim();

    var BtnSendMassage = Ladda.create(document.querySelector('#BtnSendMassage'));

    if (title.length > 2 && massage.length > 9 && massageText > 2 && userID > 0 && departments > 0) {

        $("#BtnSendMassage").attr('disabled', 'disabled');
        BtnSendMassage.start();

        let params = new FormData();
        params.append('action', 'set-new-ticket-and-room');
        params.append('userID', userID);
        params.append('massage', massage);
        params.append('title', title);
        params.append('token', token);
        params.append('departments', departments);
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
                $("#BtnSendMassage").removeAttr('disabled');

                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $("#BtnSendMassage").attr('disabled', 'disabled');
                    $("#TitleSendMassage").val("");
                    $("#ticket-message .ql-editor").html("");

                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/ticket/open/" + myArray[1]);
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

$('#TitleSendMassage').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_TitleSendMassage').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_TitleSendMassage').html('<b class="text-danger">' + len + '</b>');
    }
});

if ($('#ticket-message').length > 0) {
    $('#ticket-message').keyup(function () {
        var len1 = $('#ticket-message .ql-editor').text().length;
        if (len1 > 2) {
            $('#length_ticketMessage').html('<b class="text-success">' + len1 + '</b>');
        } else {
            $('#length_ticketMessage').html('<b class="text-danger">' + len1 + '</b>');
        }
    });
}