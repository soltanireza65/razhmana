<?php
global $Settings, $lang;
use MJ\Utils\Utils;

include_once 'header-footer.php';

enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');

getHeader($lang['page_404']);
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt" , "seo_404"))?>
    </script>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="row mt-4">
                <div class="col-12 mt-5">
                    <lottie-player src="/dist/lottie/404.json" style="height: 80%" background="" speed="1"
                                   loop autoplay></lottie-player>
                    <div class="mj-home-gt-blog-btn d-flex justify-content-center">
                        <a href="/"><?= $lang['return_to_main_page']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
getFooter('', false);