<?php

global $lang;

use MJ\Router\Router;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    $URI = Router::getCurrentUri();
    if (str_contains($URI, '/user/notification/group/')) {
        $notification = User::getGroupNotificationDetail($_REQUEST['id']);
    } else {
        $notification = User::getNotificationDetail($_REQUEST['id']);
        User::readNotification($user->UserId, $_REQUEST['id']);
    }

    if ($notification->status == 200) {
        $notification = $notification->response;

        include_once 'header-footer.php';

        getHeader($lang['d_notification_detail_title']);

        $title = explode('--', $notification->NotificationTitle);
        $text = explode('--', $notification->NotificationMessage);
        if (array_key_exists($title[0], $lang) && array_key_exists($text[0], $lang)) {
            $title = $lang[$title[0]];
            $translate = $lang[$text[0]];
            for ($index = 1; $index < count($text); $index++) {
                $translate = str_replace("#PARAM{$index}#", $text[$index], $translate);
            }
            $text = $translate;
        } else {
            $title = $notification->NotificationTitle;
            $text = $notification->NotificationMessage;
        }
        ?>
        <main class="container" style="padding-bottom: 180px;">
            <div class="col-12 mt-3 mj-card p-2">
                <div class="mj-about-header">
                    <a href="javascript:void(0);" class="d-flex align-items-center" onclick="history.back()">
                        <img src="/dist/images/icons/caret-right.svg" class="mj-profile-items-icon me-2" alt="back">
                        <span><?= $lang['back_prev'] ?></span>
                    </a>

                </div>
                <div class="mj-ticket-card p-2 mt-4">
                    <div class="mj-ticket-header">
                        <?= $title ?>
                    </div>
                    <div class="mj-ticket-body">
                        <?= $text ?>
                    </div>
                </div>
            </div>
        </main>
        <?php

        getFooter('', false);
    } else {
        Router::trigger404();
    }
}else{
    header('location: /login');
}