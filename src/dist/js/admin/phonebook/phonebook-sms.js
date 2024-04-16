member_status = $('#member-status-select')
member_road = $('#member-road-select')
member_car = $('#member-car-select')
member_inout = $('#member-inout-select')
member_type = $('#member-type-select')

member_status2 = $('#member-status-select2')
member_road2 = $('#member-road-select2')
member_car2 = $('#member-car-select2')
member_inout2 = $('#member-inout-select2')
member_type2 = $('#member-type-select2')

page = 1;
$(document).ready(function () {


    member_status.select2({
        placeholder: lang_vars.pb_user_access,
        minimumResultsForSearch: -1
    });
    member_type.select2({
        placeholder: lang_vars.pb_user_type,
        minimumResultsForSearch: -1
    });
    member_road.select2({
        placeholder: lang_vars.pb_fav_contries,
        minimumResultsForSearch: 0,
    });
    member_car.select2({
        placeholder: lang_vars.pb_car_types,
        minimumResultsForSearch: 0
    });
    member_inout.select2({
        placeholder: lang_vars.pb_cargointernal_external,
        minimumResultsForSearch: -1
    });
    member_status2.select2({
        placeholder: lang_vars.pb_user_access,
        minimumResultsForSearch: -1
    });
    member_type2.select2({
        placeholder: lang_vars.pb_user_type,
        minimumResultsForSearch: -1
    });
    member_road2.select2({
        placeholder: lang_vars.pb_fav_contries,
        minimumResultsForSearch: 0,
    });
    member_car2.select2({
        placeholder: lang_vars.pb_car_types,
        minimumResultsForSearch: 0
    });
    member_inout2.select2({
        placeholder: lang_vars.pb_cargointernal_external,
        minimumResultsForSearch: -1
    });

    $('#search-keys').keyup(delay(function () {
        let search_keys = persianToEnglishNumber($(this).val());
        let token = $('#token').val();
        page = 1
        getPhoneNumbers(search_keys, 'no')
    }, 500))

    getPhoneNumbers('', 'no')

    $(window).scroll(function () {

        if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
            let search_keys = persianToEnglishNumber($('#search-keys').val());
            page++;
            getPhoneNumbers(search_keys, 'yes');
        }
    });

})

$('.mj-pbook-apply-filter').click(function () {
    let search_keys = persianToEnglishNumber($('#search-keys').val());

    page = 1;
    getPhoneNumbers(search_keys, 'no')


})


// Attach a scroll event listener
function getPhoneNumbers(search_keys, loadmore) {
    let token = $('#token').val();
    let params = {
        action: 'get-phone-books',
        search_keys: (search_keys),
        status: member_status.val(),
        user_type: member_type.val(),
        country: member_road.val(),
        car_type: member_car.val(),
        cargo_type: member_inout.val(),
        page: page,
        token: token
    }
    console.log(params)
    sendAjaxRequest('POST', '/api/adminAjax', params, false)
        .then(response => {
            console.log(response);
            let result = JSON.parse(response)
            if (result.status == 200) {
                let contacts = result.response
                if (loadmore == 'yes') {
                    $('.mj-pbook-members-list').html($('.mj-pbook-members-list').html() + contacts)
                } else {
                    $('.mj-pbook-members-list').html(contacts)
                }

            }
        })
        .catch(error => {
            console.error(error);
        });
}


$('.mj-send-message-btn').click(function () {
    let search_keys = persianToEnglishNumber($('#search-keys').val());
    let token = $('#token').val();
    $('#sendsms-alert').modal('show')
    let params = {
        action: 'send-sms-phone-books',
        search_keys: (search_keys),
        status: member_status.val(),
        user_type: member_type.val(),
        country: member_road.val(),
        car_type: member_car.val(),
        cargo_type: member_inout.val(),
        sms_text: $('#pbook-message2').val(),
        token: token
    }
    console.log(params)
    sendAjaxRequest('POST', '/api/adminAjax', params, false)
        .then(response => {

            console.log(response);
            if (response.status == 200) {

            }
        })
        .catch(error => {
            console.error(error);
        });
})


$('.mj-pbook-message-draft-item').click(function () {
    $('#pbook-message2').val($(this).data('draft-text'))
})

