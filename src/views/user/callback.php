<?php

global $lang, $Settings;

use MJ\Utils\Utils;

include_once 'header-footer.php';

enqueueScript('menu-init-js', '/dist/libs/lottie/lottie-player.js');

getHeader($lang['d_faq_title']);

?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt", "seo_user_faq")) ?>

    </script>
    <main class="container mj-callback-body" style="padding-bottom: 180px;">
        <div class="mj-callback-message-card">
            <div class="mj-callback-animation">
                <lottie-player src="/dist/lottie/done.json" background="transparent" speed="1"
                               style="width: 200px; height: 200px;" loop autoplay></lottie-player>
            </div>
            <div class="mj-callback-texts">
                <div>هوووووراااااا</div>
                <p>آگهی شما با موفقیت به حالت فوری ارتقا یافت</p>
                <div>در حال هدایت شما به صفحه آگهی</div>
            </div>
        </div>
    </main>
<?php

getFooter('', false);