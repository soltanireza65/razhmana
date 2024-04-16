let user_id;
let city_id;
let cv_name;
let cv_lname;
let cv_brith_date;
let cv_gender = 'mr';
let cv_marital_status = 'single';
let cv_military_status = my_cv.cv_military_status;
let cv_military_image = [];
let cv_military_number;
let cv_military_date;
let cv_smartcard_status = my_cv.cv_smartcard_status;
let cv_smartcard_image = [];
let cv_smartcard_number;
let cv_smartcard_date;
let cv_passport_status = my_cv.cv_passport_status;
let cv_passport_image = [];
let cv_passport_number;
let cv_passport_date;
let cv_visa_status = my_cv.cv_visa_status;
let cv_visa_image = [];
let cv_visa_number;
let cv_visa_date;
let cv_visa_location;
let cv_workbook_status = my_cv.cv_workbook_status;
let cv_workbook_image = [];
let cv_workbook_number;
let cv_workbook_date;
let cv_driver_license_status = my_cv.cv_driver_license_status;
let cv_driver_license_image = [];
let cv_driver_license_number;
let cv_driver_license_date;
let cv_mobile;
let cv_whatsapp;
let cv_address;
let cv_faviroite_country = [];
let cv_role_status = 'no';
let cv_user_avatar = null


let soldierSection = $('#soldier-input1')
let aiCardSection = $('#ai-card-input1')
let passportSection = $('#passport-input1')
let visaSection = $('#visa-input1')
let workbookSection = $('#workbook-input1')
let driverLicenseSection = $('#drivelicense-input1')
let language = getCookie('language')


let country = $('#country-select');
let city = $('#city-select');


$(document).ready(function () {
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
                cv_user_avatar = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").change(function () {
        readURL(this);
    });

    $('#marriage-select').select2({
        placeholder: lang_vars.u_driver_cv_marital_status,
        minimumResultsForSearch: -1
    });

    $('#sex-type').select2({
        placeholder: lang_vars.u_driver_cv_gender,
        minimumResultsForSearch: -1
    });


    $('#fav-road-select').select2({
        placeholder: lang_vars.cv_drivers_fav_country,
        maximumSelectionLength: 3,
        language: {
            maximumSelected: function (e) {
                return lang_vars.cv_fav_country_count.replace('##', e.maximum)
            }
        }
    });

    let visaLocation = $('#visa-location');
    let visa_location_params = {
        action: 'get-visa-location'
    }
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(visa_location_params),
        success: function (response) {
            let language = getCookie('language')
            try {
                let html = '';
                const json = JSON.parse(response);
                let selected_visa_location = (my_cv.cv_visa_location)

                json.response.forEach(function (item) {
                    let selected =selected_visa_location.indexOf(item.visa_id.toString()) != -1 ? 'selected' : '';

                    html += `
                        <option value="${item.visa_id}" ${selected}>${item.visa_name_fa_IR}</option>
                        `;
                });
                visaLocation.html(html);
            } catch (e) {
                console.log(e)
                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
            }
        }
    });
    visaLocation.select2(
        {
            placeholder: function () {
                $(this).data('placeholder');
            },
            dropdownParent: $('.mj-custom-select'), language: {
                noResults: function () {
                    return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
                }
            }, sorter: function (results) {
                var query = $('.select2-search__field').val().toLowerCase();
                return results.sort(function (a, b) {
                    return a.text.toLowerCase().indexOf(query) - b.text.toLowerCase().indexOf(query);
                });
            }, templateResult: function (data) {
                const title = data.text;

                return $(`
                    <span class="mj-custom-select-item mj-font-13">
                        ${title}
                    </span>
                `);
            }, templateSelection: function (data) {
                const title = data.text;

                return $(`
                    <span class="mj-custom-select-item mj-font-13">
                        ${title}
                    </span>
                `);
            }
        });


    $('b[role="presentation"]').hide();
    $('.select2-selection__arrow').append('<span class="fa-caret-down" style="font-family: FontAwesome;color:#303030"></span>');

    let brithday_picker = $('#brithday').persianDatepicker({
        format: 'YYYY/MM/DD',
        altField: '#brithday-ts',
        altFormat: 'X',
        viewMode: 'year',
        initialValue: false,
        initialValueType: 'gregorian',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
    });
    var initialTimestamp = Math.floor(my_cv.cv_brith_date);
    brithday_picker.setDate(new Date(initialTimestamp * 1000));

    var dropzone = new Dropzone('#soldier-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#soldier-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#soldier-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_military_image.push(file.dataURL);
                }
                console.log(cv_military_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_military_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_military_image.splice(index, 1);
                }
                console.log(cv_military_image)
            });
            let loop = JSON.parse(my_cv.cv_military_image);
            for (let i = 0; i < loop.length; i++) {
                var imageUrl = "https://ntirapp.local" + loop[i];
                var filename = getFileNameFromUrl(imageUrl);
                var xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var file = new File([blob], filename, {type: blob.type});
                        dropzone.addFile(file);
                    }
                };
                xhr.send();
            }
        }


    });

    var dropzone2 = new Dropzone('#aicard-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            let loop = JSON.parse(my_cv.cv_smartcard_image);
            for (let i = 0; i < loop.length; i++) {
                var imageUrl = "https://ntirapp.local" + loop[i];
                var filename = getFileNameFromUrl(imageUrl);
                var xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var file = new File([blob], filename, {type: blob.type});
                        dropzone2.addFile(file);
                    }
                };
                xhr.send();
            }
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#aicard-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#aicard-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_smartcard_image.push(file.dataURL);
                }
                console.log(cv_smartcard_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_smartcard_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_smartcard_image.splice(index, 1);
                }
                console.log(cv_smartcard_image)
            });
        }


    });

    var dropzone3 = new Dropzone('#passport-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            let loop = JSON.parse(my_cv.cv_passport_image);
            for (let i = 0; i < loop.length; i++) {
                var imageUrl = "https://ntirapp.local" + loop[i];
                var filename = getFileNameFromUrl(imageUrl);
                var xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var file = new File([blob], filename, {type: blob.type});
                        dropzone3.addFile(file);
                    }
                };
                xhr.send();
            }
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#passport-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#passport-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_passport_image.push(file.dataURL);
                }
                console.log(cv_passport_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_passport_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_passport_image.splice(index, 1);
                }
                console.log(cv_passport_image)
            });
        }


    });

    var dropzone4 = new Dropzone('#visa-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            let loop = JSON.parse(my_cv.cv_visa_image);
            for (let i = 0; i < loop.length; i++) {
                var imageUrl = "https://ntirapp.local" + loop[i];
                var filename = getFileNameFromUrl(imageUrl);
                var xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var file = new File([blob], filename, {type: blob.type});
                        dropzone4.addFile(file);
                    }
                };
                xhr.send();
            }
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#visa-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#visa-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_visa_image.push(file.dataURL);
                }
                console.log(cv_visa_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_visa_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_visa_image.splice(index, 1);
                }
                console.log(cv_visa_image)
            });
        }


    });

    var dropzone5 = new Dropzone('#workbook-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            let loop = JSON.parse(my_cv.cv_workbook_image);
            for (let i = 0; i < loop.length; i++) {
                var imageUrl = "https://ntirapp.local" + loop[i];
                var filename = getFileNameFromUrl(imageUrl);
                var xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var file = new File([blob], filename, {type: blob.type});
                        dropzone5.addFile(file);
                    }
                };
                xhr.send();
            }
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#workbook-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#workbook-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_workbook_image.push(file.dataURL);
                }
                console.log(cv_workbook_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_workbook_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_workbook_image.splice(index, 1);
                }
                console.log(cv_workbook_image)
            });
        }


    });

    var dropzone6 = new Dropzone('#drivelicense-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 2,
        dictCancelUpload: lang_vars.b_cargo_cancel,
        dictRemoveFile: lang_vars.u_delete,
        dictCancelUploadConfirmation: lang_vars.cv_cancel_upload_dropzone_alert,
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {

            let loop = JSON.parse(my_cv.cv_driver_license_image);
            for (let i = 0; i < loop.length; i++) {
                let imageUrl = "https://ntirapp.local" + loop[i];
                let filename = getFileNameFromUrl(imageUrl);
                let xhr = new XMLHttpRequest();
                xhr.open("GET", imageUrl, true);
                xhr.responseType = "blob";
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let blob = xhr.response;
                        let file = new File([blob], filename, {type: blob.type});
                        dropzone6.addFile(file);
                    }
                };
                xhr.send();
            }
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#drivelicense-error").replaceWith("<div style='color: red'>" + lang_vars.cv_max_upload_dropzone_alert.replace("##", this.files.length) + "</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#drivelicense-error").replaceWith("<div style='color: red'>" + lang_vars.cv_image_upload_dropzone_alert + "</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    cv_driver_license_image.push(file.dataURL);
                }
                console.log(cv_driver_license_image)
            });
            this.on('removedfile', async function (file) {
                const index = cv_driver_license_image.indexOf(file.dataURL);
                if (index > -1) {
                    cv_driver_license_image.splice(index, 1);
                }
                console.log(cv_driver_license_image)
            });
        }


    });

    let military_date_picker = $('#cv-military-date').persianDatepicker({
        altField: '#cv-military-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',
    });

    military_date_picker.setDate(new Date(Math.floor(my_cv.cv_military_date) * 1000));
    let visa_date_picker = $('#cv-visa-date').persianDatepicker({
        altField: '#cv-visa-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',
    });
    visa_date_picker.setDate(new Date(Math.floor(my_cv.cv_visa_date) * 1000));
    //
    let smart_date_picker = $('#cv-smart-card-date').persianDatepicker({
        altField: '#cv-smart-card-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',

    });
    smart_date_picker.setDate(new Date(Math.floor(my_cv.cv_smartcard_date) * 1000));

    let passport_date_picker = $('#cv-passport-date').persianDatepicker({
        altField: '#cv-passport-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',
    });
    passport_date_picker.setDate(new Date(Math.floor(my_cv.cv_passport_date) * 1000));


    let workbook_date_picker = $('#cv-workbook-date').persianDatepicker({
        altField: '#cv-workbook-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',
    });
    workbook_date_picker.setDate(new Date(Math.floor(my_cv.cv_workbook_date) * 1000));
    let driver_license_date_picker = $('#cv-driver-license-date').persianDatepicker({
        altField: '#cv-driver-license-date-ts',
        format: 'YYYY/MM/DD',
        altFormat: 'X',
        viewMode: 'year',
        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
        initialValue: false,
        initialValueType: 'gregorian',
    });
    driver_license_date_picker.setDate(new Date(Math.floor(my_cv.cv_driver_license_date) * 1000));

    ///

///-----------------------------------------------------------
    country.select2({
        placeholder: lang_vars.u_driver_country_placeholder,
        language: {
            noResults: function () {
                return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
            }
        },
        sorter: function (results) {
            var query = $('.select2-search__field').val().toLowerCase();
            return results.sort(function (a, b) {
                return a.text.toLowerCase().indexOf(query) -
                    b.text.toLowerCase().indexOf(query);
            });
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
    city.select2({
        placeholder: lang_vars.u_driver_city_placeholder,
        language: {
            noResults: function () {
                return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
            }
        },
        sorter: function (results) {
            var query = $('.select2-search__field').val().toLowerCase();
            return results.sort(function (a, b) {
                return a.text.toLowerCase().indexOf(query) -
                    b.text.toLowerCase().indexOf(query);
            });
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
    country.on('change', function () {
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
                        city.append(html);
                    } catch (e) {

                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                    }
                }
            });


        }
    });
    city.on('change', function () {
        $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
        if ($(this).val() == -1) {
            $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
        }
        console.log($(this).val())
    });
    const citiesParams = {
        action: 'get-cities',
        country: my_cv.country_id,
        city: 'city',
        type: 'ground'
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(citiesParams),
        success: function (response) {
            try {
                let html = '';
                const json = JSON.parse(response);
                json.response.forEach(function (item) {
                    let selected = (item.CityId == my_cv.city_id) ? 'selected' : '';
                    html += `
                        <option value="${item.CityId}" ${selected}>${item.CityName}   ${item.CityNameEN}</option>
                        `;
                });
                city.append(html);
            } catch (e) {

                sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
            }
        }
    });

///-----------------------------------------------------------


///----------------------------------------------------------- soldier click start


    $('#soldier-yes').click(function () {
        soldierSection.fadeIn(300)
        cv_military_status = 'yes';
    })
    $('#soldier-no').click(function () {
        soldierSection.fadeOut(300)
        cv_military_status = 'no';
    })
///----------------------------------------------------------- soldier click end


///----------------------------------------------------------- aiCardSection click start


    $('#ai-card-yes').click(function () {
        aiCardSection.fadeIn(300)
        cv_smartcard_status = 'yes';
    })
    $('#ai-card-no').click(function () {
        aiCardSection.fadeOut(300)
        cv_smartcard_status = 'no';
    })
///----------------------------------------------------------- aiCardSection click end


///----------------------------------------------------------- passportSection click start

    $('#passport-yes').click(function () {
        passportSection.fadeIn(300)
        cv_passport_status = 'yes'
    })
    $('#passport-no').click(function () {
        passportSection.fadeOut(300)
        cv_passport_status = 'no'
    })
///----------------------------------------------------------- passportSection click end


///----------------------------------------------------------- visaSection click start

    $('#visa-yes').click(function () {
        visaSection.fadeIn(300)
        cv_visa_status = 'yes'
    })
    $('#visa-no').click(function () {
        visaSection.fadeOut(300)
        cv_visa_status = 'no'
    })
///----------------------------------------------------------- visaSection click end


///----------------------------------------------------------- workbookSection click start

    $('#workbook-yes').click(function () {
        workbookSection.fadeIn(300)
        cv_workbook_status = 'yes'
    })
    $('#workbook-no').click(function () {
        workbookSection.fadeOut(300)
        cv_workbook_status = 'no'
    })
///----------------------------------------------------------- workbookSection click end


///----------------------------------------------------------- driverLicenseSection click start

    $('#drivelicense-yes').click(function () {
        driverLicenseSection.fadeIn(300)
        cv_driver_license_status = 'yes'
    })
    $('#drivelicense-no').click(function () {
        driverLicenseSection.fadeOut(300)
        cv_driver_license_status = 'no'
    })
///----------------------------------------------------------- driverLicenseSection click end

    function getFileNameFromUrl(url) {
        const parsedUrl = new URL(url);
        const pathname = parsedUrl.pathname;
        const pathSegments = pathname.split("/");
        const fileName = pathSegments[pathSegments.length - 1];
        return fileName;
    }



    $('#submit-driver').click(function () {


        if (city.val() == "" || city.val() == null) {
            sendNotice(lang_vars.alert_warning, lang_vars.u_drivers_cv_alert_city, 'warning', 2500);
            /**
             * alert_warning
             * alert_success
             * alert_error
             * alert_info*/

        } else {
            city_id = city.val();
        }

        cv_name = $('#firstname');
        if (cv_name.val().trim() == '' || cv_name.val().length < 2) {
            cv_name.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars['u_drivers_cv_alert_cv_name'], 'warning', 2500);
        }
        cv_lname = $('#lastname');
        if (cv_lname.val().trim() == '' || cv_lname.val().length < 2) {
            cv_lname.parent().addClass('border-danger');
            sendNotice(lang_vars.alert_warning, lang_vars['u_drivers_cv_alert_cv_lname'], 'warning', 2500);
        }


        cv_brith_date = $('#brithday-ts').val();

        if ($('#sex-type').val() != '') {
            cv_gender = $('#sex-type').val();
        }

        cv_marital_status = $('#marriage-select').val();


        if (cv_military_status === 'yes') {
            cv_military_number = $('#cv-military-number');
            /* if (cv_military_number.val().trim() === '' || cv_military_number.val().length < 2) {
                 sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
             }*/
            cv_military_number = $('#cv-military-number').val();
            cv_military_date = $('#cv-military-date-ts').val();
        } else {
            cv_military_image = [];
            cv_military_number = null;
            cv_military_date = null;
        }

        if (cv_smartcard_status === 'yes') {
            cv_smartcard_number = $('#smart-card-number');
            /* if (cv_smartcard_number.val().trim() === '' || cv_smartcard_number.val().length < 2) {
                 sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
             }*/
            cv_smartcard_number = $('#smart-card-number').val();
            cv_smartcard_date = $('#cv-smart-card-date-ts').val();
        } else {
            cv_smartcard_image = [];
            cv_smartcard_number = null;
            cv_smartcard_date = null;
        }

        if (cv_passport_status === 'yes') {
            cv_passport_number = $('#passport-number');
            /* if (cv_passport_number.val().trim() === '' || cv_passport_number.val().length < 2) {
                 sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
             }*/
            cv_passport_number = $('#passport-number').val();
            cv_passport_date = $('#cv-passport-date-ts').val();
        } else {
            cv_passport_image = [];
            cv_passport_number = null;
            cv_passport_date = null;
        }

        if (cv_visa_status === 'yes') {
            cv_visa_number = $('#visa-number');
            /*   if (cv_visa_number.val().trim() === '' || cv_visa_number.val().length < 2) {
                   sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
               }*/
            cv_visa_number = $('#visa-number').val();
            cv_visa_date = $('#cv-visa-date-ts').val();
            cv_visa_location = $('#visa-location').val();
        } else {
            cv_visa_image = [];
            cv_visa_number = null;
            cv_visa_date = null;
            cv_visa_location = null;
        }

        if (cv_workbook_status === 'yes') {
            cv_workbook_number = $('#workbook-number');
            /*if (cv_workbook_number.val().trim() === '' || cv_workbook_number.val().length < 2) {
                sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
            }*/
            cv_workbook_number = $('#workbook-number').val();
            cv_workbook_date = $('#cv-workbook-date-ts').val();
        } else {
            cv_workbook_image = [];
            cv_workbook_number = null;
            cv_workbook_date = null;
        }

        if (cv_driver_license_status === 'yes') {
            cv_driver_license_number = $('#drivelicense-number');
            /*  if (cv_driver_license_number.val().trim() === '' || cv_driver_license_number.val().length < 2) {
                  sendNotice(lang_vars['alert_warning'], lang_vars['b_cargo_name_error'], 'warning', 2500);
              }*/
            cv_driver_license_number = $('#drivelicense-number').val();
            cv_driver_license_date = $('#cv-driver-license-date-ts').val();
        } else {
            cv_driver_license_image = [];
            cv_driver_license_number = null;
            cv_driver_license_date = null;
        }

        cv_mobile = $('#phonenumber');
        if (cv_mobile.val().trim() === '' || cv_mobile.val().length < 2) {
            sendNotice(lang_vars['alert_warning'], lang_vars['u_drivers_cv_alert_cv_mobile'], 'warning', 2500);
        }
        cv_whatsapp = $('#whatsappnumber');
        if (cv_whatsapp.val().trim() === '' || cv_whatsapp.val().length < 2) {
            sendNotice(lang_vars['alert_warning'], lang_vars['u_drivers_cv_alert_cv_whatsapp'], 'warning', 2500);
        }
        cv_address = $('#address');
        if (cv_address.val().trim() === '' || cv_address.val().length < 2) {
            sendNotice(lang_vars['alert_warning'], lang_vars['u_drivers_cv_alert_cv_address'], 'warning', 2500);
        }

        cv_faviroite_country = $('#fav-road-select').val();
        if ($('#contract').is(':checked')) {
            cv_role_status = 'yes'
        } else {
            cv_role_status = 'no'
        }

        if (city_id && cv_name && cv_lname && cv_brith_date && cv_gender && cv_marital_status
            && cv_military_status && cv_smartcard_status && cv_passport_status && cv_visa_status
            && cv_workbook_status && cv_driver_license_status && cv_mobile && cv_whatsapp
            && cv_address) {
            const nextBtn = document.getElementById("submit-driver")
            nextBtn.disabled = true;

            nextBtn.innerHTML = '<div id="spinner-icon" class=" me-1 fa-spinner fa-spin"></div> ' + lang_vars['u_driver_cv_submit']

            if (cv_user_avatar) {

            } else {

                cv_user_avatar = $('#imageUpload').attr('value')

            }
            // add spinner to button
            let params = {
                action: 'update-driver-cv',
                city_id: (city_id) ? city_id : null,
                cv_name: (cv_name.val()) ? cv_name.val() : null,
                cv_id: my_cv.cv_id,
                cv_lname: (cv_lname.val()) ? cv_lname.val() : null,
                cv_user_avatar: cv_user_avatar,
                cv_brith_date: cv_brith_date,
                cv_gender: (cv_gender) ? cv_gender : null,
                cv_marital_status: (cv_marital_status) ? cv_marital_status : null,
                cv_military_status: cv_military_status,
                cv_military_image: (cv_military_image.length > 0) ? cv_military_image : null,
                cv_military_number: (cv_military_number) ? cv_military_number : null,
                cv_military_date: (cv_military_date) ? cv_military_date : null,
                cv_smartcard_status: cv_smartcard_status,
                cv_smartcard_image: (cv_smartcard_image.length > 0) ? cv_smartcard_image : null,
                cv_smartcard_number: (cv_smartcard_number) ? cv_smartcard_number : null,
                cv_smartcard_date: (cv_smartcard_date) ? cv_smartcard_date : null,
                cv_passport_status: cv_passport_status,
                cv_passport_image: (cv_passport_image.length > 0) ? cv_passport_image : null,
                cv_passport_number: (cv_passport_number) ? cv_passport_number : null,
                cv_passport_date: (cv_passport_date) ? cv_passport_date : null,
                cv_visa_status: cv_visa_status,
                cv_visa_image: (cv_visa_image.length > 0) ? cv_visa_image : null,
                cv_visa_number: (cv_visa_number) ? cv_visa_number : null,
                cv_visa_date: (cv_visa_date) ? cv_visa_date : null,
                cv_visa_location: (cv_visa_location) ? cv_visa_location : null,
                cv_workbook_status: cv_workbook_status,
                cv_workbook_image: (cv_workbook_image.length > 0) ? cv_workbook_image : null,
                cv_workbook_number: (cv_workbook_number) ? cv_workbook_number : null,
                cv_workbook_date: (cv_workbook_date) ? cv_workbook_date : null,
                cv_driver_license_status: cv_driver_license_status,
                cv_driver_license_image: (cv_driver_license_image.length > 0) ? cv_driver_license_image : null,
                cv_driver_license_number: (cv_driver_license_number) ? cv_driver_license_number : null,
                cv_driver_license_date: (cv_driver_license_date) ? cv_driver_license_date : null,
                cv_mobile: (cv_mobile.val()) ? cv_mobile.val() : null,
                cv_whatsapp: (cv_whatsapp.val()) ? cv_whatsapp.val() : null,
                cv_address: (cv_address.val()) ? cv_address.val() : null,
                cv_faviroite_country: (cv_faviroite_country.length > 0) ? cv_faviroite_country : null,
                cv_role_status: cv_role_status,
                token: $('#token').val().trim(),
            };
            console.log(params)
            $.ajax({
                url: '/api/ajax',
                type: 'POST',
                data: JSON.stringify(params),
                success: function (response) {
                    let result = JSON.parse(response)
                    if (result.status === 200) {
                        sendNotice(lang_vars['alert_success'], lang_vars['u_driver_cv_submit_success'], 'success', 2500);
                        setTimeout(function () {
                            // window.location.href = "/user/drivers";
                        }, 3000)
                    } else {
                        sendNotice(lang_vars['alert_warning'], lang_vars['u_driver_cv_submit_error'], 'warning', 2500);

                    }

                    nextBtn.disabled = false;
                    nextBtn.innerHTML = lang_vars['u_driver_cv_submit']

                }
            })
        } else {
            sendNotice(lang_vars['alert_warning'], lang_vars['u_drivers_cv_alert_cv_info'], 'warning', 2500);
        }
    })

})
