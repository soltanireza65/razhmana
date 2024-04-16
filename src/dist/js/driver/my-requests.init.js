let page  =0;
$(".mj-search-btn").click(function () {
    $(".mj-trx-serach-form").toggleClass("searchopen")
    $(".mj-filter-dropdown").removeClass("filteropen")
});
$(".mj-filter-btn").click(function () {
    $(".mj-filter-dropdown").toggleClass("filteropen")
    $(".mj-trx-serach-form").removeClass("searchopen")
});
$(".mj-filter-close-btn").click(function () {
    $(".mj-filter-dropdown").removeClass('filteropen')
});
getDriverRequests(request_status, "all-requests");
$('.mj-filter-apply-my-cargoes').click(function () {
    let request_status = [];
    $('.mj-mycargo-items input:checked').each(function () {
        request_status.push($(this).val())
    })

    if (request_status.length ==0) {
        request_status = ['all'];
    }
    // console.log(request_status)
    page=0
    getDriverRequests(request_status, "all-requests")
    $(".mj-filter-dropdown").removeClass('filteropen')
    $('.mj-filter-cargoes-refresh').removeClass('d-none')
})
function getDriverRequests(request_status, search_value) {
    if (!search_value) {
        search_value = "all-requests";
    }
    if (page %10 ==0){
        if ( $('.mj-trx-item').length){
            page = $('.mj-trx-item').length -1;
        }else{
            page = 0;
        }
        let params = {
            action: 'get-driver-requests',
            request_status: request_status,
            search_value: search_value,
            page:page
        }
        // console.log(params)
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                $('.mj-transaction-items').html(response)
            }
        });
    }else{

    }
}
$('#tx-search').keyup(delay(function () {
    page=0
    getDriverRequests(request_status, $(this).val());
}));
function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}
let ajax_load_status = true;
$(async function () {
    $(document).on('scroll', async function () {
        if (ajax_load_status == true) {
            if ($(this).scrollTop() + $(window).height() >= $(document).height()) {
                ajax_load_status = false;
                if (request_status == []) {
                    request_status = ['all'];
                }else{
                    $('.mj-mycargo-items input:checked').each(function () {
                        request_status.push($(this).val())
                    })
                }
                await getDriverRequests(request_status, $('#tx-search').val())
                ajax_load_status = true;
            }
        }else{
            // console.log('is loading')
        }
    });
});
$('.mj-filter-cargoes-refresh').click(function () {
    window.location.reload()
})