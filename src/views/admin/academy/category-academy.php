<?php
$pageSlug = "academy_c";
// permission_can_show

global $lang;

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
         * Get All Academy Categories
         */
        $resultAllCategories = Academy::getAllCategories();
        $dataCategories = [];
        if ($resultAllCategories->status == 200 && !empty($resultAllCategories->response)) {
            $dataCategories = $resultAllCategories->response;
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

        getHeader($lang['academy_category_list'], [
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
                                    <h4 class="page-title"><?= $lang['academy_category_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/category/academy/add"
                                           class="btn btn-outline-primary waves-effect waves-light btn-sm"><i
                                                    class="mdi mdi-plus-circle me-1"></i><?= $lang['add_new']; ?></a>
                                    </div>
                                </div>
                            </div> <!-- end row -->

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['a_parent_category']; ?></th>
                                        <th><?= $lang['language']; ?></th>
                                        <th><?= $lang['priority_show']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_active = 0;
                                    $status_inactive = 0;
                                    if (!empty($dataCategories)) {
                                        $i = 1;
                                        $dataCategories = array_reverse($dataCategories);
                                        foreach ($dataCategories as $dataCategoriesTEMP) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $dataCategoriesTEMP->category_name; ?></td>
                                                <td><?php
                                                    if (is_null($dataCategoriesTEMP->parent_id) ||
                                                        empty($dataCategoriesTEMP->parent_id)) {
                                                        echo $lang['a_status_parent_category'];
                                                    } else {
                                                        foreach ($dataCategories as $loop) {
                                                            if ($loop->category_id == $dataCategoriesTEMP->parent_id) {
                                                                echo '<a href="/admin/category/academy/edit/' . $loop->category_id . '">' . $loop->category_name . '</a>';
                                                            }
                                                        }
                                                    }
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    if ($dataCategoriesTEMP->category_language == "fa_IR") {
                                                        echo $lang['fa_IR'];
                                                    } elseif ($dataCategoriesTEMP->category_language == "en_US") {
                                                        echo $lang['en_US'];
                                                    } elseif ($dataCategoriesTEMP->category_language == "tr_Tr") {
                                                        echo $lang['tr_Tr'];
                                                    } elseif ($dataCategoriesTEMP->category_language == "ru_RU") {
                                                        echo $lang['ru_RU'];
                                                    } else {
                                                        echo $dataCategoriesTEMP->category_language;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $dataCategoriesTEMP->category_priority; ?></td>
                                                <td>
                                                    <?php
                                                    if ($dataCategoriesTEMP->category_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $status_active += 1;
                                                    } elseif ($dataCategoriesTEMP->category_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $status_inactive += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataCategoriesTEMP->category_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       href="/admin/category/academy/edit/<?= $dataCategoriesTEMP->category_id; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
                                                    </a>
                                                    <a target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['delete']; ?>"
                                                       href="/admin/category/academy/delete/<?= $dataCategoriesTEMP->category_id; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-delete"></i>
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
                                    <h4 class="page-title"><?= $lang['categories_chart']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_categories']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_active + $status_inactive; ?></span>
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
?>