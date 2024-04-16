<?php

global $lang;

use MJ\Router\Router;
use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    $ticket = Ticket::getTicketDetail($_REQUEST['id'], $user->UserId);

    if ($ticket->status == 200) {
        $ticket = $ticket->response;
        include_once 'header-footer.php';

        enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
        enqueueScript('ticket-init', '/dist/js/user/ticket.init.js');

        getHeader($lang['d_ticket_title']);

        ?>
        <style>
            .mj-backbtn {
                bottom: 76px !important;
                right: 26px !important;
            }
        </style>
        <main class="container position-relative h-100">
            <div class="row align-content-end h-100">
                <div class="col-12 px-0 position-fixed" style="top: 116px; left: 0; right: 0;z-index: 100;">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <div class="row align-items-center justify-content-between mt-2">
                                <div class="col-6">
                                    <div class="text-center">
                                        <span class="mj-font-11"><?= $lang['d_ticket_list_table_title'] ?></span>
                                        <h6 class="mj-fw-700 mj-font-13 mb-0"
                                            style="display: -webkit-box; text-overflow: ellipsis; -webkit-line-clamp:1; -webkit-box-orient: vertical; overflow: hidden">
                                            <?= $ticket->TicketTitle ?>
                                        </h6>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="text-center">
                                        <span class="mj-font-11"><?= $lang['d_ticket_create_time'] ?></span>
                                        <h6 class="mj-fw-700 mj-font-13 mb-0"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $ticket->TicketTime) : date('Y-m-d', $ticket->TicketTime) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12"
                     style="padding-top: 125px;position: absolute;top: 138px;left: 0;right: 0;bottom: 0;">
                    <div class="mj-chat-list">
                        <?php
                        $messages = Ticket::getTicketMessages($ticket->TicketId);
                        foreach ($messages->response as $item) {
                            if (!$item->isAdmin) {
                                ?>
                                <div class="mj-chat-item">
                                    <div>
                                        <div class="mj-chat-message">
                                            <?= $item->MessageText ?>
                                        </div>

                                        <div class="d-flex my-2">
                                            <?php
                                            foreach ($item->MessageAttachment as $attachment) {
                                                if (is_object($attachment)) {
                                                    // Type 1: Object with 'attachment' property
                                                    $format = explode('.', $attachment->attachment);
                                                    $url = $attachment->attachment;
                                                } elseif (is_string($attachment)) {
                                                    // Type 2: String URL
                                                    $format = explode('.', $attachment);
                                                    $url = $attachment;
                                                } else {
                                                    continue; // Skip invalid attachment format
                                                }
                                                ?>
                                                <a href="<?= $url ?>"
                                                   class="d-flex align-items-center justify-content-center bg-info text-white text-truncate me-2"
                                                   style="width: 40px; height: 40px; border-radius: 8px;"
                                                   download="">
                                                    <?= strtoupper($format[1]) ?>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <div class="mj-chat-time">
                                            <?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('j F Y, H:i a', $item->MessageTime) : date('j F Y, H:i A', $item->MessageTime) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="mj-chat-item odd">
                                    <div>
                                        <div class="mj-chat-message">
                                            <?= $item->MessageText ?>
                                        </div>

                                        <div class="d-flex my-2">
                                            <?php
                                            foreach ($item->MessageAttachment as $attachment) {
                                                $format = explode('.', $attachment->attachment);
                                                ?>
                                                <a href="<?= $attachment->attachment ?>"
                                                   class="d-flex align-items-center justify-content-center bg-info text-white text-truncate me-2"
                                                   style="width: 40px; height: 40px; border-radius: 8px;"
                                                   download="">
                                                    <?= strtoupper($format[1]) ?>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <div class="mj-chat-time">
                                            <?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('j F Y, H:i a', $item->MessageTime) : date('j F Y, H:i A', $item->MessageTime) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <?php
                if ($ticket->TicketStatus == 'open') {
                    ?>
                    <div class="col-12 py-2 mj-fixed-chat-box">
                        <div class="mj-chat-control-box">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="d-flex align-items-center mj-chat-controls-box h-100">
                                        <input type="hidden" id="token" name="token"
                                               value="<?= Security::initCSRF('send-ticket-message') ?>">
                                        <button type="button" class="mj-btn-send-message me-2" id="send-message"
                                                name="send-message" data-ticket="<?= $ticket->TicketId ?>">
                                            <img src="/dist/images/icons/send.svg" width="16" alt="send"/>
                                        </button>

                                        <label for="attachments">
                                            <img src="/dist/images/icons/paperclip.svg" width="20" alt="attachment"/>
                                        </label>
                                        <input type="file" id="attachments" name="attachments" multiple hidden>
                                    </div>
                                </div>

                                <div class="col align-self-center">
                            <textarea class="mj-input-filter mj-font-13" id="message-body"
                                      placeholder="<?= $lang['d_ticket_input_placeholder'] ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
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
        Router::trigger404();
    }
} else {
    header('location: /login');
}