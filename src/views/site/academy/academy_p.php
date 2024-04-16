<?php
global $Settings, $lang, $Tour;
use MJ\Security\Security;
use MJ\Utils\Utils;

$res = Admin::checkAdminLogin();
if ($res->status == 200 && !empty($res->response) && $res->response->admin_status == "active") {



include_once 'views/site/header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('all-css', '/dist/css/all.css');
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');

enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('lottie-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('academy-js', '/dist/js/site/academy-swiper.js');


$academy_id = $_REQUEST['id'];
$academy_detail  = Academy::getAcademyByID($academy_id);
if($academy_detail->status ==200){
    $academy_detail = $academy_detail->response[0] ;
}else{
    $academy_id = [] ;
}
$category_detail = Academy::getCategoryById($academy_detail->category_id);
if($category_detail->status ==200){
    $category_detail=$category_detail->response[0] ;
}else{
    $category_detail=[];
}
$version = $Settings['site_version'];
getHeader($academy_detail->academy_title ,$version  ,$academy_detail->academy_excerpt  );

?>
    <style>
        a{
            color:dodgerblue ;
        }
    </style>
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

        <div class="mj-cat-detail-image">
            <img src="<?=Utils::fileExist($academy_detail->academy_thumbnail, BOX_EMPTY)?>" alt="">
        </div>
        <div class="mj-academy-post-detail">
            <div class="acaddemy-post-info">
                <span><?=$category_detail->category_name?></span>
                <div class="mx-2">|</div>
                <span dir="ltr"><?=Utils::getTimeByLang($academy_detail->academy_submit_time)?></span>
            </div>
            <div class="mj-academy-post-title">
                <?=$academy_detail->academy_title?>
            </div>
            <div class="mj-academy-post-description">
                <?=$academy_detail->academy_description?>
            </div>
        </div>
        <!--        end head-->
    </main>
<?php
    getFooter();
} else {
    header('Location: /academy');
}