<?php
global $Settings, $lang;

use MJ\Utils\Utils;


/**
 * Get Post By Limit
 */
$langCookie = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $langCookie = $_COOKIE['language'];
} else {
    $langCookie = 'fa_IR';
    setcookie('language', 'fa_IR', time() + STABLE_COOKIE_TIMEOUT, "/");
    User::changeUserLanguageOnChangeLanguage('fa_IR');
}
$resultPostByLimit = Post::getPostByLimit(0, 5, $langCookie);
$dataPostByLimit = [];
if ($resultPostByLimit->status == 200 && !empty($resultPostByLimit->response)) {
    $dataPostByLimit = $resultPostByLimit->response;
}

include_once 'header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');


getHeader($lang['home']);
$sliders = $Settings['p_home_silders'];


/**
 * Get All Cargo
 */
$resultAllCargo = Cargo::getAllCargoByLimit();
$dataAllCargo = [];
if ($resultAllCargo->status == 200 && !empty($resultAllCargo->response)) {
    $dataAllCargo = $resultAllCargo->response;
}
?>
    <main class="container" style="padding-bottom: 180px">
        <!-- filter and city btns-->
        <div class="container">
            <div class="row">
                <div class="mj-p-first-page-btns">
                    <a href="#" id="city-btn">
                        <div class="mj-p-city-choose-btn">
                            <div>انتخاب شهر</div>
                            <img src="../../dist/images/poster/location-dot.svg" alt="">

                        </div>
                    </a>
                    <a href="#" id="filter-btn">
                        <div class="mj-p-filter-btn">
                            <div>جستجوی پیشرفته</div>
                            <img src="../../dist/images/poster/search.svg" alt="">

                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- filter and city btns-->

        <!-- filter and city item-->
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 mj-p-margin col-no-pad mt-2">
                    <div class="mj-p-poster-item-card">
                        <a href="#" class="mj-p-poster-item-content">
                            <div class="mj-p-poster-top-section">
                                <div class="mj-p-poster-image">
                                    <img src="../../dist/images/poster/poster-item-img.jpg" alt="">
                                    <div class="mj-p-poster-fast">فوری</div>
                                </div>
                                <div class="mj-p-poster-details ps-1">
                                    <div class="mj-p-poster-name ">
                                        <div class="mj-p-poster-title text-zip">
                                            حواله کشنده امپاور 1401 صفر
                                        </div>

                                        <span class="d-block">نیم ساعت پیش</span>
                                    </div>

                                    <div class="mj-p-poster-price">
                                        <span class="mj-p-poster-price-unit">تومان</span><span
                                                class="mj-p-poster-price-num">15,000,000 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mj-p-poster-bottom-section">
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/city.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        تبریز
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/road.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        40,000
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img src="../../dist/images/poster/timer.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1401
                                    </div>
                                </div>
                                <div class="mj-p-poster-line"></div>
                                <div class="mj-p-poster-item-feature">
                                    <div class="mj-p-poster-item-feature-icon">
                                        <img id="calender-icon" src="../../dist/images/poster/calendar-week.svg" alt="">
                                    </div>
                                    <div class="mj-p-poster-item-feature-value">
                                        1400/07/12
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>


        </div>


        <!-- filter and city item-->

    </main>
<?php
getFooter('', false);
?>