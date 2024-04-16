<?php
$pageSlug = "departments";
// permission_can_show

global $lang,$antiXSS;

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

        $language = $antiXSS->xss_clean($_COOKIE['language']);

        /**
         * Get All Departments
         */
        $resultAllDepartments = ATicket::getAllDepartments("");
        $dataAllDepartments = [];
        if ($resultAllDepartments->status == 200 && !empty($resultAllDepartments->response)) {
            $dataAllDepartments = $resultAllDepartments->response;
        }


        /**
         * Get All Tickets
         */
        $countTickets = 0;
        $resultAllTickets = ATicket::getAllTickets("");
        $dataAllTickets = [];
        if ($resultAllTickets->status == 200 && !empty($resultAllTickets->response)) {
            $dataAllTickets = $resultAllTickets->response;
            $countTickets = count($dataAllTickets);
        }


        /**
         * create array from departments info
         */
        $d_temp = [];
        if (!empty($dataAllDepartments)) {
            foreach ($dataAllDepartments as $index => $dataAllDepartmentsIOOPP) {
                $d_temp[$index]['name'] = $dataAllDepartmentsIOOPP->department_name;
                $d_temp[$index]['id'] = $dataAllDepartmentsIOOPP->department_id;
                $d_temp[$index]['count'] = 0;
            }
        }


        /**
         * Get charts From All tickets Room By Array Created
         */
        if (!empty($dataAllTickets)) {
            foreach ($dataAllTickets as $dataAllTicketsLOOPP) {

                if (!empty($d_temp)) {
                    foreach ($d_temp as $index => $d_tempLOOP) {
                        if ($d_tempLOOP['id'] == $dataAllTicketsLOOPP->department_id) {
                            $d_temp[$index]['count'] += 1;
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

        getHeader($lang["departments"], [
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
                                    <h4 class="page-title"><?= $lang['departments']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/department/add"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-plus-circle me-1"></i>
                                            <?= $lang['add_new']; ?>
                                        </a>
                                    </div>
                                </div><!-- end col-->
                            </div>


                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title_category']; ?></th>
                                        <th><?= $lang['category_type']; ?></th>
                                        <th><?= $lang['count_tickets']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_active = 0;
                                    $status_inactive = 0;
                                    if (!empty($dataAllDepartments)) {
                                        $i = 1;
                                        $dataAllDepartments = array_reverse($dataAllDepartments);
                                        foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td class="table-user text-start">
                                                    <a href="/admin/department/edit/<?= $dataAllDepartmentsITEM->department_id; ?>"
                                                       class="text-body fw-normal">
                                                        <?= (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                            array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : ""; ?>
                                                    </a>
                                                </td>
                                                <td><?= (array_key_exists($dataAllDepartmentsITEM->department_type, $lang)) ? $lang[$dataAllDepartmentsITEM->department_type] : ""; ?></td>
                                                <td>
                                                    <span data-plugin="counterup">
                                                    <?php
                                                    if (!empty($d_temp)) {
                                                        foreach ($d_temp as $LOOPP) {
                                                            if ($LOOPP['id'] == $dataAllDepartmentsITEM->department_id) {
                                                                echo $LOOPP['count'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                        </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataAllDepartmentsITEM->department_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $status_active += 1;
                                                    } elseif ($dataAllDepartmentsITEM->department_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $status_inactive += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataAllDepartmentsITEM->department_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       href="/admin/department/edit/<?= $dataAllDepartmentsITEM->department_id; ?>"
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
                                    } else {
                                        ?>
                                        <tr>
                                            <td class="text-center text-warning"
                                                colspan="5"><?= $lang['no_departments_in_table']; ?></td>
                                            <td hidden></td>
                                            <td hidden></td>
                                            <td hidden></td>
                                            <td hidden></td>
                                        </tr>
                                        <?php
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
                                    <h4 class="page-title"><?= $lang['departments_chart']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_departments']; ?> :
                                    </strong>
                                    <span data-plugin="counterup"
                                          class="ms-2"><?= $status_active + $status_inactive; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['department_active']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $status_active; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['department_inactive']; ?> :
                                    </strong>
                                    <span data-plugin="counterup" class="ms-2"><?= $status_inactive; ?></span>
                                </p>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => [
                            ['name'=> $lang['department_active'],'count'=>$status_active],
                            ['name'=> $lang['department_inactive'],'count'=>$status_inactive],
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
                    $lang['help_department_1'],
                    $lang['help_department_2'],
                    $lang['help_department_3'],
                    $lang['help_department_4'],
                    $lang['help_department_5'],
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