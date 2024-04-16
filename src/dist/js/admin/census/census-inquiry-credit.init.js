$('#dateY').mask('AS', {
    'translation': {
        S: {pattern: /[0-9]/},
        A: {pattern: /[0]/},
    }
});
// Inputmask.unmask("23/03/1973", { alias: "datetime", inputFormat: "dd/mm/yyyy", outputFormat: "ddmmyyyy"});
Inputmask({
    alias: "datetime", inputFormat: "mm",
    'groupSeparator': ',',
    'inputmode': 'integer',
    'rightAlign': true,
    outputFormat: "mm"
}).mask(document.querySelectorAll("#dateM"));

Inputmask({
    alias: "datetime", inputFormat: "dd",
    'groupSeparator': ',',
    'inputmode': 'integer',
    'rightAlign': true,
    outputFormat: "dd"
}).mask(document.querySelectorAll("#dateD"));

function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}


$('#btnSubmit').click(function () {
    var BTNN = Ladda.create(document.querySelector('#btnSubmit'));


    let day = $('#dateD').val().trim();
    let month = $('#dateM').val().trim();
    let year = $('#dateY').val().trim();


    BTNN.start();
    let data = {
        action: 'census-inquiry-credit',
        time: year + month + day,
        token: $('#token').val().trim(),
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();
            let temp = JSON.parse(data);

            if (temp.status == 200) {
                $('#EmptyTR').addClass('d-none');
                $('table').find('.trGets').addClass('d-none');
                getResult(temp.response);
                $('#token').val(temp.token);
            } else if (temp.status == -1) {
                $('#token').val(temp.token);

            } else {
                $('#EmptyTR').removeClass('d-none');
                $('table').find('.trGets').addClass('d-none');
                // location.reload();
            }

        }
    });
});


function getResult(loop) {
    let temp = '';
    for (let i = 0; i < (loop).length; ++i) {

        temp = '<tr class="trGets"><td>' + (i + 1) + '</td><td class="text-center">' + loop[i].persianName + '</td><td class="text-center">' + loop[i].count + '</td></tr>';
        $('#AddDiv').append(temp);
    }
    // let i = 1;
    // $.each( loop, function( key, value ) {
    //     // sum += value;
    //     $('#AddDiv').append('<tr><td>'+i+'</td><td>'+key+'</td><td>'+value+'</td></tr>');
    //     i++;
    // });

    // $('#AddDiv').append(temp);
}










