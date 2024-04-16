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
        text: desc,
        showHideTransition: 'fade', // It can be plain, fade or slide
        bgColor: bgColors, // Background color for toast
        textColor: '#FFF', // text color
        allowToastClose: false, // Show the close button or not
        hideAfter: duration, // `false` to make it sticky or time in miliseconds to hide after
        stack: 5, // `fakse` to show one stack at a time count showing the number of toasts that can be shown at once
        textAlign: 'right', // Alignment of text i.e. left, right, center
        icon: icons, //warning success info error
        heading: title,
        loader: true, // Whether to show loader or not. True by default
        loaderBg: loaderBgs,
        position: 'top-left' // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values to position the toast on page
    })
}
function convertToEnglishNumber(text) {
    const NUMERAL_SYSTEMS = {
        '٠': '0', // Arabic numerals
        '١': '1',
        '٢': '2',
        '٣': '3',
        '٤': '4',
        '٥': '5',
        '٦': '6',
        '٧': '7',
        '٨': '8',
        '٩': '9',
        '۰': '0', // Persian (Farsi) numerals
        '۱': '1',
        '۲': '2',
        '۳': '3',
        '۴': '4',
        '۵': '5',
        '۶': '6',
        '۷': '7',
        '۸': '8',
        '۹': '9',
    };
    let englishNumber = '';
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        if (NUMERAL_SYSTEMS.hasOwnProperty(char)) {
            englishNumber += NUMERAL_SYSTEMS[char];
        } else {
            englishNumber += char;
        }
    }
    return englishNumber;
}



$('.btn-close').click(function () {
    var videos = document.getElementsByTagName("video");
    for (var i = 0; i < videos.length; i++) {
        videos[i].pause();
    }
})

