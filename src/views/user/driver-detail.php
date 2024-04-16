<?php
global $lang, $Settings;
use MJ\Utils\Utils;
include_once 'header-footer.php';
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.css');
enqueueScript('lottie-js', '/dist/js/user/drivers-list.js');
enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('accounts-js', '/dist/libs/lottie/lottie-player.js');
getHeader($lang['d_faq_title']);
$faqs = $Settings['u_faq'];

?>


    <section style="padding: 120px 10px 0px 10px;position:relative; z-index: 1;">
        <div class="mj-driver-detail-header-card">
            <div class="mj-driver-profile-head">
                <img src="/dist/images/drivers/image%204.png" alt="profile">
                <div class="mj-driver-name-city">
                    <div class="mj-cv-list-driver-name mj-cv-list-driver-name2">سعید محمدزاد نجمی</div>
                    <div class="mj-cv-city-name mj-cv-city-name2">
                        <span id="">تهران</span>
                        |
                        <span>ایران</span>
                    </div>
                </div>
            </div>

            <div class="mj-driver-bith-date">
                <span>تاریخ تولد :</span>
                <span>1370-12-04</span>
            </div>
            <div id="line"></div>
            <div class="mj-driver-awards">
                <div class="mj-driver-money">
                    <div id="money-award-title">
                        <div class="fa-dollar mj-awards-icon">
                            <span>جوایز</span>
                        </div>
                        <div id="money-num-row">
                            <span id="money-num">200,000</span>
                            <span>تومان</span>
                        </div>
                    </div>
                </div>
                <div class="mj-driver-points">
                    <div id="point-award-title">
                        <div class="fa-star mj-awards-icon">
                            <span>امتیاز ها</span>
                        </div>
                        <div id="point-num">1250</div>

                    </div>
                </div>
            </div>
        </div>
        <div class="mj-cv-operations-btn">
            <button class="mj-cv-edit-btn">
                <div class="fa-edit "></div>
                <span>ویرایش رزومه</span>
            </button>
            <button class="mj-cv-on-off-btn">
                <div class="fa-circle-xmark"></div>
                <span>غیر فعال کردن</span>
            </button>
        </div>
    </section>
    <section class="mj-cv-items-list">
        <div class="mj-cv-item-card">
            <div class="mj-cv-item-badge">
                <img src="/dist/images/drivers/ntirapp-logo.svg" alt="nti-logo">
            </div>
            <div class="mj-cv-item-text">
                <span>وضعیت نظام وظیفه</span>
                <span>پایان خدمت</span>
            </div>
        </div>
        <div class="accordion-item mj-cv-item-accordion">
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
        </div>


    </section>


<?php
getFooter('', false);