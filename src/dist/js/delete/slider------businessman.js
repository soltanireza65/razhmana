var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 10,
    autoplay:true,
    rtl:true,
    loop:true,
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 10
        },
        // when window width is >= 480px
        768: {
            slidesPerView: 2,
            spaceBetween: 10
        },
        // when window width is >= 640px
        992: {
            slidesPerView: 3,
            spaceBetween: 15
        }
    }


});