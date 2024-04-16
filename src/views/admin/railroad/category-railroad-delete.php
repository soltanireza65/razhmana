<?php
$pageSlug = "railroad_c";

// permission_can_delete

use MJ\Security\Security;

global $lang, $antiXSS;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_delete == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);
        /**
         * Get Airport By ID
         */
        $Result = Railroad::getRailroadInfoById($id);
        $data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $data = $Result->response[0];
        }

        if (empty($data)) {
            header('Location: /admin/category/railroad');
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('railroad-delete', '/dist/js/admin/railroad/railroad-delete.init.js');

        getHeader($lang['delete_railroad'], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_delete',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["delete_railroad"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-danger"><?= $lang['delete_railroad_desc']; ?></p>
                                    <p class="text-muted mb-2 font-16">
                                        <span class="ms-2"><?= $lang["name_railroad"]; ?> : </span>
                                        <strong id="railroad_name">
                                            <?= (!empty(array_column(json_decode($data->railroad_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                array_column(json_decode($data->railroad_name, true), 'value', 'slug')[$_COOKIE['language']] : ""; ?>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["name_city"]; ?> : </span>
                                        <strong>
                                            <?= (!empty(array_column(json_decode($data->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                array_column(json_decode($data->city_name, true), 'value', 'slug')[$_COOKIE['language']] : ""; ?>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["priority_show"]; ?> : </span>
                                        <strong>
                                            <?= $data->railroad_priority; ?>
                                        </strong>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["status"]; ?> : </span>
                                        <strong>
                                            <?php
                                            if ($data->railroad_status == "active") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                            } elseif ($data->railroad_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $data->railroad_status . "</span>";
                                            }
                                            ?>
                                        </strong>
                                    </p>
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
                                <button id="btnDelete" type="button"
                                        data-railroad-id="<?= $data->railroad_id; ?>"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["delete"]; ?>
                                </button>
                                <a href="/admin/category/railroad/edit/<?= $data->railroad_id; ?>"
                                   class="btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                   data-style="zoom-in">
                                    <?= $lang["edit_2"]; ?>
                                </a>
                                <a href="/admin/category/railroad"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
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
                        'successful_delete_mag' => $lang['successful_delete_mag'],
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