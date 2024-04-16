$(document).ready(function () {
    const link = $('#shareBtn').attr('href');
    $('#xDesc').keyup(function () {
        let len1 = $(this).val().trim().length;
        $('#length_xDesc').html('<b class="text-info">' + len1 + '</b>');
        let text = $(this).val().trim();
        $('#shareBtn').attr('href', link + text)
    });

    $('.btnShare').on({
        click: function () {
            let t = $(this).data('tj-text');
            $('#shareBtn').attr('href', link + t)
            $('#xDesc').val(t)
        }
    })


});

