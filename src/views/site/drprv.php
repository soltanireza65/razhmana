<?php
global $Settings, $lang;

use MJ\Utils\Utils;

include_once 'header-footer.php';
enqueueStylesheet('qrcodecss', '/dist/libs/bootstrap/css/bootstrap.min.css');
enqueueStylesheet('swipercss', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('qrcodecss', '/dist/css/drprv.css');
enqueueScript('swiperjs', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('drprve-js', '/dist/js/site/drprv.js');

if (isset($_COOKIE['language']) && substr($_COOKIE['language'], 0, 2) != 'fa') {

    enqueueStylesheet('qrcodecss', '/dist/css/personel-ltr.css');

}
$init_lang = 'fa';
if (isset($_COOKIE['language'])) {
    $init_lang = substr($_COOKIE['language'], 0, 2);
}

enqueueStylesheet('BScss', '/dist/libs/bootstrap/css/bootstrap.rtl.min.css');

enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
enqueueScript('BSjs', '/dist/libs/bootstrap/js/bootstrap.bundle.min.js');

getHeader('', '', '', false);

?>

    <style>
        .swiper{
            padding:10px 0 10px 0 !important;
        }
    </style>
    <main>
        <div class="mobile">
            <div class="mj-doctor-header-image">
                <img src="/dist/images/dr/drparvaresh.png" alt="dr">
            </div>
            <div class="mj-doctor-name-card">
                <!--            <span>دکتر</span>-->
                <!--            <span>رشیـــد</span>-->
                <!--            <span>پـــرورش</span>-->
                <span>Dr.</span>
                <span>Rashid</span>
                <span>Parvaresh</span>
            </div>
            <div class="mj-doctor-contact-us-card">
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-odd">
                    <div class="mj-doctor-contact-item-title">Mobile</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-mobile icon"></div>
                        <div class="mj-doctor-contact-item-detail">
                            +989143302964
                        </div>
                    </div>
                </a>
                <a href="mailto:drparvaresh@drph.com" class="mj-doctor-contact-item doctor-even">
                    <div class="mj-doctor-contact-item-title">Email</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-envelope icon"></div>
                        <div class="mj-doctor-contact-item-detail">
                            drparvaresh@drph.com
                        </div>
                    </div>
                </a>
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-odd">
                    <div class="mj-doctor-contact-item-title">Whatsapp</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-whatsapp icon icon-brand"></div>
                        <div class="mj-doctor-contact-item-detail">
                            +989143302964
                        </div>
                    </div>
                </a>
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-even">
                    <div class="mj-doctor-contact-item-title">Telegram</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-telegram icon icon-brand"></div>
                        <div class="mj-doctor-contact-item-detail">
                            @Drparvaresh
                        </div>
                    </div>
                </a>
            </div>

            <div class="mj-doctor-projects-carousel">
                <div class="mj-doctor-projects-carousel-title">بنیانگذار</div>
                <div class="mj-carousel-object1"></div>
                <div class="mj-carousel-object2"></div>
                <div class="mj-projects-carousel">

                    <div class="swiper mySwiper project-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Absar.png" alt="absar">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/almali.png" alt="almali">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/H2F.png" alt="h2f">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Hygifine.png" alt="hygifine">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Kalabar.png" alt="kalabar">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Lubex.png" alt="lubex">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/nti-civil.png" alt="nti-civil">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Nti%20-Road.png" alt="nti-road">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/STC.png" alt="stc">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/TMS.png" alt="tms">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Ay%20yildiz%20seven.png" alt="ayyildiz">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/STC.png" alt="absar">
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="screen">
            <div class="mj-doctor-round-bg">

            </div>
            <img class="mj-doctor-header-image" src="/dist/images/dr/drparvaresh.png" alt="dr">

            <div  class="mj-doctor-name-card">
                            <span>دکتر</span>
                            <span>رشیـــد</span>
                            <span>پـــرورش</span>
<!--                <span>Dr.</span>-->
<!--                <span>Rashid</span>-->
<!--                <span>Parvaresh</span>-->
            </div>


            <div class="mj-doctor-projects-carousel">
                <div class="mj-doctor-projects-carousel-title">بنیانگذار</div>
                <div class="mj-carousel-object1"></div>
                <div class="mj-carousel-object2"></div>
                <div class="mj-projects-carousel">

                    <div class="swiper mySwiper project-swiper2">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Absar.png" alt="absar">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/almali.png" alt="almali">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/H2F.png" alt="h2f">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Hygifine.png" alt="hygifine">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Kalabar.png" alt="kalabar">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Lubex.png" alt="lubex">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/nti-civil.png" alt="nti-civil">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Nti%20-Road.png" alt="nti-road">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/STC.png" alt="stc">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/TMS.png" alt="tms">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/Ay%20yildiz%20seven.png" alt="ayyildiz">
                            </div>
                            <div class="swiper-slide">
                                <img src="/dist/images/dr/STC.png" alt="absar">
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
            <div class="mj-doctor-contact-us-card">
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-odd">
                    <div class="mj-doctor-contact-item-title">Mobile</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-mobile icon"></div>
                        <div class="mj-doctor-contact-item-detail">
                            +989143302964
                        </div>
                    </div>
                </a>
                <a href="mailto:drparvaresh@drph.com" class="mj-doctor-contact-item doctor-even">
                    <div class="mj-doctor-contact-item-title">Email</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-envelope icon"></div>
                        <div class="mj-doctor-contact-item-detail">
                            drparvaresh@drph.com
                        </div>
                    </div>
                </a>
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-odd">
                    <div class="mj-doctor-contact-item-title">Whatsapp</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-whatsapp icon icon-brand"></div>
                        <div class="mj-doctor-contact-item-detail">
                            +989143302964
                        </div>
                    </div>
                </a>
                <a href="tel:+989143302964" class="mj-doctor-contact-item doctor-even">
                    <div class="mj-doctor-contact-item-title">Telegram</div>
                    <div class="mj-doctor-contact-item-info">
                        <div class="fa-telegram icon icon-brand"></div>
                        <div class="mj-doctor-contact-item-detail">
                            @Drparvaresh
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </main>

<?php
getFooter('', false);