<?php
$pageSlug = "tickets";
// permission_can_edit

global $lang, $antiXSS, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once getcwd() . '/views/admin/header-footer.php';


$ticket_id = (int)$antiXSS->xss_clean($_REQUEST['id']);

$resultTicketById = ATicket::getTicketById($ticket_id);
$dataTicketById = [];
if ($resultTicketById->status == 200 && !empty($resultTicketById->response)) {
    $dataTicketById = $resultTicketById->response[0];
}
if (empty($dataTicketById)) {
    header('Location: /admin');
}

$user_id = $dataTicketById->user_id;


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


/**
 * Get All Departments
 */
$resultAllDepartments = ATicket::getAllDepartments("");
$dataAllDepartments = [];
if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
    $dataAllDepartments = $resultAllDepartments->response;
}

$xx_department_Info_type='';
if(!empty($dataAllDepartments)){
    foreach ($dataAllDepartments as $loop){
        if($loop->department_id  ==$dataTicketById->department_id){
            $xx_department_Info_type = $loop->department_type;
            $pageSlug = "tickets_" . $xx_department_Info_type;
        }
    }
}


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
                if ($item000->slug_name == 'tickets' && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                    $pageSlug = "tickets";
                }
            }
        }
// end roles 1



        $userName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $userName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }

        /**
         * Get ALL Messages If Exist
         */
        $resultAllTicketMessages = ATicket::getAllTicketMessages($ticket_id);
        $dataAllTicketMessages = [];
        if ($resultAllTicketMessages->status == 200 && !empty($resultAllTicketMessages->response)) {
            $dataAllTicketMessages = array_reverse($resultAllTicketMessages->response);
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
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
        enqueueScript('ticket-single', '/dist/js/admin/ticket/ticket-single.init.js');

        getHeader($lang["ticket"], [
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
                                   target="_self"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="<?= $lang['user_info']; ?>">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                </a>
                                <?= $lang['all_info']; ?>
                            </h5>
                            <div class="d-flex align-items-start mb-3">
                                <img onclick="this.requestFullscreen()"
                                     src="<?= USER_AVATAR; ?>"
                                     class="me-2 avatar-md rounded-circle" height="42"
                                     id="ticketID"
                                     data-user-id="<?= $user_id; ?>"
                                     data-room-id="<?= $ticket_id; ?>"
                                     alt="<?= $userName; ?>">
                                <div class="w-100">
                                    <h5 class="mt-0 mb-0 font-15">
                                        <a href="/admin/users/info/<?= $user_id; ?>"
                                           class="text-reset">
                                            <?= $userName; ?>
                                        </a>
                                    </h5>
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


                            <div class="p-2">


                                <a href="javascript: void(0);" class="text-reset mb-2 d-block">
                                    <i class="mdi mdi-clock-time-eight-outline me-1 text-success mdi-spin"></i>
                                    <span class="mb-0 mt-1"><bdi><?= Utils::getTimeCountry('d F Y ', $dataTicketById->ticket_submit_date); ?></bdi></span>
                                </a>

                                <a href="javascript: void(0);" class="text-reset mb-2 d-block">
                                    <i class="mdi mdi-email-open me-1 text-primary"></i>
                                    <span class="mb-0 mt-1"><?= $lang[$dataTicketById->ticket_status]; ?></span>
                                </a>

                                <a href="javascript: void(0);" class="text-reset mb-2 d-block">
                                    <i class="mdi mdi-office-building me-1 text-danger"></i>
                                    <span class="mb-0 mt-1">
                                        <?php
                                        $type = '';
                                        if (!empty($dataAllDepartments)) {
                                            foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                if ($dataAllDepartmentsITEM->department_id == $dataTicketById->department_id) {
                                                    $type = $dataAllDepartmentsITEM->department_type;
                                                    echo (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                        array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                }
                                            }
                                        }
                                        ?>
                                    </span>
                                </a>
                            </div>

                            <div class="col-auto">
                                <div class="text-center button-list">
                                    <a target="_self"
                                       href="/admin/users/info/<?= $user_id; ?>"
                                       class="btn btn-soft-primary btn-sm waves-effect waves-light">
                                        <i class="mdi mdi-phone me-1"></i>
                                        <?= $lang['user_info']; ?>
                                    </a>

                                    <?php
                                    if ($dataTicketById->ticket_status == "open") {
                                        ?>
                                        <button id="BtnCloseTicket"
                                                type="button"
                                                data-style="zoom-in"
                                                class="btn btn-soft-danger btn-sm waves-effect waves-light">
                                            <i class="mdi mdi-lock-outline me-1"></i>
                                            <?= $lang['room_close']; ?>
                                        </button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->

                </div>

                <div class="col-xl-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $dataTicketById->ticket_title; ?></h5>

                            <ul class="conversation-list" data-simplebar="init" style="max-height: 460px;">

                                <div class="simplebar-wrapper" style="margin: 0px -15px;">
                                    <div class="simplebar-height-auto-observer-wrapper">
                                        <div class="simplebar-height-auto-observer"></div>
                                    </div>
                                    <div class="simplebar-mask">
                                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                                 aria-label="scrollable content"
                                                 style="height: auto; overflow: hidden scroll;">
                                                <div class="simplebar-content" style="padding: 0px 15px;">

                                                    <?php
                                                    if (!empty($dataAllTicketMessages)) {
                                                        foreach ($dataAllTicketMessages as $dataAllTicketMessagesITEM) {
                                                            ?>
                                                            <li class="clearfix <?php
                                                            if (empty($dataAllTicketMessagesITEM->admin_id)) {
                                                                echo " odd";
                                                                //admin
                                                            }
                                                            ?>">
                                                                <div class="chat-avatar" style="width: 60px;">
                                                                    <img data-id="<?= $dataAllTicketMessagesITEM->message_id ?>"
                                                                         src="<?php
                                                                         if (empty($dataAllTicketMessagesITEM->admin_id)) {
                                                                             echo USER_AVATAR;
                                                                         } else {
                                                                             $adminName = "";
                                                                             $adminAvatar = "";
                                                                             if (!empty($dataAllAdmins)) {
                                                                                 foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                                                     if ($dataAllAdminsITEM->admin_id == $dataAllTicketMessagesITEM->admin_id) {
                                                                                         $adminName = $dataAllAdminsITEM->admin_nickname;
                                                                                         $adminAvatar = $dataAllAdminsITEM->admin_avatar;
                                                                                     }
                                                                                 }
                                                                             }
                                                                             echo Utils::fileExist($adminAvatar, USER_AVATAR);
                                                                         }
                                                                         ?>"
                                                                         class="rounded"
                                                                         style="height: 60px"
                                                                         alt="<?php
                                                                         if (empty($dataAllTicketMessagesITEM->admin_id)) {
                                                                             echo $userName;
                                                                         } else {
                                                                             echo $adminName;
                                                                         }
                                                                         ?>">
                                                                    <i>
                                                                        <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataAllTicketMessagesITEM->message_submit_date); ?></bdi>
                                                                        <br>
                                                                        <bdi><?= Utils::getTimeCountry($Settings['time_format'], $dataAllTicketMessagesITEM->message_submit_date); ?></bdi>
                                                                    </i>
                                                                </div>
                                                                <div class="conversation-text">
                                                                    <div class="ctext-wrap">
                                                                        <i><?php
                                                                            if (empty($dataAllTicketMessagesITEM->admin_id)) {
                                                                                echo $userName;
                                                                            } else {
                                                                                echo $adminName;
                                                                            }
                                                                            ?></i>
                                                                        <p>
                                                                            <?= $dataAllTicketMessagesITEM->message_body ?>
                                                                        </p>
                                                                    </div>


                                                                    <?php
                                                                    if (!empty($dataAllTicketMessagesITEM->message_attachment)) {
                                                                        ?>
                                                                        <div class="mt-2 mb-1">
                                                                            <div>
                                                                                <div class="row <?php
                                                                                if (empty($dataAllTicketMessagesITEM->admin_id)) {
                                                                                    echo " flex-row-reverse ";
                                                                                    //admin
                                                                                }
                                                                                ?> align-items-center mx-0">
                                                                                    <?php
                                                                                    foreach (json_decode($dataAllTicketMessagesITEM->message_attachment) as $loop) {
                                                                                        $extension = pathinfo($loop->attachment, PATHINFO_EXTENSION);
                                                                                        ?>
                                                                                        <div class="col-auto px-1">
                                                                                            <a target="_self"
                                                                                               href="<?= SITE_URL . $loop->attachment; ?>"
                                                                                               class="d-block avatar-sm"
                                                                                               download="">
                                                                                                <span class="avatar-title bg-primary rounded"
                                                                                                      style="text-transform: uppercase;">
                                                                                                 <!--  <bdi><i class="dripicons-download"></i></bdi>-->
                                                                                                    <bdi>.<?= $extension; ?></bdi>
                                                                                                </span>
                                                                                            </a>
                                                                                        </div>
                                                                                        <?php
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </li>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <p class="text-center">
                                                            <img src="<?= BOX_EMPTY; ?>"
                                                                 style="width: 100%;max-width: fit-content;"
                                                                 alt="<?= $lang['no_massages']; ?>">
                                                        </p>
                                                        <?php
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="simplebar-placeholder" style="width: auto; height: 852px;"></div>
                                </div>

                                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                </div>

                                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                    <div class="simplebar-scrollbar"
                                         style="height: 248px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                                </div>
                            </ul>
                            <?php
                            if ($dataTicketById->ticket_status == "open") {
                                ?>
                                <div class="row">
                                    <div class="col">
                                        <div class="mt-2 bg-light p-3 rounded">

                                            <!-- end row-->
                                            <div class="row align-items-center ">

                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <div id="ticket-message" class="form-control"
                                                             style="height: 200px;"></div>
                                                        <small class="form-text text-muted">
                                                            <?= $lang['length_text']; ?> : <span
                                                                    id="length_ticketMessage"
                                                                    class="text-danger">0</span>
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="ticket-status"
                                                               hidden><?= $lang['status']; ?></label>
                                                        <select class="form-control" id="ticket-status"
                                                                data-toggle="select2"
                                                                data-width="100%">
                                                            <?php
                                                            if (!empty($dataAllDepartments)) {
                                                                foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                                    if ($dataAllDepartmentsITEM->department_id == $dataTicketById->department_id) {
                                                                        $departmentName = (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                            array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                        ?>
                                                                        <option selected disabled="disabled" value="-1">
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
                                <?php
                            } else {
                                ?>
                                <div class="row">
                                    <div class="col">
                                        <div class="mt-2 bg-light p-3 rounded text-center text-warning">
                                            <?= $lang['ticket_closed']; ?>
                                        </div>
                                    </div>
                                    <!-- end col-->
                                </div>
                                <?php
                            }
                            ?>

                        </div> <!-- end card-body -->
                    </div> <!-- end card-->


                </div> <!-- end col -->

            </div>
            <input type="hidden" id="token" name="token"
                   value="<?= Security::initCSRF('admin-set-ticket-exist-room') ?>">
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