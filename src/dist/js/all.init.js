function setCookie(key, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (365 * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';path=/' + ';expires=' + expires.toUTCString();
};

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
};


async function delay(func, delay) {
    let timeout;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, delay);
    };
}


document.addEventListener('DOMContentLoaded', function () {
    let targetElement = document.querySelector('.mj-b-video-cover')
    if (targetElement) {
        targetElement.addEventListener('click', function () {
            document.querySelector('video').play();
        });
    }
});

window.onload =  function () {
    document.getElementById('change-lang').addEventListener('click' , function () {
        console.log('test')
        setCookie('login-back-url' ,window.location.href );
        window.location.replace('/lang')
    })
}

