<?php
global $lang,$Settings;

use MJ\Utils\Utils;

?>
<!doctype html >
<html lang="fa" dir="<?=Utils::getThemeDirection() ;?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $lang['maintenance']." | ".$Settings['site_name']; ?></title>
    <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">
    <link href="<?= SITE_URL; ?>/dist/css/admin/app<?=(Utils::getThemeDirection()=='rtl')?"-rtl":null ;?>.min.css?v=1" rel="stylesheet" type="text/css" id="app-style"/>
    <!-- icons -->
    <link href="<?= SITE_URL; ?>/dist/css/admin/icons.min.css?v=1" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL; ?>/dist/css/admin/fontiran.css?v=1" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center" style="height: 70vh;">

                    <svg id="Layer_1" class="svg-computer" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 424.2 424.2">
                        <style>
                            .st0 {
                                fill: none;
                                stroke: var(--ct-text-color-h);
                                stroke-width: 5;
                                stroke-linecap: round;
                                stroke-linejoin: round;
                                stroke-miterlimit: 10;
                            }
                        </style>
                        <g id="Layer_2">
                            <path class="st0"
                                  d="M339.7 289h-323c-2.8 0-5-2.2-5-5V55.5c0-2.8 2.2-5 5-5h323c2.8 0 5 2.2 5 5V284c0 2.7-2.2 5-5 5z"/>
                            <path class="st0"
                                  d="M26.1 64.9h304.6v189.6H26.1zM137.9 288.5l-3.2 33.5h92.6l-4.4-33M56.1 332.6h244.5l24.3 41.1H34.5zM340.7 373.7s-.6-29.8 35.9-30.2c36.5-.4 35.9 30.2 35.9 30.2h-71.8z"/>
                            <path class="st0" d="M114.2 82.8v153.3h147V82.8zM261.2 91.1h-147"/>
                            <path class="st0"
                                  d="M124.5 105.7h61.8v38.7h-61.8zM196.6 170.2H249v51.7h-52.4zM196.6 105.7H249M196.6 118.6H249M196.6 131.5H249M196.6 144.4H249M124.5 157.3H249M124.5 170.2h62.2M124.5 183.2h62.2M124.5 196.1h62.2M124.5 209h62.2M124.5 221.9h62.2"/>
                        </g>
                    </svg>

                    <div class="position-relative">
                        <div class="position-absolute d-flex w-100" style="direction: ltr">
                            <lottie-player src="/dist/lottie/maintenance.json"
                                           style="height: 20%;z-index: -500;width: 35%;"
                                           background=""
                                           speed="1"
                                           loop
                                           autoplay></lottie-player>
                        </div>
                    </div>

                    <h3 class="mt-4"><?= $lang['a_maintenance_1']; ?></h3>
                    <p><?= $lang['a_maintenance_2']; ?></p>
                    <h1 id="expiredText"
                        class="mt-5 mb-5 text-pink"
                        style="direction: ltr;display: none"
                        data-tj-time="<?= (time() + 1200) * 1000; ?>">
                        <?= $lang['a_expired_m']; ?>
                    </h1>
                    <div class="row d-flex justify-content-center mt-3" id="countDownTimer">
                        <div class="col-3 col-md-2">
                            <div class="avatar-xl">
                                                    <span id="sID"
                                                          class="avatar-title bg-soft-primary text-primary font-24 rounded-circle">
                                                    </span>
                                <p class="mb-0 font-14 mt-1">
                                    <?= $lang['second']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="avatar-xl">
                                                    <span id="mID"
                                                          class="avatar-title bg-soft-primary text-primary font-24 rounded-circle">
                                                    </span>
                                <p class="mb-0 font-14 mt-1">
                                    <?= $lang['minute']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="avatar-xl">
                                                    <span id="hID"
                                                          class="avatar-title bg-soft-primary text-primary font-24 rounded-circle">
                                                    </span>
                                <p class="mb-0 font-14 mt-1">
                                    <?= $lang['hour']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-3 col-md-2">
                            <div class="avatar-xl">
                                                    <span id="dID"
                                                          class="avatar-title bg-soft-primary text-primary font-24 rounded-circle">
                                                    </span>
                                <p class="mb-0 font-14 mt-1">
                                    <?= $lang['day']; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                </div> <!-- end /.text-center-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->
<!-- Vendor js -->
<script src="<?= SITE_URL; ?>/dist/js/admin/vendor.min.js?v=1"></script>
<script src="<?= SITE_URL;?>/dist/libs/lottie/lottie-player.js"></script>
<script src="<?= SITE_URL; ?>/dist/js/admin/maintenance.init.js?v=1"></script>
<!-- App js -->
<script src="<?= SITE_URL; ?>/dist/js/admin/app.min.js?v=1"></script>
</body>
</html>