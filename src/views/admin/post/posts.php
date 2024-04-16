<?php
$pageSlug = "posts";
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

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');

        getHeader($lang["list_posts"], [
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
                                    <h4 class="page-title"><?= $lang['list_posts']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/post/add"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-plus-circle me-1"></i>
                                            <?= $lang['add_new']; ?>
                                        </a>
                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='25' data-order='[[ 0, "desc" ]]'
                                       data-tj-col="post_id,post_title,category_name,post_language,post_status,post_submit_time"
                                       data-tj-address="dt-posts"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['title_category']; ?></th>
                                        <th><?= $lang['language']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_publish = 0;
                                    $status_draft = 0;
                                    if (!empty($dataPosts)) {
                                        $i = 1;
                                        foreach ($dataPosts as $dataPostsTEMP) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= mb_strimwidth($dataPostsTEMP->post_title, 0, 50, '...'); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataCategories)) {
                                                        foreach ($dataCategories as $dataCategoriesEMP) {
                                                            if ($dataCategoriesEMP->category_id == $dataPostsTEMP->category_id) {
                                                                echo $dataCategoriesEMP->category_name;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    if (!empty($dataAllAdmins)) {
                                                        foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                            if ($dataAllAdminsITEM->admin_id == $dataPostsTEMP->admin_id) {
                                                                echo $dataAllAdminsITEM->admin_nickname;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry('d F Y', (int)json_decode($dataPostsTEMP->post_options)->create); ?></bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataPostsTEMP->post_status == "published") {
                                                        echo "<span class='badge badge-soft-success font-13'>" . $lang['published'] . "</span>";
                                                        $status_publish += 1;
                                                    } elseif ($dataPostsTEMP->post_status == "draft") {
                                                        echo "<span class='badge badge-soft-warning font-13'>" . $lang['draft'] . "</span>";
                                                        $status_draft += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-13'>" . $dataPostsTEMP->post_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="<?= SITE_URL . '/blog_p/' . $dataPostsTEMP->post_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['preview']; ?>"
                                                       target="_blank"
                                                       class="action-icon">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a target="_self"
                                                       href="/admin/post/edit/<?= $dataPostsTEMP->post_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
                                                    </a>
                                                    <a href="/admin/post/delete/<?= $dataPostsTEMP->post_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['delete_post']; ?>"
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
            </div>
            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-posts'] = "dt-posts-44"; ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_post_1'],
                $lang['help_post_2'],
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