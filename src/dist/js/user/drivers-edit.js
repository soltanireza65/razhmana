let user_id;
let city_id;
let cv_name;
let cv_lname;
let cv_brith_date;
let cv_gender = 'mr';
let cv_marital_status = 'single';
let cv_military_status = 'no';
let cv_military_image = [];
let cv_military_number;
let cv_military_date;
let cv_smartcard_status = 'no';
let cv_smartcard_image = [];
let cv_smartcard_number;
let cv_smartcard_date;
let cv_passport_status = 'no';
let cv_passport_image = [];
let cv_passport_number;
let cv_passport_date;
let cv_visa_status = 'no';
let cv_visa_image = [];
let cv_visa_number;
let cv_visa_date;
let cv_visa_location;
let cv_workbook_status = 'no';
let cv_workbook_image = [];
let cv_workbook_number;
let cv_workbook_date;
let cv_driver_license_status = 'no';
let cv_driver_license_image = [];
let cv_driver_license_number;
let cv_driver_license_date;
let cv_mobile;
let cv_whatsapp;
let cv_address;
let cv_faviroite_country = [];
let cv_role_status = 'no';
let cv_user_avatar = null
let language = getCookie('language')
/**
 * select and dropzone area
 * */
let country = $('#country-select');
let city = $('#city-select');



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

$(document).ready(function () {
//---soldier input
    let soldierDZ = ''
    let aicardDZ = " "
    let passprtDZ = "  "
    let visaDZ = ""



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

    $('b[role="presentation"]').hide();
    $('.select2-selection__arrow').append('<span class="fa-caret-down" style="font-family: FontAwesome;color:#303030"></span>');


    $('#soldier-yes').click(function () {

        cv_military_status = 'yes';
        if ($('#soldier-yes').attr("data-state") == "true") {
            $('#soldier-yes').attr("data-state", "false")
            $('#soldier-input1').empty().slideDown(500).append(soldierDZ).find('#soldier-input').show('slow');
            $('#cv-military-date').persianDatepicker({
                altField: '#cv-military-date-ts',
                format: 'YYYY/MM/DD',
                altFormat: 'X',
                viewMode: 'year',
                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });
        }
        var dropzone = new Dropzone('#soldier-dz', {
            url: '/uploads',
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
            }


        });


        var minSteps = 6,
            maxSteps = 60,
            timeBetweenSteps = 100,
            bytesPerStep = 100000;

        dropzone.uploadFiles = function (files) {
            var self = this;

            for (var i = 0; i < files.length; i++) {

                var file = files[i];
                totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                for (var step = 0; step < totalSteps; step++) {
                    var duration = timeBetweenSteps * (step + 1);
                    setTimeout(function (file, totalSteps, step) {
                        return function () {
                            file.upload = {
                                progress: 100 * (step + 1) / totalSteps,
                                total: file.size,
                                bytesSent: (step + 1) * file.size / totalSteps
                            };

                            self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                            if (file.upload.progress == 100) {
                                file.status = Dropzone.SUCCESS;
                                self.emit("success", file, 'success', null);
                                self.emit("complete", file);
                                self.processQueue();
                            }
                        };
                    }(file, totalSteps, step), duration);
                }
            }
        }

    })

    $('#soldier-no').click(function () {
        cv_military_status = 'no';
        $('#soldier-yes').attr("data-state", "true")
        $('#soldier-input').hide('slow').empty();
    })


    $('#ai-card-yes').click(function () {
        cv_smartcard_status = 'yes';
        if ($('#ai-card-yes').attr("data-state") == "true") {
            $('#ai-card-yes').attr("data-state", "false")
            $('#ai-card-input1').empty().slideDown(500).append(aicardDZ).find('#ai-card-input').show('slow');

            $('#cv-smart-card-date').persianDatepicker({
                altField: '#cv-smart-card-date-ts',
                format: 'YYYY/MM/DD',
                altFormat: 'X',
                viewMode: 'year',
                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });

            var dropzone2 = new Dropzone('#aicard-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone2.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
        }


    })

    $('#ai-card-no').click(function () {
        cv_smartcard_status = 'no';
        $('#ai-card-yes').attr("data-state", true)
        $('#ai-card-input').hide('slow').empty();
    })


    $('#passport-yes').click(function () {
        cv_passport_status = 'yes'
        if ($('#passport-yes').attr("data-state") == "true") {
            $('#passport-yes').attr("data-state", "false")
            $('#passport-input1').empty().slideDown(500).append(passprtDZ).find('#passport-input').show('slow');

            $('#cv-passport-date').persianDatepicker({
                altField: '#cv-passport-date-ts',
                format: 'YYYY/MM/DD',
                 altFormat: 'X',
                viewMode: 'year',
                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });


            var dropzone3 = new Dropzone('#passport-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone3.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
        }


    })

    $('#passport-no').click(function () {
        cv_passport_status = 'no'
        $('#passport-yes').attr("data-state", true)
        $('#passport-input').hide('slow').empty();
    })


    $('#visa-yes').click(function () {



// select country and phone



        cv_visa_status = 'yes'
        if ($('#visa-yes').attr("data-state") == "true") {
            $('#visa-yes').attr("data-state", "false")
            $('#visa-input1').empty().slideDown(500).append(visaDZ).find('#visa-input').show('slow');

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
                        json.response.forEach(function (item) {
                            // let selected = (item.visa_id == my_cv.city_id) ? 'selected' : '';
                            let selected=''
                            html += `
                        <option value="${item.visa_id}" ${selected}>${item.visa_name_fa_IR}</option>
                        `;
                        });
                        visaLocation.html(html);
                    } catch (e) {

                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                    }
                }
            });

            $('#cv-visa-date').persianDatepicker({

                altField: '#cv-visa-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });




            var dropzone4 = new Dropzone('#visa-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone4.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }

        }


    })

    $('#visa-no').click(function () {
        cv_visa_status = 'no'
        $('#visa-yes').attr("data-state", true)
        $('#visa-input').hide('slow').empty();
    })


    $('#workbook-yes').click(function () {
        let workbookDZ = ""
        cv_workbook_status = 'yes'
        if ($('#workbook-yes').attr("data-state") == "true") {
            $('#workbook-yes').attr("data-state", "false")
            $('#workbook-input1').empty().slideDown(500).append(workbookDZ).find('#workbook-input').show('slow');

            $('#cv-workbook-date').persianDatepicker({

                altField: '#cv-military-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });
            var dropzone5 = new Dropzone('#workbook-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone5.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
        }


    })

    $('#workbook-no').click(function () {
        cv_workbook_status = 'no'
        $('#workbook-yes').attr("data-state", true)
        $('#workbook-input').hide('slow').empty();
    })


    $('#drivelicense-yes').click(function () {
        let drivelicenseDZ = ""
        cv_driver_license_status = 'yes'
        if ($('#drivelicense-yes').attr("data-state") == "true") {
            $('#drivelicense-yes').attr("data-state", "false")
            $('#drivelicense-input1').empty().slideDown(500).append(drivelicenseDZ).find('#drivelicense-input').show('slow');
            $('#cv-driver-license-date').persianDatepicker({

                altField: '#cv-driver-license-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });
            var dropzone6 = new Dropzone('#drivelicense-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone6.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
        }


    })

    $('#drivelicense-no').click(function () {
        cv_driver_license_status = 'no'
        $('#drivelicense-yes').attr("data-state", true)
        $('#drivelicense-input').hide('slow').empty();
    })


    // //---driver license input
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
    //todo implement for all work success remove comment
    let brithday_picker =  $('#brithday').persianDatepicker({
        format: 'YYYY/MM/DD',
        altField: '#brithday-ts',
        altFormat: 'X',
        viewMode: 'year',

        calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
    });
    // var initialTimestamp = Math.floor(Date.now() / 1000);
    // brithday_picker.setDate(new Date(1681926332 * 1000 ));

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


    if (my_cv.cv_military_status == 'yes') {
        let soldierDZ = " <div style='display: none' id=\"soldier-input\" >\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"cv-military-number\"\n" +
            "                                       placeholder=\"name@example.com\" value=\"" + my_cv.cv_military_number + "\"  >\n" +
            "                                <label class=\"mj-floating-labels\" for=\"cv-military-number\" >" + lang_vars.cv_military_number + "</label>\n" +
            "                            </div>\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-military-date\"\n" +
            "                                   name=\"cv-military-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\"  value=\"" + my_cv.cv_military_date + "\"><span>" + lang_vars.cv_millitry_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-military-date-ts\" name=\"cv-military-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"soldier-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_military_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"soldier-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>\n"

        cv_military_status = 'yes';
        if ($('#soldier-yes').attr("data-state") == "true") {
            $('#soldier-yes').attr("data-state", "false")
            $('#soldier-input1').empty().slideDown(500).append(soldierDZ).find('#soldier-input').show('slow');
            $('#cv-military-date').persianDatepicker({

                altField: '#cv-military-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });

            var dropzone = new Dropzone('#soldier-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }

            }


        }
    }

    if (my_cv.cv_smartcard_status == 'yes') {
        let aicardDZ = " <div style='display: none' id=\"ai-card-input\" >\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"smart-card-number\"\n" +
            "                                       placeholder=\"name@example.com\">\n" +
            "                                <label class=\"mj-floating-labels\" for=\"smart-card-number\">" + lang_vars.cv_aicard_number + "</label>\n" +
            "                            </div>\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-smart-card-date\"\n" +
            "                                   name=\"cv-smart-card-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\" value=\"\"><span>" + lang_vars.cv_expire_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-smart-card-date-ts\" name=\"cv-smart-card-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"aicard-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_aicard_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"aicard-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>"

        cv_smartcard_status = 'yes';
        if ($('#ai-card-yes').attr("data-state") == "true") {
            $('#ai-card-yes').attr("data-state", "false")
            $('#ai-card-input1').empty().slideDown(500).append(aicardDZ).find('#ai-card-input').show('slow');

            $('#cv-smart-card-date').persianDatepicker({

                altField: '#cv-smart-card-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });

            var dropzone2 = new Dropzone('#aicard-dz', {
                url: '/uploads',
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
                        console.log(file)
                        const index = cv_smartcard_image.indexOf(file);
                        if (index > -1) {
                            cv_smartcard_image.splice(index, 1);
                        }
                    });
                }
            });


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone2.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
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
        }
    }

    if (my_cv.cv_passport_status == 'yes') {
        let passprtDZ = "  <div style='display:none;' id=\"passport-input\" class=\"mj-cv-detail-input\">\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"passport-number\"\n" +
            "                                       placeholder=\"name@example.com\">\n" +
            "                                <label class=\"mj-floating-labels\" for=\"passport-number\">" + lang_vars.cv_passport_number + "</label>\n" +
            "                            </div>\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-passport-date\"\n" +
            "                                   name=\"cv-passport-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\" value=\"\"><span>" + lang_vars.cv_expire_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-passport-date-ts\" name=\"cv-passport-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"passport-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_passport_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"passport-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>"

        cv_passport_status = 'yes'
        if ($('#passport-yes').attr("data-state") == "true") {
            $('#passport-yes').attr("data-state", "false")
            $('#passport-input1').empty().slideDown(500).append(passprtDZ).find('#passport-input').show('slow');

            $('#cv-passport-date').persianDatepicker({

                altField: '#cv-passport-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });


            var dropzone3 = new Dropzone('#passport-dz', {
                url: '/uploads',
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

                    });
                }


            });


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone3.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
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
        }
    }

    if (my_cv.cv_visa_status == 'yes') {
        let visaDZ = "<div id=\"visa-input1\">\n" +
            "                        <div id=\"visa-input\" class=\"mj-cv-detail-input\">\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"visa-number\"\n" +
            "                                       placeholder=\"name@example.com\">\n" +
            "                                <label class=\"mj-floating-labels\" for=\"visa-number\">" + lang_vars.cv_visa_number + "</label>\n" +
            "                            </div>" +
            "                                       \n" +
            "    <div class=\"mj-custom-select mj-select2-selects \">\n" +
            "\n" +
            "\n" +
            "        <select class=\" width-95 my-1 mb-3\"\n" +
            "                id=\"visa-location\"\n" +
            "                name=\"visa-location\"\n" +
            "                data-width=\"100%\"\n" +
            "                multiple=\"multiple\"\n" +
            "                data-placeholder="+lang_vars.cv_visa_location+">\n" +
            "            <option value=\"1\">2</option>\n" +
            "            <option value=\"2\">2</option>\n" +
            "            <option value=\"3\">3</option>\n" +
            "            <option value=\"4\">3</option>\n" +
            "        </select>\n" +
            "\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-visa-date\"\n" +
            "                                   name=\"cv-visa-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\" value=\"\"><span>" + lang_vars.cv_expire_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-visa-date-ts\" name=\"cv-visa-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"visa-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_visa_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"visa-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>"

        cv_visa_status = 'yes'
        if ($('#visa-yes').attr("data-state") == "true") {
            $('#visa-yes').attr("data-state", "false")
            $('#visa-input1').empty().slideDown(500).append(visaDZ).find('#visa-input').show('slow');

            $('#cv-visa-date').persianDatepicker({

                altField: '#cv-visa-date-ts',
                format: 'YYYY/MM/DD',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
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
                        json.response.forEach(function (item) {
                            // let selected = (item.visa_id == my_cv.city_id) ? 'selected' : '';
                            let selected=''
                            html += `
                        <option value="${item.visa_id}" ${selected}>${item.visa_name_fa_IR}</option>
                        `;
                        });
                        visaLocation.html(html);
                    } catch (e) {

                        sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                    }
                }
            });
            visaLocation.select2(
                {
                    placeholder: function(){
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

            var dropzone4 = new Dropzone('#visa-dz', {
                url: '/uploads',
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



            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone4.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
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
        }


    }

    if (my_cv.cv_workbook_status == 'yes') {
        let workbookDZ = "<div id=\"workbook-input1\">\n" +
            "                        <div id=\"workbook-input\" class=\"mj-cv-detail-input\">\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"workbook-number\"\n" +
            "                                       placeholder=\"name@example.com\">\n" +
            "                                <label class=\"mj-floating-labels\" for=\"workbook-number\">" + lang_vars.cv_workbook_number + "</label>\n" +
            "                            </div>\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-workbook-date\"\n" +
            "                                   name=\"cv-workbook-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\" value=\"\"><span>" + lang_vars.cv_expire_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-workbook-date-ts\" name=\"cv-workbook-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"workbook-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_workbook_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"workbook-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>\n" +
            "\n" +
            "                    </div>"
        cv_workbook_status = 'yes'
        if ($('#workbook-yes').attr("data-state") == "true") {
            $('#workbook-yes').attr("data-state", "false")
            $('#workbook-input1').empty().slideDown(500).append(workbookDZ).find('#workbook-input').show('slow');

            $('#cv-workbook-date').persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: '#cv-military-date-ts',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });
            var dropzone5 = new Dropzone('#workbook-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone5.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
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
        }


    }

    if (my_cv.cv_driver_license_status == 'yes') {

        let drivelicenseDZ = "<div id=\"drivelicense-input1\">\n" +
            "                        <div id=\"drivelicense-input\" class=\"mj-cv-detail-input\">\n" +
            "                            <div class=\"mb-1 mt-3 col-12 no-pad-left form-floating \">\n" +
            "                                <input type=\"email\" class=\"form-control mj-cv-add-input\" id=\"drivelicense-number\"\n" +
            "                                       placeholder=\"name@example.com\">\n" +
            "                                <label class=\"mj-floating-labels\" for=\"drivelicense-number\">" + lang_vars.cv_driver_license_number + "</label>\n" +
            "                            </div>\n" +
            "                               <div class=\"form-floating mb-1 col-12 no-pad-left mj-select2-selects\">\n" +
            "                        <div  class=\"mj-input-filter-box mj-driver-add-date-picker\">\n" +
            "                            <input type=\"text\"\n" +
            "                                   id=\"cv-driver-license-date\"\n" +
            "                                   name=\"cv-driver-license-date\" readonly=\"readonly\" \n" +
            "                                   class=\"mj-input-filter mj-fw-400 mj-font-13 px-0\"\n" +
            "                                   style=\"min-height: 38px;\" value=\"\"><span>" + lang_vars.cv_expire_date + "</span>" +
            "                            <input type=\"hidden\" id=\"cv-driver-license-date-ts\" name=\"cv-driver-license-date-ts\">\n" +
            "                        </div>\n" +
            "                    </div>" +
            "\n" +
            "\n" +
            "                            <DIV id=\"dropzone\">\n" +
            "                                <FORM class=\"dropzone needsclick mj-add-dropzone\" id=\"drivelicense-dz\" action=\"/upload\">\n" +
            "                                    <DIV class=\"dz-message needsclick\">" + lang_vars.cv_driver_license_dropzone + "<div class=\"fa-plus mt-2\"></div>\n" +
            "                                        <div style=\"color: red\" id=\"drivelicense-error\">\n" +
            "                                        </div>\n" +
            "                                    </DIV>\n" +
            "\n" +
            "                                </FORM>\n" +
            "                            </DIV>\n" +
            "\n" +
            "                        </div>\n" +
            "                        <DIV id=\"preview-template\" style=\"display: none;\">\n" +
            "                            <DIV class=\"dz-preview dz-file-preview\">\n" +
            "                                <DIV class=\"dz-image\"><IMG data-dz-thumbnail=\"\"></DIV>\n" +
            "                                <DIV class=\"dz-details\"></DIV>\n" +
            "                                <DIV class=\"dz-progress\"><SPAN class=\"dz-upload\"\n" +
            "                                                               data-dz-uploadprogress=\"\"></SPAN></DIV>\n" +
            "                                <div class=\"dz-success-mark\">\n" +
            "                                    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\"\n" +
            "                                         xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n" +
            "                                        <title>Check</title>\n" +
            "                                        <desc>Created with Sketch.</desc>\n" +
            "                                        <defs></defs>\n" +
            "                                        <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n" +
            "                                            <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"\n" +
            "                                                  id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\"\n" +
            "                                                  fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n" +
            "                                        </g>\n" +
            "                                    </svg>\n" +
            "                                </div>\n" +
            "\n" +
            "                            </div>\n" +
            "                        </div>\n"
        cv_driver_license_status = 'yes'
        if ($('#drivelicense-yes').attr("data-state") == "true") {
            $('#drivelicense-yes').attr("data-state", "false")
            $('#drivelicense-input1').empty().slideDown(500).append(drivelicenseDZ).find('#drivelicense-input').show('slow');
            $('#cv-driver-license-date').persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: '#cv-driver-license-date-ts',

                altFormat: 'X',
                viewMode: 'year',

                calendarType: (language.substr(0, 2) === 'fa' ? 'persian' : 'gregorian'),
            });
            var dropzone6 = new Dropzone('#drivelicense-dz', {
                url: '/uploads',
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


            var minSteps = 6,
                maxSteps = 60,
                timeBetweenSteps = 100,
                bytesPerStep = 100000;

            dropzone6.uploadFiles = function (files) {
                var self = this;

                for (var i = 0; i < files.length; i++) {

                    var file = files[i];
                    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                    for (var step = 0; step < totalSteps; step++) {
                        var duration = timeBetweenSteps * (step + 1);
                        setTimeout(function (file, totalSteps, step) {
                            return function () {
                                file.upload = {
                                    progress: 100 * (step + 1) / totalSteps,
                                    total: file.size,
                                    bytesSent: (step + 1) * file.size / totalSteps
                                };

                                self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                                if (file.upload.progress == 100) {
                                    file.status = Dropzone.SUCCESS;
                                    self.emit("success", file, 'success', null);
                                    self.emit("complete", file);
                                    self.processQueue();
                                }
                            };
                        }(file, totalSteps, step), duration);
                    }
                }
            }
            let loop = JSON.parse(my_cv.cv_driver_license_image);
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
                        dropzone6.addFile(file);
                    }
                };
                xhr.send();
            }
        }


    }

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

    console.log(my_cv)
});

function getFileNameFromUrl(url) {
    const parsedUrl = new URL(url);
    const pathname = parsedUrl.pathname;
    const pathSegments = pathname.split("/");
    const fileName = pathSegments[pathSegments.length - 1];
    return fileName;
}

console.log(my_cv)


