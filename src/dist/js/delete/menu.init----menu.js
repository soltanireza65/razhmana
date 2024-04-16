$('.mj-menu-expand').on('click', function () {
    $('.mj-menu-box').toggleClass('expanded');
    $('.mj-menu').toggleClass('expanded');
    $(this).toggleClass('expanded');
});