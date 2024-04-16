var swiper = new Swiper(".mySwiper1", {
    slidesPerView: 1.1,
    spaceBetween: 10,

    breakpoints: {
        640: {
            slidesPerView: 2.1,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 3.1,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    },
});

let not_search = $('#not-search')
let search = $('#search-container')
let academies;

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

$('#mj-m-academy-search').keyup(delay(function (e) {
    var len = $(this).val().trim().length;
    var search_value = $(this).val().trim();
    if (len != 0) {
        not_search.css('display', 'none')
        search.css('display', 'block')
    } else {
        not_search.css('display', 'block')
        search.css('display', 'none')
    }
    search.html('  <lottie-player src="/dist/lottie/loading.json" class="mx-auto mt-3"\n' +
        '                                                               style="max-width: 400px;" speed="1" loop\n' +
        '                                                               autoplay></lottie-player>\n' +
        '\n' +
        '                                                <h6 class="mb-0 text-center">' + lang_vars.u_academy_loading + ' \n' +
        '                                                    ...</h6>')
    let params = {
        action: 'search-in-academy-category',
        search: search_value,
        category_id :category_id ,
        token: $('#token-search-academy').val()
    };
    $.ajax({
        url: '/api/ajax', type: 'POST', data: JSON.stringify(params), success: function (response) {
            const json = JSON.parse(response);
            $('#token_login').val(json.response);
            if (json.status == 200) {
                academies = json.response;
                let output = '';
                for (let i = 0; i <= 10; i++) {
                    if (academies[i]) {
                        let category_name = academies[i].category_name;
                        let academy_title = academies[i].academy_title;
                        let academy_thumbnail = academies[i].academy_thumbnail;
                        let academy_id = academies[i].academy_id;
                        let academy_slug = academies[i].academy_slug;
                        let academy_submit_time = academies[i].academy_submit_time;
                        output += '<a href="/academy/'+academy_slug +'" <div class="mj-cat-tab-post">\n' +
                            '                            <img src="' + academy_thumbnail + '" alt="4">\n' +
                            '                            <div class="mj-cat-tab-post-content">\n' +
                            '                                <div class="mj-post-category">' + category_name + '</div>\n' +
                            '                                <div class="mj-post-title">' + academy_title + '\n' +
                            '                                </div>\n' +
                            '                                <div class="mj-post-date">' + academy_submit_time + '\n' +
                            '                                </div>\n' +
                            '                            </div>\n' +
                            '                        </div>';
                    } else {

                    }
                }
                search.html(output);
            }
            else if (json.status == 204) {
                search.html('  <lottie-player src="/dist/lottie/404.json" class="mx-auto mt-3"\n' +
                    '                                                               style="max-width: 400px;" speed="1" loop\n' +
                    '                                                               autoplay></lottie-player>\n' +
                    '\n' +
                    '                                                <h6 class="mb-0 text-center">' + lang_vars.u_academy_not_found + ' \n' +
                    '                                                    ...</h6>')
            }

            search.attr('data-item-resume' , 10)
        }
    });
}, 750));

let ajax_load_status = true;
$(function () {
    $(document).on('scroll', async function () {
        //start ajax load more academies
        if (academies) {
            if (ajax_load_status == true) {
                console.log('run')

                let step = search.data('item-steps')
                let resume = search.attr('data-item-resume');
                let start_index = parseInt(resume) + 1
                let loop_index = (parseInt(resume) + parseInt(step))

                if ($(this).scrollTop() + $(window).height() >= $(document).height()) {
                    if (academies.length >= start_index) {
                        $('#loading').css('display', 'flex ')
                        ajax_load_status = false;
                        setTimeout(await function () {
                            let output = search.html();
                            for (let i = start_index; i < loop_index; i++) {
                                if (academies[i]) {
                                    let category_name = academies[i].category_name;
                                    let academy_title = academies[i].academy_title;
                                    let academy_id = academies[i].academy_id;
                                    let academy_slug = academies[i].academy_slug;
                                    let academy_thumbnail = academies[i].academy_thumbnail;
                                    let academy_submit_time = academies[i].academy_submit_time;
                                    output += '<a href="/academy/' + academy_slug + '"><div class="mj-cat-tab-post">\n' +
                                        '                            <img src="' + academy_thumbnail + '" alt="4">\n' +
                                        '                            <div class="mj-cat-tab-post-content">\n' +
                                        '                                <div class="mj-post-category">' + category_name + '</div>\n' +
                                        '                                <div class="mj-post-title">' + academy_title +  '\n' +
                                        '                                </div>\n' +
                                        '                                <div class="mj-post-date">' + academy_submit_time + '\n' +
                                        '                                </div>\n' +
                                        '                            </div>\n' +
                                        '                        </div></a>';
                                    search.attr('data-item-resume', i)
                                } else {
                                    search.attr('data-item-resume', i)
                                    break;
                                }
                            }
                            search.html(output);
                            $('#loading').css('display', 'none ')
                            ajax_load_status = true;
                        }, 1500)
                    }
                }
            }
        }
        //end ajax load more academies
    });
});