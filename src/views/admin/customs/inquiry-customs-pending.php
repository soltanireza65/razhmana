<?php
$pageSlug = "inquiry_customs";
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


        $Result = Customs::getAllInquiryCustomsByStatus('pending');
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response;
        }

        getHeader($lang["a_inquiry_customs"], [
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
                        ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-sm-4">
                                        <div class="d-flex align-items-start">
                                            <img class="d-flex align-self-center me-3 rounded-circle"
                                                 src="<?= Utils::fileExist($DataITEM->user_avatar, USER_AVATAR); ?>"
                                                 alt="<?= ($DataITEM->user_firstname) ? Security::decrypt($DataITEM->user_firstname) . " " . Security::decrypt($DataITEM->user_lastname) : $lang['guest_user']; ?>"
                                                 height="64">
                                            <div class="w-100">
                                                <h4 class="mt-0 mb-2 font-16">
                                                    <?= ($DataITEM->user_firstname) ? Security::decrypt($DataITEM->user_firstname) . " " . Security::decrypt($DataITEM->user_lastname) : $lang['guest_user']; ?>
                                                </h4>
                                                <p class="mb-1">
                                                    <b><?= $lang['users_type']; ?>:</b>
                                                    <?= $lang['businessman']; ?>
                                                </p>
                                                <p class="mb-0">
                                                    <b><?= $lang['language']; ?>:</b>
                                                    <?= $lang[$DataITEM->user_language]; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mb-1 mt-3 mt-sm-0">
                                            <i class="mdi mdi-clock-time-eight-outline mdi-spin me-1"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['date_create']; ?>"></i>
                                            <bdi><?= Utils::getTimeCountry($Settings['date_format'], $DataITEM->freight_submit_date); ?></bdi>
                                        </p>
                                        <p class="mb-0">
                                            <i class="mdi mdi-phone-classic me-1"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['phone_number']; ?>"></i>
                                            <bdi> <?= Security::decrypt($DataITEM->user_mobile); ?> </bdi>
                                        </p>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-center mt-3 mt-sm-0">
                                            <?= "<div class='badge badge-soft-warning text-warning p-1 font-14'>" . $lang['a_freight_pending'] . "</div>"; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-sm-end">
                                            <a href="/admin/inquiry/customs/<?= $DataITEM->freight_id; ?>"
                                               class="action-icon">
                                                <i class="mdi mdi-eye"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['all_info']; ?>"></i>
                                            </a>
                                        </div>
                                    </div> <!-- end col-->
                                </div> <!-- end row -->
                            </div>
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
                $lang['help_complaint_6']
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