<?php
$pageSlug = "a_share_whatsapp";

// permission_can_edit

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang, $antiXSS, $Settings;

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
         * get Share Whats App Info By Id
         */
        $result = Share::getShareWhatsAppInfoById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }

        if (empty($data)) {
            header('Location: /admin/share/whatsapp');
        }


        $resultUserInfoById = AUser::getUserInfoById($data->user_id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }

        $UserName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('share-whatsapp-info', '/dist/js/admin/share/share-whatsapp-info.init.js');

        getHeader($lang['a_info_share_massage'], [
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
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_info_share_massage"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-user text-start mb-3">
                                        <img class="me-2 avatar-sm rounded-circle"
                                             src="<?= USER_AVATAR; ?>"
                                             alt="<?= $UserName; ?>">
                                        <a target="_self"
                                           class="text-body fw-normal"
                                           href="/admin/users/info/<?= $dataUserInfoById->user_id; ?>">
                                            <?= $UserName; ?>
                                        </a>
                                    </div>


                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["title"]; ?> : </span>
                                        <strong>
                                            <?= $data->wa_massage; ?>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["status"]; ?> : </span>
                                        <strong>
                                            <?php
                                            if ($data->wa_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['a_pending_check'] . "</span>";
                                            } elseif ($data->wa_status == "sended") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['a_sended'] . "</span>";
                                            } elseif ($data->wa_status == "rejected") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-secondary font-12'>" . $data->wa_status . "</span>";
                                            }
                                            ?>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["date_create"]; ?> : </span>
                                        <strong>
                                            <bdi>
                                                <?= Utils::getTimeCountry($Settings['data_time_format'], $data->wa_submit_time) ?>
                                            </bdi>
                                        </strong>
                                    </p>

                                    <?php
                                    if ($data->wa_send_time) {
                                        ?>
                                        <p class="text-muted mb-2 font-13">
                                            <span class="ms-2"><?= $lang["a_date_send"]; ?> : </span>
                                            <strong>
                                                <bdi>
                                                    <?= Utils::getTimeCountry($Settings['data_time_format'], $data->wa_send_time) ?>
                                                </bdi>
                                            </strong>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <?php
                                if ($data->wa_status == "pending") {
                                    ?>
                                    <button id="btnSend"
                                            type="button"
                                            data-tj-id="<?= $id; ?>"
                                            data-tj-status="sended"
                                            class="btnSubmit btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                            data-style="zoom-in">
                                        <?= $lang["a_sending"]; ?>
                                    </button>
                                    <button id="btnRejected"
                                            type="button"
                                            data-tj-id="<?= $id; ?>"
                                            data-tj-status="rejected"
                                            class="btnSubmit btn w-sm btn-soft-danger waves-effect shadow-none waves-light"
                                            data-style="zoom-in">
                                        <?= $lang["rejecting"]; ?>
                                    </button>
                                <?php } ?>
                                <a href="/admin/share/whatsapp"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>

                                <hr>
                                <a target="_self"
                                   href="https://api.whatsapp.com/send?phone=<?= Security::decrypt($dataUserInfoById->user_mobile); ?>&text=<?= $data->wa_massage; ?>"
                                   class="btn w-sm btn-primary waves-effect waves-light mt-1">
                                    <?= $lang["a_share_massage"]; ?>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
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

        getFooter(
            [
                $lang['help_academy_1'],
                $lang['help_academy_2'],
                $lang['help_academy_3'],
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