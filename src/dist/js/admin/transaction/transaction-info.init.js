let temp_lang = JSON.parse(var_lang);

// $('#sasad').bootstrapTable();
$.fn.editable.defaults.mode = 'inline';

$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect"><i class="mdi mdi-close"></i></button>';
if ($('#change_transaction_authority').length > 0) {
    $("#change_transaction_authority").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 1,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_category,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_transaction_authority').data('mj-type');
            if (Value.length > 0) {
                editable(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    });
}
if ($('#change_transaction_trackingCode').length > 0) {
    $("#change_transaction_trackingCode").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 2,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_category,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_transaction_trackingCode').data('mj-type');
            if (Value.length > 0) {
                editable(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    });
}

if ($('#change_transaction_gateway').length > 0) {
    $("#change_transaction_gateway").editable({
        // prepend: "not selected",
        type: 'selected',
        mode: "inline",
        pk: 3,
        emptytext: temp_lang.a_empty,
        inputclass: "form-select-sm form-select",
        source: temp_lang.array_category,
        success: function (response, newValue) {
            let Value = newValue.trim();
            let type = $('#change_transaction_gateway').data('mj-type');
            if (Value.length > 0) {
                editable(type, Value)
            } else {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            }
        }
    });
}

function editable(type, newValue) {

    let transactionID = $('#transactionID').data('mj-transaction-id');
    let token = $('#token').val().trim();

    let data = {
        action: 'transaction-info',
        value: newValue,
        type: type,
        transactionID: transactionID,
        token: token,
        // token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            // console.log(data)

            const myArray = data.split(" ");

            if (myArray[0] == 'successful') {
                // $(".btn").attr('disabled', 'disabled');

                $('#token').val(myArray[1]);
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
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


$('.setSubmitBtn').click(function () {
    let btn = $(this).prop('id');
    let BTNN = Ladda.create(document.querySelector('#' + btn));
    let token = $('#token').val().trim();

    let transactionID = $('#transactionID').data('mj-transaction-id');

    let lists = ["completed", "rejected", "paid", "rejected_deposit"];

    if (transactionID > 0 && token.length > 0 && jQuery.inArray(btn, lists) != -1)
        BTNN.start();
    $(".btn").attr("disabled", true);
    let data = {
        action: 'change-transaction-status',
        token: token,
        transactionID: transactionID,
        status: btn,
    };
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
// console.log(data)
//             $(".btn").attr("disabled", false);
            BTNN.remove();
            $(".btn").attr("disabled", true);
            const myArray = data.split(" ");
            if (myArray[0] == 'successful') {
                $('#token').val(myArray[1]);

                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, "success");
                let sss = temp_lang.accepted;
                if (btn == 'completed') {
                    // sss = "<span class='badge badge-soft-success font-12'>"+temp_lang.completed+"</span>";
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (btn == 'rejected') {
                    // sss = "<span class='badge badge-soft-danger font-12'>"+temp_lang.rejected+"</span>";
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (btn == 'paid') {
                    // sss = "<span class='badge badge-outline-info font-12'>"+temp_lang.paid+"</span>";
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else if (btn == 'rejected_deposit') {
                    // sss = "<span class='badge badge-outline-danger font-12'>"+temp_lang.rejected_deposit+"</span>";
                    window.setTimeout(
                        function () {
                            location.reload();
                        },
                        2000
                    );
                } else {
                    sss = "<span class='badge badge-soft-info font-12'>" + btn + "</span>";
                }
                $('#change_transaction_status').html(sss);
            } else if (data == "empty") {
                toastNotic(temp_lang.error, temp_lang.empty_input);
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }

        }
    });
});


function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}


// else if (btn == 'pending') {
//     sss = "<span class='badge badge-soft-warning font-12'>"+temp_lang.pending+"</span>";
// }

// else if (btn == 'unpaid') {
//     sss = "<span class='badge badge-outline-warning font-12'>"+temp_lang.unpaid+"</span>";
// } else if (btn == 'expired') {
//     sss = "<span class='badge badge-outline-secondary font-12'>"+temp_lang.expired+"</span>";
// }