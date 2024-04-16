<?php
$pageSlug = "academy";

// permission_can_insert

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang;

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

        /**
         * Get All Academy Categories
         */
        $resultAllCategories = Academy::getAllCategories('active');
        $dataCategories = [];
        if ($resultAllCategories->status == 200 && !empty($resultAllCategories->response)) {
            $dataCategories = $resultAllCategories->response;
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
        enqueueScript('academy-add', '/dist/js/admin/academy/academy-add.init.js');

        getHeader($lang["academy_add"], [
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
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["academy_add"]; ?></h5>

                            <div class="row show" id="cardCollpase1">

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="xTitle"
                                               placeholder="<?= $lang["title"]; ?>">
                                        <label for="xTitle"><?= $lang["title"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_xTitle">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <div id="xBody" style="height: 700px;">


                                    </div>
                                </div>
                                <!-- end col -->

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
                                                  style="height: 100px"></textarea>
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
                                                  id="xExcerpt" style="height: 100px"></textarea>
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
                                                  style="height: 100px"></textarea>
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
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["published_submit"]; ?>
                                </button>
                                <button id="btnDraft"
                                        type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["draft_submit"]; ?>
                                </button>
                                <a href="/admin/academy"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
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
                                        <option value="<?= $dataCategoryITEM->category_id; ?>"
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
                                <input type="text" class="form-control" id="xSlug"
                                       placeholder="<?= $lang["address"]; ?>"
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["thumbnail"]; ?></h5>
                            <form action="/" method="post" class="dropzone" id="uploadPost"
                                  data-plugin="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file">
                                </div>
                                <div class="dz-message needsclick">
                                    <i class="h1 text-muted dripicons-cloud-upload"></i>
                                    <h3><?= $lang["drop_files"]; ?></h3>
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
                        'successful_submit_mag' => $lang['successful_submit_mag'],
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
                $lang['help_academy_1'],
                $lang['help_academy_2'],
                $lang['help_academy_3'],
                $lang['help_academy_4'],
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