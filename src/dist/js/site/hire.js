const formName = $('#form-name');
const formLname = $('#form-lname');
const formFather = $('#form-father');
const formBirthdayLocation = $('#form-birthday-location');
const formBirthdayTime = $('#form-birthday-time');
const formCodeNational = $('#form-code-national');
const formExemptionType = $('#form-exemption-type');
const formCountChild = $('#form-count-child');
const formInsuranceTime = $('#form-insurance-time');
// const formLiveLocationCountry = $('#form-live-location-country');
const liveCountry = $('#live-country');
const liveCity = $('#live-city');
// const formLiveLocationState = $('#form-live-location-state');
// const formLiveLocationCity = $('#form-live-location-city');
const formMobile = $('#form-mobile');
const formPhone = $('#form-phone');
const formAddressLocation = $('#form-address-location');
const formEduName1 = $('#form-edu-name-1');
const formEduName2 = $('#form-edu-name-2');
const formEduName3 = $('#form-edu-name-3');
const formEduName4 = $('#form-edu-name-4');
const formEduName5 = $('#form-edu-name-5');
const formEduAddress1 = $('#form-edu-address-1');
const formEduAddress2 = $('#form-edu-address-2');
const formEduAddress3 = $('#form-edu-address-3');
const formEduAddress4 = $('#form-edu-address-4');
const formEduAddress5 = $('#form-edu-address-5');
const formPrice = $('#form-price');
const representativeName = $('#form-representative-name');
const representativePhone = $('#form-representative-phone');
const formRepresentativeJob = $('#form-representative-job');
const formRepresentativeAddress = $('#form-representative-address');
const hireTitleCategory = $('#form-hire-title-category');
liveCountry.select2({})
liveCity.select2({})
liveCountry.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities',
            country: $(this).val().trim(),
            city: 'city',
            type: 'ground'
        };
        // console.log($(this).val().trim())

        $('#live-city').html('');
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(citiesParams),
            success: function (response) {
                try {
                    let html = '';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
                    });
                    liveCity.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});

$(document).ready(function () {

    $("#progressbarwizard").bootstrapWizard({
        onTabShow: function (t, r, a) {

            var o = (a + 1) / r.find("li").length * 100;
            $("#progressbarwizard").find(".bar").css({width: o + "%"})
            if (a == 0) {
                $('.previous').addClass('d-none');
            } else {
                $('.previous').removeClass('d-none');
            }

            if (a == 6) {
                $('.next a').text(lang_vars.submit);
            } else {
                $('.next a').text(lang_vars.u_next);
            }

            window.scrollTo(0, 0);
        },
        onNext: function (t, r, a) {
            if (a == 1) {
                formName.removeClass('mj-a-border-danger');
                formLname.removeClass('mj-a-border-danger');
                formCodeNational.removeClass('mj-a-border-danger');
                liveCountry.removeClass('mj-a-border-danger');
                // formLiveLocationState.removeClass('mj-a-border-danger');
                // formLiveLocationCity.removeClass('mj-a-border-danger');

               if (formName.val().trim()==""){
                   formName.addClass('mj-a-border-danger');
                   sendNotice(lang_vars.alert_warning, lang_vars.login_user_name_error, 'warning', 2500);
                   $('html,body').animate({scrollTop: formName.first().offset().top - 200},'slow');
                   return false;
               }else if (formLname.val().trim()==""){
                   formLname.addClass('mj-a-border-danger');
                   sendNotice(lang_vars.alert_warning, lang_vars.login_user_lname_error, 'warning', 2500);
                   $('html,body').animate({scrollTop: formLname.first().offset().top - 200},'slow');
                   return false;
               }else if (formCodeNational.val().trim().length !=10){
                   formCodeNational.addClass('mj-a-border-danger');
                   sendNotice(lang_vars.alert_warning, lang_vars.alert_error_auth_id_card, 'warning', 2500);
                   $('html,body').animate({scrollTop: formCodeNational.first().offset().top - 200},'slow');
                   return false;
               }else if (liveCountry.val()==""){
                   liveCountry.addClass('mj-a-border-danger');
                   sendNotice(lang_vars.alert_warning, lang_vars.u_enter_name_country, 'warning', 2500);
                    return false;
               }else if (liveCity.val()==""){
                   liveCity.addClass('mj-a-border-danger');
                   sendNotice(lang_vars.alert_warning, lang_vars.u_enter_name_city, 'warning', 2500);
                   $('html,body').animate({scrollTop: liveCity.first().offset().top - 200},'slow');
                   return false;
               }

            }
            else if (a == 2){
                formMobile.removeClass('mj-a-border-danger');
                $('#profilePassport').removeClass('mj-a-border-danger');


                if (formMobile.val().length < 10){
                    formMobile.addClass('mj-a-border-danger');
                    sendNotice(lang_vars.alert_warning, lang_vars.u_login_enter_phone_number, 'warning', 2500);
                    $('html,body').animate({scrollTop: formMobile.first().offset().top - 200},'slow');
                    return false;
                }else if (selectFiles_profilePassport.length<1){
                    $('#profilePassport').addClass('mj-a-border-danger');
                    sendNotice(lang_vars.alert_warning, lang_vars.u_enter_profile_picture, 'warning', 2500);
                    $('html,body').animate({scrollTop: $('#profilePassport').first().offset().top - 200},'slow');
                    return false;
                }
            }
            else if (a == 7) {

                hireTitleCategory.parent().removeClass('mj-a-border-danger');



                if (hireTitleCategory.val().length < 1){
                    hireTitleCategory.parent().addClass('mj-a-border-danger');
                    sendNotice(lang_vars.alert_warning, lang_vars.u_select_category_title_employ, 'warning', 2500);
                    $('html,body').animate({scrollTop: hireTitleCategory.first().offset().top - 200},'slow');
                    return false;
                }else{
                    submitInfo();
                }

            }
        }
    });
});

$('.repeater').repeater({
    initEmpty: false,
    show: function () {
        $(this).slideDown();
    },
    hide: function (deleteElement) {
        // if (confirm('Are you sure you want to delete this element?')) {
        $(this).slideUp(deleteElement);
        // }
    },
    isFirstItemUndeletable: true
});

$('.repeater-2').repeater({
    initEmpty: false,
    defaultValues: {
            'formLanguageTalk': '1'
    },
    show: function () {
        $(this).slideDown();
    },
    hide: function (deleteElement) {
        // if (confirm('Are you sure you want to delete this element?')) {
        $(this).slideUp(deleteElement);
        // }
    },
    isFirstItemUndeletable: true
});

$('.repeater-3').repeater({
    initEmpty: false,
    show: function () {
        $(this).slideDown();
    },
    hide: function (deleteElement) {
        // if (confirm('Are you sure you want to delete this element?')) {
        $(this).slideUp(deleteElement);
        // }
    },
    isFirstItemUndeletable: true
});


const formGender = $('input[name="form-gender"]');
const formMilitaryService = $('input[name="form-military-service"]');
const formMarital = $('input[name="form-marital"]');


formGender.each(function () {
    $(this).change(function () {

        if ($(this).prop('id') == "form-man") {

            $('#form-military-service').fadeIn(500)
            if ($('#form-military-exempt').is(':checked')) {
                $('#form-exemption-type').parent().parent().fadeIn()
            } else {
                $('#form-exemption-type').parent().parent().fadeOut()
            }
        } else {
            $('#form-military-service').fadeOut(500)
            $('#form-exemption-type').parent().parent().fadeOut(500)
        }
    });
});

formMilitaryService.each(function () {
    $(this).change(function () {
        if ($(this).prop('id') == "form-military-exempt") {
            $('#form-exemption-type').parent().parent().fadeIn(500)
        } else {
            $('#form-exemption-type').parent().parent().fadeOut(500)
        }
    });
});

formMarital.each(function () {
    $(this).change(function () {
        if ($(this).prop('id') == "form-single") {
            $('#form-count-child').parent().parent().fadeOut(500)
        } else {
            $('#form-count-child').parent().parent().fadeIn(500)
        }
    });
});

// Dropzone.autoDiscover = false;
let selectFiles_profilePassport = [];
Dropzone.options.profilePassport = {
    url: '/hire',
    method: 'post',
    acceptedFiles: 'image/*',
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: lang_vars.delete,
    dictMaxFilesExceeded: lang_vars.dictMaxFilesExceeded,
    dictCancelUpload: lang_vars.cancel_upload,
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
            selectFiles_profilePassport.push(file);
        });
        this.on('removedfile', function (file) {
            let index = selectFiles_profilePassport.indexOf(file);
            if (index > -1) {
                selectFiles_profilePassport.splice(index, 1); // 2nd parameter means remove one item only
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

let selectFiles_cvPassport = [];
let selectedFiles = [];
Dropzone.options.cvPassport = {
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
            if (file.upload.progress == 100) {
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

hireTitleCategory.select2({
    // dropdownParent: $('.mj-custom-select'),
    minimumResultsForSearch: Infinity,
    maximumSelectionLength: 10,
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});

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

function submitInfo() {

    const _this = $('.mj-employee-next-btn');

    let groupA = $(".repeater").repeaterVal();
    let company = [];
    $(groupA['group-a']).each(function (index, element) {
        temp = {};
        temp.name = element.formCompanyName;
        temp.timeStart = element.formCompanyTimeStart;
        temp.timeEnd = element.formCompanyTimeEnd;
        temp.reason = element.formCompanyLeftReason;
        company.push(temp)
    })

    let groupB = $(".repeater-2").repeaterVal();
    let record = [];
    $(groupB['group-b']).each(function (index, element) {
        temp = {};
        temp.title = element.formRecordTitle;
        temp.address = element.formRecordAddress;
        temp.period = element.formRecordPeriod;
        record.push(temp)
    })


    let groupC = $(".repeater-3").repeaterVal();
    console.log(groupC)
    let language = [];
    $(groupC['group-c']).each(function (index, element) {
        temp = {};
        temp.title = element.formLanguageTitle;
        temp.talk = element.formLanguageTalk;
        temp.read = element.formLanguageRead;
        temp.write = element.formLanguageWrite;
        // console.log($("[name='group-c["+index+"][formLanguageTalk]']:checked").data('tj-val'));
        language.push(temp)
    })


    let params = new FormData();
    params.append('action', 'submit-employ');
    params.append('name', formName.val().trim());
    params.append('lname', formLname.val().trim());
    params.append('father', formFather.val().trim());
    params.append('birthdayLocation', formBirthdayLocation.val().trim());
    params.append('birthdayTime', formBirthdayTime.val().trim());
    params.append('codeNational', formCodeNational.val().trim());
    params.append('gender', $("input[name='form-gender']:checked").data('tj-val'));
    params.append('military', $("input[name='form-military-service']:checked").data('tj-val'));
    params.append('exemptionType', formExemptionType.val().trim());
    params.append('marital', $("input[name='form-marital']:checked").data('tj-val'));
    params.append('countChild', formCountChild.val().trim());
    params.append('homeStatus', $("input[name='form-home']:checked").data('tj-val'));
    params.append('insuranceStatus', $("input[name='form-insurance']:checked").data('tj-val'));
    params.append('insuranceTime', formInsuranceTime.val().trim());
    params.append('liveLocationCountry', liveCountry.val());
    // params.append('liveLocationState', formLiveLocationState.val().trim());
    params.append('liveLocationCity', liveCity.val());
    params.append('mobile', formMobile.val().trim());
    params.append('phone', formPhone.val().trim());
    params.append('addressLocation', formAddressLocation.val().trim());
    params.append('company', JSON.stringify(company));

    params.append('eduName1', formEduName1.val().trim());
    params.append('eduName2', formEduName2.val().trim());
    params.append('eduName3', formEduName3.val().trim());
    params.append('eduName4', formEduName4.val().trim());
    params.append('eduName5', formEduName5.val().trim());
    params.append('eduAddress1', formEduAddress1.val().trim());
    params.append('eduAddress2', formEduAddress2.val().trim());
    params.append('eduAddress3', formEduAddress3.val().trim());
    params.append('eduAddress4', formEduAddress4.val().trim());
    params.append('eduAddress5', formEduAddress5.val().trim());

    params.append('language', JSON.stringify(language));
    params.append('record', JSON.stringify(record));

    params.append('category', hireTitleCategory.val());
    params.append('work', $("input[name='form-work']:checked").val());
    params.append('guarantee', $("input[name='form-guarantee']:checked").val());
    params.append('transfer', $("input[name='form-transfer']:checked").val());
    params.append('price', formPrice.val().trim(),);
    params.append('representativeName', representativeName.val().trim());
    params.append('representativePhone', representativePhone.val().trim());
    params.append('representativeJob', formRepresentativeJob.val().trim());
    params.append('representativeAddress', formRepresentativeAddress.val().trim());
    params.append('employ', $("input[name='form-employ']:checked").val());
    params.append('token', $('#token').val().trim());

    selectFiles_profilePassport.forEach(function (element, index) {
        params.append("profile", element);
    });

    selectedFiles.forEach(function (element, index) {
        params.append(index + "", element);
    });


    _this.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });
    _this.addClass('tj-a-loader-4');


    $.ajax({
        type: 'POST',
        url: '/api/ajax',
        data: params,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response)
            const json = JSON.parse(response);
            if (json.status == 200) {
                sendNotice(lang_vars.alert_success, lang_vars.u_alert_success_add_employ, 'success', 7000);

                setTimeout(() => {
                    window.location.href = '/';
                }, 5000);
            }else if (json.status == -9) {
                sendNotice(lang_vars.alert_info, lang_vars.u_employ_before_set, 'info', 15000);
                $('#token').val(json.response);
                setCookie('login-back-url', '/user/support')
                setTimeout(() => {
                    window.location.href = '/user/support';
                }, 5000);


                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
                _this.removeClass('tj-a-loader-4');
            }else {
                sendNotice(lang_vars.alert_warning, lang_vars.u_alert_warning_add_employ, 'warning', 7000);
                $('#token').val(json.response);
                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
                _this.removeClass('tj-a-loader-4');
            }
        }
    })
}

