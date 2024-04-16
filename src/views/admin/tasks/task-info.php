<?php
$pageSlug = "a_task";
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
         * Get All Roles
         */
        $result = Tasks::getTaskById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/tasks');
        }

        $resultDetail = Tasks::getTaskDetailByTaskId($id);
        $dataDetail = [];
        if ($resultDetail->status == 200 && !empty($resultDetail->response)) {
            $dataDetail = $resultDetail->response;
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
         * Get Admin ID
         */
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('task-info', '/dist/js/admin/tasks/task-info.init.js');

        getHeader($lang["a_task_report"], [
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
                <div class="col-xl-8 col-lg-7">
                    <!-- project card -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $data->task_title; ?></h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- assignee -->
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['from_to']; ?> :</p>
                                    <div class="d-flex align-items-start">
                                        <?php
                                        $name_from = "";
                                        $image_from = '';
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdmin) {
                                                if ($dataAllAdmin->admin_id == $data->creator_id) {
                                                    $name_from = $dataAllAdmin->admin_nickname;
                                                    $image_from = $dataAllAdmin->admin_avatar;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <img src="<?= Utils::fileExist($image_from, USER_AVATAR); ?>"
                                             alt="<?= $name_from; ?>"
                                             class="rounded-circle me-2"
                                             width="29"
                                             height="24"/>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <?= $name_from; ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <!-- end assignee -->
                                </div>
                                <!-- end col -->

                                <div class="col-md-4">
                                    <!-- assignee -->
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['refer_to']; ?> :</p>
                                    <div class="d-flex align-items-start">
                                        <?php
                                        $name = "";
                                        $image = '';

                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdmin) {
                                                if ($dataAllAdmin->admin_id == $data->admin_id) {
                                                    $name = $dataAllAdmin->admin_nickname;
                                                    $image = $dataAllAdmin->admin_avatar;
                                                    break;
                                                }
                                            }
                                        }

                                        ?>
                                        <img src="<?= Utils::fileExist($image, USER_AVATAR); ?>"
                                             alt="<?= $name; ?>"
                                             class="rounded-circle me-2"
                                             width="29"
                                             height="24"/>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <?= $name; ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <!-- end assignee -->
                                </div>
                                <!-- end col -->

                                <div class="col-md-4">
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['priority']; ?>:</p>
                                    <div class="d-flex align-items-start">
                                        <?php
                                        $color = 'danger';
                                        if ($data->task_priority == "important") {
                                            $color = 'danger';
                                        } elseif ($data->task_priority == "critical") {
                                            $color = 'pink';
                                        } elseif ($data->task_priority == "high") {
                                            $color = 'warning';
                                        } elseif ($data->task_priority == "medium") {
                                            $color = 'success';
                                        } elseif ($data->task_priority == "low") {
                                            $color = 'primary';
                                        } elseif ($data->task_priority == "informational") {
                                            $color = 'info';
                                        }
                                        ?>
                                        <i class="mdi mdi-progress-alert font-18 text-<?= $color; ?> me-1"></i>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <?php if ($data->task_priority == "important") {
                                                    echo $lang["important"];
                                                } elseif ($data->task_priority == "critical") {
                                                    echo $lang["critical"];
                                                } elseif ($data->task_priority == "high") {
                                                    echo $lang["high"];
                                                } elseif ($data->task_priority == "medium") {
                                                    echo $lang["medium"];
                                                } elseif ($data->task_priority == "low") {
                                                    echo $lang["low"];
                                                } elseif ($data->task_priority == "informational") {
                                                    echo $lang["informational"];
                                                } else {
                                                    echo 'XXX';
                                                } ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-md-4">
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['start_date']; ?> :</p>
                                    <div class="d-flex align-items-start">
                                        <i class="mdi mdi-calendar-month-outline font-18 text-success me-1"></i>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <bdi>
                                                    <?= Utils::getTimeCountry($Settings['data_time_format'], $data->task_start_date); ?>
                                                </bdi>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-md-4">
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['end_date']; ?>:</p>
                                    <div class="d-flex align-items-start">
                                        <i class="mdi mdi-calendar-month-outline font-18 text-danger me-1"></i>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <bdi>
                                                    <?= Utils::getTimeCountry($Settings['data_time_format'], $data->task_end_date); ?>
                                                </bdi>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-md-4">
                                    <p class="mt-2 mb-1 text-muted"><?= $lang['status']; ?>:</p>
                                    <div class="d-flex align-items-start">
                                        <?php
                                        $color = "pink";
                                        if ($data->task_status == "pending") {
                                            $color = "warning";
                                        } elseif ($data->task_status == "process") {
                                            $color = "info";
                                        } elseif ($data->task_status == "rejected") {
                                            $color = "danger";
                                        } elseif ($data->task_status == "ok") {
                                            $color = "success";
                                        }
                                        ?>
                                        <i class="mdi mdi-list-status font-18 text-<?= $color; ?> me-1"></i>
                                        <div class="w-100">
                                            <h5 class="mt-1 font-size-14">
                                                <?php
                                                if ($data->task_status == "pending") {
                                                    echo $lang['a_task_pending'];
                                                } elseif ($data->task_status == "process") {
                                                    echo $lang['a_task_process'];
                                                } elseif ($data->task_status == "rejected") {
                                                    echo $lang['a_task_rejected'];
                                                } elseif ($data->task_status == "ok") {
                                                    echo $lang['a_task_ok'];
                                                }
                                                ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->

                            <h5 class="mt-3"><?= $lang['description']; ?>:</h5>
                            <p class="text-muted mb-4"><?= $data->task_desc; ?></p>

                        </div>
                        <!-- end card-body-->
                    </div>
                    <!-- end card-->


                    <div class="card">
                        <div class="card-body">

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['a_report_action']; ?></h5>


                            <div class="row">
                                <div class="col">
                                    <?php
                                    if (!empty($dataDetail)) {
                                        foreach ($dataDetail as $loop) {
                                            ?>
                                            <!-- detail tasks -->
                                            <div class="mt-1">
                                                <a class="text-dark"
                                                   data-bs-toggle="collapse"
                                                   href="#task-detail-<?= $loop->detail_id; ?>"
                                                   aria-expanded="false"
                                                   aria-controls="task-detail-<?= $loop->detail_id; ?>">
                                                    <h5 class="mb-0">
                                                        <i class="mdi mdi-chevron-down font-18"></i>
                                                        <?= $loop->detail_title; ?>
                                                        <span class="text-muted font-11">(<?php
                                                            if ($loop->detail_status == "pending") {
                                                                echo '<span class="badge badge-soft-warning font-11">' . $lang['a_task_pending'] . "</span>";
                                                            } elseif ($loop->detail_status == "process") {
                                                                echo '<span class="badge badge-soft-info font-11">' . $lang['a_task_process'] . "</span>";
                                                            } elseif ($loop->detail_status == "rejected") {
                                                                echo '<span class="badge badge-soft-danger font-11">' . $lang['a_task_rejected'] . "</span>";
                                                            } elseif ($loop->detail_status == "ok") {
                                                                echo '<span class="badge badge-soft-success font-11">' . $lang['a_task_ok'] . "</span>";
                                                            } elseif ($loop->detail_status == "add") {
                                                                echo '<span class="badge badge-soft-primary font-11">' . $lang['a_task_add'] . "</span>";
                                                            } else {
                                                                echo '<span class="badge badge-soft-warning font-11">' . $lang['a_task_process'] . "</span>";
                                                            }
                                                            ?>)</span>
                                                    </h5>
                                                </a>

                                                <div class="collapse show"
                                                     id="task-detail-<?= $loop->detail_id; ?>">
                                                    <div class="card mb-0 shadow-none">
                                                        <div class="card-body pb-0"
                                                             id="task-list-<?= $loop->detail_id; ?>">

                                                            <div class="d-flex align-items-start">
                                                                <?php
                                                                $nameA = "";
                                                                $imageA = '';

                                                                if (!empty($dataAllAdmins)) {
                                                                    foreach ($dataAllAdmins as $dataAllAdmin) {
                                                                        if ($dataAllAdmin->admin_id == $loop->admin_id) {
                                                                            $nameA = $dataAllAdmin->admin_nickname;
                                                                            $imageA = $dataAllAdmin->admin_avatar;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <img class="me-2 rounded-circle"
                                                                     src="<?= Utils::fileExist($imageA, USER_AVATAR); ?>"
                                                                     alt="<?= $nameA; ?>"
                                                                     height="32"/>
                                                                <div class="w-100">
                                                                    <h5 class="mt-0">
                                                                        <?= $nameA; ?>
                                                                        <small class="text-muted float-end">
                                                                            <bdi>
                                                                                <?= Utils::getTimeCountry($Settings['data_time_format'], $loop->detail_submit_date); ?>
                                                                            </bdi>
                                                                        </small>
                                                                    </h5>
                                                                    <?= $loop->detail_desc; ?>

                                                                    <br/>

                                                                    <div class="row align-items-center mx-0">
                                                                        <?php
                                                                        if (!empty($loop->detail_attach) && @json_decode($loop->detail_attach)) {
                                                                            foreach (json_decode($loop->detail_attach) as $attachLoop) {
                                                                                ?>
                                                                                <div class="col-auto px-1 mt-2">
                                                                                    <a href="<?= $attachLoop; ?>"
                                                                                       class="d-block avatar-sm"
                                                                                       download="">
                                                                                         <span class="avatar-title bg-primary rounded"
                                                                                               style="text-transform: uppercase;">
                                                                                            <bdi><i class="dripicons-download"></i></bdi>
                                                                                         </span>
                                                                                    </a>
                                                                                </div>

                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <br/>
                                                                    <?php
                                                                    if (!empty($loop->detail_status_date) && !empty($loop->detail_status)) {
                                                                        ?>
                                                                        <a href="javascript: void(0);"
                                                                           class="text-muted font-13 d-inline-block mt-2">
                                                                            <i class="fas fa-clock"></i>
                                                                            <?= Utils::getTimeCountry($Settings['data_time_format'], $loop->detail_status_date); ?>
                                                                        </a>
                                                                        <?php
                                                                    }
                                                                    ?>


                                                                </div>
                                                            </div>


                                                        </div>
                                                        <!-- end card-body-->
                                                    </div>
                                                    <!-- end card -->
                                                </div>
                                                <!-- end collapse-->
                                            </div>
                                            <!-- end detail tasks -->

                                            <hr>
                                            <?php
                                        }
                                    }
                                    ?>


                                </div>
                                <!-- end col -->
                            </div>


                            <!-- end .border-->
                        </div>
                        <!-- end card-body-->
                    </div>


                    <?php
                    if ($data->task_status != "ok") {
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-widgets">
                                    <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                       aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                                </div>
                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_answer_to_task"]; ?></h5>

                                <div class="row show" id="cardCollpase1">

                                    <div class="col-lg-12">
                                        <div class="form-floating">
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
                                                        class="text-info"
                                                        id="length_xDescription">0</span>
                                            </small>
                                        </div>
                                    </div>


                                    <div class="col-12 mt-3">
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

                                    <div class="col-12 text-end">
                                        <button id="btnSend"
                                                data-tj-task-id="<?= $id; ?>"
                                                type="button"
                                                class="btn w-sm btn-soft-primary waves-effect shadow-none waves-light"
                                                data-style="zoom-in">
                                            <?= $lang["submit"]; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card-->
                        <?php
                    }
                    ?>
                </div>
                <!-- end col -->

                <div class="col-xl-4 col-lg-5">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <?php
                                if ($data->creator_id == $admin_id) {
                                    $arrayExist = ['ok', 'pending'];
                                    if (!in_array($data->task_status, $arrayExist)) {
                                        ?>
                                        <button id="ok"
                                                data-tj-task-id="<?= $id; ?>"
                                                type="button"
                                                data-style="zoom-in"
                                                class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                            <?= $lang['a_task_y_1']; ?>
                                        </button>
                                        <button id="rejected"
                                                data-tj-task-id="<?= $id; ?>"
                                                type="button"
                                                data-style="zoom-in"
                                                class="setSubmitBtn btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                            <?= $lang['a_task_y_2']; ?>
                                        </button>
                                        <?php
                                    }
                                }
                                ?>
                                <a href="/admin/tasks"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["attachment_files"]; ?></h5>

                            <?php
                            if (!empty($data->task_attach) && @json_decode($data->task_attach)) {
                                foreach (json_decode($data->task_attach) as $loop) {
                                    ?>
                                    <div class="card mb-1 shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="avatar-sm">
                                                            <span class="avatar-title badge-soft-primary text-primary rounded">
                                                                 <?= strtoupper(pathinfo($loop, PATHINFO_EXTENSION)); ?>
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="col ps-0">
                                                    <a href="<?= SITE_URL . $loop; ?>"
                                                       target="_self"
                                                       class="text-muted fw-bold">
                                                        <bdi>
                                                            <?= Utils::formatSizeUnits(filesize(getcwd() . $loop)); ?>
                                                        </bdi>
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Button -->
                                                    <a href="<?= SITE_URL . $loop; ?>"
                                                       download=""
                                                       class="btn btn-link font-16 text-muted">
                                                        <i class="dripicons-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="mdi mdi-alert-outline me-2"></i>
                                    <?= $lang['a_no_file_attach']; ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

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