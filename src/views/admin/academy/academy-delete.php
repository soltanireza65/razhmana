<?php
$pageSlug = "academy";

// permission_can_delete

use MJ\Security\Security;
use MJ\Utils\Utils;

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
         * Get Academy By ID
         */
        $result = Academy::getAcademyByID($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }

        if (empty($data)) {
            header('Location: /admin/academy');
        }


        /**
         * Get All Languages
         */
        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('academy-delete', '/dist/js/admin/academy/academy-delete.init.js');

        getHeader($lang['delete_academy'], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["delete_academy"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-danger"><?= $lang['delete_academy_desc']; ?></p>
                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["title"]; ?> : </span>
                                        <strong>
                                            <?= $data->academy_title; ?>
                                        </strong>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["excerpt"]; ?> : </span>
                                        <strong>
                                            <?= $data->academy_excerpt; ?>
                                        </strong>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["status"]; ?> : </span>
                                        <strong>
                                            <?php
                                            if ($data->academy_status == "published") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['published'] . "</span>";
                                            } elseif ($data->academy_status == "draft") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['draft'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $data->academy_status . "</span>";
                                            }
                                            ?>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["preview"]; ?> : </span>
                                        <strong>
                                            <a target="_blank"
                                               href="<?= SITE_URL . '/academy_p/' . $data->academy_id;; ?>">
                                                <?= $lang['view']; ?>
                                            </a>
                                        </strong>
                                    </p>

                                    <p class="text-muted mb-2 font-13">
                                        <span class="ms-2"><?= $lang["language"]; ?> : </span>
                                        <strong>
                                            <?php
                                            if (!empty($dataLanguages)) {
                                                foreach ($dataLanguages as $dataLanguagesITEM) {
                                                    if ($dataLanguagesITEM->slug == $data->academy_language) {
                                                        echo $lang[$dataLanguagesITEM->name];
                                                    }

                                                }
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
                                        data-academy-id="<?= $data->academy_id; ?>"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["delete"]; ?>
                                </button>
                                <a href="/admin/academy/edit/<?= $data->academy_id; ?>"
                                   class="btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                   data-style="zoom-in">
                                    <?= $lang["edit_2"]; ?>
                                </a>
                                <a href="/admin/academy"
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