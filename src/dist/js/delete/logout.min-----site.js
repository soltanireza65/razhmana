$('#logout').on('click' , function () {
    let params = {
        action : 'logout-user'
    }
    $.ajax({
        url: '/api/ajax',
        type: 'POST',
        data: JSON.stringify(params),
        success: function (response) {
            response = JSON.parse(response);
            if (response.status == 200){
                window.location.href ='/login';
            }

        }
    });
});
