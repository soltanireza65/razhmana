const gridImages = document.querySelectorAll(".mj-driver-item-detail-images-item img");
const lightbox = document.getElementById("lightbox");
const lightboxImg = document.getElementById("lightbox-img");

// to open lightbox
gridImages.forEach((img) => {

    img.addEventListener("click", () => {
        lightbox.classList.add("active");
        // set the image clicked as the image of the lightbox
        lightboxImg.src = img.src;
    });
});

// To close lightbox
lightbox.addEventListener("click", (e) => {
    // if the clicked element is not the dark overlay don't close it
    if (e.target !== e.currentTarget) return;
    // if it was the dark overlay it will close it
    lightbox.classList.remove("active");
});

$('#accept-driver-cv').click(function () {
    let cv_id = $(this).data('cv-id')
    let token = $('#token2').val().trim();
    let data = {
        action: 'change-driver-cv-status',
        cv_id: cv_id,
        status: 'accepted',
        token: token,
    };
    $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {


            if (data == 'successful') {

                toastNotic(var_lang.successful, var_lang.successful_update_mag, "success");

            } else if (data == "empty") {
                toastNotic(var_lang.error, var_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(var_lang.error, var_lang.token_error);
            } else {
                toastNotic(var_lang.error, var_lang.error_mag);
            }
            $(this).prop('disabled', false);
            window.setTimeout(
                function () {
                    window.location.replace('/admin/driver-services')
                },
                2000
            );
        }
    });
})


$('#reject-driver-cv').click(function () {
    let cv_id = $(this).data('cv-id')
    let token = $('#token2').val().trim();
    let reject_desc = $('#cv-reject-detail').val()
    let data = {
        action: 'reject-driver-cv',
        cv_id: cv_id,
        status: 'rejected',
        token: token,
        reject_desc:reject_desc
    };
    $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {


            if (data == 'successful') {

                toastNotic(var_lang.successful, var_lang.successful_update_mag, "success");

            } else if (data == "empty") {
                toastNotic(var_lang.error, var_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(var_lang.error, var_lang.token_error);
            } else {
                toastNotic(var_lang.error, var_lang.error_mag);
            }
            $(this).prop('disabled', false);
            window.setTimeout(
                function () {
                    window.location.replace('/admin/driver-services')
                },
                2000
            );
        }
    });
})