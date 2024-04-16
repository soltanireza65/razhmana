<?php

global $lang;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    include_once 'header-footer.php';
    enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');
    enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('my-cargoes-init', '/dist/js/businessman/my-cargoes.init.js');
    getHeader($lang['b_title_my_cargos']);

    ?>
    <main style="padding-top: 110px !important;padding-bottom: 60px !important;  ">
        <section>

            <div class="mj-wallet-head-blue">
                <div class="mj-wallet-blue">
                    <?= $lang['b_mycargo_my_cargo_list'] ?>
                </div>
                <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
                </svg>
                <div class="mj-transaction-list">
                    <div class="row mj-list-header-btn">
                        <div class="col-6">
                            <div class="mb-3">
                                <a href="/businessman/add-cargo"
                                   class="mj-btn-more mj-fw-400 mj-font-12 py-2" style="">
                                    <img src="/dist/images/icons/circle-plus.svg" alt="">
                                    &nbsp;<?= $lang['b_add_cargo_1'] ?> &nbsp;</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <a href="/businessman/add-cargo-in"
                                   class="mj-btn-more mj-fw-400 mj-font-12 py-2">
                                    <img src="/dist/images/icons/circle-plus.svg" alt="">
                                    &nbsp;<?= $lang['b_add_cargo_in_1'] ?> &nbsp;</a>
                            </div>
                        </div>
                    </div>
                    <div class="mj-trx-head">
                        <div class="mj-trx-list-title"><?= $lang['b_mycargo_my_cargo_list'] ?> :</div>
                        <div class="mj-trx-operation-btns">
                            <div class="mj-search-btn me-2">
                                <div class="fa-search"></div>
                            </div>
                            <div class="mj-filter-btn">
                                <div class="fa-sliders"></div>

                            </div>
                            <div class="mj-filter-dropdown mj-mycargo-items ">
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="out">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-icon">
                                                <img src="/dist/images/outer.svg" alt="">
                                            </div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_international'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="in">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-icon">
                                                <img src="/dist/images/inter.svg" alt="">
                                            </div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_in'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="all">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color all"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_all'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="pending">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color pending"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_pending'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="accepted">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color accept"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_accepted'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="process">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color process"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_process'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="completed">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color complete"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_completed'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="expired">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color expire"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_expired'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="rejected">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color abort"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_rejected'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="mj-mycargo-list-filter-item">
                                    <label>
                                        <input type="checkbox" value="canceled">
                                        <div class="mj-filter-item-content">
                                            <div class="mj-mycargo-filter-item-color canceled"></div>
                                            <div class="mj-mycargo-filter-item-name">
                                                <?= $lang['u_filter_cargo_canceled'] ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <button class="mj-filter-apply-my-cargoes">
                                    <?= $lang['u_cargo_apply_filter'] ?>
                                </button>
                                <button class="mj-filter-close-btn">
                                     <?= $lang['closes'] ?>
                                </button>
                            </div>

                        </div>

                    </div>
                    <div class="mj-filter-cargoes-refresh d-none">
                        <button>
                            <div class="fa-refresh"></div>
                            <?=$lang['u_filter_cargoes_refresh']?></button>
                    </div>
                    <div class="mj-trx-search">
                        <form action="" class="mj-trx-serach-form">
                            <input type="text" id="tx-search"
                                   placeholder="<?= $lang['transaction_list_search_placeholder'] ?>">
                            <button type="button">
                                <div class="fa-search"></div>
                            </button>
                        </form>
                    </div>
                    <img id="trx-image" src="/dist/images/wallet/trx-img.svg" alt="">
                    <div class="mj-transaction-items">
                    </div>
                    <div class="mj-trx-list-load d-none">
                        <lottie-player src="/dist/lottie/wallet-load.json" background="transparent" speed="1" loop
                                       autoplay></lottie-player>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>

        let cargo_status = ['<?= !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'all' ?>'];

    </script>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}