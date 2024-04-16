<?php
$pageSlug = "complaint";
// permission_can_show

global $lang, $Settings;

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
         * Get All Complaints Whit Cargo
         */
        $Result = Complaint::getAllComplaintsWhitCargo('accepted');
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response;
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $cargoName="cargo_name_".$language;

        getHeader($lang["complaints_accepted"], [
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
            <div class="row" id="RowDiv">
                <?php
                $flagT = true;
                if (!empty($Data)) {
                    foreach ($Data as $DataITEM) {
                        $flagT = false;
                        $userFrom = $DataITEM->complaint_from;
                        $userTo = $DataITEM->complaint_to;

                        $resUser = AUser::getMultipleUserByID($userFrom . "," . $userTo);
                        $Complainer = [];
                        $Accused = [];
                        if ($resUser->status == 200 && $resUser->response) {
                            foreach ($resUser->response as $loop) {
                                if ($loop->user_id == $userFrom) {
                                    $Complainer = $loop;
                                }
                                if ($loop->user_id == $userTo) {
                                    $Accused = $loop;
                                }
                            }
                        }


                        ?>
                        <div class="col-md-4">
                            <div class="card mb-0 mt-3 border border-warning">
                                <div class="card-body">
                                    <span class="badge badge-soft-warning float-end"><?= $lang['now_answer']; ?></span>
                                    <h5 class="mt-0"><a href="javascript: void(0);" class="text-warning">
                                            <?php
                                            if(isset($DataITEM->$cargoName)){
                                                echo $DataITEM->$cargoName;
                                            }else{
                                                if(isset($DataITEM->cargo_name_fa_IR)){
                                                    echo $DataITEM->cargo_name_fa_IR;
                                                }elseif(isset($DataITEM->cargo_name_en_US)){
                                                    echo $DataITEM->cargo_name_tr_Tr;
                                                }elseif(isset($DataITEM->cargo_name_tr_Tr)){
                                                    echo $DataITEM->cargo_name_en_US;
                                                }elseif(isset($DataITEM->cargo_name_ru_RU)){
                                                    echo $DataITEM->cargo_name_ru_RU;
                                                }
                                            }
                                            ?>
                                        </a>
                                    </h5>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                           <span class="text-muted">
                                                <?= $lang['complainer']; ?> :
                                           </span>
                                            <?= (!empty($Complainer)) ? Security::decrypt($Complainer->user_firstname) . " " . Security::decrypt($Complainer->user_lastname) : ""; ?>
                                        </li>
                                        <li class="list-group-item">
                                               <span class="text-muted">
                                            <?= $lang['accused']; ?> :
                                            </span>
                                            <?= (!empty($Accused)) ? Security::decrypt($Accused->user_firstname) . " " . Security::decrypt($Accused->user_lastname) : ""; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <?php
                                            $adminNickname = '';
                                            $adminImage = '';
                                            if (!empty($dataAllAdmins)) {
                                                foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                    if ($dataAllAdminsITEM->admin_id == $DataITEM->admin_id) {
                                                        $adminImage = $dataAllAdminsITEM->admin_avatar;
                                                        $adminNickname = $dataAllAdminsITEM->admin_nickname;
                                                    }
                                                }
                                            }
                                            ?>
                                            <a href="javascript: void(0);" class="text-reset">
                                                <img src="<?= Utils::fileExist($adminImage, USER_AVATAR); ?>"
                                                     alt="<?= $adminNickname; ?>"
                                                     class="avatar-sm img-thumbnail rounded-circle">
                                                <span class="d-none d-md-inline-block ms-1 fw-semibold"><?= $adminNickname; ?></span>
                                            </a>

                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col">

                                            <a href="/admin/complaint/<?= ($DataITEM->xtype == "out") ? null : "in/"; ?><?= $DataITEM->complaint_id; ?>"
                                               class="btn btn-soft-primary rounded-pill waves-effect waves-light">
                                                <?= $lang['show_detail']; ?>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-end text-muted">
                                                <p class="font-13 mt-2 mb-0">
                                                    <i class="mdi mdi-calendar"></i>
                                                    <bdi> <?= Utils::getTimeCountry($Settings['data_time_format'], $DataITEM->complaint_date); ?></bdi>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div>
                        <?php
                    }
                }
                if ($flagT) {
                    ?>
                    <div class="col-12">
                        <div class="alert alert-light bg-light text-dark border-0 chatBox">
                            <i class="mdi mdi-hand-heart-outline mdi-18px"></i>
                            <?= $lang['fortunately_the_item_was_not_found']; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_complaint_5']
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