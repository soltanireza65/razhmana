<?php

global $lang, $Settings;

use MJ\Utils\Utils;

include_once 'header-footer.php';

getHeader($lang['u_contact_us_2']);

$about = $Settings['u_about_us'];
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt" , "seo_contact_us"))?>
    </script>
    <main class="container" style="padding-bottom: 180px;">
        <div class="col-12">
                <div class="mj-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2" alt="support">
                            <div>
                                <span class="mj-d-icon-title"><?= $lang['d_cargo_support'] ?></span>
                                <p class="mj-d-cargo-item-desc mb-0">
                                    <?= $lang['d_cargo_support_sub_title'] ?>
                                </p>
                            </div>
                        </div>

                        <div class="mj-support-links-cargo">

                                    <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>"
                                       class="mj-btn mj-d-btn-call me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                        <?= $lang['d_cargo_call'] ?>
                                    </a>

                                    <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>"
                                       class="mj-btn mj-d-btn-whatsapp me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                        <?= $lang['d_cargo_whatsapp'] ?>
                                    </a>
                        </div>
                    </div>
                </div>
        </div>

        <div class="col-12">
            <div class="mj-card">
                <div class="card-body">
                    <h4 class="mj-fw-600 mj-font-14 mt-0 mb-4"><?= $Settings['contact_us'] ?></h4>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <h6 class="mj-fw-600 mj-font-13 mt-0"><?= $Settings['tehran_office'] ?></h6>

                                <div class="mb-2">
                                    <img src="/dist/images/icons/location-dot.svg" class="mj-header-tabs-icon"
                                         alt="tehran-address-location">
                                    <span class="mj-fw-500 mj-font-12"><?= $Settings['tehran_office_address'] ?></span>
                                </div>

                                <div>
                                    <img src="/dist/images/icons/headset.svg" class="mj-header-tabs-icon"
                                         alt="tehran-address-location">
                                    <span class="mj-fw-500 mj-font-12"><?= $Settings['tehran_office_tel'] ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <h6 class="mj-fw-600 mj-font-13 mt-0"><?= $Settings['tabriz_office'] ?></h6>

                                <div class="mb-2">
                                    <img src="/dist/images/icons/location-dot.svg" class="mj-header-tabs-icon"
                                         alt="tehran-address-location">
                                    <span class="mj-fw-500 mj-font-12"><?= $Settings['tabriz_office_address'] ?></span>
                                </div>

                                <div>
                                    <img src="/dist/images/icons/headset.svg" class="mj-header-tabs-icon"
                                         alt="tehran-address-location">
                                    <span class="mj-fw-500 mj-font-12"><?= $Settings['tabriz_office_tel'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php

getFooter('', false);