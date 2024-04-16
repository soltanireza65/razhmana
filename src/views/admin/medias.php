<?php
$pageSlug = "medias";
// permission_can_insert

global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'header-footer.php';

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
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_insert == "yes")) {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        if (!file_exists(getcwd() . SITE_MEDIAS) && !is_dir(getcwd() . SITE_MEDIAS)) {
            mkdir(getcwd() . SITE_MEDIAS);
        }

        $mediasDir = SITE_ROOT . SITE_MEDIAS;
        $mediasUrl = SITE_URL . SITE_MEDIAS;
        $mediasFiles = scandir($mediasDir, 1);

        file_put_contents('test.json' ,json_encode($mediasFiles,JSON_PRETTY_PRINT));

//        foreach ($mediasDir as $files){
////    echo filetype((($mydir."/".$files)));
//            echo "<br>";
//            if (is_dir($mydir."/".$files))
//                echo ("$files is a directory");
//            else
//                echo ("$files is not a directory");
//        }


        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
//        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('medias', '/dist/js/admin/medias.init.js');

        getHeader($lang["medias"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_insert',
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
                                    <h4 class="page-title"><?= $lang['medias']; ?></h4>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='25' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>

                                        <th class="border-0"><?= $lang['name']; ?></th>
                                        <th class="border-0"><?= $lang['name']; ?></th>
                                        <th class="border-0"><?= $lang['name']; ?></th>
                                        <th class="border-0"><?= $lang['name']; ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <!-- Left sidebar -->
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['medias_chart']; ?></h4>
                                </div>
                            </div>
                            <div class="mail-list mt-3">
                                <table class="table mb-0">
                                    <tbody>
                                    <?php
                                    $count = 0;
                                    if (!empty($array)) {
                                        foreach ($array as $index => $loop) {
                                            ?>
                                            <tr>
                                                <th scope="row"><?= $index; ?></th>
                                                <td><?= $loop; ?></td>
                                            </tr>
                                            <?php
                                            $count += $loop;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row"><?= $lang['all_medias'] ?></th>
                                        <td><?= $count; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-5">
                                <h6 class="text-uppercase mt-3"><?= $lang['hard_space']; ?></h6>
                                <div class="progress my-2 progress-sm">
                                    <?php
                                    $d = disk_total_space("/");
                                    $df = disk_free_space("/");
                                    $ds = $d - $df;
                                    $darsad = ($ds * 100) / $d;
                                    ?>
                                    <div class="progress-bar progress-lg bg-success" role="progressbar"
                                         style="width: <?= $darsad; ?>%" aria-valuenow="<?= $darsad; ?>"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                                <p class="text-muted font-12 mb-0">
                                    <?= $lang['hard_free']; ?>
                                    <strong>
                                        <bdi>
                                            <?= Utils::formatSizeUnits($df); ?>
                                        </bdi>
                                    </strong>
                                </p>

                                <p class="text-muted font-12 mb-0">
                                    <?= $lang['hard_used']; ?>
                                    <strong>
                                        <bdi>
                                            <?= Utils::formatSizeUnits($ds); ?>
                                        </bdi>
                                    </strong>
                                </p>
                            </div>
                            <!-- End Left sidebar -->
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["add_media"]; ?></h5>
                            <form action="/" method="post" class="dropzone" id="attachmentsDropzone"
                                  data-plugin="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file">
                                </div>
                                <div class="dz-message needsclick">
                                    <i class="h1 text-muted dripicons-cloud-upload"></i>
                                    <h3><?= $lang["drop_files"]; ?></h3>
                                </div>
                            </form>
                            <div class="row justify-content-between mt-3">

                                <div class="col-12">
                                    <div
                                        class="text-lg-end my-1 my-lg-0 d-flex justify-content-between align-items-center">
                                        <input type="text" id="media-name" style="width: 70%;background: #ebebeb;padding: 10px;border-radius: 10px;outline: none !important;
   border: 1px solid #919191;" placeholder="<?= $lang['medias_name_placeholder'] ?>">
                                        <button style="padding: 10px;border-radius: 10px;" id="submitUpload" data-style="zoom-in"
                                                class="submitUpload btn btn-sm btn-outline-primary waves-effect waves-light ">
                                            <i class="mdi mdi-plus-circle me-1"></i>
                                            <?= $lang['add_new']; ?>
                                        </button>
                                    </div>
                                </div><!-- end col-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'mobile_invalid' => $lang['mobile_invalid'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
                        'token_error' => $lang['token_error'],
                    ];
                    print_r(json_encode($var_lang));
                    ?>';

                let file_names = '<?=json_encode($mediasFiles)?>';
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
// end roles 3

        getFooter(
            [
                $lang['help_media_1'],
                $lang['help_media_2'],
                $lang['help_media_3'],
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
?>