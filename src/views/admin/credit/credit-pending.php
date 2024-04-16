<?php
$pageSlug = "card_bank";
// permission_can_show

global $lang,$Settings;

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
         * Get All Pending Bank Card
         */
        $resultBankCard = AUser::getBankCard('pending');
        $dataBankCard = [];
        if ($resultBankCard->status == 200 && !empty($resultBankCard->response)) {
            $dataBankCard = $resultBankCard->response;
        }

        getHeader($lang["card_banks_pending"], [
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
                <?php
                if (!empty($dataBankCard)) {
                    foreach ($dataBankCard as $dataBankCardITEM) {

                        /**
                         * Get User Info By Id
                         */
                        $resultUserInfoById = AUser::getUserInfoById($dataBankCardITEM->user_id);
                        $name = $lang['guest_user'];
                        $status = 'active';
                        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
                            $name = Security::decrypt($resultUserInfoById->response[0]->user_firstname) . " " . Security::decrypt($resultUserInfoById->response[0]->user_lastname);
                            $status = $resultUserInfoById->response[0]->user_status;
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="card mb-0 mt-3 border border-warning">
                                <div class="card-body">
                                    <span class="badge badge-soft-warning float-end"><?= $lang['pending']; ?></span>
                                    <h5 class="mt-0">
                                        <a href="/admin/credit/<?= $dataBankCardITEM->card_id; ?>"
                                           target="_self"
                                           class="text-warning">
                                            <?= $dataBankCardITEM->card_bank; ?>
                                        </a>
                                    </h5>

                                    <p><?= $dataBankCardITEM->card_number; ?></p>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col">
                                            <a href="/admin/users/info/<?=  $dataBankCardITEM->user_id; ?>"
                                               class="text-reset">
                                                <img src="<?= Utils::fileExist((!empty($resultUserInfoById->user_avatar)) ? $resultUserInfoById->user_avatar : USER_AVATAR, USER_AVATAR); ?>"
                                                     alt="<?= $name; ?>"
                                                     class="avatar-sm img-thumbnail rounded-circle">
                                                <span class="d-none d-md-inline-block ms-1 fw-semibold"><?= $name; ?></span>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-end text-muted">
                                                <p class="font-13 mt-2 mb-0">
                                                    <i class="mdi mdi-calendar"></i>
                                                    <bdi> <?= Utils::getTimeCountry($Settings['date_format'], $dataBankCardITEM->card_time); ?></bdi>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-lg-12 ">
                        <p class="text-center">
                            <img src="<?= BOX_EMPTY; ?>"
                                 style="width: 50%;max-width: fit-content;"
                                 alt="<?= $lang['no_massages']; ?>">
                        </p>
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
                $lang['help_credit_8'],
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