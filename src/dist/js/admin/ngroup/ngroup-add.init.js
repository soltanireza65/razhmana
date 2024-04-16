let temp_lang = JSON.parse(var_lang);

tinymce.init({
    selector: '#ngroupBody',
    // font_formats:
    //     " Arial=arial,helvetica,sans-serif;iransans= IRANSans;",
    language: 'fa',
    language_url : '../../dist/libs/TinyMCE/js/langs/fa.js',
    content_style:
        "@import url('../../dist/css/admin/fontiran.css');body {font-family:iransans;direction:rtl; }",
    plugins: [
        'directionality', 'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
        'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
        'media', 'table', 'emoticons', 'template', 'help'
    ],


    //fontfamily styleselect | fontselect
    toolbar: 'undo redo | ltr rtl |  styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
        'forecolor backcolor emoticons | help',
    font_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace'
});

let relation = $('#relation').select2().select2('val');
let language = $('#language').select2().select2('val');

$('#noticTitle').keyup(function () {
    var len = $(this).val().trim().length;
    if (len > 2) {
        $('#length_noticTitle').html('<b class="text-success">' + len + '</b>');
    } else {
        $('#length_noticTitle').html('<b class="text-danger">' + len + '</b>');
    }
});


$('#noticSender').keyup(function () {
    var len1 = $(this).val().trim().length;
    if (len1 > 2) {
        $('#length_noticSender').html('<b class="text-success">' + len1 + '</b>');
    } else {
        $('#length_noticSender').html('<b class="text-danger">' + len1 + '</b>');
    }
});


$('.setSubmitBtn').on('click', function () {

    let title = $('#noticTitle').val().trim();
    let sender = $('#noticSender').val().trim();
    var text = tinymce.get("ngroupBody").getContent({});
    let id = $(this).attr('id');
    let relation = $('#relation').select2().select2('val');
    let language = $('#language').select2().select2('val');
    let BTNN = Ladda.create(document.querySelector('#' + id));
    let token = $('#token').val().trim();

    let status = "inactive";
    if (id == "btnPublish") {
        status = "active"
    }

    let x = [];
    $('#relation').map(function (key, element) {
        $(element).find('option').map(function (key, value) {
            if ($(this).is('[data-select2-id]')) {
                let i = {};
                i.type = $(this).attr('data-type');
                i.id = $(this).val();
                x.push(i);
            }
        });

    });


    let selected = [];
    $(x).each(function (index, value) {
        if (jQuery.inArray(value.id, relation) != -1) {
            selected.push(value);
        }
    });

    if (title.length > 2 && sender.length > 2 && text.length > 9 && selected.length > 0) {


        let notics_type= $('#notic-type').val();
        if (notics_type!=-1){
            BTNN.start();
            $(".btn").attr('disabled', 'disabled');
            let data = {
                action: 'ngroup-add',
                status: status,
                title: title,
                sender: sender,
                text: text,
                type: selected,
                language: language,
                notics_type:notics_type,
                token: token,
            };
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    BTNN.remove();
                    $(".btn").removeAttr('disabled');

                    if (data == 'successful') {
                        $(".btn").attr('disabled', 'disabled');

                        toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                        window.setTimeout(
                            function () {
                                window.location.replace("/admin/ngroup");
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
        }else{
            toastNotic(temp_lang.error, temp_lang.notices_type_not_empty);
        }


    } else if (selected.length <= 0) {
        toastNotic(temp_lang.error, temp_lang.group_users_empty_error);
    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});