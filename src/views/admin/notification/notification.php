<?php
$pageSlug = "notification";
// permission_can_insert

global $lang, $antiXSS;

use MJ\Security\Security;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
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
            header('Location: /admin/notification');
        }


        $userName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $userName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('quill-core-css', '/dist/libs/quill/quill.core.css');
        enqueueStylesheet('quill-snow-css', '/dist/libs/quill/quill.snow.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('quill-js', '/dist/libs/quill/quill.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('notification', '/dist/js/admin/notification/notification.init.js');

        getHeader($lang["notification"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_insert',
        ]);
// start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">

                <div class="col-xl-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3  text-uppercase bg-light p-2">
                                <a href="<?= $dataUserInfoById->user_type . "/" . $id; ?>"
                                   target="_self"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="<?= $lang['user_info']; ?>">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                </a>
                                <?= $lang['all_info']; ?>
                            </h5>

                            <div class="d-flex align-items-start mb-3">
                                <img onclick="this.requestFullscreen()"
                                     src="<?= USER_AVATAR; ?>"
                                     class="me-2 avatar-md rounded-circle" height="42"
                                     alt="<?= $userName; ?>">
                                <div class="w-100">
                                    <h5 class="mt-0 mb-0 font-15">
                                        <a href="/admin/users/info/<?= $id; ?>"
                                           class="text-reset">
                                            <?= $userName; ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted">
                                        <?php
                                        if ($dataUserInfoById->user_type == "businessman") {
                                            echo '<i class="mdi mdi-office-building"></i> ' . $lang["businessman"];
                                        } elseif ($dataUserInfoById->user_type == "driver") {
                                            echo '<i class="mdi mdi-truck-outline"></i> ' . $lang["driver"];
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="text-center button-list">
                                    <a href="/admin/users/info/<?= $id; ?>"
                                       target="_self"
                                       class="btn btn-soft-primary btn-sm  waves-effect waves-light">
                                        <?= $lang['user_info']; ?>
                                    </a>
                                    <a href="/admin/notification"
                                       class="btn btn-soft-secondary btn-sm  waves-effect waves-light">
                                        <?= $lang['btn_back']; ?>
                                    </a>
                                </div>
                            </div>

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->

                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3  text-uppercase bg-light p-2"><i
                                        class="mdi mdi-bell-circle me-1"></i>
                                <?= $lang['notifications']; ?>
                            </h5>
                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center" id="v-pills-tab"
                                 role="tablist" aria-orientation="vertical">
                                <a class="nav-link active show py-2" id="custom-v-main-tab"
                                   data-bs-toggle="pill" href="#custom-v-main" role="tab"
                                   aria-controls="custom-v-main"
                                   aria-selected="true">
                                    <?= $lang['send_notification']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-logos-tab" data-bs-toggle="pill"
                                   href="#custom-v-logos" role="tab" aria-controls="custom-v-logos"
                                   aria-selected="false">
                                    <?= $lang['notifications_sended']; ?>
                                </a>
                            </div>
                        </div>
                    </div> <!-- end col-->
                </div>

                <div class="col-xl-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3 pt-0">
                                <!-- start send notification-->
                                <div class="tab-pane fade active show" id="custom-v-main"
                                     role="tabpanel" aria-labelledby="custom-v-main-tab">
                                    <h5 class="mb-3  text-uppercase bg-light p-2">
                                        <i class="mdi mdi-bell me-1"></i>
                                        <?= $lang['send_notification']; ?>
                                    </h5>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="noticTitle"
                                                       placeholder="<?= $lang['title']; ?>">
                                                <label for="noticTitle"><?= $lang['title']; ?></label>
                                                <small class="form-text text-muted">
                                                    <?= $lang['length_text']; ?> :
                                                    <span id="length_noticTitle" class="text-danger">0</span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="noticSenderName"
                                                       placeholder="<?= $lang['sender']; ?>">
                                                <label for="noticSenderName"><?= $lang['sender']; ?></label>
                                                <small class="form-text text-muted">
                                                    <?= $lang['length_text']; ?> :
                                                    <span id="length_noticSenderName" class="text-danger">0</span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <div id="noticSenderText" class="form-control"
                                                     style="height: 200px;"></div>
                                                <small class="form-text text-muted">
                                                    <?= $lang['text_massage']; ?> : <span
                                                            id="length_noticSenderText" class="text-danger">0</span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="text-end progress-demo">
                                                <button id="sendNotic"
                                                        data-user-id="<?= $id; ?>"
                                                        type="button"
                                                        class="btn w-sm btn-primary waves-effect shadow-none waves-light e px-4"
                                                        data-style="zoom-in">
                                                    <i class="mdi mdi-send-outline me-1  mdi-rotate-315 "></i>
                                                    <?= $lang['send']; ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- end send notification -->


                                <!-- start list notification -->
                                <div class="tab-pane fade" id="custom-v-logos" role="tabpanel"
                                     aria-labelledby="custom-v-logos-tab">
                                    <div>
                                        <h5 class="mb-3  text-uppercase bg-light p-2">
                                            <i class="mdi mdi-bell-check me-1"></i>
                                            <?= $lang['notifications_sended']; ?>
                                        </h5>
                                        <div class="table-responsive">
                                            <table id="orders-table"
                                                   data-page-length='10'
                                                   data-order='[[ 0, "desc" ]]'
                                                   data-tj-col="notification_id,user_id,notification_sender,notification_title,notification_status,notification_time"
                                                   data-tj-address="dt-notification"
                                                   data-tj-where="<?= $id; ?>"
                                                   class="table table-hover m-0 table-centered dt-responsive w-100">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?= $lang['sender']; ?></th>
                                                    <th><?= $lang['title']; ?></th>
                                                    <th><?= $lang['status']; ?></th>
                                                    <th><?= $lang['time']; ?></th>
                                                    <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- end list notification-->
                            </div>
                        </div>
                    </div>

                </div> <!-- end col -->

            </div>


            <!-- Start modal -->
            <div id="modalGroupDiv" class="modal fade" tabindex="-1" role="dialog"
                 aria-labelledby="standard-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalSenderG"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="modalTitleG"></div>
                            <hr>
                            <div id="modalDescG"></div>
                        </div>
                        <div class="modal-footer">
                            <bdi id="modalDateG"></bdi>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End modal -->
            <input type="hidden" id="tokenShow" name="tokenShow" value="<?= Security::initCSRF2() ?>">
            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-notification'] = "dt-notification-44"; ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'empty_input' => $lang['empty_input'],
                        'error' => $lang['error'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'successful' => $lang['successful'],
                        'error_mag' => $lang['error_mag'],
                        'token_error' => $lang['token_error'],
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter();

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