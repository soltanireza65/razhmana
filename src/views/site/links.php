<?php
global $Settings, $lang;

use MJ\Utils\Utils;

include_once 'header-footer.php';
enqueueStylesheet('BScss', '/dist/libs/bootstrap/css/bootstrap.rtl.min.css');
enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
enqueueStylesheet('qrcodecss', '/dist/css/qrcode.css');
enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
enqueueScript('dropzone-js', '/dist/js/site/qrcode.js');
if (isset($_COOKIE['language']) && substr($_COOKIE['language'], 0, 2) != 'fa') {
    enqueueStylesheet('qrcodecss', '/dist/css/qrcode-ltr.css');
}
$init_lang = 'fa';
if (isset($_COOKIE['language'])) {
    $init_lang = substr($_COOKIE['language'], 0, 2);
}
enqueueStylesheet('BScss', '/dist/libs/bootstrap/css/bootstrap.rtl.min.css');
enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
enqueueScript('BSjs', '/dist/libs/bootstrap/js/bootstrap.bundle.min.js');
getHeader('', '', '', false);
?>
    <div class="modal fade mj-me-support-modal" id="mj-me-staticBackdrop" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mj-me-support-modal-content">
                <div class="modal-body mj-me-support-modal-body">
                    <div class="mj-me-support-modal-title-div">
                        <span><?= $lang['Send_pm_to_support'] ?></span>
                    </div>
                    <input id="ticket_title" type="text"
                           placeholder="<?= $lang['d_send_support_input_title_placeholder'] ?>"
                           class="mj-me-support-modal-body-input">
                    <select name="" id="departemant-select">
                        <option value="-1"><?= $lang['d_send_ticket_input_department_placeholder'] ?></option>
                        <?php
                        $departments = Ticket::getDepartmentsList();
                        foreach ($departments->response as $item) {
                            ?>
                            <option value="<?= $item->DepartmentId ?>"><?= $item->DepartmentName ?></option>
                            <?php
                        } ?>
                    </select>
                    <textarea id="ticket_description" class="mj-me-support-modal-body-textarea"
                              placeholder="<?= $lang['d_Support_text_placeholder'] ?>"
                              maxlength="200"></textarea>

                    <div style="" id="">
                        <DIV id="dropzone">
                            <FORM class="dropzone needsclick mj-add-dropzone" id="my-support-dz"
                                  action="/uploads">
                                <DIV class="dz-message needsclicklang_vars.cv_aicard_drop">
                                    <div class="fa-plus-circle mt-2 font-28"></div>
                                    <div class=" mt-2">
                                        <?= $lang['d_Support_dz_message'] ?>

                                    </div>
                                    <div style="color: red" id="support-error">

                                    </div>
                                </DIV>
                            </FORM>
                        </DIV>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mj-me-support-modal-footer">
                    <button type="button"
                            id="submit_ticket"
                            class="btn mj-me-support-modal-footer-send-btn"><?= $lang['d_Support_send_message'] ?></button>
                    <button type="button" class="btn mj-me-support-modal-footer-close-btn"
                            data-bs-dismiss="modal">
                        <?= $lang['d_btn_close'] ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <?=$lang['L_special_IOS_user']?>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mj-add-to-home-tut-head">
                        <img src="/dist/images/ntirapp-qr.svg" alt="">
                        <div> <?=$lang['L_ntirapp_use_guide']?></div>
                    </div>
                    <div id="divider"></div>
                    <div class="mj-add-to-home-rows">
                        <div class=" mj-add-to-home-tut-icons">
                            <img src="/dist/images/outsquare.svg" alt="">
                        </div>
                        <span> <?=$lang['L_touch_share_btn']?></span>
                    </div>
                    <div class="mj-add-to-home-rows">
                        <div class="f mj-add-to-home-tut-icons">
                            <img src="/dist/images/plus.svg" alt="">
                        </div>
                        <span> <?=$lang['L_touch_ATH_btn']?></span>

                    </div>
                    <div class="mj-add-to-home-rows">
                        <div class="f mj-add-to-home-tut-icons">
                            <img src="/dist/images/add.svg" alt="">
                        </div>
                        <span> <?=$lang['L_touch_add_btn']?></span>

                    </div>
                    <div class="mj-add-to-home-tut-video">
                        <video class="mj-turkey" width="80%"  controls>
                            <source src="<?=$Settings['links_add_to_home_screen_video_section']?>" type="video/mp4">
                        </video>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <main class="mj-links-main">
        <div class="mj-site-name">
            nTirApp
        </div>
        <div class="mj-direct-download">
            <a href="https://ntirapp.com/">
                <div class="mj-android-direct-item">
                    <div class="mj-direct-right-sec">
                        <img src="/dist/images/ntirapp-qr.svg" alt="direct">
                        <div class="mj-direct-download-text">
                            <span><?=$lang['L_enter_site_direct']?></span>
                        </div>
                    </div>
                    <div class="mj-fa-globe fa-globe fa-bounce"></div>
                </div>
            </a>
        </div>
        <div class="mb-3 text-center">
            <?=$lang['L_special_android_user']?>
        </div>
        <div class="mj-android-links">
            <div class="mj-three-android-links">
                <a href="https://myket.ir/app/com.ntirapp.androidapp">
                    <div class="mj-app-market-link mj-myket">
                        <img src="/dist/images/myket.svg" alt="myket">
                        <div class="mj-download-text">
                    <span class="text-center d-block">
                       <?=$lang['L_myket_download']?>
                    </span>
                        </div>
                        <div class="mj-fa-arrow-down fa-arrow-down"></div>
                    </div>
                </a>
                <a href="https://cafebazaar.ir/app/com.ntirapp.androidapp">
                    <div class="mj-app-market-link mj-bazar">
                        <img src="/dist/images/bazar.svg" alt="bazar">
                        <div class="mj-download-text">
                    <span class="text-center d-block">
   <?=$lang['L_cafebazar_download']?>
                    </span>
                        </div>
                        <div class="mj-fa-arrow-down fa-arrow-down">

                        </div>
                    </div>
                </a>
                <a href="javascript:void(0)">
                    <div class="mj-app-market-link mj-playstore">
                        <img src="/dist/images/playstore.svg" alt="playstore">
                        <div class="mj-download-text">
                    <span class="text-center d-block">

                        <span id="googleplay" class="d-block">   <?=$lang['L_from_download']?></span>
                        <span class="d-block">Google Play</span>
                    </span>
                        </div>
                        <div class="mj-fa-arrow-down fa-arrow-down">

                        </div>
                    </div>
                </a>
            </div>


        </div>
        <div class="mj-direct-download">
            <a href="https://ntirapp.com/app.apk">
                <div class="mj-android-direct-item">
                    <div class="mj-direct-right-sec">
                        <img src="/dist/images/ntirapp-qr.svg" alt="direct">
                        <div class="mj-direct-download-text">
                            <span>   <?=$lang['L_direct_download']?></span>

                            <span>   <?=$lang['L_special_android_user']?></span>
                        </div>
                    </div>

                    <div class="mj-fa-arrow-down fa-arrow-down">

                    </div>
                </div>
            </a>
        </div>

        <div class="mb-3 text-center">
           <?=$lang['L_special_IOS_user']?>
        </div>
        <div class="mj-ios-link mb-5">
            <a href="https://ntirapp.com" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <div class="mj-ios-link-card">
                    <div class="mj-ios-link-text">
                        <div class="mj-ios-download-title">
                            <span> <?=$lang['L_webapp_version']?></span>
                            <div class="fa-arrow-down mj-fa-arrow-down-ios"></div>
                        </div>

                    </div>
                    <img src="/dist/images/apple.svg" alt="apple">
                </div>
            </a>
        </div>

        <div class="container mt-4">
            <div class="mj-me-support-contact">
                <span> <?= $lang['d_support_ntirapp_way'] ?></span>
                <span><?= $lang['d_support_other_subtitle'] ?></span>

                <div class="mj-me-support-contact-icons">
                    <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>" target="_blank"
                       class="mj-me-support-contact-icons-img mj-me-call">
                        <img src="/dist/images/call.png" alt="">
                        <span><?= $lang['u_support'] ?></span>
                    </a>

                    <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>" target="_blank"
                       class="mj-me-support-contact-icons-img mj-me-whatsapp">
                        <img src="/dist/images/whtsap.png" alt="">
                        <span><?= $lang['support_whatsapp_small'] ?></span>
                    </a>
                    <a href="https://t.me/<?= Utils::getFileValue("settings.txt", 'telegram') ?>"
                       class="mj-me-support-contact-icons-img mj-me-telegram">
                        <img src="/dist/images/telgrm.png" alt="">
                        <span><?= $lang['d_support_telegram'] ?></span>
                    </a>
                    <a href="https://www.instagram.com/ntirapp" target="_blank"
                       class="mj-me-support-contact-icons-img mj-me-instagram"><img
                            src="/dist/images/instgrm.png" alt="">
                        <span><?= $lang['d_support_instagram'] ?></span>
                    </a>


                </div>
                <img class="mj-me-support-contact-img" src="/dist/images/Group.png" alt="">
            </div>
        </div>
    </main>

<?php
getFooter('', false);