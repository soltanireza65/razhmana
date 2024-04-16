<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    $request = Driver::getRequestDetail($_REQUEST['id'], $user->UserId);
    $request = (isset($request->response)) ? $request->response : null;

    include_once 'header-footer.php';

    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');

    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('start-transportation-init', '/dist/js/driver/start-transportation.init.js');

    getHeader($lang['d_start_transportation_title']);

    ?>
    <main class="container" style="padding-bottom: 180px;">


        <?php


        if (empty($request) || (!empty($request) && $request->RequestStatus == 'pending')) {
            ?>
            <div class="row">
                <div class="col-12">
                    <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                        <div class="mj-alert-icon">
                            <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                        </div>
                        <?= $lang['d_alert_authentication_4'] ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            if (in_array($request->RequestStatus, ['progress', 'completed'])) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>
                            <?= str_replace('#date#', ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('j F Y | H:i', $request->RequestStartDate) : date('j F Y | H:i', $request->RequestStartDate), $lang['d_alert_authentication_5']) ?>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($request->RequestStatus == 'accepted') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" id="token" name="token"
                               value="<?= Security::initCSRF('start-transportation') ?>">
                        <button type="button" id="submit-start" name="submit-start"
                                data-request="<?= $request->RequestId ?>" data-cargo="<?= $request->CargoId ?>"
                                class="mj-d-floating-button mj-d-floating-button-top"><?= $lang['d_cargo_start_transportation_button'] ?></button>

                        <div class="mj-d-cargo-card mb-3">
                            <input type="hidden" id="token-complaint" name="token-complaint"
                                   value="<?= Security::initCSRF('submit-complaint') ?>">
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

                                <div class="d-flex align-items-center flex-nowrap overflow-auto">
                                    <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>" class="mj-btn mj-d-btn-call me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                        <?= $lang['d_cargo_call'] ?>
                                    </a>

                                    <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>" class="mj-btn mj-d-btn-whatsapp me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                        <?= $lang['d_cargo_whatsapp'] ?>
                                    </a>

                                    <a href="/user/support" class="mj-btn mj-d-btn-ticekt me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/circle-envelope.svg" class="me-1" alt="ticket"/>
                                        <?= $lang['d_cargo_ticket'] ?>
                                    </a>

                                    <a href="javascript:void(0);" class="mj-btn mj-d-btn-complaint me-2"
                                       style="flex: 0 0 auto; min-height: 34px;" data-cargo="<?= $request->CargoId ?>"
                                       data-request="<?= $request->RequestId ?>"
                                       data-businessman="<?= $request->BusinessmanId ?>" onclick="submitComplaint(this);">
                                        <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="complaint"/>
                                        <?= $lang['d_cargo_complaint'] ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mj-d-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/photo-film.svg" class="mj-d-icon-box me-2"
                                         alt="images-of-delivery">
                                    <div>
                                        <span class="mj-d-icon-title"><?= $lang['d_cargo_images_of_delivery'] ?></span>
                                        <p class="mj-d-cargo-item-desc mb-0">
                                            <?= $lang['d_cargo_images_of_delivery_sub_title'] ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <form action="#" method="post" class="dropzone mj-dropzone"
                                                  id="attachments"
                                                  data-plugin="dropzone" data-previews-container="#file-previews"
                                                  data-upload-preview-template="#uploadPreviewTemplate">
                                                <div class="fallback">
                                                    <input type="file" name="file">
                                                </div>

                                                <div class="dz-message needsclick">
                                                    <img src="/dist/images/icons/folder-plus.svg" class="mb-2"
                                                         alt="choose-images">
                                                    <h5><?= $lang['d_dropzone_label'] ?></h5>
                                                </div>
                                            </form>
                                        </div>
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
                                                                         role="progressbar" data-dz-progress
                                                                         aria-valuemin="0"
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

                                    <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['d_start_transportation_title'], $lang['b_info_processing']) ?>
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
                <?php
            } else {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>
                            <?= $lang['d_alert_authentication_4'] ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}