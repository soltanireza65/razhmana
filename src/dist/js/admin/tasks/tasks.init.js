// start todo_
let myTimer = $('body').find('.my-timer-class');

myTimer.each(function (index) {
    let idDiv = $(this).prop('id');
    let countTimerS = $(this).data('tj-start-time');
    let countTimerE = $(this).data('tj-end-time');
    if (countTimerS && countTimerE) {
        timer(countTimerE, idDiv, countTimerS)
    }

});


function timer(timeEnd, target, timeStart) {
    var x = setInterval(function () {
        // Get today's date and time
        let timeE = timeEnd * 1000;
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = timeE - now;

        if (distance > 0) {
            let eee = (((now / 1000) - timeStart) * 100) / (timeEnd - timeStart)

            checkValue(eee, target)
            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Output the result in an element with id="demo"
            $("#" + target).html(days + "d " + hours + "h " + minutes + "m " + seconds + "s ");
        } else {
            // If the count down is over, write some text
            $("#" + target).html(0 + "d " + 0 + "h " + 0 + "m " + 0 + "s ");
            checkValue(100, target)
            clearInterval(x);
        }
    }, 1000);
}


function checkValue(val, bar) {
    let obj = $('body').find("[data-tj-id=" + bar + "]")
    switch (true) {
        case (val <= 25):
            obj.addClass('bg-info');
            obj.css('width', val + "%");
            break;
        case (val > 25 && val <= 50):
            obj.addClass('bg-primary');
            obj.css('width', val + "%");
            break;
        case (val > 50 && val <= 75):
            obj.addClass('bg-warning');
            obj.css('width', val + "%");
            break;
        case (val > 75 && val <= 90):
            obj.addClass('bg-pink');
            obj.css('width', val + "%");
            break;
        case (val > 90 && val <= 99):
            obj.addClass('bg-danger');
            obj.css('width', val + "%");
            break;
        case (val >= 100):
            obj.addClass('bg-secondary');
            obj.css('width', val + "%");
            break;
    }
}