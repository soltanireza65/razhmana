<?php

global $lang;
global $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;


include_once getcwd() . '/views/site/header-footer.php';

enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');

enqueueStylesheet('detail-css', '/dist/css/exchange/exchange.css');
enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');


enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('fontawesome-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('select2-js', '/dist/js/site/exchange/exchange.js');


getHeader($lang['menu_exchange']);

$language = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
}

$ir_prices = Exchange::getPricesFromBonbast('ir_order_status');
$ir_prices = $ir_prices->status == 200 ? $ir_prices->response : [];
$ru_prices = Exchange::getPricesFromBonbast('ru_order_status');
$ru_prices = $ru_prices->status == 200 ? $ru_prices->response : [];
$du_prices = Exchange::getPricesFromBonbast('du_order_status');
$du_prices = $du_prices->status == 200 ? $du_prices->response : [];
$tr_prices = Exchange::getPricesFromBonbast('tr_order_status');
$tr_prices = $tr_prices->status == 200 ? $tr_prices->response : [];


//modal


$slug = 'title_' . $language;
$login_flag = isset($_COOKIE['user-login']) ? '' : 'd-none';
?>
    <style>
        #tradeModal{
            max-width: 483px !important;
            margin: auto !important;
            right: 50% !important;
            transform: translateX(50%) !important;
        }
        .modal-backdrop{
            max-width: 500px;
            right: 50%;
            transform: translateX(50%);
        }
    </style>
    <!--  buy modal start  -->
    <div class="modal fade mj-buy-sell-modal" id="tradeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel1"><?= $lang['buy_sell_currencies'] ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link mj-buy-tab-link " id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home-tab-pane" type="button" role="tab"
                                    aria-controls="home-tab-pane" aria-selected="true"><?= $lang['buy'] ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link mj-sell-tab-link" id="profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-tab-pane" type="button" role="tab"
                                    aria-controls="profile-tab-pane" aria-selected="false"><?= $lang['sell'] ?>
                            </button>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active mj-buy-tab-content" id="home-tab-pane" role="tabpanel"
                             aria-labelledby="home-tab" tabindex="0">
                            <div class="mj-currency-select2 mj-select2">
                                <span><?= $lang['buy_curreny_text'] ?> :</span>
                                <span name="" id="currency-select">
                                </span>
                            </div>
                            <div class="mj-currency-price buy">
                                <span> <?= $lang['buy_price'] ?> :</span>
                                <div>
                                    <span id="buy-price"></span>
                                    <span id="currency-unit"><?= $lang['toman'] ?></span>
                                </div>
                            </div>
                            <div class="mj-currency-quantity-input">
                                <input type="number" id="buy-count"
                                       placeholder="<?= $lang['currency_count_placeholder'] ?>">
                            </div>
                            <p>
                                <?= $lang['currency_price_alert'] ?>

                            </p>
                            <div class="mj-buy-result-box">
                                <span>
                                    <?= $lang['ask_total_price'] ?>
                                </span>
                                <div>
                                    <span id="result-buy"></span>
                                    <span><?= $lang['toman'] ?></span>
                                </div>

                            </div>

                            <button id="buy-btn" class="mj-trade-currency-btn buy">
                                <?= $lang['submit'] ?>
                            </button>
                        </div>
                        <div class="tab-pane fade mj-sell-tab-content" id="profile-tab-pane" role="tabpanel"
                             aria-labelledby="profile-tab"
                             tabindex="0">
                            <div class="mj-currency-select2 mj-select2">
                                <span><?= $lang['sell_curreny_text'] ?> :</span>
                                <span id="currency-sell-select">

                                </span>
                            </div>
                            <div id="buy-proce" class="mj-currency-price sell">
                                <span>   <?= $lang['sell_price'] ?> :</span>
                                <div>
                                    <span id="sell-price"> </span>
                                    <span id="currency-unit">  <?= $lang['toman'] ?></span>
                                </div>
                            </div>
                            <div class="mj-currency-quantity-input">
                                <input type="number" id="sell-count"
                                       placeholder="<?= $lang['currency_count_placeholder'] ?>">
                            </div>
                            <p>
                                <?= $lang['currency_price_alert'] ?>
                            </p>
                            <div class="mj-sell-result-box">
                                <span>
                                    <?= $lang['ask_total_price'] ?>
                                </span>
                                <div>
                                    <span id="result-sell"></span>
                                    <span><?= $lang['toman'] ?></span>
                                </div>

                            </div>
                            <button id="sell-btn" class="mj-trade-currency-btn sell">
                                <?= $lang['submit'] ?>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--  buy modal end  -->
    <!--  buy modal start  -->
    <div class="modal fade mj-result-modal" id="result-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <div id="l-player">

                    </div>

                    <p id="request-result">
                    </p>
                </div>

            </div>
        </div>
    </div>
    <!--  buy modal end  -->

    <main class="mj-container">

        <div class="container">
            <!-- alert start -->
            <section class="mj-sad-exchange-alert-sec">
                <span><?= $lang['info'] ?>:</span>
                <div><?= $lang['u_exchange_base_currency'] ?></div>
            </section>
            <!-- alert end -->
            <!-- currency start -->
            <section class="mj-sad-currency-section">
                <div class="mj-sad-currency-header">
                    <span><?= $lang['ir_currency_exchange'] ?></span>
                    <span><img src="/dist/images/chart-line-up.svg" alt=""></span>
                    <span><img src="/dist/images/chart-line-down.svg" alt=""></span>
                </div>
                <div class="mj-sad-currency-body">
                    <?php foreach ($ir_prices as $price) {
                        ?>
                        <div class="mj-sad-currency-item">
                            <div class="mj-sad-currency-item-title"><?= $price->$slug ?></div>
                            <div class="mj-sad-currency-item-buy">
                                <div
                                    class="mj-sad-currency-item-buy-value"><?= number_format($price->price_buy + floatval($price->ir_plus_value)) ?></div>
                                <div class="mj-sad-currency-item-buy-btn">
                                    <button
                                        data-type-request="ir" data-price-id="<?= $price->id ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->ir_plus_value) ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->ir_mines_value) ?>"
                                        class="buy-modal <?= $login_flag ?>">
                                        <?= $lang['buy'] ?>
                                    </button>
                                </div>
                            </div>
                            <div class="mj-sad-currency-item-sell">
                                <div
                                    class="mj-sad-currency-item-sell-value"><?= number_format($price->price_sell + floatval($price->ir_mines_value)) ?></div>
                                <div class="mj-sad-currency-item-sell-btn">
                                    <button
                                        data-type-request="ir" data-price-id="<?= $price->id ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->ir_mines_value) ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->ir_plus_value) ?>"
                                        class="sell-modal <?= $login_flag ?>">
                                        <?= $lang['sell'] ?>
                                    </button>

                                </div>
                            </div>

                        </div>
                    <?php } ?>


                </div>
            </section>
            <!-- currency end -->
            <!-- poster start   -->
            <section class="mj-sad-exchange-poster-section">
                <a href="javascript:void(0);" class="mj-sad-exchange-poster-a">
                    <img src="<?=$Settings['exchange_poster1']?>">
                </a>
            </section>
            <!-- poster end   -->
            <!-- currency start -->
            <section class="mj-sad-currency-section">
                <div class="mj-sad-currency-header">
                    <span><?= $lang['ru_currency_exchange'] ?></span>
                    <span><img src="/dist/images/chart-line-up.svg" alt=""></span>
                    <span><img src="/dist/images/chart-line-down.svg" alt=""></span>
                </div>
                <div class="mj-sad-currency-body">
                    <?php foreach ($ru_prices as $price) {
                        ?>
                        <div class="mj-sad-currency-item">
                            <div class="mj-sad-currency-item-title"><?= $price->$slug ?></div>

                            <div class="mj-sad-currency-item-buy">
                                <div
                                    class="mj-sad-currency-item-buy-value"><?= number_format($price->price_buy + floatval($price->ru_plus_value)) ?></div>
                                <div class="mj-sad-currency-item-buy-btn">
                                    <button
                                        data-type-request="ru" data-price-id="<?= $price->id ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->ru_plus_value) ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->ru_mines_value) ?>"
                                        class="buy-modal <?= $login_flag ?>">
                                        <?= $lang['buy'] ?>
                                    </button>
                                </div>
                            </div>
                            <div class="mj-sad-currency-item-sell">
                                <div
                                    class="mj-sad-currency-item-sell-value"><?= number_format($price->price_sell + floatval($price->ru_mines_value)) ?></div>
                                <div class="mj-sad-currency-item-sell-btn">
                                    <button
                                        data-type-request="ru" data-price-id="<?= $price->id ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->ru_mines_value) ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->ru_plus_value) ?>"
                                        class="sell-modal <?= $login_flag ?>">
                                        <?= $lang['sell'] ?>
                                    </button>

                                </div>
                            </div>

                        </div>
                    <?php } ?>


                </div>
            </section>
            <!-- currency end -->
            <!-- poster start   -->
            <section class="mj-sad-exchange-poster-section">
                <a href="javascript:void(0);" class="mj-sad-exchange-poster-a">
                    <img src="<?=$Settings['exchange_poster2']?>">
                </a>
            </section>
            <!-- poster end   -->
            <!-- currency start -->
            <section class="mj-sad-currency-section">
                <div class="mj-sad-currency-header">
                    <span><?= $lang['du_currency_exchange'] ?></span>
                    <span><img src="/dist/images/chart-line-up.svg" alt=""></span>
                    <span><img src="/dist/images/chart-line-down.svg" alt=""></span>
                </div>
                <div class="mj-sad-currency-body">
                    <?php foreach ($du_prices as $price) {
                        ?>
                        <div class="mj-sad-currency-item">
                            <div class="mj-sad-currency-item-title"><?= $price->$slug ?></div>


                            <div class="mj-sad-currency-item-buy">
                                <div
                                    class="mj-sad-currency-item-buy-value"><?= number_format($price->price_buy + floatval($price->du_plus_value)) ?></div>
                                <div class="mj-sad-currency-item-buy-btn">
                                    <button
                                        data-type-request="du" data-price-id="<?= $price->id ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->du_plus_value) ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->du_mines_value) ?>"
                                        class="buy-modal <?= $login_flag ?>">
                                        <?= $lang['buy'] ?>
                                    </button>
                                </div>
                            </div>
                            <div class="mj-sad-currency-item-sell">
                                <div
                                    class="mj-sad-currency-item-sell-value"><?= number_format($price->price_sell + floatval($price->du_mines_value)) ?></div>
                                <div class="mj-sad-currency-item-sell-btn">
                                    <button
                                        data-type-request="du" data-price-id="<?= $price->id ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->du_mines_value) ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->du_plus_value) ?>"
                                        class="sell-modal <?= $login_flag ?>">
                                        <?= $lang['sell'] ?>
                                    </button>

                                </div>
                            </div>

                        </div>
                    <?php } ?>


                </div>
            </section>
            <!-- currency end -->
            <!-- poster start   -->
            <section class="mj-sad-exchange-poster-section">
                <a href="javascript:void(0);" class="mj-sad-exchange-poster-a">
                    <img src="<?=$Settings['exchange_poster3']?>">
                </a>
            </section>
            <!-- poster end   -->
            <!-- currency start -->
            <section class="mj-sad-currency-section">
                <div class="mj-sad-currency-header">
                    <span><?= $lang['tr_currency_exchange'] ?></span>
                    <span><img src="/dist/images/chart-line-up.svg" alt=""></span>
                    <span><img src="/dist/images/chart-line-down.svg" alt=""></span>
                </div>
                <div class="mj-sad-currency-body">
                    <?php foreach ($tr_prices as $price) {
                        ?>
                        <div class="mj-sad-currency-item">
                            <div class="mj-sad-currency-item-title"><?= $price->$slug ?></div>

                            <div class="mj-sad-currency-item-buy">
                                <div
                                    class="mj-sad-currency-item-buy-value"><?= number_format($price->price_buy + floatval($price->tr_plus_value)) ?></div>
                                <div class="mj-sad-currency-item-buy-btn">
                                    <button
                                        data-type-request="tr" data-price-id="<?= $price->id ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->tr_plus_value) ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->tr_mines_value) ?>"
                                        class="buy-modal <?= $login_flag ?>">
                                        <?= $lang['buy'] ?>
                                    </button>
                                </div>
                            </div>
                            <div class="mj-sad-currency-item-sell">
                                <div
                                    class="mj-sad-currency-item-sell-value"><?= number_format($price->price_sell + floatval($price->tr_mines_value)) ?></div>
                                <div class="mj-sad-currency-item-sell-btn">
                                    <button
                                        data-type-request="tr" data-price-id="<?= $price->id ?>"
                                        data-price-sell="<?= $price->price_sell + floatval($price->tr_mines_value) ?>"
                                        data-price-buy="<?= $price->price_buy + floatval($price->tr_plus_value) ?>"
                                        class="sell-modal <?= $login_flag ?>">
                                        <?= $lang['sell'] ?>
                                    </button>

                                </div>
                            </div>

                        </div>
                    <?php } ?>


                </div>
            </section>
            <!-- currency end -->
            <!-- poster start   -->
            <section class="mj-sad-exchange-poster-section">
                <a href="javascript:void(0);" class="mj-sad-exchange-poster-a">
                    <img src="<?=$Settings['exchange_poster4']?>">
                </a>
            </section>
            <!-- poster end   -->
        </div>

    </main>
    <script>
        let ir_prices = JSON.parse('<?=json_encode($ir_prices);?>')
        let ru_prices = JSON.parse('<?=json_encode($ru_prices);?>')
        let du_prices = JSON.parse('<?=json_encode($du_prices);?>')
        let tr_prices = JSON.parse('<?=json_encode($tr_prices);?>')
        let slug = '<?=$slug?>'
    </script>
<?php
getFooter();