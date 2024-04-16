<?php
$pageSlug = "academy_c";

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
         * Get Category Info By ID
         */

        $result = Academy::getCategoryById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/category/academy');
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
         * Get All Languages
         */
        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }


        /**
         * Get All Academy Categories
         */
        $resultAllCategories = Academy::getAllCategories('active');
        $dataCategories = [];
        if ($resultAllCategories->status == 200 && !empty($resultAllCategories->response)) {
            $dataCategories = $resultAllCategories->response;
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('category-academy-edit', '/dist/js/admin/academy/category-academy-edit.init.js');

        getHeader($lang["edit_academy_category"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["edit_academy_category"]; ?></h5>
                            <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="titleCategory"
                                               data-id="<?= $data->category_id; ?>"
                                               value="<?= $data->category_name; ?>"
                                               placeholder="<?= $lang["title_category"]; ?>">
                                        <label for="titleCategory"><?= $lang["title_category"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_titleCategory">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="number"
                                               id="xPriority"
                                               class="form-control"
                                               value="<?= $data->category_priority; ?>"
                                               placeholder="<?= $lang["priority_show"]; ?>">
                                        <label for="xPriority"><?= $lang["priority_show"]; ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase2"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["seo_setting"]; ?></h5>
                            <div class="row collapse" id="cardCollpase2">
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="<?= $lang['a_meta_title']; ?>"
                                                  id="xMetaTitle"
                                                  style="height: 100px"><?= $data->category_meta_title; ?></textarea>
                                        <label for="xMetaTitle"><?= $lang['a_meta_title']; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_xMetaTitle">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="<?= $lang['a_meta_desc']; ?>"
                                                  id="xMetaDesc"
                                                  style="height: 100px"><?= $data->category_meta_desc; ?></textarea>
                                        <label for="xMetaDesc"><?= $lang['a_meta_desc']; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_xMetaDesc">0</span>
                                        </small>
                                    </div>
                                </div>


                                <div class="col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="<?= $lang['a_schema']; ?>"
                                                  id="xSchema"
                                                  style="height: 100px"><?= $data->category_schema; ?></textarea>
                                        <label for="xSchema"><?= $lang['a_schema']; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_xSchema">0</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase1"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" id="cardCollpase1"
                                 style="max-height: 176px;overflow: auto;">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td scope="row"><?= $lang["status"]; ?> :</td>
                                        <td></td>
                                        <td><?php
                                            if ($data->category_status == "active") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['active'] . "</span>";
                                            } elseif ($data->category_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $data->category_status . "</span>";
                                            }
                                            ?></td>
                                    </tr>

                                    <?php
                                    if (!empty($data->category_options)) {

                                        $temp = json_decode($data->category_options);
                                        $name = "";
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                if ($dataAllAdminsLOOP->admin_id == $temp->admin) {
                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $lang['d_create']; ?></td>
                                            <td><?= (!empty($name)) ? $name : $temp->admin; ?></td>
                                            <td>
                                                <bdi><?= Utils::getTimeCountry($Settings['date_format'], $temp->date_create); ?></bdi>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?= $lang['d_update']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->create) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                }
                                                            }
                                                        }
                                                        echo (!empty($name)) ? $name : $loop->create;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnActive"
                                        type="button"
                                        class="<?= ($data->category_status == "active") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive" type="button"
                                        class="<?= ($data->category_status == "inactive") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["inactive_submit"]; ?>
                                </button>
                                <a href="/admin/category/academy/delete/<?= $id; ?>"
                                   class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["delete"]; ?>
                                </a>
                                <a href="/admin/category/academy"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["language"]; ?></h5>
                            <select class="form-control" id="language" data-toggle="select2"
                                    data-width="100%">
                                <?php
                                if (!empty($dataLanguages)) {
                                    foreach ($dataLanguages as $dataLanguagesITEM) {
                                        ?>
                                        <option value="<?= $dataLanguagesITEM->slug; ?>"
                                            <?= ($data->category_language == $dataLanguagesITEM->slug) ? "selected" : ""; ?>>
                                            <?= $lang[$dataLanguagesITEM->name]; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_select_parent_category"]; ?></h5>
                            <select class="form-control" id="child" data-toggle="select2"
                                    data-width="100%">
                                <option value="">-- <?= $lang['a_not_parent_category']; ?></option>
                                <?php
                                if (!empty($dataCategories)) {
                                    foreach ($dataCategories as $loop) {

                                        $flag = true;
                                        if (($data->parent_id == null || $data->parent_id == '')) {
                                            foreach ($dataCategories as $loop2) {
                                                if ($data->category_id == $loop2->parent_id) {
                                                    $flag = false;
                                                }
                                            }
                                        }
//                                        if (($loop->parent_id == null || $loop->parent_id == '')
//                                            && $loop->category_id != $data->category_id && $flag) {
                                        if( $loop->category_id != $data->category_id){
                                            ?>
                                            <option value="<?= $loop->category_id; ?>"
                                                <?= ($loop->category_id == $data->parent_id) ? 'selected' : null; ?>>
                                                <?= $loop->category_name; ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a target="_blank"
                                   href="<?= Utils::fileExist($data->category_thumbnail, BOX_EMPTY); ?>"
                                   class="w-100">
                                    <i class="mdi mdi-eye me-1"></i>
                                </a>
                                <?= $lang["thumbnail"]; ?></h5>
                            <form action="/" method="post" class="dropzone" id="uploadPost"
                                  data-plugin="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file">
                                </div>
                                <div class="dz-message needsclick">
                                    <img class="img-fluid rounded"
                                         src="<?= Utils::fileExist($data->category_thumbnail, BOX_EMPTY); ?>">
                                </div>
                            </form>
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
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'mobile_invalid' => $lang['mobile_invalid'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
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
