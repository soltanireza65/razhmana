<?php
$pageSlug = "a_model_c";
// permission_can_edit

global $lang, $antiXSS, $Settings;

use MJ\Utils\Utils;
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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * get Model Info By Id
         */

        $result = PosterC::getModelById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/category/model');
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
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        /**
         * Get All Brands
         */
        $resultBrandParent = PosterC::getAllBrandsParentActive();
        $dataBrandParent = [];
        if ($resultBrandParent->status == 200 && !empty($resultBrandParent->response)) {
            $dataBrandParent = $resultBrandParent->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('model-edit', '/dist/js/admin/poster-category/model-edit.init.js');


        getHeader($lang["a_model_edit"], [
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

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_brand_edit"]; ?></h5>
                            <div class="row">
                                <?php
                                if (!empty($dataLanguages)) {
                                    foreach ($dataLanguages as $dataLanguagesTEMP) {
                                        ?>
                                        <div class="col-lg-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control titleCategory"
                                                       data-id="<?= $dataLanguagesTEMP->id; ?>"
                                                       data-slug="<?= $dataLanguagesTEMP->slug; ?>"
                                                       placeholder="<?= $lang["title"]; ?>(<?= $lang[$dataLanguagesTEMP->name]; ?>)"
                                                    <?php
                                                    foreach (json_decode($data->model_name) as $temp) {
                                                        if ($temp->slug == $dataLanguagesTEMP->slug) {
                                                            echo 'value="' . $temp->value . '"';
                                                        }
                                                    }
                                                    ?>
                                                       value="">
                                                <label for="nameCAteGory"><?= $lang["title"];
                                                    if ($dataLanguagesTEMP->status == "inactive") {
                                                        echo '<span class="text-danger font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                    } else {
                                                        echo '<span class="text-success font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                    }; ?></label>
                                                <small class="form-text text-muted">
                                                    <?= $lang['length_text']; ?> : <span
                                                            class="text-danger"
                                                            data-id-length="<?= $dataLanguagesTEMP->id; ?>">0</span>
                                                </small>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase1"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" style="max-height: 176px;overflow: auto;"
                                 id="cardCollpase1">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td><?php
                                            if ($data->model_status == "active") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['active'] . "</span>";
                                            } elseif ($data->model_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['inactive'] . "</span>";
                                            } elseif ($data->model_status == "user") {
                                                echo "<span class='badge badge-soft-info font-13'>" . $lang['a_user_creator'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $data->model_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"><?= $lang["a_creator"]; ?> :</td>
                                        <td><?php
                                            if ($data->model_creator == "admin") {
                                                echo $lang['admin'];
                                            } elseif ($data->model_creator == "user") {
                                                echo $lang['user'];
                                            } else {
                                                echo $data->model_creator;
                                            }
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                    if (!empty($data->model_options)) {

                                        $temp = json_decode($data->model_options);
                                        $name = "";
                                        if (isset($temp->admin) && isset($temp->date_create)) {


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
                                        } else {
                                            ?>
                                            <tr>
                                                <td><?= $lang['d_create']; ?></td>
                                                <td><?= '<a href="/admin/users/info/' . $temp->user . '">' . $lang['user'] . "</a>"; ?></td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $temp->date_create); ?></bdi>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        if (!empty(@$temp->update)) {
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
                                                                    break;
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
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive"
                                        type="button"
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["inactive_submit"]; ?>
                                </button>
                                <a href="/admin/category/model"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["priority_show"]; ?></h5>
                            <div class="col-lg-12">
                                <div class="form-floating mb-3">
                                    <input type="number"
                                           id="priority"
                                           class="form-control"
                                           value="<?= $data->model_priority; ?>"
                                           placeholder="<?= $lang["priority_show"]; ?>">
                                    <label for="priority"><?= $lang["priority_show"]; ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <select class="form-control"
                                    id="xParent"
                                    data-toggle="select2"
                                    data-width="100%">
                                <option value="0">
                                    <?= $lang['a_no_parent']; ?>
                                </option>
                                <?php
                                if (!empty($dataBrandParent)) {
                                    foreach ($dataBrandParent as $dataBrandParentLoop) {
                                        if ($dataBrandParentLoop->brand_type == "truck") {
                                            $posterType = $lang['a_truck'];
                                        } elseif ($dataBrandParentLoop->brand_type == "trailer") {
                                            $posterType = $lang['a_trailer'];
                                        } else {
                                            $posterType = $dataBrandParentLoop->brand_type;
                                        }
                                        ?>
                                        <option value="<?= $dataBrandParentLoop->brand_id; ?>"
                                            <?= ($data->brand_id == $dataBrandParentLoop->brand_id) ? "selected" : ""; ?>>
                                            <?= (!empty(array_column(json_decode($dataBrandParentLoop->brand_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                array_column(json_decode($dataBrandParentLoop->brand_name, true), 'value', 'slug')[$_COOKIE['language']] . " - " . $posterType : $dataBrandParentLoop->brand_id . " - " . $posterType; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
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
                        'warning' => $lang['warning'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'delete' => $lang['delete'],
                        'a_no_parent_error' => $lang['a_no_parent_error'],
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