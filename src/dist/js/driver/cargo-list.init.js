const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const cargoDestination = $('#cargo-destination');
let source_city = 'all-city';
let dest_city = 'all-city';
let source_country = 'all-country';
let dest_country = 'all-country';
let car_type = [];
let flag = true;
let flag2 = true;
let page = 0;
$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 680) {
        get_cargo_list();
    }
});
get_cargo_list()

$('#submit-filter').click(async function () {
    $('#cargo-list').html('');
    await $('button[data-load-more]').attr('data-page' , 0)
    get_cargo_list()
})

function get_cargo_list() {
    if (flag && flag2){
        flag = false;

    const _btn = $('button[data-load-more]');
    const _btn2 = $('.tj-a-loader');
    _btn2.removeClass('d-none')

    if (cargoOrigin.val()) {
        source_city = cargoOrigin.val();
    }
    if (cargoDestination.val()) {
        dest_city = cargoDestination.val();
    }
    if (cargoOriginCountry.val()) {
        source_country = cargoOriginCountry.val();
    }
    if (cargoDestCountry.val()) {
        dest_country = cargoDestCountry.val();
    }

    car_type=[];
    $('#car_type_area input:checked').each(function () {
        car_type.push($(this).val());
    });
    const params = {
        action: 'load-more-cargo',
        source_country: source_country,
        dest_country: dest_country,
        source_city: source_city,
        dest_city: dest_city,
        car_type: car_type,
        page: page ,
    };

    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {

            _btn2.addClass('d-none')
            if (response != '') {
                page++;
                $('#cargo-list').append(response);
            } else {
                flag2 =false;
                _btn.fadeOut(300);
            }
            flag =true ;
        }
    })
    }
}


// select country and phone
cargoOriginCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin-country'),
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
cargoOriginCountry.on('change', function () {
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
        $('#cargo-origin').html('');
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(citiesParams),
            success: function (response) {
                try {
                    let html = '<option value="all-city">'+lang_vars.b_filter_by_all+'</option>';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
                    });
                    cargoOrigin.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});

cargoDestCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-dest-country'),
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
cargoDestCountry.on('change', function () {
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
        $('#cargo-destination').html('');

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(citiesParams),
            success: function (response) {
                try {
                    let html = '<option value="all-city">'+lang_vars.b_filter_by_all+'</option>';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestination.append(html);
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
            }
        });
    }
});


cargoOrigin.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin'),
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
cargoOrigin.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


cargoDestination.select2({
    dropdownParent: $('.mj-custom-select.cargo-destination'),
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
})
cargoDestination.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});

$('#submit-filter').on('click', function () {
    page = 0;
    $('.mj-filters-btn-driver .fa-close').removeClass('d-none');
    $('.mj-filters-btn-driver').addClass('active');
    $('#staticBackdrop').modal('hide');
})

$('.mj-filters-btn-driver .fa-close').on('click', function () {
    $(this).addClass('d-none');
    $('.mj-filters-btn-driver').removeClass('active');
    $('.checkbox-type-car').each(function () {
        this.checked = false;
    });
})

$('.remove-filter').click(function () {
    source_city = 'all-city';
    dest_city = 'all-city';
    source_country = 'all-country';
    dest_country = 'all-country';
    car_type = [];
    flag = true;
    flag2 = true;

    cargoOriginCountry.val("all-country").trigger("change");
    cargoDestCountry.val("all-country").trigger("change");
    cargoOrigin.val("all-city").trigger("change");
    cargoDestination.val("all-city").trigger("change");

    $("#car_type_area input").prop( "checked", false );


    get_cargo_list();
})