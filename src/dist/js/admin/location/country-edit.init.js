let temp_lang = JSON.parse(var_lang);

let selectFilesPost = [];
// myAwesomeDropzone  id
Dropzone.options.uploadImage = {
    url: 'country-edit',
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
            if (this.files[1] != null) {
                this.removeFile(this.files[0]);
            }
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


    let title = $(".titleCategory").map(function () {

        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();


    let title_save = "[" + title + "]";
    let btn = $(this).attr('id');
    let id = $('.setSubmitBtn').data('id');

    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });

    let token = $('#token').val().trim();
    var BTN = Ladda.create(document.querySelector('#' + btn));
    let iso_country_two_word = $('#iso_country_two_word').val().trim();
    let iso_language = $('#iso_language').val().trim();
    let a_status_login = $('#a_status_login').is(':checked');
    let a_status_poster = $('#a_status_poster').is(':checked');
    let country_code = $('#country_code').val().trim();
    let country_display_code = $('#country_display_code').val().trim();
    let priority = $('#priority').val().trim();


    let img_thumbnail = '';
    if (selectFilesPost.length > 0) {
        img_thumbnail = selectFilesPost[0].dataURL;
    }

    if (flag && id != 0 && iso_country_two_word.length == 2 && iso_language.length > 4 && country_display_code.length > 1 && country_code.length > 2) {
        BTN.start();
        $(".btn").attr('disabled', 'disabled');


        let data = {
            action: 'country-edit',
            img: img_thumbnail,
            status_login: a_status_login,
            status_poster: a_status_poster,
            title: title_save,
            iso_country_two_word: iso_country_two_word,
            iso_language: iso_language,
            country_code: country_code,
            country_display_code: country_display_code,
            id: id,
            priority: priority,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {

                BTN.remove();
                $(".setSubmitBtn").removeAttr('disabled');

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


$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
    }
});


var len = $('.titleCategory');
len.each(function (index, element) {
    let lennn = $(element).val().trim().length;
    var idid = $(element).attr('data-id');
    if (lennn > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + lennn + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + lennn + '</b>');
    }
});


$('#iso_language').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 4) {
        $('#iso_language_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#iso_language_text').html('<b class="text-danger">' + len + '</b>');
    }
});
var len1 = $('#iso_language').val().trim().length;
if (len1 > 4) {
    $('#iso_language_text').html('<b class="text-success">' + len1 + '</b>');
} else {
    $('#iso_language_text').html('<b class="text-danger">' + len1 + '</b>');
}

$('#iso_country_two_word').keyup(function () {
    var len = $(this).val().trim().length;
    if (len == 2) {
        $('#iso_country_two_word_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#iso_country_two_word_text').html('<b class="text-danger">' + len + '</b>');
    }
});
var len2 = $('#iso_country_two_word').val().trim().length;
if (len2 == 2) {
    $('#iso_country_two_word_text').html('<b class="text-success">' + len2 + '</b>');
} else {
    $('#iso_country_two_word_text').html('<b class="text-danger">' + len2 + '</b>');
}


$('#country_display_code').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 1) {
        $('#country_display_code_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#country_display_code_text').html('<b class="text-danger">' + len + '</b>');
    }
});
var len3 = $('#country_display_code').val().trim().length;
if (len3 > 1) {
    $('#country_display_code_text').html('<b class="text-success">' + len3 + '</b>');
} else {
    $('#country_display_code_text').html('<b class="text-danger">' + len3 + '</b>');
}


$('#country_code').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#country_code_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#country_code_text').html('<b class="text-danger">' + len + '</b>');
    }
});
var len4 = $('#country_code').val().trim().length;
if (len4 > 2) {
    $('#country_code_text').html('<b class="text-success">' + len4 + '</b>');
} else {
    $('#country_code_text').html('<b class="text-danger">' + len4 + '</b>');
}

$('#iso_language').mask('SS_EE', {
    'translation': {
        S: {pattern: /[a-z]/},
        E: {pattern: /[A-Z]/},
    }
});
$('#iso_country_two_word').mask('SS', {
    'translation': {
        S: {pattern: /[a-z]/},
    }
});
$('#country_code').mask('AASSSS', {
    'translation': {
        S: {pattern: /[0-9]/},
        A: {pattern: /[0]/},
    }
});
$('#country_display_code').mask('+SSSS', {
    'translation': {
        S: {pattern: /[0-9]/},
    }
});