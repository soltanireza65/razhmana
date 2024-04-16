tinymce.init({
    selector: '#xBody',
    // font_formats:
    //     " Arial=arial,helvetica,sans-serif;iransans= IRANSans;",
    language: 'fa',
    language_url: '../../../dist/libs/TinyMCE/js/langs/fa.js',
    content_style:
        "@import url('../../../dist/css/admin/fontiran.css');body {font-family:iransans;direction:rtl; }",
    plugins: [
        'directionality', 'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
        'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
        'media', 'table', 'emoticons', 'template', 'help'
    ],
    link_rel_list: [
        { title: 'No Referrer', value: 'noreferrer' },
        { title: 'External Link', value: 'external' },
        { title: 'No Follow', value: 'nofollow' },
        { title: 'No Opener', value: 'noopener' }

    ],
    //fontfamily styleselect | fontselect
    toolbar: 'undo redo | ltr rtl |  styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
        'forecolor backcolor emoticons | help',
    font_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace'
});

let temp_lang = JSON.parse(var_lang);


let postCategries = $('#xCategries').select2().select2('val');

let selectFilesPost = [];
// myAwesomeDropzone  id
Dropzone.options.uploadPost = {
    url: 'academy-edit',
    method: 'post',
    acceptedFiles: 'image/*',
    maxFilesize: 10,
    maxThumbnailFilesize: 10,
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
            // if (this.files[1] != null) {
            //     this.removeFile(this.files[0]);
            //     $('#avatarAdminUrl').val('');
            // }
            selectFilesPost.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFilesPost.indexOf(file);
            if (index > -1) {
                selectFilesPost.splice(index, 1); // 2nd parameter means remove one item only
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


$('.setSubmitBtn').click(function () {

    let btn = $(this).prop('id');
    let id = $(this).data('tj-id');

    let token = $('#token').val().trim();

    let title = $('#xTitle').val().trim();
    var myContent = tinymce.get("xBody").getContent({});
    let excerpt = $('#xExcerpt').val().trim();
    let metaTitle = $('#xMetaTitle').val().trim();
    let schema = $('#xSchema').val().trim();
    let language = $('#xCategries').find(":selected").data("tj-language");
    let category = $('#xCategries').select2().select2('val');
    let xSlug = $('#xSlug').val().trim();


    let img_thumbnail = '';
    if (selectFilesPost.length > 0) {
        img_thumbnail = selectFilesPost[0].dataURL;
    }


    if (title.length > 2 && category.length > 0 && xSlug.length > 4 && id > 0 && language.length > 3) {

        var BTN = Ladda.create(document.querySelector('#' + btn));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let status = "draft";
        if (btn == 'btnPublish') {
            status = "published";
        }

        let data = {
            action: 'academy-edit',
            status: status,
            title: title,
            myContent: myContent,
            excerpt: excerpt,
            metaTitle: metaTitle,
            schema: schema,
            category: category,
            slug: xSlug,
            thumbnail: img_thumbnail,
            language: language,
            token: token,
            id: id,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
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


$('#xTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_xTitle').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_xTitle').html('<b class="text-danger">' + len + '</b>');
    }
});
var len1 = $('#xTitle').val().trim().length;
if (len1 > 2) {
    $('#length_xTitle').html('<b class="text-success">' + len1 + '</b>');
} else {
    $('#length_xTitle').html('<b class="text-danger">' + len1 + '</b>');
}


$('#xSlug').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 4) {
        $('#length_xSlug').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_xSlug').html('<b class="text-danger">' + len + '</b>');
    }
});
var len2 = $('#xSlug').val().trim().length;
if (len2 > 4) {
    $('#length_xSlug').html('<b class="text-success">' + len2 + '</b>');
} else {
    $('#length_xSlug').html('<b class="text-danger">' + len2 + '</b>');
}

$('#xMetaTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 70) {
        $('#length_xMetaTitle').html('<b class="text-info">' + len + '</b>');
    } else if (len == 70) {
        $('#length_xMetaTitle').html('<b class="text-success">' + len + '</b>');
    } else if (len > 70) {
        $('#length_xMetaTitle').html('<b class="text-warning">' + len + '</b>');
    }
});
var len3 = $('#xMetaTitle').val().trim().length;
if (len3 > 0 && len3 < 70) {
    $('#length_xMetaTitle').html('<b class="text-info">' + len3 + '</b>');
} else if (len3 == 70) {
    $('#length_xMetaTitle').html('<b class="text-success">' + len3 + '</b>');
} else if (len3 > 70) {
    $('#length_xMetaTitle').html('<b class="text-warning">' + len3 + '</b>');
}


$('#xExcerpt').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 0 && len < 150) {
        $('#length_xExcerpt').html('<b class="text-info">' + len + '</b>');
    } else if (len == 150) {
        $('#length_xExcerpt').html('<b class="text-success">' + len + '</b>');
    } else if (len > 150) {
        $('#length_xExcerpt').html('<b class="text-warning">' + len + '</b>');
    }
});
var len4 = $('#xExcerpt').val().trim().length;
if (len4 > 0 && len4 < 150) {
    $('#length_xExcerpt').html('<b class="text-info">' + len4 + '</b>');
} else if (len4 == 150) {
    $('#length_xExcerpt').html('<b class="text-success">' + len4 + '</b>');
} else if (len4 > 150) {
    $('#length_xExcerpt').html('<b class="text-warning">' + len4 + '</b>');
}


$('#xSchema').keyup(function () {
    var len = $(this).val().trim().length;
    $('#length_xSchema').html('<b class="text-info">' + len + '</b>');
});
var len5 = $('#xSchema').val().trim().length;
$('#length_xSchema').html('<b class="text-info">' + len5 + '</b>');


var formSubmitting = false;
var setFormSubmitting = function() {
    debugger;
    formSubmitting = true;
};
window.onload = function() {
    window.addEventListener("beforeunload", function(e) {
        console.log('beforeunload');
        var confirmationMessage = 'It looks like you have been editing something. ';
        confirmationMessage += 'If you leave before saving, your changes will be lost.';
        var isDirty = false;
        //Here we are checking the condition
        if ($('#managerForm').serialize() != $('#managerForm').data('serialize')) isDirty = true;
        else e = null; // i.e; if form state change show warning box, else don't show it.


        if (formSubmitting || !isDirty) {
            return undefined;
        }

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
};


$('#btnUpdateDate').click(function (){
    let btn = $(this).prop('id');
    let id = $(this).data('tj-id');
    let token = $('#token').val().trim();

    if ( id > 0) {

        var BTN = Ladda.create(document.querySelector('#' + btn));
        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'academy-edit-update-date',
            token: token,
            id: id,
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                console.log(data)
                BTN.remove();
                $(".btn").removeAttr('disabled');

                if (data == 'successful') {
                    // $(".btn").attr('disabled', 'disabled');
                    toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                    // window.setTimeout(
                    //     function () {
                    //         location.reload();
                    //     },
                    //     2000
                    // );
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
})