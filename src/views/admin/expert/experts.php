<?php
$pageSlug = "a_experts";
// permission_can_show

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


        $data = Expert::getAllExperts();

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
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

        getHeader($lang["a_experts_list"], [
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
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['a_experts_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/expert/add"
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
                                        <th><?= $lang['name_and_family']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_active = 0;
                                    $status_inactive = 0;
                                    if (!empty($data)) {
                                        $i = 1;
                                        $data = array_reverse($data);
                                        foreach ($data as $loop) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $loop->expert_firstname . " " . $loop->expert_lastname; ?></td>
                                                <td>
                                                    <?php
                                                    if ($loop->expert_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $status_active += 1;
                                                    } elseif ($loop->expert_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $status_inactive += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $loop->expert_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       href="/admin/expert/edit/<?= $loop->expert_id; ?>"
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
            </div>
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => [
                            ['name' => $lang['active'], 'count' => $status_inactive],
                            ['name' => $lang['inactive'], 'count' => $status_active],
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
                $lang['help_cate_1'],
                $lang['help_cate_2'],
                $lang['help_cate_3'],
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