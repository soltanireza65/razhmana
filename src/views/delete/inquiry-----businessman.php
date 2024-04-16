<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
    enqueueStylesheet('persian-datepicker-css', '/dist/libs/persian-calendar/persian-datepicker.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('menu-init-js', '/dist/js/businessman/dashboard.init.js');

    getHeader($lang['b_inquiry']);

    ?>

    <main class="container" style="padding-bottom: 180px">
        <div class="row">
            <div class="col-sm-6 col-lg-6">
                <a href="/inquiry-ground">
                    <div class="mj-b-estelam-item d-flex align-items-center">
                        <div class="mj-b-estelam-name"><?=$lang['b_inquiry_ground']?></div>
                        <img src="/dist/images/Frame.png" alt="">
                    </div>
                </a>
            </div>
            <div class="col-sm-6  col-lg-6">
                <a href="/inquiry-air">
                    <div class="mj-b-estelam-item d-flex align-items-center">
                        <div class="mj-b-estelam-name"><?=$lang['b_inquiry_air']?></div>
                        <img src="/dist/images/plane.png" alt="">
                    </div>
                </a>
            </div>
            <div class="col-sm-6 ">
                <a href="/inquiry-rail">
                    <div class="mj-b-estelam-item d-flex align-items-center">
                        <div class="mj-b-estelam-name"><?=$lang['b_inquiry_railroad']?></div>
                        <img src="/dist/images/gatar.png" alt="">
                    </div>
                </a>
            </div>
            <div class="col-sm-6 ">
                <a href="/inquiry-ship">
                    <div class="mj-b-estelam-item d-flex align-items-center">
                        <div class="mj-b-estelam-name"><?=$lang['b_inquiry_ship']?></div>
                        <img src="/dist/images/ship.png" alt="">
                    </div>
                </a>
            </div>
          <!--  <div class="col-sm-6 ">
                <a href="/businessman/inquiry-inventory">
                    <div class="mj-b-estelam-item d-flex align-items-center">
                        <div class="mj-b-estelam-name"><?/*=$lang['b_inquiry_inventory']*/?></div>
                        <img src="/dist/images/storage.png" alt="">
                    </div>
                </a>
            </div>-->
        </div>





    </main>

    <?php
    getFooter('', false);

} else {
    header('location: /login');
}