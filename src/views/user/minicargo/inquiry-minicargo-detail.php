<?php

global $lang, $Settings;

use MJ\Router\Router;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {

    $user = User::getUserInfo();
    $freight = MiniCargo::getInquiryMiniCargoDetail($_REQUEST['id'], $user->UserId);

    if ($freight->status == 200 && !empty($freight->response[0])) {
        include_once getcwd() . '/views/user/header-footer.php';

        getHeader($lang['d_cargo_detail_title']);
        $minicargo_values = $freight->response;
        $freight = $freight->response[0];
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $langCookie = $_COOKIE['language'];
        } else {
            $langCookie = 'fa_IR';
        }
        $currency_unit = '';
        if (!empty($freight->freight_price) && !empty($freight->currency_id)) {
            $currency_unit = array_column(json_decode(Currency::getCurrencyNameById($freight->currency_id)), 'value', 'slug')[$langCookie];
        }


        if ($freight->freight_status == 'completed') {
            MiniCargo::updateInquiryStatus($freight->freight_id, 'read');
        }


        ?>
        <main class="container">
            <style>
                .mj-backbtn {
                    display: none !important;
                }
            </style>
            <?php
            if ($freight->freight_status == 'pending') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-question.svg" alt="exclamation">
                            </div>
                            <?= $lang['u_inquiry_alert_pending'] ?>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($freight->freight_status == 'process') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-info mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/info-circle(white).svg" alt="exclamation">
                            </div>
                            <?= $lang['u_inquiry_alert_process'] ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <div class="mj-d-cargo-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mj-d-cargo-item-category me-2"
                                     style="background: linear-gradient(360deg, #689bff, #51e2f7);">
                                    <img src="/dist/images/icons/storage/warehouse(white).svg"
                                         alt="<?= $lang['u_inquery_minicargo_name_prefix'] ?>">
                                    <span> <?= $lang['u_inquery_minicargo_category_name_replace'] ?></span>
                                </div>
                                <div class="flex-fill">
                                    <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?= $lang['u_inquery_minicargo_name_prefix'] . ' ' . $freight->freight_id ?></h2>
                                    <div
                                        class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                        <span><?= $lang['u_recommended_price'] ?>:</span>
                                        <span>
                                        <?= (isset($freight->freight_price)) ? number_format($freight->freight_price) : $lang['u_inquiry_in_process'] ?>
                                        <small><?= $currency_unit ?></small>
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/storage/location-storage.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['location_cargo_origin'] ?>:
                                            </div>
                                            <?= Location::getCountryByCityId($freight->source_city_id)->CountryName ?>
                                            -
                                            <?= Location::getCityNameById($freight->source_city_id)->response ?>


                                        </div>
                                    </div>
                                </div>
                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/storage/location-storage.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="origin"/>
                                        <div>
                                            <div
                                                class="mj-d-cargo-item-title"><?= $lang['location_cargo_destination'] ?>
                                                :
                                            </div>
                                            <?= Location::getCountryByCityId($freight->dest_city_id)->CountryName ?>
                                            -
                                            <?= Location::getCityNameById($freight->dest_city_id)->response ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/calendar-star.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="loading-time"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['u_minicargo_date'] ?>:
                                            </div>
                                            <div
                                                class="mj-d-cargo-item-value"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $freight->freight_start_date) : date('Y-m-d', $freight->freight_start_date) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/weight-scale.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="weight"/>
                                        <div>
                                            <div
                                                class="mj-d-cargo-item-title"><?= $lang['u_minicargo_transportation'] ?>
                                                :
                                                <?= $freight->freight_arrangement == 'yes' ? $lang['yes'] : $lang['no'] ?>
                                            </div>
                                            <div class="mj-d-cargo-item-value">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div>
                            <div class="mj-d-cargo-card">

                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/boxes(blue).svg" class="mj-b-icon-box me-2"
                                             alt="description">
                                        <span class="mj-b-icon-title"><?= $lang['u_mincargo_cargo_lists'] ?></span>
                                    </div>
                                    <div class="mj-cargo-item-list">
                                        <?php foreach ($minicargo_values as $item) {
//      
                                            ?>
                                            <div class="mj-cargo-item   mb-1">
                                                <div class="fa-boxes"></div>
                                                <div
                                                    class="mj-cargo-item-cat"><?= array_column(json_decode($item->category_name), 'value', 'slug')[$_COOKIE['language']]; ?></div>
                                                <div class="mj-cargo-item-name"><?= $item->value_name ?></div>

                                                <div class="mj-cargo-item-weight">
                                                    <span><?= $lang['u_mincargo_cargo_weight'] ?> :</span>
                                                    <div><?= $item->value_weight ?> <span
                                                            id="cargo-weight"><?= $item->value_weight_slug ?></span>
                                                    </div>
                                                </div>
                                                <div class="mj-cargo-item-volume">
                                                    <span><?= $lang['u_mincargo_cargo_volume'] ?> :</span>
                                                    <div><?= $item->value_volume ?> <span
                                                            id="cargo-volume"><?= $lang['u_mincargo_cargo_volume_unit'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </div>


                            </div>


                        </div>
                        <?php
                        if (isset($freight->freight_description) && !empty($freight->freight_description)) {
                            ?>
                            <div class="mj-b-cargo-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/align-center.svg" class="mj-b-icon-box me-2"
                                             alt="description">
                                        <span class="mj-b-icon-title"><?= $lang['b_details_cargo_desc'] ?></span>
                                    </div>

                                    <div class="mj-b-cargo-item-desc">
                                        <?= $freight->freight_description ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>


                    </div>
                </div>
        </main>
        <?php
        getFooter();
    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}