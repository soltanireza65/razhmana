<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
    enqueueStylesheet('dropzone-css', '/dist/css/nsupport/nsupport.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('support-init', '/dist/js/user/nsupport.js');

    getHeader($lang['d_support_title']);
    $support_count = User::getSupportCountByStatus(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
    if ($support_count->status == 200) {
        $support_count = $support_count->response[0]->support_badge;
    } else {
        $support_count = 0;
    }

    $open_support_count = User::getSupportCountByStatus(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId, 'open');
    if ($open_support_count->status == 200) {
        $open_support_count = $open_support_count->response[0]->support_badge;
    } else {
        $open_support_count = 0;
    }
    $close_support_count = User::getSupportCountByStatus(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId, 'close');
    if ($close_support_count->status == 200) {
        $close_support_count = $close_support_count->response[0]->support_badge;
    } else {
        $close_support_count = 0;
    }

    $tickets = Ticket::getMyTicketList($user->UserId);
    ?>
    <main class=" mj-me-support-main" style="padding-bottom: 80px !important;">
        <div class="modal fade mj-me-support-modal" id="mj-me-staticBackdrop" data-bs-backdrop="static"
             data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content mj-me-support-modal-content">
                    <div class="modal-body mj-me-support-modal-body">
                        <div class="mj-me-support-modal-title-div">
                            <span><?= $lang['Send_pm_to_support'] ?></span>
                        </div>
                        <input id="ticket_title" type="text" placeholder="<?= $lang['d_send_support_input_title_placeholder'] ?>"
                               class="mj-me-support-modal-body-input">
                        <select name="" id="departemant-select">
                            <option value="-1"><?= $lang['d_send_ticket_input_department_placeholder'] ?></option>
                            <?php
                            $departments = Ticket::getDepartmentsList();
                            foreach ($departments->response as $item) {
                                ?>
                                <option value="<?= $item->DepartmentId ?>"><?= $item->DepartmentName ?></option>
                                <?php
                            }?>
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
        <div class="mj-me-support-blue-box"></div>
        <!--  support modal start -->
        <!--<section class="mj-me-support-modal-sec">-->
<!--            <button type="button" class="btn mj-me-support-modal-btn" data-bs-toggle="modal"-->
<!--                    data-bs-target="#mj-me-staticBackdrop">-->
<!--                <div class="mj-support-modal-btn-text">-->
<!--                    <div class="fa fa-message fa-beat"></div>-->
<!--                    <div class="my-3">--><?php //= $lang['Send_pm_to_support'] ?><!--</div>-->
<!--                </div>-->
<!--                <div class="fa fa-plus"></div>-->
<!--            </button>-->

     <!--   </section>-->
        <!--  support-contact start -->
        <section class="mj-me-support-contact-sec">
            <div class="mj-me-support-contact">
                <span> <?= $lang['d_support_ntirapp_way'] ?></span>
                <span><?= $lang['d_support_other_subtitle'] ?></span>

                <div class="mj-me-support-contact-icons">
                    <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>" target="_blank"
                                                             class="mj-me-support-contact-icons-img mj-me-call">
                        <img src="/dist/images/call.png" alt="">
                        <span><?= $lang['u_support'] ?></span>
                    </a>

                    <a data-bs-toggle="modal"
                       data-bs-target="#mj-me-staticBackdrop"
                       class="mj-me-support-contact-icons-img mj-me-ticket">
                        <img src="/dist/images/ticket.png" alt="">
                        <span><?= $lang['d_cargo_ticket'] ?></span>
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
        </section>
        <!--  support-contact end  -->
        <!--  new support tab links start -->
        <section class="mj-me-support-tabs-sect">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item mj-me-support-nav-item" role="presentation">
                    <button class="nav-link active mj-me-support-nav-link" id="pills-all-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-all"
                            type="button" role="tab" aria-controls="pills-all"
                            aria-selected="true"><?= $lang['d_support_m_all'] ?>
                        <span><?= $support_count ?></span>
                    </button>
                </li>

                <li class="nav-item mj-me-support-nav-item" role="presentation">
                    <button class="nav-link mj-me-support-nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-contact"
                            type="button" role="tab" aria-controls="pills-contact"
                            aria-selected="false"><?= $lang['d_support_m_open'] ?>
                        <span><?= $open_support_count ?></span>
                    </button>
                </li>
                <li class="nav-item mj-me-support-nav-item" role="presentation">
                    <button class="nav-link mj-me-support-nav-link" id="pills-closed-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-closed"
                            type="button" role="tab" aria-controls="pills-closed"
                            aria-selected="false"><?= $lang['d_support_m_closed'] ?>
                        <span><?= $close_support_count ?></span>
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                    <?php foreach ($tickets->response as $key => $item) {
                        ?>
                        <a href="/user/ticket/<?= $item->TicketId ?>"
                           class="mj-me-support-cards <?= $item->TicketStatus == 'open' ? ' mj-me-answered' : 'mj-me-wating' ?>">
                            <div class="mj-me-support-cards-firdiv"><span
                                    class="mj-me-support-cards-firdiv-span"><?= $item->TicketTitle ?></span><span><?= $item->TicketId ?></span>
                            </div>
                            <div class="mj-me-support-cards-secdiv">
                                <div class="mj-me-support-cards-secdiv-date">
                                    <span><?= Utils::getTimeByLang($item->TicketTime) ?></span>
                                    <div>|</div>
                                    <span><?= Utils::getHourMiniteSecondByLang($item->TicketTime, "H:i") ?></span>
                                </div>
                                <span><?= $item->TicketDepartment ?></span>
                            </div>
                        </a>

                        <?php
                    }
                    ?>

                </div>

                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <?php foreach ($tickets->response as $key => $item) {
                        if ($item->TicketStatus == 'open') {


                            ?>
                            <a href="/user/ticket/<?= $item->TicketId ?>"
                               class="mj-me-support-cards <?= $item->TicketStatus == 'open' ? ' mj-me-answered' : 'mj-me-wating' ?>">
                                <div class="mj-me-support-cards-firdiv"><span
                                        class="mj-me-support-cards-firdiv-span"><?= $item->TicketTitle ?></span><span><?= $item->TicketId ?></span>
                                </div>
                                <div class="mj-me-support-cards-secdiv">
                                    <div class="mj-me-support-cards-secdiv-date">
                                        <span><?= Utils::getTimeByLang($item->TicketTime) ?></span>
                                        <div>|</div>
                                        <span><?= Utils::getHourMiniteSecondByLang($item->TicketTime, "H:i") ?></span>
                                    </div>
                                    <span><?= $item->TicketDepartment ?></span>
                                </div>
                            </a>

                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="tab-pane fade" id="pills-closed" role="tabpanel" aria-labelledby="pills-closed-tab">
                    <?php foreach ($tickets->response as $key => $item) {
                        if ($item->TicketStatus == 'close') {


                            ?>
                            <a href="/user/ticket/<?= $item->TicketId ?>"
                               class="mj-me-support-cards <?= $item->TicketStatus == 'open' ? ' mj-me-answered' : 'mj-me-wating' ?>">
                                <div class="mj-me-support-cards-firdiv"><span
                                        class="mj-me-support-cards-firdiv-span"><?= $item->TicketTitle ?></span><span><?= $item->TicketId ?></span>
                                </div>
                                <div class="mj-me-support-cards-secdiv">
                                    <div class="mj-me-support-cards-secdiv-date">
                                        <span><?= Utils::getTimeByLang($item->TicketTime) ?></span>
                                        <div>|</div>
                                        <span><?= Utils::getHourMiniteSecondByLang($item->TicketTime, "H:i") ?></span>
                                    </div>
                                    <span><?= $item->TicketDepartment ?></span>
                                </div>
                            </a>

                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
        <!--  new support tab links start  -->

    </main>
    <input type="hidden" id="token" name="token"
           value="<?= Security::initCSRF2( ) ?>">
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}