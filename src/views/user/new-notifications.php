<?php

global $lang;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once 'header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/css/nnotification/nnotification.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('notifications-init-js', '/dist/js/user/notifications.init.js');

    getHeader($lang['d_notifications_title']);
    $groupNotifications = User::getGroupNotifications($user->UserType, $user->UserStatus, 'notices');
    $notics_count =count($groupNotifications->response);

    $discountNotifications = User::getGroupNotifications($user->UserType, $user->UserStatus, 'discount');
    $discount_count = count($discountNotifications->response);


    $personal_notics_unread_count = Notification::getUserNotificationsCount($user->UserId  );
    $personal_notifications = Notification::getUserNotifications($user->UserId);
    $personal_notics_count = count($personal_notifications->response);


    ?>
    <main class="mj-container" style="padding-bottom: 180px;">
        <ul class="nav nav-pills mj-sad-activities-tabs-ul" id="pills-tab" role="tablist">
            <li class="nav-item mj-sad-activities-tabs-item" role="presentation">
                <button class="nav-link mj-sad-activities-tabs-btn active" id="mj-sad-activities-tab-all-id"
                        data-bs-toggle="pill" data-bs-target="#mj-sad-myActivities-tab-id" type="button" role="tab"
                        aria-controls="mj-sad-myActivities-tab-id" aria-selected="true">
                    <span><?=$lang['my_operations']?>  </span>
                    <span class="mj-sad-activities-tab-item-numbers"><?= $personal_notics_unread_count ?></span>
                </button>
            </li>

            <li class="nav-item mj-sad-activities-tabs-item" role="presentation">
                <button class="nav-link mj-sad-activities-tabs-btn" id="mj-sad-activities-tab-offer-id"
                        data-bs-toggle="pill" data-bs-target="#mj-sad-offers-notif-tab-id" type="button" role="tab"
                        aria-controls="mj-sad-offers-notif-tab-id" aria-selected="false">
                    <span><?=$lang['discount']?></span>
                    <span class="mj-sad-activities-tab-item-numbers"><?= $discount_count ?></span>
                </button>
            </li>
            <li class="nav-item mj-sad-activities-tabs-item" role="presentation">
                <button class="nav-link mj-sad-activities-tabs-btn" id="mj-sad-notice-all-id" data-bs-toggle="pill"
                        data-bs-target="#mj-sad-notices-tab-id" type="button" role="tab"
                        aria-controls="mj-sad-notices-tab-id" aria-selected="false">
                    <span class="mj-sad-tabs-text"><?=$lang['notices']?></span>
                    <span class="mj-sad-activities-tab-item-numbers"><?= $notics_count ?></span>
                </button>
            </li>
        </ul>
        <div class="tab-content" id="mj-sad-activities-tab-contents">
            <div class="tab-pane fade show active" id="mj-sad-myActivities-tab-id" role="tabpanel"
                 aria-labelledby="mj-sad-activities-tab-all-id" tabindex="0">
                <?php
                foreach ($personal_notifications->response as $key => $item) {

                    ?>
                    <div data-notification-id="<?=$item->notification_id?>" data-notification-link="<?=$item->notification_link?>"
                        class="mj-sad-activities-tab-myActivities-item <?= $item->notification_status == 'read' ? 'mj-sad-activities-tab-read-item' : '' ?> " >
                        <div class="mj-sad-activities-tab-all-item-info-1">
                            <div class="mj-sad-activities-tab-all-item-info-1-top"><?=  $lang[$item->notification_title]  ?>
                            </div>
                            <div class="mj-sad-activities-tab-all-item-info-1-bottom">

                                <?=  $lang[$item->notification_message] ?>
                             </div>
                        </div>
                        <div class="mj-sad-activities-tab-all-item-info-2">
                            <div
                                class="mj-sad-activities-chat-message-time-date"><?= Utils::getTimeByLang($item->notification_time) ?></div>
                            <div>|</div>
                            <div
                                class="mj-sad-activities-chat-message-time-clock"><?= Utils::getHourMiniteSecondByLang($item->notification_time, "H:i") ?></div>
                        </div>
                    </div>


                    <?php
                }
                if ($personal_notics_count == 0) {
                    ?>
                    <div class="mj-sad-activities-tab-message-notFound-div">
                        <div class="mj-sad-activities-tab-message-notFound-img-div">
                            <img src="/dist/images/message-not-found.png" alt="ntirapp">
                        </div>
                        <span><?=$lang['not_found']?></span>
                    </div>
                    <?php
                }
                ?>

            </div>
            <div class="tab-pane fade" id="mj-sad-offers-notif-tab-id" role="tabpanel"
                 aria-labelledby="mj-sad-activities-tab-offer-id" tabindex="0">
                <?php

                foreach ($discountNotifications->response as $key => $item) {
                    ?>
                    <a href="/user/notification/group/<?= $item->NotificationId ?>"
                       >
                        <div class="mj-sad-activities-tab-notice-item">
                            <div class="mj-driver-item-badge">
                                <img src="/dist/images/drivers/ntirapp-logo.svg" alt="ntirapp">
                            </div>
                            <div class="mj-sad-activities-tab-all-item-info-1">
                                <div class="mj-sad-activities-tab-all-item-info-1-top">
                                    <?= $item->NotificationTitle ?>
                                </div>
                                <div class="mj-sad-activities-tab-all-item-info-1-bottom">
                                    <div
                                        class="mj-sad-activities-chat-message-time-date"><?= Utils::getTimeByLang($item->NotificationTime) ?></div>
                                    <div>|</div>
                                    <div
                                        class="mj-sad-activities-chat-message-time-clock"><?= Utils::getHourMiniteSecondByLang($item->NotificationTime, "H:i") ?></div>
                                </div>
                            </div>

                        </div>
                    </a>


                    <?php
                }
                ?>



                <?php if ($discount_count == 0) {
                    ?>
                    <div class="mj-sad-activities-tab-message-notFound-div">
                        <div class="mj-sad-activities-tab-message-notFound-img-div">
                            <img src="/dist/images/message-not-found.png" alt="ntirapp">
                        </div>
                        <span><?=$lang['not_found']?></span>
                    </div>
                    <?php
                } ?>
            </div>
            <div class="tab-pane fade" id="mj-sad-notices-tab-id" role="tabpanel" aria-labelledby="mj-sad-notice-all-id"
                 tabindex="0">
                <?php

                foreach ($groupNotifications->response as $key => $item) {
                    ?>
                    <a href="/user/notification/group/<?= $item->NotificationId ?>"
                        >
                        <div class="mj-sad-activities-tab-notice-item">
                            <div class="mj-driver-item-badge">
                                <img src="/dist/images/drivers/ntirapp-logo.svg" alt="ntirapp">
                            </div>
                            <div class="mj-sad-activities-tab-all-item-info-1">
                                <div class="mj-sad-activities-tab-all-item-info-1-top">
                                    <?= $item->NotificationTitle ?>
                                </div>
                                <div class="mj-sad-activities-tab-all-item-info-1-bottom">
                                    <div
                                        class="mj-sad-activities-chat-message-time-date"><?= Utils::getTimeByLang($item->NotificationTime) ?></div>
                                    <div>|</div>
                                    <div
                                        class="mj-sad-activities-chat-message-time-clock"><?= Utils::getHourMiniteSecondByLang($item->NotificationTime, "H:i") ?></div>
                                </div>
                            </div>

                        </div>
                    </a>


                    <?php
                }
                ?>

                <?php if ($personal_notics_count == 0) {
                    ?>
                    <div class="mj-sad-activities-tab-message-notFound-div">
                        <div class="mj-sad-activities-tab-message-notFound-img-div">
                            <img src="/dist/images/message-not-found.png" alt="ntirapp">
                        </div>
                        <span><?=$lang['not_found']?></span>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}
