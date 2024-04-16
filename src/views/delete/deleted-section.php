<?php
use MJ\Utils\Utils;
?>

<!-- حذف بخش های آکادمی . و  وبلاگ از صفحه خانه -->
     <div dir="rtl" class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php
                $newsets = Academy::getNewsetsAcademy();
                if ($newsets->status == 200) {
                    $newsets = $newsets->response;
                } else {
                    $newsets = [];
                }
                foreach ($newsets as $item) { ?>
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
        <!-- end academy -->
        <!-- start blog-->
        <section class="container-fluid mj-blog-slider-section mt-3 mb-3">
            <div class="row mj-blog-slider-row">
                <div class="mj-cargo-neweset-header mb-2">
                    <div class="d-flex align-items-center">
                        <div class="mj-white-card pe-1">
                            <img src="/dist/images/icons/sun-haze.svg" alt="">
                        </div>
                        <span><?= $lang['site_new_blogs'] ?></span>
                    </div>
                    <a href="/blog"><?= $lang['see_all'] ?></a>
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
                                                    <?= (empty($dataPostByLimitITEM->post_excerpt)) ? strip_tags(mb_strimwidth($dataPostByLimitITEM->post_description, 0, 150, '...')) : mb_strimwidth($dataPostByLimitITEM->post_excerpt, 0, 150, '...'); ?>
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
            </div>
        </section>
        <!-- end blog -->
