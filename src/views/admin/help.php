<?php
global $lang;

include_once 'header-footer.php';

$resultCheckAdminLogin = Admin::checkAdminLogin();

$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {

        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }

        getHeader($lang['help'], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => 'help',
            'pageSlugValue' => 'general',
        ]);
        ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title"><?= $lang['help']; ?></h4>
                        <p class="text-muted font-13 mb-3">

                        </p>

                        <div class="row">
                            <div class="col-4">
                                <nav id="navbar-example3" class="navbar navbar-light flex-column">
                                    <a class="navbar-brand font-15" href="#"><?= $lang['help_notic_first']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11 active" href="#item-1"><?= $lang['help_notic_login']; ?></a>
                                        <a class="nav-link font-11" href="#item-2"><?= $lang['help_notic_myaccount']; ?></a>
                                        <a class="nav-link font-11" href="#item-3"><?= $lang['help_notic_time']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-4"><?= $lang['help_notic_users']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-4"><?= $lang['help_notic_driver']; ?></a>
                                        <a class="nav-link font-11" href="#item-5"><?= $lang['help_notic_businessman']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-6"><?= $lang['help_notic_cargo']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-6"><?= $lang['help_notic_cargo_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-7"><?= $lang['help_notic_car']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-7"><?= $lang['help_notic_car_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-8"><?= $lang['help_department']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-8"><?= $lang['help_department_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-9"><?= $lang['help_notic_car_bank']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-9"><?= $lang['help_notic_car_bank_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-10"><?= $lang['help_notic_transaction']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-10"><?= $lang['help_notic_transaction_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-11"><?= $lang['help_notic_complaint']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-11"><?= $lang['help_notic_complaint_desc']; ?></a>
                                    </nav>

                                    <a class="navbar-brand font-15" href="#item-12"><?= $lang['help_notic_ngroup']; ?></a>
                                    <nav class="nav nav-pills flex-column">
                                        <a class="nav-link font-11" href="#item-12"><?= $lang['help_notic_ngroup_desc']; ?></a>
                                    </nav>


                                </nav>
                            </div>
                            <div class="col-8">
                                <div data-bs-spy="scroll" data-bs-target="#navbar-example3"
                                     data-bs-offset="0" class="scrollspy-example"
                                     style="height: 700px;">

                                    <h4 id="item-1"><?= $lang['help_notic_login_1']; ?></h4>
                                    <p><?= $lang['help_login']; ?></p>
                                    <p><?= $lang['help_login_1']; ?></p>
                                    <p><?= $lang['help_login_2']; ?></p>
                                    <hr>
                                    <hr>
                                    <h4 id="#"><?= $lang['help_notic_login_2']; ?></h4>
                                    <p><?= $lang['help_logout']; ?></p>
                                    <p><?= $lang['help_logout_2']; ?></p>
                                    <hr>
                                    <hr>
                                    <h4 id="item-2"><?= $lang['help_notic_myaccount']; ?></h4>
                                    <p><?= $lang['help_myaccount']; ?></p>
                                    <p><?= $lang['help_myaccount_1']; ?></p>
                                    <p><?= $lang['help_permission']; ?></p>
                                    <hr>
                                    <hr>
                                    <h4 id="item-3"><?= $lang['help_notic_time']; ?></h4>
                                    <p><?= $lang['help_time']; ?></p>
                                    <p><?= $lang['help_time_2']; ?></p>


                                    <hr>
                                    <hr>
                                    <h4 id="item-4"><?= $lang['help_notic_driver']; ?></h4>
                                    <p><?= $lang['help_user_status']; ?></p>
                                    <p><?= $lang['help_user_status_guest']; ?></p>
                                    <p><?= $lang['help_user_status_guest_1']; ?></p>
                                    <p><?= $lang['help_user_status_active']; ?></p>
                                    <p><?= $lang['help_user_status_active_driver']; ?></p>
                                    <p><?= $lang['help_user_status_active_driver_1']; ?></p>
                                    <p><?= $lang['help_user_status_inactive']; ?></p>
                                    <p><?= $lang['help_user_status_inactive_1']; ?></p>
                                    <p><?= $lang['help_user_status_suspend']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_1']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_2']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_2']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_3']; ?></p>
                                    <p><?= $lang['help_auth_2']; ?></p>
                                    <hr>
                                    <p><?= $lang['help_user_lists_12']; ?></p>
                                    <p><?= $lang['help_user_lists_11']; ?></p>
                                    <p><?= $lang['help_user_lists_10']; ?></p>
                                    <p><?= $lang['help_user_lists_1']; ?></p>
                                    <p><?= $lang['help_user_lists_2']; ?></p>
                                    <p><?= $lang['help_user_lists_3']; ?></p>
                                    <p><?= $lang['help_user_lists_4']; ?></p>
                                    <p><?= $lang['help_user_lists_5']; ?></p>
                                    <p><?= $lang['help_user_lists_6']; ?></p>
                                    <p><?= $lang['help_user_lists_6_1']; ?></p>
                                    <p><?= $lang['help_user_lists_6_2']; ?></p>
                                    <p><?= $lang['help_user_lists_7']; ?></p>
                                    <p><?= $lang['help_user_lists_8']; ?></p>
                                    <p><?= $lang['help_user_lists_9']; ?></p>
                                    <hr>
                                    <p><?= $lang['help_user_pages']; ?></p>
                                    <p><?= $lang['help_user_logs']; ?></p>
                                    <p><?= $lang['help_user_notifications']; ?></p>
                                    <p><?= $lang['help_user_notifications_2']; ?></p>
                                    <p><?= $lang['help_user_ticket']; ?></p>
                                    <p><?= $lang['help_user_ticket_2']; ?></p>
                                    <p><?= $lang['help_user_ticket_3']; ?></p>
                                    <p><?= $lang['help_user_transaction_1']; ?></p>
                                    <p><?= $lang['help_user_transaction_2']; ?></p>
                                    <hr>
                                    <hr>


                                    <h4 id="item-5"><?= $lang['help_notic_businessman']; ?></h4>
                                    <p><?= $lang['help_user_status']; ?></p>
                                    <p><?= $lang['help_user_status_guest']; ?></p>
                                    <p><?= $lang['help_user_status_guest_1']; ?></p>
                                    <p><?= $lang['help_user_status_active']; ?></p>
                                    <p><?= $lang['help_user_status_active_businessman']; ?></p>
                                    <p><?= $lang['help_user_status_active_businessman_1']; ?></p>
                                    <p><?= $lang['help_user_status_inactive']; ?></p>
                                    <p><?= $lang['help_user_status_inactive_1']; ?></p>
                                    <p><?= $lang['help_user_status_suspend']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_1']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_2']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_2']; ?></p>
                                    <p><?= $lang['help_user_status_suspend_3']; ?></p>
                                    <p><?= $lang['help_auth_1']; ?></p>
                                    <hr>
                                    <p><?= $lang['help_user_lists_12']; ?></p>
                                    <p><?= $lang['help_user_lists_11']; ?></p>
                                    <p><?= $lang['help_user_lists_10']; ?></p>
                                    <p><?= $lang['help_user_lists_1']; ?></p>
                                    <p><?= $lang['help_user_lists_2']; ?></p>
                                    <p><?= $lang['help_user_lists_3']; ?></p>
                                    <p><?= $lang['help_user_lists_4']; ?></p>
                                    <p><?= $lang['help_user_lists_5']; ?></p>
                                    <p><?= $lang['help_user_lists_6']; ?></p>
                                    <p><?= $lang['help_user_lists_6_1']; ?></p>
                                    <p><?= $lang['help_user_lists_6_2']; ?></p>
                                    <p><?= $lang['help_user_lists_7']; ?></p>
                                    <p><?= $lang['help_user_lists_8']; ?></p>
                                    <p><?= $lang['help_user_lists_9']; ?></p>
                                    <hr>
                                    <p><?= $lang['help_user_pages']; ?></p>
                                    <p><?= $lang['help_user_logs']; ?></p>
                                    <p><?= $lang['help_user_notifications']; ?></p>
                                    <p><?= $lang['help_user_notifications_2']; ?></p>
                                    <p><?= $lang['help_user_ticket']; ?></p>
                                    <p><?= $lang['help_user_ticket_2']; ?></p>
                                    <p><?= $lang['help_user_ticket_3']; ?></p>
                                    <p><?= $lang['help_user_transaction_1']; ?></p>
                                    <p><?= $lang['help_user_transaction_2']; ?></p>


                                    <hr>
                                    <hr>

                                    <h4 id="item-6"><?= $lang['help_notic_cargo']; ?></h4>
                                    <p><?= $lang['help_cargo_1']; ?></p>
                                    <p><?= $lang['help_cargo_2']; ?></p>
                                    <p><?= $lang['help_cargo_3']; ?></p>
                                    <p><?= $lang['help_cargo_4']; ?></p>
                                    <p><?= $lang['help_cargo_5']; ?></p>
                                    <p><?= $lang['help_cargo_6']; ?></p>
                                    <p><?= $lang['help_cargo_7']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-7"><?= $lang['help_notic_car']; ?></h4>
                                    <p><?= $lang['help_car_1']; ?></p>
                                    <p><?= $lang['help_car_2']; ?></p>
                                    <p><?= $lang['help_car_3']; ?></p>
                                    <p><?= $lang['help_car_4']; ?></p>
                                    <p><?= $lang['help_car_5']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-8"><?= $lang['help_department']; ?></h4>
                                    <p><?= $lang['help_department_1']; ?></p>
                                    <p><?= $lang['help_department_2']; ?></p>
                                    <p><?= $lang['help_department_3']; ?></p>
                                    <p><?= $lang['help_department_4']; ?></p>
                                    <p><?= $lang['help_department_5']; ?></p>
                                    <p><?= $lang['help_department_6']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-9"><?= $lang['help_notic_car_bank']; ?></h4>
                                    <p><?= $lang['help_credit_1']; ?></p>
                                    <p><?= $lang['help_credit_2']; ?></p>
                                    <p><?= $lang['help_credit_3']; ?></p>
                                    <p><?= $lang['help_credit_4']; ?></p>
                                    <p><?= $lang['help_credit_5']; ?></p>
                                    <p><?= $lang['help_credit_6']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-10"><?= $lang['help_notic_transaction']; ?></h4>
                                    <p><?= $lang['help_transaction_1']; ?></p>
                                    <p><?= $lang['help_transaction_2']; ?></p>
                                    <p><?= $lang['help_transaction_3']; ?></p>
                                    <p><?= $lang['help_transaction_4']; ?></p>
                                    <p><?= $lang['help_transaction_5']; ?></p>
                                    <p><?= $lang['help_transaction_5_1']; ?></p>
                                    <p><?= $lang['help_transaction_6']; ?></p>
                                    <p><?= $lang['help_transaction_7']; ?></p>
                                    <p><?= $lang['help_transaction_8']; ?></p>
                                    <p><?= $lang['help_transaction_8']; ?></p>
                                    <p><?= $lang['help_transaction_10']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-11"><?= $lang['help_notic_complaint']; ?></h4>
                                    <p><?= $lang['help_complaint_1']; ?></p>
                                    <p><?= $lang['help_complaint_2']; ?></p>
                                    <p><?= $lang['help_complaint_3']; ?></p>
                                    <p><?= $lang['help_complaint_4']; ?></p>

                                    <hr>
                                    <hr>

                                    <h4 id="item-12"><?= $lang['help_notic_ngroup']; ?></h4>
                                    <p><?= $lang['help_ngroup_1']; ?></p>
                                    <p><?= $lang['help_ngroup_2']; ?></p>
                                    <p><?= $lang['help_ngroup_3']; ?></p>


                                </div>
                            </div>
                        </div>

                        <p class="text-muted font-13 mb-3">
                            <?= $lang['hope_of_success']; ?>
                        </p>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->


        <?php
        getFooter();

    } else {
        setcookie('EID', null, -1, '/');
        setcookie('UID', null, -1, '/');
        setcookie('INF', null, -1, '/');
        unset($_COOKIE['EID']);
        unset($_COOKIE['UID']);
        unset($_COOKIE['INF']);

        header('Location: ' . ADMIN_HEADER_LOCATION);
    }
} else {

    setcookie('EID', null, -1, '/');
    setcookie('UID', null, -1, '/');
    setcookie('INF', null, -1, '/');
    unset($_COOKIE['EID']);
    unset($_COOKIE['UID']);
    unset($_COOKIE['INF']);

    header('Location: ' . ADMIN_HEADER_LOCATION);

}


