let temp_lang = JSON.parse(var_lang);



/**
 * Strat export files
 */
$('#submit_export_settings').on('click', function () {
    const BTNN = Ladda.create(document.querySelector('#submit_export_settings'));
    let searchIDs = $('input:checked').map(function(){
        return $(this).data('tj-name');
    });

    let data = {
        action: 'export-settings',
        files: searchIDs.get(),
        token: $('#token').val().trim(),
    };

    BTNN.start();
    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();
            let ddata = jQuery.parseJSON(data).status;
            if (ddata == 200) {
                window.open(jQuery.parseJSON(data).response, '_blank');
            } else if (ddata == -2) {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_export);
            }
        }
    });

});


let allCheckBox = $("[data-tj-name]");
allCheckBox.each(function () {
    $(this).change(function () {

        if ($(this).data('tj-name')=='all'){
            allCheckBox.prop('checked',false);
            $('[data-tj-name="all"]').prop('checked',true)
        }else{
            // $(this).prop('checked',true)
            $('[data-tj-name="all"]').prop('checked',false)
        }


        let searchIDs = $('input:checked').map(function(){
            return $(this).data('tj-name');
        });
        if (searchIDs.get().length>0){
            $('#submit_export_settings').attr('disabled',false)
        }else{
            $('#submit_export_settings').attr('disabled',true)
        }
    });

});


/**
 * End export files
 */


$('#set-admin-cargo-out').select2({
    minimumResultsForSearch: Infinity,
    maximumSelectionLength: 10,
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
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
$('#set-admin-cargo-in').select2({
    minimumResultsForSearch: Infinity,
    maximumSelectionLength: 10,
    language: {
        noResults: function () {
            return $("<span class='mj-custom-select-item mj-font-13'>" + lang_vars.u_no_result_found + "</span>");
        }
    },
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

$("#submit_all_settings").on('click', function () {

    const BTNN = Ladda.create(document.querySelector('#submit_all_settings'));

    $("#submit_all_settings").attr('disabled', true);
    BTNN.start();
    let data = {
        action: 'settings-security-all',
        set_admin_cargo_out: $('#set-admin-cargo-out').val(),
        set_admin_cargo_in: $('#set-admin-cargo-in').val(),

        token:  $('#token').val().trim(),
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            BTNN.remove();
            $("#submit_all_settings").attr('disabled', false);
            if (data == 'successful') {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else {
                toastNotic(temp_lang.warning, temp_lang.error_mag, 'warning');
            }
        }
    });
});