var swiper = new Swiper(".project-swiper", {
    slidesPerView: 3,
    spaceBetween:20,

    loop: true,
    autoplay: {
        delay: 2500,
        disableOnInteraction: true,
    },
    breakpoints: {
        640: {
            slidesPerView:4,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 5,
            spaceBetween: 20,
        },

    },

});
var swiper = new Swiper(".project-swiper2", {
    slidesPerView: 5,
    spaceBetween:10,

    loop: true,
    autoplay: {
        delay: 2500,
        disableOnInteraction: true,
    },

});