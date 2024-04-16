var countDownDate = $('#expiredText').data('tj-time');

// Update the count down every 1 second
var x = setInterval(function () {
    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Output the result in an element with id="demo"
    // $('#countDownTimer').html(days + " : " + hours + " : "
    //     + minutes + " : " + seconds);
    $('#dID').html(days);
    $('#hID').html(hours);
    $('#mID').html(minutes);
    $('#sID').html(seconds);

    // If the count down is over, write some text
    if (distance < 0) {
        // $('#countDownTimer').hide(1000,function () {
        // $(this).remove()
        // });
        $('#dID').html(0);
        $('#hID').html(0);
        $('#mID').html(0);
        $('#sID').html(0);

        clearInterval(x);
        // $('#expiredText').show(1000);

        $("#countDownTimer").fadeOut(1000, function () {
            $(this).remove();
            $('#expiredText').show(1000);
        });

        // $("#countDownTimer").fadeOut(1000, function() {
        //     $('#expiredText').show(1000);
        // });
    }
}, 1000);