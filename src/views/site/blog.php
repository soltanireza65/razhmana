<?php

use MJ\Utils\Utils;

global $lang, $Settings;

/**
 * Get Post By Limit
 */
if (isset($_COOKIE['language'])) {
    $lag = $_COOKIE['language'];
} else {
    $lag = 'fa_IR';
}
$resultPostByLimit = Post::getPostByLimit(0, 6, $lag);
$dataPostByLimit = [];
if ($resultPostByLimit->status == 200 && !empty($resultPostByLimit->response)) {
    $dataPostByLimit = $resultPostByLimit->response;
}

include_once 'header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
enqueueScript('blog-slider', '/dist/js/site/blog-slider.js');

getHeader($lang['blog']);
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt", "seo_blog")) ?>
    </script>
    <main class="container" style="padding-bottom: 70px">
        <div class=" row mt-2">
            <div class="col-12">
                <div class="mj-home-image">
                    <img class="w-100" src="<?= Utils::fileExist($Settings['site_blog_banner'], BOX_EMPTY); ?>"
                         alt="<?= $Settings['site_name']; ?>">
                </div>
            </div>
        </div>

        <section class=" container-fluid mj-blog-slider-section mt-3 mb-2">
            <div class="row mj-blog-slider-row">
                <div class="mj-blog-newest-header mb-3">
                    <img src="/dist/images/icons/sun-haze.svg" alt="">
                    <span><?= $lang['newest']; ?></span>
                </div>

                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <?php
                        if (!empty($dataPostByLimit)) {
                            foreach ($dataPostByLimit as $dataPostByLimitITEM) {
                                ?>
                                <div class="swiper-slide">
                                    <div class="mj-blog-news-card">
                                        <a href="/blog/<?= $dataPostByLimitITEM->post_slug; ?>">
                                            <div class="mj-blog-news-content">
                                                <div class="mj-news-image">
                                                    <img src="<?= Utils::fileExist($dataPostByLimitITEM->post_thumbnail, BOX_EMPTY); ?>"
                                                         alt="<?= strip_tags(mb_strimwidth($dataPostByLimitITEM->post_title, 0, 18, '...')); ?>">
                                                </div>
                                                <div class="mj-blog-news-category">
                                                    <?= $dataPostByLimitITEM->category_name; ?>
                                                </div>
                                                <div class="mj-blog-news-title text-truncate">
                                                    <?= (empty($dataPostByLimitITEM->post_excerpt)) ? strip_tags(mb_strimwidth($dataPostByLimitITEM->post_description, 0, 600, '...')) : $dataPostByLimitITEM->post_excerpt; ?>
                                                </div>
                                                <div class="mj-blog-news-btn">
                                                    <?= $lang['read_more']; ?>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
              <!--  <div class="mySwiper">
                    <div class="swiper-button-next slider-next-btn"></div>
                    <div class="swiper-button-prev slider-perv-btn"></div>
                </div>-->
            </div>
        </section>

        <section>
            <div class="row mb-3 mt-3">
                <div class="mj-home-blog-title">
                    <img src="/dist/images/icons/blog.svg" alt="<?= $lang['blog']; ?>">
                    <span><?= $lang['blog']; ?></span>
                </div>
            </div>
            <div class="row" id="AddDiv">
                <?php $count = 0;
                if (!empty($dataPostByLimit)) {
                    $count = count($dataPostByLimit);

                    foreach ($dataPostByLimit as $index => $dataPostByLimitITEM) {
                        if ($index >= 5) {
                            break;
                        }
                        ?>
                        <div class="mj-home-blog-list mb-2">
                            <a href="/blog/<?= $dataPostByLimitITEM->post_slug; ?>">
                                <div class="mj-blog-list-item">
                                    <div class="mj-blog-item-card d-flex align-items-center">
                                        <div class="mj-blog-img">
                                            <img src="<?= Utils::fileExist($dataPostByLimitITEM->post_thumbnail, BOX_EMPTY); ?>"
                                                 alt="<?= strip_tags(mb_strimwidth($dataPostByLimitITEM->post_title, 0, 18, "...")); ?>">
                                            <div class="mj-blog-date">
                                                <?= Utils::getTimeCountry('Y / m / d', $dataPostByLimitITEM->post_submit_time); ?>
                                            </div>

                                        </div>
                                        <div class="mj-blog-card-title">
                                            <?= strip_tags(mb_strimwidth($dataPostByLimitITEM->post_title, 0, 45, '...')); ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php }
                } ?>
            </div>
            <div class="mj-home-gt-blog-btn d-flex justify-content-center <?= ($count <= 5) ? "d-none" : ""; ?>">
                <a href="javascript:void(0);"
                   data-mj-count="5"

                   id="load_more">
                    <?= $lang['load_more']; ?>
                </a>
            </div>
        </section>
    </main>
<?php
getFooter('', false);