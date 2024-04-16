<?php
$pageSlug = "brands";
// permission_can_show

global $lang;

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
         * Get All Brands
         */
        $result = PosterC::getAllBrandsFromTabel();
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response;
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


        getHeader($lang["a_list_brands"], [
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
                                    <h4 class="page-title"><?= $lang['a_list_brands']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/category/brand/add"
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
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['a_type_2']; ?></th>
                                        <th><?= $lang['priority_show']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_active = 0;
                                    $status_inactive = 0;
                                    $status_user = 0;
                                    if (!empty($data)) {
                                        $i = 1;
                                        $data = array_reverse($data);
                                        foreach ($data as $loop) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td class="table-user text-start">
                                                    <img src="<?= Utils::fileExist($loop->brand_image, BOX_EMPTY); ?>"
                                                         alt=" <?= $loop->brand_id; ?>"
                                                         class="me-2 rounded-circle">
                                                    <a href="/admin/category/brand/edit/<?= $loop->brand_priority; ?>"
                                                       class="text-body fw-normal">
                                                        <?= (!empty(array_column(json_decode($loop->brand_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                            array_column(json_decode($loop->brand_name, true), 'value', 'slug')[$_COOKIE['language']] : $loop->brand_id; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if ($loop->brand_type == "truck") {
                                                        echo "<span class='badge badge-outline-info font-12'>" . $lang['a_truck'] . "</span>";
                                                    } elseif ($loop->brand_type == "trailer") {
                                                        echo "<span class='badge badge-outline-primary font-12'>" . $lang['a_trailer'] . "</span>";
                                                    } else {
                                                        echo "<span class='badge badge-outline-pink font-12'>" . $loop->brand_type . "</span>";
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?= $loop->brand_priority; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($loop->brand_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $status_active += 1;
                                                    } elseif ($loop->brand_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $status_inactive += 1;
                                                    } elseif ($loop->brand_status == "user") {
                                                        echo "<span class='badge badge-soft-info font-12'>" . $lang['a_user_creator'] . "</span>";
                                                        $status_user += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-pink font-12'>" . $loop->brand_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       href="/admin/category/brand/edit/<?= $loop->brand_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit_2']; ?>"
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
                                    <h4 class="page-title"><?= $lang['a_chart_category']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['a_all_category']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_user + $status_inactive + $status_active; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['active']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_active; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['inactive']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_inactive; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['a_user_creator']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_user; ?></span>
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
                            ['name' => $lang['active'], 'count' => $status_active],
                            ['name' => $lang['inactive'], 'count' => $status_inactive],
                            ['name' => $lang['a_user_creator'], 'count' => $status_user],
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