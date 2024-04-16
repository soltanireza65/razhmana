<?php

global $lang, $Settings;

use MJ\Router\Router;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {

    $user = User::getUserInfo();
    $freight = Inventory::getInquiryDetail($_REQUEST['id'], $user->UserId);
    if ($freight->status == 200 && !empty($freight->response[0])) {
        include_once getcwd() . '/views/user/header-footer.php';

        getHeader($lang['d_cargo_detail_title']);
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

        $sourceOutput = '';
        if (isset($freight->city_id)) {
            $origin = ($freight->city_id);
            $sourceCountry = Driver::getCountryByCities($origin)->response;
            $sourceCity = Location::getCityNameById($origin)->response;
            $sourceOutput = $sourceCountry . ' - ' . $sourceCity;
        }
        if($freight->freight_status == 'completed'){
            Inventory::updateInquiryStatus($freight->freight_id, 'read');
        }
        ?>
        <main class="container">
            <style>
                .mj-backbtn{
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
                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mj-d-cargo-item-category me-2"
                                     style="background: linear-gradient(360deg, #689bff, #51e2f7);">
                                    <img src="/dist/images/icons/storage/warehouse(white).svg"
                                         alt="<?= mb_strimwidth($freight->freight_name, 0, 10, '') ?>">
                                    <span><?= array_column(json_decode($freight->category_name), 'value', 'slug')[$langCookie] ?></span>
                                </div>
                                <div class="flex-fill">
                                    <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?= $freight->freight_name ?></h2>
                                    <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
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
                                            <div class="mj-d-cargo-item-title"><?= $lang['u_location'] ?>:</div>
                                            <div class="mj-d-cargo-item-value"><?= $sourceOutput ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/calendar-star.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="loading-time"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['u_inventory_date'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $freight->freight_start_date) : date('Y-m-d', $freight->freight_start_date) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/weight-scale.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="weight"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_weight'] ?>:</div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= (int)$freight->freight_wieght ?> <?= $lang['u_inquiry_air_weight_unit'] ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/maximize.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="volume"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_volume'] ?>:</div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= (int)$freight->freight_volume ?> <?= $lang['d_cargo_volume_unit'] ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/storage/duration.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="duration day"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['b_inquiry_duration_day'] ?>:</div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= (int)$freight->freight_duration ?> <?= $lang['b_day'] ?></div>
                                        </div>
                                    </div>
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