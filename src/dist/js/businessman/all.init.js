function sendNotice(title, desc, icons = 'error', duration = 5000) {
    let bgColors = '#ec223d';
    let loaderBgs = '#f16477';
    if (icons == 'success') {
        bgColors = '#0fce5b';
        loaderBgs = '#57dc8c';
    } else if (icons == 'warning') {
        bgColors = '#ee9d10';
        loaderBgs = '#f3ba57';
    } else if (icons == 'info') {
        bgColors = '#106bee';
        loaderBgs = '#52aaf8';
    } else {
        bgColors = '#ec223d';
        loaderBgs = '#f16477';
    }


    $.toast({
        text: desc, showHideTransition: 'fade', // It can be plain, fade or slide
        bgColor: bgColors, // Background color for toast
        textColor: '#FFF', // text color
        allowToastClose: false, // Show the close button or not
        hideAfter: duration, // `false` to make it sticky or time in miliseconds to hide after
        stack: 5, // `fakse` to show one stack at a time count showing the number of toasts that can be shown at once
        textAlign: 'right', // Alignment of text i.e. left, right, center
        icon: icons, //warning success info error
        heading: title, loader: true, // Whether to show loader or not. True by default
        loaderBg: loaderBgs, position: 'top-left' // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values to position the toast on page
    })
}

let persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g],
    arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g],
    fixNumbers = function (str) {
        if (typeof str === 'string') {
            for (var i = 0; i < 10; i++) {
                str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
            }
        }
        return str;
    };



$('.btn-close').click(function () {
    var videos = document.getElementsByTagName("video");
    for (var i = 0; i < videos.length; i++) {
        videos[i].pause();
    }
})