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

        $resultUserInfoById = AUser::getUserInfoById($id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }

        $UserName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }

        /**
         * Get All Settings
         */
        $resultTextShareWhatsapp = Utils::getFileValue("settings.txt", "whatsapp_default_text");

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('whatsapp-user', '/dist/js/admin/share/whatsapp-user.init.js');

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

                                    <div class="text-center progress-demo my-2">
                                        <?php
                                        if (!empty($resultTextShareWhatsapp) && $resultTextShareWhatsapp != '[]') {
                                            foreach (json_decode($resultTextShareWhatsapp) as $loop) {
                                                ?>
                                                <button type="button"
                                                        data-tj-text="<?= $loop->desc; ?>"
                                                        class="btnShare btn w-sm btn-outline-info waves-effect waves-light">
                                                    <?= $loop->title; ?>
                                                </button>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="Leave a comment here"
                                                  id="xDesc"
                                                  style="height: 100px"></textarea>
                                        <label for="floatingTextarea2"><?= $lang['text_massage']; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> :
                                            <span id="length_xDesc" class="text-info">0</span>
                                        </small>
                                    </div>
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
                                <a target="_self"
                                   id="shareBtn"
                                   href="https://api.whatsapp.com/send?phone=<?= Security::decrypt($dataUserInfoById->user_mobile); ?>&text="
                                   class="btn w-sm btn-soft-primary waves-effect waves-light">
                                    <?= $lang["a_share_massage"]; ?>
                                </a>
                                <a href="/admin/users/info/<?= $id; ?>"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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