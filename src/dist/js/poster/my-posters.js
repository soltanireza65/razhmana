
let id=0;
$('.mj-mypost-menu-btn').on('click',function (){

    // let tempHtml1='<li><a href="/poster/edit/"> <div class="fa-edit">'+lang_vars.u_edit_poster+'</div></a></li>';
    // let tempHtml2='<li><a data-bs-toggle="modal" href="#modalDelete"><div class="fa-trash">'+lang_vars.u_delete_poster+'</div></a></li>';
    // let tempHtml3='<li><a data-bs-toggle="modal" href="#modalUpgrade"> <div class="fa-rocket">'+lang_vars.u_upgrade+'</div></a></li>'
    // let tempHtml4='<li><a data-bs-toggle="modal" href="#modalExpert"><div class="fa-user-tie">'+lang_vars.a_poster_expert+'</div></a></li>'
    let tempNotHtml='<li><a data-bs-toggle="modal" href="javascript:void (0);"><div class="fa-circle">'+lang_vars.u_no_menu_active+'</div></a> </li>';

    let _this=$(this);
    id=0;
    id=_this.data('tj-id')
    let status =_this.data('tj-status')
    let html='';
    if (jQuery.inArray(status, ['pending', 'accepted', 'needed']) != -1){
        html +='<li><a href="/poster/edit/'+id+'"> <div class="fa-edit">'+lang_vars.u_edit_poster+'</div></a></li>';
        html +='<li><a data-bs-toggle="modal" href="#modalDelete"><div class="fa-trash">'+lang_vars.u_delete_poster+'</div></a></li>';
        if (status=="accepted"){
           // html +=tempHtml3+tempHtml4
        }
        $('#menu-modal').html(html)
    }else {
        $('#menu-modal').html(tempNotHtml)
    }
    $('#exampleModalToggle').modal('show');
});










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
    let posterId = id;
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
    let posterId = id;
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
    let posterId = id;
    let address = $('#address').val().trim();

    if (address.length > 0) {
        _this.addClass('tj-a-loader-5');
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
                    _this.removeClass('tj-a-loader-5');
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



// Start Search
$(document).ready(function () {
    $("#poster-search").on("keyup", function () {
        console.log(444444)
        let value = $(this).val().toLowerCase();
        $(".mj-s-poster-item-card").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
// End Search


$(document).on('click', '.mj-p-poster-item-content',async function (){
    let ad_id = $(this).data('id');
    $('#poster-detail').attr('src' , '/poster/detail/' +  ad_id)
    await load_iframe();
});
function load_iframe() {
    document.getElementById('poster-detail').onload= function() {
        $('#exampleModaliframe').modal('show')
    };
}

$('#exampleModaliframe').on('shown.bs.modal', function(e) {
    window.location.hash = "detail";
});

$('#exampleModaliframe').on('hidden.bs.modal', '.mj-p-poster-item-content', function () {
    location.hash = ''
})

$(window).on('hashchange', function (event) {
    if(window.location.hash != "#detail") {
        $('#exampleModaliframe').modal('hide');
    }
});





