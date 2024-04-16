let dashboard = {
    id: "dashboard",
    i18n: {
        nextBtn: "بعدی",
        prevBtn: "قبلی",
        doneBtn: "تمام",
        skipBtn: "بستن",
        closeTooltip: "بستن",
        stepNums: ["1", "2", "3"]
    },
    steps: [{
        target: "my-toured-1",
        title: "خوش آمدید",
        content: "به سامانه حمل و نقل بین المللی انتیرپ خوش آمدید.",
        placement: "bottom",
        yOffset: 0,
        xOffset: 0,
        width: 200,
        // delay :1500,
        // showPrevButton   :false,
        // showNextButton   :false,
        // nextOnTargetClick  :true,
        arrowOffset: "20",
        isRtl: true,
        zindex: 1001
    }, {
        target: "my-toured-2",
        title: $('#my-toured-2').data('tj-title'),
        content: $('#my-toured-2').data('tj-desc'),
        placement: "bottom",
        width: 200,
        yOffset: 0,
        xOffset: 50,
        arrowOffset: 20,
        // arrowOffset: "180",
        // showCTAButton  :false,
        isRtl: true,
        zindex: 1001
    }, {
        target: "my-toured-3",
        title: $('#my-toured-3').data('tj-title'),
        content: $('#my-toured-3').data('tj-desc'),
        placement: "bottom",
        isRtl: true,
        width: 200,
        yOffset: 0,
        xOffset: -110,
        arrowOffset: 130,
        zindex: 1001,
        // onNext : function () {
        //     alert('666')
        // }
    }, {
        target: "my-toured-4",
        title: "فقط همینا نیستند!!!",
        content: "ما در کنار شما و همراه و آرامش برایتان خواهیم ماند.",
        placement: "top",
        zindex: 1001,
        width: 200,
        isRtl: true,
        yOffset: 0,
        xOffset: 30,
        arrowOffset: "center",
        onEnd: function () {
            setCookie("toured", "shown");
        },
    }],
    // onStart: function() {
    //     // alert('444444')
    // },
    showPrevButton: !0,
    onEnd: function () {
        setCookie("toured", "shown");
    },
    onClose: function () {
        setCookie("toured", "shown");
    }
    // scrollTopMargin: 500
}

hopscotch.startTour(dashboard);



var anno2 = new Anno({
    target: '#my-toured-1', // second block of code
    position: {
        top: '20px',
        right: '0px'
    },
    autoFocusLastButton: false,
    content: 'You can specify where you want each anno to appear.',
    buttons: [
        {
            text: 'Open HN',
            click: function (anno, evt) {
                anno2.hide();
                anno1.show();
                console.log(evt);
            }
        }, {
            text: 'Sweet',
            className: 'anno-btn-low-importance',
            click: function (anno, evt) {
                anno.hide()
            }
        }
    ]
});
const anno1 = new Anno({
    target: '#my-toured-2',
    position: 'left',
    content: "Use 'top', 'left', 'bottom' and 'right', just like CSS.",
});

// anno2.show()
