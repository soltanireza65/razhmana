$(document).ready(async function () {
    let country_select2 = $('#country')
    let city_select2 = $('#city')
    let submit_office = $('#submit-office')
    let office_mobile = $('#office-mobile')
    let office_email = $('#office-email')
    country_select2.select2()
    city_select2.select2()
    let countries = await getCountries();
    countries = JSON.parse(countries)
    if (countries.status == 200) {
        let countriesOutput = '<option value=""></option>';
        countries.response.forEach(function (item) {
            countriesOutput += '<option value="' + item.country_id + '">' + item.country_name + '</option>';
        })
        country_select2.html(countriesOutput)

    }
    country_select2.on('change', async function () {
        let cities = await getCities($(this).val());
        cities = JSON.parse(cities)
        if (cities.status == 200) {
            let citiesOutput = '<option value=""></option>';
            cities.response.forEach(function (item) {
                citiesOutput += '<option value="' + item.city_id + '">' + item.city_name + '</option>';
            })
            city_select2.html(citiesOutput)

        }
    });


    submit_office.click(function () {


        if (!office_mobile.val()) {
            $.notify('لطفا موبایل را وارد نمائید ', {
                showDuration: 300,
                className: 'error',
                hideDuration: 300,
                position: 'bottom left',
                style: 'mj-notice',
            })
        }
        if (!office_email.val()) {
            $.notify('لطفا ایمیل را وارد نمائید ', {
                showDuration: 300,
                className: 'error',
                hideDuration: 300,
                position: 'bottom left',
                style: 'mj-notice',
            })
        }
        if (!city_select2.val()) {
            $.notify('لطفا شهر را وارد نمائید ', {
                showDuration: 300,
                className: 'error',
                hideDuration: 300,
                position: 'bottom left',
                style: 'mj-notice',
            })
        }

        if (city_select2.val() && office_mobile.val() && office_email.val()) {
            let params = {
                action: 'insert-office',
                mobile : office_mobile.val(),
                email : office_email.val(),
                city : city_select2.val()
            }
            sendAjaxRequest('POST', '/api/adminAjax', params)
                .then(response => {
                    console.log(response)
                    let result = JSON.parse(response)
                    if (result.status == 200){
                        // window.location.href = '/admin/office'
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        }
    })
})