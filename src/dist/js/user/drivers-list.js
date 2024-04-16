


$(document).ready(function () {
    var page = 1;
    var loading = false;
    let cargoOriginCountry2 = $('#cargo-origin-country2');
    let driverVisaLocation = $('#driver-visa-location');
    let not_fount = '<lottie-player src="/dist/lottie/emptycargo.json" background="transparent" speed="1" loop\n' +
        '                           autoplay></lottie-player>';
    let status = 'all';
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
    driverVisaLocation.select2({
        dropdownParent: $('.driver-visa-location'), language: {
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

    $(".mj-search-btn").click(function () {
        $(".mj-trx-serach-form").toggleClass("searchopen")
    });
    $(".mj-filter-btn").click(function () {
        $(".mj-filter-dropdown").toggleClass("filteropen")
    });
    $("#show-balance-more").click(function () {
        if ($(this).attr('src') == '/dist/images/wallet/down-arrow.svg') {
            $(".mj-wallet-rial-balance-withdraw ").addClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/up-arrow.svg')
        } else {
            $(".mj-wallet-rial-balance-withdraw ").removeClass("balanceopen")
            $(this).attr('src', '/dist/images/wallet/down-arrow.svg')
        }
    });
    $(document).on('click', '.mj-driver-card-title', function () {
        $(this).parent().children('.mj-accounts-operations').toggleClass('deleteacntshowed')
        $(this).toggleClass('opened')
        $(this).parent().children('.mj-driver-subdetail').toggleClass('showed')
    })
    delay(getData())

    // $('#tx-search').keyup(function () {
    //     page = 1;
    //     $('.mj-accounts-list').html('');
    //     getData();
    // });

    $(window).scroll((function () {

        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            if (!loading) {
                loading = true;
                getData();
            }
        }
    }));


    $('#submit-filter').click(function () {
        page = 1;
        status = $('#active').prop('checked') ? 'active' :'inactive';
        (getData())
        $('#staticBackdrop').modal('hide');
    })

    function getData() {
        $('.mj-accounts-list').html('');
        let search_text = 'no-search-data';
        if ($('#tx-search').val()) {
            search_text = $('#tx-search').val();
        }
        let params = {
            action: 'get-drivers-cv-lists',
            countries: cargoOriginCountry2.val(),
            visa_locations: driverVisaLocation.val(),
            status:status,
            page: page,
            search_text: search_text,
        };
        console.log(params)

        $('.mj-trx-list-load').removeClass('d-none')
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            async: false,
            data: JSON.stringify(params),
            success: function (response) {
                console.log(response )
                if (response == '' && $('.mj-accounts-list').text().trim() == '') {
                    $('.mj-accounts-list').append(not_fount);
                    $('.mj-trx-list-load').addClass('d-none')
                } else {
                    if ($('.mj-accounts-list').text() == not_fount) {

                    } else {
                        $('.mj-accounts-list').append(response);
                    }
                    loading = false;
                    $('.mj-trx-list-load').addClass('d-none')

                }
            },
            error: function () {
                loading = false;
            }
        });
        page++;

    }

});

