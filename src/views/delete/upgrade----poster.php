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

enqueueStylesheet('poster-css', '/dist/css/poster/upgrade.css');
enqueueStylesheet('poster-css', '/dist/libs/fontawesome/all.css');

enqueueScript('swiper-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('swiper-js', '/dist/js/poster/upgrade.js');


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
            <div class="mj-upgrade-text">
                ارتقای آگهی خود جهت بهتر دیده شدن
            </div>
            <div class="mj-upgrade-item-list">
                <a href="javascript:void(0)">
                    <div id="fori" class="mj-upgrade-item mj-fori-item active">
                        <div class="mj-upgrade-title">
                            <div>ارتقا به</div>
                            <div>فــوری</div>
                        </div>
                        <img class="fa-bounce"  src="/dist/images/poster/rocket.svg" alt="rocket">
                    </div>
                </a>
                <a href="javascript:void(0)">
                    <div id="lader" class="mj-upgrade-item mj-lader-item">
                        <div class="mj-upgrade-title">
                            <div>ارتقا به</div>
                            <div>نــردبان</div>
                        </div>
                        <img  src="/dist/images/poster/stars.svg" alt="rocket">
                    </div>
                </a>
            </div>
            <div style="min-height: 500px; position:relative;">
            <div class="mj-fori-card active">
                <div class="mj-fori-content">
                    <div class="mj-fori-desc">
                        ارتقای آگهی به حالت فوری شما را فوری میکنید و ماشین شاما به صورت فوری به فروش میرود و اگر در 3
                        روز نرود نردبانش کنید تا برود
                    </div>
                    <div class="mj-fori-date">
                        <span>مدت زمان :</span>
                        <span>3 روز</span>
                    </div>
                    <div class="mj-fori-pay">
                        <span>قیمت :</span>
                        <span>20 هزار تومان</span>
                    </div>
                    <div id="fori-rocket" class="fa-rocket"></div>
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
                            <button class="mj-upgrade-button" type="submit">ارتقا</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mj-lader-card ">
                <div class="mj-lader-content">
                    <div class="mj-lader-desc">
                        ارتقای آگهی به حالت فوری شما را فوری میکنید و ماشین شاما به صورت فوری به فروش میرود و اگر در 3
                        روز نرود نردبانش کنید تا برود
                    </div>
                    <div class="mj-lader-date">
                        <span>مدت زمان :</span>
                        <span>3 روز</span>
                    </div>
                    <div class="mj-lader-pay">
                        <span>قیمت :</span>
                        <span>20 هزار تومان</span>
                    </div>
                    <div id="lader-stars" class="fa-star"></div>
                    <div class="mj-upgrade-pay-type">
                        <form>
                            <div class="mj-upgrade-bank">
                                <input  type="radio" id="bank" name="contact" checked/>
                                <label for="bank">
                                    <span>پرداخت مستقیم بانکی</span>
                                </label>

                            </div>
                            <div class="mj-upgrade-wallet ">
                                <input  type="radio" id="wallet" name="contact" />
                                <label class="me-2" for="wallet">
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
                            <button class="mj-upgrade-button" type="submit">ارتقا</button>
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