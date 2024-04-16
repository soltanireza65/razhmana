<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('support-init', '/dist/js/user/support.init.js');

    getHeader($lang['d_support_title']);
    $support_count = User::getSupportCount(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
    if ($support_count->status == 200) {
        $support_count = $support_count->response[0]->support_badge;
    } else {
        $support_count = 0;
    }
    ?>
    <main class="container" style="padding-bottom: 80px !important;">
        <div class="row">
            <div class="col-12">
                <div class="mj-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2" alt="support">
                            <div>
                                <span class="mj-d-icon-title"><?= $lang['d_cargo_support'] ?></span>
                                <p class="mj-d-cargo-item-desc mb-0">
                                    <?= $lang['d_cargo_support_sub_title'] ?>
                                </p>
                            </div>
                        </div>

                        <div class="mj-support-links-cargo">
                            <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>"
                               class="mj-btn mj-d-btn-call me-2"
                               style="flex: 0 0 auto; min-height: 34px;">
                                <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                <?= $lang['d_cargo_call'] ?>
                            </a>

                            <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>"
                               class="mj-btn mj-d-btn-whatsapp me-2"
                               style="flex: 0 0 auto; min-height: 34px;">
                                <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                <?= $lang['d_cargo_whatsapp'] ?>
                            </a>

                            <a href="/user/support" class="mj-btn mj-d-btn-ticekt me-2"
                               style="flex: 0 0 auto; min-height: 34px;">
                                <img src="/dist/images/icons/circle-envelope.svg" class="me-1" alt="ticket"/>
                                <?= $lang['d_cargo_ticket'] ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mj-card">
                    <div class="card-body">
                        <div class="col-10 mx-auto">
                            <a href="/user/ticket-list" class="mj-btn-more py-2">
                                <?= $lang['d_show_ticket_list'] ?>
                                <div class="mj-header-support-badge mj-btn-more-badge">


                                    <?php
                                    if ($support_count != 0) {
                                        ?>
                                        <span dir="ltr"
                                              class="mj-header-support-badge-number badge rounded-pill bg-danger">
                                            <?= $support_count ?>
                                        </span>
                                        <?php
                                    }
                                    ?>


                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mj-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2" alt="">
                            <div>
                                <span class="mj-d-icon-title"><?= $lang['d_send_ticket'] ?></span>
                                <p class="mj-d-cargo-item-desc mb-0">
                                    <?= $lang['d_send_ticket_sub_title'] ?>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mj-input-filter-box mb-3">
                                    <input type="text" class="mj-text-inputs-mal mj-input-filter mj-fw-400 mj-font-12 px-0 py-1"
                                           id="ticket-subject"
                                           name="ticket-subject"
                                           placeholder="<?= $lang['d_send_ticket_input_title_placeholder'] ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mj-custom-select mb-3">
                                    <select name="ticket-department" id="ticket-department"
                                            data-width="100%">
                                        <option value="-1"><?= $lang['d_send_ticket_input_department_placeholder'] ?></option>
                                        <?php
                                        $departments = Ticket::getDepartmentsList();
                                        foreach ($departments->response as $item) {
                                            ?>
                                            <option value="<?= $item->DepartmentId ?>"><?= $item->DepartmentName ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mj-input-filter-box mb-3">
                                    <textarea class="mj-text-inputs-mal mj-input-filter mj-fw-400 mj-font-12 px-0 py-1" id="ticket-message"
                                              name="ticket-message"
                                              placeholder="<?= $lang['d_send_ticket_input_message_placeholder'] ?>"
                                              rows="6"></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <form action="#" method="post" class="dropzone mj-dropzone" id="ticektAttachments"
                                      data-plugin="dropzone" data-previews-container="#file-previews"
                                      data-upload-preview-template="#uploadPreviewTemplate">
                                    <div class="fallback">
                                        <input type="file" name="file">
                                    </div>

                                    <div class="dz-message needsclick">
                                        <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="choose-files">
                                        <h5><?= $lang['d_dropzone_label'] ?></h5>
                                    </div>
                                </form>
                            </div>

                            <div class="col-12">
                                <div class="row dropzone-previews mt-3" id="file-previews"></div>

                                <div class="d-none" id="uploadPreviewTemplate">
                                    <div class="col-12">
                                        <div class="card shadow-none border">
                                            <div class="p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <img data-dz-thumbnail src="#"
                                                             class="avatar-sm rounded" alt="">
                                                    </div>
                                                    <div class="col">
                                                        <strong class="text-muted" data-dz-name></strong>
                                                        <p class="mb-0" data-dz-size></p>
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-striped"
                                                                 role="progressbar" data-dz-progress aria-valuemin="0"
                                                                 aria-valuemax="100"></div>
                                                        </div>
                                                        <p  class="text-center mj-dropzone-progress">
                                                            <?=$lang['dropzone_inprogress']?>
                                                        </p>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href=""
                                                           class="btn btn-lg btn-link text-danger shadow-none"
                                                           data-dz-remove>
                                                            <i class="fe-x align-middle"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-7 mx-auto">
                                <input type="hidden" id="token" name="token"
                                       value="<?= Security::initCSRF('send-ticket') ?>">
                                <button class="mj-btn-more py-2 w-100" id="submit-ticket" name="submit-ticket">
                                    <?= $lang['d_button_send'] ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-processing" data-bs-backdrop="static" data-bs-keyboard="false"
             role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center my-3">
                            <lottie-player src="/dist/lottie/loading.json" class="mx-auto"
                                           style="max-width: 400px;" speed="1" loop
                                           autoplay></lottie-player>

                            <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['submit_ticket'], $lang['b_info_processing']) ?>
                                ...</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-submitted"
             role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center my-3" id="submitting-alert">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}