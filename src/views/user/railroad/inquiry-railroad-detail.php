<?php

global $lang, $Settings;

use MJ\Router\Router;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {

    $user = User::getUserInfo();
    $freight = Railroad::getInquiryDetail($_REQUEST['id'], $user->UserId);
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


        $destOutput = '';
        if (isset($freight->dest_city_id)) {
            $destination = ($freight->dest_city_id);
            $destCountry = Driver::getCountryByCities($destination)->response;
            $destCity = Location::getCityNameById($destination)->response;
            $destOutput = $destCountry . ' - ' . $destCity;
        }
        $sourceOutput = '';
        if (isset($freight->source_city_id)) {
            $origin = ($freight->source_city_id);
            $sourceCountry = Driver::getCountryByCities($origin)->response;
            $sourceCity = Location::getCityNameById($origin)->response;
            $sourceOutput = $sourceCountry . ' - ' . $sourceCity;
        }
        $sourceRailroadOutput = '';
        if (!empty($freight->source_railroad_id) && !empty($freight->source_railroad_id)) {
            $sourceRailroadOutput = array_column(json_decode(Railroad::getRailroadNameById($freight->source_railroad_id)), 'value', 'slug')[$langCookie];
        }

        $destRailroadOutput = '';
        if (!empty($freight->dest_railroad_id) && !empty($freight->dest_railroad_id)) {
            $destRailroadOutput = array_column(json_decode(Railroad::getRailroadNameById($freight->dest_railroad_id)), 'value', 'slug')[$langCookie];
        }
        $packing_name = '';
        if (!empty($freight->packing_id) && !empty($freight->packing_id)) {
            $destRailroadOutput = array_column(json_decode(Railroad::getRailroadPackingNameById($freight->packing_id)), 'value', 'slug')[$langCookie];
        }

        $packing_name = '';
        if (!empty($freight->packing_id) && !empty($freight->packing_id)) {
            $packing_name = array_column(json_decode(Railroad::getRailroadPackingNameById($freight->packing_id)), 'value', 'slug')[$langCookie];
        }

        $wagon_name = '';
        if (!empty($freight->packing_id) && !empty($freight->packing_id)) {
            $wagon_name = array_column(json_decode(Railroad::getRailroadWagonNameById($freight->wagon_id)), 'value', 'slug')[$langCookie];
        }
        $container_name = '';
        if (!empty($freight->packing_id) && !empty($freight->packing_id)) {
            $container_name = array_column(json_decode(Railroad::getRailroadContainerNameById($freight->container_id)), 'value', 'slug')[$langCookie];
        }

        if($freight->freight_status == 'completed'){
            Railroad::updateInquiryStatus($freight->freight_id, 'read');
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
                                    <img src="/dist/images/icons/train/train(white).svg"
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
                                        <img src="/dist/images/icons/air/first-city.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_origin'] ?>:</div>
                                            <div class="mj-d-cargo-item-value"><?= $sourceOutput ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/calendar-star.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="loading time"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_loading_time'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $freight->freight_start_date) : date('Y-m-d', $freight->freight_start_date) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/end-city.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="destination"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_destination'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $destOutput ?></div>
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
                                        <img src="/dist/images/icons/train/intrain.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="source port"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['b_cargo_source_port'] ?>
                                                :
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $sourceRailroadOutput ?></div>
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
                                        <img src="/dist/images/icons/train/endtrain.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="dest port"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['b_cargo_desc_port'] ?>
                                                :
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $destRailroadOutput ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/air/box.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="packing type"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title">
                                                <?= $lang['b_packing_type'] ?> :
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $packing_name; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/train/wagen-type.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="wagon type"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title">
                                                <?= $lang['b_wagon_type'] ?> :
                                            </div>
                                            <div class="mj-d-cargo-item-value">
                                                  <span>
                                                     <?= $wagon_name ?>
                                                  </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/train/container-type.svg"
                                             class="mj-d-cargo-item-icon me-2"
                                             alt="container type"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['b_container_type'] ?>
                                                :
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $container_name ?></div>
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
        getFooter('', false);
    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}