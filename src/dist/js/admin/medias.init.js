let temp_lang = JSON.parse(var_lang);

// $('a.delete').on('click', function () {
$(document).on('click', ".delete", function () {
    let src = $(this).attr('data-src').trim();
    let thiss = $(this);
    let token = $('#token').val().trim();

    let data = {
        action: 'media-delete',
        src: src,
        token: token,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            if (data == "successful") {
                thiss.parent().parent().hide();
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
                window.setTimeout(
                    function () {
                        location.reload();
                    },
                    2000
                );
            }

        }
    });


});

// $('.copyyyyyyyyyyyy').click(function () {
$(document).on('click', ".copyyyyyyyyyyyy", function () {
    let _this = this;
    let srcCopy = $(this).attr('data-mj-src').trim();
    navigator.clipboard.writeText(srcCopy);

    window.setTimeout(
        function () {
            $(_this).tooltip('hide')
        },
        2000
    );
});


let selectedFiles = [];
Dropzone.options.attachmentsDropzone = {
    url: 'medias',
    method: 'post',
    acceptedFiles: 'image/*, application/pdf, application/x-zip-compressed,.mp4,.mkv,.avi,.gif, .ico,audio/*,video/*,.doc,.docx,.txt',
    uploadMultiple: true,
    maxFiles: 20,
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
                selectedFiles.push(temp);
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


// $('.submitUpload').click(function () {
$(document).on('click', ".submitUpload", function () {
     let token = $('#token').val().trim();
    let name = $('#media-name').val().trim() ?? null;

    let params = new FormData();
    params.append('action', 'media-upload');
    params.append('name', name);
    params.append('token', token);
    selectedFiles.forEach(function (element, index) {
        params.append(index + "", element);
    });
    if (file_names.includes(name)) {
        console.log('exists')
        toastNotic(temp_lang.error, temp_lang.exists_media_error);
    } else {
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: params,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                 if (data == "successful") {
                    location.reload();
                } else if (data == "error") {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                } else {
                    toastNotic(temp_lang.error, temp_lang.token_error);
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
                }
            }
        });
    }


});
// $('.show').hover(
//     function () {
//
//         const imageUrl = $(this).attr("href");
//         console.log(imageUrl)
//         if (imageUrl) {
//             $("#popup-image").attr("src", imageUrl);
//             $("#popup-image").removeClass("d-none");
//             $("#popup").show();
//         }
//     }
//     ,
//     function () {
//         $("#popup").hide();
//         $("#popup-image").addClass("d-none");
//     }
// );
$(document).on('mouseenter','.show', function (event) {
    const imageUrl = $(this).attr("href");
        console.log(imageUrl)
        if (imageUrl) {
            $("#popup-image").attr("src", imageUrl);
            $("#popup-image").removeClass("d-none");
            $("#popup").show();
        }
}).on('mouseleave','.show',  function(){
    $("#popup").hide();
        $("#popup-image").addClass("d-none");
});

$(document).ready(function() {
    let table = $('#orders-table').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': '../../../../api/datatable/dt-medias',
        },

        oLanguage: {
            sUrl: "/dist/libs/datatables.net-i18/fa.json",
        },
        responsive: true,
        "paging": true,
        "pageLength": 10,
        "searching": true, // Enable searching

    });

});