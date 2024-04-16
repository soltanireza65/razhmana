$(document).ready(function () {
    $('.mj-driver-cargo2').click(function () {
        if ($(this).hasClass('disable')){
            $('.mj-disable-btn-alert').addClass('active')
        }else{

        }
    })

    setTimeout(function() {
            $('.mj-disable-btn-alert').removeClass('active')
    }, 5000);

})
$('.btn-close').click(function () {
    var videos = document.getElementsByTagName("video");
    for (var i = 0; i < videos.length; i++) {
        videos[i].pause();
    }
})