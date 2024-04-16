<?php
global $Settings, $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'views/site/header-footer.php';

enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('all-css', '/dist/css/all.css');
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('academy-init', '/dist/js/site/academy/academy.js');
enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
getHeader($lang['academy']);

$newsets = Academy::getNewsetsAcademy();
if ($newsets->status == 200) {
    $newsets = $newsets->response;
} else {
    $newsets = [];
}


$categories = Academy::getCategoryList();
if ($categories->status == 200) {
    $categories = $categories->response;
} else {
    $categories = [];
}
//$first_category_item = Academy::getAcademyByCategory(3);
//if ($first_category_item->status == 200) {
//    $first_category_item = $first_category_item->response;
//} else {
//    $first_category_item = [];
//}
?>

    <main class="container" style="padding-bottom: 60px !important;">
        <!--        start head-->
        <div class="mj-academy-head">
            <div class="mj-aca-page-title"><?= $lang['u_academy'] ?></div>
            <div class="mj-aca-img">
                <img src="/dist/images/academy/book.png" alt="">
            </div>
            <div class="mj-aca-head-search">
                <input type="text" placeholder="<?= $lang['u_academy_search_place_holder'] ?>" id="mj-m-academy-search">
                <button class="fa-search mj-search-icon"></button>
            </div>
        </div>
        <!--        end head-->
        <section id="mj-m-search-container-empty">
            <!--        start slider newest-->

            <!--       blog button start-->

            <div class="mj-blog-btn-academy">
                <a href="/blog">
                    <div class="mj-blog-btn-link">
                        <span><?=$lang['enter_to_blog']?></span>
                        <img src="/dist/images/blog.png" alt="blog">
                    </div>
                </a>
            </div>

            <!--       blog button end-->

            <!-- Swiper -->
            <div class="mj-newest-post-slider">
                <div class="mj-slider-heading"><?= $lang['u_academy_newsets'] ?></div>
                <div class="mj-slider-heading-border "></div>
                <div class="swiper mySwiper1">
                    <div class="swiper-wrapper">
                        <?php foreach ($newsets as $item) { ?>
                            <div class="swiper-slide">
                                <div class="mj-newest-post">
                                    <div class="mj-newest-post-cat"><?= $item->category_name ?></div>
                                    <a href="/academy/<?= $item->academy_slug ?>">
                                        <img src="<?= Utils::fileExist($item->academy_thumbnail, BOX_EMPTY) ?>" alt="">
                                        <div class="mj-newest-post-title">
                                            <div>
                                                <?= $item->academy_title ?>
                                            </div>

                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!--        end slider newest-->
            <!--        start category-->
            <div class="mj-category-items my-3">
                <?php foreach ($categories as $item) {
                    ?>
                    <div class="mj-category-item" data-cat-id="<?= $item->category_id ?>">
                        <a href="/academycat/<?= $item->category_id ?>">
                            <div class="mj-cat-item-card">
                                <img src="<?= Utils::fileExist($item->category_thumbnail, BOX_EMPTY) ?>" alt="">
                            </div>
                        </a>
                        <span><?= $item->category_name ?></span>
                    </div>
                    <?php
                } ?>
            </div>
            <!--        end category-->

        </section>
        <section id="mj-m-search-container" class="mt-4" data-item-steps="10" data-item-resume="10">

        </section>
        <section id="loading" class=" justify-content-center align-items-center" style="display: none;">
            <lottie-player src="/dist/lottie/wallet-load.json" style="width:25%;" background="" speed="1"
                           loop autoplay></lottie-player>
        </section>
        <input type="hidden" id="token-search-academy" name="token-search-academy"
               value="<?= Security::initCSRF2() ?>">
    </main>
<?php
getFooter();