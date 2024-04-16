<?php global $Settings, $lang, $Tour;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'views/site/header-footer.php';
if (!isset($_COOKIE['t-home']) || $_COOKIE['t-home'] != 'shown') {
    enqueueStylesheet('shepherd-css', '/dist/libs/shepherd/shepherd.css');
    enqueueScript('shepherd-js', '/dist/libs/shepherd/shepherd.js');
    ?>
    <?php
}

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

//print_r($dataAllCargo);
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt", "seo_home")) ?></script>
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
            <div class="mj-aca-page-title">
                <span style="font-size: 12px;">انتیــراپ</span>
                /
                <span >رانندگان</span>
                </div>
            <div class="mj-aca-img">
                <img src="/dist/images/academy/book.png" alt="">
            </div>
            <div class="mj-aca-head-search">
                <input type="text" placeholder="جستجو ...">
                <button class="fa-search mj-search-icon"></button>
            </div>
        </div>

        <!--        end head-->



        <!--        start category newest-->

        <div class="mj-slider-heading mt-4 pt-2">جدیدترین ها</div>
        <div class="mj-slider-heading-border "></div>
        <div class="mj-academy-cat-newest-grid mt-2">
            <div id="first-new-cat-post" class="mj-cat-newest-post">
                <div class="mj-cat-newest-post-cat">رانندگان</div>
                <a href="javascript:void(0)">
                    <img src="/dist/images/academy/6.jpg" alt="">
                    <div class="mj-cat-newest-post-title">
                        <div>
                            آموزش ثبت بار در انتیراپ
                        </div>
                    </div>
                </a>
            </div>
            <div id="second-new-cat-post" class="mj-cat-newest-post">
                <div class="mj-cat-newest-post-cat">رانندگان</div>
                <a href="javascript:void(0)">
                    <img src="/dist/images/academy/5.webp" alt="">
                    <div class="mj-cat-newest-post-title">
                        <div>
                            آموزش ثبت بار در انتیراپ
                        </div>
                    </div>
                </a>
            </div>
            <div id="third-new-cat-post" class="mj-cat-newest-post">
                <div class="mj-cat-newest-post-cat">رانندگان</div>
                <a href="javascript:void(0)">
                    <img src="/dist/images/academy/2.webp" alt="">
                    <div class="mj-cat-newest-post-title">
                        <div>
                            آموزش ثبت بار در انتیراپ
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!--        end category newest-->


        <!--        start category tabs-->
        <div class="mj-subcat-border"></div>
        <div class="mj-subcat-posts">
            <div class="mj-subcat-post">
                <img src="/dist/images/academy/4.webp" alt="4">
                <div class="mj-subcat-post-content">
                    <div class="mj-post-category">استعلام</div>
                    <div class="mj-post-title">
                        بارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تستبارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تست
                    </div>
                    <div class="mj-post-date">
                        1401 / 09 / 12
                    </div>

                </div>

            </div>
            <div class="mj-cat-tab-post">
                <img src="/dist/images/academy/2.webp" alt="4">
                <div class="mj-cat-tab-post-content">
                    <div class="mj-post-category">رانندگان</div>
                    <div class="mj-post-title">
                        بارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تستبارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تست
                    </div>
                    <div class="mj-post-date">
                        1401 / 09 / 12
                    </div>

                </div>

            </div>
            <div class="mj-cat-tab-post">
                <img src="/dist/images/academy/1.webp" alt="4">
                <div class="mj-cat-tab-post-content">
                    <div class="mj-post-category">تجار</div>
                    <div class="mj-post-title">
                        بارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تستبارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تست
                    </div>
                    <div class="mj-post-date">
                        1401 / 09 / 12
                    </div>

                </div>

            </div>
            <div class="mj-cat-tab-post">
                <img src="/dist/images/academy/6.jpg" alt="4">
                <div class="mj-cat-tab-post-content">
                    <div class="mj-post-category">استعلام</div>
                    <div class="mj-post-title">
                        بارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تستبارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تست
                    </div>
                    <div class="mj-post-date">
                        1401 / 09 / 12
                    </div>

                </div>

            </div>
            <div class="mj-cat-tab-post">
                <img src="/dist/images/academy/logo.png" alt="4">
                <div class="mj-cat-tab-post-content">
                    <div class="mj-post-category">احراز هویت</div>
                    <div class="mj-post-title">
                        بارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تستبارنامه زمینی چیست؟ ویژگی های بارنامه زمینی چیست؟
                        رانندگان متن درخواستی برای تست
                    </div>
                    <div class="mj-post-date">
                        1401 / 09 / 12
                    </div>

                </div>

            </div>
            <div class="mj-subcat-list-load">
                <lottie-player src="/dist/lottie/wallet-load.json"  background="transparent"  speed="1"  loop autoplay></lottie-player>
            </div>

        </div>

        <!--        end category tabs-->


    </main>
<?php
getFooter();