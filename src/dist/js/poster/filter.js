const cargoOriginCountry = $('#cargo-origin-country');
const cargoOrigin = $('#cargo-origin');

const CURRENT_FILTER_KEY = "poster:currentFilter";

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
            action: 'get-poster-cities',
            country: $(this).val().trim(),
            city: 'city',
            type: 'poster'
        };
        $('#cargo-origin').html('');
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(citiesParams),
            success: function (response) {
                try {
                    let html = '<option value="all-city">' + lang_vars.b_filter_by_all + '</option>';
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


let poster_category = 'truck';
let trailer_types = 'all-type';
let trailer_name = '';
let gear_boxes = [];
let gear_boxes_name = [];
let fuels = [];
let fuels_name = [];
let brands = [];
let brands_name = [];
let from_year = '1350'
let to_year = '1403'
let worked_km_from = 0
let worked_km_to = 1000000
let min_price = 0
let max_price = 100000000000
let installments = true
let leasing = true
let cash = true
let properties = []
let properties_name = [];
let city = 'all-city'
let country = 'all-country'
let current_filter;

$(document).on('click', '#selectcityicon', function () {
    $('.mj-city-select').removeClass('active')
    $('#selectcityicon').addClass('fa-caret-left')
    $('#selectcityicon').removeClass('fa-close')
    $('#select-country-city-title').text(lang_vars.u_poster_filter_select_city)
})

$(document).ready(function () {

})
$(document).on('click', '#miladi', function () {

    let miladi = ' <div class="mj-filter-title mb-2">\n' +
        '                                                ' + lang_vars.u_filter_built_year + ' : <span style="font-size: 11px;font-weight: 300">(' + lang_vars.u_filter_miladi + ')</span>\n' +
        '                                            </div>\n' +
        '                                            <div class="range-result mb-3">\n' +
        '                                                <div class="range-from">\n' +
        '                                                    <span>' + lang_vars.u_from + '</span>\n' +
        '                                                    <span id="rangefrom">1975</span>\n' +
        '                                                </div>\n' +
        '                                                <div class="range-to">\n' +
        '                                                    <span>' + lang_vars.u_to + '</span>\n' +
        '                                                    <span id="rangeto">2024</span>\n' +
        '                                                </div>\n' +
        '                                            </div>\n' +
        '                                            <div class="slider">\n' +
        '                                                <div class="progress"></div>\n' +
        '                                            </div>\n' +
        '                                            <div dir="ltr" class="range-input">\n' +
        '                                                <input type="range" class="range-min" min="1975" max="2024"\n' +
        '                                                       value="2024">\n' +
        '                                                <input type="range" class="range-max" min="1975" max="2024"\n' +
        '                                                       value="2024">\n' +
        '                                            </div>'

    $('.mj-year-filter-range').html(miladi)
    init_range_input3()
})
$(document).on('click', '#shamsi', function () {

    let shamsi = '<div class="mj-filter-title mb-2">\n' +
        '                                                 ' + lang_vars.u_filter_built_year + ' : <span style="font-size: 11px;font-weight: 300">(' + lang_vars.u_filter_shamsi + ')</span>\n' +
        '                                            </div>\n' +
        '                                            <div class="range-result mb-3">\n' +
        '                                                <div class="range-from">\n' +
        '                                                    <span>' + lang_vars.u_from + '</span>\n' +
        '                                                    <span id="rangefrom">1350</span>\n' +
        '                                                </div>\n' +
        '                                                <div class="range-to">\n' +
        '                                                    <span>' + lang_vars.u_to + '</span>\n' +
        '                                                    <span id="rangeto">1403</span>\n' +
        '                                                </div>\n' +
        '                                            </div>\n' +
        '                                            <div class="slider">\n' +
        '                                                <div class="progress"></div>\n' +
        '                                            </div>\n' +
        '                                            <div dir="ltr" class="range-input">\n' +
        '                                                <input type="range" class="range-min" min="1350" max="1403"\n' +
        '                                                       value="1350">\n' +
        '                                                <input type="range" class="range-max" min="1350" max="1403"\n' +
        '                                                       value="1350">\n' +
        '                                            </div>\n' +
        '                                        </div>'
    $('.mj-year-filter-range').html(shamsi)
    init_range_input3()


})

$(document).on('click', '#trailer', function () {
    poster_category = 'trailer';
    get_properties_by_type('trailer')
    $('.mj-filter-gas-item').hide()
    $('.mj-filter-gear-item').hide()
    $('#devider1').hide()
    $('#mjgas').hide()
    $('#mjgear').hide()
    $('#selected-trailer-type').show()
    $('#filter-trailer-type').removeClass('d-none')
    $('.mj-trailer-type').removeClass('d-none')
    $('#mj-selected-brand-list').html('')
    brands = [];
    brands_name = [];
    get_brands_by_type('trailer');
})
$(document).on('click', '#truck', function () {
    poster_category = 'truck';
    get_properties_by_type('truck')
    $('.mj-filter-gas-item').show()
    $('.mj-filter-gear-item').show()
    $('#devider1').show()
    $('#mjgas').show()
    $('#mjgear').show()
    $('#selected-trailer-type').hide()
    $('#filter-trailer-type').addClass('d-none')
    $('.mj-trailer-type').addClass('d-none')
    $('#mj-selected-brand-list').html('')
    brands = [];
    brands_name = [];
    get_brands_by_type('truck');
})

let temp_trailer_type = '';
$(document).on('click', '.mj-trailer-item', function () {
    let typeName = $(this).data('name')

    var itemSelect = `  <div class="mj-filter-selected-type">
                               <div class="mj-filter-selected-type-name"> ${typeName} </div>
          <div class="fa-close mj-type-close"></div>
            </div>`;
    document.getElementById('selected-trailer-type').innerHTML = itemSelect;

    temp_trailer_type = $('.mj-trailer-type').html()
    $('.mj-trailer-type').html("")

    trailer_types = $(this).data('type-id')
    trailer_name = typeName;

    /*$('.mj-trailer-subtype').html(' <div class="mj-trailer-subitem" data-subtype-id="1" data-subname="13 فوت">\n' +
        '                                <span>13 فوت</span>\n' +
        '                            </div>')*/
})


$(document).on('click', '.mj-type-close', function () {
    $('.mj-trailer-type').html(temp_trailer_type)

    $('.mj-filter-selected-type').hide()
    $('.mj-filter-selected-subtype').hide()
    $('.mj-trailer-subtype').html("")
    trailer_types = 'all-type'
})


$(document).on('click', '.mj-trailer-subitem', function () {
    let typeName = $(this).data('subname')

    var itemSelect = `  <div class="mj-filter-selected-subtype" style="width: 90%;">
                               <div class="mj-filter-selected-type-name"> ${typeName} </div>
          <div class="fa-close mj-subtype-close"></div>
            </div>`;
    document.getElementById('selected-trailer-subtype').innerHTML = itemSelect;
    $('.mj-trailer-subtype').html('')
})

// todo sub types
$(document).on('click', '.mj-subtype-close', function () {
    $('.mj-trailer-subtype').html(' <div class="mj-trailer-subitem" data-subtype-id="1" data-subname="13 فوت">\n' +
        '                                <span>13 فوت</span>\n' +
        '                            </div>')
    $('.mj-filter-selected-subtype').hide()
})


$(document).on('click', '.mj-filter-brand-item-input', function () {
    let brandName = $(this).data('brand')
    var brandImage = $(this).next(".mj-filter-brand-item").children('.mj-brand-image').prop('src')
    let brandNumber = $(this).data('id')


    var brandSelect = `<div class="mj-selected-brand" data-id='${brandNumber}' data-name="${brandName}">
                                <div class="mj-selected-brand-value">
                                    <img src=${brandImage} alt="brand-logo">
                                    <span>${brandName}</span>
                                </div>
                                <div class="fa-close mj-brand-selected-close"></div>
                            </div>`;
    if ($(this).is(':checked')) {
        document.getElementById('mj-selected-brand-list').insertAdjacentHTML('beforeend', brandSelect)
    } else {
        let selectbrandname = $('.mj-selected-brand[data-id=' + brandNumber + ']').children($('.mj-selected-brand-value')).children($('span')).text().trim()
        if (selectbrandname === brandName) {
            $('.mj-selected-brand[data-id=' + brandNumber + ']').remove()
        }
    }

    brands = [];
    brands_name = [];
    $("body").find('.mj-selected-brand').each(function () {
        let template = $(this).data('id');
        let template_name = $(this).data('name');
        brands.push(template);
        brands_name.push(template_name);
    });
    console.log(brands)

})

$(document).on('click', '.mj-brand-selected-close', function () {
    let brandId = $(this).parent().data('id')
    console.log(brandId)
    $('.mj-filter-brand-item-input[data-id=' + brandId + ']').prop("checked", false);
    $(this).parent().remove()
})


$('.mj-filter-gear-item').click(function () {
    gear_boxes = [];
    gear_boxes_name = [];
    $(this).toggleClass('active')
    $("body").find('.mj-filter-gear-item.active').each(function () {
        let template = $(this).data('gear-box-id');
        let template_name = $(this).data('gear-box-name');
        gear_boxes.push(template);
        gear_boxes_name.push(template_name);
    });
    console.log(gear_boxes)
});

$('.mj-filter-gas-item').click(function () {
    fuels = [];
    $(this).toggleClass('active')
    $("body").find('.mj-filter-gas-item.active').each(function () {
        let template = $(this).data('fuel-id');
        let template_name = $(this).data('fuel-name');
        fuels.push(template);
        fuels_name.push(template_name);
    });
    console.log(fuels)
});

$(document).on('click', '.mj-filter-option-item', function () {
    properties = [];
    properties_name = []
    $(this).toggleClass('active')
    $("body").find('.mj-filter-option-item.active').each(function () {
        let template = $(this).data('id');
        let template_name = $(this).data('name');
        properties.push(template);
        properties.push(template_name);
    });
    console.log(properties)
});


get_properties_by_type('truck')

function get_properties_by_type(type) {
    $('.mj-filter-option-list').html('')
    const params = {
        action: 'get-property-poster',
        type: type,
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            let result = JSON.parse(response);
            let output = '';
            result.forEach((item, index, array) => {
                output += ' <label >\n' +
                    '                                                <input type="checkbox">\n' +
                    '                                                <div class="mj-filter-option-item" data-id="' + item.id + '" data-name="' + item.name + '">\n' +
                    '                                                    <div class="mj-filter-option-img active">\n' +
                    '                                                        <img src="' + item.image + '" alt="option">\n' +
                    '                                                    </div>\n' +
                    '                                                    <span>' + item.name + '</span>\n' +
                    '                                                </div>\n' +
                    '                                            </label>'
            })

            $('.mj-filter-option-list').html(output)
        }
    });
}


$('#save-filter').click(function () {

    save_filter();
    get_filters(get_data_from_html())
    $('#exampleModal').modal('hide')

})

async function save_filter() {
    let my_filters = [];
    let current_count = 0;
    if (localStorage.getItem('my-filters')) {
        my_filters = await JSON.parse(localStorage.getItem('my-filters'))
        current_count = my_filters.length
        console.log(current_count)
        console.log(my_filters)
    }

    let {page, ...filter_object} = get_data_from_html()
    localStorage.setItem(CURRENT_FILTER_KEY, JSON.stringify(filter_object))
    my_filters.push(filter_object)

    localStorage.setItem('my-filters', JSON.stringify(my_filters))

}
$('.mj-refreshh-filter-btn').click(function () {
    window.location.reload() ;
})

async function load_filter() {
    // mj-saved-filters
    let my_filters = [];
    let current_count = 0;
    if (localStorage.getItem('my-filters')) {
        my_filters = await JSON.parse(localStorage.getItem('my-filters'))
        current_count = my_filters.length
        console.log(current_count)
        console.log(my_filters)
    }
    $('.mj-saved-filters').html('');
    my_filters.forEach((item, index, array) => {
        // todo generate name with language
        let name = ' کشنده'
        if (item.poster_category == 'trailer') {
            name = ' تریلر'
        }

        if (item.fuels.includes(1)) {
            name += " بنزینی "
        }
        if (item.fuels.includes(2)) {
            name += " گازوئیلی "
        }
        if (item.gear_boxes.includes(1)) {
            name += " اتوماتیک "
        }
        if (item.gear_boxes.includes(2)) {
            name += " دنده ای "
        }
        name += " ...";
        $('.mj-saved-filters').html($('.mj-saved-filters').html() + '<div class="mj-saved-filter-item">\n' +
            '                                <div class="mj-s-item-badge"></div>\n' +
            '                                <div class="mj-saved-filter-title">\n' +
            '                                    ' + name + '\n' +
            '                                </div>\n' +
            '                                <div class="mj-s-item-check" data-id="' + index + '" >\n' +
            '                                    <img src="/dist/images/poster/check(sfilter).svg" alt="import">\n' +
            '                                </div>\n' +
            '\n' +
            '                                <button class="mj-s-item-delete" data-id="' + index + '" data-bs-target="#exampleModalToggle2"\n' +
            '                                        data-bs-toggle="modal">\n' +
            '                                    <img src="/dist/images/poster/trash(filter).svg" alt="delete">\n' +
            '                                </button>\n' +
            '\n' +
            '                            </div>');
    })
}


$(document).on('click', '.mj-s-item-delete', async function () {
    $('.mj-second-modal-yes').attr('data-id', $(this).data('id'))
});
$(document).on('click', '.mj-second-modal-yes', async function () {
    let my_filters = [];
    let current_count = 0;
    if (localStorage.getItem('my-filters')) {
        my_filters = await JSON.parse(localStorage.getItem('my-filters'))
        current_count = my_filters.length
        console.log(current_count)
        console.log(my_filters)
    }

    let index = $(this).data('id')
    my_filters.splice(index, 1)
    console.log(index)

    await localStorage.setItem('my-filters', JSON.stringify(my_filters))

    await load_filter();
});

$('#my-filters').click(async function () {
    console.log('my-filters')
    await load_filter()
})

$('.mj-delete-save-filter').click(async function () {
    localStorage.setItem('my-filters', [])
    await load_filter()
})


$('.mj-view-filter-btn .mj-filter-search-btn-item').click(function () {
    let params = get_data_from_html();
    get_filters(params)
    let {page, ...filter_object} = params
    localStorage.setItem(CURRENT_FILTER_KEY, JSON.stringify(filter_object))

    $('#exampleModal').modal('hide')
})

function get_data_from_html() {
    let price_slider = $('.mj-price-filter-range .range-input input')
    min_price = price_slider[0].value
    max_price = price_slider[1].value
    let worked_km_slider = $('.mj-use-filter-range .range-input input')
    worked_km_from = worked_km_slider[0].value
    worked_km_to = worked_km_slider[1].value
    let date_slider = $('.mj-year-filter-range .range-input input')
    from_year = date_slider[0].value
    to_year = date_slider[1].value
    installments = $('#installments').is(':checked')
    leasing = $('#leasing').is(':checked')
    cash = $('#cash').is(':checked')


    brands = [];
    brands_name = [];
    $("body").find('.mj-selected-brand').each(function () {
        let template = $(this).data('id');
        let template_name = $(this).data('name');
        brands.push(template);
        brands_name.push(template_name);
    });
    gear_boxes = [];
    gear_boxes_name = [];
    $("body").find('.mj-filter-gear-item.active').each(function () {
        let template = $(this).data('gear-box-id');
        let template_name = $(this).data('gear-box-name');
        gear_boxes.push(template);
        gear_boxes_name.push(template_name);
    });

    fuels = [];
    fuels_name = [];
    $("body").find('.mj-filter-gas-item.active').each(function () {
        let template = $(this).data('fuel-id');
        let template_name = $(this).data('fuel-name');
        fuels.push(template);
        fuels_name.push(template_name);
    });

    properties = [];
    properties_name = []
    $(this).toggleClass('active')
    $("body").find('.mj-filter-option-item.active').each(function () {
        let template = $(this).data('id');
        let template_name = $(this).data('name');
        properties.push(template);
        properties_name.push(template_name)
    });

    let country_city_title = $('#select-country-city-title').text();
    if (country_city_title == lang_vars.u_poster_select_location_default) {
        country_city_title = lang_vars.u_poster_all_city;
    }
    let params = {
        "action": "get-poster-filters",
        "poster_category": poster_category,
        "trailer_types": trailer_types,
        "trailer_type_name": trailer_name,
        "gear_boxes": gear_boxes,
        "gear_boxes_name": gear_boxes_name,
        "fuels": fuels,
        "fuels_name": fuels_name,
        "brands": brands,
        "brands_name": brands_name,
        "from_year": from_year,
        "to_year": to_year,
        "worked_km_from": worked_km_from,
        "worked_km_to": worked_km_to,
        "min_price": min_price,
        "max_price": max_price,
        "installments": installments,
        "leasing": leasing,
        "cash": cash,
        "properties": properties,
        "properties_name": properties_name,
        "city": $('#cargo-origin').val(),
        "country_city_name": country_city_title,
        "country":  $('#cargo-origin-country').val(),
        "page":1
    }

    return params;
}

$(document).on('click', '.mj-s-item-check', async function () {
    let my_filters = [];
    if (localStorage.getItem('my-filters')) {
        my_filters = await JSON.parse(localStorage.getItem('my-filters'))
    }
    let id = $(this).data('id')
    current_filter = my_filters[id];
    // $('#current-filter').text($(this).parent().children('.mj-saved-filter-title').text().trim())
    $('#current-filter-container').removeClass('d-none')

    $('#exampleModalToggle').modal('hide')
    $('#exampleModal').modal('show')

    get_filters(current_filter)
});
$('#remove-cuurent-filter').click(function () {
    $('#current-filter-container').addClass('d-none')
    get_filters(get_data_from_html())
})
$('.remove-filter').click(function () {
    source_city = 'all-city';
    source_country = 'all-country';
    cargoOriginCountry.val("all-country").trigger("change");
    cargoOrigin.val("all-city").trigger("change");

    city = "all-city"
    country = 'all-country'
})


$('.close-city-modal').click(function () {
    $('#exampleModal4').modal('hide')
    $('#exampleModal').modal('show')
    $('#selectcityicon').addClass('fa-caret-left')
    $('#selectcityicon').removeClass('fa-close')
    $('.mj-city-select').removeClass('active')
})

$('.accept-city-modal').click(function () {
    $('#exampleModal4').modal('hide')
    $('#exampleModal').modal('show')
    $('#selectcityicon').removeClass('fa-caret-left')
    $('#selectcityicon').addClass('fa-close')
    $('.mj-city-select').addClass('active')
    let country_title = $('#select2-cargo-origin-country-container').attr('title')
    let city_title = $('#select2-cargo-origin-container').attr('title')
    $('#select-country-city-title').text((country_title ? country_title : lang_vars.d_filter_all) + " / " + (city_title ? city_title : lang_vars.d_filter_all))
})

function get_filters(params) {
    console.log("🚀 ~ get_filters ~ params:", JSON.stringify(params))
    $('.mj-poster-home-items').html('')
    console.log(params)
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),

        success: function (response) {
            console.log(JSON.parse(response).response)
            let result = JSON.parse(response);
            if (result.status == 200) {
                $('.mj-poster-home-items').html(result.response)
            } else {
                //todo  not found
                $('.mj-poster-home-items').html('<div class="mj-filter-empty"><lottie-player src="/dist/lottie/emptycargo.json" background="transparent" speed="1" loop\n' +
                    '                           autoplay></lottie-player></div>')
            }

        }
    });

}

$('#close-current-modal-detail').click(function () {
    $('#exampleModalcurrentfilter').modal('hide')
    $('#exampleModal').modal('show')
})
$('#detail-modal').click(function () {

    let output = '';
    if (current_filter.poster_category == 'truck') {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/T.svg" alt="T"></div><p>' + lang_vars.a_truck + '</p></div>';
        if (current_filter.brands_name) {
            brands_name.forEach((item, index, array) => {
                output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/star(blue).svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_brand + ' :</span> ' + item + '</p></div>';
            })
        }
        if (current_filter.fuels_name) {
            fuels_name.forEach((item, index, array) => {
                output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/gas-pump.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_fuel + ' :</span> ' + item + '</p></div>';
            })
        }
        if (current_filter.gear_boxes_name) {
            gear_boxes_name.forEach((item, index, array) => {
                output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/gear.png" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_gear_box + ' :</span> ' + item + '</p></div>';
            })
        }
    }
    if (current_filter.poster_category == 'trailer') {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/T.svg" alt="T"></div><p>' + lang_vars.a_trailer + '</p></div>';

        if (current_filter.trailer_type_name) {
            output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/brand(blue).svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_trailer_type + ' :</span> ' + current_filter.trailer_type_name + '</p></div>';
        }
    }
    if (current_filter.properties_name) {
        properties_name.forEach((item, index, array) => {
            output += '<p>' + item + '</p>';
        })
    }
    if (current_filter.cash == true) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/T.svg" alt="T"></div><p>' + lang_vars.u_poster_cash + '</p></div>';
    }
    if (current_filter.installments == true) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/T.svg" alt="T"></div><p>' + lang_vars.u_poster_installments + '</p></div>';
    }
    if (current_filter.leasing == true) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/T.svg" alt="T"></div><p>' + lang_vars.u_poster_leasing + '</p></div>';
    }
    if (current_filter.min_price) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/money-from.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_from_price + ' :</span> ' + current_filter.min_price + '</p></div>';
    }
    if (current_filter.max_price) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/money-to.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_to_price + ' :</span> ' + current_filter.max_price + '</p></div>';
    }
    if (current_filter.from_year) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/year-from.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_from_year + ' :</span> ' + current_filter.from_year + '</p></div>';
    }
    if (current_filter.to_year) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/year-to.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_to_year + ' :</span> ' + current_filter.to_year + '</p></div>';
    }
    if (current_filter.worked_km_from) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/use-from.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_from + ' :</span> ' + current_filter.worked_km_from + lang_vars.u_km + '</p></div>';
    }
    if (current_filter.worked_km_to) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/use-to.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_to + ' :</span> ' + current_filter.worked_km_to + lang_vars.u_km + '</p></div>';
    }
    if (current_filter.country_city_name) {
        output += '<div class="mj-current-filter-item"><div class="mj-my-filter-list-img"><img src="/dist/images/poster/city.svg" alt="T"></div><p>' + ' <span>' + lang_vars.u_poster_country_and_city + ' :</span> ' + current_filter.country_city_name + '</p></div>';
    }


    $('#my-filter-detail-container').html(output);

    $('#exampleModal').modal('hide')
    $('#exampleModalcurrentfilter').modal('show')
})

function initFilters() {
    const filter_object = {
        "action": "get-poster-filters",
        "poster_category": "both",
        "trailer_types": trailer_types,
        "trailer_type_name": trailer_name,
        "gear_boxes": gear_boxes,
        "gear_boxes_name": gear_boxes_name,
        "fuels": fuels,
        "fuels_name": fuels_name,
        "brands": brands,
        "brands_name": brands_name,
        "from_year": from_year,
        "to_year": to_year,
        "worked_km_from": worked_km_from,
        "worked_km_to": worked_km_to,
        "min_price": min_price,
        "max_price": max_price,
        "installments": installments,
        "leasing": leasing,
        "cash": leasing,
        "properties": properties,
        "properties_name": properties_name,
        "city": city,
        "country": country,
        "page": 1,
    }

    get_filters(filter_object);
    
    localStorage.setItem(CURRENT_FILTER_KEY, JSON.stringify(filter_object)) 
}
initFilters()

$('#save-filter').click(function () {

    save_filter();
    get_filters(get_data_from_html())
    $('#exampleModal').modal('hide')

})


$(document).on('click', '#mj-p-get-poster-filters_prev', async function () {
    const page = $(this).data("page")
    const currentFilterStr =  localStorage.getItem(CURRENT_FILTER_KEY)
    const currentFilter =  JSON.parse(currentFilterStr ?? "{}");

    get_filters({
        ...currentFilter,
        // "action": "get-poster-filters",
        // "poster_category": "both",
        // "trailer_types": trailer_types,
        // "trailer_type_name": trailer_name,
        // "gear_boxes": gear_boxes,
        // "gear_boxes_name": gear_boxes_name,
        // "fuels": fuels,
        // "fuels_name": fuels_name,
        // "brands": brands,
        // "brands_name": brands_name,
        // "from_year": from_year,
        // "to_year": to_year,
        // "worked_km_from": worked_km_from,
        // "worked_km_to": worked_km_to,
        // "min_price": min_price,
        // "max_price": max_price,
        // "installments": installments,
        // "leasing": leasing,
        // "cash": leasing,
        // "properties": properties,
        // "properties_name": properties_name,
        // "city": city,
        // "country": country,
        "page": page,
    });
});

$(document).on('click', '#mj-p-get-poster-filters_next', async function () {
    const page = $(this).data("page")
    const currentFilterStr =  localStorage.getItem(CURRENT_FILTER_KEY)
    const currentFilter =  JSON.parse(currentFilterStr ?? "{}");
    get_filters({
        ...currentFilter,
        // "action": "get-poster-filters",
        // "poster_category": "both",
        // "trailer_types": trailer_types,
        // "trailer_type_name": trailer_name,
        // "gear_boxes": gear_boxes,
        // "gear_boxes_name": gear_boxes_name,
        // "fuels": fuels,
        // "fuels_name": fuels_name,
        // "brands": brands,
        // "brands_name": brands_name,
        // "from_year": from_year,
        // "to_year": to_year,
        // "worked_km_from": worked_km_from,
        // "worked_km_to": worked_km_to,
        // "min_price": min_price,
        // "max_price": max_price,
        // "installments": installments,
        // "leasing": leasing,
        // "cash": leasing,
        // "properties": properties,
        // "properties_name": properties_name,
        // "city": city,
        // "country": country,
        "page": page,
    });
});

$('#get-poster-filters_next').click(function () {
    const nextPage = $("#get-poster-filters_next").data("go-to")

    get_filters({
        "action": "get-poster-filters",
        "poster_category": "both",
        "trailer_types": trailer_types,
        "trailer_type_name": trailer_name,
        "gear_boxes": gear_boxes,
        "gear_boxes_name": gear_boxes_name,
        "fuels": fuels,
        "fuels_name": fuels_name,
        "brands": brands,
        "brands_name": brands_name,
        "from_year": from_year,
        "to_year": to_year,
        "worked_km_from": worked_km_from,
        "worked_km_to": worked_km_to,
        "min_price": min_price,
        "max_price": max_price,
        "installments": installments,
        "leasing": leasing,
        "cash": leasing,
        "properties": properties,
        "properties_name": properties_name,
        "city": city,
        "country": country,
        // "limit": 2,
        // "offset": 0,
        "page": nextPage,

    });
})

get_brands_by_type('truck');


$(document).on('click', '.mj-p-poster-item-content', async function () {
    let ad_id = $(this).data('id');
    $('#poster-detail').attr('src', '/poster/detail/' + ad_id)
    await load_iframe();
});

function load_iframe() {
    document.getElementById('poster-detail').onload = function () {
        $('#exampleModaliframe').modal('show')
    };
}

$('#exampleModaliframe').on('shown.bs.modal', function (e) {
    window.location.hash = "detail";
});

$('#exampleModaliframe').on('hidden.bs.modal', '.mj-p-poster-item-content', function () {
    location.hash = ''
})

$(window).on('hashchange', function (event) {
    if (window.location.hash != "#detail") {
        $('#exampleModaliframe').modal('hide');
    }
});

function get_brands_by_type(type) {

    $('.mj-brands-filter-list').html('')
    const params = {
        action: 'get-brand-poster',
        type: type,
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            let result = JSON.parse(response);
            let output = '';
            result.forEach((item, index, array) => {
                output += '   <label>\n' +
                    '                                                    <input class="mj-filter-brand-item-input" type="checkbox"\n' +
                    '                                                           name="' + item.name + '"\n' +
                    '                                                           id="' + item.id + '"\n' +
                    '                                                           data-brand="' + item.name + '"\n' +
                    '                                                           data-id="' + item.id + '">\n' +
                    '                                                    <div class="mj-filter-brand-item">\n' +
                    '                                                        <img class="mj-brand-image"\n' +
                    '                                                             src="' + item.image + '"\n' +
                    '                                                             alt="brand-logo">\n' +
                    '                                                        <span> ' + item.name + '</span>\n' +
                    '                                                    </div>\n' +
                    '                                                </label>'
            })

            $('.mj-brands-filter-list').html(output)
        }
    });

}

$(document).on('keyup', '#filter-brand-search', function () {

    $('.mj-brands-filter-list').html('')

    const params = {
        action: 'get-brand-poster-search',
        type: poster_category,
        search_value: ($(this).val()) ? $(this).val() : 'all-brands'
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            let result = JSON.parse(response);
            let output = '';
            result.forEach((item, index, array) => {
                output += '   <label>\n' +
                    '                                                    <input class="mj-filter-brand-item-input" type="checkbox"\n' +
                    '                                                           name="' + item.name + '"\n' +
                    '                                                           id="' + item.id + '"\n' +
                    '                                                           data-brand="' + item.name + '"\n' +
                    '                                                           data-id="' + item.id + '">\n' +
                    '                                                    <div class="mj-filter-brand-item">\n' +
                    '                                                        <img class="mj-brand-image"\n' +
                    '                                                             src="' + item.image + '"\n' +
                    '                                                             alt="brand-logo">\n' +
                    '                                                        <span> ' + item.name + '</span>\n' +
                    '                                                    </div>\n' +
                    '                                                </label>'
            })

            $('.mj-brands-filter-list').html(output)
        }
    });
});
$(document).on('keyup', '#filter-option-search', function () {
    $('.mj-filter-option-list').html('')
    const params = {
        action: 'get-property-poster-search',
        type: poster_category,
        search_value: ($(this).val()) ? $(this).val() : 'all-properties'
    };
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            let result = JSON.parse(response);
            let output = '';
            result.forEach((item, index, array) => {
                output += ' <label >\n' +
                    '                                                <input type="checkbox">\n' +
                    '                                                <div class="mj-filter-option-item" data-id="' + item.id + '" data-name="' + item.name + '">\n' +
                    '                                                    <div class="mj-filter-option-img active">\n' +
                    '                                                        <img src="' + item.image + '" alt="option">\n' +
                    '                                                    </div>\n' +
                    '                                                    <span>' + item.name + '</span>\n' +
                    '                                                </div>\n' +
                    '                                            </label>'
            })

            $('.mj-filter-option-list').html(output)
        }
    });

});


/*tab next and back*/
$('.mj-next-tab').click(function () {
    console.log($(".mj-filter-tabs .active").data('tab-id'));
    let tab = $(".mj-filter-tabs .active").data('tab-id');
    if (tab == 1) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-profile"]').tab('show')
        if (!$('.mj-filter-footer-btns').hasClass('d-none')) {
            $('.mj-filter-footer-btns').addClass('d-none')
        }
        $('.mj-back-tab').removeClass('d-none')

    }
    if (tab == 2) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-contact"]').tab('show')
        if (!$('.mj-filter-footer-btns').hasClass('d-none')) {
            $('.mj-filter-footer-btns').addClass('d-none')
        }


    }
    if (tab == 3) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-4"]').tab('show')
        if (!$('.mj-next-tab').hasClass('d-none')) {
            $('.mj-next-tab').addClass('d-none')

        }
        $('.mj-view-filter-btn').removeClass('d-none')
    }

})

$('.mj-back-tab').click(function () {
    console.log($(".mj-filter-tabs .active").data('tab-id'));
    let tab = $(".mj-filter-tabs .active").data('tab-id');
    if (tab == 1) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-4"]').tab('show')
        $('.mj-filter-footer-btns').removeClass('d-none')

    }
    if (tab == 2) {
        $('.mj-filter-tabs .nav-link[ data-bs-target="#pills-home"]').tab('show')
        if (!$('.mj-filter-footer-btns').hasClass('d-none')) {
            $('.mj-filter-footer-btns').addClass('d-none')
            $('.mj-back-tab').addClass('d-none')
        }
        $('.mj-next-tab').removeClass('d-none')
        $('.mj-view-filter-btn').addClass('d-none')
    }
    if (tab == 3) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-profile"]').tab('show')
        if (!$('.mj-filter-footer-btns').hasClass('d-none')) {
            $('.mj-filter-footer-btns').addClass('d-none')

        }
        $('.mj-next-tab').removeClass('d-none')
        $('.mj-view-filter-btn').addClass('d-none')
    }
    if (tab == 4) {
        $('.mj-filter-tabs .nav-link[data-bs-target="#pills-contact"]').tab('show')
            if (!$('.mj-filter-footer-btns').hasClass('d-none')) {
            $('.mj-filter-footer-btns').addClass('d-none')

        }
        $('.mj-view-filter-btn').addClass('d-none')
        $('.mj-next-tab').removeClass('d-none')
    }

})

window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth'
});

