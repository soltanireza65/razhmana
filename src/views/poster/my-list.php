<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once getcwd() . '/views/user/header-footer.php';

//    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
//    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
    enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');
    enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
    enqueueStylesheet('detail-css', '/dist/css/poster/detail.css');
//
//    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
//    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
//    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
//    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('my-posters-js', '/dist/js/poster/my-posters.js');

    getHeader($lang['u_list_my_poster']);

    $language = 'fa_IR';
    if (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
    }

    $balance = User::getBalance($user->UserId);

    $BalanceAvailable = 0;
    $BalanceInWithdraw = 0;
    $BalanceCurrency = '';
    foreach ($balance->response as $item) {
        if ($item->CurrencyId == 1) {
            // 1 is toman
            $BalanceAvailable = $item->BalanceAvailable;
            $BalanceCurrency = $item->BalanceCurrency;
            $BalanceInWithdraw = $item->BalanceInWithdraw;
        }
    }
//    if (empty($BalanceCurrency) || is_null($BalanceCurrency)) {
//        header('location: /user/wallet');
//    }

    $language  =  $_COOKIE['language'] ? $_COOKIE['language']   : 'fa_IR';
    $title_column_name = 'poster_title_'.$language;

    ?>

    <main>
        <div class="container">

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <a href="/poster/add"
                           class="mj-btn-more mj-fw-400 mj-font-12 py-2">
                            <img src="/dist/images/icons/circle-plus.svg" alt="add">
                            &nbsp;<?= $lang['u_add_poster']; ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card mj-card">
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1">
                                <?= $lang['u_list_my_poster']; ?>
                                </span>
                                <p class="mj-b-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                         alt="exclamation"/>
                                </span>
                                    <?= $lang['u_list_my_poster_desc']; ?>
                                </p>
                            </div>

                            <div class="row align-items-center mb-1">
                                <div class="col">
                                    <div class="mj-input-filter-box">
                                        <input type="text"
                                               class="mj-input-filter"
                                               id="poster-search"
                                               name="poster-search"
                                               placeholder="<?= $lang['u_list_my_poster_search_plcaeholder'] ?>">
                                        <label for="cargo-search" class="mj-input-filter-search">
                                            <img src="/dist/images/icons/search.svg" alt="search"/>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button type="button" class="mj-btn-filter" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <img src="/dist/images/icons/sliders.svg" class="me-1" alt="filter">
                                            <?= $lang['b_filter'] ?>
                                        </button>

                                        <div class="dropdown-menu mj-dropdown-menu">
                                            <div class="mb-2">
                                                <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['b_filter_by'] ?></span>
                                                <p class="mj-b-cargo-item-desc mb-0">
                                                    <?= $lang['b_filter_by_desc'] ?>
                                                </p>
                                            </div>

                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="/poster/my-list"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                        <span class="mj-badge mj-badge-all"></span>
                                                        <span style="padding-right: 3px;"><?= $lang['d_filter_all'] ?></span>
                                                    </a>
                                                </div>

                                                <div class="col-6">
                                                    <a href="/poster/my-list/pending"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-warning"
                                                          style="background-color: #ffc700"></span>
                                                        <span style="padding-right: 3px;"><?= $lang['d_filter_pending'] ?></span>
                                                    </a>
                                                </div>

                                                <div class="col-6">
                                                    <a href="/poster/my-list/accepted"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-success"
                                                          style="background-color: #3d3d3d"></span>
                                                        <span style="padding-right: 3px;"><?= $lang['b_filter_by_accepted'] ?></span>
                                                    </a>
                                                </div>

                                                <div class="col-6">
                                                    <a href="/poster/my-list/expired"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-dark"
                                                          style="background-color: #ffc700"></span>
                                                        <span style="padding-right: 3px;"><?= $lang['b_filter_by_expire'] ?></span>
                                                    </a>
                                                </div>

                                                <div class="col-6">
                                                    <a href="/poster/my-list/rejected"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-danger"
                                                          style="background-color: #ff1d1d"></span>
                                                        <span style="padding-right: 3px;"><?= $lang['d_filter_rejected'] ?></span>
                                                    </a>
                                                </div>

                                                <div class="col-6">
                                                    <a href="/poster/my-list/needed"
                                                       class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-cancel"
                                                          style="background-color: #ffc700"></span>
                                                        <span style="padding-right: 3px;"> <?= $lang['u_poster_needed'] ?></span>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mj-poster-home-items">
                                <?php
                                $status = (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['pending', 'rejected', 'accepted', 'needed', 'expired'])) ? $_REQUEST['status'] : 'all';
                                $records = Poster::getPosterMyList($user->UserId, $status);
                                foreach ($records->response as $key => $item) {
                                    ?>
                                    <div class="mj-p-poster-item-card mj-s-poster-item-card"
                                         >
                                        <span class="mj-poster-need-edit"> <?= $lang['u_poster_need_edit'] ?></span>
                                        <a class="mj-mypost-menu-btn"
                                           href="javascript:void (0);"
                                           data-tj-id="<?= $item->PosterId; ?>"
                                           data-tj-status="<?= $item->PosterStatus; ?>">
                                            <div class="fa-bars"></div>
                                        </a>
                                        <a href="javascript:void(0)" class="mj-p-poster-item-content"
                                           data-id="<?= $item->PosterId; ?>">
                                            <div class="mj-p-poster-top-section">
                                                <div class="mj-p-poster-image">
                                                 <?php
                                                    $images = json_decode($item->posterImages);
                                                    if (isset($images[0])) {
                                                        echo '<img src="' . Utils::fileExist($images[0], POSTER_DEFAULT) . '" alt="poster">';
                                                    } else {
                                                        echo '<img src="' . POSTER_DEFAULT . '" alt="">';
                                                    }
                                                    ?>

                                                </div>
                                                <div class="mj-p-poster-details mj-s-poster-details ps-1">
                                                    <div class="mj-poster-type-badges">
                                                        <?php
                                                        if ($item->PosterCash == "yes") {
                                                            echo '<span id="pay-badge">' . $lang['u_cash_2'] . '</span>';
                                                        } elseif ($item->PosterLeasing == "yes") {
                                                            echo '<span id="pay-badge">' . $lang['u_leasing'] . '</span>';
                                                        } elseif ($item->PosterInstallments == "yes") {
                                                            echo '<span id="pay-badge">' . $lang['u_installment'] . '</span>';
                                                        }

                                                        if ($item->PosterType == "truck") {
                                                            if ($item->posterTypeStatus == "new") {
                                                                echo '<span id="car-badge">' . $lang['u_zero'] . '</span>';
                                                            } elseif ($item->posterTypeStatus == "stock") {
                                                                echo '<span id="car-badge">' . $lang['u_worked'] . '</span>';
                                                            } elseif ($item->posterTypeStatus == "order") {
                                                                echo '<span id="car-badge">' . $lang['u_remittance'] . '</span>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="mj-p-poster-name ">
                                                        <div class="mj-p-poster-title text-zip">
                                                            <?= $item->poster_title_fa_IR; ?>
                                                        </div>
                                                        <span class="d-block"><?= Utils::timeElapsedString('@' . $item->PosterSubmit); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mj-s-my-post-badge <?php
                                            if ($item->PosterStatus == 'pending') {
                                                echo 'pending';
                                            } elseif ($item->PosterStatus == 'accepted') {
                                                echo 'accepted';
                                            } elseif ($item->PosterStatus == 'expired') {
                                                echo 'expired';
                                            } elseif ($item->PosterStatus == 'rejected') {
                                                echo 'rejected';
                                            } elseif ($item->PosterStatus == 'needed') {
                                                echo 'needed';
                                            } else {
                                                echo '';
                                            }
                                            ?>"></div>
                                        </a>

                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </main>


<!--Start Modal Menu-->
    <div class="mj-menu-modal">
        <div class="modal fade bottom-0" id="exampleModalToggle" aria-hidden="true"
             aria-labelledby="exampleModalToggleLabel"
             tabindex="-1">
            <div class="modal-dialog modal-full-width">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="mj-menu-filter-modal-header">
                            <button type="button" class="mj-menu-close" data-bs-dismiss="modal" aria-label="Close">
                                <span class="fa-close"></span>
                            </button>
                            <div class="mj-title-sf">
                                <img src="/dist/images/poster/save-filter-icon.svg" alt="">
                                <span><?= $lang['menu']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mj-menu-list">
                            <div class="mj-menu-list-item">
                                <ul id="menu-modal"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

    </div>
    <!--End Modal Menu-->

    <!--Start Modal Delete-->
    <div class="mj-menu-modal">
        <div class="modal fade" id="modalDelete" aria-hidden="true" aria-labelledby="modalDeleteLabel"
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
                                <span><?= $lang['u_delete_reason_poster']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <section class="container">
                            <div class="row">
                                <h4><?= $lang['u_poste_reason_delete_title']; ?></h4>
                                <p class="font-12"><?= $lang['u_poste_reason_delete_desc']; ?></p>
                                <?php
                                $getAllPosterReasonDeleteFromPosterDetail = PosterC::getAllPosterReasonDeleteFromPosterDetail();
                                foreach ($getAllPosterReasonDeleteFromPosterDetail as $index => $loop) {
                                    ?>
                                    <div class="col-12 <?= ($index == 0) ? null : 'mt-1'; ?>">
                                        <div class="mj-a-radio-poster mt-1">
                                            <input id="delete-<?= $loop->category_id; ?>"
                                                   data-tj-delete-id="<?= $loop->category_id; ?>"
                                                   data-tj-poster-id=""
                                                   class="custom-radio"
                                                   type="radio"
                                                   name="delete-reason">
                                            <label for="delete-<?= $loop->category_id; ?>">
                                                <?= (!empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language])) ?
                                                    array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language] : '';; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-12 mt-3" style="z-index: 20">
                                    <div class="row">
                                        <div class="col-6 ps-3">
                                            <button class="btn w-100 py-2 mj-a-btn-cancel"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                <?= $lang['u_opt_out']; ?>
                                            </button>
                                        </div>
                                        <div class="col-6 pe-3">
                                            <button class="btn w-100 py-2 mj-a-btn-danger"
                                                    disabled
                                                    id="btn-reason-delete"><?= $lang['delete']; ?></button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
    <!--End Modal Delete-->

    <!--Start Modal Upgrade-->
    <div class="mj-menu-modal">
        <div class="modal fade" id="modalUpgrade" aria-hidden="true" aria-labelledby="modalUpgradeLabel"
             tabindex="-1"
             style="height: 100vh !important;">
            <div class="modal-dialog modal-full-width">
                <div class="modal-content" style="height: 100vh;overflow-y: auto;">

                    <div class="modal-header"></div>

                    <div class="modal-body">
                        <section class="container">
                            <div class="mj-upgrade-section">
                                <div class="mj-upgrade-text">
                                    <?= $lang['u_upgrade_text_from_modal']; ?>
                                </div>
                                <div class="mj-upgrade-item-list">
                                    <a href="javascript:void(0)">
                                        <div id="fori" class="mj-upgrade-item1 mj-fori-item-2 active">
                                            <div class="mj-upgrade-title">
                                                <div><?= $lang['u_upgrade_to']; ?></div>
                                                <div><?= $lang['u_quick']; ?></div>
                                            </div>
                                            <img class="fa-bounce" src="/dist/images/poster/rocket.svg" alt="quick">
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)">
                                        <div id="lader" class="mj-upgrade-item1 mj-lader-item">
                                            <div class="mj-upgrade-title">
                                                <div><?= $lang['u_upgrade_to']; ?></div>
                                                <div><?= $lang['u_ladder']; ?></div>
                                            </div>
                                            <img src="/dist/images/poster/stars.svg" alt="ladder">
                                        </div>
                                    </a>
                                </div>

                                <div style="min-height: 400px; position:relative;">
                                    <div class="mj-fori-card mj-fori-card-2 active">
                                        <div class="mj-fori-content">
                                            <div class="mj-fori-desc"><?= $lang['u_quick_desc']; ?></div>

                                            <div class="mj-fori-date">
                                                <span><?= $lang['b_inquiry_duration_day']; ?> :</span>
                                                <span><?= Utils::getFileValue("settings.txt", 'poster_immediate_time') . " " . $lang['b_day']; ?></span>
                                            </div>

                                            <div class="mj-fori-pay">
                                                <span><?= $lang['u_price']; ?> :</span>
                                                <span><?= number_format(Utils::getFileValue("settings.txt", 'poster_immediate_price_toman')) . " " . $lang['a_toman']; ?></span>
                                            </div>

                                            <div id="fori-rocket" class="fa-rocket"></div>

                                            <div class="mj-upgrade-pay-type">
                                                <form>
                                                    <div class="mj-upgrade-wallet mj-a-border-active">
                                                        <input type="radio"
                                                               id="pay-wallet-quick"
                                                               name="upgrade-quick"
                                                               data-tj-type="wallet"
                                                               checked/>
                                                        <label class="me-2" for="pay-wallet-quick">
                                                            <div style="width: 45%;">
                                                                <?= $lang['wallet']; ?>
                                                            </div>
                                                            <div>
                                                                <div>
                                                                     <span id="wallet-balance">
                                                                        <?= number_format($BalanceAvailable); ?>
                                                                    </span>
                                                                    <span id="currency"><?= $BalanceCurrency; ?></span>
                                                                </div>
                                                                <a href="/user/wallet/deposit/1">+ <?= $lang['wallet_deposit']; ?></a>
                                                            </div>


                                                        </label>
                                                    </div>
                                                    <?php
                                                    if ($BalanceAvailable < intval(Utils::getFileValue("settings.txt", 'poster_immediate_price_toman'))) {
                                                        ?>
                                                        <span id="balance-alert3"><?= $lang['a_price_not_enough']; ?></span>
                                                    <?php } ?>
                                                    <!--<div class="mj-upgrade-bank">
                                                        <input type="radio"
                                                               id="pay-online-quick"
                                                               name="upgrade-quick"
                                                               data-tj-type="online"/>
                                                        <label for="pay-online-quick">
                                                            <span>< ?= $lang['u_direct_bank_payment']; ?></span>
                                                        </label>
                                                    </div>-->

                                                    <div class="d-flex justify-content-between">
                                                        <button class="submit-upgrade btn w-100 py-2 mj-a-btn-submit me-2"
                                                                type="button"
                                                                data-tj-poster=""
                                                                data-tj-type="quick"
                                                                id="btn-upgrade-quick">
                                                            <?= $lang['u_upgrade']; ?>
                                                        </button>
                                                        <button class="btn w-100 py-2 mj-a-btn-cancel"
                                                                type="button"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            <?= $lang['u_opt_out']; ?>
                                                        </button>
                                                    </div>

                                                    <!--<button class="mj-upgrade-button" type="button">< ?=$lang['u_upgrade'];?></button>
                                                    <button class="mj-upgrade-button"
                                                            type="button">< ?= $lang['u_opt_out']; ?></button>-->
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mj-lader-card ">
                                        <div class="mj-lader-content">
                                            <div class="mj-lader-desc"><?= $lang['u_ladder_desc']; ?></div>
                                            <div class="mj-lader-pay">
                                                <span><?= $lang['u_price']; ?> :</span>
                                                <span><?= number_format(Utils::getFileValue("settings.txt", 'poster_ladder_price_toman')) . " " . $lang['a_toman']; ?></span>
                                            </div>
                                            <div id="lader-stars" class="fa-star"></div>
                                            <div class="mj-upgrade-pay-type">
                                                <form>
                                                    <div class="mj-upgrade-wallet mj-a-border-active">
                                                        <input type="radio"
                                                               id="pay-wallet-ladder"
                                                               name="upgrade-ladder"
                                                               data-tj-type="wallet"
                                                               checked/>
                                                        <label class="me-2" for="pay-wallet-ladder">
                                                            <div style="width: 45%;">
                                                                <?= $lang['wallet']; ?>
                                                            </div>
                                                            <div>
                                                                <div>
                                                                       <span id="wallet-balance">
                                                                        <?= number_format($BalanceAvailable); ?>
                                                                    </span>
                                                                    <span id="currency"><?= $BalanceCurrency; ?></span>
                                                                </div>
                                                                <a href="/user/wallet/deposit/1">+ <?= $lang['wallet_deposit']; ?></a>
                                                            </div>

                                                        </label>
                                                    </div>
                                                    <?php
                                                    if ($BalanceAvailable < intval(Utils::getFileValue("settings.txt", 'poster_ladder_price_toman'))) {
                                                        ?>
                                                        <span id="balance-alert3"><?= $lang['a_price_not_enough']; ?></span>
                                                    <?php } ?>
                                                    <!--<div class="mj-upgrade-bank">
                                                        <input type="radio"
                                                               id="pay-online-ladder"
                                                               name="upgrade-ladder"
                                                               data-tj-type="online"/>
                                                        <label for="pay-online-ladder">
                                                            <span>< ?= $lang['u_direct_bank_payment']; ?></span>
                                                        </label>
                                                    </div>-->

                                                    <div class="d-flex justify-content-between">
                                                        <button class="submit-upgrade btn w-100 py-2 mj-a-btn-submit me-2"
                                                                type="button"
                                                                data-tj-poster=""
                                                                data-tj-type="ladder"
                                                                id="btn-upgrade-ladder">
                                                            <?= $lang['u_upgrade']; ?>
                                                        </button>
                                                        <button class="btn w-100 py-2 mj-a-btn-cancel"
                                                                type="button"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            <?= $lang['u_opt_out']; ?>
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
    <!--End Modal Upgrade-->

    <!--Start Modal Expert-->
    <div class="mj-menu-modal">
        <div class="modal fade" id="modalExpert" aria-hidden="true" aria-labelledby="modalExpert"
             tabindex="-1"
             style="height: 100vh !important;">
            <div class="modal-dialog modal-full-width">
                <div class="modal-content" style="height: 100vh;overflow-y: auto;">
                    <!--<div class="modal-header"></div>-->
                    <div class="modal-body">
                        <section class="container">
                            <div class="mj-upgrade-section">
                                <div class="mj-expert-item-list">
                                    <a href="javascript:void(0)">
                                        <div class="mj-upgrade-item mj-fori-item active">
                                            <div class="mj-upgrade-title">
                                                <div><?= $lang['u_request']; ?></div>
                                                <div><?= $lang['u_expert_officer']; ?></div>
                                            </div>
                                            <img class="fa-beat" src="/dist/images/poster/user-tie.svg" alt="rocket">
                                        </div>
                                    </a>
                                </div>
                                <div style="min-height: 400px; position:relative;">
                                    <div class="mj-fori-card active">
                                        <div class="mj-fori-content">
                                            <div class="mj-fori-desc"><?= $lang['u_expert_officer_modal_desc']; ?></div>
                                            <div class="mj-fori-date">
                                                <span><?= $lang['u_expert_officer_time']; ?> :</span>
                                                <span><?= Utils::getFileValue("settings.txt", 'poster_expert_time') . " " . $lang['b_day']; ?></span>
                                            </div>
                                            <div class="mj-fori-pay">
                                                <span><?= $lang['u_price']; ?> :</span>
                                                <span><?= number_format(Utils::getFileValue("settings.txt", 'poster_expert_price_toman')) . " " . $lang['a_toman']; ?></span>
                                            </div>
                                            <div id="fori-rocket" class="fa-user-tie"></div>
                                            <div class="mj-upgrade-pay-type">
                                                <form>
                                                    <div class="mj-upgrade-wallet mj-a-border-active">
                                                        <input type="radio"
                                                               data-tj-type="wallet"
                                                               id="pay-wallet-expert"
                                                               name="request-expert"
                                                               checked/>
                                                        <label class="me-2" for="pay-wallet-expert">
                                                            <div style="width: 45%;">
                                                                <?= $lang['wallet']; ?>
                                                            </div>
                                                            <div>
                                                                <div>
                                                                    <span id="wallet-balance">
                                                                        <?= number_format($BalanceAvailable); ?>
                                                                    </span>
                                                                    <span id="currency"><?= $BalanceCurrency; ?></span>
                                                                </div>
                                                                <a href="/user/wallet/deposit/1">+ <?= $lang['wallet_deposit']; ?></a>

                                                            </div>

                                                        </label>

                                                    </div>
                                                    <?php
                                                    if ($BalanceAvailable < intval(Utils::getFileValue("settings.txt", 'poster_expert_price_toman'))) {
                                                        ?>
                                                        <span id="balance-alert3"><?= $lang['a_price_not_enough']; ?></span>
                                                    <?php } ?>
                                                    <!--<div class="mj-upgrade-bank">
                                                        <input type="radio"
                                                               data-tj-type="online"
                                                               id="pay-online-expert"
                                                               name="request-expert"/>
                                                        <label for="pay-online-expert">
                                                            <span>< ?= $lang['u_direct_bank_payment']; ?></span>
                                                        </label>
                                                    </div>-->

                                                    <div class="mj-expert-address-area">
                                                        <textarea rows="2" id="address" name="address"
                                                                  placeholder="<?= $lang['u_alert_set_expert_4']; ?>"></textarea>
                                                    </div>


                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn w-100 py-2 mj-a-btn-submit me-2"
                                                                type="button"
                                                                data-tj-poster=""
                                                                id="btn-expert-submit">
                                                            <?= $lang['a_request_expert']; ?>
                                                        </button>
                                                        <button class="btn w-100 py-2 mj-a-btn-cancel"
                                                                type="button"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            <?= $lang['u_opt_out']; ?>
                                                        </button>
                                                    </div>


                                                    <!-- <div class="d-flex justify-content-between">
                                                         <button class="mj-upgrade-button w-100 me-2"
                                                                 id="btn-expert-submit"
                                                                 data-tj-poster="< ?= $data->poster_id; ?>"
                                                                 type="button">
                                                             < ?= $lang['a_request_expert']; ?>
                                                         </button>
                                                         <button class="mj-upgrade-button w-100"
                                                                 data-bs-dismiss="modal"
                                                                 aria-label="Close"
                                                                 type="button">
                                                             < ?= $lang['u_opt_out']; ?>
                                                         </button>
                                                     </div>-->

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
    <!--End Modal Expert-->

    <!--Start Modal Show Iframe-->
    <div class="modal fade" id="exampleModaliframe" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <iframe id="poster-detail" style="height: 100%; width: 100%" src="" frameborder="0"></iframe>
                <a href="javascript:void(0)" onclick="window.history.back()">
                    <div class="mj-backbtn"  style="z-index: 555555 !important;">
                        <div class="fa-caret-right"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!--End Modal Show Iframe-->

    <input type="hidden"
           value="<?= Security::initCSRF('poster-detail') ?>"
           data-tj-token="<?= Security::initCSRF('poster-detail') ?>"
           name="token"
           id="token">
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}