let temp_lang = JSON.parse(var_lang);

Dropzone.autoDiscover = false;
let selectFilesPost_attachmentsDropzone = [];
Dropzone.options.attachmentsDropzone = {
    url: '/task/add',
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
                selectFilesPost_attachmentsDropzone.push(temp);
            }
        });

        this.on('uploadprogress', (file, progress, bytesSent) => {
            if (file.accepted) {
                if (file.upload.progress == 100) {
                    $(file['previewElement']).find('*[data-dz-progress]').removeClass('progress-bar-striped').addClass('bg-success');
                }
                $(file['previewElement']).find('*[data-dz-progress]').css("width", file.upload.progress + "%").text(`${Math.floor(file.upload.progress)}%`);
            }
        });

        this.on('removedfile', async function (file) {
            const index = selectFilesPost_attachmentsDropzone.indexOf(file.dataURL);
            if (index > -1) {
                selectFilesPost_attachmentsDropzone.splice(index, 1);
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
    selectFilesPost_attachmentsDropzone.map(function (element, index) {
        if (element.name == file.name) {
            deletedIndex = index;
            return index;
        }
    });
    return deletedIndex;
}

$(document).ready(function () {
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

$('#xTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_xTitle').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_xTitle').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#xDescription').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_xDescription').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_xDescription').html('<b class="text-danger">' + len + '</b>');
    }
});

// let referT = $('#referTask').select2().select2('val');
// start select 2
const select2 = $('#referTask[data-toggle="select2"]').select2({
    templateResult: function (idioma) {
        const title = idioma.text;
        // const symbol = (idioma.id) ? idioma.id : '';
        const symbol = (idioma.element && $(idioma.element).data('tj-category-status')) ? $(idioma.element).data('tj-category-status') : '';
        const image = (idioma.element && $(idioma.element).data('tj-category-image')) ? $(idioma.element).data('tj-category-image') : '';
        const color = (idioma.element && $(idioma.element).data('tj-category-image')) ? $(idioma.element).data('tj-category-color') : '';

        return $(`<span class="sd-asset-item"><img src="${image}" class="sd-asset-image" alt="${title}" /> &nbsp; ${title} &nbsp;<span class="text-${color} font-11">(${symbol})</span></span>`);
    },
    templateSelection: function (idioma) {
        const title = idioma.text;
        // const symbol = (idioma.id) ? idioma.id : '';
        const symbol = (idioma.element && $(idioma.element).data('tj-category-status')) ? $(idioma.element).data('tj-category-status') : '';
        const image = (idioma.element && $(idioma.element).data('tj-category-image')) ? $(idioma.element).data('tj-category-image') : '';
        const color = (idioma.element && $(idioma.element).data('tj-category-image')) ? $(idioma.element).data('tj-category-color') : '';

        return $(`<span class="sd-asset-item-selection"><img src="${image}" class="sd-asset-image" alt="${title}" /> &nbsp; ${title} &nbsp;<span class="text-${color} font-11">(${symbol})</span></span>`);
    }
});

select2.data('select2').$selection.css('height', 'auto');
select2.data('select2').$selection.find('.sd-asset-item').css('line-height', '30px');
select2.data('select2').$selection.find('.select2-selection__arrow').css('height', 'auto');

$('#StartDate').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#startDefault',
    altFormat: 'X',
    minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    }
});

$('#EndDate').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#endDefault',
    altFormat: 'X',
    minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#endDefault').val(Math.floor(unixDate / 1000));
    },
});

$('.clockpicker').clockpicker({
    placement: 'top',
    align: 'right',
    // donetext: 'Done',
    autoclose: true,
    // vibrate:true,

});


$('.setSubmitBtn').click(function () {
    let id = $(this).prop('id');
    let title = $('#xTitle').val().trim();
    let desc = $('#xDescription').val().trim();
    let priority = $('input[name=priority]:checked').val();
    let referTd = $('#referTask').select2().select2('val');
    // let StartDate = $('#StartDate').val().trim();
    let StartTime = $('#StartTime').val().trim();
    // let EndDate = $('#EndDate').val().trim();
    let EndTime = $('#EndTime').val().trim();
    let token = $('#token').val().trim();

    let start = $('#startDefault').val(),
        end = $('#endDefault').val();

    var list_priority = ["important", "critical", "high", "medium", "low", "informational"];

    if (title.length > 2 && desc.length > 2 && jQuery.inArray(priority, list_priority) != -1
        && referTd.length > 0) {

        var BTN = Ladda.create(document.querySelector('#' + id));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let params = new FormData();
        params.append('action', 'task-add');
        params.append('title', title);
        params.append('desc', desc);
        params.append('priority', priority);
        params.append('referTd', referTd);
        params.append('start', start);
        params.append('end', end);
        params.append('StartTime', StartTime);
        params.append('EndTime', EndTime);
        params.append('token', token);

        selectFilesPost_attachmentsDropzone.forEach(function (element, index) {
            params.append(index + "", element);
        });

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: params,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data)
                $(".setSubmitBtn").removeAttr('disabled');
                BTN.remove();
                if (data == 'successful') {
                    $(".setSubmitBtn").attr('disabled', 'disabled');

                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            window.location.replace("/admin/tasks");
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
        if (referTd.length <= 0) {
            toastNotic(temp_lang.error, temp_lang.a_empty_refer);
        } else if (title.length <= 2) {
            toastNotic(temp_lang.error, temp_lang.a_empty_title);
        } else if (desc.length <= 2) {
            toastNotic(temp_lang.error, temp_lang.a_empty_desc);
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
})
