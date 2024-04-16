let temp_lang = JSON.parse(var_lang);

let selectFilesPost = [];
// myAwesomeDropzone  id
Dropzone.options.uploadImage = {
    url: 'country-add',
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
    let id = $(this).attr('id');
    let token = $('#token').val().trim();
    let iso_country_two_word = $('#iso_country_two_word').val().trim();
    let iso_language = $('#iso_language').val().trim();
    let a_status_login = $('#a_status_login').is(':checked');
    let a_status_poster = $('#a_status_poster').is(':checked');
    let country_code = $('#country_code').val().trim();
    let country_display_code = $('#country_display_code').val().trim();
    let priority = $('#priority').val().trim();


    var title = $(".titleCategory").map(function () {
        let temp1 = '"slug":"' + $(this).attr('data-slug') + '"';
        let temp2 = '"value":"' + $(this).val().trim() + '"';
        return "{" + temp1 + "," + temp2 + "}";
    }).get();


    let title_save = "[" + title + "]";


    let flag = true;
    $.each(title, function (i, item) {
        // console.log(jQuery.parseJSON(item).slug);
        let temp = jQuery.parseJSON(item).value;
        if (temp.toString().length < 3) {
            flag = false;
        }
    });


    let img_thumbnail = '';
    if (selectFilesPost.length > 0) {
        img_thumbnail = selectFilesPost[0].dataURL;
    }

    if (flag && iso_country_two_word.length == 2 && iso_language.length > 4 && img_thumbnail.length > 0 && country_display_code.length > 1 && country_code.length > 2) {


        let BTN = Ladda.create(document.querySelector('#' + id));

        BTN.start();
        $(".btn").attr('disabled', 'disabled');

        let data = {
            action: 'country-add',
            img: img_thumbnail,
            status_login: a_status_login,
            status_poster: a_status_poster,
            title: title_save,
            iso_country_two_word: iso_country_two_word,
            iso_language: iso_language,
            country_code: country_code,
            country_display_code: country_display_code,
            priority: priority,
            token: token,
        };

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                BTN.remove();
                $(".btn").removeAttr('disabled');
                console.log(data)
                const myArray = data.split(" ");
                if (myArray[0] == 'successful') {
                    $(".btn").attr('disabled', 'disabled');

                    toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                    window.setTimeout(
                        function () {
                            // window.location.replace("/admin/"+type+"/edit/" + myArray[1]);
                            window.location.replace("/admin/country/add");

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

$('.titleCategory').keyup(function () {
    var len = $(this).val().trim().length;
    var idid = $(this).attr('data-id');
    if (len > 2) {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-success">' + len + '</b>');
    } else {
        $('span[data-id-length="' + idid + '"]').html('<b class="text-danger">' + len + '</b>');
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


$('#iso_country_two_word').keyup(function () {
    var len = $(this).val().trim().length;
    if (len == 2) {
        $('#iso_country_two_word_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#iso_country_two_word_text').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#country_display_code').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 1) {
        $('#country_display_code_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#country_display_code_text').html('<b class="text-danger">' + len + '</b>');
    }
});

$('#country_code').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#country_code_text').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#country_code_text').html('<b class="text-danger">' + len + '</b>');
    }
});

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
