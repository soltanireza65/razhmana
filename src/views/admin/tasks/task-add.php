<?php
$pageSlug = "a_task";
// permission_can_insert

global $lang;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All Roles
         */
        $resultAllRoles = Admin::getAllRoles();
        $dataAllRoles = [];
        if ($resultAllRoles->status == 200 && !empty($resultAllRoles->response)) {
            $dataAllRoles = $resultAllRoles->response;
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdminWithRole();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        /**
         * Get Admin ID
         */
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('clockpicker', '/dist/libs/clockpicker/bootstrap-clockpicker.min.css');
        enqueueStylesheet('persian-datepicker', '/dist/libs/persian-calendar/persian-datepicker.min.css');

        // Load Script In Footer
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('clockpicker', '/dist/libs/clockpicker/bootstrap-clockpicker.min.js');
        enqueueScript('persian-date-min-js', '/dist/libs/persian-calendar//persian-date.min.js');
        enqueueScript('persian-datepicker-min-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
//        enqueueScript('mask', '/dist/libs/inputmask/inputmask.js');
        enqueueScript('task-add', '/dist/js/admin/tasks/task-add.init.js');

        getHeader($lang["list_posts"], [
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
            <style>
                .row.align-items-center > .col-auto > img[alt]:after {
                    position: absolute;
                    top: 21px;
                    left: 0;
                    right: 11px;
                    bottom: 0;
                    color: transparent;
                    text-align: center;
                    content: attr(alt);
                    background: #fff url('/dist/images/placeholder.svg') no-repeat 50%;
                    background-size: contain;
                    height: 2.25rem;
                    width: 2.25rem;
                }

                .sd-asset-item-selection {
                    display: flex;
                    align-items: center;
                    line-height: 30px;
                }

                .sd-asset-item {
                    display: flex;
                    align-items: center;
                    line-height: 34px;
                }

                .sd-asset-image {
                    width: 24px;
                    height: 24px;
                    margin-right: .5rem;
                    object-fit: contain;
                }
                .popover-title{
                    direction: ltr;
                }
            </style>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["set_new_task"]; ?></h5>

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

                                <div class="col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="<?= $lang["description"]; ?>"
                                                  id="xDescription" style="height: 100px"></textarea>
                                        <label for="xDescription"><?= $lang["description"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_xDescription">0</span>
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase3" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["time"]; ?></h5>
                            <div class="row show" id="cardCollpase3">

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" style="direction: ltr"
                                               id="StartDate"
                                               value="<?= date('Y/m/d', time()); ?>"
                                               placeholder="<?= $lang["start_date"]; ?>" maxlength="10"
                                               data-inputmask="'alias': 'datetime', 'inputFormat': 'yyyy/mm/dd'">
                                        <label for="StartDate">
                                            <i class="mdi mdi-calendar-check"></i> <?= $lang["start_date"]; ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-group clockpicker form-floating mb-3" data-placement="top"
                                         data-align="top" data-autoclose="true">
                                        <input type="text" class="form-control" value="09:15" id="StartTime">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-clock-outline mdi-spin"></i></span>
                                        <label for="StartTime" class="form-label"><?= $lang["start_time"]; ?></label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" style="direction: ltr"
                                               id="EndDate"
                                               value="<?= date('Y/m/d', time() + 86400); ?>"
                                               placeholder="<?= $lang["end_date"]; ?>" maxlength="10"
                                               data-inputmask="'alias': 'datetime', 'inputFormat': 'yyyy/mm/dd'">
                                        <label for="EndDate">
                                            <i class="mdi mdi-calendar-check"></i> <?= $lang["end_date"]; ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-group clockpicker form-floating mb-3" data-placement="top"
                                         data-align="top" data-autoclose="true">
                                        <input type="text" class="form-control" value="09:15" id="EndTime">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-clock-outline mdi-spin"></i></span>
                                        <label for="EndTime" class="form-label"><?= $lang["end_time"]; ?></label>
                                    </div>
                                </div>

                                <input id="startDefault" type="hidden">
                                <input id="endDefault" type="hidden">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase2" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["attachment_files"]; ?></h5>
                            <div class="row show" id="cardCollpase2">

                                <div class="col-12">
                                    <form action="#" method="post" class="dropzone"
                                          id="attachmentsDropzone"
                                          data-plugin="dropzone"
                                          data-previews-container="#file-previews"
                                          data-upload-preview-template="#uploadPreviewTemplate"
                                          enctype="multipart/form-data">
                                        <div class="fallback">
                                            <input type="file" name="file">
                                        </div>

                                        <div class="dz-message needsclick">
                                            <i class="fe-upload-cloud h3 text-muted"></i>
                                            <h5><?= $lang['drop_files']; ?></h5>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-12">

                                    <div class="row dropzone-previews mt-3" id="file-previews"></div>

                                    <div class="d-none" id="uploadPreviewTemplate">
                                        <div class="col-12">
                                            <div class="card   border">
                                                <div class="p-2">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <img data-dz-thumbnail src="#"
                                                                 class="avatar-sm rounded" alt="">
                                                        </div>
                                                        <div class="col">
                                                            <strong class="text-muted" data-dz-name></strong>
                                                            <p class="mb-0" data-dz-size></p>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-striped"
                                                                     role="progressbar" data-dz-progress
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="javascript:void(0);"
                                                               class="btn btn-lg btn-link text-danger  "
                                                               data-dz-remove>
                                                                <i class="fe-x align-middle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div> <!-- end row -->
                        </div> <!-- end card-body-->
                    </div>


                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnActive"
                                        type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnCancel"
                                        type="button"
                                        class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["priority"]; ?></h5>

                            <div class="form-check mb-2 form-check-danger">
                                <input class="form-check-input rounded-circle" type="radio" value="important"
                                       id="priority_important" name="priority">
                                <label class="form-check-label text-danger"
                                       for="priority_important"><?= $lang["important"]; ?></label>
                            </div>

                            <div class="form-check mb-2 form-check-pink">
                                <input class="form-check-input rounded-circle" type="radio" value="critical"
                                       id="priority_critical" name="priority">
                                <label class="form-check-label text-pink"
                                       for="priority_critical"><?= $lang["critical"]; ?></label>
                            </div>

                            <div class="form-check mb-2 form-check-warning">
                                <input class="form-check-input rounded-circle" type="radio" value="high"
                                       id="priority_high" name="priority">
                                <label class="form-check-label text-warning"
                                       for="priority_high"><?= $lang["high"]; ?></label>
                            </div>
                            <div class="form-check mb-2 form-check-success">
                                <input class="form-check-input rounded-circle" type="radio" value="medium"
                                       checked
                                       id="priority_medium" name="priority">
                                <label class="form-check-label text-success"
                                       for="priority_medium"><?= $lang["medium"]; ?></label>
                            </div>
                            <div class="form-check mb-2 form-check-primary">
                                <input class="form-check-input rounded-circle" type="radio" value="low"
                                       id="priority_low" name="priority">
                                <label class="form-check-label text-primary"
                                       for="priority_low"><?= $lang["low"]; ?></label>
                            </div>
                            <div class="form-check mb-2 form-check-info">
                                <input class="form-check-input rounded-circle" type="radio" value="informational"
                                       id="priority_informational" name="priority">
                                <label class="form-check-label text-info"
                                       for="priority_informational"><?= $lang["informational"]; ?></label>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['refer_to']; ?></h5>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <select class="form-control"
                                            id="referTask"
                                            multiple="multiple"
                                            data-toggle="select2"
                                            data-width="100%">
                                        <?php
                                        if (!empty($dataAllAdmins)) {
//                                        echo "<optgroup label='" . $lang['adminss'] . "'>";
                                            foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                if ($dataAllAdminsITEM->admin_id != $admin_id) {
                                                    ?>
                                                    <option
                                                            data-tj-category-image="<?= Utils::fileExist($dataAllAdminsITEM->admin_avatar, USER_AVATAR); ?>"
                                                            data-tj-category-status="<?= ($dataAllAdminsITEM->admin_status == "active") ? $lang['active'] : $lang['inactive']; ?>"
                                                            data-tj-category-color="<?php
                                                            if ($dataAllAdminsITEM->admin_status == "inactive") {
                                                                echo "danger";
                                                            } elseif ($dataAllAdminsITEM->role_status == "inactive") {
                                                                echo "pink";
                                                            } else {
                                                                echo 'success';
                                                            }
                                                            ?>"
                                                            data-tj-type="admin"
                                                            value="<?= $dataAllAdminsITEM->admin_id; ?>">
                                                        <?= $dataAllAdminsITEM->admin_nickname; ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
//                                        echo '</optgroup>';
                                        }
                                        ?>
                                    </select>
                                </div>
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
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'token_error' => $lang['token_error'],
                        'a_empty_refer' => $lang['a_empty_refer'],
                        'a_empty_desc' => $lang['a_empty_desc'],
                        'a_empty_title' => $lang['a_empty_title'],
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