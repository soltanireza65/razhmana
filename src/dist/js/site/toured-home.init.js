// $('<div>in process...</div>')
//     .addClass('overlay-tour')
//     .fadeTo(0, 0.4)
//     .appendTo('body');
$(document).ready(function () {

    setTimeout(function () {


        if (!$('#install-model').hasClass('show')) {

            const CookieName = 't-home';
            var keys = {37: 1, 38: 1, 39: 1, 40: 1};

            function preventDefault(e) {
                e.preventDefault();
            }

            function preventDefaultForScrollKeys(e) {
                if (keys[e.keyCode]) {
                    preventDefault(e);
                    return false;
                }
            }

// modern Chrome requires { passive: false } when adding event
            var supportsPassive = false;
            try {
                window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
                    get: function () {
                        supportsPassive = true;
                    }
                }));
            } catch (e) {
            }

            var wheelOpt = supportsPassive ? {passive: false} : false;
            var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

// call this to Disable
            function disableScroll() {
                window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
                window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
                window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
                window.addEventListener('keydown', preventDefaultForScrollKeys, false);
            }

// disableScroll()
// call this to Enable
            function enableScroll() {
                window.removeEventListener('DOMMouseScroll', preventDefault, false);
                window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
                window.removeEventListener('touchmove', preventDefault, wheelOpt);
                window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
            }


            var shepherd = new Shepherd.Tour({
                defaultStepOptions: {
                    cancelIcon: {
                        enabled: false
                    },
                    classes: 'shepherd-content',
                    scrollTo: {
                        behavior: 'smooth',
                        block: 'center'
                    }
                },
                // This should add the first tour step
                steps: [
                    {
                        text: tour_vars.home_1,
                        attachTo: {
                            element: '#tj-logo',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            // {
                            //     action: function () {
                            //         return tjBack();
                            //     },
                            //     classes: 'shepherd-button',
                            //     text: tour_vars.back,
                            // },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile'
                    },
                    {
                        text: tour_vars.home_2,
                        attachTo: {
                            element: '#t-home-ground',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile2'
                    },
                    {
                        text: tour_vars.home_3,
                        attachTo: {
                            element: '#t-home-ship',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile3'
                    },
                    {
                        text: tour_vars.home_4,
                        attachTo: {
                            element: '#t-home-air',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile4'
                    },
                    {
                        text: tour_vars.home_5,
                        attachTo: {
                            element: '#t-home-railroad',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile5'
                    },
                    {
                        text: tour_vars.home_6,
                        attachTo: {
                            element: '#t-home-inventory',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile6'
                    },
                    {
                        text: tour_vars.home_9,
                        attachTo: {
                            element: '#t-home-customs',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjCancel();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            },
                            {
                                action: function () {
                                    return tjNext();
                                },
                                text: tour_vars.next
                            }

                        ],
                        id: 'tourprofile6'
                    },

                    {
                        text: tour_vars.home_8,
                        attachTo: {
                            element: '#t-home-question',
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                action: function () {
                                    return tjClose();
                                },
                                secondary: true,
                                text: tour_vars.close
                            },
                            {
                                action: function () {
                                    return tjBack();
                                },
                                classes: 'shepherd-button',
                                text: tour_vars.back,
                            }

                        ],
                        id: 'tourprofile7'
                    }
                ],
                useModalOverlay: true
            });


// var shepherd = setupShepherd();

// $(window).scrollTop(0)
// disableScroll()
            window.scrollTo(0, 0);

            async function test() {
                await shepherd.start();
                setTimeout(async function () {
                    await window.scrollTo(0, 0);
                }, 500)

                disableScroll()
            }

            test()

            function tjCancel() {
                setCookie(CookieName, "shown");
                shepherd.cancel();
                enableScroll()
            }

            function tjNext() {
                shepherd.next();

            }

            function tjBack() {
                shepherd.back();

            }

            function tjClose() {
                shepherd.hide();
                setCookie(CookieName, "shown");
                enableScroll()
            }



            // })
        } else {
            $('#install-model').on('hidden.bs.modal', function () {


                const CookieName = 't-home';
                var keys = {37: 1, 38: 1, 39: 1, 40: 1};

                function preventDefault(e) {
                    e.preventDefault();
                }

                function preventDefaultForScrollKeys(e) {
                    if (keys[e.keyCode]) {
                        preventDefault(e);
                        return false;
                    }
                }

// modern Chrome requires { passive: false } when adding event
                var supportsPassive = false;
                try {
                    window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
                        get: function () {
                            supportsPassive = true;
                        }
                    }));
                } catch (e) {
                }

                var wheelOpt = supportsPassive ? {passive: false} : false;
                var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

// call this to Disable
                function disableScroll() {
                    window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
                    window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
                    window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
                    window.addEventListener('keydown', preventDefaultForScrollKeys, false);
                }

// disableScroll()
// call this to Enable
                function enableScroll() {
                    window.removeEventListener('DOMMouseScroll', preventDefault, false);
                    window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
                    window.removeEventListener('touchmove', preventDefault, wheelOpt);
                    window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
                }


                var shepherd = new Shepherd.Tour({
                    defaultStepOptions: {
                        cancelIcon: {
                            enabled: false
                        },
                        classes: 'shepherd-content',
                        scrollTo: {
                            behavior: 'smooth',
                            block: 'center'
                        }
                    },
                    // This should add the first tour step
                    steps: [
                        {
                            text: tour_vars.home_1,
                            attachTo: {
                                element: '#tj-logo',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                // {
                                //     action: function () {
                                //         return tjBack();
                                //     },
                                //     classes: 'shepherd-button',
                                //     text: tour_vars.back,
                                // },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile'
                        },
                        {
                            text: tour_vars.home_2,
                            attachTo: {
                                element: '#t-home-ground',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile2'
                        },
                        {
                            text: tour_vars.home_3,
                            attachTo: {
                                element: '#t-home-ship',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile3'
                        },
                        {
                            text: tour_vars.home_4,
                            attachTo: {
                                element: '#t-home-air',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile4'
                        },
                        {
                            text: tour_vars.home_5,
                            attachTo: {
                                element: '#t-home-railroad',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile5'
                        },
                        {
                            text: tour_vars.home_6,
                            attachTo: {
                                element: '#t-home-inventory',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjCancel();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                },
                                {
                                    action: function () {
                                        return tjNext();
                                    },
                                    text: tour_vars.next
                                }

                            ],
                            id: 'tourprofile6'
                        },
                        {
                            text: tour_vars.home_8,
                            attachTo: {
                                element: '#t-home-question',
                                on: 'bottom'
                            },
                            buttons: [
                                {
                                    action: function () {
                                        return tjClose();
                                    },
                                    secondary: true,
                                    text: tour_vars.close
                                },
                                {
                                    action: function () {
                                        return tjBack();
                                    },
                                    classes: 'shepherd-button',
                                    text: tour_vars.back,
                                }

                            ],
                            id: 'tourprofile7'
                        }
                    ],
                    useModalOverlay: true
                });


// var shepherd = setupShepherd();

// $(window).scrollTop(0)
// disableScroll()
                window.scrollTo(0, 0);

                async function test() {
                    await shepherd.start();
                    setTimeout(async function () {
                        await window.scrollTo(0, 0);
                    }, 500)

                    disableScroll()
                }

                test()

                function tjCancel() {
                    setCookie(CookieName, "shown");
                    shepherd.cancel();
                    enableScroll()
                }

                function tjNext() {
                    shepherd.next();

                }

                function tjBack() {
                    shepherd.back();

                }

                function tjClose() {
                    shepherd.hide();
                    setCookie(CookieName, "shown");
                    enableScroll()
                }


            })
        }

    }, 500);
});


/*
{
    text: tour_vars.home_7,
        attachTo: {
    element: '#t-home-poster',
        on: 'bottom'
},
    buttons: [
        {
            action: function () {
                return tjCancel();
            },
            secondary: true,
            text: tour_vars.close
        },
        {
            action: function () {
                return tjBack();
            },
            classes: 'shepherd-button',
            text: tour_vars.back,
        },
        {
            action: function () {
                return tjNext();
            },
            text: tour_vars.next
        }

    ],
        id: 'tourprofile6'
},*/
