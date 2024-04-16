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

include_once getcwd() . '/views/user/header-footer.php';

enqueueStylesheet('poster-css', '/dist/css/poster/expert.css');
enqueueStylesheet('poster-css', '/dist/libs/fontawesome/all.css');

enqueueScript('swiper-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('swiper-js', '/dist/js/poster/expert.js');


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


    <main class="container" style="padding-bottom: 25px !important;">
        <div class="mj-upgrade-section">
            <div class="mj-expert-item-list">
                <a href="javascript:void(0)">
                    <div class="mj-upgrade-item mj-fori-item active">
                        <div class="mj-upgrade-title">
                            <div>درخواست</div>
                            <div>کارشناس رسمی</div>
                        </div>
                        <img class="fa-beat" src="/dist/images/poster/user-tie.svg" alt="rocket">
                    </div>
                </a>
            </div>
            <div style="min-height: 500px; position:relative;">
            <div class="mj-fori-card active">
                <div class="mj-fori-content">
                    <div class="mj-fori-desc">
                    در این قسمت شما شما برای آگهی خود درخواست کارشناسی میدهید تا کارشناس خرامان خرامان به دیدار شما بیاید
                        و ماشین/تریلر شما را مورد بررسی قرار دهد
                    </div>
                    <div class="mj-fori-date">
                        <span>مدت زمان :</span>
                        <span>3 روز</span>
                    </div>
                    <div class="mj-fori-pay">
                        <span>قیمت :</span>
                        <span>20 هزار تومان</span>
                    </div>
<!--                    <div id="fori-rocket" class="fa-user-tie"></div>-->
                    <div class="mj-upgrade-pay-type">
                        <form>
                            <div class="mj-upgrade-bank">
                                <input  type="radio" id="bank2" name="contact" checked/>
                                <label for="bank2">
                                        <span>پرداخت مستقیم بانکی</span>
                                </label>

                            </div>
                            <div class="mj-upgrade-wallet ">
                                <input  type="radio" id="wallet2" name="contact" />
                                <label class="me-2" for="wallet2">
                                    <div style="width: 45%;">
                                        کیف پول
                                    </div>
                                    <div>
                                        <div>
                                            <span id="wallet-balance">100,000,000,000</span>
                                            <span id="currency">تومان</span>
                                        </div>
                                        <a href="user/wallet">+افزایش موجودی</a>

                                    </div>
                                    <span id="balance-alert">موجوی کیف پول کافی نیست</span>

                                </label>
                            </div>
                            <button class="mj-upgrade-button" type="submit">درخواست کارشناس</button>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>



    </main>
<?php
getFooter('', false);
?>