<?php
global $Settings, $lang, $Tour;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'views/site/header-footer.php';

enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('all-css', '/dist/css/all.css');
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');
enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('swiper-bundle-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('lottie-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('swiper-js', '/dist/js/site/academy/academy-cat.js');
getHeader($lang['academy']);
$ca_id = $_REQUEST['id'];
$category_detail = Academy::getCategoryById($ca_id);
if ($category_detail->status == 200) {
    $category_detail = $category_detail->response[0];
} else {
    $category_detail = [];
}
$child_categories = Academy::getCategoryList($ca_id);
if ($child_categories->status == 200) {
    $child_categories = $child_categories->response;
} else {
    $child_categories = [];
}
$newsest_item = Academy::getAcademyByCategory($ca_id, 'published', 3);
if ($newsest_item->status == 200) {
    $newsest_item = $newsest_item->response;
} else {
    $newsest_item = [];
}
$breadcrump = '';
$parent_category = [];
if (is_null($category_detail->parent_id) && empty($category_detail->parent_id)) {
    $breadcrump = $category_detail->category_name;
} else {
    $parent_category = Academy::getCategoryById($category_detail->parent_id);

    if ($parent_category->status == 200) {
        $parent_category = $parent_category->response[0];

    } else {
        $parent_category = [];
    }
    $breadcrump = $parent_category->category_name . ' / ' . $category_detail->category_name;
}
?>
    <style>
        .mj-academy-footer-home-btn {
            position: fixed;
            z-index: 9999;
            bottom: 30px;
            right: 85px;
            border-radius: 10px;
            height: 40px !important;
            background: var(--primary);
            box-shadow: 0 3px 10px rgb(0 130 231 / 64%);
            color: #fff;
            display: flex;
            align-items: center;
            font-size: 12px;
            padding: 0 5px;
        }
        .mj-academy-footer-home-btn:hover {
            position: fixed;
            z-index: 9999;
            bottom: 30px;
            right: 85px;
            border-radius: 10px;
            height: 40px !important;
            background: var(--primary);
            box-shadow: 0 3px 10px rgb(0 130 231 / 64%);
            color: #fff;
            display: flex;
            align-items: center;
            font-size: 12px;
            padding: 0 5px;
        }
    </style>

    <a href="/academy" class="mj-academy-footer-home-btn">
        <div class="fa-home me-1"></div>
        <span><?=$lang['u_back_to_academy'];?></span>
    </a>
    <main class="container" style="padding-bottom: 60px !important;">
        <!--        start head-->
        <div class="mj-academy-head">
            <div class="mj-aca-page-title"><?= $breadcrump ?></div>
            <div class="mj-aca-img">
                <img src="/dist/images/academy/book.png" alt="">
            </div>
            <div class="mj-aca-head-search">
                <input type="text" placeholder="<?= $lang['u_academy_search_place_holder'] ?> ..."
                       id="mj-m-academy-search">
                <button class="fa-search mj-search-icon"></button>
            </div>
        </div>
        <!--        end head-->
        <!-- start not_search-->
        <section id="not-search">
            <!--        start category-->
            <?php if (count($child_categories) > 0) { ?>
                <div class="mj-subcat-heading"><?= $lang['u_academy_sub_cat'] . $category_detail->category_name ?></div>
                <div class="mj-slider-heading-border "></div>
                <div class="mj-category-items mb-3 mt-2">

                    <?php foreach ($child_categories as $item) {
                        ?>
                        <div class="mj-category-item">
                            <a href="/academycat/<?= $item->category_id ?>">
                                <div class="mj-subcat-item-card">
                                    <img src="<?= Utils::fileExist($item->category_thumbnail, BOX_EMPTY); ?>" alt="">
                                </div>
                            </a>
                            <span><?= $item->category_name ?></span>
                        </div>
                        <?php
                    } ?>
                </div>
            <?php } ?>
            <!--        end category-->
            <!--        start category newest-->
            <div class="mj-slider-heading <?= (count($child_categories) > 0) ? '' : 'mt-5' ?>"><?= $lang['u_academy_newsets'] ?></div>
            <div class="mj-slider-heading-border "></div>
            <div class="mj-academy-cat-newest-grid mt-2">
                <?php if (isset($newsest_item[0])) { ?>
                    <div id="first-new-cat-post" class="mj-cat-newest-post">
                        <div class="mj-cat-newest-post-cat"><?= $newsest_item[0]->category_name ?></div>
                        <a href="/academy/<?= $newsest_item[0]->academy_slug ?>">
                            <img src="<?= $newsest_item[0]->academy_thumbnail ?>" alt="">
                            <div class="mj-cat-newest-post-title">
                                <div>
                                    <?= $newsest_item[0]->academy_title ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <?php if (isset($newsest_item[1])) { ?>
                    <div id="second-new-cat-post" class="mj-cat-newest-post">
                        <div class="mj-cat-newest-post-cat"><?= $newsest_item[1]->category_name ?></div>
                        <a href="/academy/<?= $newsest_item[1]->academy_slug ?>">
                            <img src="<?= $newsest_item[1]->academy_thumbnail ?>" alt="">
                            <div class="mj-cat-newest-post-title">
                                <div>
                                    <?= $newsest_item[1]->academy_title ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <?php if (isset($newsest_item[2])) { ?>
                    <div id="third-new-cat-post" class="mj-cat-newest-post">
                        <div class="mj-cat-newest-post-cat"><?= $newsest_item[2]->category_name ?></div>
                        <a href="/academy/<?= $newsest_item[2]->academy_slug ?>">
                            <img src="<?= $newsest_item[2]->academy_thumbnail ?>" alt="">
                            <div class="mj-cat-newest-post-title">
                                <div>
                                    <?= $newsest_item[2]->academy_title ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <!--        end category newest-->
            <?php if (is_null($category_detail->parent_id)) {
                ?>
                <div class="mj-cat-tabs">
                    <ul class="nav nav-pills mb-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                    id="pills-home-tab-all"
                                    data-bs-toggle="pill"
                                    data-bs-target="#pills-home-all" type="button" role="tab"
                                    aria-controls="pills-home-all"
                                    aria-selected="true"><?= $lang['u_academy_all'] ?>
                            </button>
                        </li>
                        <?php foreach ($child_categories as $index => $item) {
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link "
                                        id="pills-home-tab-<?= $item->category_id ?>"
                                        data-bs-toggle="pill"
                                        data-bs-target="#pills-home-<?= $item->category_id ?>" type="button" role="tab"
                                        aria-controls="pills-home-<?= $item->category_id ?>"
                                        aria-selected="true"><?= $item->category_name ?>
                                </button>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="mj-slider-heading-border "></div>
                <div class="mj-tab-content">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active"
                             id="pills-home-all" role="tabpanel"
                             aria-labelledby="pills-home-tab-all"
                             tabindex="0">
                            <div class="mj-cat-tab-posts">
                                <?php
                                $posts = Academy::getAcademyByCategoryForAllTabs($ca_id);
                                if ($posts->status == 200) {
                                    $posts = $posts->response;
                                } else {
                                    $posts = [];
                                }
                                foreach ($posts as $item) {
                                    ?>
                                    <a href="/academy/<?= $item->academy_slug ?>">
                                        <div class="mj-cat-tab-post">
                                            <img src="<?= $item->academy_thumbnail ?>" alt="4">
                                            <div class="mj-cat-tab-post-content">
                                                <div class="mj-post-category"><?= $item->category_name ?></div>
                                                <div class="mj-post-title">
                                                    <?= $item->academy_title ?>
                                                </div>
                                                <div class="mj-post-date">
                                                    <?= Utils::getTimeByLang($item->academy_submit_time) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php } ?>

                            </div>
                        </div>
                        <?php foreach ($child_categories as $index => $item) { ?>
                            <div class="tab-pane fade "
                                 id="pills-home-<?= $item->category_id ?>" role="tabpanel"
                                 aria-labelledby="pills-home-tab-<?= $item->category_id ?>"
                                 tabindex="0">
                                <div class="mj-cat-tab-posts">
                                    <?php
                                    $posts = Academy::getAcademyByCategory($item->category_id);
                                    if ($posts->status == 200) {
                                        $posts = $posts->response;
                                    } else {
                                        $posts = [];
                                    }
                                    foreach ($posts as $item) {
                                        ?>
                                        <a href="/academy/<?= $item->academy_slug ?>">
                                            <div class="mj-cat-tab-post">
                                                <img src="<?= $item->academy_thumbnail ?>" alt="4">
                                                <div class="mj-cat-tab-post-content">
                                                    <div class="mj-post-category"><?= $item->category_name ?></div>
                                                    <div class="mj-post-title">
                                                        <?= $item->academy_title ?>
                                                    </div>
                                                    <div class="mj-post-date">
                                                        <?= Utils::getTimeByLang($item->academy_submit_time) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!--        end category tabs-->
            <?php } else { ?>
                <!--        start category tabs-->
                <!--        start category tabs-->
                <div class="mj-slider-heading-border "></div>
                <div class="mj-cat-tab-posts">
                    <?php
                    $posts = Academy::getAcademyByCategory($ca_id);
                    if ($posts->status == 200) {
                        $posts = $posts->response;
                    } else {
                        $posts = [];
                    }
                    foreach ($posts as $item) {
                        ?>
                        <a href="/academy/<?= $item->academy_slug ?>">
                            <div class="mj-cat-tab-post">
                                <img src="<?= $item->academy_thumbnail ?>" alt="4">
                                <div class="mj-cat-tab-post-content">
                                    <div class="mj-post-category"><?= $item->category_name ?></div>
                                    <div class="mj-post-title">
                                        <?= $item->academy_title ?>
                                    </div>
                                    <div class="mj-post-date">
                                        <?= Utils::getTimeByLang($item->academy_submit_time) ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } ?>

                </div>
                <!--        end category tabs-->
            <?php } ?>

        </section>
        <!-- end not_search-->
        <!-- start search-->
        <section id="search-container" class="mt-4" data-item-steps="10" data-item-resume="10">
        </section>
        <!-- end search-->
        <input type="hidden" id="token-search-academy" name="token-search-academy"
               value="<?= Security::initCSRF2() ?>">
    </main>
    <script>
        let category_id = '<?=$ca_id?>'
    </script>
<?php
getFooter();