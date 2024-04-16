<?php


use MJ\HTML\Builder;
use MJ\Router\Router;
use MJ\Utils\Utils;
include_once 'classes/Menu.php';
$stylesheets = [];
$headerScripts = [];
$footerScripts = [];

function enqueueStylesheet($name, $src, $rel = 'stylesheet', $type = 'text/css', $version = '1.1.0')
{
    global $stylesheets, $Settings;
    $version = $Settings['site_version'];
    $stylesheets[] = [
        'name' => $name,
        'src' => "{$src}?v={$version}",
        'rel' => $rel,
        'type' => $type
    ];
}

function enqueueScript($name, $src, $type = 'text/javascript', $version = '1.1.0', $inFooter = true)
{
    global $headerScripts, $footerScripts, $Settings;
    $version = $Settings['site_version'];
    if ($inFooter) {
        $footerScripts[] = [
            'name' => $name,
            'src' => "{$src}?v={$version}",
            'type' => $type
        ];
    } else {
        $headerScripts[] = [
            'name' => $name,
            'src' => "{$src}?v={$version}",
            'type' => $type
        ];
    }
}

function getHeader($title = '', $showMenu=true)
{
    global $stylesheets, $headerScripts, $lang, $Settings;
    $version = $Settings['site_version'];
    if (!isset($stylesheets)) {
        $stylesheets = [];
    }

    if (!isset($headerScripts)) {
        $headerScripts = [];
    }
//    if(!isset($_COOKIE['language'])){
//        header("Location: /lang");
//    }
    ?>
    <!doctype html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?= $title . " | " . $Settings['site_name'] ?></title>
        <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">
        <link rel="icon"  href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">

        <link rel="apple-touch-icon" sizes="144x144" href="<?= SITE_URL ?>/uploads/site/144.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/uploads/site/180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="<?= SITE_URL ?>/uploads/site/192.png">
        <link rel="apple-touch-icon" sizes="384x384" href="<?= SITE_URL ?>/uploads/site/384.png">
        <link rel="apple-touch-icon" sizes="512x512" href="<?= SITE_URL ?>/uploads/site/512.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <link href="/dist/css/driver/app-rtl.min.css?v=<?= $version ?>" rel="stylesheet" type="text/css"
              id="app-style"/>
        <link rel="stylesheet" type="text/css"
              href="/dist/libs/jquery-toast-plugin/jquery.toast.min.css?v=<?= $version ?>"/>

        <?php
        Builder::loadStylesheet($stylesheets);
        ?>
        <link rel="stylesheet" type="text/css" href="/dist/css/all.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/css/driver/drivers.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/css/user.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/libs/fontawesome/all.css?v=<?= $version ?>"/>
        <link href="<?= SITE_URL; ?>/dist/css/driver/icons.min.css?v=<?= $version ?>" rel="stylesheet" type="text/css"/>
        <script src="<?= SITE_URL ?>/dist/js/all.init.js"></script>
        <?php
        Builder::loadScripts($headerScripts);
        ?>
        <?php
        if (isset($_COOKIE['language']) && substr($_COOKIE['language'], 0, 2) != 'fa') {
            ?>
            <link rel="stylesheet" type="text/css" href="/dist/css/driver/driver-ltr.css?v=<?= $version ?>">
            <link rel="stylesheet" type="text/css" href="/dist/css/all-ltr.css?v=<?= $version ?>">
            <?php if( Router::getCurrentUri() != '/poster/add'  ){
                ?>
                <link href="/dist/css/businessman/app-ltr.min.css?v=<?= $version ?>" rel="stylesheet" type="text/css"
                      id="app-style"/>
                    <?php
            }?>

            <?php
        }
        $init_lang = 'fa';
        if (isset($_COOKIE['language'])) {
            $init_lang = substr($_COOKIE['language'], 0, 2);
        }
        ?>
        <link rel="manifest" href="/manifest.webmanifest">
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-KJMVSFM');</script>
        <!-- End Google Tag Manager -->
    </head>

    <body class="mj-bg-background" dir="rtl" lang="<?=$init_lang ?>">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KJMVSFM"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <script type="text/javascript">
        let lang_vars = <?= json_encode($lang) ?>;
    </script>


    <?php
    if($showMenu) {
        Menu::getHeaderMenu();
    }
    }
    function getFooter($version = '1.2.0'  , $display_footer = true,$showBack=true)
    {
    global $footerScripts, $lang, $Settings;
    $version = $Settings['site_version'];
    if (!isset($footerScripts)) {
        $footerScripts = [];
    }
    ?>
    <div class="mj-toast-box">
        <div class="toast-container p-3 top-0 mx-auto">
            <div class="toast bg-white rounded-3 fade show" data-bs-autohide="false">
                <div class="toast-header">
                    <strong class="text-dark me-auto"><?= $lang['install_title'] ?></strong>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
                <div class="toast-body ">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>
                            <?= $lang['install_text'] ?>
                        </span>

                        <button type="button" id="btn-install" name="btn-install"
                                class="btn btn-xs btn-primary waves-effect waves-light shadow-none rounded-3">
                            <?= $lang['install_button'] ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if ($display_footer){
    Menu::getFooterMenu();
}
if($showBack){
?>
    <a href="javascript:void(0)" onclick="window.history.back()">
        <div class="mj-backbtn" >
            <div class="fa-caret-right"></div>
        </div>
    </a>
<?php } ?>
    <!-- Vendor js -->
    <script src="/dist/js/site/vendor.min.js?v=<?= $version ?>"></script>

    <script type="text/javascript" src="/dist/libs/jquery-toast-plugin/jquery.toast.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/js/driver/all.init.js?v=<?= $version ?>"></script>
    <?php Builder::loadScripts($footerScripts); ?>
    <script src="/dist/js/site/app.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript">
        if ($('#modal-alert-auth').length > 0) {
            const modalAuth = new bootstrap.Modal($('#modal-alert-auth'));
            modalAuth.show();
        }
    </script>
    </body>
    </html>
    <?php
}


function defaultMenu()
{
    global $lang;
    ?>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/">
            <img src="/dist/images/icons/truck.svg" alt="transportation"
                 style="background: linear-gradient(180deg, var(--primary), #07224c)">
            <?= $lang['menu_transportation'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/truck-container.svg" alt="buy-car"
                 style="background: linear-gradient(180deg, #ff8f8f, #ec3939)">
            <?= $lang['menu_buy_car'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/truck-container.svg" alt=""
                 style="background: linear-gradient(180deg, #ff8f8f, #ec3939)">
            <?= $lang['menu_customs_services'] ?>

        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/badge-check.svg" alt="insurance"
                 style="background: linear-gradient(180deg, #dd96ff, #b821ff)">
            <?= $lang['menu_insurance'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/tire.svg" alt="spare-parts"
                 style="background: linear-gradient(180deg, #00ff92, #04bc68)">
            <?= $lang['menu_spare_parts'] ?>
        </a>
    </li>

    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/exchange.svg" alt="exchange"
                 style="background: linear-gradient(180deg, #ffae9c, #ff2e00)">
            <?= $lang['menu_exchange'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/bank.svg" alt="bank"
                 style="background: linear-gradient(180deg, #a175ff, #5200ff)">
            <?= $lang['menu_bank'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/ranande.svg" alt="driver-services"
                 style="background: linear-gradient(180deg, #ea0881, #4c072c)">
            <?= $lang['menu_businessman_services'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/ranande.svg" alt="driver-services"
                 style="background: linear-gradient(180deg, #ea0881, #4c072c)">
            <?= $lang['menu_driver_services'] ?>
        </a>
    </li>
    <li class="mj-menu-item">
        <a class="mj-menu-link" href="/soon">
            <img src="/dist/images/icons/road.svg" alt="read-services"
                 style="background: linear-gradient(180deg, #92e4ff, #0089b5)">
            <?= $lang['menu_road_services'] ?>
        </a>
    </li>
    <?php
}

function getLoginHeader($title = '', $version = '1.2.0')
{
    global $stylesheets, $headerScripts, $Settings;
    $version = $Settings['site_version'];
    if (!isset($stylesheets)) {
        $stylesheets = [];
    }

    if (!isset($headerScripts)) {
        $headerScripts = [];
    }

    ?>
    <!doctype html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title><?= $title . " | " . $Settings['site_name'] ?></title>
        <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= SITE_URL ?>/uploads/site/144.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/uploads/site/180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="<?= SITE_URL ?>/uploads/site/192.png">
        <link rel="apple-touch-icon" sizes="384x384" href="<?= SITE_URL ?>/uploads/site/384.png">
        <link rel="apple-touch-icon" sizes="512x512" href="<?= SITE_URL ?>/uploads/site/512.png">
        <link rel="manifest" href="/manifest.webmanifest">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="theme-color" content="var(--primary)">
        <link rel="stylesheet" type="text/css" href="/dist/libs/fontawesome/all.css?v=<?= $version ?>"/>
        <link rel="stylesheet" type="text/css" href="/dist/libs/bootstrap/css/bootstrap.rtl.min.css?v=<?= $version ?>"/>

        <link rel="stylesheet" type="text/css"
              href="/dist/libs/jquery-toast-plugin/jquery.toast.min.css?v=<?= $version ?>"/>
        <?php
        Builder::loadStylesheet($stylesheets);
        ?>
        <link rel="stylesheet" type="text/css" href="/dist/css/all.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/css/driver/drivers.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/libs/fontawesome/all.css?v=<?= $version ?>"/>

        <?php
        Builder::loadScripts($headerScripts);
        $init_lang = 'fa';
        if (isset($_COOKIE['language'])) {
            $init_lang = substr($_COOKIE['language'], 0, 2);
        }
        ?>
        <!-- Google Tag Manager -->

        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-KJMVSFM');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body class="mj-bg-primary" dir="rtl" lang="<?=$init_lang?>">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KJMVSFM"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
    }
    function getLoginFooter($version = '1.2.0')
    {
    global $footerScripts, $Settings;
    $version = $Settings['site_version'];
    if (!isset($footerScripts)) {
        $footerScripts = [];
    }

    ?>

    <script type="text/javascript" src="/dist/libs/jquery/jquery-3.6.4.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/libs/bootstrap/js/bootstrap.bundle.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/libs/jquery-toast-plugin/jquery.toast.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/js/driver/all.init.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/libs/fontawesome/all.min.js?v=<?= $version ?>"></script>

    <?php Builder::loadScripts($footerScripts); ?>

    </body>
    </html>
    <?php
}


function getFooterHome($version = '1.2.0')
{
    global $footerScripts, $lang, $Settings;
    $version = $Settings['site_version'];
    if (!isset($footerScripts)) {
        $footerScripts = [];
    }
    ?>
    <!-- Vendor js -->
    <script src="/dist/js/site/vendor.min.js"></script>
    <script type="text/javascript" src="/dist/libs/jquery-toast-plugin/jquery.toast.min.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/js/driver/all.init.js?v=<?= $version ?>"></script>
    <?php Builder::loadScripts($footerScripts); ?>
    <script src="/dist/js/site/app.min.js?v=<?= $version ?>"></script>

    </body>
    </html>
    <?php
}
