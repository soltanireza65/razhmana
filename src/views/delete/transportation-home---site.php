<?php
global $Settings, $lang;

use MJ\Security\Security;
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
//$resultPostByLimit = Post::getPostByLimit(0, 5, $langCookie);
//$dataPostByLimit = [];
//if ($resultPostByLimit->status == 200 && !empty($resultPostByLimit->response)) {
//    $dataPostByLimit = $resultPostByLimit->response;
//}

include_once 'header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('slider-js', '/dist/js/businessman/slider.js');


enqueueScript('home-init', '/dist/js/site/transportation-home.js');
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
if (isset($_COOKIE['user-login'])) {
    $user_type = User::getUserType(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
} else {
    $user_type = 'not-login';
}

?>

    <main class="container" style="padding-bottom: 180px">
        <div class="modal fade" id="exampleModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content mj-modal-content">
                    <div class="modal-header" style="border-bottom: 0;">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0;"></button>
                        <h5 class="modal-title" id="exampleModalLabel">نصب وب اپلیکیشن </h5>
                    </div>
                    <div class="modal-body" style="border-bottom:0 ;">

                        <div class="mj-modal-body">
                            <div class="mj-modal-first-text">
                                برای افزودن وب اپلیکیشن Ntirapp به صفحه گوشی خود کافیست دکمه نصب را بزنید تا به صفحه شما
                                افزوده شود
                            </div>
                            <div class="mj-modal-instal-btn">
                                <a id="install" href="#">
                                    <span id="instal-btn-1">نصب</span><span
                                            id="instal-btn-2">(add to home screen)</span>
                                </a>
                            </div>
                            <div class="mj-modal-second-text">
                                *در برخی از گوشی ها روند ذکر شده را پشتیبانی نمیکنند کافیست به آموزش های زیر مراجعه
                                بفرمایید
                            </div>
                            <div class="mj-modal-tutorials-btn">
                                <a id="android" href="#">اندروید</a>
                                <a id="ios" href="#">IOS</a>
                            </div>
                            <div class="mj-modal-image">
                                <img src="../../dist/images/modal-img.png" alt="modal-image">
                            </div>
                        </div>

                        <!--                        <div class="row">-->
                        <!--                            <div class="col-12">-->
                        <!--                                <button type="button" class="btn btn-success w-100 my-1" id="install"-->
                        <!--                                        data-bs-dismiss="modal">نصب-->
                        <!--                                </button>-->
                        <!--                            </div>-->
                        <!--                            <div class="col-12">-->
                        <!--                                <button type="button" class="btn btn-primary w-100 my-1" id="android">آموزش اندروید-->
                        <!--                                </button>-->
                        <!--                            </div>-->
                        <!--                            <div class="col-12">-->
                        <!--                                <button type="button" class="btn btn-primary w-100 my-1" id="ios">آموزش ios</button>-->
                        <!--                            </div>-->
                        <!--                            <div class="col-12">-->
                        <!--                                <button type="button" class="btn btn-danger w-100 my-1" onclick="installApp()"-->
                        <!--                                        data-bs-dismiss="modal">بستن-->
                        <!--                                </button>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="mj-home-btns-row d-flex justify-content-center row">
            <?php if ($user_type == 'driver' || $user_type == 'businessman') {
                ?>
                <div class="mj-gt-dashboard-section" id="go-to-dashboard">
                    <div><?= $lang['go_to_dashboard'] ?></div>
                    <div><img src="/dist/images/icons/user-tie(white).svg" alt=""></div>
                </div>
                <?php
            } else {
                ?>
                <div class="container-fluid mj-home-btn-section">

                    <div class="mj-alert mj-alert-with-icon  mb-3 mj-home-alert-signup">
                        <div class="mj-alert-icon">
                            <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100 pe-1">
                            <?= $lang['change_user_type_role_permission'] ?>
                        </div>
                    </div>

                    <div class="mj-gt-driver-btn mb-3" id="driver-login">
                        <a href="javascript:void(0)">
                            <span><?= $lang['log_in_as_a_driver']; ?></span>
                            <img src="/dist/images/icons/truck-container(white).svg" alt="<?= $lang['driver']; ?>">
                        </a>
                    </div>
                    <div class="mj-gt-businessman-btn" id="businessman-login">
                        <a href="javascript:void(0)">
                            <span><?= $lang['log_in_as_a_businessman']; ?></span>
                            <img src="/dist/images/icons/user-tie(white).svg" alt="<?= $lang['businessman']; ?>">

                        </a>
                    </div>
                    <input type="hidden" id="token_change_user_type" name="token_change_user_type"
                           value="<?= Security::initCSRF('token_change_user_type') ?>">
                    <div class="mj-circle"></div>
                    <div class="mj-aquare"></div>
                </div>
                <?php
            } ?>
        </div>


        <!-- <section class=" container-fluid mj-slider-section my-5">
            <div dir="ltr" class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php /*foreach ($sliders as $slider) { */ ?>
                        <div class="swiper-slide">
                            <a href="<? /*= $slider['url'] */ ?>">
                                <div class="mj-slide-card">
                                    <img src="<? /*= $slider['image'] */ ?>" alt="<? /*= $slider['alt'] */ ?>">
                                </div>
                            </a>
                        </div>
                    <?php /*} */ ?>
                </div>
            </div>
        </section>
        <section>
            <div class="row mb-3 mt-3">
                <div class="mj-home-blog-title">
                    <img src="/dist/images/icons/blog.svg" alt="<? /*= $lang['blog']; */ ?>">
                    <span><? /*= $lang['blog']; */ ?></span>
                </div>
            </div>
            <div class="row">
                <?php /*if (!empty($dataPostByLimit)) {
                    foreach ($dataPostByLimit as $dataPostByLimitITEM) { */ ?>
                        <div class="mj-home-blog-list mb-2">
                            <a href="/blog/<? /*= $dataPostByLimitITEM->post_slug; */ ?>">
                                <div class="mj-blog-list-item">
                                    <div class="mj-blog-item-card d-flex align-items-center">
                                        <div class="mj-blog-img">
                                            <img src="<? /*= Utils::fileExist($dataPostByLimitITEM->post_thumbnail, BOX_EMPTY); */ ?>"
                                                 alt="">
                                            <div class="mj-blog-date">
                                                <? /*= Utils::getTimeCountry('Y / m / d', $dataPostByLimitITEM->post_submit_time); */ ?>
                                            </div>

                                        </div>
                                        <div class="mj-blog-card-title">
                                            <? /*= strip_tags(mb_strimwidth($dataPostByLimitITEM->post_title, 0, 45, '...')); */ ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php /*}
                } */ ?>
            </div>
            <div class="mj-home-gt-blog-btn d-flex justify-content-center">
                <a href="/blog"><? /*= $lang['view_more']; */ ?></a>
            </div>

        </section>
-->
        <section>
            <div class="row my-3">
                <div class="mj-home-blog-title">
                    <img src="/dist/images/icons/truck-blue.svg" alt="<?= $lang['cargos']; ?>">
                    <span><?= $lang['cargos']; ?></span>
                </div>
            </div>
            <div class="row my-3 ">
                <div class="col-12">
                    <?php
                    foreach ($dataAllCargo as $item) {
                        if ($item->cargo_status == 'accepted' || $item->cargo_status == 'progress') {
                            $destOutput='';
                            if (isset(json_decode($item->cargo_destination)->id)) {
                                $destination = json_decode($item->cargo_destination)->id;
                                $destCountry = Driver::getCountryByCities($destination)->response;
                                $destCity = Location::getCityNameById($destination)->response;
                                $destOutput = $destCountry .' '.$destCity;
                            }
                            $sourceOutput = '';
                            if (isset(json_decode($item->cargo_origin)->id)) {
                                $origin = json_decode($item->cargo_origin)->id;
                                $sourceCountry = Driver::getCountryByCities($origin)->response;
                                $sourceCity = Location::getCityNameById($origin)->response;
                                $sourceOutput = $sourceCountry .' '.$sourceCity;
                            }


                            ?>
                            <div class="mj-d-cargo-card">
                                <div class="mj-d-cargo-card-badge">
                                    <img src="../../dist/images/truck.svg" alt="">
                                    <?= $item->cargo_car_count; ?>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="mj-d-cargo-item-category me-2"
                                             style="background: <?= $item->category_color ?>">
                                            <img src="<?= Utils::fileExist($item->category_icon, BOX_EMPTY) ?>"
                                                 alt="<?= $item->category_icon ?>">
                                            <span><?= array_column(json_decode($item->category_name), 'value', 'slug')[$langCookie] ?></span>
                                        </div>
                                        <div class="flex-fill">
                                            <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?= $item->cargo_name ?></h2>
                                            <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                                <span><?= $lang['d_cargo_price'] ?>:</span>
                                                <span>
                                        <?= number_format($item->cargo_recommended_price) ?>
                                        <small><?= array_column(json_decode($item->currency_name), 'value', 'slug')[$langCookie] ?></small>
                                    </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                                <div>
                                                    <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_origin'] ?>:
                                                    </div>
                                                    <div class="mj-d-cargo-item-value"><?= $sourceOutput ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <img src="/dist/images/icons/calendar-star.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                                <div>
                                                    <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_loading_time'] ?>
                                                        :
                                                    </div>
                                                    <div class="mj-d-cargo-item-value"><?= ($langCookie == 'fa_IR') ? Utils::jDate('Y/m/d', $item->cargo_start_date) : date('Y-m-d', $item->cargo_start_date) ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                                <div>
                                                    <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_destination'] ?>
                                                        :
                                                    </div>
                                                    <div class="mj-d-cargo-item-value"><?= $destOutput ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="/dist/images/icons/weight-scale.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                                <div>
                                                    <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_weight'] ?>:
                                                    </div>
                                                    <div class="mj-d-cargo-item-value">
                                                        <?= $item->cargo_weight ?> <?= $lang['d_cargo_weight_unit'] ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="/cargo-ads/<?= $item->cargo_id ?>"
                                       class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                                        <?= $lang['d_cargo_show'] ?>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                </div>
                <div class="col-12">
                    <a href="/cargo-ads"
                       class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                        <?= $lang['load_more'] ?>
                    </a>
                </div>
            </div>
        </section>
    </main>
<?php
getFooter('', false);
?>