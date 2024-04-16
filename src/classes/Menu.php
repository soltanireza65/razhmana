<?php

use MJ\Router\Router;
use MJ\Security\Security;

class Menu
{

    public static function getHeaderMenu($display_lang_modal = true)
    {
        global $lang, $Settings;
        $support_count = 0;
        if (isset($_COOKIE['user-login']) && !empty($_COOKIE['user-login'])) {
            $user = User::getUserInfo();

            $support_count = User::getSupportCount(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
            if ($support_count->status == 200) {
                $support_count = $support_count->response[0]->support_badge;
            } else {
                $support_count = 0;
            }
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        ?>
        
            
        <header class="mj-app-header">
            <p class="m-0 d-inline-flex w-100 bg-warning p-1 d-flex justify-content-center">
                <a data-bs-toggle="collapse" class="w-100" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                ntirapp در شرف انتقال به راژمانا است تا زمان فعال شدن راژمانا مخاطبین با یکدیگر میتوانند در ارتباط باشند (بیشتر)
                </a>
            </p>
            <div class="collapse"  id="collapseExample">
                <div class="card card-body bg-warning" style="z-index: 1000; position: absolute;">
                این سایت و اَپهای مربوطه آن در سال ۱۴۰۳ به آدرس جدید و بنام "راژمانا" با حوزه فعالیت داخلی و بین المللی و بستر توافق طرفینی (مزایده) که بعنوان پیشران و شتاب‌دهنده "مثلث حمل و نقل جاده‌ای" خواهد بود، انتقال خواهد یافت.

                با شرح فوق بدینوسیله اعلام میگردد تا اطلاع ثانوی اطلاعات تماس پیشنهاد دهنده و پیشنهاد گیرنده برای طرفین در این وبسایت قابل مشاهده بوده و از اینرو کیفیت و نحوه ارائه خدمات صرفاً تابع تفاهم و توافق طرفینی بوده و اینتراپ و مجموعه پشتیبان آن (شرکت راه‌اندیش ژرف مانا نظر - راژمانا) هیچ گونه مسئولیتی ناشی تفاهم یا توافق طرفین را نخواهد داشت.

                با ما بمانید تا همدلانه اعتبار و اعتماد را در "مثلث حمل و نقل جاده‌ای" تعریفی نو داشته باشیم.
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-12 py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <a href="/">
                                    <img width="50" class="mj-header-logo" src="/uploads/site/logo.jpg" alt="<?= $Settings['site_name']; ?>" />
                                </a>
                            </div>

                            <?php
                            if (isset($_COOKIE['user-login']) && !empty($_COOKIE['user-login'])) {
                                ?>
                                <div class="d-flex flex-row-reverse">
                                    <?php if ($language == 'fa_IR') { ?>
                                        <a id="change-lang" href="javascript:void(0);" class="mj-app-header-btn1 btnChangeLang">
                                            <img src="/dist/images/language/ir.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'en_US') { ?>
                                        <a id="change-lang" href="javascript:void(0);" class="mj-app-header-btn1 btnChangeLang">
                                            <img src="/dist/images/language/en.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'tr_Tr') { ?>
                                        <a id="change-lang" href="javascript:void(0);" class="mj-app-header-btn1 btnChangeLang">
                                            <img src="/dist/images/language/tr.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'ru_RU') { ?>
                                        <a id="change-lang" href="javascript:void(0);" class="mj-app-header-btn1 btnChangeLang">
                                            <img src="/dist/images/language/ru.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <a href="/user/notifications" class="mj-app-header-btn mj-app-notification me-2">
                                        <div
                                            class="fa-bell <?= (User::getCountOfUnreadNotification($user->UserId) > 0) ? 'fa-shake' : '' ?>">
                                        </div>
                                        <?php
                                        if (User::getCountOfUnreadNotification($user->UserId) > 0) {
                                            ?>
                                            <span></span>
                                            <?php
                                        }
                                        ?>
                                    </a>

                                    <a href="/user/wallet" class="mj-app-header-btn me-2 mj-wallet-header-icon">
                                        <img src="/dist/images/icons/wallet(green).svg" alt="wallets" />
                                    </a>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="mj-wallet d-flex align-items-center">


                                    <?php if ($language == 'fa_IR') { ?>
                                        <a id="change-lang" class="me-2 mj-login-header-flag" href="javascript:void(0);">
                                            <img class="mj-profile-items-icon1" src="/dist/images/language/ir.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'en_US') { ?>
                                        <a id="change-lang" class="me-2 mj-login-header-flag" href="javascript:void(0);">
                                            <img class="mj-profile-items-icon1" src="/dist/images/language/en.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'tr_Tr') { ?>
                                        <a id="change-lang" class="me-2 mj-login-header-flag" href="javascript:void(0);">
                                            <img class="mj-profile-items-icon1" src="/dist/images/language/tr.svg" alt="about">
                                        </a>
                                    <?php } ?>
                                    <?php if ($language == 'ru_RU') { ?>
                                        <a id="change-lang" class="me-2 mj-login-header-flag" href="javascript:void(0);">
                                            <img class="mj-profile-items-icon1" src="/dist/images/language/ru.svg" alt="about">
                                        </a>
                                    <?php } ?>

                                    <a class="me-2 " href="/user/contact-us">
                                        <img id="phone-icon" class="mj-profile-items-icon" src="/dist/images/icons/phone.svg"
                                            alt="about">
                                    </a>
                                    <a href="/login">
                                        <img id="login-icon" class=" mj-profile-items-icon" src="/dist/images/icons/exit.svg"
                                            alt="about">
                                        <span>
                                            <?= $lang['login'] ?>
                                        </span>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-12 px-0">
                        <ul class="nav nav-tabs nav-justified mj-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="/"
                                    class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/') ? 'active' : '' ?>">
                                    <img src="/dist/images/icons/house1.svg" class="mj-header-tabs-icon" alt="home">
                                    <?= $lang['nav_home'] ?>
                                </a>
                            </li>


                            <?php

                            if (str_contains(Router::getCurrentUri(), '/user')) {
                                if (str_contains(Router::getCurrentUri(), '/user/air')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/air"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/air')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/plane-up(white).svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_air'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/ship')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/ship"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/ship')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/ship1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_ship'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/railroad')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/railroad"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/railroad')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/railroad-transportation-white.svg" class="mj-header-tabs-icon"
                                                alt="profile">
                                            <?= $lang['u_railroad'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/inventory')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/inventory"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/inventory')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/warehouse1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_inventory'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/customs')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/customs"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/customs')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/id-card1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_customs'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/drivers')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/drivers"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/drivers')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/drivercv1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_drivers_service'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif (str_contains(Router::getCurrentUri(), '/user/support')) {
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), '/user/support')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_ticket_link'] ?>
                                            <div class="mj-header-support-badge">
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>

                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/academy"
                                            class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_academy'] ?>
                                        </a>
                                    </li>
                                    <?php
                                } else { ?>

                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                            <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                        <?= $support_count ?>
                                                    </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="/academy"
                                            class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_academy'] ?>
                                        </a>
                                    </li>

                                    <?php
                                }
                            } elseif (str_contains(Router::getCurrentUri(), '/businessman')) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a href="/businessman"
                                        class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'businessman')) ? 'active' : '' ?>">
                                        <img src="/dist/images/icons/truck.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['u_ground'] ?>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="tj-b-dashboard-5">
                                    <a href="/user/support" class="nav-link  mj-header-tabs-link">
                                        <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                        <div class="mj-header-support-badge">
                                            <?php
                                            if ($support_count != 0) {
                                                ?>
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </a>
                                </li>
                                <?php
                            } elseif (str_contains(Router::getCurrentUri(), '/academy')) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a href="/academy"
                                        class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'academy')) ? 'active' : '' ?>">
                                        <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['u_academy'] ?>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="tj-b-dashboard-5">
                                    <a href="/user/support" class="nav-link  mj-header-tabs-link">
                                        <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                        <div class="mj-header-support-badge">
                                            <?php
                                            if ($support_count != 0) {
                                                ?>
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </a>
                                </li>
                                <?php
                            } elseif (str_contains(Router::getCurrentUri(), '/blog')) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a href="/academy"
                                        class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'blog')) ? 'active' : '' ?>">
                                        <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['u_academy'] ?>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="tj-b-dashboard-5">
                                    <a href="/user/support" class="nav-link  mj-header-tabs-link">
                                        <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                        <div class="mj-header-support-badge">
                                            <?php
                                            if ($support_count != 0) {
                                                ?>
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </a>
                                </li>
                                <?php
                            } elseif (str_contains(Router::getCurrentUri(), '/exchange')) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a href="/exchange"
                                        class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'exchange')) ? 'active' : '' ?>">
                                        <img src="/dist/images/icons/exchange.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['menu_exchange'] ?>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="tj-b-dashboard-5">
                                    <a href="/user/support" class="nav-link  mj-header-tabs-link">
                                        <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                        <div class="mj-header-support-badge">
                                            <?php
                                            if ($support_count != 0) {
                                                ?>
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </a>
                                </li>
                                <?php
                            } elseif (str_contains(Router::getCurrentUri(), '/poster')) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a href="/poster"
                                        class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'poster')) ? 'active' : '' ?>">
                                        <img src="/dist/images/icons/poster1.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['menu_buy_car'] ?>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="tj-b-dashboard-5">
                                    <a href="/user/support" class="nav-link  mj-header-tabs-link">
                                        <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                        <div class="mj-header-support-badge">
                                            <?php
                                            if ($support_count != 0) {
                                                ?>
                                                <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                </span>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </a>
                                </li>
                                <?php
                            } else if (str_contains(Router::getCurrentUri(), '/driver')) {
                                ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/driver"
                                            class="nav-link mj-header-tabs-link <?= (str_contains(Router::getCurrentUri(), 'driver')) ? 'active' : '' ?>">
                                            <img src="/dist/images/icons/truck.svg" class="mj-header-tabs-icon" alt="profile">
                                        <?= $lang['u_ground'] ?>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation" id="tj-d-dashboard-4">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                    </span>
                                                <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>

                                <?php
                            } else {
                                ?>
                                    <li class="nav-item" role="presentation">
                                        <a href="/user/support" class="nav-link  mj-header-tabs-link ">
                                            <img src="/dist/images/icons/headset1.svg" class="mj-header-tabs-icon" alt="support">
                                        <?= $lang['nav_support'] ?>
                                            <div class="mj-header-support-badge">
                                                <?php
                                                if ($support_count != 0) {
                                                    ?>
                                                    <span dir="ltr" class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                                    <?= $support_count ?>
                                                    </span>
                                                <?php
                                                }
                                                ?>

                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                    <?php if ($language == 'fa_IR') { ?>
                                            <a href="/academycat/19"
                                                class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                                <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_help_menu'] ?>
                                            </a>
                                    <?php } ?>
                                    <?php if ($language == 'en_US') { ?>
                                            <a href="/academycat/35"
                                                class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                                <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_help_menu'] ?>
                                            </a>
                                    <?php } ?>
                                    <?php if ($language == 'tr_Tr') { ?>
                                            <a href="/academycat/36"
                                                class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                                <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_help_menu'] ?>
                                            </a>
                                    <?php } ?>
                                    <?php if ($language == 'ru_RU') { ?>
                                            <a href="/academycat/37"
                                                class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/academy') ? 'active' : '' ?>">
                                                <img src="/dist/images/icons/blog1.svg" class="mj-header-tabs-icon" alt="profile">
                                            <?= $lang['u_help_menu'] ?>
                                            </a>
                                    <?php } ?>

                                    </li>
                                <?php
                            }

                            ?>

                            <li class="nav-item" role="presentation" id="tj-d-dashboard-5">
                                <a href="/user/profile"
                                    class="nav-link mj-header-tabs-link <?= (Router::getCurrentUri() == '/user/profile') ? 'active' : '' ?>">
                                    <img src="/dist/images/icons/user-tie1.svg" class="mj-header-tabs-icon" alt="profile">
                                    <?= $lang['nav_profile'] ?>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </header>
        <?php if ($display_lang_modal) { ?>


            <!--        <div class="mj-languange-modal-background <?php /*= (!isset($_COOKIE['language'])) ? null : 'd-none'; */ ?>"
             id="lang-modal-h">
            <div class="mj-language-select p-2 ">
                <div class="fa-language">

                </div>

                <div class="mj-lang-items">
                    <div class="radio">
                        <input id="lang-en" value="en_US" name="lang-radio"
                               type="radio" <?php /*= ($language == "en_US") ? 'checked' : null; */ ?>>
                        <label for="lang-en" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/en.svg" alt="EN">
                                <span>EN</span>
                            </div>
                        </label>
                    </div>
                    <div class="radio">
                        <input id="lang-ir" value="fa_IR" name="lang-radio"
                               type="radio" <?php /*= ($language == "fa_IR") ? 'checked' : null; */ ?>>
                        <label for="lang-ir" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/ir.svg" alt="IR">
                                <span>IR</span>
                            </div>
                        </label>
                    </div>

                    <div class="radio">
                        <input id="lang-tr" value="tr_Tr" name="lang-radio"
                               type="radio" <?php /*= ($language == "tr_Tr") ? 'checked' : null; */ ?>>
                        <label for="lang-tr" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/tr.svg" alt="TR">
                                <span>TR</span>
                            </div>
                        </label>
                    </div>
                    <div class="radio">
                        <input id="radio-ru" value="ru_RU" name="lang-radio"
                               type="radio" <?php /*= ($language == "ru_RU") ? 'checked' : null; */ ?>>
                        <label for="radio-ru" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/ru.svg" alt="RU">
                                <span>RU</span>
                            </div>
                        </label>
                    </div>


                </div>
            </div>
        </div>
-->
            <?php

        }
    }

    public static function getFooterMenu()
    {

    }

    public static function getFooterMenu_main()
    {
        global $lang;
        if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
            ?>
            <nav class="mj-menu-box">
                <a href="javascript:void(0)" onclick="window.history.back()">
                    <div class="mj-dashboard-backbtn">
                        <img src="../dist/images/icons/caret-right(white).svg" alt="back">
                    </div>
                </a>
                <ul class="mj-menu">
                    <li class="mj-menu-item">
                        <?php
                        if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
                            ?>
                            <a class="mj-menu-link" href="/businessman">
                                <img src="/dist/images/icons/truck.svg" alt="transportation"
                                    style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                            </a>
                            <?php
                        } else if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
                            ?>
                                <a class="mj-menu-link" href="/driver">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } else {
                            ?>
                                <a class="mj-menu-link" href="/">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } ?>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/ship">
                            <img src="/dist/images/icons/ship/ship(white).svg" alt="buy-car"
                                style="background: linear-gradient(180deg, #ff8f8f, #ec3939)">
                            <?= $lang['h_inquiry_ship'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/air">
                            <img src="/dist/images/icons/air-transportation-white.svg" alt="insurance"
                                style="background: linear-gradient(180deg, #dd96ff, #b821ff)">
                            <?= $lang['h_inquiry_air'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/railroad">
                            <img src="/dist/images/icons/road.svg" alt="spare-parts"
                                style="background: linear-gradient(180deg, #00ff92, #04bc68)">
                            <?= $lang['h_inquiry_rail'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/inventory">
                            <img src="/dist/images/icons/inventory.svg" alt="exchange"
                                style="background: linear-gradient(180deg, #ffae9c, #ff2e00)">
                            <?= $lang['h_inquiry_inventory'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="bank"
                                style="background: linear-gradient(180deg, #a175ff, #5200ff)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="driver-services"
                                style="background: linear-gradient(180deg, #ea0881, #4c072c)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="read-services"
                                style="background: linear-gradient(180deg, #92e4ff, #0089b5)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                </ul>
                <button type="button" class="mj-menu-expand">
                    <img src="/dist/images/icons/arrow-up.svg" alt="expand-menu" />
                </button>
            </nav>
            <?php
        } elseif (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
            ?>
            <nav class="mj-menu-box">
                <a href="javascript:void(0)" onclick="window.history.back()">
                    <div class="mj-dashboard-backbtn">
                        <img src="../dist/images/icons/caret-right(white).svg" alt="back">
                    </div>
                </a>
                <ul class="mj-menu">
                    <li class="mj-menu-item">
                        <?php
                        if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
                            ?>
                            <a class="mj-menu-link" href="/businessman">
                                <img src="/dist/images/icons/truck.svg" alt="transportation"
                                    style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                            </a>
                            <?php
                        } else if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
                            ?>
                                <a class="mj-menu-link" href="/driver">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } else {
                            ?>
                                <a class="mj-menu-link" href="/">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } ?>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/ship">
                            <img src="/dist/images/icons/ship/ship(white).svg" alt="buy-car"
                                style="background: linear-gradient(180deg, #ff8f8f, #ec3939)">
                            <?= $lang['h_inquiry_ship'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/air">
                            <img src="/dist/images/icons/air-transportation-white.svg" alt="insurance"
                                style="background: linear-gradient(180deg, #dd96ff, #b821ff)">
                            <?= $lang['h_inquiry_air'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/railroad">
                            <img src="/dist/images/icons/road.svg" alt="spare-parts"
                                style="background: linear-gradient(180deg, #00ff92, #04bc68)">
                            <?= $lang['h_inquiry_rail'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/inventory">
                            <img src="/dist/images/icons/inventory.svg" alt="exchange"
                                style="background: linear-gradient(180deg, #ffae9c, #ff2e00)">
                            <?= $lang['h_inquiry_inventory'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="bank"
                                style="background: linear-gradient(180deg, #a175ff, #5200ff)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="driver-services"
                                style="background: linear-gradient(180deg, #ea0881, #4c072c)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="read-services"
                                style="background: linear-gradient(180deg, #92e4ff, #0089b5)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                </ul>
                <button type="button" class="mj-menu-expand">
                    <img src="/dist/images/icons/arrow-up.svg" alt="expand-menu" />
                </button>
            </nav>
            <?php
        } elseif (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'guest') {
            ?>
            <nav class="mj-menu-box">
                <a href="javascript:void(0)" onclick="window.history.back()">
                    <div class="mj-dashboard-backbtn">
                        <img src="../dist/images/icons/caret-right(white).svg" alt="back">
                    </div>
                </a>
                <ul class="mj-menu">
                    <li class="mj-menu-item">
                        <?php
                        if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
                            ?>
                            <a class="mj-menu-link" href="/businessman">
                                <img src="/dist/images/icons/truck.svg" alt="transportation"
                                    style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                            </a>
                            <?php
                        } else if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
                            ?>
                                <a class="mj-menu-link" href="/driver">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } else {
                            ?>
                                <a class="mj-menu-link" href="/">
                                    <img src="/dist/images/icons/truck.svg" alt="transportation"
                                        style="background: linear-gradient(180deg, var(--primary), #07224c)">
                                <?= $lang['h_inquiry_ground'] ?>
                                </a>
                            <?php
                        } ?>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/ship">
                            <img src="/dist/images/icons/ship/ship(white).svg" alt="buy-car"
                                style="background: linear-gradient(180deg, #ff8f8f, #ec3939)">
                            <?= $lang['h_inquiry_ship'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/air">
                            <img src="/dist/images/icons/air-transportation-white.svg" alt="insurance"
                                style="background: linear-gradient(180deg, #dd96ff, #b821ff)">
                            <?= $lang['h_inquiry_air'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/railroad">
                            <img src="/dist/images/icons/road.svg" alt="spare-parts"
                                style="background: linear-gradient(180deg, #00ff92, #04bc68)">
                            <?= $lang['h_inquiry_rail'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/user/inventory">
                            <img src="/dist/images/icons/inventory.svg" alt="exchange"
                                style="background: linear-gradient(180deg, #ffae9c, #ff2e00)">
                            <?= $lang['h_inquiry_inventory'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="bank"
                                style="background: linear-gradient(180deg, #a175ff, #5200ff)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="driver-services"
                                style="background: linear-gradient(180deg, #ea0881, #4c072c)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                    <li class="mj-menu-item">
                        <a class="mj-menu-link" href="/">
                            <img src="/dist/images/icons/circle-question(white).svg" alt="read-services"
                                style="background: linear-gradient(180deg, #92e4ff, #0089b5)">
                            <?= $lang['soon_exchange'] ?>
                        </a>
                    </li>
                </ul>
                <button type="button" class="mj-menu-expand">
                    <img src="/dist/images/icons/arrow-up.svg" alt="expand-menu" />
                </button>
            </nav>
            <?php
        } else {

        }
    }
}