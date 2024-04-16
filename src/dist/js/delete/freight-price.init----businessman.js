const originCity = $('#origin-city');
const destinationCity = $('#destination-city');
const category = $('#category');
const currency = $('#currency');


originCity.select2({
    dropdownParent: $('.mj-custom-select.origin-city'),
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});


originCity.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1 || $(this).val() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


destinationCity.select2({
    dropdownParent: $('.mj-custom-select.destination-city'),
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});


destinationCity.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1 || $(this).val() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


category.select2({
    dropdownParent: $('.mj-custom-select.category'),
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});


category.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1 || $(this).val() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


currency.select2({
    dropdownParent: $('.mj-custom-select.currency'),
    templateResult: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    },
    templateSelection: function (data) {
        const title = data.text;

        return $(`
            <span class="mj-custom-select-item mj-font-13">
                ${title}
            </span>
        `);
    }
});


currency.on('change', function () {
    $(this).parent().find('.select2-selection.select2-selection--single').removeClass('border-danger');
    if ($(this).val() == -1 || $(this).val() == '') {
        $(this).parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    }
});


$('#ground-inquiry').on('click', function () {
    if (originCity.val() == -1 || originCity.val() == '') {
        originCity.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else if (destinationCity.val() == -1 || destinationCity.val() == '') {
        destinationCity.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else if (category.val() == -1 || category.val() == '') {
        category.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else if (currency.val() == -1 || currency.val() == '') {
        currency.parent().find('.select2-selection.select2-selection--single').addClass('border-danger');
    } else {
        sendNotice(lang_vars.alert_info, lang_vars.freight_pending, 'info', 2500);

        const params = {
            action: 'ground-freight-price',
            origin: originCity.val(),
            originName: originCity.select2('data')[0].text,
            destination: destinationCity.val(),
            destinationName: destinationCity.select2('data')[0].text,
            category: category.val(),
            categoryName: category.select2('data')[0].text,
            currency: currency.val(),
            currencyName: currency.select2('data')[0].text
        };


        $.ajax({
            url: '/api/ajax',
            type: 'POST',
            data: JSON.stringify(params),
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    let template = $('#template-result').html();
                    if (json.status == 200) {
                        let text = lang_vars['freight_result_message'].replaceAll('#FROM#', originCity.select2('data')[0].text);
                        text = text.replaceAll('#TO#', destinationCity.select2('data')[0].text);
                        text = text.replaceAll('#TYPE#', category.select2('data')[0].text);
                        text = text.replaceAll('#AMOUNT#', new Intl.NumberFormat().format(json.response));
                        text = text.replaceAll('#CURRENCY#', currency.select2('data')[0].text);
                        const html = `
                        <h4 class="lh-lg mj-fw-400 mj-font-14">${text}</h4>
                        `;
                        template = template.replaceAll('#CONTENT#', html);
                    } else {
                        let text = lang_vars['freight_result_not_found'].replaceAll('#FROM#', originCity.select2('data')[0].text);
                        text = text.replaceAll('#TO#', destinationCity.select2('data')[0].text);
                        text = text.replaceAll('#TYPE#', category.select2('data')[0].text);
                        text = text.replaceAll('#CURRENCY#', currency.select2('data')[0].text);
                        const html = `
                        <h4 class="text-danger lh-lg mj-fw-400 mj-font-14">${text}</h4>
                        `;
                        template = template.replaceAll('#CONTENT#', html);
                    }
                    $('body').append(template);
                    const _modal = new bootstrap.Modal($('#modal-result'));
                    _modal.show();

                    $('#modal-result').on('hide.bs.modal', function () {
                        $(this).remove();
                    });
                } catch (e) {
                    sendNotice(lang_vars.alert_error, lang_vars.login_unknown_error, 'error', 3500);
                }
            }
        });

    }
});