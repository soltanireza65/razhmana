<?php


use MJ\HTML\Builder;
use MJ\Router\Router;
use MJ\Utils\Utils;

include_once 'classes/Menu.php';

$stylesheets = [];
$headerScripts = [];
$footerScripts = [];

function enqueueStylesheet($name, $src, $rel = 'stylesheet', $type = 'text/css', $version = '1.2.0')
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

function enqueueScript($name, $src, $type = 'text/javascript', $version = '1.2.0', $inFooter = true)
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
        <title><?= $title . " | " . $Settings['site_name']; ?></title>

        <link rel="stylesheet" type="text/css" href="/dist/libs/bootstrap/css/bootstrap.rtl.min.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css"
              href="/dist/libs/jquery-toast-plugin/jquery.toast.min.css?v=<?= $version ?>"/>
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

        <?php
        Builder::loadStylesheet($stylesheets);
        ?>
        <link rel="stylesheet" type="text/css" href="/dist/css/all.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/libs/fontawesome/all.css?v=<?= $version ?>">
        <?php
        Builder::loadScripts($headerScripts);
        $init_lang = 'fa';
        if (isset($_COOKIE['language'])) {
            $init_lang = substr($_COOKIE['language'], 0, 2);
        }
        ?>
    </head>
    <body class="mj-bg-primary" dir="rtl" lang="<?=$init_lang ?>">
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
    <script type="text/javascript" src="/dist/js/businessman/all.init.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/libs/fontawesome/all.min.js?v=<?= $version ?>"></script>
<?php Builder::loadScripts($footerScripts); ?>

    </body>
    </html>
    <?php
}

function getHeader($title = '', $displayHeader = true, $version = '1.2.1')
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
//    $user = User::getUserInfo();


//    $support_count = User::getSupportCount(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
//    if ($support_count->status == 200) {
//        $support_count = $support_count->response[0]->support_badge;
//    } else {
//        $support_count = 0;
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


        <link rel="stylesheet" type="text/css"
              href="/dist/libs/jquery-toast-plugin/jquery.toast.min.css?v=<?= $version ?>"/>
        <?php
        Builder::loadStylesheet($stylesheets);
        ?>
        <link href="/dist/css/driver/app-rtl.min.css" rel="stylesheet" type="text/css"
              id="app-style"/>
        <link rel="stylesheet" type="text/css" href="/dist/css/all.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/css/businessman/businessman.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/css/businessman/cargodetail.css?v=<?= $version ?>">
        <?php
        if (isset($_COOKIE['language']) && substr($_COOKIE['language'], 0, 2) != 'fa') {
            ?>
            <link rel="stylesheet" type="text/css" href="/dist/css/driver/driver-ltr.css?v=<?= $version ?>">
            <link rel="stylesheet" type="text/css" href="/dist/css/all-ltr.css?v=<?= $version ?>">
            <link href="/dist/css/businessman/app-ltr.min.css?v=<?= $version ?>" rel="stylesheet" type="text/css"
                  id="app-style"/>
            <link rel="stylesheet" type="text/css" href="/dist/css/businessman/businessman-ltr.css?v=<?= $version ?>">
            <link rel="stylesheet" type="text/css" href="/dist/css/businessman/cargodetail-ltr.css?v=<?= $version ?>">
            <?php
        }
        ?>


        <link rel="stylesheet" type="text/css" href="/dist/css/driver/drivers.css?v=<?= $version ?>">
        <link rel="stylesheet" type="text/css" href="/dist/libs/fontawesome/all.css?v=<?= $version ?>">

        <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= SITE_URL ?>/uploads/site/144.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/uploads/site/180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="<?= SITE_URL ?>/uploads/site/192.png">
        <link rel="apple-touch-icon" sizes="384x384" href="<?= SITE_URL ?>/uploads/site/384.png">
        <link rel="apple-touch-icon" sizes="512x512" href="<?= SITE_URL ?>/uploads/site/512.png">
        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <link href="<?= SITE_URL; ?>/dist/css/driver/icons.min.css?v=<?= $version ?>" rel="stylesheet" type="text/css"/>
        <script src="<?= SITE_URL ?>/dist/js/all.init.js"></script>
        <?php
        Builder::loadScripts($headerScripts);
        $init_lang = 'fa';
        if (isset($_COOKIE['language'])) {
            $init_lang = substr($_COOKIE['language'], 0, 2);
        }
        ?>
    </head>
    <body class="mj-bg-background" dir="rtl" lang="<?=$init_lang?>">
    <script type="text/javascript">
        let lang_vars = <?= json_encode($lang) ?>;
    </script>

    <?php
    Menu::getHeaderMenu();
    ?>


    <?php
}

function getFooter($version = '1.2.0', $display_footer = true,$showBack=true)
{
    global $footerScripts, $lang, $Settings;
    $version = $Settings['site_version'];
    if (!isset($footerScripts)) {
        $footerScripts = [];
    }
    $hideNav = (str_contains(Router::getCurrentUri(), '/businessman/ticket/')) ? true : false;
if ($hideNav) {
} else {
if ($display_footer){
    Menu::getFooterMenu();
}
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
    <script type="text/javascript" src="/dist/js/businessman/all.init.js?v=<?= $version ?>"></script>
    <script type="text/javascript" src="/dist/libs/fontawesome/all.min.js?v=<?= $version ?>"></script>

    <script src="/dist/js/site/app.min.js?v=<?= $version ?>"></script>

    <script type="text/javascript">
        if ($('#modal-alert-auth').length > 0) {
            const modalAuth = new bootstrap.Modal($('#modal-alert-auth'));
            modalAuth.show();
        }
    </script>

<?php Builder::loadScripts($footerScripts); ?>
    </body>
    </html>
    <?php
}
