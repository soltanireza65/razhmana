<?php
$pageSlug = "a_task";
// permission_can_show

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        $resultTaskForMeNotEnd = Tasks::getTaskForMeNotEnd();
        $dataTaskForMeNotEnd = [];
        if ($resultTaskForMeNotEnd->status == 200) {
            $dataTaskForMeNotEnd = $resultTaskForMeNotEnd->response;
        }

        $resultTaskCreateIamNotEnd = Tasks::getTaskCreateIamNotEnd();
        $dataTaskCreateIamNotEnd = [];
        if ($resultTaskCreateIamNotEnd->status == 200) {
            $dataTaskCreateIamNotEnd = $resultTaskCreateIamNotEnd->response;
        }

        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        enqueueScript('task-add', '/dist/js/admin/tasks/tasks.init.js');

        getHeader($lang["a_task"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <style>
                .progress-anim {
                    animation: progres 4s 1 linear;
                }

                @keyframes progres {
                    0% {
                        width: 0%;
                    }
                    25% {
                        width: 50%;
                    }
                    50% {
                        width: 75%;
                    }
                    75% {
                        width: 85%;
                    }
                    100% {
                        width: 100%;
                    }
                }

                ;
            </style>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div class=" button-list d-grid">
                                        <a href="/admin/tasks/add"
                                           class="btn btn-sm bg-primary rounded ml-auto text-white">
                                            <i class="icon-plus align-middle text-white"></i>
                                            <span><?= $lang['add_new']; ?></span>
                                        </a>
                                    </div>
                                    <div>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-light dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="mdi mdi-filter-variant"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"
                                                   href="/admin/tasks/creator"><?= $lang['list_tasks_create_me']; ?></a>
                                                <a class="dropdown-item"
                                                   href="/admin/tasks/me"><?= $lang['list_tasks_for_me']; ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-3">

                                    <div class="nav flex-column nav-pills nav-pills-tab navtab-bg" id="v-pills-tab2"
                                         role="tablist" aria-orientation="vertical">
                                        <a class="nav-link active show mb-1"
                                           id="tab-task-1"
                                           data-bs-toggle="pill"
                                           href="#t-task-1"
                                           role="tab"
                                           aria-controls="t-task-1"
                                           aria-selected="true">
                                            <?= $lang['a_task_x_1']; ?>
                                        </a>
                                        <a class="nav-link mb-1"
                                           id="tab-task-2"
                                           data-bs-toggle="pill"
                                           href="#t-task-2"
                                           role="tab"
                                           aria-controls="t-task-2"
                                           aria-selected="false">
                                            <?= $lang['a_task_x_2']; ?>
                                        </a>
                                        <a class="nav-link mb-1"
                                           id="tab-task-3"
                                           data-bs-toggle="pill"
                                           href="#t-task-3"
                                           role="tab"
                                           aria-controls="t-task-3"
                                           aria-selected="false">
                                            <?= $lang['a_task_x_3']; ?>
                                        </a>

                                        <a class="nav-link mb-1"
                                           id="tab-task-4"
                                           data-bs-toggle="pill"
                                           href="#t-task-4"
                                           role="tab"
                                           aria-controls="t-task-4"
                                           aria-selected="false">
                                            <?= $lang['a_task_x_4']; ?>
                                        </a>
                                        <a class="nav-link mb-1"
                                           id="tab-task-5"
                                           data-bs-toggle="pill"
                                           href="#t-task-5"
                                           role="tab"
                                           aria-controls="t-task-5"
                                           aria-selected="false">
                                            <?= $lang['a_task_x_5']; ?>
                                        </a>
                                        <a class="nav-link mb-1"
                                           id="tab-task-6"
                                           data-bs-toggle="pill"
                                           href="#t-task-6"
                                           role="tab"
                                           aria-controls="t-task-6"
                                           aria-selected="false">
                                            <?= $lang['a_task_x_6']; ?>
                                        </a>
                                    </div>
                                </div> <!-- end col -->


                                <div class="col-sm-9">
                                    <div class="tab-content pt-0" id="v-pills-tabContent">

                                        <!--Start Tab 1-->
                                        <div class="tab-pane fade active show"
                                             id="t-task-1"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-1">
                                            <?php
                                            $flas0 = true;
                                            if (!empty($dataTaskForMeNotEnd)) {
                                                foreach ($dataTaskForMeNotEnd as $loop) {
                                                    if ($loop->task_status == "pending") {
                                                        $flas0 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas0) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 1-->

                                        <!--Start Tab 2-->
                                        <div class="tab-pane fade"
                                             id="t-task-2"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-2">
                                            <?php
                                            $flas1 = true;
                                            if (!empty($dataTaskForMeNotEnd)) {
                                                foreach ($dataTaskForMeNotEnd as $loop) {
                                                    if ($loop->task_status == "process") {
                                                        $flas1 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas1) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 2-->


                                        <!--Start Tab 3-->
                                        <div class="tab-pane fade"
                                             id="t-task-3"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-3">
                                            <?php
                                            $flas2 = true;
                                            if (!empty($dataTaskForMeNotEnd)) {
                                                foreach ($dataTaskForMeNotEnd as $loop) {
                                                    if ($loop->task_status == "rejected") {
                                                        $flas2 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas2) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 3-->


                                        <!--Start Tab 4-->
                                        <div class="tab-pane fade"
                                             id="t-task-4"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-4">
                                            <?php
                                            $flas3 = true;
                                            if (!empty($dataTaskCreateIamNotEnd)) {
                                                foreach ($dataTaskCreateIamNotEnd as $loop) {
                                                    if ($loop->task_status == "pending") {
                                                        $flas3 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas3) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 4-->

                                        <!--Start Tab 5-->
                                        <div class="tab-pane fade"
                                             id="t-task-5"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-5">
                                            <?php
                                            $flas4 = true;
                                            if (!empty($dataTaskCreateIamNotEnd)) {
                                                foreach ($dataTaskCreateIamNotEnd as $loop) {
                                                    if ($loop->task_status == "process") {
                                                        $flas4 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas4) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 6-->

                                        <!--Start Tab 6-->
                                        <div class="tab-pane fade"
                                             id="t-task-6"
                                             role="tabpanel"
                                             aria-labelledby="tab-task-6">
                                            <?php
                                            $flas5 = true;
                                            if (!empty($dataTaskCreateIamNotEnd)) {
                                                foreach ($dataTaskCreateIamNotEnd as $loop) {
                                                    if ($loop->task_status == "rejected") {
                                                        $flas5 = false;
                                                        ?>
                                                        <div class="row justify-content-sm-between align-content-center task-item mb-2">
                                                            <div class="col-lg-5 mb-2">
                                                                <?= $loop->task_title; ?>
                                                            </div>
                                                            <!-- end col -->
                                                            <div class="col-lg-1">
                                                                <div id="tooltips-container4">
                                                                    <img src="<?php
                                                                    $r = array_search($loop->admin_id, array_column($dataAllAdmins, 'admin_id'));
                                                                    echo Utils::fileExist(@$dataAllAdmins[$r]->admin_avatar, USER_AVATAR);
                                                                    ?>"
                                                                         alt="image"
                                                                         class="avatar-xs rounded-circle"
                                                                         data-bs-container="#tooltips-container4"
                                                                         data-bs-toggle="tooltip"
                                                                         data-bs-placement="bottom"
                                                                         title="<?= @$dataAllAdmins[$r]->admin_nickname; ?>"/>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-3">

                                                                <i class="mdi mdi-calendar-month-outline font-16"></i>
                                                                <span class="my-timer-class"
                                                                      id="timer-id-<?= $loop->task_id; ?>"
                                                                      data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                                      data-tj-end-time="<?= $loop->task_end_date; ?>"></span>


                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                                         role="progressbar"
                                                                         data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                                         aria-label="Animated striped example"
                                                                         aria-valuenow="75"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"
                                                                         style=""></div>
                                                                </div>
                                                                <!-- end .d-flex-->
                                                            </div>
                                                            <div class="col-lg-2 d-flex justify-content-between align-content-center">
                                                                <div>
                                                                    <?php
                                                                    if ($loop->task_priority == "important") {
                                                                        echo '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
                                                                    } elseif ($loop->task_priority == "critical") {
                                                                        echo '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
                                                                    } elseif ($loop->task_priority == "high") {
                                                                        echo '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
                                                                    } elseif ($loop->task_priority == "medium") {
                                                                        echo '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
                                                                    } elseif ($loop->task_priority == "low") {
                                                                        echo '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
                                                                    } elseif ($loop->task_priority == "informational") {
                                                                        echo '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-soft-secondary p-1">XXX</span>';
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <a class="showNotification action-icon"
                                                                   href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['show_detail']; ?>">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>

                                                            </div>
                                                            <!-- end col -->
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if ($flas5) {
                                                ?>
                                                <div class="alert alert-info" role="alert">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <?= $lang['a_there_are_currently_no_items_found']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <!--End Tab 6-->

                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row-->
                        </div>
                    </div>
                </div>
            </div>

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