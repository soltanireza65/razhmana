<?php
$pageSlug = "ngroup";
// permission_can_show

global $lang, $Settings;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All Group Notifications
         */
        $resultAllGroupNotifications = GNotification::getAllGroupNotifications();
        $dataAllGroupNotifications = [];
        if ($resultAllGroupNotifications->status == 200 && !empty($resultAllGroupNotifications->response)) {
            $dataAllGroupNotifications = $resultAllGroupNotifications->response;
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
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('chartJs', '/dist/libs/chart.js/Chart.bundle.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('charts', '/dist/js/admin/charts.init.js');
        enqueueScript('ngroup', '/dist/js/admin/ngroup/ngroup.init.js');

        getHeader($lang["notifications_group"], [
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
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['notifications_group']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/ngroup/add"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-plus-circle me-1"></i>
                                            <?= $lang['add_new']; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['users_type']; ?></th>
                                        <th><?= $lang['language']; ?></th>
                                        <th><?= $lang['date']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $chart_read = 0;
                                    $chart_unread = 0;
                                    if (!empty($dataAllGroupNotifications)) {
                                        $i = 1;
                                        $dataAllGroupNotifications = array_reverse($dataAllGroupNotifications);
                                        foreach ($dataAllGroupNotifications as $dataAllGroupNotificationsITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $dataAllGroupNotificationsITEM->ngroup_title; ?></td>
                                                <td><?php
                                                    $type = $dataAllGroupNotificationsITEM->user_type;
                                                    $user_status = $dataAllGroupNotificationsITEM->user_status;
                                                    if ($type == "driver") {
                                                        echo "<i class='mdi mdi-star-circle-outline text-info'></i>";
                                                        if ($user_status == "active") {
                                                            echo "<span class='badge badge-soft-success font-13'>" . $lang['driver'] . " - " . $lang['active'] . "</span>";
                                                        } elseif ($user_status == "guest") {
                                                            echo "<span class='badge badge-soft-warning font-13'>" . $lang['driver'] . " - " . $lang['guest'] . "</span>";
                                                        } elseif ($user_status == "suspend") {
                                                            echo "<span class='badge badge-outline-secondary font-13'>" . $lang['driver'] . " - " . $lang['suspend'] . "</span>";
                                                        } else {
                                                            echo "<span class='badge badge-soft-danger font-13'>" . $user_status . "</span>";
                                                        }
                                                    } elseif ($type == "businessman") {
                                                        ?>
                                                        <i class="mdi mdi-medal-outline text-primary"></i>
                                                        <?php
                                                        if ($user_status == "active") {
                                                            echo "<span class='badge badge-soft-success font-13'>" . $lang['businessman'] . " - " . $lang['active'] . "</span>";
                                                        } elseif ($user_status == "guest") {
                                                            echo "<span class='badge badge-soft-warning font-13'>" . $lang['businessman'] . " - " . $lang['guest'] . "</span>";
                                                        } elseif ($user_status == "suspend") {
                                                            echo "<span class='badge badge-outline-secondary font-13'>" . $lang['businessman'] . " - " . $lang['suspend'] . "</span>";
                                                        } else {
                                                            echo "<span class='badge badge-soft-danger font-13'>" . $user_status . "</span>";
                                                        }
                                                    }
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataLanguages)) {
                                                        foreach ($dataLanguages as $dataLanguagesITEM) {
                                                            if ($dataLanguagesITEM->slug == $dataAllGroupNotificationsITEM->ngroup_language) {
                                                                echo $lang[$dataLanguagesITEM->name];
                                                                break;
                                                            }
                                                        }
                                                    } ?>
                                                </td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], json_decode($dataAllGroupNotificationsITEM->ngroup_options)->date_create); ?></bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataAllGroupNotificationsITEM->ngroup_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $chart_read += 1;
                                                    } elseif ($dataAllGroupNotificationsITEM->ngroup_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $chart_unread += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataAllGroupNotificationsITEM->ngroup_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       data-notification-id="<?= $dataAllGroupNotificationsITEM->ngroup_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['show_detail']; ?>"
                                                       class="action-icon showNotification">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a target="_self"
                                                       href="/admin/ngroup/edit/<?= $dataAllGroupNotificationsITEM->ngroup_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['notifications_chart']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_message']; ?> :
                                    </strong>
                                    <span data-plugin="counterup"
                                          class="ms-2"><?= $chart_read + $chart_unread; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['active']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $chart_read; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['inactive']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $chart_unread; ?></span>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Start modal -->
            <div id="modalGroupDiv" class="modal fade" tabindex="-1" role="dialog"
                 aria-labelledby="standard-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalSenderG"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="modalTitleG"></div>
                            <hr>
                            <div id="modalDescG"></div>
                        </div>
                        <div class="modal-footer">
                            <bdi id="modalDateG"></bdi>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End modal -->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-group-notification') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => [
                            ['name' => $lang['active'], 'count' => $chart_read],
                            ['name' => $lang['inactive'], 'count' => $chart_unread],
                        ],
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
                $lang['help_ngroup_1'],
                $lang['help_ngroup_2'],
                $lang['help_ngroup_3'],
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