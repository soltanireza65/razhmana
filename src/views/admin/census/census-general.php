<?php
$pageSlug = "census";
// permission_can_show

global $lang, $antiXSS;

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
        enqueueStylesheet('persian-datepicker', '/dist/libs/persian-calendar/persian-datepicker.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('persian-date-min-js', '/dist/libs/persian-calendar//persian-date.min.js');
        enqueueScript('persian-datepicker-min-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('census-general', '/dist/js/admin/census/census-general.js');

        getHeader($lang['a_report_general'], [
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
            <div class="row" id="printDIV">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["a_report_general"]; ?></h5>
                            <div class="row">

                                <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="dateStart"
                                               placeholder="1374/04/04"
                                               value="<?= date('Y/m/d', strtotime('-1 month')); ?>">
                                        <label for="dateStart"><?= $lang['date_start']; ?></label>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="dateEnd"
                                               placeholder="Password">
                                        <label for="dateEnd"><?= $lang['date_end']; ?></label>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4 col-lg-4 d-flex justify-content-around mb-3">

                                    <button type="button"
                                            id="btnSubmit"
                                            data-style="zoom-in"
                                            class="btn btn-lg width-lg btn-soft-primary waves-effect waves-light">
                                        <?= $lang['submit']; ?>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <input id="startDefault" type="hidden">
            <input id="endDefault" type="hidden">

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