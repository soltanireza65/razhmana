
let id = setInterval(function () {
    let d = new Date();
    let time = parseInt(d.getTime() / 1000);

    if (time % 60 == 0) {

        let data = {
            action: 'complaint-new',
        };
        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(data),
            success: function (data) {
                $("#myButton").dblclick();
                const myArray = data.split(" ");
                if (myArray[0] == 'show') {
                    $('#warningAlertModalCount').text(myArray[1]);
                    // $('#warningAlertModalAudio').click();
                    $('#warningAlertModalAudio').get(0).play();
                    $('#warningAlertModal').modal('show');

                } else {
                    $('#warningAlertModal').modal('hide');
                }
            }
        });
    }
}, 1000);

// clearInterval(id)