let user_types,
    user_name = $('#user_name'),
    user_lname = $('#user_lname'),
    user_phone = $('#user_phone'),
    user_home_number = $('#user_home_number'),
    company_name = $('#company_name'),
    member_zone = $('#detail-member-zone'),
    member_status = $('#detail-member-status'),
    car_types = $('#detail-member-car-type'),
    member_types = $('#detail-member-type'),
    fav_countries = $('#detail-member-fav-conts'),
    activity_summery = $('#activity_summery'),
    country_code2 = $('#country-code-2'),
    country_code = $('#country-code'),
    token = $('#token')
$(document).ready(function () {
    member_status.select2({
        placeholder: lang_vars.pb_user_access,
        minimumResultsForSearch: -1
    });
    member_types.select2({
        placeholder: lang_vars.pb_user_type,
        minimumResultsForSearch: -1
    });
    country_code.select2({
        placeholder: lang_vars.pb_user_type,
        minimumResultsForSearch: 0
    });
    country_code2.select2({
        placeholder: lang_vars.pb_user_type,
        minimumResultsForSearch: 0
    });
    car_types.select2({
        placeholder: lang_vars.pb_car_types,
        maximumSelectionLength: 50,
        language: {
            maximumSelected: function (e) {
                return lang_vars.cv_fav_car_types_count.replace('##', e.maximum);
            }
        }
    });
    member_zone.select2({
        placeholder: lang_vars.pb_cargointernal_external,
        minimumResultsForSearch: -1
    });
    fav_countries.select2({
        placeholder: lang_vars.pb_driver_road,
        maximumSelectionLength: 50,
        language: {
            maximumSelected: function (e) {
                return lang_vars.cv_fav_country_count.replace('##', e.maximum);
            }
        }
    });


    $('.mj-detail-submit-btn').click(function () {
        if (user_phone.val() && user_phone.val().length > 8) {
            let temp_home = ''
            if (user_home_number.val() && user_home_number.val().length > 8) {
                temp_home = country_code2.val().replace('+', '') + user_home_number.val()
            }
            let params = {
                action: 'add-phone-book',
                user_types: member_types.val(),
                user_name: user_name.val(),
                user_lname: user_lname.val(),
                user_phone: country_code.val().replace('+', '') + user_phone.val(),
                user_home_number: temp_home,
                company_name: company_name.val(),
                member_zone: member_zone.val(),
                member_status: member_status.val(),
                car_types: car_types.val(),
                fav_countries: fav_countries.val(),
                activity_summery: activity_summery.val(),
                token: token.val()
            }

            sendAjaxRequest('POST', '/api/adminAjax', params, true,
                lang_vars.sweet_alert_success_title, 'success', lang_vars.sweet_alert_success_text, lang_vars.sweet_alert_success_btn_text)
                .then(response => {
                    let result = JSON.parse(response);
                    if (result.status == 200) {
                        window.location.href = '/admin/pbookedit/' + result.response

                    } else if (result.status == 201) {
                        Swal.fire({
                            title: lang_vars.sweet_alert_error_title,
                            text: lang_vars.sweet_alert_error_text_reuire_field_user_exists,
                            icon: 'error',
                            confirmButtonText: lang_vars.sweet_alert_error_btn_text_user_exists
                        })
                        console.log('test11')
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        } else {
            Swal.fire({
                title: lang_vars.sweet_alert_error_title,
                text: lang_vars.sweet_alert_error_text_reuire_field,
                icon: 'error',
                confirmButtonText: lang_vars.sweet_alert_error_btn_text
            })
        }


    })


});