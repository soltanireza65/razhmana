function sendAjaxRequest(method, url, data, displayDialog = false, title = '', icon = 'success', desc = '', btn_text) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: method,
            url: url,
            data: JSON.stringify(data),
            success: function (response) {

                let result = JSON.parse(response)

                if (result.status == 200) {
                    if (displayDialog) {

                        Swal.fire({
                            title: title,
                            text: desc,
                            icon: icon,
                            confirmButtonText: btn_text,
                        }).then(() => {

                        });
                    }

                }
                else if (result.status == 411) {
                    if (displayDialog) {
                        Swal.fire({
                            title: 'خطا',
                            text: 'پارامتر ها به طور صحیح ارسال نشده است ',
                            icon: 'error',
                            confirmButtonText: btn_text,
                        }).then(() => {

                        });
                    }

                }
                else if (result.status == 403) {
                    if (displayDialog) {
                        Swal.fire({
                            title: 'خطا',
                            text: 'شما به این بخش دسترسی ندارید ',
                            icon: 'error',
                            confirmButtonText: btn_text,
                        }).then(() => {

                        });
                    }

                }
                resolve(result);
            },
            error: function (xhr, textStatus, errorThrown) {

                Swal.fire({
                    title: 'خطا',
                    text: 'خطا در ارتباط',
                    icon: 'error',
                    confirmButtonText: 'تایید',
                }).then(() => {

                });
                resolve(result);
            }
        });
    });
}


function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

function persianToEnglishNumber(persianNumber) {
    const persianToEnglishMap = {
        "۰": "0",
        "۱": "1",
        "۲": "2",
        "۳": "3",
        "۴": "4",
        "۵": "5",
        "۶": "6",
        "۷": "7",
        "۸": "8",
        "۹": "9"
    };

    let englishNumber = "";
    for (let i = 0; i < persianNumber.length; i++) {
        const char = persianNumber.charAt(i);
        if (persianToEnglishMap[char]) {
            englishNumber += persianToEnglishMap[char];
        } else {
            englishNumber += char;
        }
    }

    return englishNumber;
}

function downloadUrl(url) {
    // Create an anchor element
    var anchor = document.createElement('a');
    anchor.href = url;
    anchor.target = '_blank'; // Open in a new tab/window
    // Trigger a click event on the anchor element to start the download
    anchor.click();
}


function getFileNameFromUrl(url) {
    const parsedUrl = new URL(url);
    const pathname = parsedUrl.pathname;
    const pathSegments = pathname.split("/");
    const fileName = pathSegments[pathSegments.length - 1];
    return fileName;
}

function clearCookie(cookieName) {
    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

function clearAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var cookieName = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
}

async function sendAjaxRequest(method, url, data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: method,
            url: url,
            data: JSON.stringify(data),
            success: function (response) {
                resolve(response);
            },
            error: function (xhr, textStatus, errorThrown) {
                reject(xhr);
            }
        });
    });
}

function getCities(country_id) {
    return new Promise((resolve, reject) => {
        let params = {
            action: 'get-cities-list',
            country_id: country_id,
            token: token
        };
        sendAjaxRequest('POST', '/api/adminAjax', params)
            .then(response => {
                console.log(response)
                resolve(response);
            })
            .catch(error => {
                reject(error);
            });
    });
}
function getCountries() {
    return new Promise((resolve, reject) => {
        let params = {
            action: 'get-country-list',
            token: token
        };
        sendAjaxRequest('POST', '/api/adminAjax', params)
            .then(response => {
                resolve(response);
            })
            .catch(error => {
                reject(error);
            });
    });
}


$.notify.addStyle('mj-notice', {
    html: "<div><span data-notify-text></span>!</div>",
    classes: {
        error: {
            "color": "#D91F11",
            "background-color": "#FADCD9",
            "padding": "5px 10px",
            "border-radius": "10px",
            "bottom": "70px",
            "border":"1px solid #D91F11"
        },
        success: {
            "color": "#077D55",
            "background-color": "#C7EBD1",
            "padding": "5px 10px",
            "border-radius": "10px",
            "bottom": "70px",
            "border":"1px solid #077D55"
        },
        warn: {
            "color": "#302d4f",
            "background-color": "#FAF6CF",
            "padding": "5px 10px",
            "border-radius": "10px",
            "bottom": "70px",
            "border":"1px solid #F5C518"
        }
    }
});