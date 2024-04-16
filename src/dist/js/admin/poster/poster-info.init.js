var swiper = new Swiper(".mySwiper", {
    loop: false,
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: false,
    watchSlidesProgress: true,
});
var swiper2 = new Swiper(".mySwiper2", {
    loop: false,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: swiper,
    },
});

let temp_lang = JSON.parse(var_lang);


// $('#sasad').bootstrapTable();
/*
$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>',
*/

/*
  $("#change_poster_type").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 1,
    inputclass: "form-select-sm form-select",
    source: temp_lang.array_type,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_category').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_gearbox_id").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 2,
    inputclass: "form-select-sm form-select",
    source: temp_lang.a_gearboxs,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_type').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_fuel_id").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 3,
    inputclass: "form-select-sm form-select",
    source: temp_lang.a_fuels,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_type').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_poster_cash").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 4,
    inputclass: "form-select-sm form-select",
    source: temp_lang.array_yes_no,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_type').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_poster_leasing").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 5,
    inputclass: "form-select-sm form-select",
    source: temp_lang.array_yes_no,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_type').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_poster_installments").editable({
    // prepend: "not selected",
    type: 'selected',
    mode: "inline",
    pk: 6,
    inputclass: "form-select-sm form-select",
    source: temp_lang.array_yes_no,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_type').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $('#change_poster_price').editable({
    type: 'number',
    pk: 7,
    success: function (response, newValue) {
        let Value = newValue.trim();
        let type = $('#change_cargo_weight').data('mj-type');
        if (Value.length > 0) {
            f(type, Value)
        } else {
            toastNotic(temp_lang.error, temp_lang.empty_input);
        }
    }
}),
    $("#change_currency_id").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 8,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_currency,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_monetary_unit').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_cargo_desc').editable({
        type: 'textarea',
        pk: 9,
        success: function (response, newValue) {
            // console.log(newValue);
            let Value = newValue.trim();
            let type = $('#change_cargo_description').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_trailer_id").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 10,
        inputclass: "form-select-sm form-select",
        source: temp_lang.a_trailers,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_type').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_poster_axis").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 11,
        inputclass: "form-select-sm form-select",
        source: temp_lang.a_axis,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_type').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $("#change_poster_type_status").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 12,
        inputclass: "form-select-sm form-select",
        source: temp_lang.a_truck_status,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_type').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
   $('#change_poster_built').editable({
        type: 'number',
        pk: 13,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_weight').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_poster_used').editable({
        type: 'number',
        pk: 14,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_weight').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_poster_phone').editable({
        type: 'number',
        pk: 15,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_weight').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    }),
    $('#change_poster_whatsapp').editable({
        type: 'number',
        pk: 16,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_cargo_weight').data('mj-type');
            if (Value.length > 0) {
                f(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    });
       function f(type, newValue, refresh = false) {

    let posterID = $('#poster-id').data('mj-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'poster-info',
        value: newValue,
        type: type,
        posterID: posterID,
        token: token,
        // token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {

            const myArray = data.split(" ");

            if (myArray[0] == 'successful') {
                // $(".btn").attr('disabled', 'disabled');
                $('#token').val(myArray[1]);
                toastNotic(temp_lang.successful, temp_lang.successful_submit_mag, "success");
                if (refresh) {
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                }
            } else if (data == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }


        }
    });

}
    */

if ($('.btnSubmit').length > 0) {
    $('.btnSubmit').click(function () {
        let _this = $(this);
        let btn = _this.prop('id');
        let status = _this.data('mj-status');
        let token = $('#token').val().trim();
        let reason = $('#reason-poster').val().trim();
        let posterId = $('#poster-id').data('mj-id');
        $(".btn").attr('disabled', true);
        let BTNN = Ladda.create(document.querySelector('#' + btn));

        const lists = ['rejected', 'needed'];
        let flag = true;
        if (jQuery.inArray(status, lists) != -1 && reason.length <= 0) {
            flag = false;
        }

        if (flag) {
            BTNN.start();
            let data = {
                action: 'change-poster-status',
                posterId: posterId,
                status: status,
                reason: reason,
                token: token,
            };
            $.ajax({
                type: 'POST',
                url: '/api/adminAjax',
                data: JSON.stringify(data),
                success: function (data) {
                    const myArray = data.split(" ");
                    BTNN.remove();

                    if (myArray[0] == "successful") {
                        $(".btn").attr('disabled', 'disabled');
                        toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                        window.setTimeout(
                            function () {
                                if ($('[data-tj-replace]').length > 0) {
                                    window.location.replace("/admin/poster/info/" + $('[data-tj-replace]').data('tj-replace'));
                                } else {
                                    window.location.reload();
                                }
                            },
                            2000
                        );
                    } else if (myArray[0] == 'error') {
                        $(".btn").attr('disabled', false);
                        $('#token').val(myArray[1]);
                        toastNotic(temp_lang.error, temp_lang.empty_input);
                    } else if (data == "token_error") {
                        $(".btn").attr('disabled', false);
                        toastNotic(temp_lang.error, temp_lang.token_error);
                    } else {
                        $(".btn").attr('disabled', false);
                        if (myArray[1].length > 0) {
                            $('#token').val(myArray[1]);
                        }
                        toastNotic(temp_lang.error, temp_lang.error_mag);
                    }
                }
            });
        } else {
            $(".btn").attr('disabled', false);
            toastNotic(temp_lang.error, temp_lang.a_desc_reason_poster);
        }
    });
}

function timer1(timeEnd, target) {
    var x = setInterval(function () {
        // Get today's date and time
        let timeE = timeEnd * 1000;
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = timeE - now;

        if (distance > 0) {
            let eee = (((now / 1000)) * 100) / (timeEnd)


            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Output the result in an element with id="demo"
            target.html(days + "d - " + hours + "h - " + minutes + "m - " + seconds + "s ");
        } else {
            // If the count down is over, write some text
            target.html(0 + "d - " + 0 + "h - " + 0 + "m - " + 0 + "s ");

            clearInterval(x);
        }
    }, 1000);
}


let myTimer = $('body').find('[data-tj-time]');

myTimer.each(function (index) {
    let _this = $(this);
    let val = $(this).data('tj-time');
    timer1(val, _this)
});


$('.btn-report').on('click', function () {
    let _this = $(this);
    let reportId = _this.data('tj-pr');
    let token = $('#token').val().trim();
    let posterId = _this.data('tj-poster-id');

    _this.attr('disabled', true);
    _this.find('i').addClass('mdi-spin');

    let data = {
        action: 'change-poster-report-status',
        posterId: posterId,
        reportId: reportId,
        token: token,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            _this.attr('disabled', false);
            _this.find('i').removeClass('mdi-spin');
            if (data == "successful") {
                toastNotic(temp_lang.successful, temp_lang.a_report_status_change, "info");
                window.setTimeout(
                    function () {
                        window.location.reload();
                    },
                    2000
                );
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });
});

$(document).ready(function () {
    if ($('#myModal').length > 0) {
        $('#myModal').modal('show');
    }


    $('.submit-poster-title').click(function () {
        let language = $(this).data('language');
        let poster_id = $(this).data('poster-id')
        let title = $('#poster-' + language).val()



        let params  = {
            action : 'change-poster-title',
            language : language,
            poster_id : poster_id ,
            title :  title
        }

        $.ajax({
            type: 'POST',
            url: '/api/adminAjax',
            data: JSON.stringify(params),
            success: function (data) {

                let result = JSON.parse(data)
                console.log(result)
                if (result.status == 200) {
                    toastNotic(temp_lang.successful, temp_lang.change_title, "info");
                    window.setTimeout(
                        function () {
                            window.location.reload();
                        },
                        2000
                    );
                } else {
                    toastNotic(temp_lang.error, temp_lang.error_mag);
                }
            }
        });
    })
});