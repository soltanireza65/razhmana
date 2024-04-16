$('.mj-menu-expand').on('click', function () {
    $('.mj-menu-box').toggleClass('expanded');
    $('.mj-menu').toggleClass('expanded');
    $(this).toggleClass('expanded');

    if ($('.mj-d-floating-button').length > 0) {
        const btnRequest = $('.mj-d-floating-button');
        setTimeout(() => {
            btnRequest.css({
                bottom: `calc(58px + ${$('.mj-menu-box').innerHeight()}px)`
            }).toggleClass('expanded');
            if (!btnRequest.hasClass('expanded')) {
                btnRequest.css({
                    bottom: ''
                })
            }
        }, 100);
    }
});

let myLocation;
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (coords) {
        myLocation = coords.coords;

        const params = {
            action: 'update-location',
            lat: myLocation.latitude,
            long: myLocation.longitude,
        };

        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function () {

            }
        })
    });

}

