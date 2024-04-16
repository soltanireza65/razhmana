var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1.1, spaceBetween: 10, loop: true,
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1.1,
            spaceBetween: 10
        },
        // when window width is >= 480px
        768: {
            slidesPerView: 2.2,
            spaceBetween: 10
        },
        // when window width is >= 640px
        992: {
            slidesPerView: 3.3,
            spaceBetween: 15
        }
    },
    navigation: {
        nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev",
    },
});


$('#load_more').click(function () {
    let count = $(this).data('mj-count');
    let BTN = Ladda.create(document.querySelector('#load_more'));
    BTN.start();

    let data = {
        action: 'load_more_blog', count: count
    };

    $.ajax({
        type: 'POST', url: '/api/ajax', data: JSON.stringify(data), success: function (data) {

            BTN.remove();

            let temp = JSON.parse(data);

            if (temp.count >= 6) {
                $('#load_more').data('mj-count', count + 5);
            } else {
                $('#load_more').addClass('d-none');
            }

            if (temp.status == 200) {
                getAddBlog(temp.data)
            } else {
                $('#load_more').addClass('d-none');
            }

        }
    });
});

function getAddBlog(loop) {
    let temp = '';
    for (let i = 0; i < loop.length; ++i) {
        temp = temp + '  <div class="mj-home-blog-list mb-2">\n' +
            '                            <a href="/blog/' + loop[i].slug + '">\n' +
            '                                <div class="mj-blog-list-item">\n' +
            '                                    <div class="mj-blog-item-card d-flex align-items-center">\n' +
            '                                        <div class="mj-blog-img">\n' +
            '                                            <img src="' + loop[i].image + '"\n' +
            '                                                 alt="' + loop[i].alt + '">\n' +
            '                                            <div class="mj-blog-date">\n' +
            '                                                ' + loop[i].submitTime + '\n' +
            '                                            </div>\n' +
            '\n' +
            '                                        </div>\n' +
            '                                        <div class="mj-blog-card-title">\n' +
            '                                            ' + loop[i].title + '\n' +
            '                                        </div>\n' +
            '                                    </div>\n' +
            '                                </div>\n' +
            '                            </a>\n' +
            '                        </div>';
    }

    $('#AddDiv').append(temp);
}