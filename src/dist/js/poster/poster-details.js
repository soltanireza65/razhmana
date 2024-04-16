$('.mj-p-moreless-features-button').click(function () {
    $('.mj-p-poster-detail-more-features').slideToggle();
    if ($('.mj-p-moreless-features-button').text().trim() === lang_vars.show_more + " >") {
        $(this).text(lang_vars.u_low + " <")
    } else {
        $(this).text(lang_vars.show_more + " >")
    }
});


const userDesc = $('.mj-p-excerpt-texts-1');
let tempHeight = userDesc.height()
if (tempHeight < 50) {
    $('.mj-p-moreless-excerpt-button').hide()
} else if (tempHeight >= 50) {
    $('.mj-p-moreless-excerpt-button').show()
}
userDesc.height(60);


$('.mj-p-moreless-excerpt-button').click(function () {
    if ($('.mj-p-moreless-excerpt-button').text() === lang_vars.show_more + " >") {
        userDesc.animate({height: tempHeight + "px"}, 200)
        $(this).text(lang_vars.u_low + " <")
    } else {
        userDesc.animate({height: "60px"}, 200)
        $(this).text(lang_vars.show_more + " >")
    }
});


const userExcerptDesc = $('.mj-p-excerpt-texts-2');
let tempHeightExcerptDesc = userExcerptDesc.height()
if (tempHeightExcerptDesc < 50) {
    $('.mj-p-moreless-pro-button').hide()
} else if (tempHeightExcerptDesc >= 50) {
    $('.mj-p-moreless-pro-button').show()
}
userExcerptDesc.height(60);


$('.mj-p-moreless-pro-button').click(function () {
    if ($('.mj-p-moreless-pro-button').text() === lang_vars.show_more + " >") {
        userExcerptDesc.animate({height: tempHeightExcerptDesc + "px"}, 200)
        $(this).text(lang_vars.u_low + " <")
    } else {
        userExcerptDesc.animate({height: "60px"}, 200)
        $(this).text(lang_vars.show_more + " >")
    }
});


$(document).on('click', '.mj-contact-close', function () {
    let PosterBtn = $('.mj-poster-details-contact')
    PosterBtn.toggleClass('active')

    if (PosterBtn.hasClass('active')) {
        $(this).children().attr('src', '/dist/images/poster/close%20(detail).svg')
    } else {
        $(this).children().attr('src', '/dist/images/poster/phone(detail).svg')
    }
});
var swiper = new Swiper(".mySwiper", {
    pagination: {
        el: ".swiper-pagination",

    },
    breakpoints: {
        1200: {
            slidesPerView: 3,

        },
        992: {
            slidesPerView: 3,

        },
        771: {
            slidesPerView: 2,

        }
    }
});

var lightbox = new SimpleLightbox('.mj-p-poster-slides a', { /* options */});



$('#share').click(function () {
    if (navigator.share) {
        navigator.share({
            title: $(this).data('tj-title') + $(this).data('tj-share-setting'),
            text: $(this).data('tj-title') + $(this).data('tj-share-setting'),
            url: $(this).data('tj-link')
        }).then(() => {
            // console.log('Thanks for sharing!');
        })
            .catch(console.error);
    } else {
        // shareDialog.classList.add('is-open');
        copyToClipboard($(this).data('tj-title') + $(this).data('tj-share-setting')) + $(this).data('tj-link')
        function copyToClipboard(text) {
            /* Create a textarea element to hold the text to be copied */
            var textarea = document.createElement("textarea");
            textarea.value = text; // Set the text to be copied as the parameter
            document.body.appendChild(textarea); // Append the textarea element to the DOM
            textarea.select(); // Select the text in the textarea
            document.execCommand("copy"); // Execute the copy command
            document.body.removeChild(textarea); // Remove the textarea element from the DOM
            $('.mj-share-tooltip').fadeIn(300);
            setTimeout(function () {
                $('.mj-share-tooltip').fadeOut(200);
            },2000)
        }
    }
});


// Start Report Poster
const reposts = $('[name="report"]');
const reportOtherDiv = $('#report-other-div');
reposts.each(function () {
    $(this).change(function () {
        $('#btn-report').attr('disabled', false)
        reposts.each(function (i) {
            if ($(this).is(':checked') === true) {
                if ($(this).prop('id') == "report-other") {
                    reportOtherDiv.removeClass('mj-a-height-0');
                    reportOtherDiv.addClass('mj-a-height-active');
                } else {
                    reportOtherDiv.addClass('mj-a-height-0');
                    reportOtherDiv.removeClass('mj-a-height-active');
                }
            }
        });
    });
});

$('#btn-report').on('click', function () {
    let catId = $('[name="report"]:checked').data('tj-report-id');
    let posterId = $('[name="report"]:checked').data('tj-poster-id');
    let description = $('#desc-report-other').val().trim();
    if (catId && catId > 0) {
        setReport(posterId, catId, description);
    } else {
        if (catId == 0 && description.length > 0) {
            setReport(posterId, catId, description);
        } else {
            sendNotice(lang_vars.alert_warning, lang_vars.u_report_input_placeholder, 'warning', 2500);
        }
    }


});

function setReport(posterId, catId, description) {
    $('#btn-report').addClass('tj-a-loader-2');
    $('#btn-report').attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });

    let data = {
        action: 'poster-report',
        catId: catId,
        posterId: posterId,
        description: description,
        token: $('#token').data('tj-token'),
    };
    $.ajax({
        type: 'POST',
        url: '/api/ajax',
        data: JSON.stringify(data),
        success: function (data) {

            setTimeout(function () {
                $('#btn-report').removeClass('tj-a-loader-2');
                $('#btn-report').attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
            }, 1000);

            let response = JSON.parse(data);
            console.log(response)
            if (response.status == 200) {
                sendNotice(lang_vars.alert_info, lang_vars.u_report_alert_3, 'info', 2500);
                $('#modalReport').modal('hide');
            } else if (response.status == -10) {
                sendNotice(lang_vars.alert_warning, lang_vars.u_report_alert_2, 'warning', 2500);
                $('#modalReport').modal('hide');
            } else {
                sendNotice(lang_vars.alert_warning, lang_vars.u_report_alert_1, 'warning', 2500);
            }


        }
    });
}

// End Report Poster


// Start Delete Poster
const deleteReason = $('[name="delete-reason"]');
deleteReason.each(function () {
    $(this).change(function () {
        $('#btn-reason-delete').attr('disabled', false)
        deleteReason.each(function (i) {

        });
    });
});

$('#btn-reason-delete').on('click', function () {
    let catId = $('[name="delete-reason"]:checked').data('tj-delete-id');
    let posterId = $('[name="delete-reason"]:checked').data('tj-poster-id');
    if (catId && catId > 0) {
        setDelete(posterId, catId);
    } else {
        sendNotice(lang_vars.alert_warning, lang_vars.u_delete_input_placeholder, 'warning', 2500);
    }


});

function setDelete(posterId, catId) {
    $('#btn-reason-delete').addClass('tj-a-loader-2');
    $('#btn-reason-delete').attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });

    let data = {
        action: 'poster-delete',
        catId: catId,
        posterId: posterId,
        token: $('#token').data('tj-token'),
    };
    $.ajax({
        type: 'POST',
        url: '/api/ajax',
        data: JSON.stringify(data),
        success: function (data) {
            setTimeout(function () {
                $('#btn-reason-delete').removeClass('tj-a-loader-2');
                $('#btn-reason-delete').attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
            }, 1000);

            let response = JSON.parse(data);
            if (response.status == 200) {
                sendNotice(lang_vars.alert_info, lang_vars.u_reason_delete_alert_2, 'info', 2500);
                window.setTimeout(
                    function () {
                        window.location.reload();
                    },
                    2000
                );
            } else {
                sendNotice(lang_vars.alert_warning, lang_vars.u_reason_delete_alert_1, 'warning', 2500);
            }


        }
    });
}

// End Delete Poster


// Start Upgrade
const walletQuick = $('#pay-wallet-quick');
const onlineQuick = $('#pay-online-quick');
const btnUpgradeQuick = $('#btn-upgrade-quick');


walletQuick.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    onlineQuick.parent().removeClass('mj-a-border-active')
});
onlineQuick.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    walletQuick.parent().removeClass('mj-a-border-active')
});


const walletLadder = $('#pay-wallet-ladder');
const onlineLadder = $('#pay-online-ladder');
const btnUpgradeLadder = $('#btn-upgrade-ladder');

walletLadder.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    onlineLadder.parent().removeClass('mj-a-border-active')
});
onlineLadder.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    walletLadder.parent().removeClass('mj-a-border-active')
});

$(document).on('click', '.mj-fori-item-2', function () {
    $(this).addClass('active')
    $('.mj-lader-item').removeClass('active')
    $('.mj-fori-item-2 img').addClass('fa-bounce')
    $('.mj-lader-item img').removeClass('fa-bounce')
    $('.mj-fori-card-2').addClass('active')
    $('.mj-lader-card').removeClass('active')
});
$(document).on('click', '.mj-lader-item', function () {
    $(this).addClass('active')
    $('.mj-fori-item-2').removeClass('active')
    $('.mj-lader-item img').addClass('fa-bounce')
    $('.mj-fori-item-2 img').removeClass('fa-bounce')
    $('.mj-lader-card').addClass('active')
    $('.mj-fori-card-2').removeClass('active')
});

let btnSubmitUpgrade = $('.submit-upgrade');

btnSubmitUpgrade.on('click', function () {
    let _this = $(this);
    let posterId = _this.data('tj-poster');
    let kind = _this.data('tj-type');

    _this.addClass('tj-a-loader-2');
    _this.attr('disabled', true).css({
        transition: 'all .3s',
        opacity: .5
    });

    let type = $('[name="upgrade-' + kind + '"]:checked').data('tj-type');

    let data = {
        action: 'poster-upgrade',
        posterId: posterId,
        type: type,
        kind: kind,
        token: $('#token').data('tj-token'),
    };

    $.ajax({
        type: 'POST',
        url: '/api/ajax',
        data: JSON.stringify(data),
        success: function (data) {
            setTimeout(function () {
                _this.removeClass('tj-a-loader-2');
                _this.attr('disabled', false).css({
                    transition: 'all .3s',
                    opacity: 1
                });
            }, 1000);
            console.log(data)
            let response = JSON.parse(data);
            if (response.status == 200) {
                sendNotice(lang_vars.alert_success, lang_vars.u_alert_set_upgrade_1, 'success', 3500);
                window.setTimeout(
                    function () {
                        window.location.reload();
                    },
                    3500
                );
            } else if (response.status == 201) {
                sendNotice(lang_vars.alert_success, lang_vars.u_alert_set_upgrade_2, 'success', 3500);
                window.setTimeout(
                    function () {
                        window.location.reload();
                    },
                    3500
                );
            } else if (jQuery.inArray(response.status, [-11, -23]) != -1) {
                sendNotice(lang_vars.alert_info, lang_vars.u_alert_set_upgrade_3, 'info', 3500);
            } else if (response.status == -22) {
                sendNotice(lang_vars.alert_info, lang_vars.u_alert_set_upgrade_5, 'info', 3500);
            } else {
                sendNotice(lang_vars.alert_error, lang_vars.u_alert_set_upgrade_4, 'error', 3500);
            }


        }
    });
});

// End Upgrade


// Start Expert

const onlineExpert = $('#pay-online-expert');
const walletExpert = $('#pay-wallet-expert');
const btnExpertSubmit = $('#btn-expert-submit');
const requestExpert = $('[name="request-expert"]');
const addressExpert = $('#address');


walletExpert.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    onlineExpert.parent().removeClass('mj-a-border-active')
});
onlineExpert.click(function () {
    $(this).parent().addClass('mj-a-border-active')
    walletExpert.parent().removeClass('mj-a-border-active')
});

btnExpertSubmit.on('click', function () {
    let _this = $(this);
    let posterId = _this.data('tj-poster');
    let address = $('#address').val().trim();

    if (address.length > 0) {
        _this.addClass('tj-a-loader-2');
        _this.attr('disabled', true).css({
            transition: 'all .3s',
            opacity: .5
        });
        let type = $('[name="request-expert"]:checked').data('tj-type');

        let data = {
            action: 'poster-request-expert',
            posterId: posterId,
            address: address,
            type: type,
            token: $('#token').data('tj-token'),
        };
        $.ajax({
            type: 'POST',
            url: '/api/ajax',
            data: JSON.stringify(data),
            success: function (data) {
                setTimeout(function () {
                    _this.removeClass('tj-a-loader-2');
                    _this.attr('disabled', false).css({
                        transition: 'all .3s',
                        opacity: 1
                    });
                }, 1000);
                console.log(data)
                let response = JSON.parse(data);
                if (response.status == 200) {
                    sendNotice(lang_vars.alert_success, lang_vars.u_alert_set_expert_5, 'success', 2500);
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                } else if (response.status == -22) {
                    sendNotice(lang_vars.alert_info, lang_vars.u_alert_set_expert_1, 'info', 2500);
                } else if (response.status == -23) {
                    sendNotice(lang_vars.alert_warning, lang_vars.u_alert_set_expert_3, 'warning', 2500);
                } else {
                    sendNotice(lang_vars.alert_error, lang_vars.u_alert_set_expert_2, 'error', 2500);
                }


            }
        });
    } else {
        sendNotice(lang_vars.alert_warning, lang_vars.u_alert_set_expert_4, 'warning', 2500);
    }
});


// End Expert


function inIframe () {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

loadBack()
async function loadBack(){
    if (await  inIframe()){

    }else{
        $('main').append('<a href="/poster" >\n' +
            '        <div class="mj-backbtn" >\n' +
            '            <div class="fa-caret-right"></div>\n' +
            '        </div>\n' +
            '    </a>')
    }
}


