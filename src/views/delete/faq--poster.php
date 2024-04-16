<?php

global $lang, $Settings;

include_once getcwd() . '/views/user/header-footer.php';

getHeader($lang['d_faq_title']);

$faqs = $Settings['d_faq'];
?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="col-12 mt-3 mj-card">
            <div class="card-body">
                <?php
                $counter = 0;
                foreach ($faqs as $faq) {
                    $counter++;
                    ?>
                    <div class="accordion accordion-flush" id="accordionFlushExample<?= $counter ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-heading<?= $counter ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse<?= $counter ?>" aria-expanded="false"
                                        aria-controls="flush-collapse<?= $counter ?>">
                                    <img class="me-2" src="/dist/images/icons/faq.svg" alt="">
                                    <?= $faq['title'] ?>
                                </button>
                            </h2>
                            <div id="flush-collapse<?= $counter ?>" class="accordion-collapse collapse"
                                 aria-labelledby="flush-heading<?= $counter ?>"
                                 data-bs-parent="#accordionFlushExample<?= $counter ?>">
                                <div class="accordion-body">   <?= $faq['desc'] ?></div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </main>
<?php
getFooter('', false);