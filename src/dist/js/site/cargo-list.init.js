const cargoOriginCountry = $('#cargo-origin-country');
const cargoDestCountry = $('#cargo-dest-country');
const cargoOrigin = $('#cargo-origin');
const cargoDestination = $('#cargo-destination');
const cargoOriginCountry2 = $('#cargo-origin-country2');
const cargoDestCountry2 = $('#cargo-dest-country2');
const cargoOrigin2 = $('#cargo-origin2');
const cargoDestination2 = $('#cargo-destination2');
let source_city = 'all-city';
let dest_city = 'all-city';
let source_country = 'all-country';
let dest_country = 'all-country';
let car_type = [];
let flag = true;
let flag2 = true;
let page = 0;

$(document).ready(function () {

    $('input[name="filter-switch"]').on("change", function () {
        if ($(this).prop('checked')) {
            $(".mj-filter-bell-alert").addClass("active")
            submitRing('active')
        } else {
            $(".mj-filter-bell-alert").removeClass("active")
            submitRing('inactive')
        }
    });
})


$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 680) {
        get_cargo_list();
    }
});
get_cargo_list()

$('#submit-filter').click(async function () {
    $('#cargo-list').html('');
    await $('button[data-load-more]').attr('data-page', 0)
    get_cargo_list()
})

function get_cargo_list() {
    if (flag && flag2) {
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

        car_type = [];
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
            page: page,
        };

        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {

                _btn2.addClass('d-none')
                if (response != '') {
                    page++;
                    $('#cargo-list').append(response);
                } else {
                    flag2 = false;
                    _btn.fadeOut(300);
                }
                flag = true;
            }
        })
    }
}


/***
 * ring start
 */
// select country and phone
cargoOriginCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin-country'), language: {
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
})
cargoOriginCountry.on("select2:opening", function () {
    document.getElementById("filter-modal-body").scroll(0, findPosition(document.getElementById("cargofirstcountry")));

    function findPosition(obj) {
        var currenttop = 0;
        if (obj.offsetParent) {
            do {
                currenttop += obj.offsetTop;
            } while ((obj = obj.offsetParent));
            return [currenttop];
        }
    }
});
cargoDestCountry.on("select2:opening", function () {
    document.getElementById("filter-modal-body").scroll(0, findPosition(document.getElementById("cargodestcountry")));

    function findPosition(obj) {
        var currenttop = 0;
        if (obj.offsetParent) {
            do {
                currenttop += obj.offsetTop;
            } while ((obj = obj.offsetParent));
            return [currenttop];
        }
    }
});
cargoOrigin.on("select2:opening", function () {
    document.getElementById("filter-modal-body").scroll(0, findPosition(document.getElementById("cargoOrigin")));

    function findPosition(obj) {
        var currenttop = 0;
        if (obj.offsetParent) {
            do {
                currenttop += obj.offsetTop;
            } while ((obj = obj.offsetParent));
            return [currenttop];
        }
    }
});
cargoDestination.on("select2:opening", function () {
    document.getElementById("filter-modal-body").scroll(0, findPosition(document.getElementById("cargoDestination")));

    function findPosition(obj) {
        var currenttop = 0;
        if (obj.offsetParent) {
            do {
                currenttop += obj.offsetTop;
            } while ((obj = obj.offsetParent));
            return [currenttop];
        }
    }
});

// cargoOrigin.on("select2:opening", function (e) {
//     $('.mj-filter-modal-content2 .modal-content').animate({
//         scrollTop: $(this).offset().top - 20
//     }, 1500)
//
// });

cargoOriginCountry.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities', country: $(this).val().trim(), city: 'city', type: 'ground'
        };
        $('#cargo-origin').html('');
        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(citiesParams), success: function (response) {
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

cargoDestCountry.select2({
    dropdownParent: $('.mj-custom-select.cargo-dest-country'), language: {
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
cargoDestCountry.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities', country: $(this).val().trim(), city: 'city', type: 'ground'
        };
        $('#cargo-destination').html('');

        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(citiesParams), success: function (response) {
                try {
                    let html = '<option value="all-city">' + lang_vars.b_filter_by_all + '</option>';
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
    dropdownParent: $('.mj-custom-select.cargo-origin'), language: {
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
cargoOrigin.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


cargoDestination.select2({
    dropdownParent: $('.mj-custom-select.cargo-destination'), language: {
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

    $("#car_type_area input").prop("checked", false);


    get_cargo_list();
})


$(document).on("click", ".mj-d-cargo-item-link2", function () {
    $('.mj-cargo-owner-modal-info').modal("show")
})


$('#cargo-id').keyup(function () {
    page = 0;
    $('#cargo-list').html('')
    if (!$(this).val()) {
        get_cargo_list()
    } else {
        const params = {
            action: 'load-cargo-with-id', cargo_id: $(this).val()
        };
        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
                if (response != '') {
                    $('#cargo-list').append(response);
                }
            }
        })
    }
})


// select country and phone
cargoOriginCountry2.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin-country2'), language: {
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
cargoOriginCountry2.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities-for-multi-contries', country: $(this).val(), city: 'city', type: 'ground'
        };
        $('#cargo-origin2').html('');
        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(citiesParams), success: function (response) {

                try {
                    let html = ' ';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        if (origin_val && item.CityId === origin_val) {
                            html += `
                        <option value="${item.CityId}" checked>${item.CityName}   ${item.CityNameEN}</option>
                        `;
                        } else {
                            html += `
                        <option value="${item.CityId}">${item.CityName}   ${item.CityNameEN}</option>
                        `;
                        }

                    });
                    cargoOrigin2.append(html);


                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }

                cargoOrigin2.val(origin_val).trigger('change')
            }

        });
    }
});

cargoDestCountry2.select2({
    dropdownParent: $('.mj-custom-select.cargo-dest-country2'), language: {
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
cargoDestCountry2.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == "") {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        const citiesParams = {
            action: 'get-cities-for-multi-contries', country: $(this).val(), city: 'city', type: 'ground'
        };
        $('#cargo-destination2').html('');

        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(citiesParams), success: function (response) {
                try {
                    let html = ' ';
                    const json = JSON.parse(response);
                    json.response.forEach(function (item) {
                        html += `
                        <option value="${item.CityId}">${item.CityName}  ${item.CityNameEN}</option>
                        `;
                    });
                    cargoDestination2.append(html);
                    if (dest_val) {
                        cargoDestination2.val(dest_val).trigger('change')
                    }
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 2500);
                }
                cargoDestination2.val(dest_val).trigger('change')
            }
        });
    }
});


cargoOrigin2.select2({
    dropdownParent: $('.mj-custom-select.cargo-origin2'), language: {
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
cargoOrigin2.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


cargoDestination2.select2({
    dropdownParent: $('.mj-custom-select.cargo-destination2'), language: {
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
})
cargoDestination2.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1) {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


let origin_val;
let dest_val;

function submitRing(status) {
    let source_city, dest_city, source_country, dest_country;
    if (cargoOrigin2.val().length !=0) {
        source_city = cargoOrigin2.val();
    }else {
        sendNotice(lang_vars.warning, lang_vars.ring_empty_city_alert, 'warning', 2500)
        return;
    }
    if (cargoDestination2.val().length !=0) {
        dest_city = cargoDestination2.val();
    }else {
        sendNotice(lang_vars.warning, lang_vars.ring_empty_city_alert, 'warning', 2500)
        return;
    }
    if (cargoOriginCountry2.val().length !=0) {
        source_country = cargoOriginCountry2.val();
    }else {
        sendNotice(lang_vars.warning, lang_vars.ring_empty_country_alert, 'warning', 2500)
        return;
    }
    if (cargoDestCountry2.val().length !=0) {
        dest_country = cargoDestCountry2.val();
    }else {
        sendNotice(lang_vars.warning, lang_vars.ring_empty_country_alert, 'warning', 2500)
        return;
    }

    let car_type = [];
    $('#car_type_area2 input:checked').each(function () {
        car_type.push($(this).val());
    });

    if (typeof car_type !== 'undefined' && car_type.length > 0  ) {
        const params = {
            action: 'submit-ring',
            source_country: source_country,
            dest_country: dest_country,
            source_city: source_city,
            dest_city: dest_city,
            car_type: car_type,
            status: status,
        };
        console.log(params)
        $.ajax({
            url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
                let result = JSON.parse(response)

                if (result.status == 200) {
                    if (status == 'inactive') {
                        sendNotice(lang_vars.u_cargo_ring_update_sucess_title, lang_vars.u_cargo_ring_update_sucess_desc, 'error', 2500)
                        $('input[name="filter-switch"]').prop('checked', true)
                        $(".mj-filter-bell-alert").addClass("active")
                    } else {
                        sendNotice(lang_vars.u_cargo_ring_update_sucess_title, lang_vars.u_cargo_ring_update_sucess_desc, 'success', 2500)
                        $('input[name="filter-switch"]').prop('checked', true)
                        $(".mj-filter-bell-alert").addClass("active")
                    }
                    $('#staticBackdrop2').modal('hide')
                } else if (result.status == 201) {
                    sendNotice(lang_vars.u_cargo_ring_insert_error_title, lang_vars.ring_user_not_loged_in, 'warning', 2500)
                    $('input[name="filter-switch"]').prop('checked', false)
                    $(".mj-filter-bell-alert").removeClass("active")
                } else {
                    sendNotice(lang_vars.u_cargo_ring_insert_error_title, lang_vars.u_cargo_ring_insert_error_desc, 'warning', 2500)
                    $('input[name="filter-switch"]').prop('checked', false)
                    $(".mj-filter-bell-alert").removeClass("active")
                }
                setTimeout(function () {
                    window.location.reload()
                } , 200)
            }
        })
    }
    else {
        sendNotice(lang_vars.warning, lang_vars.ring_empty_one_car_type, 'warning', 2500)
        $('input[name="filter-switch"]').prop('checked', false)
        $(".mj-filter-bell-alert").removeClass("active")
    }
}

$('#submit-bell').click(function () {
    submitRing('active');
    $('input[name="filter-switch"]').prop('checked', true)
    $(".mj-filter-bell-alert").addClass("active")
})
$('#remove-bell').click(function () {
    cargoOriginCountry2.val("").trigger("change");
    cargoDestCountry2.val("").trigger("change");
    cargoOrigin2.val("all-city").trigger("change");
    cargoDestination2.val("all-city").trigger("change");
    $("#car_type_area2 input").prop("checked", false);
})
$(document).on('click', 'a.mj-d-cargo-item-link', async function () {
    let cargo_id = $(this).attr('data-id');
    $('#cargo-detail').attr('src', '/driver/cargo/' + cargo_id)
    await load_iframe();
});

function load_iframe() {
    document.getElementById('cargo-detail').onload = function () {
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

// cargoOriginCountry.on("select2:opening" ,  function(e){
//     $('.mj-filter-modal-content2 .modal-content').animate({
//         scrollTop: cargoOriginCountry.offset().top },
//
//     500);
//
//
// })
// cargoOriginCountry.on("select2:opening" ,  function(e){
//     $('.mj-filter-modal-content2 .modal-content').animate({
//         scrollTop: cargoOriginCountry.offset().top },
//     'slow');
//     return false;
// })


