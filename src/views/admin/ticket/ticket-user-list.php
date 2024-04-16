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

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get User َ All Tickets
         */
        $resultUserOpenTicketsById = ATicket::getUserOpenTicketsById($id);
        $dataUserOpenTicketsById = [];
        if ($resultUserOpenTicketsById->status == 200 && !empty($resultUserOpenTicketsById->response)) {
            $dataUserOpenTicketsById = $resultUserOpenTicketsById->response;
        }


        /**
         * Get All Departments
         */
        $resultAllDepartments = ATicket::getAllDepartments("");
        $dataAllDepartments = [];
        if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
            $dataAllDepartments = $resultAllDepartments->response;
        }


        $resultUserInfoById = AUser::getUserInfoById($id);
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

        $arrayTemp = [];
        if (!empty($dataUserOpenTicketsById)) {
            foreach ($dataUserOpenTicketsById as $dataUserOpenTicketsByIdLOOP) {
                if (!empty($dataAllDepartments)) {
                    foreach ($dataAllDepartments as $dataAllDepartmentsLOOP) {
                        if ($dataUserOpenTicketsByIdLOOP->department_id == $dataAllDepartmentsLOOP->department_id) {
                            if (isset($arrayTemp[$dataUserOpenTicketsByIdLOOP->department_id])) {
                                $arrayTemp[$dataUserOpenTicketsByIdLOOP->department_id]['count'] += 1;
                            } else {
                                $arrayTemp[$dataUserOpenTicketsByIdLOOP->department_id]['count'] = 1;
//                                $arrayTemp[$dataUserOpenTicketsByIdLOOP->department_id]['name'] = $dataAllDepartmentsLOOP->department_name;
                                $arrayTemp[$dataUserOpenTicketsByIdLOOP->department_id]['name'] = (!empty(array_column(json_decode($dataAllDepartmentsLOOP->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                    array_column(json_decode($dataAllDepartmentsLOOP->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";;
                            }
                        }
                    }
                }

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


        getHeader($lang["a_ticket_user"], [
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

                <div class="col-sm-12 col-md-12 col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-uppercase bg-light p-2 mt-0">
                                <a href="/admin/users/info/<?= $id; ?>"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="<?= $lang['user_info']; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline"></i>
                                </a>
                                <?= $lang['all_info']; ?>
                            </h5>


                            <div class="d-flex align-items-start mb-3">
                                <img src="<?= USER_AVATAR; ?>"
                                     class="me-2 avatar-md rounded-circle" height="42"
                                     id="ticketID"
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
                                       href="/admin/users/info/<?= $id; ?>"
                                       class="btn btn-soft-primary btn-sm  waves-effect waves-light">
                                        <?= $lang['user_info']; ?>
                                    </a>
                                    <a target="_self"
                                       href="/admin/ticket/add/<?= $id; ?>"
                                       class="btn btn-soft-info btn-sm  waves-effect waves-light">
                                        <?= $lang['create_ticket']; ?>
                                    </a>
                                </div>
                            </div>


                            <h5 class="mb-3 text-uppercase bg-light p-2 mt-3">
                                <i class="mdi mdi-chart-bar me-1"></i>
                                <?= $lang['departments_chart']; ?>
                            </h5>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">
                                <?php
                                if (!empty($arrayTemp)) {
                                    foreach ($arrayTemp as $loop) {
                                        ?>
                                        <p class="text-muted mb-2 font-13">
                                            <strong>
                                                <?= $loop['name']; ?> :
                                            </strong>
                                            <span data-plugin="counterup" class="ms-2"><?= $loop['count'] ?></span>
                                        </p>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-uppercase bg-light p-2 mt-0">
                                <i class="mdi mdi-chat me-1"></i>
                                <?= $lang['a_ticket_user']; ?>
                            </h5>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['departments']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    if (!empty($dataUserOpenTicketsById)) {
                                        $i = 1;
                                        foreach ($dataUserOpenTicketsById as $dataUserOpenTicketsByIdITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $dataUserOpenTicketsByIdITEM->ticket_title; ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataAllDepartments)) {
                                                        foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                            if ($dataAllDepartmentsITEM->department_id == $dataUserOpenTicketsByIdITEM->department_id) {
                                                                //  echo $dataAllDepartmentsITEM->department_name;
                                                                echo (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                    array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td><bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataUserOpenTicketsByIdITEM->ticket_submit_date); ?></bdi></td>
                                                <td>
                                                    <?php
                                                    if ($dataUserOpenTicketsByIdITEM->ticket_status == "open") {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['ticket_open'] . "</span>";
                                                    } elseif ($dataUserOpenTicketsByIdITEM->ticket_status == "close") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['ticket_close'] . "</span>";
                                                    } else {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $dataUserOpenTicketsByIdITEM->ticket_status . "</span>";
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <a href="/admin/ticket/open/<?= $dataUserOpenTicketsByIdITEM->ticket_id; ?>"
                                                       target="_self"
                                                       class="action-icon">
                                                        <i data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['show_detail']; ?>"
                                                           class="mdi mdi-eye"></i>
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

            </div>
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => ($arrayTemp)
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
                $lang['help_user_ticket'],
                $lang['help_user_ticket_2'],
                $lang['help_user_ticket_3'],
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

