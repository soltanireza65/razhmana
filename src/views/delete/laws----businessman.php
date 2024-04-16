<?php

global $lang, $Settings;

include_once 'header-footer.php';

enqueueScript('menu-init-js', '/dist/js/businessman/dashboard.init.js');

getHeader($lang['b_title_laws']);

$laws = $Settings['b_laws'];
?>

    <main class="container" style="padding-bottom: 180px;">
        <div class="col-12 mt-3 mj-card">
            <div class="card-body">
                <div class="mj-about-header d-flex align-items-center">
                    <img src="/dist/images/icons/laws.svg" class="mj-profile-items-icon me-2" alt="laws">
                    <span><?= $lang['b_laws'] ?></span>
                </div>
                <div class="mj-about-body mt-4">
                    <p>
                        <?= $laws['desc'] ?>
                    </p>
                    <?php
                    foreach ($laws['items'] as $law) {
                        ?>
                        <h5 class="my-2"><?= $law['title'] ?></h5>
                        <p><?= $law['desc'] ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

<?php
getFooter('', false);
