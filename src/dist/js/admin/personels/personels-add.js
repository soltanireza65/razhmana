let personel_name_fa_IR= $('#personel_name_fa_IR');
let personel_name_en_US= $('#personel_name_en_US');
let personel_name_tr_Tr= $('#personel_name_tr_Tr');
let personel_name_ru_RU= $('#personel_name_ru_RU');
let personel_lname_fa_IR = $('#personel_lname_fa_IR');
let personel_lname_en_US = $('#personel_lname_en_US');
let personel_lname_tr_Tr = $('#personel_lname_tr_Tr');
let personel_lname_ru_RU = $('#personel_lname_ru_RU');
let personel_job_fa_IR = $('#personel_job_fa_IR');
let personel_job_en_US = $('#personel_job_en_US');
let personel_job_tr_Tr = $('#personel_job_tr_Tr');
let personel_job_ru_RU = $('#personel_job_ru_RU');
let personel_ref_code = $('#personel_ref_code');
let phone = $('#phone');
let home_numer = $('#home-number');
let whatsapp = $('#whatsapp');
let phone_country_code = $('#phone-country-code');
let home_country_code = $('#home-country-code');
let whatsapp_country_code = $('#whatsapp-country-code');
let personel_email = $('#personel_email');
let personel_desc_fa_IR = $('#personel_desc_fa_IR');
let personel_desc_en_US = $('#personel_desc_en_US');
let personel_desc_tr_Tr = $('#personel_desc_tr_Tr');
let personel_desc_ru_RU = $('#personel_desc_ru_RU');
let add_personel = $('#add-personel');
let personel_avatar = null ;
phone_country_code.select2()
home_country_code.select2()
whatsapp_country_code.select2()

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
            personel_avatar = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#imageUpload").change(function () {
    readURL(this);
});

phone.on('input', function () {
    if ($(this).val().substring(0, 1) == '0' || $(this).val().substring(0, 1) == '٠') {
        $(this).val($(this).val().substring(1));
    }
});
home_numer.on('input', function () {
    if ($(this).val().substring(0, 1) == '0' || $(this).val().substring(0, 1) == '٠') {
        $(this).val($(this).val().substring(1));
    }
});
whatsapp.on('input', function () {
    if ($(this).val().substring(0, 1) == '0' || $(this).val().substring(0, 1) == '٠') {
        $(this).val($(this).val().substring(1));
    }
});


add_personel.click(function () {
    let params = {
        action: 'add-personel',
        personel_name_fa_IR: personel_name_fa_IR.val(),
        personel_name_en_US: personel_name_en_US.val(),
        personel_name_tr_Tr: personel_name_tr_Tr.val(),
        personel_name_ru_RU: personel_name_ru_RU.val(),
        personel_lname_fa_IR: personel_lname_fa_IR.val(),
        personel_lname_en_US: personel_lname_en_US.val(),
        personel_lname_tr_Tr: personel_lname_tr_Tr.val(),
        personel_lname_ru_RU: personel_lname_ru_RU.val(),
        personel_job_fa_IR: personel_job_fa_IR.val(),
        personel_job_en_US: personel_job_en_US.val(),
        personel_job_tr_Tr: personel_job_tr_Tr.val(),
        personel_job_ru_RU: personel_job_ru_RU.val(),
        personel_ref_code: personel_ref_code.val(),
        phone: phone.val(),
        home_numer: home_numer.val(),
        whatsapp: whatsapp.val(),
        phone_country_code: phone_country_code.val(),
        home_country_code: home_country_code.val(),
        whatsapp_country_code: whatsapp_country_code.val(),
        personel_email: personel_email.val(),
        personel_desc_fa_IR: personel_desc_fa_IR.val(),
        personel_desc_en_US: personel_desc_en_US.val(),
        personel_desc_tr_Tr: personel_desc_tr_Tr.val(),
        personel_desc_ru_RU: personel_desc_ru_RU.val(),
        personel_avatar: personel_avatar,
        token: $('#token2').val()
    }
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(params),
        success: function (data) {
            let response =  JSON.parse(data)
            if (response.status == 200 ){
                toastNotic(var_lang.successful, var_lang.alert_success_operations, "success");
                window.location.replace('/admin/personel/')
            }else if(response.status ==402){
                toastNotic(var_lang.error, var_lang.personel_exsits_error, "error");
            }else{
                toastNotic(var_lang.error, var_lang.alert_error_operations, "error");
            }
        }
    });
})


$(function ()
{
    $('.mj-personels-inputs-textarea textarea').keyup(function (e){
        if(e.keyCode == 13){
            var curr = getCaret(this);
            var val = $(this).val();
            var end = val.length;
            $(this).val( val.substr(0, curr) + '<br>' + val.substr(curr, end));
        }

    })
});

function getCaret(el) {
    if (el.selectionStart) {
        return el.selectionStart;
    }
    else if (document.selection) {
        el.focus();

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = el.createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
}