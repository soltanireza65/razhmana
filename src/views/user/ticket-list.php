<?php

global $lang;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    include_once 'header-footer.php';

    getHeader($lang['d_ticket_list_title']);

    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive hidden-search mt-2">
                    <table id="requests-table"
                           class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                        <thead class="text-nowrap">
                        <tr>
                            <th>#</th>
                            <th><?= $lang['d_ticket_list_table_title'] ?></th>
                            <th><?= $lang['d_ticket_list_table_department'] ?></th>
                            <th><?= $lang['d_ticket_list_table_status'] ?></th>
                            <th><?= $lang['d_table_action'] ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tickets = Ticket::getMyTicketList($user->UserId);
                        foreach ($tickets->response as $key => $item) {
                            ?>
                            <tr class="align-middle">
                                <td><?= $key + 1 ?></td>
                                <td><?= $item->TicketTitle ?></td>
                                <td><?= $item->TicketDepartment ?></td>
                                <td>
                                    <div class="text-center">
                                        <span class="mj-badge <?php
                                        if ($item->TicketStatus == 'open') {
                                            echo 'mj-badge-warning';
                                        } else {
                                            echo 'mj-badge-danger';
                                        }
                                        ?>"></span>
                                    </div>
                                </td>
                                <td>
                                    <a href="/user/ticket/<?= $item->TicketId ?>" class="mj-btn-more">
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
    </main>
    <?php
    getFooter('', false);
} else {
    header('location: /login');
}