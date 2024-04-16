<?php
global $lang;

include_once 'header-footer.php';

enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');

getHeader($lang['coming_soon']);

?>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="row mt-5">

                <div class="col-12 mt-4">
                    <lottie-player src="/dist/lottie/bank.json" style="height: 80%" background="" speed="1"
                                   loop autoplay></lottie-player>
                    <p class="text-center text-info font-17"><?= $lang['soon_exchange']; ?></p>
                    <p class="text-center text-info font-12"><?= $lang['soon_contact_me']; ?></p>
                    <div class="mj-home-gt-blog-btn d-flex justify-content-center">
                        <a href="/user/contact-us"><?= $lang['return_to_main_page']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
getFooter('', false);