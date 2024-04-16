$(document).ready(function () {

})
$('#buy-btn').click(function () {
    if ($('#currency-select').val().trim() == "") {
        $('.mj-select2 .select2-container--default .select2-selection--single').css('border', '1px solid #cc0f0f')
    } else {
        $('.mj-select2 .select2-container--default .select2-selection--single').css('border', '1px solid #989898')
    }
})
$('.buy-modal').click(function () {
    initModal($(this))
    $('#tradeModal').modal('show')
    $('.mj-buy-tab-link').addClass('active')
    $('.mj-sell-tab-link').removeClass('active')
    $('.mj-sell-tab-content').removeClass('active')
    $('.mj-buy-tab-content').addClass('active')
    $('.mj-sell-tab-content').removeClass('show')
    $('.mj-buy-tab-content').addClass('show')
})
$('.sell-modal').click(function () {
    initModal($(this))
    $('#tradeModal').modal('show')
    $('.mj-sell-tab-link').addClass('active')
    $('.mj-buy-tab-link').removeClass('active')
    $('.mj-sell-tab-content').addClass('active')
    $('.mj-buy-tab-content').removeClass('active')
    $('.mj-sell-tab-content').addClass('show')
    $('.mj-buy-tab-content').removeClass('show')

})


$('#buy-count').keyup(function () {
    let price = $('#buy-price').attr('price-buy');
    let count = $(this).val()
    $('#result-buy').text((price * count).toLocaleString());
})
$('#sell-count').keyup(function () {
    let price = $('#sell-price').attr('price-sell');
    let count = $(this).val()
    $('#result-sell').text((price * count).toLocaleString());
})
let request_type
let price_id
let price_buy
let price_sell

function initModal(input) {
    request_type = $(input).data('type-request')
    price_id = $(input).data('price-id')
    price_buy = $(input).data('price-buy')
    price_sell = $(input).data('price-sell')
    $('#sell-price').text(price_sell.toLocaleString())
    $('#sell-price').attr('price-sell', price_sell)
    $('#buy-price').text(price_buy.toLocaleString())
    $('#buy-price').attr('price-buy', price_buy)
    let output = ' ';

    let index_ir = ir_prices.findIndex(item => item.id == price_id);
    let index_ru = ru_prices.findIndex(item => item.id == price_id);
    let index_du = du_prices.findIndex(item => item.id == price_id);
    let index_tr = tr_prices.findIndex(item => item.id == price_id);
    console.log(index_ir)
    console.log(index_ru)
    console.log(index_du)
    console.log(index_tr)
    console.log(ir_prices)
    console.log(price_id)
    if (request_type == 'ir') {
        if (ir_prices[index_ir]){
            output = ir_prices[index_ir][slug]
        }
    } else if (request_type == 'ru') {
        if (ru_prices[index_ru]){
            output = ru_prices[index_ru][slug]
        }
    } else if (request_type == 'du') {
        if (du_prices[index_du]){
            output = du_prices[index_du][slug]
        }
    } else if (request_type == 'tr') {
        if (tr_prices[index_tr]){
            output = tr_prices[index_tr][slug]
        }
    }

    $('#currency-sell-select').html(output)
    $('#currency-select').html(output)
}


$('#sell-btn').click(function () {
    if ($('#sell-count').val() && $('#sell-count').val()!=0 ){
        let params = {
            action: 'submit-exchange-request',
            request_type: request_type,
            price_id: price_id,
            price_buy: price_buy,
            price_sell: price_sell,
            count: $('#sell-count').val(),
            request_side: 'sell'
        };
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                let result  =  JSON.parse(response)
                if (result.status ==  200){
                    $('#l-player').html(' <lottie-player  src="/dist/lottie/done.json" background="transparent" speed="1" loop autoplay></lottie-player>')

                    $('#request-result').text(lang_vars.exchange_request_success)
                    $('#result-modal').modal('show')
                    $('#tradeModal').modal('hide')
                }else{
                    $('#l-player').html(' <lottie-player  src="/dist/lottie/reject.json" background="transparent" speed="1" loop autoplay></lottie-player>')
                    $('#request-result').text(lang_vars.exchange_request_error)
                    $('#result-modal').modal('show')
                    $('#tradeModal').modal('hide')

                }
                setTimeout(function () {
                    // window.location.reload()
                },5000)
            }
        })
    }else {
        $('#l-player').html(' <lottie-player  src="/dist/lottie/reject.json" background="transparent" speed="1" loop autoplay></lottie-player>')
        $('#request-result').text(lang_vars.exchange_request_count_error)
        $('#result-modal').modal('show')
        $('#tradeModal').modal('hide')
    }

})
$('#buy-btn').click(function () {
    if ($('#buy-count').val() &&  $('#buy-count').val() !=0){
        let params = {
            action: 'submit-exchange-request',
            request_type: request_type,
            price_id: price_id,
            price_buy: price_buy,
            price_sell: price_sell,
            count: $('#buy-count').val(),
            request_side: 'buy'
        };
        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                let result  =  JSON.parse(response)
                if (result.status ==  200){
                    $('#l-player').html(' <lottie-player  src="/dist/lottie/done.json" background="transparent" speed="1" loop autoplay></lottie-player>')

                    $('#request-result').text(lang_vars.exchange_request_success)
                    $('#result-modal').modal('show')
                    $('#tradeModal').modal('hide')
                }else{
                    $('#l-player').html(' <lottie-player  src="/dist/lottie/reject.json" background="transparent" speed="1" loop autoplay></lottie-player>')
                    $('#request-result').text(lang_vars.exchange_request_error)
                    $('#result-modal').modal('show')
                    $('#tradeModal').modal('hide')

                }
                setTimeout(function () {
                    // window.location.reload()
                },5000)
            }
        })
    }else{
        $('#l-player').html(' <lottie-player  src="/dist/lottie/reject.json" background="transparent" speed="1" loop autoplay></lottie-player>')
        $('#request-result').text(lang_vars.exchange_request_count_error)
        $('#result-modal').modal('show')
        $('#tradeModal').modal('hide')
    }

})