<?php

global $lang;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once 'header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('menu-init-js', '/dist/js/businessman/dashboard.init.js');
    enqueueScript('notifications-init-js', '/dist/js/businessman/notifications.init.js');

    getHeader($lang['d_notifications_title']);

    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mj-card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 mj-message-nav" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                        aria-selected="true">
                                    <?= $lang['notification_private'] ?>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">
                                    <?= $lang['notification_group'] ?>
                                </button>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                 aria-labelledby="pills-home-tab">
                                <div class="table-responsive hidden-search">
                                    <table id="notification-table"
                                           class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                                        <thead class="text-nowrap">
                                        <tr>
                                            <th>#</th>
                                            <th><?= $lang['notification_title'] ?></th>
                                            <th class="text-center"><?= $lang['d_table_action'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $notifications = User::getNotifications($user->UserId);

                                        foreach ($notifications->response as $key => $item) {
                                            $title = explode('--', $item->NotificationTitle);
                                            $text = explode('--', $item->NotificationMessage);
                                            if (array_key_exists($title[0], $lang) && array_key_exists($text[0], $lang)) {
                                                $title = $lang[$title[0]];
                                                $translate = $lang[$text[0]];
                                                for ($index = 1; $index < count($text); $index++) {
                                                    $translate = str_replace("#PARAM{$index}#", $text[$index], $translate);
                                                }
                                                $text = $translate;
                                            } else {
                                                $title = $item->NotificationTitle;
                                                $text = $item->NotificationMessage;
                                            }
                                            ?>
                                            <tr class="align-middle">
                                                <td><?= $key + 1 ?></td>
                                                <?php if($item->NotificationStatus =='unread'){
                                                    ?>
                                                    <td class="mj-unread-message"><?= $title ?></td>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <td><?= $title ?></td>
                                                    <?php
                                                }?>

                                                <td>
                                                    <a href="/businessman/notification/<?= $item->NotificationId ?>"
                                                       class="mj-btn-more">
                                                        <?= $lang['d_button_detail'] ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                 aria-labelledby="pills-profile-tab">
                                <div class="table-responsive hidden-search">
                                    <table id="group-notifications-table"
                                           class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                                        <thead class="text-nowrap">
                                        <tr>
                                            <th>#</th>
                                            <th><?= $lang['notification_title'] ?></th>
                                            <th class="text-center"><?= $lang['d_table_action'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $groupNotifications = User::getGroupNotifications($user->UserType, $user->UserStatus);
                                        foreach ($groupNotifications->response as $key => $item) {
                                            ?>
                                            <tr class="align-middle">
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $item->NotificationTitle ?></td>
                                                <td>
                                                    <a href="/businessman/notification/group/<?= $item->NotificationId ?>"
                                                       class="mj-btn-more">
                                                        <?= $lang['d_button_detail'] ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
