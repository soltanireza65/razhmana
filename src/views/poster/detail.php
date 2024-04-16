<?php
global $Settings, $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

$langCookie = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $langCookie = $_COOKIE['language'];
} else {
    $langCookie = 'fa_IR';
    setcookie('language', 'fa_IR', time() + STABLE_COOKIE_TIMEOUT, "/");
    User::changeUserLanguageOnChangeLanguage('fa_IR');
}

$dataFirst = [];
$data = [];
$dataEdit = [];

$flagUser = false;


$result = Poster::getPosterDetail($_REQUEST['posterId']);
if ($result->status == 200 && !empty($result->response)) {
    $dataFirst = $result->response;

    if (is_null($dataFirst->poster_parent_id)) {
        $data = $dataFirst;

        if (User::userIsLoggedIn()) {
            $user = User::getUserInfo();
            if ($data->user_id == $user->UserId) {
                $flagUser = true;
            }
        }

        if ($flagUser) {
            $result_3 = Poster::getPosterChildDetail($data->poster_id);
            if ($result_3->status == 200) {
                $dataEdit = $result_3->response;
            }


        } else {
            if (in_array($data->poster_status, ['accepted', 'deleted', 'expired'])) {

            } else {
                header('location: /404');
            }
        }


    } else {
        $result_parent = Poster::getPosterDetail($dataFirst->poster_parent_id);
        if ($result_parent->status == 200 && !empty($result_parent->response)) {
            $data = $result_parent->response;

            if (User::userIsLoggedIn()) {
                $user = User::getUserInfo();
                if ($data->user_id == $user->UserId) {
                    $flagUser = true;
                }
            }


            if ($flagUser) {
                $result_3 = Poster::getPosterChildDetail($data->poster_id);
                if ($result_3->status == 200) {
                    $dataEdit = $result_3->response;
                }
            } else {
                if (in_array($data->poster_status, ['accepted', 'deleted', 'expired'])) {

                } else {
                    header('location: /404');
                }
            }
        } else {
            header('location: /404');
        }
    }
} else {
    header('location: /404');
}


$language = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
}

$posterProperties = Poster::getPosterPropertiesByPosterIdFromDetail($data->poster_id);
$PosterExpertReason = Poster::getPosterExpertReason($data->poster_id);
$getAllReportFromPosterDetail = PosterC::getAllReportFromPosterDetail();


include_once getcwd() . '/views/user/header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
enqueueStylesheet('all-css', '/dist/libs/fontawesome/all.css');
enqueueStylesheet('detail-css', '/dist/css/poster/detail.css');
enqueueStylesheet('simple-light-box-css', '/dist/libs/simplelightbox/dist/simple-lightbox.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('simple-light-box-js', '/dist/libs/simplelightbox/dist/simple-lightbox.min.js');
enqueueScript('all-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('poster-detail-js', '/dist/js/poster/poster-details.js');


$title = '';
if ($data->poster_type == "truck") {
    $brandTitle = (!empty(array_column(json_decode($data->brand_name, true), 'value', 'slug')[$language])) ?
        array_column(json_decode($data->brand_name, true), 'value', 'slug')[$language] : '';
    if (empty($brandTitle)) {
        foreach (json_decode($data->brand_name) as $loop) {
            if (!empty($loop->brand_name)) {
                $brandTitle = $loop->brand_name;
            }
        }
    }
    $title = $lang['a_truck'] . " -&rlm; " . $brandTitle . " -&rlm; " . $data->poster_built;
} elseif ($data->poster_type == 'trailer') {
    $modelTitle = (!empty(array_column(json_decode($data->type_name, true), 'value', 'slug')[$language])) ?
        array_column(json_decode($data->type_name, true), 'value', 'slug')[$language] : '';
    $title = $lang['a_trailer'] . " -&rlm; " . $modelTitle;
}

getHeader($title, false);



$language  =  $_COOKIE['language'] ? $_COOKIE['language']   : 'fa_IR';
$title_column_name = 'poster_title_'.$language;
?>
    <script>


    </script>
    <main class="container" style="padding-bottom: 125px !important;padding-top: 5px !important;">
        <?php if ($data->poster_status == 'deleted') { ?>
            <div class="mj-post-deleted-badge">
                <img src="/dist/images/poster/deleted.png" alt="deleted">
            </div>
        <?php } elseif ($data->poster_status == 'expired') { ?>
            <div class="mj-post-expire-badge">
                <img src="/dist/images/poster/expire.png" alt="expire">
            </div>
        <?php } ?>

        <!-- slider-->
        <div class="row my-2">

            <div class="swiper mySwiper mj-p-poster-slides">
                <div class="swiper-wrapper">

                    <?php
                    $images = json_decode($data->poster_images);
                    $flagImage = true;
                    foreach ($images as $index => $image) {
                        $flagImage = false;
                        ?>
                        <div class="swiper-slide px-2">
                            <div class="mj-p-slide-img">
                                <a href="<?= Utils::fileExist($image, POSTER_WEBP_DEFAULT); ?>">
                                    <img src="<?= Utils::fileExist($image, POSTER_WEBP_DEFAULT); ?>"
                                         alt="image-<?= $index; ?>">
                                </a>
                            </div>
                        </div>
                    <?php }
                    if ($flagImage) {
                        ?>
                        <div class="swiper-slide px-2">
                            <div class="mj-p-slide-img">
                                <a href="<?= POSTER_WEBP_DEFAULT; ?>">
                                    <img src="<?= POSTER_WEBP_DEFAULT; ?>" alt="default image">
                                </a>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <div class="swiper-pagination"></div>
            </div>

        </div>
        <!-- slider-->


        <!-- poster content -->
        <div class="mj-p-poster-detail-content">
<!--            todo add  reject desc from admin-->
<!--            <div class="mj-need-edit-issues-card">-->
<!--                <span>-->
<!--                    لطفا موارد نیز را اصلاح فرمائید-->
<!--                </span>-->
<!--                <div class="mj-need-edit-issues">-->
<!--                    عکس های بدون کیفیت-->
<!--                    <br>-->
<!--                    در توضیحات نباید اطلاعات تماس نوشته شود-->
<!--                </div>-->
<!--            </div>-->
            <div class="mj-poster-detail-menu">
                <div class="mj-p-poster-details-head col-no-pad">
                    <div class=" mj-p-share-btn"
                         id="share"
                         data-tj-link="<?= SITE_URL; ?>/poster/detail/<?= $data->poster_id ?>"
                         data-tj-title="<?= $title ?>"
                         data-tj-share-setting="<?= $Settings['share_text'] ?>">
                        <img src="/dist/images/poster/share-alt.svg" alt="share">
                        <div class="mj-share-tooltip  "><?=$lang['copied']?></div>
                    </div>
                    <?php
                    if (!empty($PosterExpertReason)) {
                        ?>
                        <div class="mj-p-header-btn mj-p-license-check">
                            <img src="/dist/images/poster/star.svg" alt="star">
                            <span><?= $lang['u_has_expert']; ?></span>
                        </div>
                    <?php } ?>
                </div>

                <div class="mj-poster-detail-badges">
                    <?php if ($data->poster_type == "truck") {
                        if ($data->poster_type_status == "new") {
                            echo '<span id="car-badge">' . $lang['u_zero'] . '</span>';
                        } elseif ($data->poster_type_status == "stock") {
                            echo '<span id="car-badge">' . $lang['u_worked'] . '</span>';
                        } elseif ($data->poster_type_status == "order") {
                            echo '<span id="car-badge">' . $lang['u_remittance'] . '</span>';
                        }
                    }

                    if ($data->poster_cash == "yes") {
                        echo '<span id="pay-badge">' . $lang['u_cash_2'] . '</span>';
                    } elseif ($data->poster_leasing == "yes") {
                        echo '<span id="pay-badge">' . $lang['u_leasing'] . '</span>';
                    } elseif ($data->poster_installments == "yes") {
                        echo '<span id="pay-badge">' . $lang['u_installment'] . '</span>';
                    }
                    ?>

                </div>
            </div>
            <div class="mj-p-poster-detail-date-brand">
                <div class="mj-p-poster-detail-date">
                    <img src="/dist/images/poster/clock.svg" alt="clock">
                    <span id="poster-time"> <?= Utils::timeElapsedString('@' . $data->poster_update_date); ?></span>
                    <span id="poster-date"><?= ($language == 'fa_IR') ? Utils::jDate('Y/m/d', $data->poster_update_date) : date('Y-m-d', $data->poster_update_date); ?></span>
                </div>
                <div class="mj-p-poster-detail-brand">
                    <img src="<?= Utils::fileExist($data->brand_image, POSTER_DEFAULT) ?>" alt="brand">
                </div>
            </div>
            <div class="mj-p-poster-detail-title mt-3">
                <h3><?= $data->$title_column_name; ?></h3>
            </div>
            <div class="mj-poster-detail-price">
                <span><?= $lang['u_poster_price']; ?> :</span>
                <?php
                if (isset($data->poster_price) && !empty($data->poster_price)) {
                    $currency = (!empty(array_column(json_decode($data->currency_name, true), 'value', 'slug')[$language])) ?
                        array_column(json_decode($data->currency_name, true), 'value', 'slug')[$language] : '';
                    echo '<span>' . number_format($data->poster_price) . '</span>' . '<span> ' . $currency . '</span>';
                } else {
                    echo '<span>' . $lang['u_agreement'] . '<span>';
                }
                ?>
            </div>
            <div class="mj-p-poster-details-features mt-3">

                <!--Start Location -->
                <div class="mj-p-poster-details-features-item mt-2">
                    <div class="mj-p-feature-item-name">
                        <img src="/dist/images/poster/city(blue).svg" alt="location">
                        <span><?= $lang['location']; ?> :</span>
                    </div>
                    <div class="mj-p-feature-item-value">
                        <span>
                            <?php
                            $country = (!empty(array_column(json_decode($data->country_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($data->country_name, true), 'value', 'slug')[$language] : '';
                            $city = (!empty(array_column(json_decode($data->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($data->city_name, true), 'value', 'slug')[$language] : '';
                            echo $country . " - " . $city;
                            ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($dataEdit) && $dataEdit->city_id != $data->city_id) { ?>
                    <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/city(blue).svg" alt="location">
                            <span><?= $lang['location']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                        <span>
                            <?php
                            $country = (!empty(array_column(json_decode($dataEdit->country_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($dataEdit->country_name, true), 'value', 'slug')[$language] : '';
                            $city = (!empty(array_column(json_decode($dataEdit->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($dataEdit->city_name, true), 'value', 'slug')[$language] : '';
                            echo $country . " - " . $city;
                            ?>
                        </span>
                        </div>
                    </div>
                <?php } ?>
                <!--End Location -->


                <!--Start Brand -->
                <div class="mj-p-poster-details-features-item mt-2">
                    <div class="mj-p-feature-item-name">
                        <img src="/dist/images/poster/brand(blue).svg" alt="brand">
                        <span><?= $lang['a_brand']; ?> :</span>
                    </div>
                    <div class="mj-p-feature-item-value">
                        <span>
                        <?php
                        $brandName = (!empty(array_column(json_decode($data->brand_name, true), 'value', 'slug')[$language])) ?
                            array_column(json_decode($data->brand_name, true), 'value', 'slug')[$language] : '';
                        if (empty($brandName)) {
                            foreach (json_decode($data->brand_name) as $loop) {
                                if (!empty($loop->brand_name)) {
                                    $brandName = $loop->brand_name;
                                }
                            }
                        }
                        echo $brandName;
                        ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($dataEdit) && $dataEdit->brand_id != $data->brand_id) { ?>
                    <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/brand(blue).svg" alt="brand">
                            <span><?= $lang['a_brand']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                        <span>
                        <?php
                        $brandName = (!empty(array_column(json_decode($dataEdit->brand_name, true), 'value', 'slug')[$language])) ?
                            array_column(json_decode($dataEdit->brand_name, true), 'value', 'slug')[$language] : '';
                        if (empty($brandName)) {
                            foreach (json_decode($dataEdit->brand_name) as $loop) {
                                if (!empty($loop->brand_name)) {
                                    $brandName = $loop->brand_name;
                                }
                            }
                        }
                        echo $brandName;
                        ?>
                        </span>
                        </div>
                    </div>
                <?php } ?>
                <!--End Brand -->


                <!--Start Model -->
                <div class="mj-p-poster-details-features-item mt-2">
                    <div class="mj-p-feature-item-name">
                        <img src="/dist/images/poster/model(blue).svg" alt="model">
                        <span><?= $lang['a_model']; ?> :</span>
                    </div>
                    <div class="mj-p-feature-item-value ">
                           <span>
                        <?php
                        $modelName = (!empty(array_column(json_decode($data->model_name, true), 'value', 'slug')[$language])) ?
                            array_column(json_decode($data->model_name, true), 'value', 'slug')[$language] : '';
                        if (empty($modelName)) {
                            foreach (json_decode($data->model_name) as $loop) {
                                if (!empty($loop->model_name)) {
                                    $modelName = $loop->model_name;
                                }
                            }
                        }
                        echo $modelName;
                        ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($dataEdit) && $dataEdit->model_id != $data->model_id) { ?>
                    <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/model(blue).svg" alt="model">
                            <span><?= $lang['a_model']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                           <span>
                        <?php
                        $modelName = (!empty(array_column(json_decode($dataEdit->model_name, true), 'value', 'slug')[$language])) ?
                            array_column(json_decode($dataEdit->model_name, true), 'value', 'slug')[$language] : '';
                        if (empty($modelName)) {
                            foreach (json_decode($dataEdit->model_name) as $loop) {
                                if (!empty($loop->model_name)) {
                                    $modelName = $loop->model_name;
                                }
                            }
                        }
                        echo $modelName;
                        ?>
                        </span>
                        </div>
                    </div>
                <?php } ?>
                <!--End Model -->

                <?php if ($data->poster_type == "truck") { ?>


                    <!--Start Built Year -->
                    <div class="mj-p-poster-details-features-item mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/timer(blue).svg" alt="timer">
                            <span><?= $lang['u_built_year']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value">
                            <span><?= $data->poster_built; ?></span>
                        </div>
                    </div>

                    <?php if (!empty($dataEdit) && $dataEdit->poster_built != $data->poster_built) { ?>
                        <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/timer(blue).svg" alt="timer">
                                <span><?= $lang['u_built_year']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                                <span><?= $dataEdit->poster_built; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <!--End Built Year -->

                    <!--Start Run Worked -->
                    <?php if ($data->poster_type_status != 'new') { ?>
                        <div class="mj-p-poster-details-features-item mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/road(blue).svg" alt="run worked">
                                <span><?= $lang['u_run_worked']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value">
                                <span><?= number_format($data->poster_used) . " " . $lang['u_km']; ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($dataEdit) && $dataEdit->poster_used != $data->poster_used) { ?>
                        <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/road(blue).svg" alt="run worked">
                                <span><?= $lang['u_run_worked']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                                <span><?= number_format($dataEdit->poster_used) . " " . $lang['u_km']; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <!--End Run Worked -->

                <?php } elseif ($data->poster_type == "trailer") { ?>

                    <!--Start trailer Type -->
                    <div class="mj-p-poster-details-features-item mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/model(blue).svg" alt="model">
                            <span><?= $lang['a_type_trailer']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value">
                           <span>
                        <?= (!empty(array_column(json_decode($data->type_name, true), 'value', 'slug')[$language])) ?
                            array_column(json_decode($data->type_name, true), 'value', 'slug')[$language] : '';
                        ?>
                        </span>
                        </div>
                    </div>

                    <?php if (!empty($dataEdit) && $dataEdit->type_id != $data->type_id) { ?>
                        <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/model(blue).svg" alt="model">
                                <span><?= $lang['a_type_trailer']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                           <span>
                                 <?= (!empty(array_column(json_decode($dataEdit->type_name, true), 'value', 'slug')[$language])) ?
                                     array_column(json_decode($dataEdit->type_name, true), 'value', 'slug')[$language] : ''; ?>
                             </span>
                            </div>
                        </div>
                    <?php } ?>
                    <!--End trailer Type -->

                    <!--Start trailer Type -->
                    <div class="mj-p-poster-details-features-item mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/model(blue).svg" alt="model">
                            <span><?= $lang['u_axis_count']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value">
                           <span>
                        <?= $data->poster_axis; ?>
                        </span>
                        </div>
                    </div>

                    <?php if (!empty($dataEdit) && $dataEdit->poster_axis != $data->poster_axis) { ?>
                        <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/model(blue).svg" alt="model">
                                <span><?= $lang['u_axis_count']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                                <span><?= $dataEdit->poster_axis; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <!--End trailer Type -->

                <?php } ?>

                <!--Start Clock -->
                <div class="mj-p-poster-details-features-item mt-2">
                    <div class="mj-p-feature-item-name">
                        <img src="/dist/images/poster/clock(detail).svg" alt="time">
                        <span><?= $lang['u_time_tel']; ?> :</span>
                    </div>
                    <div class="mj-p-feature-item-value">
                        <span>
                            <?= $lang['u_from'] . " " . $data->poster_time_from . " " . $lang['u_to'] . " " . $data->poster_time_to; ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($dataEdit) && ($dataEdit->poster_time_from != $data->poster_time_from || $dataEdit->poster_time_to != $data->poster_time_to)) { ?>
                    <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                        <div class="mj-p-feature-item-name">
                            <img src="/dist/images/poster/clock(detail).svg" alt="time">
                            <span><?= $lang['u_time_tel']; ?> :</span>
                        </div>
                        <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                        <span>
                            <?= $lang['u_from'] . " " . $dataEdit->poster_time_from . " " . $lang['u_to'] . " " . $dataEdit->poster_time_to; ?>
                        </span>
                        </div>
                    </div>
                <?php } ?>
                <!--End Clock -->


                <?php if ($data->poster_type == "truck") { ?>
                    <div class="mj-p-poster-detail-more-features">


                        <!--Start Gearbox -->
                        <div class="mj-p-poster-details-features-item mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/gear.svg" alt="">
                                <span><?= $lang['a_gearboxs']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value">
                            <span>
                                <?= (!empty(array_column(json_decode($data->gearbox_name, true), 'value', 'slug')[$language])) ?
                                    array_column(json_decode($data->gearbox_name, true), 'value', 'slug')[$language] : ''; ?>
                            </span>
                            </div>
                        </div>

                        <?php if (!empty($dataEdit) && $dataEdit->gearbox_id != $data->gearbox_id) { ?>
                            <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                                <div class="mj-p-feature-item-name">
                                    <img src="/dist/images/poster/gear.svg" alt="">
                                    <span><?= $lang['a_gearboxs']; ?> :</span>
                                </div>
                                <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                            <span>
                                <?= (!empty(array_column(json_decode($dataEdit->gearbox_name, true), 'value', 'slug')[$language])) ?
                                    array_column(json_decode($dataEdit->gearbox_name, true), 'value', 'slug')[$language] : ''; ?>
                            </span>
                                </div>
                            </div>
                        <?php } ?>
                        <!--End Gearbox -->


                        <!--Start fuel -->
                        <div class="mj-p-poster-details-features-item mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/gas-pump.svg" alt="fuel">
                                <span><?= $lang['u_type_fuel']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value">
                                <span>
                                     <?= (!empty(array_column(json_decode($data->fuel_name, true), 'value', 'slug')[$language])) ?
                                         array_column(json_decode($data->fuel_name, true), 'value', 'slug')[$language] : ''; ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($dataEdit) && $dataEdit->fuel_id != $data->fuel_id) { ?>
                            <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                                <div class="mj-p-feature-item-name">
                                    <img src="/dist/images/poster/gas-pump.svg" alt="gas">
                                    <span><?= $lang['u_type_fuel']; ?> :</span>
                                </div>
                                <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                                <span>
                                     <?= (!empty(array_column(json_decode($dataEdit->fuel_name, true), 'value', 'slug')[$language])) ?
                                         array_column(json_decode($dataEdit->fuel_name, true), 'value', 'slug')[$language] : ''; ?>
                                </span>
                                </div>
                            </div>
                        <?php } ?>
                        <!--End fuel -->

                        <!--Start Color -->
                        <div class="mj-p-poster-details-features-item mt-2">
                            <div class="mj-p-feature-item-name">
                                <img src="/dist/images/poster/palette.svg" alt="fuel">
                                <span><?= $lang['a_out_color']; ?> :</span>
                            </div>
                            <div class="mj-p-feature-item-value">
                                <i class="fa-solid fa-square font-20"
                                   style="color: <?= $data->poster_color_out; ?>"></i>
                            </div>
                        </div>

                        <?php if (!empty($dataEdit) && $dataEdit->poster_color_out != $data->poster_color_out) { ?>
                            <div class="mj-p-poster-details-features-item mj-p-poster-details-features-item-edit mt-2">
                                <div class="mj-p-feature-item-name">
                                    <img src="/dist/images/poster/palette.svg" alt="color">
                                    <span><?= $lang['a_out_color']; ?> :</span>
                                </div>
                                <div class="mj-p-feature-item-value mj-p-feature-item-value-edit">
                                    <i class="fa-solid fa-square font-20"
                                       style="color: <?= $dataEdit->poster_color_out; ?>"></i>
                                </div>
                            </div>
                        <?php } ?>
                        <!--End Color -->


                    </div>
                    <div class="mt-3">
                        <div class="mj-p-moreless-features-button"><?= $lang['show_more']; ?> ></div>
                    </div>
                <?php }
                if (!empty($posterProperties)) { ?>
                    <div class="mj-p-divider"></div>

                    <div class="mj-p-excerpt-title">
                        <img src="/dist/images/poster/texts.svg" alt="options">
                        <span><?= $lang['u_options']; ?> :</span>
                    </div>

                    <div class="mj-options-detail-items">
                        <?php

                        foreach ($posterProperties as $loop) {
                            ?>
                            <div class="mj-options-detail-item">
                                <div class="mj-option-img">
                                    <img src="<?= Utils::fileExist($loop->property_image, BOX_EMPTY); ?>" alt="option">
                                </div>
                                <span>
                              <?= (!empty(array_column(json_decode($loop->property_name, true), 'value', 'slug')[$language])) ?
                                  array_column(json_decode($loop->property_name, true), 'value', 'slug')[$language] : ''; ?>
                            </span>
                            </div>
                        <?php } ?>

                    </div>
                <?php }
                if (!empty($data->poster_desc)) { ?>
                    <div class="mj-p-divider"></div>

                    <div class="mj-p-poster-detail-excerpt">
                        <div class="mj-p-excerpt-title">
                            <img src="/dist/images/poster/texts.svg" alt="description">
                            <span><?= $lang['description']; ?> :</span>
                        </div>
                        <div class="mj-p-excerpt-content mt-3">
                            <div class="mj-p-excerpt-texts mj-p-excerpt-texts-1">
                                <?= $data->poster_desc; ?>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="mj-p-moreless-excerpt-button"><?= $lang['show_more']; ?> ></div>
                        </div>
                    </div>
                    <?php
                }
                if (!empty($PosterExpertReason)) {
                    ?>
                    <div class="mj-p-divider"></div>

                    <div class="mj-p-poster-pro-excerpt">
                        <div class="mj-p-pro-excerpt-title">
                            <img src="/dist/images/poster/star(blue).svg" alt="expert">
                            <span><?= $lang['u_description_expert']; ?> :</span>
                        </div>
                        <div class="mj-p-pro-excerpt-content mt-3">
                            <div class="mj-p-excerpt-texts mj-p-excerpt-texts-2">
                                <div><?= $PosterExpertReason; ?></div>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="mj-p-moreless-pro-button"><?= $lang['show_more']; ?> ></div>
                        </div>
                    </div>
                <?php } ?>

                <!--start support-->
                <div class="mj-p-divider"></div>
                <div class=" my-2">
                    <div class="">

                        <div class="d-flex align-items-center mb-3">
                            <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2"
                                 alt="support">
                            <div class="mj-p-excerpt-title">
                                <span class="mj-d-icon-title"><?= $lang['u_poster_call_customer'] ?></span>

                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-nowrap overflow-auto">
<!--                              todo display user number when empty-->
                            <a href="tel:<?=$data->poster_phone;  ?>"
                               class="mj-btn mj-d-btn-call me-2"
                               style="flex: 0 0 auto; min-height: 34px;">
                                <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                <?= $lang['d_cargo_call'] ?>
                            </a>

                            <?php
                            if (!$flagUser && $data->poster_status == "accepted" && isset($_COOKIE['user-login'])) { ?>
                                <!--Start Report-->
<!--                                -->
                                <a class="mj-p-report-btn" data-bs-toggle="modal" href="#modalReport">
                                    <img src="/dist/images/poster/report-btn.svg" alt="report">
                                    <?= $lang['u_report_poster']; ?>
                                </a>
                                <!--                    <div class="mj-p-poster-report mt-3">-->
                                <!--                        <div class="mj-p-report-title mb-2">-->
                                <!--                            <img src="/dist/images/poster/report-icon.svg" alt="">-->
                                <!--                            <div class="mj-p-report-text">-->
                                <!--                                <span>--><?//= $lang['u_report_poster']; ?><!--</span>-->
                                <!--                                <span>--><?//= $lang['u_report_poster_desc']; ?><!--</span>-->
                                <!--                            </div>-->
                                <!--                        </div>-->
                                <!--                        <a class="mj-p-report-btn" data-bs-toggle="modal" href="#modalReport">-->
                                <!--                            <img src="/dist/images/poster/report-btn.svg" alt="report">-->
                                <!--                            --><?//= $lang['u_report_poster']; ?>
                                <!--                        </a>-->
                                <!--                    </div>-->

                                <div class="mj-menu-modal">
                                    <div class="modal fade" id="modalReport" aria-hidden="true" aria-labelledby="modalReportLabel"
                                         tabindex="-1">
                                        <div class="modal-dialog modal-full-width">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <div class="mj-menu-filter-modal-header">
                                                        <button type="button" class="mj-menu-close" data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            <span class="fa-close"></span>
                                                        </button>
                                                        <div class="mj-title-sf">
                                                            <img src="/dist/images/poster/save-filter-icon.svg" alt="">
                                                            <span><?= $lang['u_report_poster']; ?></span>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="modal-body">


                                                    <section class="container">
                                                        <div class="row">
                                                            <h4><?= $lang['u_poste_report_title']; ?></h4>
                                                            <p class="font-12"><?= $lang['u_poste_report_desc']; ?></p>
                                                            <?php
                                                            foreach ($getAllReportFromPosterDetail as $index => $loop) {
                                                                ?>
                                                                <div class="col-12 <?= ($index == 0) ? null : 'mt-1'; ?>">
                                                                    <div class="mj-a-radio-poster mt-1">
                                                                        <input id="report-<?= $loop->report_id; ?>"
                                                                               data-tj-report-id="<?= $loop->report_id; ?>"
                                                                               data-tj-poster-id="<?= $data->poster_id; ?>"
                                                                               class="custom-radio"
                                                                               type="radio"
                                                                               name="report">
                                                                        <label for="report-<?= $loop->report_id; ?>">
                                                                            <?= (!empty(array_column(json_decode($loop->report_title, true), 'value', 'slug')[$language])) ?
                                                                                array_column(json_decode($loop->report_title, true), 'value', 'slug')[$language] : '';; ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>

                                                            <div class="col-12 mt-1">
                                                                <div class="mj-a-radio-poster mt-1">
                                                                    <input id="report-other"
                                                                           data-tj-report-id="0"
                                                                           data-tj-poster-id="<?= $data->poster_id; ?>"
                                                                           class="custom-radio"
                                                                           type="radio"
                                                                           name="report">
                                                                    <label for="report-other"><?= $lang['u_other']; ?></label>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 mt-1">
                                                                <div class="m-2 mj-a-height-0" id="report-other-div">
                                                        <textarea type="text"
                                                                  inputmode="text"
                                                                  class="form-control mj-a-textarea-poster mt-2"
                                                                  id="desc-report-other"
                                                                  lang="en"
                                                                  placeholder="<?= $lang['u_report_input_placeholder']; ?>"
                                                                  style="min-height: 38px;"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-12" style="z-index: 20">
                                                                <div class="row">
                                                                    <div class="col-6 ps-3">
                                                                        <button class="btn w-100 py-2 mj-a-btn-cancel"

                                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                            <?= $lang['u_opt_out']; ?>
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-6 pe-3">
                                                                        <button class="btn w-100 py-2 mj-a-btn-submit"
                                                                                disabled
                                                                                id="btn-report"><?= $lang['b_accept']; ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </section>


                                                </div>
                                                <div class="modal-footer">

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!--End Report-->

                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!--End support-->
            </div>
            <!-- poster content -->

        </div>

        <?php if (!in_array($data->poster_status, ['deleted', 'expired']) && !$flagUser) { ?>
            <div class="mj-poster-details-contact">
                <?php if (!empty($data->poster_whatsapp)) { ?>
                    <div class="mj-contact-whatsapp">
                        <a class="w-100 h-100 d-flex justify-content-center align-items-center"
                           href="https://wa.me/<?= $data->poster_whatsapp; ?>">
                            <img src="/dist/images/poster/whatsapp(detail).svg" alt="whatsapp">
                        </a>
                    </div>
                <?php } ?>
                <div class="mj-contact-phone">
                    <a class="w-100 h-100 d-flex justify-content-center align-items-center"
                       href="tel:<?=Utils::getFileValue("settings.txt", 'support_call') ?>">
                        <img src="/dist/images/poster/phone(detail).svg" alt="phone">
                    </a>
                </div>

                <div class="mj-contact-close">
                    <img src="/dist/images/poster/phone(detail).svg" alt="close">
                </div>
            </div>
        <?php } ?>
    </main>


    <input type="hidden"
           value="<?= Security::initCSRF('poster-detail') ?>"
           data-tj-token="<?= Security::initCSRF('poster-detail') ?>"
           name="token"
           id="token">
<?php
getFooter('', false, false);
