<?php

global $lang, $Settings;
include_once 'header-footer.php';

enqueueScript('menu-init-js', '/dist/js/driver/dashboard.init.js');

getHeader($lang['d_about_title']);

$about = $Settings['d_about_us'];
?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="col-12 mt-3 mj-card">
            <div class="card-body">
                <div class="mj-about-header d-flex align-items-center">
                    <img src="/dist/images/icons/about-us.svg" class="mj-profile-items-icon me-2" alt="about">
                    <span><?= $lang['d_about_title'] ?></span>
                </div>
                <div class="mj-about-body mt-4">
                    <p>
                        <?= $about['desc1'] ?>
                    </p>
                    <img src="<?= $about['image'] ?>" alt="">
                    <p>
                        <?= $about['desc2'] ?>
                    </p>
                </div>
            </div>
        </div>
    </main>
<?php

getFooter('', false);