<?php
$pageSlug = "posts";
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
         * Get Post Info By ID
         */
        $result = post::getPostById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/post');
        }

        /**
         * Get All Post Categories
         */
        $resultAllCategories = post::getAllPostCategories('active');
        $dataCategories = [];
        if ($resultAllCategories->status == 200 && !empty($resultAllCategories->response)) {
            $dataCategories = $resultAllCategories->response;
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


        // Load Stylesheets & Icons
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('TinyMCE', '/dist/libs/TinyMCE/js/TinyMCE.js');
        enqueueScript('post-edit', '/dist/js/admin/post/post-edit.init.js');

        getHeader($lang["post_edit"], [
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
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["post_edit"]; ?></h5>

                            <div class="row show" id="cardCollpase1">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control"
                                               value="<?= $data->post_title; ?>"
                                               type="text"
                                               id="xTitle"
                                               placeholder="<?= $lang["title"]; ?>">
                                        <label for="xTitle"><?= $lang["title"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_xTitle">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <div id="xBody" style="height: 400px;">
                                        <?= $data->post_description; ?>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase2" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["seo_setting"]; ?></h5>
                            <div class="row show" id="cardCollpase2">
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="<?= $lang['a_meta_title']; ?>"
                                                  id="xMetaTitle"
                                                  style="height: 100px"><?php echo $data->post_meta_title; ?></textarea>
                                        <label for="xMetaTitle"><?= $lang['a_meta_title']; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_xMetaTitle">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="<?= $lang["excerpt"]; ?>"
                                                  id="xExcerpt"
                                                  style="height: 100px"><?php echo $data->post_excerpt; ?></textarea>
                                        <label for="xExcerpt"><?= $lang["excerpt"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-info"
                                                    id="length_xExcerpt">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="<?= $lang['a_schema']; ?>"
                                                  id="xSchema"
                                                  style="height: 100px"><?php echo $data->post_schema; ?></textarea>
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
                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnPublish"
                                        type="button"
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn mt-1 w-sm btn-soft-success waves-effect shadow-none waves-light
                                        <?= ($data->post_status == "published") ? "active" : ""; ?>"
                                        data-style="zoom-in">
                                    <?= $lang["published_submit"]; ?>
                                </button>
                                <button id="btnDraft"
                                        type="button"
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn mt-1 w-sm btn-soft-warning waves-effect shadow-none waves-light
                                        <?= ($data->post_status == "draft") ? "active" : ""; ?>"
                                        data-style="zoom-in">
                                    <?= $lang["draft_submit"]; ?>
                                </button>
                                <button id="btnUpdateDate"
                                        type="button"
                                        data-tj-id="<?= $id; ?>"
                                        class="btn mt-1 w-sm btn-soft-info waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["a_update_date"]; ?>
                                </button>
                                <a href="/admin/post/delete/<?= $id; ?>"
                                   class="btn mt-1 w-sm btn-soft-danger waves-effect shadow-none waves-light">
                                    <?= $lang["delete"]; ?>
                                </a>
                                <a href="/admin/post"
                                   class="btn mt-1 w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["category"]; ?></h5>
                            <select class="form-control"
                                    id="xCategries"
                                    data-toggle="select2"
                                    data-width="100%">
                                <?php
                                if (!empty($dataCategories)) {
                                    foreach ($dataCategories as $dataCategoryITEM) {
                                        ?>
                                        <option <?= ($data->category_id == $dataCategoryITEM->category_id) ? " selected " : ""; ?>
                                                value="<?= $dataCategoryITEM->category_id; ?>"
                                                data-tj-language="<?= $dataCategoryITEM->category_language; ?>">
                                            <?= $dataCategoryITEM->category_name; ?>
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["address_slug"]; ?></h5>
                            <div class="form-floating mb-3">
                                <input value="<?php
                                $e = explode("-", $data->post_slug);
                                unset($e[array_key_last($e)]);
                                echo implode("-", $e); ?>"
                                       type="text"
                                       class="form-control"
                                       id="xSlug" placeholder="<?= $lang["address"]; ?>"
                                       onkeypress="return /[0-9a-zA-Z,.*+\u0600-\u06FF)`(}{_/|\-@#$%^[\]]/i.test(event.key)">
                                <label for="xSlug"><?= $lang["address"]; ?></label>
                                <small class="form-text text-muted">
                                    <?= $lang['length_text']; ?> : <span
                                            class="text-danger"
                                            id="length_xSlug">0</span>
                                </small>
                                <p class="text-warning mt-3">
                                    <?= $lang['min_length_input']; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a target="_blank"
                                   href="<?= Utils::fileExist($data->post_thumbnail, BOX_EMPTY); ?>"
                                   class="w-100">
                                    <i class="mdi mdi-eye me-1"></i>
                                </a>
                                <?= $lang["thumbnail"]; ?>
                            </h5>
                            <form action="/" method="post" class="dropzone" id="uploadPost"
                                  data-plugin="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file">
                                </div>
                                <div class="dz-message needsclick">
                                    <img class="img-fluid rounded"
                                         src="<?= Utils::fileExist($data->post_thumbnail, BOX_EMPTY); ?>">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase3"
                                   role="button"
                                   aria-expanded="false" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>

                            <div class="table-responsive collapse" id="cardCollpase3"
                                 style="max-height: 176px;overflow: auto;">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td class="text-end"><?php
                                            if ($data->post_status == "published") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['published'] . "</span>";
                                            } elseif ($data->post_status == "draft") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['draft'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $data->post_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["preview"]; ?> :</td>
                                        <td class="text-end"><a target="_blank"
                                                                href="<?= SITE_URL . '/blog_p/' . $data->post_id;; ?>">
                                                <?= $lang['view']; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["a_show_blog"]; ?> :</td>
                                        <td class="text-end"><a target="_blank"
                                                                href="<?= SITE_URL . '/blog/' . $data->post_slug;; ?>">
                                                <?= $lang['view']; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["language"]; ?> :</td>
                                        <td class="text-end">
                                            <?php
                                            if (!empty($dataLanguages)) {
                                                foreach ($dataLanguages as $dataLanguagesITEM) {
                                                    if ($dataLanguagesITEM->slug == $data->post_language) {
                                                        echo $lang[$dataLanguagesITEM->name];
                                                    }

                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    if (!empty($data->post_options)) {

                                        $temp = json_decode($data->post_options);
                                        $name = "";
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                if ($dataAllAdminsLOOP->admin_id == $data->admin_id) {
                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $lang['creator']; ?></td>
                                            <td><?= (!empty($name)) ? $name : $data->admin_id; ?></td>
                                            <td class="text-end">
                                                <bdi><?= Utils::getTimeCountry($Settings['date_format'], $data->post_submit_time); ?></bdi>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?= $lang['editor']; ?></td>
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
                                                    <td class="text-end">
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
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'mobile_invalid' => $lang['mobile_invalid'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
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
                $lang['help_post_1'],
                $lang['help_post_2'],
                $lang['help_post_3'],
                $lang['help_post_4'],
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