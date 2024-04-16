<?php
$pageSlug = "tickets";
// permission_can_insert

global $lang,$antiXSS;

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

        $pageSlugList=[
            "tickets",
            "tickets_driver",
            "tickets_businessman",
            "tickets_ship",
            "tickets_air",
            "tickets_railroad",
            "tickets_inventory",
        ];

        $flagSlug = false;
        if (!empty($dataCheckAdminRoleForCheck) && json_decode($dataCheckAdminRoleForCheck)->role_status == "active") {
            foreach (json_decode($dataCheckAdminRoleForCheck)->permissons as $item000) {
                if (in_array($item000->slug_name, $pageSlugList) && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                    $pageSlug=$item000->slug_name;
                }
            }
        }
// end roles 1

        $user_id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($user_id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }


        $userName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $userName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
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
         * Get All Departments
         */
        $resultAllDepartments = ATicket::getAllDepartments("");
        $dataAllDepartments = [];
        if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
            $dataAllDepartments = $resultAllDepartments->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('quill-core-css', '/dist/libs/quill/quill.core.css');
        enqueueStylesheet('quill-snow-css', '/dist/libs/quill/quill.snow.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('quill-js', '/dist/libs/quill/quill.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('ticket-add', '/dist/js/admin/ticket/ticket-add.init.js');


        getHeader($lang["add_ticket"], [
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
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    color: transparent;
                    text-align: center;
                    content: attr(alt);
                    background: #fff url('/dist/images/placeholder.svg') no-repeat 50%;
                    background-size: contain;
                    height: 2.25rem;
                    width: 2.25rem;
                }
            </style>
            <div class="row">

                <div class="col-xl-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-uppercase bg-light p-2 mt-0">
                                <a href="/admin/users/info/<?= $user_id; ?>"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="<?= $lang['user_info']; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline me-1"></i>
                                </a>
                                <?= $lang['all_info']; ?>
                            </h5>
                            <div class="d-flex align-items-start mb-3">
                                <img src="<?= USER_AVATAR; ?>"
                                     class="me-2 avatar-md rounded-circle" height="42"
                                     id="ticketID"
                                     style="height: 60px"
                                     data-user-id="<?= $user_id; ?>"
                                     alt="<?= $userName; ?>">
                                <div class="w-100">
                                    <h4 class="mt-0 mb-1"><?= $userName; ?></h4>
                                    <p class="text-muted">
                                        <?php
                                        if ($dataUserInfoById->user_type == "businessman") {
                                            ?>
                                            <i class="mdi mdi-office-building"></i>
                                            <?= $lang['businessman']; ?>
                                            <?php
                                        } elseif ($dataUserInfoById->user_type == "driver") {
                                            ?>
                                            <i class="mdi mdi-dump-truck"></i>
                                            <?= $lang['driver']; ?>
                                            <?php
                                        } elseif ($dataUserInfoById->user_type == "guest") {
                                            ?>
                                            <i class="mdi mdi-account-alert"></i>
                                            <?= $lang['guest_user']; ?>
                                        <?php } else { ?>
                                            <i class="mdi mdi-account-edit-outline"></i>
                                            <?= $dataUserInfoById->user_type; ?>
                                        <?php } ?>
                                    </p>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="text-center button-list">
                                    <a target="_self"
                                       href="/admin/users/info/<?= $user_id; ?>"
                                       class="btn btn-soft-info btn-sm  waves-effect waves-light">
                                        <?= $lang['user_info']; ?>
                                    </a>
                                    <a target="_self"
                                       href="/admin/ticket/user/<?= $user_id; ?>"
                                       class="btn btn-soft-primary btn-sm  waves-effect waves-light">
                                        <?= $lang['list_tickets']; ?>
                                    </a>
                                </div>
                            </div>

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

                <div class="col-xl-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['add_ticket']; ?></h5>


                            <div class="row">

                                <div class="col">

                                    <div class="mt-2 bg-light p-3 rounded">

                                        <!-- end row-->
                                        <div class="row align-items-center ">

                                            <div class="col-12 col-md-12 col-lg-12 mb-2 mb-sm-0">
                                                <div class="form-floating mb-3">
                                                    <input id="TitleSendMassage" type="text" class="form-control"
                                                           placeholder="<?= $lang['please_enter_your_title']; ?>">
                                                    <label for="TitleSendMassage"><?= $lang['please_enter_your_title']; ?></label>
                                                    <small class="form-text text-muted">
                                                        <?= $lang['length_text']; ?> : <span
                                                                id="length_TitleSendMassage"
                                                                class="text-danger">0</span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <div id="ticket-message" class="form-control"
                                                         style="height: 200px;"></div>
                                                    <small class="form-text text-muted">
                                                        <?= $lang['length_text']; ?> : <span
                                                                id="length_ticketMessage" class="text-danger">0</span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="ticket-status"
                                                           hidden><?= $lang['status']; ?></label>
                                                    <select class="form-control" id="departments"
                                                            data-toggle="select2"
                                                            data-width="100%">
                                                        <?php
                                                        if (!empty($dataAllDepartments)) {
                                                            foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                                if ($dataAllDepartmentsITEM->department_status == "active") {
                                                                    $departmentName = (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                        array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";

                                                                    ?>
                                                                    <option value="<?= $dataAllDepartmentsITEM->department_id ?>">
                                                                        <?= $departmentName; ?>
                                                                    </option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="text-end mb-3">
                                                    <button type="button" id="BtnSendMassage"
                                                            data-style="zoom-in"
                                                            class="btn btn-primary waves-effect waves-light shadow-none px-4">
                                                        <i class="mdi mdi-send-outline me-1  mdi-rotate-315 "></i>
                                                        <?= $lang['send']; ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-auto">
                                                <h4 class="header-title mb-3"><?= $lang['attachment_files']; ?></h4>

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
                                                    <div class="col-auto">
                                                        <div class="card shadow-none border">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <img data-dz-thumbnail src="#"
                                                                             class="avatar-sm rounded position-relative"
                                                                             alt="">
                                                                    </div>
                                                                    <div class="col">
                                                                        <strong class="text-muted"
                                                                                data-dz-name></strong>
                                                                        <p class="mb-0" data-dz-size></p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href=""
                                                                           class="btn btn-lg btn-link text-danger shadow-none"
                                                                           data-dz-remove>
                                                                            <i class="fe-x align-middle"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div>

                                                                        <div class="progress mb-2 progress-md">
                                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                                                                                 role="progressbar" data-dzc-id
                                                                                 aria-valuenow="50"
                                                                                 aria-valuemin="0"
                                                                                 aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- end col-->
                            </div>
                            <!-- end row -->

                        </div> <!-- end card-body -->
                    </div> <!-- end card-->

                </div> <!-- end col -->

            </div>

            <input type="hidden" id="token" name="token"
                   value="<?= Security::initCSRF('admin-set-new-ticket-and-room') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
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