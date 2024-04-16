<?php
$pageSlug = "tickets";
// permission_can_show

global $lang,$antiXSS,$Settings;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All Tickets
         */
        $resultAllTickets = ATicket::getAllTickets("");
        $dataAllTickets = [];
        if ($resultAllTickets->status == 200 && !empty($resultAllTickets->response)) {
            $dataAllTickets = $resultAllTickets->response;
        }


        /**
         * Get All Departments
         */
        $resultAllDepartments = ATicket::getAllDepartments("");
        $dataAllDepartments = [];
        if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
            $dataAllDepartments = $resultAllDepartments->response;
        }


        /**
         * Get All Users
         */
        $resultAllUsers = AUser::getAllUsers();
        $dataAllUsers = [];
        if ($resultAllUsers->status == 200 && !empty($resultAllUsers->response)) {
            $dataAllUsers = $resultAllUsers->response;
        }


        $d_temp = [];
        if (!empty($dataAllDepartments)) {
            foreach ($dataAllDepartments as $index => $dataAllDepartmentsIOOPP) {
                $d_temp[$index]['name'] = array_column(json_decode($dataAllDepartmentsIOOPP->department_name, true), "value", "slug")[$_COOKIE['language']];
                $d_temp[$index]['id'] = $dataAllDepartmentsIOOPP->department_id;
                $d_temp[$index]['count'] = 0;
            }
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

        getHeader($lang["tickets"], [
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
                                    <h4 class="page-title"><?= $lang['tickets']; ?></h4>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['name_and_family']; ?></th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['department']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_open = 0;
                                    $status_close = 0;
                                    if (!empty($dataAllTickets)) {
                                        $i = 1;
                                        $dataAllTickets = array_reverse($dataAllTickets);
                                        foreach ($dataAllTickets as $dataAllTicketsITEM) {

                                            if (!empty($dataAllDepartments)) {
                                                $temp = array_column($dataAllDepartments, 'department_name', 'department_id')[$dataAllTicketsITEM->department_id];
                                                $tempType = array_column($dataAllDepartments, 'department_type', 'department_id')[$dataAllTicketsITEM->department_id];

                                                if (!empty($temp) && !empty($tempType)) {


                                                    $departmentName = (!empty(array_column(json_decode($temp, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                        array_column(json_decode($temp, true), 'value', 'slug')[$_COOKIE['language']] : "";

                                                    foreach ($d_temp as $index => $d_tempLOOP) {
                                                        if ($d_tempLOOP['id'] == $dataAllTicketsITEM->department_id) {
                                                            $d_temp[$index]['count'] += 1;
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td class="table-user text-start">
                                                            <img src="<?= Utils::fileExist($dataAllTicketsITEM->user_id, USER_AVATAR); ?>"
                                                                 alt=""
                                                                 class="me-2 rounded-circle">
                                                            <a href="/admin/users/info/<?= $dataAllTicketsITEM->user_id; ?>"
                                                               class="text-body fw-normal">
                                                                <?php
                                                                $userName = $lang['guest_user'];
                                                                if (!empty(array_column($dataAllUsers, 'user_firstname', 'user_id')[$dataAllTicketsITEM->user_id])) {
                                                                    $userNameF = array_column($dataAllUsers, 'user_firstname', 'user_id')[$dataAllTicketsITEM->user_id];
                                                                    $userNameL = array_column($dataAllUsers, 'user_lastname', 'user_id')[$dataAllTicketsITEM->user_id];
                                                                    $userName = Security::decrypt($userNameF) . " " . Security::decrypt($userNameL);
                                                                }
                                                                echo $userName;
                                                                ?>
                                                            </a>
                                                        </td>
                                                        <td><?= $dataAllTicketsITEM->ticket_title; ?></td>
                                                        <td>
                                                            <?= $departmentName; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($dataAllTicketsITEM->ticket_status == "open") {
                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['ticket_open'] . "</span>";
                                                                $status_open += 1;
                                                            } elseif ($dataAllTicketsITEM->ticket_status == "close") {
                                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['ticket_close'] . "</span>";
                                                                $status_close += 1;
                                                            } else {
                                                                echo "<span class='badge badge-soft-warning font-12'>" . $dataAllTicketsITEM->ticket_status . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataAllTicketsITEM->ticket_submit_date); ?></bdi></td>
                                                        <td>
                                                            <a href="/admin/ticket/open/<?= $dataAllTicketsITEM->ticket_id; ?>"
                                                               target="_self"
                                                               data-bs-toggle="tooltip"
                                                               data-bs-placement="top"
                                                               title="<?= $lang['show_detail']; ?>"
                                                               class="action-icon">
                                                                <i class="mdi mdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
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

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">

                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['tickets_chart']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">


                                <p class="text-muted mb-3 font-14">
                                    <strong>
                                        <?= $lang['tickets_department']; ?>
                                    </strong>
                                </p>
                                <?php
                                if (!empty($d_temp)) {
                                    foreach ($d_temp as $d_tempLOOOP) {

                                        ?>
                                        <p class="text-muted mb-2 font-13  d-flex">
                                            <strong>
                                                <?= $d_tempLOOOP['name']; ?> :
                                            </strong>
                                            <span data-plugin="counterup"
                                                  class="ms-2"><?= $d_tempLOOOP['count']; ?></span>
                                        </p>
                                        <?php

                                    }
                                }
                                ?>
                                <hr>
                                <p class="text-muted mb-3 font-14">
                                    <strong>
                                        <?= $lang['status_tickets']; ?>
                                    </strong>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_tickets']; ?> :
                                    </strong>
                                    <span data-plugin="counterup"
                                          class="ms-2"><?= $status_open + $status_close; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['ticket_open']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $status_open; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['ticket_close']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $status_close; ?></span>
                                </p>

                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => $d_temp,
                        'ticket_open' => $lang['ticket_open'],
                        'ticket_close' => $lang['ticket_close'],
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