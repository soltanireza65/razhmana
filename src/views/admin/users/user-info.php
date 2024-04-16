<?php
$pageSlug = "users";
// permission_can_edit

global $lang, $antiXSS, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once getcwd() . '/views/admin/header-footer.php';

// start roles 1
$resultCheckAdminLogin = Admin::checkAdminLogin();
$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200 && !empty($resultCheckAdminLogin->response)) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {


        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }


        $flagSlug = false;
        if (!empty($dataCheckAdminRoleForCheck) && json_decode($dataCheckAdminRoleForCheck)->role_status == "active") {
            foreach (json_decode($dataCheckAdminRoleForCheck)->permissons as $item000) {
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }

        $UserNam = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserNam = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }


        /**
         * Get User Open Tickets Room By Id
         */
        $resultUserOpenTicketsByIdOpen = ATicket::getUserOpenTicketsById($id, "open");
        $dataUserOpenTicketsByIdOpen = [];
        if ($resultUserOpenTicketsByIdOpen->status == 200 && !empty($resultUserOpenTicketsByIdOpen->response)) {
            $dataUserOpenTicketsByIdOpen = $resultUserOpenTicketsByIdOpen->response;
        }

        /**
         * Get All Departments
         */
        $resultAllDepartments = ATicket::getAllDepartments("");
        $dataAllDepartments = [];
        if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
            $dataAllDepartments = $resultAllDepartments->response;
        }


        /**
         * Get All Balances
         */
        $resultAllBalances = AUser::getAllBalances($id);
        $dataAllBalances = [];
        if ($resultAllBalances->status == 200 && !empty($resultAllBalances->response)) {
            $dataAllBalances = $resultAllBalances->response;
        }


        /**
         * Get All Currencies
         */
        $resultAllCurrencies = Currency::getAllCurrencies();
        $dataAllCurrencies = [];
        if ($resultAllCurrencies->status == 200 && !empty($resultAllCurrencies->response)) {
            $dataAllCurrencies = $resultAllCurrencies->response;
        }


        /**
         * Get User Bank Card
         */
        $resultUserBankCard = AUser::getUserBankCard($id);
        $dataUserBankCard = [];
        if ($resultUserBankCard->status == 200 && !empty($resultUserBankCard->response)) {
            $dataUserBankCard = $resultUserBankCard->response;
        }


        /**
         * Get All Complaints Whit Cargo ==> From
         */
        $ResultComplaintsFrom = Complaint::getAllComplaintsWhitCargoByUserId($id, 'from');
        $DataComplaintsFrom = [];
        if ($ResultComplaintsFrom->status == 200 && !empty($ResultComplaintsFrom->response)) {
            $DataComplaintsFrom = $ResultComplaintsFrom->response;
        }

        /**
         * Get All Complaints Whit Cargo ==>To
         */
        $ResultComplaintsTo = Complaint::getAllComplaintsWhitCargoByUserId($id, 'to');
        $DataComplaintsTo = [];
        if ($ResultComplaintsTo->status == 200 && !empty($ResultComplaintsTo->response)) {
            $DataComplaintsTo = $ResultComplaintsTo->response;
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        /**
         * Get All user Options
         */
        $resultUserOptions = AUser::getUserAuthOptions($id);
        $dataUserOptions = [];
        if ($resultUserOptions->status == 200 && !empty($resultUserOptions->response)) {
            $dataUserOptions = $resultUserOptions->response;
        }

        /**
         * get All user  refferal users
         */
        $user_referals = AUser::getUserReferrals(Security::decrypt(str_replace('+', '', $dataUserInfoById->user_mobile)));
        $user_referals = $user_referals->status == 200 ? $user_referals->response : [];
        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('user-info', '/dist/js/admin/users/user-info.init.js');

        getHeader($lang["user_info"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <style>
                .nav-fill .nav-item .nav-link, .nav-justified .nav-item .nav-link {
                    /*width: 160px;*/
                    white-space: nowrap;
                }
            </style>
            <div class="row">

                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 id="UserID"
                                        data-mj-user-id="<?= $id; ?>"
                                        data-tj-user-type="<?= $dataUserInfoById->user_type; ?>"
                                        class="mb-3 mt-0 text-uppercase bg-light p-2">
                                        <i class="mdi mdi-account-circle me-1"></i>
                                        <?= $lang['all_info']; ?>
                                    </h5>

                                    <div class="d-flex align-items-start mb-3">
                                        <img class="d-flex me-3 rounded-circle avatar-lg"
                                             onclick="this.requestFullscreen()"
                                             src="<?= Utils::fileExist($dataUserInfoById->user_avatar, USER_AVATAR); ?>"
                                             alt="<?= $UserNam; ?>">

                                        <div class="w-100">
                                            <h4 class="mt-0 mb-1 <?= ($dataUserInfoById->user_class == "own") ? 'text-warning' : null ?>">
                                                <?= ($dataUserInfoById->user_class == "own") ? '<i class="mdi mdi-star"></i>' : null ?> <?= $UserNam; ?>
                                            </h4>
                                            <p class="text-muted">
                                                <?php
                                                if ($dataUserInfoById->user_type == "businessman") {
                                                    ?>
                                                    <i class="mdi mdi-office-building"></i>
                                                    <?= $lang['businessman']; ?>
                                                    <?php
                                                } elseif ($dataUserInfoById->user_type == "driver") {
                                                    ?>
                                                    <i class="mdi mdi-dump-truck"></i>
                                                    <?= $lang['driver']; ?>
                                                    <?php
                                                } elseif ($dataUserInfoById->user_type == "guest") {
                                                    ?>
                                                    <i class="mdi mdi-account-alert"></i>
                                                    <?= $lang['guest_user']; ?>
                                                <?php } else { ?>
                                                    <i class="mdi mdi-account-edit-outline"></i>
                                                    <?= $dataUserInfoById->user_type; ?>
                                                <?php } ?>
                                            </p>

                                            <a href="javascript: void(0);"
                                               id="btnActive"
                                               data-type="active"
                                               data-user-id="<?= $id; ?>"

                                               class="btnChangeUSerStatus btn btn-xs btn-soft-success  mt-1 <?= ($dataUserInfoById->user_status == "active") ? "active" : ""; ?>">
                                                <?= $lang['active']; ?>
                                            </a>

                                            <!--             <a href="javascript: void(0);"
                                               id="btnInactive"
                                               data-type="inactive"
                                               data-user-id="<?php /*= $id; */ ?>"

                                               class="btnChangeUSerStatus btn btn-xs btn-soft-secondary mt-1 <?php /*= ($dataUserInfoById->user_status == "inactive") ? "active" : ""; */ ?>">
                                                <?php /*= $lang['inactive']; */ ?>
                                            </a>


                                            <a href="javascript: void(0);"
                                               id="btnGuest"
                                               data-type="guest"
                                               data-user-id="<?php /*= $id; */ ?>"

                                               class="btnChangeUSerStatus btn btn-xs btn-soft-warning mt-1 <?php /*= ($dataUserInfoById->user_status == "guest") ? "active" : ""; */ ?>">
                                                <?php /*= $lang['guest']; */ ?>
                                            </a>-->

                                            <a href="javascript: void(0);"
                                               id="btnSuspend"
                                               data-type="suspend"
                                               data-user-id="<?= $id; ?>"

                                               class="btnChangeUSerStatus btn btn-xs btn-soft-danger mt-1 <?= ($dataUserInfoById->user_status == "suspend") ? "active" : ""; ?>">
                                                <?= $lang['to_suspend']; ?>
                                            </a>
                                            <hr>
                                            <?= $lang['a_user_class']; ?>
                                            :
                                            <?php
                                            if ($dataUserInfoById->user_class == "own") {
                                                echo $lang['u_own'];
                                            } elseif ($dataUserInfoById->user_class == "marketer") {
                                                echo $lang['u_marketer'];
                                            } else {
                                                echo $lang['u_custom'];
                                            }
                                            ?>
                                            <br>
                                            <a href="javascript: void(0);"
                                               id="userClassOwn"
                                               data-class="own"
                                               data-user-id="<?= $id; ?>"

                                               class="btnUserClassbtn btn btn-xs btn-soft-warning mt-1">
                                                <?= $lang['u_own']; ?>
                                            </a>
                                            <a href="javascript: void(0);"
                                               id="userClassNull"
                                               data-class=""
                                               data-user-id="<?= $id; ?>"

                                               class="btnUserClassbtn btn btn-xs btn-soft-info mt-1">
                                                <?= $lang['u_custom']; ?>
                                            </a>
                                            <a href="javascript: void(0);"
                                               id="userClassMarketer"
                                               data-class="marketer"
                                               data-user-id="<?= $id; ?>"

                                               class="btnUserClassbtn btn btn-xs btn-soft-info mt-1">
                                                <?= $lang['u_marketer']; ?>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div id="tooltips-container" class="text-center">
                                            <a href="tel:<?php echo Security::decrypt($dataUserInfoById->user_mobile); ?>"
                                               class="text-reset font-19 py-1 px-2 d-inline-block">
                                                <i class="fe-phone-call"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['voice_call']; ?>"></i>
                                            </a>

                                            <a href="/admin/share/user/<?= $id; ?>"
                                               target="_self"
                                               class="text-reset font-19 py-1 px-2 d-inline-block">
                                                <i class="mdi mdi-whatsapp"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['a_share_massage_whatsapp']; ?>"></i>
                                            </a>

                                            <a target="_self"
                                               href="/admin/ticket/add/<?= $dataUserInfoById->user_id; ?>"
                                               class="text-reset font-19 py-1 px-2 d-inline-block">
                                                <i class="fe-message-circle"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['create_ticket']; ?>"></i>
                                            </a>
                                            <a target="_self"
                                               href="/admin/users/notification/<?= $dataUserInfoById->user_id; ?>"
                                               class="text-reset font-19 py-1 px-2 d-inline-block">
                                                <i class="fe-bookmark" data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['send_notification']; ?>"></i>
                                            </a>

                                        </div>
                                    </div>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['phone_number']; ?> :</strong>
                                        <a class="text-dark ms-2" target="_self"
                                           href="tel:<?= Security::decrypt($dataUserInfoById->user_mobile); ?>">
                                            <bdi>
                                                <?= Security::decrypt($dataUserInfoById->user_mobile); ?>
                                            </bdi>
                                        </a>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['date_register']; ?> :</strong>
                                        <bdi class="ms-2 text-dark">
                                            <?= Utils::getTimeCountry($Settings['date_format'], $dataUserInfoById->user_register_date); ?>
                                        </bdi>
                                    </p>


                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['u_referral_code']; ?> :</strong>
                                        <bdi class="ms-2 text-dark">
                                            <bdi> +<?= $dataUserInfoById->user_referral_code; ?></bdi>
                                        </bdi>
                                    </p>
                                    <?php
                                    $admin_2_id = Security::decrypt($_COOKIE['UID']);

                                    if ($admin_2_id == 2) {
                                        ?>
                                        <p class="text-muted mb-2 font-13">
                                            <input id="user_refferal" type="text"
                                                   placeholder="<?= $lang['u_referral_code_new_placeholder'] ?>">

                                            <button class="btn btn-primary waves-effect waves-light"
                                                    id="update-user-refferal"
                                                    data-user-id="<?= $id; ?>"><?= $lang['submit'] ?></button>
                                        </p>
                                        <?php
                                    }
                                    ?>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['language_user']; ?> :</strong>
                                        <bdi class="ms-2 text-dark">
                                            <?= $lang[$dataUserInfoById->user_language]; ?>
                                        </bdi>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['status']; ?> :</strong>
                                        <?php
                                        if ($dataUserInfoById->user_status == "active") {
                                            echo '<span class="badge badge-soft-success font-12 ms-2">' . $lang['normal'] . '</span>';
                                        } elseif ($dataUserInfoById->user_status == "guest") {
                                            echo "<span class='badge badge-soft-warning font-12'>" . $lang['guest'] . "</span>";
                                        } elseif ($dataUserInfoById->user_status == "inactive") {
                                            echo "<span class='badge badge-soft-secondary font-12'>" . $lang['inactive'] . "</span>";
                                        } elseif ($dataUserInfoById->user_status == "suspend") {
                                            echo "<span class='badge badge-soft-danger font-12'>" . $lang['suspend'] . "</span>";
                                        } else {
                                            echo "<span class='badge badge-soft-pink font-12'>" . $dataUserInfoById->user_status . "</span>";
                                        }
                                        ?>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['score_user']; ?> :</strong>
                                        <bdi class="ms-2 text-dark">
                                            <?php
                                            $rateUser = $dataUserInfoById->user_rate;
                                            $ratCountUser = ($dataUserInfoById->user_rate_count != 0) ? $dataUserInfoById->user_rate_count : 1;
                                            echo Utils::getStarsByRate($rateUser / $ratCountUser);
                                            ?>
                                        </bdi>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['score']; ?> :</strong>
                                        <bdi class="ms-2 text-dark" id="scoreValueID">
                                            <?= $dataUserInfoById->user_score; ?>
                                        </bdi>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['gifts']; ?> :</strong>
                                        <bdi class="ms-2 text-dark" id="giftValueID">
                                            <?= $dataUserInfoById->user_gift; ?>
                                        </bdi>
                                    </p>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-3 mt-0 text-uppercase bg-light p-2">
                                        <i class="mdi mdi-clipboard-account"></i>
                                        <?= $lang['a_authorization']; ?>
                                    </h5>

                                    <p class="text-muted mb-2 font-13">
                                        <strong>
                                            <?= $lang['a_authorization_status']; ?>
                                            :</strong>
                                        <?php
                                        if (is_null($dataUserInfoById->user_auth_status)) {
                                            echo '<span class="badge badge-outline-info font-12 ms-2">' . $lang['a_authorization_no'] . '</span>';
                                        } elseif ($dataUserInfoById->user_auth_status == "pending") {
                                            echo '<span class="badge badge-outline-warning font-12 ms-2">' . $lang['a_authorization_pending'] . '</span>';
                                        } elseif ($dataUserInfoById->user_auth_status == "accepted") {
                                            echo '<span class="badge badge-outline-success font-12 ms-2">' . $lang['a_authorization_accepted'] . '</span>';
                                        } elseif ($dataUserInfoById->user_auth_status == "rejected") {
                                            echo '<span class="badge badge-outline-danger font-12 ms-2">' . $lang['a_authorization_rejected'] . '</span>';
                                        } else {
                                            echo '<span class="badge badge-outline-secondary font-12 ms-2">' . $dataUserInfoById->user_auth_status . '</span>';
                                        }
                                        ?>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <strong>
                                            <?= ($dataUserInfoById->user_type_card == "id-card") ? $lang['auth_id_card_number'] : $lang['auth_id_card_number']; ?>
                                            :</strong>
                                        <bdi class="ms-2 text-dark">
                                            <?= Security::decrypt($dataUserInfoById->user_number_card); ?>
                                        </bdi>
                                    </p>

                                    <?php
                                    if (!is_null($dataUserInfoById->user_auth_status)) {
                                        ?>
                                        <div class="w-100 d-flex justify-content-center mb-2">
                                            <a href="javascript: void(0);"
                                               id="btnAuthAccepted"
                                               data-tj-status="accepted"
                                               data-tj-user-id="<?= $id; ?>"

                                               class="btnChangeAuth btn btn-xs btn-soft-success mt-1 mx-1">
                                                <?= $lang['acceptedes']; ?>
                                            </a>

                                            <a href="javascript: void(0);"
                                               id="btnAuthRejected"
                                               data-tj-status="rejected"
                                               data-tj-user-id="<?= $id; ?>"

                                               class="btnChangeAuth btn btn-xs btn-soft-danger mt-1">
                                                <?= $lang['rejecting']; ?>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div> <!-- end card-->

                            <!-- شروع بخش استعلام کاربر با جیبیت-->
                            <?php
                            if (!is_null($dataUserInfoById->user_auth_status) && $dataUserInfoById->user_type_card == "id-card") {
                                ?>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3 mt-0 text-uppercase bg-light p-2">
                                            <i class="mdi mdi-circle-edit-outline"></i>
                                            <?= $lang['a_inquiry_user']; ?>
                                        </h5>

                                        <p class="text-muted mb-2 font-13">
                                            <strong><?= $lang['a_inquiry_status_user']; ?> :</strong>
                                            <?php
                                            if (is_null($dataUserInfoById->user_inquiry_status)) {
                                                echo '<span class="badge badge-outline-info font-12 ms-2">' . $lang['a_user_inquiry_no'] . '</span>';
                                            } elseif ($dataUserInfoById->user_inquiry_status == "true") {
                                                echo '<span class="badge badge-outline-success font-12 ms-2">' . $lang['a_user_inquiry_accepted'] . '</span>';
                                            } elseif ($dataUserInfoById->user_inquiry_status == "false") {
                                                echo '<span class="badge badge-outline-danger font-12 ms-2">' . $lang['a_user_inquiry_rejected'] . '</span>';
                                            } else {
                                                echo '<span class="badge badge-outline-secondary font-12 ms-2">' . $dataUserInfoById->user_inquiry_status . '</span>';
                                            }
                                            ?>
                                        </p>

                                        <p class="text-muted mb-2 font-13">
                                            <strong><?= $lang['a_user_inquiry_id_card_number']; ?> :</strong>
                                            <bdi>
                                                <?= Security::decrypt($dataUserInfoById->user_inquiry_id_card); ?>
                                            </bdi>
                                        </p>

                                        <p class="text-muted mb-2 font-13">
                                            <strong><?= $lang['a_user_inquiry_date']; ?> :</strong>
                                            <bdi class="ms-2 text-dark">
                                                <?= (!is_null($dataUserInfoById->user_inquiry_admin_time)) ? Utils::getTimeCountry($Settings['date_format'], $dataUserInfoById->user_inquiry_admin_time) : ""; ?>
                                            </bdi>
                                        </p>

                                        <div class="w-100 d-flex justify-content-center mb-2">
                                            <a href="javascript: void(0);"
                                               id="btnInquiry"
                                               data-tj-user-id="<?= $id; ?>"

                                               class="btn btn-xs btn-soft-primary mt-1">
                                                <?= $lang['a_user_inquiry_request']; ?>
                                            </a>
                                        </div>

                                    </div>
                                </div> <!-- end card-->
                                <?php
                            }
                            ?>
                            <!-- پایان بخش استعلام کاربر با جیبیت-->
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-widgets">
                                        <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase1"
                                           role="button"
                                           aria-expanded="false" aria-controls="cardCollpase1">
                                            <i class="mdi mdi-minus"></i></a>
                                    </div>
                                    <h5 class="mb-3 mt-0 text-uppercase bg-light p-2"><i
                                            class="mdi mdi-cog-outline me-1"></i>
                                        <?= $lang['action']; ?>
                                    </h5>
                                    <div class="collapsed collapse" id="cardCollpase1">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <tbody>
                                                <tr>
                                                    <td><?= $lang['user_log']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/log/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['notifications']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/notification/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['tickets']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/ticket/user/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['transactions']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/transaction/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['requests_out']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/request/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['requests_in']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/request-in/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['cargoes_out']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/cargo/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['cargoes_in']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/cargo-in/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['cars']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/users/car/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang['a_poster']; ?> :</td>
                                                    <td>
                                                        <a class="action-icon ms-2"
                                                           target="_self"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show']; ?>"
                                                           href="/admin/poster/user/<?= $dataUserInfoById->user_id; ?>">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-->
                        </div>

                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-8">
                    <div class="card">
                        <div class="card-body">

                            <form>
                                <div id="basicwizard">
                                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                        <li class="nav-item">
                                            <a href="#basictab1" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2 active">
                                                <i class="mdi mdi-message-alert-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['ticket_open']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab3" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-gift-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['gift_score']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item ">
                                            <a href="#basictab4" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-two-factor-authentication me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['security']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab5" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-currency-usd me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['balance']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab6" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-two-factor-authentication me-1"></i>
                                                <span
                                                    class="d-none d-sm-inline"><?= $lang['a_authorization_all']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab7" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-credit-card-multiple-outline me-1"></i>
                                                <span
                                                    class="d-none d-sm-inline"><?= $lang['list_cards_banks']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab8" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['complainer']; ?></span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#basictab9" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-alert-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['list_accuseds']; ?></span>
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content b-0 mb-0 pt-0">

                                        <div class="tab-pane active" id="basictab1">
                                            <div class="row">
                                                <div class="col-12">
                                                    <?php
                                                    if (!empty($dataUserOpenTicketsByIdOpen)) {
                                                        foreach ($dataUserOpenTicketsByIdOpen as $dataUserOpenTicketsByIdOpenITEM) {
                                                            ?>
                                                            <div class="alert alert-warning">
                                                                <a target="_self"
                                                                   href="/admin/ticket/open/<?= $dataUserOpenTicketsByIdOpenITEM->ticket_id; ?>"
                                                                   class="text-body">
                                                                    <div class="d-flex align-items-start p-2">
                                                                        <img src="<?= USER_AVATAR; ?>"
                                                                             class="me-2 rounded-circle" height="42"
                                                                             width="42"
                                                                             alt="<?= $UserNam; ?>">
                                                                        <div class="w-100">
                                                                            <h5 class="mt-0 mb-0 font-14">
                                                                                <span
                                                                                    class="float-end text-muted fw-normal font-12">
                                                                                    <bdi>
                                                                                        <?= Utils::getTimeCountry($Settings['data_time_format'], $dataUserOpenTicketsByIdOpenITEM->ticket_submit_date); ?>
                                                                                    </bdi>
                                                                                </span>
                                                                                <?= $dataUserOpenTicketsByIdOpenITEM->ticket_title; ?>
                                                                            </h5>
                                                                            <p class="mt-1 mb-0 text-muted font-14">
                                                                            <span class="w-25 float-end text-end">
                                                                                <span
                                                                                    class="badge badge-outline-danger"><?= $lang['ticket_open']; ?></span>
                                                                            </span>
                                                                                <span class="w-75">
                                                                                    <?php
                                                                                    if (!empty($dataAllDepartments)) {
                                                                                        foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                                                            if ($dataAllDepartmentsITEM->department_id == $dataUserOpenTicketsByIdOpenITEM->department_id) {
                                                                                                echo (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                                    array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : $dataUserOpenTicketsByIdOpenITEM->department_id;
                                                                                                break;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                </span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <p class="text-center">
                                                            <img src="<?= BOX_EMPTY; ?>"
                                                                 style="width: 100%;max-width: fit-content;"
                                                                 alt="<?= $lang['no_massages']; ?>">
                                                        </p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <!-- end col -->
                                            </div> <!-- end row -->
                                        </div>


                                        <div class="tab-pane" id="basictab3">
                                            <div class="row">
                                                <h5 class="mb-2 text-uppercase bg-light p-2">
                                                    <?= $lang['add_remove_gifts']; ?>
                                                </h5>
                                            </div>
                                            <div class="row bg-light">
                                                <div class="col-lg-12 my-2">
                                                    <label class="form-label"
                                                           for="giftTitle"><?= $lang['title']; ?></label>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="giftTitle"
                                                           placeholder="<?= $lang['title']; ?>">
                                                    <small class="form-text text-muted">
                                                        <?= $lang['length_text']; ?> :
                                                        <span id="giftTitle_text"
                                                              class="text-info">
                                                          0
                                                        </span>
                                                    </small>

                                                </div>
                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1">

                                                    <label class="form-label"><?= $lang['numerical_value']; ?></label>
                                                    <input type="number"
                                                           min="0"
                                                           class="form-control"
                                                           id="giftCount"
                                                           placeholder="<?= $lang['numerical_value']; ?>">
                                                    <small
                                                        class="form-text text-muted"><?= $lang['numerical_value_desc']; ?></small>
                                                </div>


                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1">
                                                    <label for="giftAction"
                                                           class="form-label"><?= $lang['action']; ?></label>
                                                    <select id="giftAction" class="form-select">
                                                        <option value="add"><?= $lang['increase']; ?></option>
                                                        <option value="low"><?= $lang['Low_off']; ?></option>
                                                    </select>

                                                </div>

                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1 mb-3">
                                                    <button type="button"
                                                            id="giftSubmit"
                                                            data-mj-user-id="<?= $id; ?>"

                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>

                                                </div>
                                                <!-- end col -->
                                            </div>


                                            <div class="row">
                                                <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                    <?= $lang['add_remove_score']; ?>
                                                </h5>
                                            </div>
                                            <div class="row bg-light">
                                                <div class="col-lg-12 my-1">
                                                    <label class="form-label"
                                                           for="scoreTitle"><?= $lang['title']; ?></label>
                                                    <input type="text" class="form-control" id="scoreTitle"
                                                           placeholder="<?= $lang['title']; ?>">
                                                    <small class="form-text text-muted">
                                                        <?= $lang['length_text']; ?> :
                                                        <span id="scoreTitle_text" class="text-info">
                                                          0
                                                        </span>
                                                    </small>

                                                </div>
                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1">

                                                    <label class="form-label"
                                                           for="scoreCount"><?= $lang['numerical_value']; ?></label>
                                                    <input type="number" min="0" class="form-control"
                                                           id="scoreCount"
                                                           placeholder="<?= $lang['numerical_value']; ?>">
                                                    <small
                                                        class="form-text text-muted"><?= $lang['numerical_value_desc']; ?></small>
                                                </div>


                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1">
                                                    <label for="scoreAction"
                                                           class="form-label"><?= $lang['action']; ?></label>
                                                    <select id="scoreAction" class="form-select">
                                                        <option value="add"><?= $lang['increase']; ?></option>
                                                        <option value="low"><?= $lang['Low_off']; ?></option>
                                                    </select>

                                                </div>

                                                <div class="col-sm-12 col-md-12 col-lg-6  my-1">
                                                    <button type="button"
                                                            id="scoreSubmit"
                                                            data-mj-user-id="<?= $id; ?>"

                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>

                                                </div>
                                                <!-- end col -->
                                            </div>

                                        </div>


                                        <div class="tab-pane" id="basictab4">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['active_session']; ?>
                                            </h5>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th><?= $lang['ip']; ?></th>
                                                                <th><?= $lang['location']; ?></th>
                                                                <th><?= $lang['device']; ?></th>
                                                                <th><?= $lang['time_submit']; ?></th>
                                                                <th><?= $lang['time_expire']; ?></th>
                                                                <th><?= $lang['action']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $flagTableEmptySessions = true;
                                                            if (!empty($dataUserInfoById->user_active_session)) {
                                                                $jsonS = json_decode($dataUserInfoById->user_active_session);
                                                                if (!empty($jsonS)) {
                                                                    $i = 1;
                                                                    foreach ($jsonS as $jsonSITEM) {
                                                                        $flagTableEmptySessions = false;
                                                                        ?>
                                                                        <tr>
                                                                            <th><?= $i; ?></th>
                                                                            <td><?= $jsonSITEM->ip; ?></td>
                                                                            <td><?= $jsonSITEM->location; ?></td>
                                                                            <td><?= $jsonSITEM->device . " - " . $jsonSITEM->os . " - " . $jsonSITEM->browser; ?></td>
                                                                            <td>
                                                                                <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $jsonSITEM->time); ?></bdi>
                                                                            </td>
                                                                            <td>
                                                                                <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $jsonSITEM->expire); ?></bdi>
                                                                            </td>
                                                                            <td>
                                                                                <a href="javascript:void(0);"
                                                                                   data-id-user="<?= $id; ?>"
                                                                                   data-number="<?= $i++; ?>"
                                                                                   data-expire="<?= $jsonSITEM->expire; ?>"
                                                                                   class="action-icon deleteSession">
                                                                                    <i class="mdi mdi-delete"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                            if ($flagTableEmptySessions) {
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center"
                                                                        colspan="7"><?= $lang['empty_active_session_user']; ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- end col -->
                                            </div>

                                        </div>


                                        <div class="tab-pane" id="basictab5">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['balance']; ?>
                                            </h5>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th><?= $lang['currency_type']; ?></th>
                                                                <th><?= $lang['balance_value']; ?></th>
                                                                <th><?= $lang['balance_frozen']; ?></th>
                                                                <th><?= $lang['balance_in_withdraw']; ?></th>
                                                                <!--<th>< ?= $lang['action']; ?></th>-->
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $arrayTemp = [];
                                                            $flagBalance = true;
                                                            if (!empty($dataAllBalances)) {
                                                                $i = 1;
                                                                $dataAllBalances = array_reverse($dataAllBalances);
                                                                foreach ($dataAllBalances as $dataAllBalancesITEM) {
                                                                    $flagBalance = false;
                                                                    ?>
                                                                    <tr>
                                                                        <td><?= $i++; ?></td>
                                                                        <td>
                                                                            <?php
                                                                            if (!empty($dataAllCurrencies)) {
                                                                                foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                                    if ($dataAllCurrenciesITEM->currency_id == $dataAllBalancesITEM->currency_id) {

                                                                                        $name = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                            array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                                        if (isset($arrayTemp[$dataAllCurrenciesITEM->currency_id]) && isset($arrayTemp[$dataAllCurrenciesITEM->currency_id]['name'])) {
                                                                                            $arrayTemp[$dataAllCurrenciesITEM->currency_id]['value'] += $dataAllBalancesITEM->balance_value;
                                                                                            $arrayTemp[$dataAllCurrenciesITEM->currency_id]['name'] = $name;
                                                                                        } else {
                                                                                            $arrayTemp[$dataAllCurrenciesITEM->currency_id]['value'] = $dataAllBalancesITEM->balance_value;
                                                                                            $arrayTemp[$dataAllCurrenciesITEM->currency_id]['name'] = $name;
                                                                                        }
                                                                                        echo $name;
                                                                                    }
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?= $dataAllBalancesITEM->balance_value; ?></td>
                                                                        <td><?= $dataAllBalancesITEM->balance_frozen; ?></td>
                                                                        <td><?= $dataAllBalancesITEM->balance_in_withdraw; ?></td>
                                                                        <!--<td>
                                                                            <a href="/admin/users/currency/edit/< ?= $dataAllBalancesITEM->balance_id; ?>"
                                                                               target="_self"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="< ?= $lang['currency_edit']; ?>"
                                                                               class="action-icon">
                                                                                <i class="mdi mdi-square-edit-outline"></i>
                                                                            </a>
                                                                        </td>-->
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            if ($flagBalance) {
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center"
                                                                        colspan="5"><?= $lang['table_empty']; ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- end col -->
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="basictab6">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['authentication']; ?>
                                            </h5>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <tbody>
                                                            <?php $t = 1;
                                                            if ($dataUserInfoById->user_type == "businessman") {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_title_company']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'company');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['company'])) ? $dataUserOptions['company']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="company"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['company'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="company"
                                                                                    data-tj-status="accepted"
                                                                                    id="auth<?= $t++; ?>"
                                                                                    data-tj-value="no"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="company"
                                                                                    data-tj-status="rejected"
                                                                                    id="auth<?= $t++; ?>"
                                                                                    data-tj-value="no"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['company']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['company']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_manager_company']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'manager');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['manager'])) ? $dataUserOptions['manager']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="manager"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['manager'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="manager"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="manager"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['manager']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['manager']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_address']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'address');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['address'])) ? $dataUserOptions['address']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="address"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['address'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="address"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="address"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['address']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['address']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_phone']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'phone');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['phone'])) ? $dataUserOptions['phone']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="phone"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['phone'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="phone"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="phone"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['phone']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['phone']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_fox']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'fox');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['fox'])) ? $dataUserOptions['fox']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="fox"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['fox'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="fox"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="fox"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['fox']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['fox']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_mail']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'mail');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['mail'])) ? $dataUserOptions['mail']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="mail"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['mail'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="mail"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="mail"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['mail']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['mail']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_site']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'site');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['site'])) ? $dataUserOptions['site']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="site"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['site'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="site"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="site"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['site']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['site']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_id_card']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'id-card-image');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="file"
                                                                                   class="form-control valueClass">

                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="id-card-image"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['id-card-image'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="id-card-image"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="id-card-image"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['id-card-image']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['id-card-image']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <a target="_self"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $trans; ?>"
                                                                                   href="<?= (isset($dataUserOptions['id-card-image'])) ? $dataUserOptions['id-card-image']->option_value : ''; ?>"
                                                                                   class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_passport_image']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'passport-image');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="file"
                                                                                   class="form-control valueClass">

                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="passport-image"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['passport-image'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="passport-image"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="passport-image"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['passport-image']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['passport-image']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <a target="_self"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $trans; ?>"
                                                                                   href="<?= (isset($dataUserOptions['passport-image'])) ? $dataUserOptions['passport-image']->option_value : '#'; ?>"
                                                                                   class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            } elseif ($dataUserInfoById->user_type == "driver") {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_birthday_city']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'birthday-city');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['birthday-city'])) ? $dataUserOptions['birthday-city']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="birthday-city"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['birthday-city'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="birthday-city"
                                                                                    data-tj-status="accepted"
                                                                                    id="auth<?= $t++; ?>"
                                                                                    data-tj-value="no"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="birthday-city"
                                                                                    data-tj-status="rejected"
                                                                                    id="auth<?= $t++; ?>"
                                                                                    data-tj-value="no"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['birthday-city']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['birthday-city']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td><?= $lang['auth_birthday_date']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'birthday-date');
                                                                        ?>
                                                                    </td>

                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['birthday-date'])) ? $dataUserOptions['birthday-date']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="birthday-date"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['birthday-date'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="birthday-date"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="birthday-date"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['birthday-date']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['birthday-date']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_phone_national']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'phone-national');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['phone-national'])) ? $dataUserOptions['phone-national']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="phone-national"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['phone-national'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="phone-national"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="phone-national"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['phone-national']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['phone-national']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_address']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'address');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['address'])) ? $dataUserOptions['address']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="address"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['address'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="address"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="address"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['address']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['address']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_phone']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'phone');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['phone'])) ? $dataUserOptions['phone']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="phone"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['phone'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="phone"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="phone"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['phone']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['phone']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_insurance_type']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'insurance-type');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['insurance-type'])) ? $dataUserOptions['insurance-type']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="insurance-type"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['insurance-type'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="insurance-type"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="insurance-type"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['insurance-type']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['insurance-type']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_insurance_number']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'insurance-number');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text"
                                                                                   class="form-control valueClass"
                                                                                   value="<?= (isset($dataUserOptions['insurance-number'])) ? $dataUserOptions['insurance-number']->option_value : ''; ?>">
                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="insurance-number"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['insurance-number'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="insurance-number"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="insurance-number"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['insurance-number']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['insurance-number']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <button type="button"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="<?= $trans; ?>"
                                                                                        class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_id_card']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'id-card-image');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="file"
                                                                                   class="form-control valueClass">

                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="id-card-image"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['id-card-image'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="id-card-image"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="id-card-image"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['id-card-image']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['id-card-image']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <a target="_self"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $trans; ?>"
                                                                                   href="<?= (isset($dataUserOptions['id-card-image'])) ? $dataUserOptions['id-card-image']->option_value : ''; ?>"
                                                                                   class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_passport_image']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'passport-image');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="file"
                                                                                   class="form-control valueClass">

                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="passport-image"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['passport-image'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="passport-image"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="passport-image"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['passport-image']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['passport-image']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <a target="_self"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $trans; ?>"
                                                                                   href="<?= (isset($dataUserOptions['passport-image'])) ? $dataUserOptions['passport-image']->option_value : '#'; ?>"
                                                                                   class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>
                                                                        <?= $lang['auth_car_card']; ?> :
                                                                        <?php
                                                                        generateStatusAuthentication($id, 'car-card-image');
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="file"
                                                                                   class="form-control valueClass">

                                                                            <button
                                                                                class="btn submit_change_authentication input-group-text btn-soft-primary waves-effect waves-light"
                                                                                data-tj-option-slug="car-card-image"
                                                                                data-tj-status="accepted"
                                                                                data-tj-value="yes"
                                                                                id="auth<?= $t++; ?>"

                                                                                type="button">
                                                                                <?= $lang['submit_change']; ?>
                                                                            </button>
                                                                            <?php if (isset($dataUserOptions['car-card-image'])) { ?>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-success waves-effect waves-light"
                                                                                    data-tj-option-slug="car-card-image"
                                                                                    data-tj-status="accepted"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['acceptedes']; ?>
                                                                                </button>
                                                                                <button
                                                                                    class="btn submit_change_authentication input-group-text btn-soft-danger waves-effect waves-light"
                                                                                    data-tj-option-slug="car-card-image"
                                                                                    data-tj-status="rejected"
                                                                                    data-tj-value="no"
                                                                                    id="auth<?= $t++; ?>"

                                                                                    type="button">
                                                                                    <?= $lang['rejecting']; ?>
                                                                                </button>
                                                                                <?php
                                                                                if ($dataUserOptions['car-card-image']->option_status == "rejected") {
                                                                                    $color = "danger";
                                                                                    $icon = "mdi-cancel";
                                                                                    $trans = $lang['reject'];
                                                                                } elseif ($dataUserOptions['car-card-image']->option_status == "accepted") {
                                                                                    $color = "success";
                                                                                    $icon = "mdi-check-bold";
                                                                                    $trans = $lang['a_accepted'];
                                                                                } else {
                                                                                    $color = "warning";
                                                                                    $icon = 'mdi-circle-outline';
                                                                                    $trans = $lang['a_pending_check'];
                                                                                }
                                                                                ?>
                                                                                <a target="_self"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $trans; ?>"
                                                                                   href="<?= (isset($dataUserOptions['car-card-image'])) ? $dataUserOptions['car-card-image']->option_value : ''; ?>"
                                                                                   class="btn input-group-text btn-outline-<?= $color; ?> waves-effect waves-light">
                                                                                    <i class="mdi <?= $icon; ?>"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            } elseif ($dataUserInfoById->user_type == "guest") {

                                                            } else {

                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- end table-responsive -->
                                                </div> <!-- end col -->
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="basictab7">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['list_cards_banks']; ?>
                                            </h5>
                                            <div class="row">
                                                <?php
                                                $flagCredit = true;
                                                if (!empty($dataUserBankCard)) {
                                                    foreach ($dataUserBankCard as $dataUserBankCardITEM) {
                                                        $flagCredit = false;
                                                        if ($dataUserBankCardITEM->card_status == "accepted") {
                                                            $cardStatus = $lang['accepted'];
                                                            $cardStatusColor = 'success';
                                                        } elseif ($dataUserBankCardITEM->card_status == "pending") {
                                                            $cardStatus = $lang['pending'];
                                                            $cardStatusColor = 'warning';
                                                        } elseif ($dataUserBankCardITEM->card_status == "deleted") {
                                                            $cardStatus = $lang['deleted'];
                                                            $cardStatusColor = 'secondary';
                                                        } elseif ($dataUserBankCardITEM->card_status == "rejected") {
                                                            $cardStatus = $lang['rejected'];
                                                            $cardStatusColor = 'danger';
                                                        } else {
                                                            $cardStatus = $dataUserBankCardITEM->card_status;
                                                            $cardStatusColor = 'pink';
                                                        }
                                                        ?>
                                                        <div class="col-md-6">
                                                            <div
                                                                class="card mb-0 mt-3 border border-<?= $cardStatusColor; ?>">
                                                                <div class="card-body">
                                                                    <span
                                                                        class="badge badge-soft-<?= $cardStatusColor; ?> float-end"><?= $cardStatus; ?></span>
                                                                    <h5 class="mt-0">
                                                                        <a href="/admin/credit/<?= $dataUserBankCardITEM->card_id; ?>"
                                                                           target="_self"
                                                                           data-bs-toggle="tooltip"
                                                                           data-bs-placement="top"
                                                                           title="<?= $lang['show_detail']; ?>"
                                                                           class="text-<?= $cardStatusColor; ?>">
                                                                            <?= $dataUserBankCardITEM->card_bank; ?>
                                                                        </a>
                                                                    </h5>

                                                                    <p><?= $lang['card_iban'] . " : " . $dataUserBankCardITEM->card_number; ?></p>
                                                                    <p><?= $lang['card_account'] . " : " . $dataUserBankCardITEM->card_account; ?></p>
                                                                    <div class="clearfix"></div>
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <a href="/admin/users/<?= $dataUserBankCardITEM->user_id; ?>"
                                                                               class="text-reset">
                                                                                <img src="<?= USER_AVATAR; ?>"
                                                                                     alt="<?= $UserNam; ?>"
                                                                                     class="avatar-sm img-thumbnail rounded-circle">
                                                                                <span
                                                                                    class="d-none d-md-inline-block ms-1 fw-semibold"><?= $UserNam; ?></span>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <div class="text-end text-muted">
                                                                                <p class="font-13 mt-2 mb-0">
                                                                                    <i class="mdi mdi-calendar"></i>
                                                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataUserBankCardITEM->card_time); ?></bdi>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> <!-- end card-body-->
                                                            </div> <!-- end card-->
                                                        </div>
                                                        <!-- end col -->
                                                        <?php
                                                    }
                                                }
                                                if ($flagCredit) {
                                                    ?>
                                                    <div class="col-12">
                                                        <div class="alert alert-danger" role="alert">
                                                            <i class="mdi mdi-block-helper me-2"></i>
                                                            <?= $lang['no_card_bank_submit']; ?>
                                                        </div>
                                                    </div>

                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="basictab8">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['list_complaints']; ?>
                                            </h5>
                                            <div class="row">

                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table id="orders-table1" data-page-length='10'
                                                               data-order='[[ 0, "desc" ]]'
                                                               class="table table-hover m-0 table-centered dt-responsive w-100">
                                                            <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th><?= $lang['cargo_name']; ?></th>
                                                                <th><?= $lang['admin_answer']; ?></th>
                                                                <th><?= $lang['date']; ?></th>
                                                                <th><?= $lang['status']; ?></th>
                                                                <th><?= $lang['action']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            if (!empty($DataComplaintsFrom)) {
                                                                $cargoName = 'cargo_name_' . $_COOKIE['language'];
                                                                $i = 1;
                                                                $DataComplaintsFrom = array_reverse($DataComplaintsFrom);
                                                                foreach ($DataComplaintsFrom as $DataITEM) {
                                                                    ?>
                                                                    <tr class="<?= (is_null($DataITEM->admin_id)) ? 'bg-soft-danger' : ''; ?>">
                                                                        <td><?= $i++; ?></td>
                                                                        <td><?= $DataITEM->$cargoName; ?></td>
                                                                        <td class="table-user text-start">
                                                                            <?php
                                                                            if (!empty($dataAllAdmins) && !is_null($DataITEM->admin_id)) {
                                                                                foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                                                    if ($dataAllAdminsITEM->admin_id == $DataITEM->admin_id) {
                                                                                        ?>
                                                                                        <img
                                                                                            src="<?= Utils::fileExist($dataAllAdminsITEM->admin_avatar, USER_AVATAR); ?>"
                                                                                            alt="<?= Security::decrypt($dataAllAdminsITEM->admin_nickname);; ?>"
                                                                                            class="me-2 rounded-circle">
                                                                                        <?= $dataAllAdminsITEM->admin_nickname; ?>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                ?>
                                                                                <img src="<?= USER_AVATAR; ?>"
                                                                                     alt="<?= $lang['admin']; ?>"
                                                                                     class="me-2 rounded-circle">
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $DataITEM->complaint_date); ?></bdi>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if ($DataITEM->complaint_status == "accepted") {
                                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['now_answer'] . "</span>";
                                                                            } elseif ($DataITEM->complaint_status == "pending") {
                                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['pending_answer'] . "</span>";
                                                                            } elseif ($DataITEM->complaint_status == "closed") {
                                                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['closed'] . "</span>";
                                                                            } else {
                                                                                echo "<span class='badge badge-soft-pink font-12'>" . $DataITEM->complaint_status . "</span>";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <a href="/admin/complaint/<?= $DataITEM->complaint_id; ?>"
                                                                               target="_self"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['all_info']; ?>"
                                                                               class="action-icon">
                                                                                <i class="mdi mdi-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="basictab9">
                                            <h5 class="mb-2 mt-3 text-uppercase bg-light p-2">
                                                <?= $lang['list_accuseds']; ?>
                                            </h5>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive">
                                                        <table id="orders-table2" data-page-length='10'
                                                               data-order='[[ 0, "desc" ]]'
                                                               class="table table-hover m-0 table-centered dt-responsive w-100">
                                                            <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th><?= $lang['cargo_name']; ?></th>
                                                                <th><?= $lang['admin_answer']; ?></th>
                                                                <th><?= $lang['date']; ?></th>
                                                                <th><?= $lang['status']; ?></th>
                                                                <th><?= $lang['action']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            if (!empty($DataComplaintsTo)) {
                                                                $cargoName = 'cargo_name_' . $_COOKIE['language'];
                                                                $i = 1;
                                                                $DataComplaintsTo = array_reverse($DataComplaintsTo);
                                                                foreach ($DataComplaintsTo as $DataITEM) {
                                                                    ?>
                                                                    <tr class="<?= (is_null($DataITEM->admin_id)) ? 'bg-soft-danger' : ''; ?>">
                                                                        <td><?= $i++; ?></td>
                                                                        <td><?= $DataITEM->$cargoName; ?></td>
                                                                        <td class="table-user text-start">
                                                                            <?php
                                                                            if (!empty($dataAllAdmins) && !is_null($DataITEM->admin_id)) {
                                                                                foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                                                    if ($dataAllAdminsITEM->admin_id == $DataITEM->admin_id) {
                                                                                        ?>
                                                                                        <img
                                                                                            src="<?= Utils::fileExist($dataAllAdminsITEM->admin_avatar, USER_AVATAR); ?>"
                                                                                            alt="<?= Security::decrypt($dataAllAdminsITEM->admin_nickname);; ?>"
                                                                                            class="me-2 rounded-circle">
                                                                                        <?= $dataAllAdminsITEM->admin_nickname; ?>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                ?>
                                                                                <img src="<?= USER_AVATAR; ?>"
                                                                                     alt="<?= $lang['admin']; ?>"
                                                                                     class="me-2 rounded-circle">
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $DataITEM->complaint_date); ?></bdi>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if ($DataITEM->complaint_status == "accepted") {
                                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['now_answer'] . "</span>";
                                                                            } elseif ($DataITEM->complaint_status == "pending") {
                                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['pending_answer'] . "</span>";
                                                                            } elseif ($DataITEM->complaint_status == "closed") {
                                                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['closed'] . "</span>";
                                                                            } else {
                                                                                echo "<span class='badge badge-soft-pink font-12'>" . $DataITEM->complaint_status . "</span>";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <a href="/admin/complaint/<?= $DataITEM->complaint_id; ?>"
                                                                               target="_self"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['all_info']; ?>"
                                                                               class="action-icon">
                                                                                <i class="mdi mdi-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div> <!-- tab-content -->
                                </div> <!-- end #basicwizard-->
                            </form>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header collapsed" id="headingOne">
                                        <h5 class="mb-2 mt-3 p-2" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            <?= $lang['user_referals']; ?>
                                            <i class="mdi mdi-eye"></i>
                                        </h5>

                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse"
                                         aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">

                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?= $lang['user_mobile']; ?></th>
                                                        <th><?= $lang['name_and_family']; ?></th>
                                                        <th><?= $lang['action']; ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $flagTableEmptyReferals = true;
                                                    if (!empty($user_referals)) {


                                                        $i = 1;
                                                        foreach ($user_referals as $item) {
                                                            $flagTableEmptyReferals = false;
                                                            ?>
                                                            <tr>
                                                                <th><?= $i; ?></th>
                                                                <td>
                                                                    <bdi><?= Security::decrypt($item->user_mobile); ?></bdi>
                                                                </td>
                                                                <td><?= Security::decrypt($item->user_firstname) ?> <?= Security::decrypt($item->user_lastname) ?></td>
                                                                <td>
                                                                    <a href="/admin/users/info/<?= $item->user_id; ?>"
                                                                       target="_blank"
                                                                       data-bs-toggle="tooltip"
                                                                       data-bs-placement="top"
                                                                       title="<?= $lang['all_info']; ?>"
                                                                       class="action-icon">
                                                                        <i class="mdi mdi-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }

                                                    }
                                                    if ($flagTableEmptyReferals) {
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"
                                                                colspan="4"><?= $lang['empty_user_referals']; ?></td>
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
                </div>

            </div>

            <input type="hidden" id="token2" name="token2" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'ticket_close' => $lang['ticket_close'],
                        'ticket_open' => $lang['ticket_open'],
                        'inactive' => $lang['inactive'],
                        'active' => $lang['active'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
                        'successful' => $lang['successful'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'token_error' => $lang['token_error'],
                        'upload_error' => $lang['upload_error'],
                        'empty_input' => $lang['empty_input'],
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>


            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_user_status'],
                $lang['help_user_lists_10'],
                $lang['help_user_lists_11'],
                $lang['help_user_lists_12'],
                $lang['help_user_lists_1'],
                $lang['help_user_lists_2'],
                $lang['help_user_lists_3'],
                $lang['help_user_lists_4'],
                $lang['help_user_lists_5'],
                $lang['help_user_lists_6'],
                $lang['help_user_lists_6_1'],
                $lang['help_user_lists_6_2'],
                $lang['help_user_lists_7'],
                $lang['help_user_lists_8'],
                $lang['help_user_lists_9'],
            ]
        );


        // start roles 4
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
// end roles 4


function generateStatusAuthentication($user_id, $option)
{
    global $lang;
    $resultUserOptions = AUser::getUserAuthOptions($user_id);
    $dataUserOptions = [];
    if ($resultUserOptions->status == 200 && !empty($resultUserOptions->response)) {
        $dataUserOptions = $resultUserOptions->response;
    }
    if (isset($dataUserOptions[$option])) { ?>
        <?php
        if ($dataUserOptions[$option]->option_status == "rejected") {
            ?>
            <div
                class="mj-admin-auth-status rejected">
                <?= $lang['rejected'] ?>
            </div>
            <?php
        } elseif ($dataUserOptions[$option]->option_status == "accepted") {
            ?>
            <div
                class="mj-admin-auth-status accepted">
                <?= $lang['accepted'] ?>
            </div>
            <?php
        } else {
            ?>
            <div
                class="mj-admin-auth-status pending">
                <?= $lang['pending'] ?>
            </div>
            <?php
        }
        ?>
        <?php
    }
}