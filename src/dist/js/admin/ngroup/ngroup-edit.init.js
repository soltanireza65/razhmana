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
var len0 = $('#noticTitle').val().trim().length;
if (len0 > 2) {
    $('#length_noticTitle').html('<b class="text-success">' + len0 + '</b>');
} else {
    $('#length_noticTitle').html('<b class="text-danger">' + len0 + '</b>');
}


$('#noticSender').keyup(function () {
    var len1 = $(this).val().trim().length;
    if (len1 > 2) {
        $('#length_noticSender').html('<b class="text-success">' + len1 + '</b>');
    } else {
        $('#length_noticSender').html('<b class="text-danger">' + len1 + '</b>');
    }
});
var len11 = $('#noticSender').val().trim().length;
if (len11 > 2) {
    $('#length_noticSender').html('<b class="text-success">' + len11 + '</b>');
} else {
    $('#length_noticSender').html('<b class="text-danger">' + len11 + '</b>');
}

$('.setSubmitBtn').on('click', function () {

    let title = $('#noticTitle').val().trim();
    let sender = $('#noticSender').val().trim();
    var text = tinymce.get("ngroupBody").getContent({});
    let id = $(this).attr('id');
    let gnID = $(this).attr('data-ngroup-id');
    let relation = $('#relation').select2().select2('val');
    let relation_type = $("#relation option:selected").attr('data-type');
    let token = $('#token').val().trim();
    let language = $('#language').select2().select2('val');

    let BTNN = Ladda.create(document.querySelector('#' + id));

    let status = "inactive";
    if (id == "btnPublish") {
        status = "active"
    }

    if (title.length > 2 && sender.length > 2 && text.length > 9 && relation.length > 0 && relation_type.length > 0) {

        let notics_type= $('#notic-type').val();
        if (notics_type!=-1){
            BTNN.start();
            $(".btn").attr('disabled', 'disabled');
            let data = {
                action: 'ngroup-edit',
                status: status,
                title: title,
                sender: sender,
                text: text,
                relation: relation,
                relation_type: relation_type,
                language: language,
                notics_type:notics_type,
                token: token,
                id: gnID,
            };
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    BTNN.remove();
                    $(".btn").removeAttr('disabled');

                    if (data == 'successful') {
                        // $(".btn").attr('disabled', 'disabled');
                        toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                        // window.setTimeout(
                        //     function () {
                        //         window.location.reload();
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
        }else{
            toastNotic(temp_lang.error, temp_lang.notices_type_not_empty);
        }


    } else {
        toastNotic(temp_lang.error, temp_lang.empty_input);
    }

});


$('#btnDelete').click(function () {
    let gnID = $(this).attr('data-ngroup-id');
    let token = $('#token').val().trim();
    let BTNN = Ladda.create(document.querySelector('#btnDelete'));
    BTNN.start();
    $(".btn").attr('disabled', 'disabled');

    let data = {
        action: 'ngroup-delete',
        id: gnID,
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

                toastNotic(temp_lang.successful, temp_lang.successful_delete_mag, "info");
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
});