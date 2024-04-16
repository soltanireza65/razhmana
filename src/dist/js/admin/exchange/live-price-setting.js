$('.mj-filter-switcher input').change( function () {

    if ($('input[name="filter-switch2"]').prop('checked')) {
        $('.minus-arz').prop("disabled", false)
        $('.plus-arz').prop("disabled", false)
        $('.mj-default-price-submit').prop("disabled", false)

    } else {
        $('.minus-arz').prop("disabled", true)
        $('.plus-arz').prop("disabled", true)
        $('.mj-default-price-submit').prop("disabled", true)

    }
});

$(document).ready(function () {
    $('.mj-status-input label').click(function () {
        var cardPriceID = $(this).data('label-id').trim()
        var cardInputs = $('input[data-arz-input=' + cardPriceID + ']')
        var priceInputID = $(".mj-arz-inputs-setting[data-input-id='" + cardPriceID + "']")
        var priceInputID2 = $(".mj-arz-inputs-setting[data-input-id='" + cardPriceID + "']").data('input-id')
        console.log(cardInputs)

        if (cardPriceID == priceInputID2 && cardInputs.prop('checked') !== true) {
            priceInputID.show('slow')
        } else {
            priceInputID.hide('slow')
        }




        // console.log($('.mj-arz-card-main').find(cardPriceId == inputIdMain) )

    })
    $(".mj-arz-submit").click(function () {

        var price_id = $(this).data("price-id");


        var ir_price_status = $("input[name='ir-arz-status-" + price_id + "']").prop('checked');
        var ru_price_status = $("input[name='ru-arz-status-" + price_id + "']").prop('checked');
        var du_price_status = $("input[name='du-arz-status-" + price_id + "']").prop('checked');
        var tr_price_status = $("input[name='tr-arz-status-" + price_id + "']").prop('checked');

        ir_price_status= (ir_price_status === true) ? 'active' :'inactive';
        ru_price_status= (ru_price_status === true) ? 'active' :'inactive';
        du_price_status= (du_price_status === true) ? 'active' :'inactive';
        tr_price_status= (tr_price_status === true) ? 'active' :'inactive';

        var ir_plus_value = $(".ir-plus-arz[data-price-id='" + price_id + "']").val();
        var ru_plus_value = $(".ru-plus-arz[data-price-id='" + price_id + "']").val();
        var du_plus_value = $(".du-plus-arz[data-price-id='" + price_id + "']").val();
        var tr_plus_value = $(".tr-plus-arz[data-price-id='" + price_id + "']").val();
        var ir_minus_value = $(".ir-minus-arz[data-price-id='" + price_id + "']").val();
        var ru_minus_value = $(".ru-minus-arz[data-price-id='" + price_id + "']").val();
        var du_minus_value = $(".du-minus-arz[data-price-id='" + price_id + "']").val();
        var tr_minus_value = $(".tr-minus-arz[data-price-id='" + price_id + "']").val();


        var token = $("#token-price").val();

        let params = {
            action: 'change-price-status',
            price_id: price_id,
            ir_price_status: ir_price_status,
            ru_price_status: ru_price_status,
            du_price_status: du_price_status,
            tr_price_status: tr_price_status,
            ir_plus_value: ir_plus_value,
            ru_plus_value: ru_plus_value,
            du_plus_value: du_plus_value,
            tr_plus_value: tr_plus_value,
            ir_minus_value: ir_minus_value,
            ru_minus_value: ru_minus_value,
            du_minus_value: du_minus_value,
            tr_minus_value: tr_minus_value,
            token: token

        }
        sendAjaxRequest('POST', '/api/adminAjax', params, false)
            .then(response => {
                if (response.status == 200) {
                    toastNotic(lang_vars.alert_success, lang_vars.alert_success_operations, 'success');
                } else {
                    toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                toastNotic(lang_vars.alert_error, lang_vars.alert_error_operations, 'error');
            });
    });
});