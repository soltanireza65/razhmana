<?php
global $lang;

include_once 'header-footer.php';
use MJ\Utils\Utils;
enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');

getHeader($lang['d_laws_title']);
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt" , "seo_developer"))?>
    </script>
    <main class="container" style="padding-bottom: 80px !important;">
        <div class="col-12 mj-card">
            <div class="mj-card h-auto">
                <div class="card-body">
                    <h4 class="header-title font-14 mb-3"><?=$lang['design']?></h4>

                    <div class="text-center">
                        <lottie-player src="/dist/lottie/developer.json" style="max-width: 280px;"
                                       class="mx-auto" speed="1"
                                       loop autoplay></lottie-player>

                        <a href="https://tjavan.com">
                            <h4 class="header-title text-secondary font-13">طراحان جوان</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php
getFooter('', false);