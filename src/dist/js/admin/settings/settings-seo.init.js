let temp_lang = JSON.parse(var_lang);

$("#btnSubmit").on('click', function () {
    const submit_overall = Ladda.create(document.querySelector('#btnSubmit'));
    let seo_home = $('#seo_home').val().trim();
    let seo_user_laws = $('#seo_user_laws').val().trim();
    let seo_404 = $('#seo_404').val().trim();
    let seo_about_us = $('#seo_about_us').val().trim();
    let seo_contact_us = $('#seo_contact_us').val().trim();
    let seo_developer = $('#seo_developer').val().trim();
    let seo_user_faq = $('#seo_user_faq').val().trim();
    let seo_blog = $('#seo_blog').val().trim();
    let seo_robots = $('#seo_robots').val().trim();
    let token = $('#token').val().trim();

    $("#btnSubmit").attr('disabled', 'disabled');
    submit_overall.start();
    let data = {
        action: 'settings-seo',
        seo_home: seo_home,
        seo_user_laws: seo_user_laws,
        seo_404: seo_404,
        seo_about_us: seo_about_us,
        seo_contact_us: seo_contact_us,
        seo_developer: seo_developer,
        seo_user_faq: seo_user_faq,
        seo_blog: seo_blog,
        seo_robots: seo_robots,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            submit_overall.remove();
            $("#btnSubmit").removeAttr('disabled');
            if (data == 'successful') {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });
});

$("#submit_sitemap").on('click', function () {
    const submit_overall = Ladda.create(document.querySelector('#submit_sitemap'));

    let sitemap_all = $('#sitemap_all').val().trim();
    let sitemap_blog = $('#sitemap_blog').val().trim();
    let sitemap_academy = $('#sitemap_academy').val().trim();
    let sitemap_cargo_out = $('#sitemap_cargo_out').val().trim();
    let sitemap_cargo_in = $('#sitemap_cargo_in').val().trim();
    let sitemap_poster = $('#sitemap_poster').val().trim();

    let token = $('#token').val().trim();

    $("#submit_sitemap").attr('disabled', true);
    submit_overall.start();
    let data = {
        action: 'settings-sitemap',
        sitemap_all: sitemap_all,
        sitemap_blog: sitemap_blog,
        sitemap_academy: sitemap_academy,
        sitemap_cargo_out: sitemap_cargo_out,
        sitemap_cargo_in: sitemap_cargo_in,
        sitemap_poster: sitemap_poster,
        token: token,
    };

    $.ajax({
        type: 'POST',
        url: '/api/adminAjax',
        data: JSON.stringify(data),
        success: function (data) {
            submit_overall.remove();
            $("#submit_sitemap").attr('disabled', false);
            if (data == 'successful') {
                toastNotic(temp_lang.successful, temp_lang.successful_update_mag, 'success');
            } else if (data == "token_error") {
                toastNotic(temp_lang.error, temp_lang.token_error);
            } else {
                toastNotic(temp_lang.error, temp_lang.error_mag);
            }
        }
    });
});