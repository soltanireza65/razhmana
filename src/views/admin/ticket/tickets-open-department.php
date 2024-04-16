<?php
$pageSlug = "xx";
// permission_can_show

global $lang, $antiXSS, $Settings;

use MJ\Utils\Utils;

include_once getcwd() . '/views/admin/header-footer.php';


$deId = (int)$antiXSS->xss_clean($_REQUEST['deId']);

$xx_department=ATicket::getDepartmentById($deId);
if($xx_department->status==200 && isset($xx_department->response[0])){
    $xx_department_Info=$xx_department->response[0];
    $xx_department_Info_type=$xx_department_Info->department_type;
    $pageSlug = "tickets_".$xx_department_Info_type;
}else{
    header('Location: /admin');
}


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
        $resultAllTickets = ATicket::getAllTicketD($deId,"open");
        $dataAllTickets = [];
        if ($resultAllTickets->status == 200 && !empty($resultAllTickets->response)) {
            $dataAllTickets = $resultAllTickets->response;
        }


        getHeader($lang["tickets_open_".$xx_department_Info_type], [
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
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="page-title mb-4"><?= $lang["tickets_open_".$xx_department_Info_type]; ?></h4>
                            <div class="row">
                                <?php
                                if (!empty($dataAllTickets)) {
                                    foreach ($dataAllTickets as $dataAllTicketsITEM) {

                                        $departmentName = "";
                                        if (!empty($dataAllDepartments)) {
                                            foreach ($dataAllDepartments as $dataAllDepartmentsITEM) {
                                                if ($dataAllDepartmentsITEM->department_id == $dataAllTicketsITEM->department_id) {
                                                    $departmentName = (!empty(array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                        $departmentName = array_column(json_decode($dataAllDepartmentsITEM->department_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="alert alert-warning chatBox">
                                                <a target="_self"
                                                   href="/admin/ticket/open/<?= $dataAllTicketsITEM->ticket_id; ?>"
                                                   class="text-body">
                                                    <div class="d-flex align-items-start p-2">
                                                        <img src="<?= USER_AVATAR ?>"
                                                             class="me-2 rounded-circle" height="42" width="42"
                                                             alt="Brandon Smith">
                                                        <div class="w-100 pt-2">
                                                            <h5 class="mt-0 mb-0 font-14">
                                                                <span class="float-end text-muted fw-normal font-12"><bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $dataAllTicketsITEM->ticket_submit_date); ?></bdi></span>
                                                                <?= $dataAllTicketsITEM->ticket_title; ?>
                                                            </h5>
                                                            <p class="mt-1 mb-0 text-muted font-14">
                                                                <span class="w-50 float-end text-end">
                                                                <?php if ($dataAllTicketsITEM->ticket_status == "open") {
                                                                    echo '<span class="badge badge-outline-danger">' . $lang['ticket_open'] . '</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-outline-primary">' . $lang['ticket_close'] . '</span>';
                                                                } ?>
                                                                    </span>
                                                                <span class="w-25">
                                                                    <?= $departmentName; ?>
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="alert alert-light bg-light text-dark border-0 chatBox">
                                            <i class="mdi mdi-gift-outline mdi-18px"></i>
                                            <?= $lang['no_ticket']; ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
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