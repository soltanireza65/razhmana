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

var swiper = new Swiper(".mj-footer-slider", {
    slidesPerView: 5,
    spaceBetween: 10,
    autoplay: true,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});


var swiper = new Swiper(".DriverSwiper", {
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
        576: {
            slidesPerView: 2,
            spaceBetween: 10
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 10
        },
        // when window width is >= 640px
        992: {
            slidesPerView: 4,
            spaceBetween: 15
        }
    }
});