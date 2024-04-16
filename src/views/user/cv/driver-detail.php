<?php
global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;


$faqs = $Settings['u_faq'];

$cv_id = $_REQUEST['id'];
if (isset($_COOKIE['user-login'])) {
    $user = User::getUserInfo();
}


$my_cv = CV::getCvDetailById($cv_id);
if ($my_cv->status == 200) {
    $my_cv = $my_cv->response[0];
} else {
    $my_cv = [];
    header("Location: /");
}
$cv_user_info = User::getUserInfo($my_cv->user_id);

$user_type = 'other';
if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
    $user_type = 'driver';
}

$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

include_once getcwd() . '/views/site/header-footer.php';
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.css');

enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('driver-js', '/dist/js/user/drivers/driver-detail.js');
enqueueScript('accounts-js', '/dist/libs/lottie/lottie-player.js');
getHeader($lang['d_faq_title']);

$rate = Driver::getDriverRequestsRate($my_cv->user_id);
$rate_in = Driver::getDriverRequestsInRate($my_cv->user_id);

$rateouput = ($rate + $rate_in) / 2;


?>
    <section style="padding: 120px 10px 0px 10px;position:relative; z-index: 1;">
        <div class="mj-driver-detail-header-card">
            <div class="mj-driver-profile-head">
                <img src="<?= Utils::fileExist($my_cv->cv_user_avatar, POSTER_DEFAULT); ?>" alt="profile">
                <div class="mj-driver-name-city">
                    <div class="mj-cv-list-driver-name mj-cv-list-driver-name2">
                        <?= $my_cv->cv_name . ' ' . $my_cv->cv_lname ?>
                    </div>
                    <div class="mj-cv-city-name mj-cv-city-name2">
                        <span
                            id=""><?= array_column(json_decode($my_cv->city_name, true), 'value', 'slug')[$language] ?></span>
                        |
                        <span><?= Location::getCountryByCityId($my_cv->city_id)->CountryName ?></span>
                    </div>
                </div>
            </div>
            <div class="mj-driver-bith-date">
                <span><?= $lang['u_driver_cv_birth_day'] ?> :</span>
                <span><?= Utils::getTimeByLang($my_cv->cv_brith_date) ?></span>
            </div>
            <div id="line"></div>
            <div class="mj-driver-awards">
                <div class="mj-driver-money">
                    <div id="money-award-title">
                        <div class="fa-dollar mj-awards-icon">
                            <span><?= $lang['u_driver_cv_gift'] ?></span>
                        </div>
                        <div id="money-num-row">
                            <span id="money-num"><?= $cv_user_info->UserGift ?></span>
                        </div>
                    </div>
                </div>

                <div class="mj-driver-points">
                    <div id="point-award-title">
                        <div class="fa-star mj-awards-icon">
                            <span> <?= $lang['u_driver_cv_score'] ?></span>
                        </div>
                        <div class="wrapper">


                            <div class="rating-holder">
                                <div class="c-rating c-rating--big" data-rating-value="<?= round($rateouput, 0.5) ?>">
                                    <button>1</button>
                                    <button>2</button>
                                    <button>3</button>
                                    <button>4</button>
                                    <button>5</button>
                                </div>
                            </div>
                        </div>
                        <div id="point-num"></div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="mj-cv-operations-btn <?= ($user_type == 'driver' && $user->UserId == $my_cv->user_id) ? '' : 'd-none'; ?>">
            <a href="/user/drivers/edit" class="mj-cv-edit-btn">
                <div class="fa-edit "></div>
                <span><?= $lang['u_driver_cv_edit_cv'] ?></span>
            </a>
            <?php if ($my_cv->cv_status == 'accepted') { ?>
                <a href="javascript:void(0);" id="change-role-status" data-cv-id="<?= $my_cv->cv_id ?>"
                   data-cv-role-status="<?= $my_cv->cv_role_status ?>"
                   class="mj-cv-on-off-btn <?= ($my_cv->cv_role_status == 'yes') ? 'disbale' : ''; ?>">
                    <div class="fa-circle-xmark"></div>
                    <span><?= ($my_cv->cv_role_status == 'no') ? $lang['u_cv_driver_notactive'] : $lang['u_cv_driver_active']; ?></span>
                </a>
            <?php } ?>
        </div>
    </section>
    <section>
        <?php if ($my_cv->cv_status == 'rejected') {
            ?>
            <span>
                <?= $my_cv->rejected_desc ?>
            </span>
            <?php
        } ?>
    </section>

    <section class="<?= ($user->UserId == $my_cv->user_id) ? 'd-none' : ''; ?>">
        <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>" class="mj-call-with-driver">
            <div class="fa-phone fa-shake"></div>
            تماس با راننده
        </a>
    </section>
    <section class="mj-cv-items-list">
        <?php if ($my_cv->cv_military_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_military_status'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>
            <?php
        } ?>
        <?php if ($my_cv->cv_smartcard_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_smartcard_status'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>
            <?php
        } ?>   <?php if ($my_cv->cv_passport_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_passport_status'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>
            <?php
        } ?>   <?php if ($my_cv->cv_visa_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_visa_status'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>

            <?php
            $locations = explode(',', $my_cv->cv_visa_location);
 
            ?>
            <div class="mj-m-location-items <?=  !$my_cv->cv_visa_location ? 'd-none' : '' ?>">
                <?php
                foreach ($locations as $item) {

                    ?>
                    <span class="mj-m-location-item">
                        <?php if (VisaLocation::getVisaLocationById($item)->response[0]->visa_name) {
                            echo array_column(json_decode(VisaLocation::getVisaLocationById($item)->response[0]->visa_name), 'value', 'slug')[$_COOKIE['language']];
                        } ?>
                </span>
                    <?php
                }
                ?>

            </div>
            <?php
        } ?>   <?php if ($my_cv->cv_workbook_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_workbook_status'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>
            <?php
        } ?>   <?php if ($my_cv->cv_driver_license_status == 'yes') {
            ?>
            <div class="mj-cv-item-card">
                <div class="mj-cv-item-badge">
                    <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                </div>
                <div class="mj-cv-item-text">
                    <span><?= $lang['cv_driver_license_image'] ?></span>
                    <span><?= $lang['u_driver_cv_haveing'] ?></span>
                </div>
            </div>
            <?php
        } ?>
        <!--   <div class="accordion-item mj-cv-item-accordion">
               <h2 class="accordion-header " id="headingTwo">
                   <div class="mj-cv-item-badge">
                       <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
                   </div>
                   <button class="accordion-button  collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                       گواهینامه
                   </button>
               </h2>
               <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                   <div class="accordion-body">
                       <div class="mj-cv-subitem-accordion">
                           <span>شماره گواهینامه</span>
                           <span>123545451135</span>
                       </div>
                       <div id="line2"></div>
                       <div class="mj-cv-subitem-accordion">
                           <span>تایخ انقضاء</span>
                           <span>1403-01-21</span>
                       </div>
                       <div id="line2"></div>
                       <div class="mj-cv-subitem-accordion">
                           <span>مشاهده مدارک</span>
                           <div class="mj-cv-subitem-image">
                               <a href="javascript:void(0)">
                                   <div class="fa-search"></div>
                               </a>
                               <img src="/dist/images/drivers/image%204.png" alt="#">

                           </div>
                       </div>
                   </div>
               </div>
           </div>-->
    </section>
    <input type="hidden" id="token2" name="token2" value="<?= Security::initCSRF2() ?>">
<?php
getFooter('', false);