<?php

global $lang, $Settings;

use MJ\Utils\Utils;

include_once 'header-footer.php';
enqueueScript('accounts-js', '/dist/js/user/drivers-list.js');
enqueueScript('accounts-js', '/dist/libs/lottie/lottie-player.js');
getHeader($lang['d_faq_title']);

?>


    <section style="padding-top: 110px">
        <div class="mj-wallet-head-blue">
            <div class="mj-wallet-blue">
                خدمات راننده ای
            </div>
            <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
            </svg>

                <a href="javascript:void(0)" class="mj-driver-list-my-cv">
                    <div class="mj-my-cv-image">
                        <img src="/dist/images/drivers/image%204.png" alt="profile">
                    </div>
                    <div class="mj-driver-list-my-header">
                        <div class="mj-my-cv-name">
                            سعید محمد زاد نجمی
                        </div>
                        <div class="mj-my-cv-city">
                            <Span id="mycv-city-name">تهران</Span>
                            <div class="mx-1">|</div>
                            <span>ایران</span>
                        </div>
                    </div>
                    <img id="mj-mycv-icon" src="/dist/images/drivers/truckcontainer.svg" alt="truck">
                </a>
            <a href="javascript:void(0)" class="mj-driver-list-my-cv empty">
                <div class="mj-my-cv-image">
                    <img src="/dist/images/drivers/empty-profile.svg" alt="empty-profile">
                </div>
                <div class="mj-driver-list-my-header">
                    <div class="mj-my-cv-name">
                        *************
                    </div>
                    <div class="mj-my-cv-city">
                        <Span id="mycv-city-name">******</Span>
                        <div class="mx-1">|</div>
                        <span>********</span>
                    </div>
                </div>
                <img id="mj-mycv-icon" src="/dist/images/drivers/truckcontainer.svg" alt="truck">
            </a>

            <button class="mj-driver-list-add-cv-btn">افزودن رزومه</button>

        </div>
    </section>





    <section class="mj-drivers-list-section"style="padding-bottom:70px ">
        <div>
            <div class="mj-trx-head px-2">
                <div class="mj-trx-list-title ">لیست رانندگان :</div>
                <div class="mj-trx-operation-btns">
                    <div class="mj-search-btn me-2">
                        <div class="fa-search"></div>
                    </div>
                </div>
            </div>
            <div class="mj-trx-search">
                <form action="" class="mj-trx-serach-form">
                    <input type="text" id="tx-search"
                           placeholder="جستجو در بین رانندگان">
                    <button type="button">
                        <div class="fa-search"></div>
                    </button>
                </form>
            </div>
        </div>

        <div class="mj-accounts-list">

            <div class="mj-account-card mj-driver-card mb-1">
                <div class="mj-driver-card-title">
                    <div class="mj-driver-item-badge">
                        <img src="/dist/images/drivers/ntirapp-logo.svg" alt="ntirapp">
                    </div>
                    <div class="mj-driver-list-profile">
                        <img src="/dist/images/drivers/image%204.png" alt="dd">
                    </div>
                    <div class="mj-driver-info">
                        <div class="mj-cv-list-driver-name">مرتضی قاسم خانی</div>
                        <div class="mj-cv-list-city">
                            <Span class="mj-cv-city-name">تهران</Span>
                            |
                            <span>ایران</span>
                        </div>
                    </div>


                    <svg width="63" height="13" viewBox="0 0 63 13" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_818_507)">
                            <path d="M49.7336 1H0C6.35681 1 11.9766 3.14994 15.2318 6.41296C15.3239 6.55019 15.4314 6.68742 15.5542 6.8094C15.7692 7.08386 16.0149 7.34308 16.2605 7.58704C19.5157 10.8501 25.1355 13 31.4923 13C37.8491 13 43.4689 10.8501 46.7241 7.60229C46.7241 7.58704 46.7241 7.58704 46.7241 7.58704C46.9544 7.37357 47.154 7.14485 47.3536 6.90089C47.5072 6.74841 47.6147 6.59593 47.7375 6.42821C47.7375 6.42821 47.7426 6.42313 47.7529 6.41296C51.008 3.14994 56.6125 1 62.9846 1H49.7183L49.7336 1Z"
                                  fill="white"/>
                        </g>
                        <path d="M36.3906 5.16016L32.5312 8.79102C32.4043 8.91797 32.252 8.96875 32.125 8.96875C31.9727 8.96875 31.8203 8.91797 31.6934 8.81641L27.834 5.16016C27.5801 4.93164 27.5801 4.55078 27.8086 4.29688C28.0371 4.04297 28.418 4.04297 28.6719 4.27148L32.125 7.52148L35.5527 4.27148C35.8066 4.04297 36.1875 4.04297 36.416 4.29688C36.6445 4.55078 36.6445 4.93164 36.3906 5.16016Z"
                              fill="#9A9A9A"/>
                        <defs>
                            <clipPath id="clip0_818_507">
                                <rect width="63" height="12" fill="white" transform="translate(0 1)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>

                <div class="mj-driver-subdetail">
                    <div class="mj-account-info-detail mj-driver-info-subdetail">
                        <span>تاریخ تولد:</span>
                        <span>1374-12-30</span>
                    </div>
                    <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                        <span>وضعیت تاهل:</span>
                        <span>متاهل</span>
                    </div>
                    <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                        <span>وضعیت نظام وظیفه:</span>
                        <span>پایان خدمت</span>
                    </div>
                    <a class="mj-driver-item-list-link" href="javascript:void(0)">
                        جزئیات
                    </a>
                </div>


            </div>
            <div class="mj-account-card mj-driver-card mb-1">
                <div class="mj-driver-card-title">
                    <div class="mj-driver-item-badge">
                        <img src="/dist/images/drivers/ntirapp-logo.svg" alt="ntirapp">
                    </div>
                    <div class="mj-driver-list-profile">
                        <img src="/dist/images/drivers/image%204.png" alt="dd">
                    </div>
                    <div class="mj-driver-info">
                        <div class="mj-cv-list-driver-name">مرتضی قاسم خانی</div>
                        <div class="mj-cv-list-city">
                            <Span class="mj-cv-city-name">تهران</Span>
                            |
                            <span>ایران</span>
                        </div>
                    </div>


                    <svg width="63" height="13" viewBox="0 0 63 13" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_818_507)">
                            <path d="M49.7336 1H0C6.35681 1 11.9766 3.14994 15.2318 6.41296C15.3239 6.55019 15.4314 6.68742 15.5542 6.8094C15.7692 7.08386 16.0149 7.34308 16.2605 7.58704C19.5157 10.8501 25.1355 13 31.4923 13C37.8491 13 43.4689 10.8501 46.7241 7.60229C46.7241 7.58704 46.7241 7.58704 46.7241 7.58704C46.9544 7.37357 47.154 7.14485 47.3536 6.90089C47.5072 6.74841 47.6147 6.59593 47.7375 6.42821C47.7375 6.42821 47.7426 6.42313 47.7529 6.41296C51.008 3.14994 56.6125 1 62.9846 1H49.7183L49.7336 1Z"
                                  fill="white"/>
                        </g>
                        <path d="M36.3906 5.16016L32.5312 8.79102C32.4043 8.91797 32.252 8.96875 32.125 8.96875C31.9727 8.96875 31.8203 8.91797 31.6934 8.81641L27.834 5.16016C27.5801 4.93164 27.5801 4.55078 27.8086 4.29688C28.0371 4.04297 28.418 4.04297 28.6719 4.27148L32.125 7.52148L35.5527 4.27148C35.8066 4.04297 36.1875 4.04297 36.416 4.29688C36.6445 4.55078 36.6445 4.93164 36.3906 5.16016Z"
                              fill="#9A9A9A"/>
                        <defs>
                            <clipPath id="clip0_818_507">
                                <rect width="63" height="12" fill="white" transform="translate(0 1)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>

                <div class="mj-driver-subdetail">
                    <div class="mj-account-info-detail mj-driver-info-subdetail">
                        <span>تاریخ تولد:</span>
                        <span>1374-12-30</span>
                    </div>
                    <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                        <span>وضعیت تاهل:</span>
                        <span>متاهل</span>
                    </div>
                    <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                        <span>وضعیت نظام وظیفه:</span>
                        <span>پایان خدمت</span>
                    </div>
                    <a class="mj-driver-item-list-link" href="javascript:void(0)">
                        جزئیات
                    </a>
                </div>


            </div>
            <div class="mj-trx-list-load d-none">
                <lottie-player src="/dist/lottie/wallet-load.json" background="transparent" speed="1" loop
                               autoplay></lottie-player>
            </div>

        </div>


    </section>

<?php
getFooter('', false);