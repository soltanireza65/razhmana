$('#dateStart').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#startDefault',
    altFormat: 'X',
    // minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    }
});

$('#dateEnd').persianDatepicker({
    format: 'YYYY/MM/DD',
    altField: '#endDefault',
    altFormat: 'X',
    // minDate: Date.now(),
    viewMode: 'year',
    onSelect: function (unixDate) {
        $('#startDefault').val(Math.floor(unixDate / 1000));
    },
});

let Admin = $('#selectAdmin').select2().select2('val');

// $( document ).ready(function() {
//     var adminNickname=$('#selectAdmin').find(":selected").data("mj-nickname");
//     var adminAvatar=$('#selectAdmin').find(":selected").data("mj-avatar");
//     var adminName=$('#selectAdmin').find(":selected").text();
//     $('#adminNickname').text(adminNickname);
//     $('#adminName').text(adminName);
//     $('#adminAvatar').attr('src',adminAvatar);
// });

// $('#selectAdmin').on('change', function() {
// var status=$(this).find(":selected").data("mj-nickname");
// var data = $(".select2 option:selected").text();
// });

$('#btnSubmit').click(function () {
    var BTNN = Ladda.create(document.querySelector('#btnSubmit'));
    let adminID = $('#selectAdmin').select2().select2('val');

    var adminNickname = $('#selectAdmin').find(":selected").data("mj-nickname");
    var adminAvatar = $('#selectAdmin').find(":selected").data("mj-avatar");
    var adminName = $('#selectAdmin').find(":selected").text();
    $('#adminNickname').text(adminNickname);
    $('#adminName').text(adminName);
    $('#adminAvatar').attr('src', adminAvatar);

    BTNN.start();
    let data = {
        action: 'get-admin-census',
        start: $('#startDefault').val(),
        end: $('#endDefault').val(),
        token: $('#token').val().trim(),
        adminID: adminID,
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
                getResult(temp.data);
                $('#token').val(temp.token);
            } else if (temp.status == -1) {
                $('#token').val(temp.token);

            } else {
                $('#EmptyTR').removeClass('d-none');
                $('table').find('.trGets').addClass('d-none');
                location.reload();
            }

        }
    });
});


function getResult(loop) {
    let temp = '';
    for (let i = 0; i < (loop).length; ++i) {

        temp = '<tr class="trGets"><td>' + (i + 1) + '</td><td class="text-center">' + loop[i].name + '</td><td class="text-center">' + loop[i].count + '</td></tr>';
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


function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}