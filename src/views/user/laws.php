<?php

global $lang, $Settings;

include_once 'header-footer.php';

use MJ\Utils\Utils;

getHeader($lang['d_laws_title']);

$laws = $Settings['u_laws'];
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt" , "seo_user_laws"))?>
    </script>
    <main class="container" style="padding-bottom: 180px;">
        <div class="col-12 mj-card">
            <div class="card-body">
                <div class="mj-about-header d-flex align-items-center">
                    <img src="/dist/images/icons/laws.svg" class="mj-profile-items-icon me-2" alt="laws">
                    <span><?= $lang['d_laws_title'] ?></span>
                </div>
                <div class="mj-about-body mt-4">
                    <p>
                        <?= $laws['desc'] ?>

                    </p>
                    <?php
                    foreach ($laws['items'] as $law) {
                        ?>
                        <h3 class="my-2"><?= $law['title'] ?></h3>
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